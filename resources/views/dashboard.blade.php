@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">مرحباً بك، {{ auth()->user()->name }}</h1>
                <div class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    {{ date('Y-m-d') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">الوظائف المتاحة</h6>
                            <h3 class="mb-0">{{ rand(50, 100) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-briefcase fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">الشركات المسجلة</h6>
                            <h3 class="mb-0">{{ rand(20, 50) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-building fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">الطلبات المعلقة</h6>
                            <h3 class="mb-0">{{ rand(10, 30) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">الموظفين النشطين</h6>
                            <h3 class="mb-0">{{ rand(100, 200) }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإجراءات السريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                                تصفح الوظائف
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="#" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                                تقديم طلب
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="#" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-user-edit fa-2x mb-2 d-block"></i>
                                تحديث الملف الشخصي
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">النشاطات الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">تم تحديث الملف الشخصي</div>
                                <small class="text-muted">قبل ساعتين</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">تم تقديم طلب جديد</div>
                                <small class="text-muted">أمس</small>
                            </div>
                            <span class="badge bg-success rounded-pill">
                                <i class="fas fa-file-alt"></i>
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">تم تسجيل الدخول</div>
                                <small class="text-muted">منذ 3 أيام</small>
                            </div>
                            <span class="badge bg-info rounded-pill">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإشعارات</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>تذكير:</strong> لا تنس تحديث بياناتك الشخصية
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>مهم:</strong> آخر موعد للتقديم 15 يوليو
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 