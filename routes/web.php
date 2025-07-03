<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ContractController;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// مسارات الوظائف العامة
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{job}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// مسارات المصادقة
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// مسار تحديث CSRF Token
Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
})->name('csrf-token');

// مسارات محمية
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // مسارات المدير
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // إدارة المستخدمين
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
        
        // إدارة الشركات
        Route::get('/companies', [AdminController::class, 'companies'])->name('admin.companies.index');
        
        // إدارة الموظفين
        Route::get('/employees', [AdminController::class, 'employees'])->name('admin.employees.index');
        
        // إدارة الوظائف
        Route::get('/jobs', [AdminController::class, 'jobs'])->name('admin.jobs.index');
        Route::post('/jobs/{job}/toggle-status', [AdminController::class, 'toggleJobStatus'])->name('admin.jobs.toggle-status');
        Route::delete('/jobs/{job}', [AdminController::class, 'deleteJob'])->name('admin.jobs.destroy');
        
        // إدارة طلبات التوظيف
        Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications.index');
        Route::put('/applications/{application}', [AdminController::class, 'updateApplicationStatus'])->name('admin.applications.update');
    });
    
    // مسارات الشركة
    Route::middleware(['role:company'])->prefix('company')->group(function () {
        Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('company.dashboard');
        Route::get('/profile', [CompanyController::class, 'profile'])->name('company.profile');
        Route::put('/profile', [CompanyController::class, 'updateProfile'])->name('company.profile.update');
        
        // إدارة الوظائف
        Route::get('/jobs', [CompanyController::class, 'jobs'])->name('company.jobs.index');
        Route::get('/jobs/create', [CompanyController::class, 'createJob'])->name('company.jobs.create');
        Route::post('/jobs', [CompanyController::class, 'storeJob'])->name('company.jobs.store');
        Route::get('/jobs/{job}', [CompanyController::class, 'showJob'])->name('company.jobs.show');
        Route::get('/jobs/{job}/edit', [CompanyController::class, 'editJob'])->name('company.jobs.edit');
        Route::put('/jobs/{job}', [CompanyController::class, 'updateJob'])->name('company.jobs.update');
        Route::delete('/jobs/{job}', [CompanyController::class, 'deleteJob'])->name('company.jobs.destroy');
        Route::post('/jobs/{job}/status', [CompanyController::class, 'updateJobStatus'])->name('company.jobs.status');
        
        // إدارة طلبات التوظيف
        Route::get('/applications', [CompanyController::class, 'applications'])->name('company.applications.index');
        Route::get('/applications/job/{job}', [CompanyController::class, 'jobApplications'])->name('company.applications.job');
        Route::put('/applications/{application}', [CompanyController::class, 'updateApplication'])->name('company.applications.update');
        Route::post('/applications/bulk', [CompanyController::class, 'bulkUpdateApplications'])->name('company.applications.bulk');
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
        Route::get('/{contract}/pdf', [ContractController::class, 'downloadPdf'])->name('contracts.pdf');
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
});
