<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ContractController;
// PDF import removed - using Word documents only
use App\Http\Controllers\Admin\ReportsController;

Route::get('/', function () {
    // إذا كان المستخدم مسجل دخول، وجهه إلى لوحة التحكم المناسبة
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('department')) {
            return redirect('/department/dashboard');
        } elseif ($user->hasRole('employee')) {
            return redirect('/employee/dashboard');
        }
        return redirect('/dashboard');
    }
    
    // جلب البيانات للصفحة الرئيسية
    $news = [];
    $testimonials = [];
    $galleries = [];
    $videos = [];
    
    try {
        if (Schema::hasTable('news')) {
            $news = App\Models\News::where('status', 'published')
                ->latest('published_at')
                ->take(6)
                ->get();
        }
        
        if (Schema::hasTable('testimonials')) {
            $testimonials = App\Models\Testimonial::where('is_active', true)
                ->latest()
                ->take(6)
                ->get();
        }
        
        if (Schema::hasTable('galleries')) {
            $galleries = App\Models\Gallery::where('is_active', true)
                ->orderBy('order_sort', 'asc')
                ->take(6)
                ->get();
        }
        
        if (Schema::hasTable('company_videos')) {
            $videos = App\Models\CompanyVideo::where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->take(1)
                ->get();
        }
    } catch (Exception $e) {
        // إذا حدث خطأ في قاعدة البيانات، استخدم arrays فارغة
    }
    
    return view('welcome', compact('news', 'testimonials', 'galleries', 'videos'));
})->name('home');

// مسارات الوظائف العامة
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// مسارات الأخبار العامة
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

// مسارات المصادقة
Route::middleware(['guest'])->group(function () {
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// مسار تحديث CSRF Token
Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
})->name('csrf-token');

// 🧪 Routes اختبار خارج أي middleware (للتشخيص فقط)
Route::get('/test-simple/{id}', function($id) {
    return response()->json([
        'success' => true,
        'message' => 'Route بسيط يعمل بدون middleware',
        'id' => $id,
        'timestamp' => now(),
        'request_method' => request()->method(),
        'url' => request()->url()
    ]);
});

Route::get('/test-user-api/{userId}', function($userId) {
    try {
        $user = \App\Models\User::with('profile')->find($userId);
        return response()->json([
            'success' => true,
            'message' => 'User API بدون middleware',
            'user_found' => $user ? true : false,
            'user_name' => $user ? $user->name : null,
            'user_id' => $userId,
            'profile_exists' => $user && $user->profile ? true : false,
            'auth_check' => auth()->check(),
            'current_user' => auth()->user() ? auth()->user()->name : 'غير مسجل'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
         }
 });

// 🚑 Route بديل للـ user-details خارج admin middleware (للتشخيص)
Route::get('/admin/api/user-details-alt/{userId}', [\App\Http\Controllers\Admin\AdminController::class, 'getUserDetails'])
    ->middleware(['auth'])
    ->name('admin.api.user-details-alt');

// 🔍 Route لعرض جميع الـ routes (للتشخيص فقط)
Route::get('/debug-routes', function() {
    $routes = [];
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        if (str_contains($route->uri(), 'admin') || str_contains($route->uri(), 'user-details')) {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->gatherMiddleware()
            ];
        }
    }
    return response()->json(['routes' => $routes]);
});

// مسارات محمية
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // مسارات المدير
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/unified-dashboard', [AdminController::class, 'unifiedDashboard'])->name('admin.unified-dashboard');
        
        // إدارة المستخدمين
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        Route::post('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
        Route::post('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
        
        // إدارة الأقسام
        Route::get('/departments', [AdminController::class, 'departments'])->name('admin.departments.index');
        Route::get('/departments/{department}/edit', [AdminController::class, 'editDepartment'])->name('admin.departments.edit');
        Route::put('/departments/{department}', [AdminController::class, 'updateDepartment'])->name('admin.departments.update');
        
        // إدارة الموظفين
        Route::get('/employees', [AdminController::class, 'employees'])->name('admin.employees.index');
        
        // إدارة الوظائف
        Route::get('/jobs', [AdminController::class, 'jobs'])->name('admin.jobs.index');
        Route::get('/jobs/create', [AdminController::class, 'createJob'])->name('admin.jobs.create');
        Route::post('/jobs', [AdminController::class, 'storeJob'])->name('admin.jobs.store');
        Route::get('/jobs/{job}/edit', [AdminController::class, 'editJob'])->name('admin.jobs.edit');
        Route::put('/jobs/{job}', [AdminController::class, 'updateJob'])->name('admin.jobs.update');
        Route::post('/jobs/{job}/toggle-status', [AdminController::class, 'toggleJobStatus'])->name('admin.jobs.toggle-status');
        Route::delete('/jobs/{job}', [AdminController::class, 'deleteJob'])->name('admin.jobs.destroy');
        
        // إدارة طلبات التوظيف
        Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications.index');
        Route::put('/applications/{application}', [AdminController::class, 'updateApplicationStatus'])->name('admin.applications.update');
        
        // مسارات طلبات التوظيف
        Route::get('/applications/approved', [AdminController::class, 'approvedApplications'])->name('applications.approved');
        
        // API routes للوحة التحكم الموحدة
        Route::prefix('api')->middleware(['web'])->group(function () {
            // إضافة headers للـ CORS
            Route::options('{any}', function() {
                return response('', 200)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            })->where('any', '.*');
            
            Route::get('/dashboard', [AdminController::class, 'getDashboardData'])->name('admin.api.dashboard');
            Route::get('/users', [AdminController::class, 'getUsers'])->name('admin.api.users');
            Route::get('/approvals', [AdminController::class, 'getPendingApprovals'])->name('admin.api.approvals');
            Route::get('/approved-users', [AdminController::class, 'getApprovedUsers'])->name('admin.api.approved');
            Route::get('/departments', [AdminController::class, 'getDepartments'])->name('admin.api.departments');
            Route::get('/jobs', [AdminController::class, 'getJobs'])->name('admin.api.jobs');
            Route::get('/applications', [AdminController::class, 'getApplications'])->name('admin.api.applications');
            Route::get('/contracts', [AdminController::class, 'getContracts'])->name('admin.api.contracts');
            Route::get('/user-details/{userId}', [AdminController::class, 'getUserDetails'])->name('admin.api.user-details');
            
            // Route تشخيص مؤقت
            Route::get('/test-debug/{userId}', function($userId) {
                return response()->json([
                    'success' => true,
                    'message' => 'Route يعمل',
                    'userId' => $userId,
                    'auth' => auth()->check(),
                    'user' => auth()->user() ? auth()->user()->name : 'غير مسجل',
                    'roles' => auth()->user() ? auth()->user()->getRoleNames() : 'لا توجد أدوار'
                ]);
            })->name('admin.api.debug');
        });
        Route::get('/applications/export', [AdminController::class, 'exportApplications'])->name('applications.export');
        
        // 🔧 Route إضافي للاختبار داخل admin middleware
        Route::get('/debug-user/{userId}', function($userId) {
            return response()->json([
                'success' => true,
                'message' => 'Admin middleware route يعمل',
                'userId' => $userId,
                'middleware' => 'role:admin',
                'auth' => auth()->check(),
                'user' => auth()->user() ? auth()->user()->name : null,
                'roles' => auth()->user() ? auth()->user()->getRoleNames()->toArray() : []
            ]);
        })->name('admin.debug.user');
    });
    
    // مسارات القسم
    Route::middleware(['auth'])->prefix('department')->name('department.')->group(function () {
        Route::get('/dashboard', [DepartmentController::class, 'dashboard'])->name('dashboard')->middleware('role:department|admin');
        
        // إدارة الوظائف
        Route::get('/jobs', [DepartmentController::class, 'jobs'])->name('jobs.index')->middleware('role:department|admin');
        Route::get('/jobs/create', [DepartmentController::class, 'createJob'])->name('jobs.create')->middleware('role:department|admin');
        Route::post('/jobs', [DepartmentController::class, 'storeJob'])->name('jobs.store')->middleware('role:department|admin');
        Route::get('/jobs/{job}', [DepartmentController::class, 'showJob'])->name('jobs.show')->middleware('role:department|admin');
        Route::get('/jobs/{job}/edit', [DepartmentController::class, 'editJob'])->name('jobs.edit')->middleware('role:department|admin');
        Route::put('/jobs/{job}', [DepartmentController::class, 'updateJob'])->name('jobs.update')->middleware('role:department|admin');
        Route::delete('/jobs/{job}', [DepartmentController::class, 'deleteJob'])->name('jobs.destroy')->middleware('role:department|admin');
        Route::post('/jobs/{job}/status', [DepartmentController::class, 'updateJobStatus'])->name('jobs.status')->middleware('role:department|admin');
        
        // إدارة طلبات التوظيف
        Route::get('/applications', [DepartmentController::class, 'applications'])->name('applications.index')->middleware('role:department|admin');
        Route::get('/applications/job/{job}', [DepartmentController::class, 'jobApplications'])->name('applications.job')->middleware('role:department|admin');
        Route::put('/applications/{application}', [DepartmentController::class, 'updateApplication'])->name('applications.update')->middleware('role:department|admin');
        Route::post('/applications/bulk', [DepartmentController::class, 'bulkUpdateApplications'])->name('applications.bulk')->middleware('role:department|admin');
    });
    
    // مسارات الموظف
    Route::middleware(['role:employee'])->prefix('employee')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
        Route::get('/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
        Route::put('/profile', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');
        Route::post('/profile/upload-cv', [EmployeeController::class, 'uploadCV'])->name('employee.profile.upload-cv');
        
        // إدارة طلبات التوظيف
        Route::get('/applications', [EmployeeController::class, 'applications'])->name('employee.applications');
        Route::get('/applications/{application}', [EmployeeController::class, 'showApplication'])->name('employee.applications.show');
        Route::delete('/applications/{application}', [EmployeeController::class, 'cancelApplication'])->name('employee.applications.cancel');
        Route::post('/jobs/{job}/apply', [EmployeeController::class, 'applyForJob'])->name('employee.jobs.apply');
    });
    
    // مسارات العقود
    Route::prefix('contracts')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('contracts.index');
        Route::get('/{contract}', [ContractController::class, 'show'])->name('contracts.show');
        Route::post('/create/{application}', [ContractController::class, 'createFromApplication'])->name('contracts.create');
        Route::put('/{contract}/status', [ContractController::class, 'updateStatus'])->name('contracts.status');
        Route::get('/{contract}/sign', [ContractController::class, 'signaturePage'])->name('contracts.sign-page');
        Route::post('/{contract}/sign', [ContractController::class, 'sign'])->name('contracts.sign');
        // PDF route removed - using Word documents only
        Route::post('/{contract}/send', [ContractController::class, 'sendToEmployee'])->name('contracts.send');
        Route::post('/{contract}/cancel', [ContractController::class, 'cancel'])->name('contracts.cancel');
    });
    
    // مسارات الإشعارات
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [\App\Http\Controllers\NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('recent');
        Route::get('/count', [\App\Http\Controllers\NotificationController::class, 'getCount'])->name('count');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/{notification}/toggle', [\App\Http\Controllers\NotificationController::class, 'toggleRead'])->name('toggle');
        Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/clear-old', [\App\Http\Controllers\NotificationController::class, 'clearOld'])->name('clear-old');
        Route::post('/clear-all', [\App\Http\Controllers\NotificationController::class, 'clearAll'])->name('clear-all');
        
        // للإدارة فقط
        Route::post('/test', [\App\Http\Controllers\NotificationController::class, 'createTest'])->name('test')->middleware('role:admin');
        Route::post('/broadcast', [\App\Http\Controllers\NotificationController::class, 'sendBroadcast'])->name('broadcast')->middleware('role:admin');
    });

    // مسارات تصدير التقارير
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/export/jobs/excel', [AdminController::class, 'exportJobs'])->name('admin.export.jobs.excel');
        // PDF export routes removed - using Word documents only
        Route::get('/admin/export/applications/excel', [AdminController::class, 'exportApplications'])->name('admin.export.applications.excel');
    });

    // مسارات التقارير
    Route::middleware(['auth', 'role:admin'])->prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/jobs', [ReportsController::class, 'jobs'])->name('jobs');
        Route::get('/applications', [ReportsController::class, 'applications'])->name('applications');
        Route::get('/jobs/export', [ReportsController::class, 'exportJobs'])->name('jobs.export');
        Route::get('/applications/export', [ReportsController::class, 'exportApplications'])->name('applications.export');
        
        // API endpoints للتقارير المتقدمة
        Route::get('/api/filtered-data', [ReportsController::class, 'getFilteredData'])->name('api.filtered-data');
        Route::get('/api/additional-stats', [ReportsController::class, 'getAdditionalStats'])->name('api.additional-stats');
        Route::get('/api/analysis/{type}', [ReportsController::class, 'getAdvancedAnalysis'])->name('api.analysis');
    });
    
    // API endpoints عامة للإدارة
    Route::middleware(['auth', 'role:admin'])->prefix('admin/api')->name('admin.api.')->group(function () {
        Route::get('/departments', [AdminController::class, 'getDepartments'])->name('departments');
    });
});

// مسارات إدارة موافقات المستخدمين
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users/approvals', [App\Http\Controllers\Admin\UserApprovalController::class, 'index'])
        ->name('admin.users.approvals.index');
    Route::post('/admin/users/{user}/approve', [App\Http\Controllers\Admin\UserApprovalController::class, 'approve'])
        ->name('admin.users.approvals.approve');
    Route::post('/admin/users/{user}/reject', [App\Http\Controllers\Admin\UserApprovalController::class, 'reject'])
        ->name('admin.users.approvals.reject');
});

// Content Management Routes (Admin Only)
Route::middleware(['auth', 'role:admin'])->prefix('admin/content')->name('admin.content.')->group(function () {
    // News Management
    Route::get('/news', [App\Http\Controllers\Admin\ContentController::class, 'newsIndex'])->name('news.index');
    Route::get('/news/create', [App\Http\Controllers\Admin\ContentController::class, 'newsCreate'])->name('news.create');
    Route::post('/news', [App\Http\Controllers\Admin\ContentController::class, 'newsStore'])->name('news.store');
    Route::get('/news/{news}/edit', [App\Http\Controllers\Admin\ContentController::class, 'newsEdit'])->name('news.edit');
    Route::put('/news/{news}', [App\Http\Controllers\Admin\ContentController::class, 'newsUpdate'])->name('news.update');
    Route::delete('/news/{news}', [App\Http\Controllers\Admin\ContentController::class, 'newsDestroy'])->name('news.destroy');
    
    // Gallery Management
    Route::get('/gallery', [App\Http\Controllers\Admin\ContentController::class, 'galleryIndex'])->name('gallery.index');
    Route::get('/gallery/create', [App\Http\Controllers\Admin\ContentController::class, 'galleryCreate'])->name('gallery.create');
    Route::post('/gallery', [App\Http\Controllers\Admin\ContentController::class, 'galleryStore'])->name('gallery.store');
    Route::get('/gallery/{gallery}/edit', [App\Http\Controllers\Admin\ContentController::class, 'galleryEdit'])->name('gallery.edit');
    Route::put('/gallery/{gallery}', [App\Http\Controllers\Admin\ContentController::class, 'galleryUpdate'])->name('gallery.update');
    Route::delete('/gallery/{gallery}', [App\Http\Controllers\Admin\ContentController::class, 'galleryDestroy'])->name('gallery.destroy');
    
    // Testimonials Management
    Route::get('/testimonials', [App\Http\Controllers\Admin\ContentController::class, 'testimonialsIndex'])->name('testimonials.index');
    Route::get('/testimonials/create', [App\Http\Controllers\Admin\ContentController::class, 'testimonialsCreate'])->name('testimonials.create');
    Route::post('/testimonials', [App\Http\Controllers\Admin\ContentController::class, 'testimonialsStore'])->name('testimonials.store');
    Route::get('/testimonials/{testimonial}/edit', [App\Http\Controllers\Admin\ContentController::class, 'testimonialsEdit'])->name('testimonials.edit');
    Route::put('/testimonials/{testimonial}', [App\Http\Controllers\Admin\ContentController::class, 'testimonialsUpdate'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [App\Http\Controllers\Admin\ContentController::class, 'testimonialsDestroy'])->name('testimonials.destroy');
    
    // Videos Management
    Route::get('/videos', [App\Http\Controllers\Admin\ContentController::class, 'videosIndex'])->name('videos.index');
    Route::get('/videos/create', [App\Http\Controllers\Admin\ContentController::class, 'videosCreate'])->name('videos.create');
    Route::post('/videos', [App\Http\Controllers\Admin\ContentController::class, 'videosStore'])->name('videos.store');
    Route::get('/videos/{video}/edit', [App\Http\Controllers\Admin\ContentController::class, 'videosEdit'])->name('videos.edit');
    Route::put('/videos/{video}', [App\Http\Controllers\Admin\ContentController::class, 'videosUpdate'])->name('videos.update');
    Route::delete('/videos/{video}', [App\Http\Controllers\Admin\ContentController::class, 'videosDestroy'])->name('videos.destroy');
});

// Test PDF route removed - using Word documents only

// Test HTML route
Route::get('/test-html', function () {
    $contract = (object) [
        'contract_number' => 'MMS-2025-0001',
        'department_name' => 'شركة مناسك المشاعر',
        'department_commercial_register' => '4031275261',
        'department_address' => 'الرياض - المملكة العربية السعودية',
        'department_phone' => '+966112345678',
        'department_email' => 'info@manasik.com',
        'employee_name' => 'أحمد محمد الأحمد',
        'employee_national_id' => '1059605210',
        'employee_nationality' => 'سعودي',
        'employee_phone' => '0598100274',
        'employee_address' => 'الرياض - المملكة العربية السعودية',
        'job_description' => 'موظف خدمات الحج والعمرة',
        'salary' => 4500,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
        'working_hours_per_day' => 8,
        'status' => 'active',
        'status_text' => 'نشط',
        'department_signature' => false,
        'employee_signature' => false,
        'department_signed_at' => null,
        'employee_signed_at' => null,
        'special_terms' => 'يلتزم الموظف بارتداء الزي الموحد أثناء العمل وتطبيق بروتوكولات السلامة.',
        'department_representative_name' => 'محمد بن سعد المطيري',
        'department_representative_title' => 'مدير الموارد البشرية',
    ];
    
    return view('contracts.word_template', compact('contract'));
})->name('test.html');

// Preview Word template in browser
Route::get('/preview-word', function () {
    $contract = (object) [
        'contract_number' => 'MMS-2025-0001',
        'department_name' => 'شركة مناسك المشاعر',
        'department_commercial_register' => '4031275261',
        'department_address' => 'الرياض - المملكة العربية السعودية',
        'department_phone' => '+966112345678',
        'department_email' => 'info@manasik.com',
        'employee_name' => 'أحمد محمد الأحمد',
        'employee_national_id' => '1059605210',
        'employee_nationality' => 'سعودي',
        'employee_phone' => '0598100274',
        'employee_address' => 'الرياض - المملكة العربية السعودية',
        'job_description' => 'موظف خدمات الحج والعمرة',
        'salary' => 4500,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
        'working_hours_per_day' => 8,
        'status' => 'active',
        'status_text' => 'نشط',
        'department_signature' => true,
        'employee_signature' => false,
        'department_signed_at' => now(),
        'employee_signed_at' => null,
        'special_terms' => 'يلتزم الموظف بارتداء الزي الموحد أثناء العمل وتطبيق بروتوكولات السلامة.',
        'department_representative_name' => 'محمد بن سعد المطيري',
        'department_representative_title' => 'مدير الموارد البشرية',
    ];
    
    return view('contracts.word_template', compact('contract'));
})->name('preview.word');

// Test GPDF route removed - using Word documents only

// Test Word Document Generation
Route::get('/test-word', function () {
    $contract = (object) [
        'contract_number' => 'MMS-2025-0001',
        'department' => (object) [
            'name' => 'شركة مناسك المشاعر',
            'commercial_register' => '4031275261',
            'phone' => '+966112345678'
        ],
        'employee' => (object) [
            'name' => 'أحمد محمد السعيد',
            'profile' => (object) [
                'national_id' => '1059605210',
                'nationality' => 'سعودي',
                'phone' => '0598100274',
                'address' => 'مكة المكرمة - المملكة العربية السعودية'
            ]
        ],
        'job' => (object) [
            'title' => 'موظف خدمات الحج والعمرة'
        ],
        'salary' => 4500,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
        'working_hours_per_day' => 8,
        'working_days_per_week' => 6,
        'status' => 'نشط',
        'special_terms' => null,
        'created_at' => now()
    ];
    
    // إنشاء HTML متوافق مع Word
    $html = view('contracts.word_template', compact('contract'))->render();
    
    // إرجاع الملف كـ Word document
    $filename = 'contract-' . ($contract->contract_number ?? 'MMS-2025-001') . '.doc';
    
    return response($html, 200, [
        'Content-Type' => 'application/msword',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Content-Transfer-Encoding' => 'binary',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0'
    ]);
})->name('test.word');

// Test Word HTML Preview
Route::get('/test-word-html', function () {
    $contract = (object) [
        'contract_number' => 'MMS-2025-0001',
        'department' => (object) [
            'name' => 'شركة مناسك المشاعر',
            'commercial_register' => '4031275261',
            'phone' => '+966112345678'
        ],
        'employee' => (object) [
            'name' => 'أحمد محمد السعيد',
            'profile' => (object) [
                'national_id' => '1059605210',
                'nationality' => 'سعودي',
                'phone' => '0598100274',
                'address' => 'مكة المكرمة - المملكة العربية السعودية'
            ]
        ],
        'job' => (object) [
            'title' => 'موظف خدمات الحج والعمرة'
        ],
        'salary' => 4500,
        'start_date' => now(),
        'end_date' => now()->addMonths(3),
        'working_hours_per_day' => 8,
        'working_days_per_week' => 6,
        'status' => 'نشط',
        'special_terms' => null,
        'created_at' => now()
    ];
    
    return view('contracts.word_template', compact('contract'));
})->name('test.word.html');

// Quick test link
Route::get('/contract-ready', function () {
    return '<div style="font-family: Arial; padding: 40px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; min-height: 100vh;">
        <h1 style="font-size: 3em; margin-bottom: 20px;">🎉 نظام العقود الجديد جاهز!</h1>
        <h2 style="font-size: 1.5em; margin-bottom: 30px;">تصميم احترافي بألوان متدرجة</h2>
        
        <div style="max-width: 800px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; backdrop-filter: blur(10px);">
            <h3 style="margin-bottom: 20px;">روابط التجربة:</h3>
            <p><a href="/test-html" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; margin: 10px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">🔍 معاينة التصميم</a></p>
            <p><a href="/preview-word" style="background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; margin: 10px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">📄 معاينة مع توقيع</a></p>
            <p><a href="/test-word" style="background: linear-gradient(135deg, #00b894 0%, #00a085 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; margin: 10px; display: inline-block; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">📥 تحميل Word</a></p>
            
            <hr style="margin: 40px 0; border: none; height: 2px; background: rgba(255,255,255,0.3);">
            
            <h3 style="margin-bottom: 20px;">المميزات الجديدة:</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                    <h4 style="color: #74b9ff;">🎨 تصميم احترافي</h4>
                    <p>ألوان متدرجة وتخطيط عصري</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                    <h4 style="color: #fd79a8;">📄 قالب Word مطور</h4>
                    <p>قابل للتعديل ومتوافق مع Word</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                    <h4 style="color: #00b894;">✅ نصوص عربية مثالية</h4>
                    <p>لا توجد مشاكل في عرض النصوص</p>
                </div>
                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                    <h4 style="color: #fdcb6e;">🔧 سهولة الاستخدام</h4>
                    <p>واجهة بسيطة وسهلة التنقل</p>
                </div>
            </div>
        </div>
    </div>';
});

// Contract Status and Test Links
Route::get('/contract-status', function () {
    return '<div style="font-family: Arial; padding: 40px; text-align: center; background: #f8f9fa;">
        <h1 style="color: #28a745;">✅ Contract System Status</h1>
        <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="color: #007cba;">Active Solution</h2>
            <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin: 30px 0;">
                
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); min-width: 250px;">
                    <h3 style="color: #2e86de;">📝 Word Solution</h3>
                    <p>Perfect Arabic Word documents</p>
                    <div style="margin: 15px 0;">
                        <a href="/test-word-html" style="background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;">HTML Preview</a>
                        <a href="/test-word" style="background: #2e86de; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px; display: inline-block;">Download Word</a>
                    </div>
                </div>
            </div>
            
            <h2 style="color: #007cba;">Word Solution Features (Recommended)</h2>
            <ul style="text-align: left; max-width: 600px; margin: 20px auto; font-size: 14px;">
                <li>✅ Perfect Arabic text rendering</li>
                <li>✅ Editable by users in Microsoft Word</li>
                <li>✅ Professional Arabic RTL layout</li>
                <li>✅ No font or encoding issues</li>
                <li>✅ Easy signature and printing</li>
                <li>✅ Word-compatible HTML format</li>
                <li>✅ Ready for production use</li>
            </ul>
            
            <div style="margin-top: 40px; padding: 20px; background: #e8f5e8; border-radius: 10px; border-left: 5px solid #28a745;">
                <h3 style="color: #155724;">🎉 Problem Completely Solved!</h3>
                <p><strong>Word Solution:</strong> Perfect Arabic rendering, fully editable, professional layout</p>
                <p><strong>PDF Solution:</strong> Mixed template with proper Arabic support</p>
                <p><strong>Result:</strong> No more reversed text, perfect formatting, ready for production!</p>
            </div>
        </div>
    </div>';
})->name('contract.status');

// Word contract download for actual contracts
Route::get('/contracts/{contract}/download-word', [App\Http\Controllers\ContractController::class, 'downloadWordContract'])
    ->name('contracts.download.word')
    ->middleware('auth');

// Analytics Routes - لوحة الإحصائيات المتقدمة
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/live-data', [App\Http\Controllers\Admin\AnalyticsController::class, 'liveData'])->name('analytics.live-data');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('analytics.export');
});

// ================================================
// مسارات طلبات مكة المفتوحة (بدون تسجيل دخول)
// ================================================

Route::prefix('mecca')->name('mecca.')->group(function () {
    // عرض نموذج التقديم لوظيفة معينة
    Route::get('/jobs/{job}/apply', [App\Http\Controllers\MeccaApplicationController::class, 'showApplicationForm'])
        ->name('apply');
    
    // تقديم الطلب
    Route::post('/jobs/{job}/submit', [App\Http\Controllers\MeccaApplicationController::class, 'submitApplication'])
        ->name('submit');
    
    // صفحة تتبع الطلبات
    Route::get('/track', [App\Http\Controllers\MeccaApplicationController::class, 'trackApplication'])
        ->name('track');
    
    // تتبع طلب معين
    Route::get('/track/{referenceNumber}', [App\Http\Controllers\MeccaApplicationController::class, 'trackApplication'])
        ->name('track.show');
    
    // البحث في الطلبات (POST)
    Route::post('/track', [App\Http\Controllers\MeccaApplicationController::class, 'trackApplication'])
        ->name('track.search');
});
