<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\Department;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Exports\JobsExport;
use App\Exports\ApplicationsExport;
use Maatwebsite\Excel\Facades\Excel;
// PDF import removed - using Word only
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        // إحصائيات عامة
        $totalUsers = User::count();
        $totalDepartments = User::role('department')->count();
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
        $recentJobs = HajjJob::with(['department', 'applications'])->withCount('applications')->latest()->take(5)->get();
        $recent_jobs = HajjJob::with(['department', 'applications'])->withCount('applications')->latest()->take(5)->get();
        $recentApplications = JobApplication::with(['user', 'job.department'])->latest()->take(5)->get();
        $recent_applications = JobApplication::with(['user', 'job.department'])->latest()->take(5)->get();
        
        // مؤشرات الأداء الرئيسية (KPIs)
        $kpis = $this->calculateKPIs();
        
        // تجميع الإحصائيات في مصفوفة
        $stats = [
            'total_users' => $totalUsers,
            'total_departments' => $totalDepartments,
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
            'departments' => $totalDepartments,
            'jobs' => $totalJobs,
            'applications' => $totalApplications,
            'approved_applications' => $acceptedApplications,
        ];
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDepartments',
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

    public function unifiedDashboard()
    {
        // إحصائيات شاملة للوحة التحكم الموحدة
        $stats = [
            'total_users' => User::count(),
            'approved_users' => User::where('approval_status', 'approved')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'admin');
                })->count(),
            'pending_users' => User::where('approval_status', 'pending')->count(),
            'rejected_users' => User::where('approval_status', 'rejected')->count(),
            'total_departments' => Department::count(),
            'total_jobs' => HajjJob::count(),
            'active_jobs' => HajjJob::where('status', 'active')->count(),
            'total_applications' => JobApplication::count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
            'approved_applications' => JobApplication::where('status', 'approved')->count(),
            'rejected_applications' => JobApplication::where('status', 'rejected')->count(),
            'total_contracts' => Contract::count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'signed_contracts' => Contract::where('status', 'signed')->count(),
        ];

        return view('admin.unified-dashboard', compact('stats'));
    }

    // إدارة المستخدمين
    public function users()
    {
        $users = User::with('roles')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }
    
    public function departments()
    {
        $departments = Department::with(['user'])
            ->withCount('jobs')
            ->latest()
            ->paginate(15);
        return view('admin.departments.index', compact('departments'));
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
            'role' => 'required|in:admin,department,employee',
            'department_name' => 'nullable|required_if:role,department|string|max:255',
            'department_phone' => 'nullable|string|max:20',
            'department_address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);
        
        try {
            DB::beginTransaction();
            
            // إنشاء المستخدم مع تحسين الأداء
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);
            
            $user->save();
            
            // تعيين الدور مباشرة بدون eager loading
            $user->roles()->attach(Role::where('name', $request->role)->value('id'));
            
            // إضافة معلومات الملف الشخصي بشكل مباشر
            if ($request->role === 'department') {
                $user->profile()->create([
                    'department_name' => $request->department_name,
                    'department_phone' => $request->department_phone,
                    'department_address' => $request->department_address,
                ]);
                
                // إنشاء قسم جديد
                Department::create([
                    'user_id' => $user->id,
                    'name' => $request->department_name,
                    'phone' => $request->department_phone,
                    'address' => $request->department_address,
                ]);
            } elseif ($request->role === 'employee') {
                $user->profile()->create([
                    'phone' => $request->phone,
                ]);
            }
            
            DB::commit();
            
            $roleText = $request->role == 'admin' ? 'مدير' : ($request->role == 'department' ? 'قسم' : 'موظف');
            
            return redirect()->route('admin.users.index')
                ->with('success', 'تم إنشاء ' . $roleText . ' جديد: ' . $user->name . ' بنجاح');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
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
            'role' => 'required|in:admin,department,employee',
            'department_name' => 'nullable|required_if:role,department|string|max:255',
            'department_phone' => 'nullable|string|max:20',
            'department_address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);
        
        try {
            DB::beginTransaction();
            
            // تحديث المعلومات الأساسية
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            
            // تحديث الدور
            $user->syncRoles($request->role);
            
            // تحديث أو إنشاء الملف الشخصي
            if ($request->role === 'department') {
                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'department_name' => $request->department_name,
                        'department_phone' => $request->department_phone,
                        'department_address' => $request->department_address,
                    ]
                );
                
                // تحديث أو إنشاء القسم
                Department::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $request->department_name,
                        'phone' => $request->department_phone,
                        'address' => $request->department_address,
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
            
            DB::commit();
            
            return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات ' . $user->name . ' بنجاح');
            
        } catch (\Exception $e) {
            DB::rollBack();
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
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} المستخدم بنجاح");
    }

    // إدارة الوظائف
    public function jobs()
    {
        $jobs = HajjJob::with(['department.user', 'applications'])
            ->withCount('applications')
            ->latest()
            ->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }
    
    public function toggleJobStatus(HajjJob $job)
    {
        $job->status = $job->status === 'active' ? 'inactive' : 'active';
        $job->save();
        
        $status = $job->status === 'active' ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} الوظيفة بنجاح");
    }
    
    public function deleteJob(HajjJob $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'تم حذف الوظيفة بنجاح');
    }

    /**
     * عرض جميع طلبات التوظيف
     */
    public function applications()
    {
        $applications = JobApplication::with([
            'user.profile',
            'job.department.user',
        ])->latest()->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }
    
    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'feedback' => 'nullable|string',
        ]);
        
        $oldStatus = $application->status;
        
        $application->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'reviewed_at' => now(),
        ]);
        
        // إذا تم قبول الطلب، نتأكد من عدم تغيير حالته لاحقاً إلا بإذن خاص
        if ($request->status === 'approved') {
            $application->update([
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => auth()->id()
            ]);
        }
        
        $statusText = [
            'pending' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ][$request->status];
        
        return redirect()->back()->with('success', "تم تحديث حالة الطلب إلى {$statusText} بنجاح");
    }

    /**
     * حساب مؤشرات الأداء الرئيسية (KPIs)
     */
    private function calculateKPIs()
    {
        // حساب نسبة القبول
        $totalApplications = JobApplication::count();
        $acceptedApplications = JobApplication::where('status', 'approved')->count();
        $acceptanceRate = $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 2) : 0;
        
        // متوسط وقت المراجعة (بالأيام)
        $avgReviewTime = JobApplication::whereNotNull('reviewed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, reviewed_at)) as avg_days')
            ->value('avg_days') ?? 0;
        
        // نسبة الوظائف النشطة
        $totalJobs = HajjJob::count();
        $activeJobs = HajjJob::where('status', 'active')->count();
        $activeJobsRate = $totalJobs > 0 ? round(($activeJobs / $totalJobs) * 100, 2) : 0;
        
        // متوسط عدد المتقدمين لكل وظيفة
        $avgApplicationsPerJob = $totalJobs > 0 ? round($totalApplications / $totalJobs, 2) : 0;

        // معدل نجاح التوظيف
        $successRate = $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 2) : 0;

        // نمو المستخدمين
        $currentMonthUsers = User::whereMonth('created_at', now()->month)->count();
        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $userGrowth = $lastMonthUsers > 0 ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 2) : 0;

        // معدل نشاط الأقسام
        $totalDepartments = User::role('department')->count();
        $activeDepartments = HajjJob::distinct('department_id')->count();
        $departmentActivityRate = $totalDepartments > 0 ? round(($activeDepartments / $totalDepartments) * 100, 2) : 0;

        // أكثر الأقسام نشاطاً
        $topDepartments = Department::withCount('jobs')
            ->orderBy('jobs_count', 'desc')
            ->take(3)
            ->get();

        // إحصائيات شهرية للرسم البياني
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            $monthAr = $date->translatedFormat('F Y');
            
            $monthlyStats[] = [
                'month' => $monthAr,
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'jobs' => HajjJob::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'applications' => JobApplication::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'accepted' => JobApplication::where('status', 'approved')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        
        return [
            'acceptance_rate' => $acceptanceRate,
            'avg_review_time' => round($avgReviewTime, 1),
            'active_jobs_rate' => $activeJobsRate,
            'avg_applications_per_job' => $avgApplicationsPerJob,
            'success_rate' => $successRate,
            'user_growth' => $userGrowth,
            'current_month_users' => $currentMonthUsers,
            'last_month_users' => $lastMonthUsers,
            'department_activity_rate' => $departmentActivityRate,
            'top_departments' => $topDepartments,
            'monthly_stats' => $monthlyStats,
        ];
    }

    public function editDepartment(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'activity_type' => 'nullable|string|max:100',
            'department_size' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'registration_number' => 'nullable|string|max:50',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'تم تحديث بيانات القسم بنجاح');
    }

    /**
     * تصدير الوظائف إلى Excel
     */
    public function exportJobs()
    {
        return Excel::download(new JobsExport, 'jobs.xlsx');
    }

    /**
     * تصدير طلبات التوظيف إلى Excel
     */
    public function exportApplications(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
            'department' => $request->get('department'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $export = new ApplicationsExport($filters);
        $fileName = 'applications_' . now()->format('Y_m_d_His');

        // PDF export removed - using Word documents only

        return Excel::download($export, $fileName . '.xlsx');
    }

    // PDF export functions removed - using Word documents only

    /**
     * عرض المتقدمين المقبولين
     */
    public function approvedApplications()
    {
        // عرض المستخدمين المعتمدين (الموظفين فقط) مع جميع معلوماتهم وطلباتهم
        $approvedUsers = User::where('approval_status', 'approved')
            ->where('id', '!=', auth()->id()) // استبعاد المدير الحالي
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'admin');
            })
            ->with([
                'profile',
                'applications' => function($query) {
                    $query->with(['job.department'])
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->latest('approved_at')
            ->paginate(10);
        
        // إحصائيات سريعة (للموظفين فقط)
        $stats = [
            'total_approved_users' => User::where('approval_status', 'approved')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'admin');
                })->count(),
            'users_with_applications' => User::where('approval_status', 'approved')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'admin');
                })
                ->whereHas('applications')->count(),
            'total_applications' => JobApplication::count(),
            'approved_applications' => JobApplication::where('status', 'approved')->count(),
        ];
        
        return view('admin.applications.approved', compact('approvedUsers', 'stats'));
    }

    public function createJob()
    {
        $departments = Department::where('status', 'active')->get();
        return view('admin.jobs.create', compact('departments'));
    }
    
    public function storeJob(Request $request)
    {
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
            'department_id' => 'required|exists:departments,id'
        ]);
        
        $job = HajjJob::create([
            'department_id' => $request->department_id,
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
        
        return redirect()->route('admin.jobs.index')
            ->with('success', 'تم إنشاء الوظيفة بنجاح');
    }
    
    public function editJob(HajjJob $job)
    {
        $departments = Department::where('status', 'active')->get();
        return view('admin.jobs.edit', compact('job', 'departments'));
    }
    
    public function updateJob(Request $request, HajjJob $job)
    {
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
            'department_id' => 'required|exists:departments,id'
        ]);
        
        $job->update($request->all());
        
        return redirect()->route('admin.jobs.index')
            ->with('success', 'تم تحديث الوظيفة بنجاح');
    }

    // API Methods للوحة التحكم الموحدة
    public function getUsers()
    {
        $users = User::with(['profile', 'roles'])
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'admin');
            })
            ->latest()
            ->take(100)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function getPendingApprovals()
    {
        $pendingUsers = User::where('approval_status', 'pending')
            ->with(['profile'])
            ->latest()
            ->take(100)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $pendingUsers
        ]);
    }

    public function getApprovedUsers()
    {
        $approvedUsers = User::where('approval_status', 'approved')
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'admin');
            })
            ->with([
                'profile',
                'applications' => function($query) {
                    $query->with(['job.department'])
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->latest('approved_at')
            ->take(100)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $approvedUsers
        ]);
    }

    public function getDepartments()
    {
        $departments = Department::with(['user', 'jobs'])
            ->withCount('jobs')
            ->latest()
            ->take(100)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    public function getJobs()
    {
        $jobs = HajjJob::with(['department.user', 'applications'])
            ->withCount('applications')
            ->latest()
            ->take(100)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    public function getApplications()
    {
        $applications = JobApplication::with(['user.profile', 'job.department'])
            ->latest()
            ->take(100)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    public function getContracts()
    {
        $contracts = \App\Models\Contract::with(['employee', 'department', 'job'])
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contracts
        ]);
    }

    public function getDashboardData()
    {
        // إحصائيات عامة
        $totalUsers = User::count();
        $totalDepartments = User::role('department')->count();
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
        $recentJobs = HajjJob::with(['department', 'applications'])->withCount('applications')->latest()->take(5)->get();
        $recentApplications = JobApplication::with(['user', 'job.department'])->latest()->take(5)->get();
        
        // مؤشرات الأداء الرئيسية (KPIs)
        $kpis = $this->calculateKPIs();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_departments' => $totalDepartments,
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
                ],
                'recent_users' => $recentUsers,
                'recent_jobs' => $recentJobs,
                'recent_applications' => $recentApplications,
                'kpis' => $kpis
            ]
        ]);
    }

    public function approveUser(User $user)
    {
        try {
            $user->update([
                'approval_status' => 'approved',
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم قبول المستخدم بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في العملية'
            ], 500);
        }
    }

    public function rejectUser(User $user)
    {
        try {
            $user->update([
                'approval_status' => 'rejected',
                'rejected_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض المستخدم بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في العملية'
            ], 500);
        }
    }

    /**
     * جلب تفاصيل المستخدم للعرض في النافذة المنبثقة
     */
    public function getUserDetails($userId)
    {
        try {
            $user = User::with(['profile'])
                ->where('id', $userId)
                ->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود'
                ], 404);
            }
            
            // إضافة URLs للمرفقات
            if ($user->profile) {
                $profile = $user->profile;
                
                // إضافة روابط المرفقات
                $profile->cv_url = $profile->cv_path ? Storage::url($profile->cv_path) : null;
                $profile->national_id_attachment_url = $profile->national_id_attachment ? Storage::url($profile->national_id_attachment) : null;
                $profile->iban_attachment_url = $profile->iban_attachment ? Storage::url($profile->iban_attachment) : null;
                $profile->national_address_attachment_url = $profile->national_address_attachment ? Storage::url($profile->national_address_attachment) : null;
                $profile->experience_certificate_url = $profile->experience_certificate ? Storage::url($profile->experience_certificate) : null;
            }
            
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب البيانات: ' . $e->getMessage()
            ], 500);
        }
    }
}
