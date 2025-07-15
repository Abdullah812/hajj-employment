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
            
            // رفع الملفات الجديدة
            $fileFields = [
                'iban_attachment' => ['field' => 'iban_attachment', 'folder' => 'documents/iban'],
                'national_address_attachment' => ['field' => 'national_address_attachment', 'folder' => 'documents/national_address'],
                'national_id_attachment' => ['field' => 'national_id_attachment', 'folder' => 'documents/national_id'],
                'experience_certificate' => ['field' => 'experience_certificate', 'folder' => 'documents/experience']
            ];
            
            foreach ($fileFields as $inputField => $config) {
                if ($request->hasFile($inputField)) {
                    \Log::info('Processing file', ['field' => $inputField]);
                    
                    // حذف الملف القديم إن وجد
                    if ($user->profile && $user->profile->{$config['field']}) {
                        Storage::delete('public/' . $user->profile->{$config['field']});
                    }
                    
                    $file = $request->file($inputField);
                    $filePath = $file->store($config['folder'], 'public');
                    $profileData[$config['field']] = $filePath;
                    
                    \Log::info('File stored', [
                        'field' => $inputField,
                        'path' => $filePath
                    ]);
                }
            }
            
            \Log::info('Final profile data', ['profile_data' => $profileData]);
            
            try {
                // تحديث أو إنشاء الملف الشخصي
                $profile = $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    $profileData
                );
                
                \Log::info('Profile updated', ['profile' => $profile]);
                
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
        
        return redirect()->back()->with('error', 'حدث خطأ في تحديث الملف الشخصي');
    }

    public function uploadCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('cv')) {
            // حذف السيرة الذاتية القديمة
            if ($user->profile && $user->profile->cv_path) {
                Storage::delete($user->profile->cv_path);
            }
            
            // رفع السيرة الذاتية الجديدة
            $path = $request->file('cv')->store('cvs', 'public');
            
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['cv_path' => $path]
            );
            
            return redirect()->route('employee.profile')->with('success', 'تم رفع السيرة الذاتية بنجاح');
        }
        
        return redirect()->route('employee.profile')->with('error', 'فشل في رفع السيرة الذاتية');
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
}
