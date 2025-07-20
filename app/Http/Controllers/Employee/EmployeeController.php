<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HajjJob;
use App\Models\JobApplication;
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
        
        // إحصائيات الموظف
        $totalJobs = HajjJob::where('status', 'active')->count();
        $myApplications = JobApplication::where('user_id', $user->id)->count();
        $pendingApplications = JobApplication::where('user_id', $user->id)->where('status', 'pending')->count();
        $approvedApplications = JobApplication::where('user_id', $user->id)->where('status', 'approved')->count();
        
        $stats = [
            'total_jobs' => $totalJobs,
            'my_applications' => $myApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            // المفاتيح الإضافية للاستخدام في views أخرى
            'total' => $myApplications,
            'pending' => $pendingApplications,
            'approved' => $approvedApplications,
            'rejected' => JobApplication::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];
        
        // الطلبات الحديثة
        $recentApplications = JobApplication::where('user_id', $user->id)
            ->with(['job', 'job.department'])
            ->latest()
            ->take(5)
            ->get();
            
        // طلباتي (نفس المحتوى مع اسم مختلف للview)
        $my_applications = $recentApplications;
            
        // الوظائف المقترحة (حسب المهارات - سنضيف هذا لاحقاً)
        $suggestedJobs = HajjJob::where('status', 'active')
            ->where('application_deadline', '>', now())
            ->whereDoesntHave('applications', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('department')
            ->withCount('applications')
            ->latest()
            ->take(6)
            ->get();
            
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
            
            return redirect()->back()->with('success', 'تم تحديث المعلومات الأساسية بنجاح');
            
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
                
                return redirect()->back()->with('success', 'تم تحديث المعلومات الإضافية بنجاح');
                
            } catch (\Exception $e) {
                \Log::error('Error updating profile', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->back()
                    ->with('error', 'حدث خطأ في تحديث الملف الشخصي: ' . $e->getMessage())
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
                    
                    return redirect()->route('employee.profile')->with('success', 'تم رفع السيرة الذاتية بنجاح');
                } else {
                    \Log::error('Failed to save CV to database', ['user_id' => $user->id]);
                    return redirect()->route('employee.profile')->with('error', 'فشل في حفظ السيرة الذاتية');
                }
                
            } catch (\Exception $e) {
                \Log::error('Error uploading CV', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->route('employee.profile')->with('error', 'حدث خطأ في رفع السيرة الذاتية');
            }
        }
        
        return redirect()->route('employee.profile')->with('error', 'لم يتم اختيار ملف');
    }
    
    // إدارة طلبات التوظيف
    public function applications()
    {
        $user = Auth::user();
        
        // إحصائيات الطلبات
        $stats = [
            'total' => JobApplication::where('user_id', $user->id)->count(),
            'pending' => JobApplication::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => JobApplication::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => JobApplication::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];
        
        // قائمة الطلبات
        $applications = JobApplication::where('user_id', $user->id)
            ->with(['job', 'job.department'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('employee.applications', compact('stats', 'applications'));
    }
    
    public function applyForJob(Request $request, HajjJob $job)
    {
        $user = Auth::user();

        // التحقق من حالة الموافقة على الحساب
        if (!$user->isApproved()) {
            return redirect()->back()->with('error', 'عذراً، يجب أن يتم اعتماد حسابك من قبل المدير قبل التقديم على الوظائف');
        }
        
        // التحقق من أن الوظيفة متاحة
        if ($job->status !== 'active') {
            return redirect()->back()->with('error', 'هذه الوظيفة غير متاحة حالياً');
        }
        
        // التحقق من انتهاء موعد التقديم
        if ($job->application_deadline < now()) {
            return redirect()->back()->with('error', 'انتهى موعد التقديم لهذه الوظيفة');
        }
        
        // التحقق من عدم التقديم المسبق
        $existingApplication = JobApplication::where('user_id', $user->id)
            ->where('job_id', $job->id)
            ->first();
            
        if ($existingApplication) {
            return redirect()->back()->with('error', 'لقد قمت بالتقديم على هذه الوظيفة من قبل');
        }
        
        // التحقق من الحد الأقصى للمتقدمين
        if ($job->max_applicants && $job->applications()->count() >= $job->max_applicants) {
            return redirect()->back()->with('error', 'تم الوصول للحد الأقصى من المتقدمين لهذه الوظيفة');
        }
        
        $request->validate([
            'cover_letter' => 'nullable|string|max:1000',
        ]);
        
        $application = JobApplication::create([
            'user_id' => $user->id,
            'job_id' => $job->id,
            'cover_letter' => $request->cover_letter,
            'status' => 'pending'
        ]);
        
        // إرسال إشعار للقسم
        $this->notificationService->notifyDepartmentAboutNewApplication($application);
        
        return redirect()->route('employee.applications')->with('success', 'تم تقديم طلبك بنجاح');
    }
    
    public function cancelApplication(JobApplication $application)
    {
        // التأكد من أن الطلب يخص المستخدم المسجل
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }
        
        // التأكد من أن الطلب لا يزال معلقاً
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الطلب');
        }
        
        $application->delete();
        
        return redirect()->back()->with('success', 'تم إلغاء الطلب بنجاح');
    }
    
    public function showApplication(JobApplication $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('employee.applications.show', compact('application'));
    }

    /**
     * عرض ملف من قاعدة البيانات
     */
    public function viewFile($type, $id)
    {
        $user = Auth::user();
        
        // التحقق من الصلاحية - المستخدم يمكنه عرض ملفاته + المديرين والأقسام يمكنهم عرض جميع الملفات
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
        try {
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
                'X-Content-Type-Options' => 'nosniff',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error decoding file', [
                'file_type' => $type,
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'خطأ في معالجة الملف');
        }
    }
}
