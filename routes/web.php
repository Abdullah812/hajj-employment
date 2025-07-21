<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Auth\AuthController;
// use App\Http\Controllers\Department\DepartmentController; - تم حذف نظام الأقسام
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Admin\AdminController;
// use App\Http\Controllers\JobController; - تم حذف نظام الوظائف
// use App\Http\Controllers\ContractController; - تم حذف نظام العقود
// PDF import removed - using Word documents only
// use App\Http\Controllers\Admin\ReportsController; - تم حذف نظام التقارير

Route::get('/', function () {
    // إذا كان المستخدم مسجل دخول، وجهه إلى لوحة التحكم المناسبة
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect('/admin/dashboard');
        // } elseif ($user->hasRole('department')) {
            // return redirect('/department/dashboard'); - تم حذف نظام الأقسام
        } elseif ($user->hasRole('employee')) {
            return redirect('/employee/dashboard');
        }
        return redirect('/dashboard');
    }
    
    // عرض الصفحة الرئيسية الأساسية
    return view('welcome');
})->name('home');

// مسارات الوظائف العامة
// جميع routes الوظائف العامة - تم حذف النظام

// مسارات الأخبار العامة - تم حذفها

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
        
        // إدارة الأقسام - تم حذف النظام
        
        // إدارة الموظفين - تم حذف النظام
        
        // إدارة الوظائف - تم حذف النظام
        
        // إدارة طلبات التوظيف - تم حذف النظام
        
        // API routes للوحة التحكم الموحدة - تم إصلاح 405 Method Not Allowed
        Route::prefix('api')->middleware(['web', 'auth'])->group(function () {
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
            // Route::get('/departments', [AdminController::class, 'getDepartments'])->name('admin.api.departments'); - تم حذف النظام
            // Route::get('/jobs', [AdminController::class, 'getJobs'])->name('admin.api.jobs'); - تم حذف النظام
            // Route::get('/applications', [AdminController::class, 'getApplications'])->name('admin.api.applications'); - تم حذف النظام
            // Route::get('/contracts', [AdminController::class, 'getContracts'])->name('admin.api.contracts'); - تم حذف نظام العقود
            Route::get('/user-details/{userId}', [AdminController::class, 'getUserDetails'])->name('admin.api.user-details');
            

        });
        // Route::get('/applications/export', [AdminController::class, 'exportApplications'])->name('applications.export'); - تم حذف النظام
        
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
    // مسارات الأقسام - تم حذف النظام بالكامل
    
    // مسارات الموظف
    Route::middleware(['role:employee'])->prefix('employee')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
        Route::get('/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
        Route::put('/profile', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');
        Route::post('/profile/upload-cv', [EmployeeController::class, 'uploadCV'])->name('employee.profile.upload-cv');
        
        // إدارة طلبات التوظيف - تم حذف النظام
    });
    
    // مسارات العقود - تم حذفها
    
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
        // تم حذف جميع export routes للوظائف والطلبات
    });

    // مسارات التقارير - تم حذف النظام بالكامل
    
    // API endpoints عامة للإدارة
    Route::middleware(['auth', 'role:admin'])->prefix('admin/api')->name('admin.api.')->group(function () {
        // Route::get('/departments', [AdminController::class, 'getDepartments'])->name('departments'); - تم حذف النظام
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

// Content Management Routes - تم حذف إدارة المحتوى بالكامل

// جميع routes العقود التجريبية - تم حذفها

// Word contract download for actual contracts - تم حذف نظام العقود

// Routes للملفات من قاعدة البيانات فقط - لا file storage
Route::get('/profile/file/{type}/{userId}', [App\Http\Controllers\FileController::class, 'viewFile'])
    ->name('profile.file.view')
    ->middleware(['auth']);

Route::get('/profile/file/{type}/{userId}/download', [App\Http\Controllers\FileController::class, 'downloadFile'])
    ->name('profile.file.download')
    ->middleware(['auth']);

// Routes لعرض صور المحتوى من قاعدة البيانات
Route::get('/content/image/{type}/{id}', [App\Http\Controllers\FileController::class, 'viewContentImage'])
    ->name('content.image.view');

// Analytics Routes - لوحة الإحصائيات المتقدمة
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/live-data', [App\Http\Controllers\Admin\AnalyticsController::class, 'liveData'])->name('analytics.live-data');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('analytics.export');
});

// ================================================
// مسارات طلبات مكة المفتوحة - تم حذفها
// ================================================
