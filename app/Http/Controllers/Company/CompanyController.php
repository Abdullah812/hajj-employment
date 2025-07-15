<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // إحصائيات القسم
        $totalJobs = HajjJob::where('department_id', $user->department->id ?? 0)->count();
        $activeJobs = HajjJob::where('department_id', $user->department->id ?? 0)->where('status', 'active')->count();
        $inactiveJobs = HajjJob::where('department_id', $user->department->id ?? 0)->where('status', 'inactive')->count();
        $totalApplications = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('department_id', $user->department->id ?? 0);
        })->count();
        $pendingApplications = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('department_id', $user->department->id ?? 0);
        })->where('status', 'pending')->count();
        $approvedApplications = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('department_id', $user->department->id ?? 0);
        })->where('status', 'approved')->count();
        $rejectedApplications = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('department_id', $user->department->id ?? 0);
        })->where('status', 'rejected')->count();
        
        // تجميع الإحصائيات في مصفوفة
        $stats = [
            'total_jobs' => $totalJobs,
            'active_jobs' => $activeJobs,
            'inactive_jobs' => $inactiveJobs,
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            // المفاتيح الإضافية للاستخدام في views أخرى
            'jobs' => $totalJobs,
            'applications' => $totalApplications,
            'approved' => $approvedApplications,
        ];
        
        // الوظائف الحديثة
        $recentJobs = HajjJob::where('department_id', $user->department->id ?? 0)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();
            
        // أيضاً للتوافق مع view
        $recent_jobs = $recentJobs;
            
        // الطلبات الحديثة
        $recentApplications = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('department_id', $user->department->id ?? 0);
        })->with(['user', 'job'])->latest()->take(5)->get();
        
        // أيضاً للتوافق مع view
        $recent_applications = $recentApplications;
        
        return view('company.dashboard', compact(
            'totalJobs',
            'activeJobs', 
            'inactiveJobs',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'recentJobs',
            'recent_jobs',
            'recentApplications',
            'recent_applications',
            'stats'
        ));
    }
    
    public function profile()
    {
        $user = Auth::user();
        
        // إحصائيات للملف الشخصي
        $stats = [
            'jobs' => HajjJob::where('department_id', $user->department->id ?? 0)->count(),
            'active_jobs' => HajjJob::where('department_id', $user->department->id ?? 0)->where('status', 'active')->count(),
            'applications' => JobApplication::whereHas('job', function($query) use ($user) {
                $query->where('department_id', $user->department->id ?? 0);
            })->count(),
            'approved' => JobApplication::whereHas('job', function($query) use ($user) {
                $query->where('department_id', $user->department->id ?? 0);
            })->where('status', 'approved')->count(),
        ];
        
        return view('company.profile', compact('stats'));
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'company_website' => 'nullable|url',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $request->company_name,
                'company_description' => $request->company_description,
                'company_website' => $request->company_website,
                'company_phone' => $request->company_phone,
                'company_address' => $request->company_address,
            ]
        );
        
        return redirect()->back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
    
    // إدارة الوظائف
    public function jobs()
    {
        $user = Auth::user();
        
        // إحصائيات سريعة للوظائف
        $stats = [
            'total' => HajjJob::where('department_id', $user->department->id ?? 0)->count(),
            'active' => HajjJob::where('department_id', $user->department->id ?? 0)->where('status', 'active')->count(),
            'inactive' => HajjJob::where('department_id', $user->department->id ?? 0)->where('status', 'inactive')->count(),
            'closed' => HajjJob::where('department_id', $user->department->id ?? 0)->where('status', 'closed')->count(),
        ];
        
        $jobs = HajjJob::where('department_id', $user->department->id ?? 0)
            ->latest()
            ->paginate(10);
            
        return view('company.jobs.index', compact('jobs', 'stats'));
    }
    
    public function createJob()
    {
        return view('company.jobs.create');
    }
    
    public function storeJob(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,temporary,seasonal',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'required|date|after:today',
            'max_applicants' => 'nullable|integer|min:1',
        ]);
        
        $job = HajjJob::create([
            'department_id' => Auth::user()->department->id ?? 0,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'employment_type' => $request->employment_type,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'requirements' => $request->requirements,
            'benefits' => $request->benefits,
            'application_deadline' => $request->application_deadline,
            'max_applicants' => $request->max_applicants,
            'status' => 'active',
        ]);
        
        // إرسال إشعارات للموظفين عن الوظيفة الجديدة
        try {
            $this->notificationService->notifyNewJob($job);
            
            // إشعار الإدارة بوظيفة جديدة
            $this->notificationService->notifyAdminActivity(
                'new_job',
                'وظيفة جديدة تم نشرها',
                "قام القسم {$job->department->name} بنشر وظيفة جديدة: {$job->title}",
                [
                    'job_id' => $job->id,
                    'department_name' => $job->department->name,
                    'job_title' => $job->title
                ]
            );
        } catch (\Exception $e) {
            \Log::error('فشل في إرسال إشعارات الوظيفة الجديدة: ' . $e->getMessage());
        }
        
        return redirect()->route('company.jobs.index')->with('success', 'تم إنشاء الوظيفة بنجاح وإشعار جميع الموظفين');
    }
    
    public function showJob(HajjJob $job)
    {
        // التأكد من أن الوظيفة تخص القسم المسجل
        if ($job->department_id !== Auth::user()->department->id ?? 0) {
            abort(403);
        }
        
        $applications = $job->applications()->with('user')->latest()->paginate(10);
        
        return view('company.jobs.show', compact('job', 'applications'));
    }
    
    public function editJob(HajjJob $job)
    {
        if ($job->department_id !== Auth::user()->department->id ?? 0) {
            abort(403);
        }
        
        return view('company.jobs.edit', compact('job'));
    }
    
    public function updateJob(Request $request, HajjJob $job)
    {
        if ($job->department_id !== Auth::user()->department->id ?? 0) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,temporary,seasonal',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'required|date',
            'max_applicants' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,closed',
        ]);
        
        $job->update($request->all());
        
        return redirect()->route('company.jobs')->with('success', 'تم تحديث الوظيفة بنجاح');
    }
    
    public function deleteJob(HajjJob $job)
    {
        if ($job->department_id !== Auth::user()->department->id ?? 0) {
            abort(403);
        }
        
        $job->delete();
        
        return redirect()->route('company.jobs')->with('success', 'تم حذف الوظيفة بنجاح');
    }
    
    // إدارة طلبات التوظيف
    public function applications()
    {
        $user = Auth::user();
        
        // إحصائيات الطلبات
        $stats = [
            'total' => JobApplication::whereHas('job', function($query) use ($user) {
                $query->where('department_id', $user->department->id ?? 0);
            })->count(),
            'pending' => JobApplication::whereHas('job', function($query) use ($user) {
                $query->where('department_id', $user->department->id ?? 0);
            })->where('status', 'pending')->count(),
            'approved' => JobApplication::whereHas('job', function($query) use ($user) {
                $query->where('department_id', $user->department->id ?? 0);
            })->where('status', 'approved')->count(),
            'rejected' => JobApplication::whereHas('job', function($query) use ($user) {
                $query->where('department_id', $user->department->id ?? 0);
            })->where('status', 'rejected')->count(),
        ];
        
        // وظائف القسم للفلترة
        $jobs = HajjJob::where('department_id', $user->department->id ?? 0)->get();
        
        $applications = JobApplication::whereHas('job', function($query) use ($user) {
            $query->where('department_id', $user->department->id ?? 0);
        })->with(['user', 'job'])->latest()->paginate(15);
        
        return view('company.applications.index', compact('applications', 'stats', 'jobs'));
    }
    
    // تحديث حالة الوظيفة
    public function updateJobStatus(Request $request, HajjJob $job)
    {
        if ($job->department_id !== Auth::user()->department->id ?? 0) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        
        $request->validate([
            'status' => 'required|in:active,inactive,closed'
        ]);
        
        $job->update([
            'status' => $request->status
        ]);
        
        return redirect()->back()->with('success', 'تم تحديث حالة الوظيفة بنجاح');
    }
    
    // تحديث حالة طلب التوظيف
    public function updateApplication(Request $request, JobApplication $application)
    {
        // التحقق من أن الطلب يخص القسم
        if ($application->job->department_id !== Auth::user()->department->id ?? 0) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $oldStatus = $application->status;
        
        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'reviewed_at' => now()
        ]);
        
        // إرسال إشعارات إذا تغيرت الحالة
        if ($oldStatus !== $request->status) {
            try {
                $this->notificationService->notifyApplicationStatusChange($application);
            } catch (\Exception $e) {
                \Log::error('فشل في إرسال إشعار تحديث الطلب: ' . $e->getMessage());
            }
        }
        
        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
    
    // تحديث الطلبات بشكل مجمع
    public function bulkUpdateApplications(Request $request)
    {
        $request->validate([
            'applications' => 'required|array',
            'applications.*' => 'exists:job_applications,id',
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $applications = JobApplication::whereIn('id', $request->applications)
            ->whereHas('job', function($query) {
                $query->where('department_id', Auth::user()->department->id ?? 0);
            })->get();
            
        foreach ($applications as $application) {
            $oldStatus = $application->status;
            
            $application->update([
                'status' => $request->status,
                'notes' => $request->notes,
                'reviewed_at' => now()
            ]);
            
            // إرسال إشعار إذا تغيرت الحالة
            if ($oldStatus !== $request->status) {
                try {
                    $this->notificationService->notifyApplicationStatusChange($application);
                } catch (\Exception $e) {
                    \Log::error('فشل في إرسال إشعار تحديث الطلب المجمع: ' . $e->getMessage());
                }
            }
        }
        
        return redirect()->back()->with('success', 'تم تحديث ' . count($applications) . ' طلبات بنجاح');
    }
}
