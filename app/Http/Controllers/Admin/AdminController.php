<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function dashboard()
    {
        // تحسين الأداء: تجميع جميع الاستعلامات في استعلامات محسنة
        
        // إحصائيات المستخدمين بـ query واحد
        $userStats = \DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                \DB::raw('COUNT(users.id) as total_users'),
                \DB::raw('COUNT(CASE WHEN roles.name = "company" THEN 1 END) as total_companies'),
                \DB::raw('COUNT(CASE WHEN roles.name = "employee" THEN 1 END) as total_employees'),
                \DB::raw('COUNT(CASE WHEN users.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_this_month'),
                \DB::raw('COUNT(CASE WHEN DATE(users.created_at) = CURDATE() THEN 1 END) as today_registrations')
            )
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->first();
        
        // إحصائيات الوظائف بـ query واحد
        $jobStats = \DB::table('hajj_jobs')
            ->select(
                \DB::raw('COUNT(*) as total_jobs'),
                \DB::raw('COUNT(CASE WHEN status = "active" THEN 1 END) as active_jobs'),
                \DB::raw('COUNT(CASE WHEN status = "inactive" THEN 1 END) as inactive_jobs'),
                \DB::raw('COUNT(CASE WHEN status = "closed" THEN 1 END) as closed_jobs'),
                \DB::raw('COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_jobs_this_month'),
                \DB::raw('COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_jobs')
            )
            ->first();
        
        // إحصائيات الطلبات بـ query واحد
        $applicationStats = \DB::table('job_applications')
            ->select(
                \DB::raw('COUNT(*) as total_applications'),
                \DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_applications'),
                \DB::raw('COUNT(CASE WHEN status = "approved" THEN 1 END) as accepted_applications'),
                \DB::raw('COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected_applications'),
                \DB::raw('COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_applications_this_month'),
                \DB::raw('COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_applications')
            )
            ->first();
        
        // تعيين المتغيرات للتوافق مع العرض
        $totalUsers = $userStats->total_users;
        $totalCompanies = $userStats->total_companies;
        $totalEmployees = $userStats->total_employees;
        $totalJobs = $jobStats->total_jobs;
        $activeJobs = $jobStats->active_jobs;
        $inactiveJobs = $jobStats->inactive_jobs;
        $closedJobs = $jobStats->closed_jobs;
        $totalApplications = $applicationStats->total_applications;
        $pendingApplications = $applicationStats->pending_applications;
        $acceptedApplications = $applicationStats->accepted_applications;
        $rejectedApplications = $applicationStats->rejected_applications;
        $newUsersThisMonth = $userStats->new_users_this_month;
        $newJobsThisMonth = $jobStats->new_jobs_this_month;
        $newApplicationsThisMonth = $applicationStats->new_applications_this_month;
        $todayRegistrations = $userStats->today_registrations;
        $todayJobs = $jobStats->today_jobs;
        $todayApplications = $applicationStats->today_applications;
        
        // أحدث الأنشطة مع تحسين العلاقات
        $recentUsers = User::select('id', 'name', 'email', 'created_at')
            ->latest()
            ->take(5)
            ->get();
        
        $recentJobs = HajjJob::select('id', 'title', 'company_id', 'status', 'created_at')
            ->with(['company:id,name'])
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();
        
        $recent_jobs = $recentJobs; // للتوافق مع العرض
        
        $recentApplications = JobApplication::select('id', 'user_id', 'job_id', 'status', 'created_at')
            ->with([
                'user:id,name,email',
                'job:id,title,company_id'
            ])
            ->latest()
            ->take(5)
            ->get();
        
        $recent_applications = $recentApplications; // للتوافق مع العرض
        
        // مؤشرات الأداء الرئيسية (KPIs)
        $kpis = $this->calculateKPIs();
        
        // تجميع الإحصائيات في مصفوفة
        $stats = [
            'total_users' => $totalUsers,
            'total_companies' => $totalCompanies,
            'total_employees' => $totalEmployees,
            'total_jobs' => $totalJobs,
            'active_jobs' => $activeJobs,
            'inactive_jobs' => $inactiveJobs,
            'closed_jobs' => $closedJobs,
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'accepted_applications' => $acceptedApplications,
            'rejected_applications' => $rejectedApplications,
            'new_users_this_month' => $newUsersThisMonth,
            'new_jobs_this_month' => $newJobsThisMonth,
            'new_applications_this_month' => $newApplicationsThisMonth,
            'today_registrations' => $todayRegistrations,
            'today_jobs' => $todayJobs,
            'today_applications' => $todayApplications,
            'employees' => $totalEmployees,
            'companies' => $totalCompanies,
            'jobs' => $totalJobs,
            'applications' => $totalApplications,
            'approved_applications' => $acceptedApplications,
        ];
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCompanies',
            'totalEmployees',
            'totalJobs',
            'activeJobs',
            'inactiveJobs',
            'closedJobs',
            'totalApplications',
            'pendingApplications',
            'acceptedApplications',
            'rejectedApplications',
            'newUsersThisMonth',
            'newJobsThisMonth', 
            'newApplicationsThisMonth',
            'todayRegistrations',
            'todayJobs',
            'todayApplications',
            'recentUsers',
            'recentJobs',
            'recent_jobs',
            'recentApplications',
            'recent_applications',
            'stats',
            'kpis'
        ));
    }

    // إدارة المستخدمين - محسن للأداء
    public function users()
    {
        $users = User::select('id', 'name', 'email', 'created_at', 'updated_at')
            ->with(['roles:id,name'])
            ->latest()
            ->paginate(15);
        return view('admin.users.index', compact('users'));
    }
    
    public function companies()
    {
        $companies = User::select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'company')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->with(['profile:user_id,company_name,company_phone,company_address'])
            ->latest('users.created_at')
            ->paginate(15);
        return view('admin.companies.index', compact('companies'));
    }
    
    public function employees()
    {
        $employees = User::select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'employee')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->with(['profile:user_id,phone,national_id,birth_date'])
            ->latest('users.created_at')
            ->paginate(15);
        return view('admin.employees.index', compact('employees'));
    }
    
    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,company,employee',
            'company_name' => 'nullable|required_if:role,company|string|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);
        
        // استخدام Database Transaction للأداء والأمان
        \DB::beginTransaction();
        
        try {
            // إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);
            
            // تعيين الدور مع تحسين الأداء
            \DB::table('model_has_roles')->insert([
                'role_id' => \DB::table('roles')->where('name', $request->role)->value('id'),
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);
            
            // إضافة معلومات الملف الشخصي حسب النوع
            if ($request->role === 'company') {
                \DB::table('user_profiles')->insert([
                    'user_id' => $user->id,
                    'company_name' => $request->company_name,
                    'company_phone' => $request->company_phone,
                    'company_address' => $request->company_address,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } elseif ($request->role === 'employee') {
                \DB::table('user_profiles')->insert([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            \DB::commit();
            
            // تنظيف cache الأدوار
            \Cache::forget('spatie.permission.cache');
            
            $roleText = $request->role == 'admin' ? 'مدير' : ($request->role == 'company' ? 'شركة' : 'موظف');
            
            return redirect()->route('admin.users.index')->with('success', 'تم إنشاء ' . $roleText . ' جديد: ' . $user->name . ' بنجاح');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('User creation error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }
    
    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,company,employee',
            'company_name' => 'nullable|required_if:role,company|string|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);
        
        try {
            // تحديث المعلومات الأساسية
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            
            // تحديث الدور
            $user->syncRoles($request->role);
            
            // تحديث أو إنشاء الملف الشخصي
            if ($request->role === 'company') {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'company_name' => $request->company_name,
                        'company_phone' => $request->company_phone,
                        'company_address' => $request->company_address,
                    ]
                );
            } elseif ($request->role === 'employee') {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => $request->phone,
                    ]
                );
            }
            
            return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات ' . $user->name . ' بنجاح');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }
    
    public function deleteUser(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->back()->with('error', 'لا يمكن حذف المدير الوحيد في النظام');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
    
    public function toggleUserStatus(User $user)
    {
        // منع إلغاء تفعيل المدير الوحيد
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1 && $user->email_verified_at) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء تفعيل المدير الوحيد في النظام');
        }
        
        try {
            $oldStatus = $user->email_verified_at;
            $newStatus = $oldStatus ? null : now();
            
            $user->update([
                'email_verified_at' => $newStatus
            ]);
            
            // تحديث البيانات من قاعدة البيانات
            $user->refresh();
            
            $statusText = $user->email_verified_at ? 'تم تفعيل' : 'تم إلغاء تفعيل';
            $message = $statusText . ' حساب ' . $user->name . ' بنجاح';
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تغيير حالة المستخدم: ' . $e->getMessage());
        }
    }

    // إدارة الوظائف - محسن للأداء
    public function jobs()
    {
        $jobs = HajjJob::select('id', 'title', 'description', 'company_id', 'status', 'salary_range', 'location', 'created_at')
            ->with(['company:id,name'])
            ->withCount('applications')
            ->latest()
            ->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }
    
    public function toggleJobStatus(HajjJob $job)
    {
        $newStatus = $job->status === 'active' ? 'inactive' : 'active';
        $job->update(['status' => $newStatus]);
        
        $statusText = $newStatus === 'active' ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        return redirect()->back()->with('success', $statusText . ' الوظيفة بنجاح');
    }
    
    public function deleteJob(HajjJob $job)
    {
        $job->delete();
        return redirect()->back()->with('success', 'تم حذف الوظيفة بنجاح');
    }

    // إدارة طلبات التوظيف - محسن للأداء
    public function applications()
    {
        $applications = JobApplication::select('id', 'user_id', 'job_id', 'status', 'notes', 'created_at')
            ->with([
                'user:id,name,email',
                'job:id,title,company_id',
                'job.company:id,name'
            ])
            ->latest()
            ->paginate(15);
        return view('admin.applications.index', compact('applications'));
    }
    
    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'reviewed_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    /**
     * حساب مؤشرات الأداء الرئيسية (KPIs)
     */
    private function calculateKPIs()
    {
        // معدل نجاح التوظيف
        $totalApplications = JobApplication::count();
        $approvedApplications = JobApplication::where('status', 'approved')->count();
        $successRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0;

        // نمو المستخدمين (مقارنة الشهر الحالي بالسابق)
        $currentMonthUsers = User::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count();
        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)
                             ->whereYear('created_at', now()->subMonth()->year)->count();
        $userGrowth = $lastMonthUsers > 0 ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;

        // متوسط التطبيقات لكل وظيفة
        $totalJobs = HajjJob::count();
        $avgApplicationsPerJob = $totalJobs > 0 ? round($totalApplications / $totalJobs, 1) : 0;

        // أكثر الشركات نشاطاً
        $topCompanies = User::role('company')
            ->withCount('jobs')
            ->orderBy('jobs_count', 'desc')
            ->take(3)
            ->get();

        // إحصائيات الأداء الشهرية للرسم البياني
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $month->format('M'),
                'users' => User::whereMonth('created_at', $month->month)
                              ->whereYear('created_at', $month->year)->count(),
                'jobs' => HajjJob::whereMonth('created_at', $month->month)
                                ->whereYear('created_at', $month->year)->count(),
                'applications' => JobApplication::whereMonth('created_at', $month->month)
                                               ->whereYear('created_at', $month->year)->count(),
            ];
        }

        // معدل نشاط الشركات
        $activeCompanies = User::role('company')
            ->whereHas('jobs', function($query) {
                $query->where('status', 'active');
            })->count();
        $companyActivityRate = User::role('company')->count() > 0 
            ? round(($activeCompanies / User::role('company')->count()) * 100, 1) : 0;

        return [
            'success_rate' => $successRate,
            'user_growth' => $userGrowth,
            'avg_applications_per_job' => $avgApplicationsPerJob,
            'top_companies' => $topCompanies,
            'monthly_stats' => $monthlyStats,
            'company_activity_rate' => $companyActivityRate,
            'current_month_users' => $currentMonthUsers,
            'last_month_users' => $lastMonthUsers,
        ];
    }
}
