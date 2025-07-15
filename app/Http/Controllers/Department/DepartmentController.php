<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        // إحصائيات القسم
        $totalJobs = HajjJob::where('department_id', $departmentId)->count();
        $activeJobs = HajjJob::where('department_id', $departmentId)->where('status', 'active')->count();
        $inactiveJobs = HajjJob::where('department_id', $departmentId)->where('status', 'inactive')->count();
        $totalApplications = JobApplication::whereHas('job', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->count();
        $pendingApplications = JobApplication::whereHas('job', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->where('status', 'pending')->count();
        $approvedApplications = JobApplication::whereHas('job', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->where('status', 'approved')->count();
        $rejectedApplications = JobApplication::whereHas('job', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
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
            'jobs' => $totalJobs,
            'applications' => $totalApplications,
            'approved' => $approvedApplications,
        ];
        
        // الوظائف الحديثة
        $recentJobs = HajjJob::where('department_id', $departmentId)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();
            
        $recent_jobs = $recentJobs;
            
        // الطلبات الحديثة
        $recentApplications = JobApplication::whereHas('job', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->with(['user', 'job'])->latest()->take(5)->get();
        
        $recent_applications = $recentApplications;
        
        return view('department.dashboard', compact(
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
            
        return view('department.jobs.index', compact('jobs', 'stats'));
    }
    
    public function createJob()
    {
        $user = Auth::user();
        $departments = [];
        
        if ($user->hasRole('admin')) {
            // إذا كان المستخدم أدمن، اجلب جميع الأقسام
            $departments = \App\Models\Department::all();
        }
        
        return view('department.jobs.create', compact('departments'));
    }
    
    public function storeJob(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');
        
        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,temporary,seasonal',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'required|date|after:today',
            'max_applicants' => 'nullable|integer|min:1',
        ];
        
        // إضافة حقل القسم للتحقق إذا كان المستخدم أدمن
        if ($isAdmin) {
            $validationRules['department_id'] = 'required|exists:departments,id';
        }
        
        $request->validate($validationRules);
        
        // التحقق من القسم
        $departmentId = $isAdmin ? $request->department_id : $user->department?->id;
        
        if (!$departmentId) {
            return redirect()->back()->with('error', 'يجب إنشاء ملف تعريف القسم أولاً');
        }
        
        $job = HajjJob::create([
            'department_id' => $departmentId,
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
        
        try {
            $this->notificationService->notifyNewJob($job);
            
            // إشعار الإدارة بوظيفة جديدة
            $this->notificationService->notifyAdminActivity(
                'new_job',
                'وظيفة جديدة تم نشرها',
                "قام قسم {$job->department->name} بنشر وظيفة جديدة: {$job->title}",
                [
                    'job_id' => $job->id,
                    'department_name' => $job->department->name,
                    'job_title' => $job->title
                ]
            );
        } catch (\Exception $e) {
            \Log::error('فشل في إرسال إشعارات الوظيفة الجديدة: ' . $e->getMessage());
        }
        
        return redirect()->route('department.jobs.index')->with('success', 'تم إنشاء الوظيفة بنجاح وإشعار جميع الموظفين');
    }
    
    public function showJob(HajjJob $job)
    {
        $user = Auth::user();
        $userDepartmentId = $user->department?->id;

        if (!$userDepartmentId) {
            return redirect()->route('department.jobs.index')
                ->with('error', 'عذراً، لم يتم العثور على القسم الخاص بك. يرجى التواصل مع الإدارة.');
        }

        if ($job->department_id !== $userDepartmentId) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الوظيفة');
        }
        
        $applications = $job->applications()->with('user')->latest()->paginate(10);
        
        return view('department.jobs.show', compact('job', 'applications'));
    }
    
    public function editJob(HajjJob $job)
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.dashboard')
                ->with('error', 'عذراً، لم يتم العثور على القسم الخاص بك. يرجى التواصل مع الإدارة.');
        }

        if ($job->department_id !== $departmentId) {
            abort(403, 'غير مصرح لك بتعديل هذه الوظيفة');
        }

        return view('department.jobs.edit', compact('job'));
    }
    
    public function updateJob(Request $request, HajjJob $job)
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.dashboard')
                ->with('error', 'عذراً، لم يتم العثور على القسم الخاص بك. يرجى التواصل مع الإدارة.');
        }

        if ($job->department_id !== $departmentId) {
            abort(403, 'غير مصرح لك بتعديل هذه الوظيفة');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,temporary,seasonal',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'required|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'required|date|after:today',
            'max_applicants' => 'nullable|integer|min:1',
        ]);
        
        $job->update($request->all());
        
        return redirect()->route('department.jobs.show', $job)
            ->with('success', 'تم تحديث الوظيفة بنجاح');
    }
    
    public function deleteJob(HajjJob $job)
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.profile')
                ->with('error', 'يرجى إكمال معلومات القسم الخاص بك أولاً');
        }

        if ($job->department_id !== $departmentId) {
            abort(403, 'غير مصرح لك بحذف هذه الوظيفة');
        }

        $job->delete();
        
        return redirect()->route('department.jobs.index')
            ->with('success', 'تم حذف الوظيفة بنجاح');
    }
    
    public function applications()
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.profile')
                ->with('error', 'يرجى إكمال معلومات القسم الخاص بك أولاً');
        }

        $applications = JobApplication::whereHas('job', function($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })->with(['user', 'job'])->latest()->paginate(10);
        
        return view('department.applications.index', compact('applications'));
    }
    
    public function updateJobStatus(Request $request, HajjJob $job)
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.profile')
                ->with('error', 'يرجى إكمال معلومات القسم الخاص بك أولاً');
        }

        if ($job->department_id !== $departmentId) {
            abort(403, 'غير مصرح لك بتحديث حالة هذه الوظيفة');
        }

        $request->validate([
            'status' => 'required|in:active,inactive,closed'
        ]);
        
        $job->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'تم تحديث حالة الوظيفة بنجاح');
    }
    
    public function updateApplication(Request $request, JobApplication $application)
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.profile')
                ->with('error', 'يرجى إكمال معلومات القسم الخاص بك أولاً');
        }

        if ($application->job->department_id !== $departmentId) {
            abort(403, 'غير مصرح لك بتحديث حالة هذا الطلب');
        }

        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string'
        ]);
        
        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'reviewed_at' => now()
        ]);
        
        // إرسال إشعار للمتقدم
        try {
            $this->notificationService->notifyApplicationUpdate($application);
        } catch (\Exception $e) {
            \Log::error('فشل في إرسال إشعار تحديث حالة الطلب: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
    
    public function bulkUpdateApplications(Request $request)
    {
        $user = Auth::user();
        $departmentId = $user->department?->id;

        if (!$departmentId) {
            return redirect()->route('department.profile')
                ->with('error', 'يرجى إكمال معلومات القسم الخاص بك أولاً');
        }

        $request->validate([
            'applications' => 'required|array',
            'applications.*' => 'exists:job_applications,id',
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $applications = JobApplication::whereIn('id', $request->applications)
            ->whereHas('job', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->get();

        foreach ($applications as $application) {
            $application->update([
                'status' => $request->status,
                'reviewed_at' => now()
            ]);

            try {
                $this->notificationService->notifyApplicationUpdate($application);
            } catch (\Exception $e) {
                \Log::error('فشل في إرسال إشعار تحديث حالة الطلب: ' . $e->getMessage());
            }
        }
        
        return redirect()->back()->with('success', 'تم تحديث حالة الطلبات المحددة بنجاح');
    }
}
