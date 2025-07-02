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
        // إحصائيات عامة
        $totalUsers = User::count();
        $totalCompanies = User::role('company')->count();
        $totalEmployees = User::role('employee')->count();
        $totalJobs = HajjJob::count();
        $activeJobs = HajjJob::where('status', 'active')->count();
        $inactiveJobs = HajjJob::where('status', 'inactive')->count();
        $closedJobs = HajjJob::where('status', 'closed')->count();
        $totalApplications = JobApplication::count();
        $pendingApplications = JobApplication::where('status', 'pending')->count();
        $acceptedApplications = JobApplication::where('status', 'approved')->count();
        $rejectedApplications = JobApplication::where('status', 'rejected')->count();
        
        // إحصائيات هذا الشهر
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $newJobsThisMonth = HajjJob::whereMonth('created_at', now()->month)->count();
        $newApplicationsThisMonth = JobApplication::whereMonth('created_at', now()->month)->count();
        
        // إحصائيات اليوم
        $todayRegistrations = User::whereDate('created_at', today())->count();
        $todayJobs = HajjJob::whereDate('created_at', today())->count();
        $todayApplications = JobApplication::whereDate('created_at', today())->count();
        
        // أحدث الأنشطة
        $recentUsers = User::latest()->take(5)->get();
        $recentJobs = HajjJob::with('company')->withCount('applications')->latest()->take(5)->get();
        $recent_jobs = HajjJob::with('company')->withCount('applications')->latest()->take(5)->get();
        $recentApplications = JobApplication::with(['user', 'job'])->latest()->take(5)->get();
        $recent_applications = JobApplication::with(['user', 'job'])->latest()->take(5)->get();
        
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

    // إدارة المستخدمين
    public function users()
    {
        $users = User::with('roles')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }
    
    public function companies()
    {
        $companies = User::role('company')->with('profile')->latest()->paginate(15);
        return view('admin.companies.index', compact('companies'));
    }
    
    public function employees()
    {
        $employees = User::role('employee')->with('profile')->latest()->paginate(15);
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
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);
            
            $user->assignRole($request->role);
            
            // إضافة معلومات الملف الشخصي حسب النوع
            if ($request->role === 'company') {
                $user->profile()->create([
                    'company_name' => $request->company_name,
                    'company_phone' => $request->company_phone,
                    'company_address' => $request->company_address,
                ]);
            } elseif ($request->role === 'employee') {
                $user->profile()->create([
                    'phone' => $request->phone,
                ]);
            }
            
            $roleText = $request->role == 'admin' ? 'مدير' : ($request->role == 'company' ? 'شركة' : 'موظف');
            
            return redirect()->route('admin.users.index')->with('success', 'تم إنشاء ' . $roleText . ' جديد: ' . $user->name . ' بنجاح');
            
        } catch (\Exception $e) {
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

    // إدارة الوظائف
    public function jobs()
    {
        $jobs = HajjJob::with(['company', 'applications'])->withCount('applications')->latest()->paginate(15);
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

    // إدارة طلبات التوظيف
    public function applications()
    {
        $applications = JobApplication::with(['user', 'job', 'job.company'])->latest()->paginate(15);
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
