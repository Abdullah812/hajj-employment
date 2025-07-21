<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
// use App\Models\HajjJob; - تم حذف نظام الوظائف
// use App\Models\JobApplication; - تم حذف نظام الطلبات
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // إحصائيات بسيطة للموظف
        $stats = [
            'total_users' => \App\Models\User::count(),
            'user_profile_complete' => $user->profile ? 1 : 0,
        ];
        
                // لا توجد طلبات أو وظائف - تم حذف الأنظمة
        $recentApplications = collect();
        $my_applications = collect();
                $suggestedJobs = collect();
            
        // الوظائف الحديثة (نفس المحتوى مع اسم مختلف للview)
        $recent_jobs = $suggestedJobs;
        
        return view('employee.dashboard', compact('stats', 'recentApplications', 'my_applications', 'suggestedJobs', 'recent_jobs'));
    }
    
    public function profile()
    {
        $profile = auth()->user()->profile;
        return view('employee.profile', compact('profile'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $formType = $request->input('form_type', 'basic');
        
        \Log::info('Updating profile', [
            'form_type' => $formType,
            'request_data' => $request->all()
        ]);
        
        if ($formType === 'basic') {
            // validation للمعلومات الأساسية
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'national_id' => 'nullable|string|max:20',
            ]);
            
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            
            $profileData = [
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'national_id' => $request->national_id,
            ];
            
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
            
            // إعادة تعيين حالة الموافقة لمراجعة المعلومات المحدثة
            $user->update([
                'approval_status' => 'pending',
                'approved_at' => null,
                'approved_by' => null
            ]);
            
            return redirect()->back()->with('success', 'تم تحديث المعلومات الأساسية بنجاح. سيتم مراجعة حسابك من قبل الإدارة.');
            
        } elseif ($formType === 'additional') {
            // validation للمعلومات الإضافية
            $request->validate([
                'qualification' => 'nullable|string|max:255',
                'iban_number' => 'nullable|string|max:24|regex:/^SA[0-9]{22}$/',
                'national_id' => 'nullable|string|max:20',
                'academic_experience' => 'nullable|string',
                'iban_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'national_address_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'national_id_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'experience_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);
            
            $profileData = [
                'qualification' => $request->qualification,
                'iban_number' => $request->iban_number,
                'national_id' => $request->national_id,
                'academic_experience' => $request->academic_experience,
            ];
            
            \Log::info('Profile data before files', ['profile_data' => $profileData]);
            
            try {
                // إنشاء أو الحصول على profile
                $profile = $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
                
                // رفع الملفات الجديدة - حفظ في قاعدة البيانات بدلاً من filesystem
                $fileFields = [
                    'iban_attachment' => 'iban',
                    'national_address_attachment' => 'national_address', 
                    'national_id_attachment' => 'national_id',
                    'experience_certificate' => 'experience'
                ];
                
                foreach ($fileFields as $inputField => $dbField) {
                    if ($request->hasFile($inputField)) {
                        \Log::info('Processing file for database storage', ['field' => $inputField]);
                        
                        $file = $request->file($inputField);
                        
                        // حفظ الملف في قاعدة البيانات
                        if ($profile->saveFileToDatabase($file, $dbField)) {
                            \Log::info('File saved to database', [
                                'field' => $inputField,
                                'db_field' => $dbField,
                                'file_name' => $file->getClientOriginalName()
                            ]);
                        } else {
                            \Log::error('Failed to save file to database', ['field' => $inputField]);
                        }
                    }
                }
                
                \Log::info('Profile updated successfully', ['profile' => $profile]);
                
                // إعادة تعيين حالة الموافقة لمراجعة المعلومات المحدثة
                $user->update([
                    'approval_status' => 'pending',
                    'approved_at' => null,
                    'approved_by' => null
                ]);
                
                \Log::info('User approval status reset to pending', ['user_id' => $user->id]);
                
                return redirect()->back()->with('success', 'تم تحديث المعلومات الإضافية بنجاح. سيتم مراجعة حسابك من قبل الإدارة.');
                
            } catch (\Exception $e) {
                \Log::error('Error updating profile', [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'user_id' => $user->id,
                    'form_type' => $formType
                ]);
                
                // رسائل خطأ محسنة للـ Laravel Cloud
                $errorMessage = 'حدث خطأ في تحديث الملف الشخصي.';
                
                if (str_contains($e->getMessage(), 'memory')) {
                    $errorMessage = 'الملف كبير جداً. يرجى استخدام ملف أصغر (أقل من 5MB).';
                } elseif (str_contains($e->getMessage(), 'upload')) {
                    $errorMessage = 'خطأ في رفع الملف. يرجى المحاولة مرة أخرى.';
                } elseif (str_contains($e->getMessage(), 'database')) {
                    $errorMessage = 'خطأ في حفظ البيانات. يرجى المحاولة مرة أخرى.';
                }
                
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->withInput();
            }
        }
        
        return redirect()->back()->with('error', 'نوع النموذج غير صحيح');
    }

    public function uploadCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('cv')) {
            try {
                // إنشاء أو الحصول على profile
                $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
                
                $file = $request->file('cv');
                
                // حفظ السيرة الذاتية في قاعدة البيانات
                if ($profile->saveFileToDatabase($file, 'cv')) {
                    \Log::info('CV uploaded successfully to database', [
                        'user_id' => $user->id,
                        'file_name' => $file->getClientOriginalName()
                    ]);
                    
                    // إعادة تعيين حالة الموافقة لمراجعة المعلومات المحدثة
                    $user->update([
                        'approval_status' => 'pending',
                        'approved_at' => null,
                        'approved_by' => null
                    ]);
                    
                    return redirect()->route('employee.profile')->with('success', 'تم رفع السيرة الذاتية بنجاح. سيتم مراجعة حسابك من قبل الإدارة.');
                } else {
                    \Log::error('Failed to save CV to database', ['user_id' => $user->id]);
                    return redirect()->route('employee.profile')->with('error', 'فشل في حفظ السيرة الذاتية');
                }
                
            } catch (\Exception $e) {
                \Log::error('Error uploading CV', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]);
                
                // رسائل خطأ محسنة للـ Laravel Cloud
                $errorMessage = 'حدث خطأ في رفع السيرة الذاتية.';
                
                if (str_contains($e->getMessage(), 'memory')) {
                    $errorMessage = 'الملف كبير جداً. يرجى استخدام ملف أصغر (أقل من 5MB).';
                } elseif (str_contains($e->getMessage(), 'upload')) {
                    $errorMessage = 'خطأ في رفع الملف. يرجى المحاولة مرة أخرى.';
                }
                
                return redirect()->route('employee.profile')->with('error', $errorMessage);
            }
        }
        
        return redirect()->route('employee.profile')->with('error', 'لم يتم اختيار ملف');
    }
    
    // إدارة طلبات التوظيف - تم حذف النظام
    
    // تم حذف نظام طلبات التوظيف بالكامل
    
    // تم حذف methods إلغاء الطلبات
    
    // تم حذف methods عرض الطلبات

    /**
     * عرض ملف من قاعدة البيانات
     */
    public function viewFile($type, $id)
    {
        $user = Auth::user();
        
        // التحقق من الصلاحية - المستخدم يمكنه عرض ملفاته فقط
        if ($user->id != $id && !$user->hasRole(['admin', 'department'])) {
            abort(403, 'غير مسموح بالوصول لهذا الملف');
        }
        
        // الحصول على الملف الشخصي
        $profile = \App\Models\UserProfile::where('user_id', $id)->first();
        
        if (!$profile) {
            abort(404, 'الملف الشخصي غير موجود');
        }
        
        // التحقق من صحة نوع الملف
        $allowedTypes = ['cv', 'national_id', 'iban', 'national_address', 'experience'];
        if (!in_array($type, $allowedTypes)) {
            abort(400, 'نوع ملف غير صحيح');
        }
        
        // الحصول على بيانات الملف
        $fileData = $profile->{"{$type}_file_data"};
        $fileName = $profile->{"{$type}_file_name"} ?: "file.pdf";
        $mimeType = $profile->{"{$type}_file_type"} ?: 'application/octet-stream';
        
        if (!$fileData) {
            abort(404, 'الملف غير موجود');
        }
        
        // فك تشفير base64
        $decodedData = base64_decode($fileData);
        
        if (!$decodedData) {
            abort(500, 'خطأ في قراءة الملف');
        }
        
        // إرجاع الملف مع headers صحيحة
        return response($decodedData, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Content-Length' => strlen($decodedData),
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
