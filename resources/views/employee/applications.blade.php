@extends('layouts.app')

@section('title', 'طلباتي - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary">طلباتي</h1>
                    <p class="text-muted mb-0">تابع حالة جميع طلباتك المقدمة للوظائف</p>
                </div>
                <div>
                    <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>العودة للوحة التحكم
                    </a>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>تصفح الوظائف
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-paper-plane text-primary"></i>
                    </div>
                    <h4 class="text-primary">{{ $stats['total'] }}</h4>
                    <p class="text-muted mb-0 small">إجمالي الطلبات</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <h4 class="text-warning">{{ $stats['pending'] }}</h4>
                    <p class="text-muted mb-0 small">قيد المراجعة</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <h4 class="text-success">{{ $stats['approved'] }}</h4>
                    <p class="text-muted mb-0 small">مقبولة</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <h4 class="text-danger">{{ $stats['rejected'] }}</h4>
                    <p class="text-muted mb-0 small">مرفوضة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الطلبات -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>جميع طلباتي
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($applications as $application)
                <div class="application-item border-bottom p-4">
                    <div class="row align-items-center">
                        <!-- معلومات الوظيفة -->
                        <div class="col-lg-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-briefcase text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('jobs.show', $application->job) }}" class="text-decoration-none">
                                            {{ $application->job->title }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-building me-1"></i>{{ $application->job->company->name }}
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $application->job->location }}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-money-bill-wave me-1"></i>{{ $application->job->salary_range }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- معلومات التقديم -->
                        <div class="col-lg-3">
                            <div class="text-center text-lg-start">
                                <span class="badge {{ $application->status_color }} mb-2">
                                    {{ $application->status_text }}
                                </span>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-calendar me-1"></i>
                                    تم التقديم: {{ $application->applied_at ? $application->applied_at->format('Y/m/d') : $application->created_at->format('Y/m/d') }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $application->applied_at ? $application->applied_at->diffForHumans() : $application->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- الإجراءات -->
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('jobs.show', $application->job) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> عرض الوظيفة
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- رسالة التقديم (إذا وجدت) -->
                    @if($application->cover_letter)
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <strong>رسالة التقديم:</strong> {{ Str::limit($application->cover_letter, 150) }}
                            </small>
                        </div>
                    @endif
                    
                    <!-- ملاحظات من الشركة (إذا وجدت) -->
                    @if($application->notes)
                        <div class="mt-3 pt-3 border-top">
                            <div class="alert alert-info mb-0">
                                <strong><i class="fas fa-comment me-1"></i>ملاحظات من الشركة:</strong>
                                <p class="mb-0 mt-1">{{ $application->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-paper-plane fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">لا توجد طلبات مقدمة</h4>
                    <p class="text-muted mb-4">لم تقدم على أي وظيفة بعد. ابدأ في استكشاف الفرص المتاحة</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>تصفح الوظائف المتاحة
                    </a>
                </div>
            @endforelse
        </div>
        
        @if($applications->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- نصائح -->
    @if($applications->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <i class="fas fa-lightbulb me-2"></i>نصائح لتحسين فرص القبول
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        تأكد من اكتمال ملفك الشخصي
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        ارفع سيرة ذاتية محدثة
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        اكتب رسالة تقديم مميزة
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        تابع حالة طلباتك بانتظام
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        قدم على وظائف تناسب مهاراتك
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-check text-success me-2"></i>
                                        كن صبوراً في انتظار الرد
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 