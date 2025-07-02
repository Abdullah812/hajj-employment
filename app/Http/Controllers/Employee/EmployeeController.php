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
            ->with(['job', 'job.company'])
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
            ->with('company')
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:20',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'skills' => 'nullable|string',
            'bio' => 'nullable|string',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);
        
        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        $profileData = [
            'phone' => $request->phone,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'national_id' => $request->national_id,
            'education' => $request->education,
            'experience' => $request->experience,
            'skills' => $request->skills,
            'bio' => $request->bio,
        ];
        
        // رفع ملف السيرة الذاتية
        if ($request->hasFile('cv')) {
            // حذف الملف القديم إن وجد
            if ($user->profile && $user->profile->cv_path) {
                Storage::delete($user->profile->cv_path);
            }
            
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $profileData['cv_path'] = $cvPath;
        }
        
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );
        
        return redirect()->back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
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
            ->with(['job', 'job.company'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('employee.applications', compact('stats', 'applications'));
    }
    
    public function applyForJob(Request $request, HajjJob $job)
    {
        // التحقق من أن الوظيفة متاحة
        if ($job->status !== 'active') {
            return redirect()->back()->with('error', 'هذه الوظيفة غير متاحة حالياً');
        }
        
        // التحقق من انتهاء موعد التقديم
        if ($job->application_deadline < now()) {
            return redirect()->back()->with('error', 'انتهى موعد التقديم لهذه الوظيفة');
        }
        
        // التحقق من عدم التقديم المسبق
        $existingApplication = JobApplication::where('user_id', Auth::id())
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
            'user_id' => Auth::id(),
            'job_id' => $job->id,
            'cover_letter' => $request->cover_letter,
            'status' => 'pending',
        ]);
        
        // إرسال إشعارات
        try {
            // إشعار للموظف بتأكيد التقديم
            $this->notificationService->createNotification(
                Auth::id(),
                'application_status',
                'تم تقديم طلبك بنجاح',
                "تم تقديم طلبك لوظيفة \"{$job->title}\" وهو الآن قيد المراجعة من قبل {$job->company->name}",
                [
                    'application_id' => $application->id,
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'company_name' => $job->company->name
                ],
                route('employee.applications'),
                false // عدم إرسال إيميل للموظف نفسه
            );

            // إشعار الشركة بطلب جديد
            $this->notificationService->notifyCompanyNewApplication($application);
            
            // إشعار الإدارة بنشاط جديد
            $this->notificationService->notifyAdminActivity(
                'application_status',
                'طلب توظيف جديد',
                "تقدم {$application->user->name} لوظيفة {$job->title} في شركة {$job->company->name}",
                [
                    'application_id' => $application->id,
                    'job_id' => $job->id,
                    'employee_name' => $application->user->name,
                    'company_name' => $job->company->name
                ]
            );
            
        } catch (\Exception $e) {
            // تسجيل الخطأ لكن عدم إيقاف العملية
            \Log::error('فشل في إرسال إشعارات التقديم: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'تم تقديم طلبك بنجاح! سيتم مراجعته من قبل الشركة');
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
