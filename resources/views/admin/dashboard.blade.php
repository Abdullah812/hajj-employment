@extends('admin.layouts.app')

@section('title', 'لوحة تحكم المدير')

@section('admin_content')
<div class="container-fluid py-3">
    <!-- الترحيب -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0 shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">مرحباً {{ auth()->user()->name }}</h2>
                            <p class="card-text mb-0">لوحة التحكم الرئيسية لإدارة نظام التوظيف</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <i class="fas fa-crown fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الإحصائيات الرئيسية -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-primary mb-1">{{ $stats['total_users'] }}</h3>
                            <p class="text-muted mb-0 small">إجمالي المستخدمين</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-user-shield fa-2x text-success"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-success mb-1">{{ $stats['total_admins'] }}</h3>
                            <p class="text-muted mb-0 small">المديرين</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-warning mb-1">{{ $stats['pending_approvals'] }}</h3>
                            <p class="text-muted mb-0 small">طلبات معلقة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-check-circle fa-2x text-info"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-info mb-1">{{ $stats['approved_users'] }}</h3>
                            <p class="text-muted mb-0 small">مستخدمين معتمدين</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات إضافية -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-alt fa-2x text-secondary"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-secondary mb-1">{{ $stats['new_users_this_month'] }}</h3>
                            <p class="text-muted mb-0 small">مستخدمين جدد هذا الشهر</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-dark bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-day fa-2x text-dark"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-dark mb-1">{{ $stats['today_registrations'] }}</h3>
                            <p class="text-muted mb-0 small">تسجيلات اليوم</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث المستخدمين -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            أحدث المستخدمين
                        </h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentUsers && $recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>حالة الاعتماد</th>
                                        <th>تاريخ التسجيل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <span class="fw-medium">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $user->email }}</td>
                                        <td>
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">لا يوجد دور</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->approval_status === 'approved')
                                                <span class="badge bg-success">معتمد</span>
                                            @elseif($user->approval_status === 'pending')
                                                <span class="badge bg-warning">معلق</span>
                                            @elseif($user->approval_status === 'rejected')
                                                <span class="badge bg-danger">مرفوض</span>
                                            @else
                                                <span class="badge bg-secondary">غير محدد</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد مستخدمين مسجلين حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link me-2 text-primary"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                إضافة مستخدم جديد
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                إدارة المستخدمين
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-info w-100 p-3">
                                <i class="fas fa-bell fa-2x mb-2 d-block"></i>
                                الإشعارات
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning w-100 p-3">
                                <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                تحديث الصفحة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 