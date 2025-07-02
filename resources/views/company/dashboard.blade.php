@extends('layouts.app')

@section('title', 'لوحة تحكم الشركة - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- الترحيب -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">مرحباً {{ auth()->user()->name }}</h2>
                            <p class="card-text mb-0">إدارة الوظائف وطلبات التوظيف في شركة مناسك المشاعر</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <i class="fas fa-building fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-briefcase fa-2x text-primary"></i>
                    </div>
                    <h3 class="h4 text-primary">{{ $stats['total_jobs'] }}</h3>
                    <p class="text-muted mb-0">إجمالي الوظائف</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h3 class="h4 text-success">{{ $stats['active_jobs'] }}</h3>
                    <p class="text-muted mb-0">الوظائف النشطة</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-2x text-warning"></i>
                    </div>
                    <h3 class="h4 text-warning">{{ $stats['total_applications'] }}</h3>
                    <p class="text-muted mb-0">إجمالي الطلبات</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                    <h3 class="h4 text-info">{{ $stats['pending_applications'] }}</h3>
                    <p class="text-muted mb-0">طلبات قيد المراجعة</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- إحصائيات مفصلة -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>إحصائيات مفصلة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-check text-success"></i>
                                </div>
                                <h4 class="text-success">{{ $stats['approved_applications'] }}</h4>
                                <p class="text-muted mb-0 small">طلبات مقبولة</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-times text-danger"></i>
                                </div>
                                <h4 class="text-danger">{{ $stats['rejected_applications'] }}</h4>
                                <p class="text-muted mb-0 small">طلبات مرفوضة</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-ban text-secondary"></i>
                                </div>
                                <h4 class="text-secondary">{{ $stats['inactive_jobs'] }}</h4>
                                <p class="text-muted mb-0 small">وظائف غير نشطة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوظائف الحديثة -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-briefcase me-2 text-primary"></i>أحدث الوظائف
                        </h5>
                        <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recent_jobs as $job)
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                 style="width: 45px; height: 45px;">
                                <i class="fas fa-briefcase text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $job->title }}</h6>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-users me-1"></i>{{ $job->applications_count }} طلب
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $job->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $job->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                                <p class="text-muted small mb-0 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد وظائف منشورة بعد</p>
                            <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>إضافة وظيفة جديدة
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- الجانب الأيمن -->
        <div class="col-lg-4">
            <!-- الطلبات الجديدة -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-inbox me-2 text-warning"></i>طلبات جديدة
                        </h5>
                        <a href="{{ route('company.applications.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-list me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recent_applications as $application)
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">{{ $application->user->name }}</h6>
                                <p class="text-muted small mb-0">{{ Str::limit($application->job->title, 30) }}</p>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $application->status_color }}">{{ $application->status_text }}</span>
                                <p class="text-muted small mb-0 mt-1">{{ $application->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">لا توجد طلبات جديدة</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- إجراءات سريعة -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-success"></i>إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>إضافة وظيفة جديدة
                        </a>
                        <a href="{{ route('company.applications.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-list me-2"></i>مراجعة الطلبات
                        </a>
                        <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-briefcase me-2"></i>إدارة الوظائف
                        </a>
                        <a href="{{ route('company.profile') }}" class="btn btn-outline-info">
                            <i class="fas fa-building me-2"></i>الملف الشخصي
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نصائح وتذكيرات -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="fas fa-lightbulb me-2"></i>نصائح لجذب أفضل المواهب
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    اكتب وصفاً واضحاً ومفصلاً للوظيفة
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    حدد المتطلبات والمهارات المطلوبة بدقة
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    اذكر المزايا والحوافز المقدمة
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    راجع الطلبات وقم بالرد بسرعة
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    حدث معلومات شركتك بانتظام
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    تفاعل مع المتقدمين بإيجابية
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 