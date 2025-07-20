@extends('layouts.app')

@section('title', 'لوحة تحكم الموظف - شركة مناسك المشاعر')

@section('content')
<div class="container py-4">
    @if(Auth::user()->approval_status === 'pending')
    <div class="alert alert-warning" role="alert">
        <i class="fas fa-clock me-2"></i>
        حسابك قيد المراجعة من قبل الإدارة. سيتم إخطارك عند الموافقة على حسابك.
    </div>
    @elseif(Auth::user()->approval_status === 'rejected')
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-times-circle me-2"></i>
        عذراً، تم رفض طلب تسجيلك. يرجى التواصل مع الإدارة لمزيد من المعلومات.
    </div>
    @endif

    <!-- الترحيب -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">مرحباً {{ auth()->user()->name }}</h2>
                            <p class="card-text mb-0">استكشف الفرص الوظيفية وتابع طلباتك في شركة مناسك المشاعر</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <i class="fas fa-user-tie fa-4x opacity-75"></i>
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
                    <p class="text-muted mb-0">الوظائف المتاحة</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-paper-plane fa-2x text-success"></i>
                    </div>
                    <h3 class="h4 text-success">{{ $stats['my_applications'] }}</h3>
                    <p class="text-muted mb-0">طلباتي المقدمة</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h3 class="h4 text-warning">{{ $stats['pending_applications'] }}</h3>
                    <p class="text-muted mb-0">طلبات قيد المراجعة</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle fa-2x text-info"></i>
                    </div>
                    <h3 class="h4 text-info">{{ $stats['approved_applications'] }}</h3>
                    <p class="text-muted mb-0">طلبات مقبولة</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- الوظائف الحديثة -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">أحدث الوظائف المتاحة</h5>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recent_jobs as $job)
                        <div class="d-flex align-items-start border-bottom pb-3 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                 style="width: 50px; height: 50px;">
                                <i class="fas fa-briefcase text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                        {{ $job->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-2">{{ $job->department->name }} • {{ $job->location }}</p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2">{{ $job->employment_type_text }}</span>
                                    <span class="text-success fw-bold">{{ $job->salary_range }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                @php
                                    $hasApplied = $job->applications()->where('user_id', auth()->id())->exists();
                                @endphp
                                @if($hasApplied)
                                    <div class="mt-1">
                                        <span class="badge bg-success">تم التقديم</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد وظائف متاحة حالياً</p>
                            <a href="{{ route('jobs.index') }}" class="btn btn-primary">تصفح الوظائف</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- طلباتي -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">طلباتي الأخيرة</h5>
                        <a href="{{ route('employee.applications') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($my_applications as $application)
                        <div class="d-flex align-items-start border-bottom pb-3 mb-3">
                            <div class="me-3 flex-shrink-0">
                                <span class="badge {{ $application->status_color }}">{{ $application->status_text }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('jobs.show', $application->job) }}" class="text-decoration-none">
                                        {{ $application->job->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-0">{{ $application->job->department->name }}</p>
                                <small class="text-muted">{{ $application->applied_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-paper-plane fa-2x text-muted mb-3"></i>
                            <p class="text-muted">لم تقدم على أي وظيفة بعد</p>
                            <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-sm">استكشف الوظائف</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- الملف الشخصي -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">الملف الشخصي</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->profile)
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>الملف الشخصي مكتمل</span>
                        </div>
                        
                                                        @if(auth()->user()->profile->cv_file_data)
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span>السيرة الذاتية مرفوعة</span>
                            </div>
                        @else
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                <span>السيرة الذاتية غير مرفوعة</span>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            يرجى إكمال ملفك الشخصي لزيادة فرص القبول
                        </div>
                    @endif
                    
                    <a href="{{ route('employee.profile') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-edit me-2"></i>تحديث الملف الشخصي
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">روابط سريعة</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-search me-2"></i>البحث عن وظائف
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('employee.applications') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-list me-2"></i>طلباتي
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('employee.profile') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-user-edit me-2"></i>الملف الشخصي
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-home me-2"></i>الصفحة الرئيسية
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 