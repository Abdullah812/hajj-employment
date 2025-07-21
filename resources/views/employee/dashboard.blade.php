@extends('layouts.app')

@section('title', 'لوحة تحكم الموظف')

@section('content')
<div class="container py-4">
    <!-- ترحيب -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body text-center py-5">
                    <h1 class="mb-3">أهلاً وسهلاً {{ auth()->user()->name }}</h1>
                    <p class="lead mb-0">مرحباً بك في لوحة تحكم الموظف</p>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h3 class="h4 mb-1">{{ $stats['total_users'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">إجمالي المستخدمين</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h3 class="h4 mb-1">{{ $stats['user_profile_complete'] ? 'مكتمل' : 'غير مكتمل' }}</h3>
                    <p class="text-muted mb-0">حالة الملف الشخصي</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-bell fa-2x text-info"></i>
                    </div>
                    <h3 class="h4 mb-1">{{ auth()->user()->unreadNotifications()->count() }}</h3>
                    <p class="text-muted mb-0">إشعارات غير مقروءة</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h3 class="h4 mb-1">{{ auth()->user()->created_at->format('Y') }}</h3>
                    <p class="text-muted mb-0">عضو منذ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- أقسام اللوحة -->
    <div class="row g-4">
        <!-- الملف الشخصي -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        الملف الشخصي
                    </h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->profile)
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>تم إكمال الملف الشخصي</span>
                        </div>
                        <p class="text-muted mb-3">يمكنك عرض وتحديث معلوماتك الشخصية</p>
                    @else
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-exclamation-circle text-warning me-2"></i>
                            <span>الملف الشخصي غير مكتمل</span>
                        </div>
                        <p class="text-muted mb-3">يرجى إكمال ملفك الشخصي</p>
                    @endif
                    
                    <a href="{{ route('employee.profile') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        إدارة الملف الشخصي
                    </a>
                </div>
            </div>
        </div>

        <!-- الإشعارات -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-bell text-info me-2"></i>
                        الإشعارات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-bell text-info me-2"></i>
                        <span>{{ auth()->user()->unreadNotifications()->count() }} إشعار غير مقروء</span>
                    </div>
                    <p class="text-muted mb-3">تابع آخر التحديثات والإشعارات</p>
                    
                    <a href="{{ route('notifications.index') }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i>
                        عرض الإشعارات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- رسالة ترحيبية -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h4>مرحباً بك في النظام</h4>
                    <p class="text-muted">تم حذف أنظمة الوظائف والطلبات. يمكنك إدارة ملفك الشخصي ومتابعة الإشعارات.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 