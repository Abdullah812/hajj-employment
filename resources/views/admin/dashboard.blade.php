@extends('layouts.app')

@section('title', 'لوحة تحكم المدير - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- الترحيب -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">مرحباً {{ auth()->user()->name }}</h2>
                            <p class="card-text mb-0">لوحة التحكم الرئيسية لإدارة نظام التوظيف الموسمي - شركة مناسك المشاعر</p>
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
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-briefcase fa-2x text-success"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-success mb-1">{{ $stats['total_jobs'] }}</h3>
                            <p class="text-muted mb-0 small">إجمالي الوظائف</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-paper-plane fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-warning mb-1">{{ $stats['total_applications'] }}</h3>
                            <p class="text-muted mb-0 small">إجمالي الطلبات</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-building fa-2x text-info"></i>
                        </div>
                        <div>
                            <h3 class="h4 text-info mb-1">{{ $stats['total_departments'] }}</h3>
                            <p class="text-muted mb-0 small">الأقسام المسجلة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات تفصيلية -->
    <div class="row g-4 mb-5">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>إحصائيات تفصيلية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- إحصائيات المستخدمين -->
                        <div class="col-md-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">المستخدمون</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">الموظفون</span>
                                <strong class="text-success">{{ $stats['employees'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small">الأقسام</span>
                                <strong class="text-info">{{ $stats['departments'] }}</strong>
                            </div>
                        </div>

                        <!-- إحصائيات الوظائف -->
                        <div class="col-md-4">
                            <h6 class="text-success border-bottom pb-2 mb-3">الوظائف</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small">وظائف نشطة</span>
                                <strong class="text-success">{{ $stats['active_jobs'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small">وظائف مغلقة</span>
                                <strong class="text-secondary">{{ $stats['inactive_jobs'] }}</strong>
                            </div>
                        </div>

                        <!-- إحصائيات الطلبات -->
                        <div class="col-md-4">
                            <h6 class="text-warning border-bottom pb-2 mb-3">الطلبات</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">قيد المراجعة</span>
                                <strong class="text-warning">{{ $stats['pending_applications'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">مقبولة</span>
                                <a href="{{ route('applications.approved') }}" class="text-decoration-none dashboard-link">
                                <strong class="text-success">{{ $stats['approved_applications'] }}</strong>
                                    <small class="text-success"><i class="fas fa-external-link-alt ms-1"></i></small>
                                </a>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small">مرفوضة</span>
                                <strong class="text-danger">{{ $stats['rejected_applications'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <!-- النشاط الحديث -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-activity me-2 text-success"></i>النشاط الحديث
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="fas fa-user-plus text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">{{ $stats['today_registrations'] }} مستخدم جديد اليوم</p>
                                    <small class="text-muted">{{ now()->format('Y/m/d') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="fas fa-briefcase text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">{{ $stats['today_jobs'] }} وظيفة جديدة اليوم</p>
                                    <small class="text-muted">{{ now()->format('Y/m/d') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                    <i class="fas fa-paper-plane text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">{{ $stats['today_applications'] }} طلب جديد اليوم</p>
                                    <small class="text-muted">{{ now()->format('Y/m/d') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مؤشرات الأداء الرئيسية (KPIs) -->
    <div class="row g-4 mb-5 kpi-section">
        <div class="col-12">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-success"></i>مؤشرات الأداء الرئيسية (KPIs)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- معدل نجاح التوظيف -->
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center">
                                <div class="position-relative d-inline-block mb-3">
                                    <div class="progress-circle" data-percentage="{{ $kpis['success_rate'] }}">
                                        <svg class="progress-ring" width="80" height="80">
                                            <circle class="progress-ring-circle" cx="40" cy="40" r="32" 
                                                    style="stroke-dasharray: 201; stroke-dashoffset: {{ 201 - (201 * $kpis['success_rate'] / 100) }}"></circle>
                                        </svg>
                                        <div class="progress-text">
                                            <span class="h5 fw-bold text-success">{{ $kpis['success_rate'] }}%</span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="text-muted mb-1">معدل نجاح التوظيف</h6>
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $kpis['success_rate'] >= 50 ? 'أداء ممتاز' : 'يحتاج تحسين' }}
                                </small>
                            </div>
                        </div>

                        <!-- نمو المستخدمين -->
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 80px; height: 80px;">
                                    <h4 class="text-primary mb-0">
                                        @if($kpis['user_growth'] >= 0)
                                            <i class="fas fa-arrow-up me-1"></i>{{ $kpis['user_growth'] }}%
                                        @else
                                            <i class="fas fa-arrow-down me-1"></i>{{ abs($kpis['user_growth']) }}%
                                        @endif
                                    </h4>
                                </div>
                                <h6 class="text-muted mb-1">نمو المستخدمين الشهري</h6>
                                <small class="text-{{ $kpis['user_growth'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $kpis['current_month_users'] }} هذا الشهر مقابل {{ $kpis['last_month_users'] }} الشهر السابق
                                </small>
                            </div>
                        </div>

                        <!-- متوسط الطلبات لكل وظيفة -->
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 80px; height: 80px;">
                                    <h4 class="text-warning mb-0">{{ $kpis['avg_applications_per_job'] }}</h4>
                                </div>
                                <h6 class="text-muted mb-1">متوسط الطلبات لكل وظيفة</h6>
                                <small class="text-info">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    {{ $kpis['avg_applications_per_job'] >= 5 ? 'جاذبية عالية' : 'جاذبية متوسطة' }}
                                </small>
                            </div>
                        </div>

                        <!-- معدل نشاط الأقسام -->
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                     style="width: 80px; height: 80px;">
                                    <h4 class="text-info mb-0">{{ $kpis['department_activity_rate'] }}%</h4>
                                </div>
                                <h6 class="text-muted mb-1">معدل نشاط الأقسام</h6>
                                <small class="text-{{ $kpis['department_activity_rate'] >= 70 ? 'success' : 'warning' }}">
                                    {{ $kpis['department_activity_rate'] >= 70 ? 'نشاط عالي' : 'يحتاج تشجيع' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- أكثر الأقسام نشاطاً -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2 mb-3">أكثر الأقسام نشاطاً</h6>
                            @forelse($kpis['top_departments'] as $index => $department)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-{{ ['primary', 'success', 'warning'][$index] }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 30px; height: 30px;">
                                        <span class="small fw-bold text-{{ ['primary', 'success', 'warning'][$index] }}">{{ $index + 1 }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-bold">{{ $department->name }}</div>
                                        <div class="text-muted small">{{ $department->jobs_count }} وظيفة</div>
                                    </div>
                                    <div class="badge bg-{{ ['primary', 'success', 'warning'][$index] }}">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">لا توجد أقسام نشطة</p>
                            @endforelse
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-line me-2"></i>اتجاه الأداء (آخر 6 أشهر)
                            </h6>
                            <div class="chart-container" style="position: relative; height: 220px; width: 100%;">
                                <canvas id="performanceChart"></canvas>
                                <div id="noDataMessage" style="display: none; text-align: center; padding: 50px 0; color: #6c757d;">
                                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                                    <p>لا توجد بيانات كافية للرسم البياني</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- أحدث الوظائف -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-briefcase me-2 text-primary"></i>أحدث الوظائف
                        </h5>
                        <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recent_jobs as $job)
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3 last:mb-0 last:border-bottom-0">
                            <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                 style="width: 45px; height: 45px;">
                                <i class="fas fa-briefcase text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $job->title }}</h6>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-building me-1"></i>{{ optional($job->department)->name ?? 'قسم غير معروف' }}
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
                        <div class="text-center py-3">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد وظائف</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- أحدث الطلبات -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-paper-plane me-2 text-warning"></i>أحدث الطلبات
                        </h5>
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-eye me-1"></i>عرض الكل
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($recent_applications as $application)
                        <div class="d-flex align-items-center border-bottom pb-3 mb-3 last:mb-0 last:border-bottom-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">{{ $application->user->name }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ Str::limit($application->job->title, 25) }}
                                    <span class="mx-2">•</span>
                                    {{ optional($application->job->department)->name ?? 'قسم غير معروف' }}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $application->status_color }}">{{ $application->status_text }}</span>
                                <p class="text-muted small mb-0 mt-1">{{ $application->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد طلبات</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- الأدوات الإدارية -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2 text-info"></i>الأدوات الإدارية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.users.index') }}" class="card border-0 bg-primary bg-opacity-10 text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h6 class="text-primary mb-0">إدارة المستخدمين</h6>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.departments.index') }}" class="card border-0 bg-success bg-opacity-10 text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-2x text-success mb-2"></i>
                                    <h6 class="text-success mb-0">إدارة الأقسام</h6>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.employees.index') }}" class="card border-0 bg-warning bg-opacity-10 text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-tie fa-2x text-warning mb-2"></i>
                                    <h6 class="text-warning mb-0">إدارة الموظفين</h6>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.jobs.index') }}" class="card border-0 bg-info bg-opacity-10 text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-briefcase fa-2x text-info mb-2"></i>
                                    <h6 class="text-info mb-0">إدارة الوظائف</h6>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.applications.index') }}" class="card border-0 bg-secondary bg-opacity-10 text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-paper-plane fa-2x text-secondary mb-2"></i>
                                    <h6 class="text-secondary mb-0">إدارة الطلبات</h6>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('admin.users.create') }}" class="card border-0 bg-danger bg-opacity-10 text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-plus fa-2x text-danger mb-2"></i>
                                    <h6 class="text-danger mb-0">إضافة مستخدم</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ملاحظات وتنبيهات -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">
                        <i class="fas fa-bell me-2"></i>تنبيهات النظام
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            @if($stats['pending_applications'] > 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    يوجد {{ $stats['pending_applications'] }} طلب في انتظار المراجعة
                                </div>
                            @endif

                            @if($stats['today_registrations'] > 0)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    انضم {{ $stats['today_registrations'] }} مستخدم جديد اليوم
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($stats['active_jobs'] == 0)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    لا توجد وظائف نشطة حالياً
                                </div>
                            @endif

                            @if($stats['today_jobs'] > 0)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    تم إضافة {{ $stats['today_jobs'] }} وظيفة جديدة اليوم
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- روابط سريعة -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إدارة الوظائف</h5>
                    <p class="card-text">إدارة الوظائف المتاحة والطلبات</p>
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-primary">عرض الوظائف</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إدارة الأقسام</h5>
                    <p class="card-text">إدارة الأقسام والموظفين</p>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-primary">عرض الأقسام</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">التقارير والإحصائيات</h5>
                    <p class="card-text">عرض وتصدير التقارير المتقدمة</p>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-success">عرض التقارير</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    height: calc(100% + 1rem);
    border-left: 2px dashed #e9ecef;
}

.progress-circle {
    position: relative;
    width: 80px;
    height: 80px;
}

.progress-ring-circle {
    fill: none;
    stroke: #28a745;
    stroke-width: 4;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.kpi-card {
    transition: all 0.3s ease;
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إعداد الرسم البياني
    const ctx = document.getElementById('performanceChart');
    if (!ctx) return;
    
    const monthlyData = @json($kpis['monthly_stats']);
    
    // التحقق من وجود بيانات
    if (!monthlyData || monthlyData.length === 0) {
        ctx.style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'block';
        return;
    }
    
    // حفظ موقع الـ scroll الحالي
    const currentScrollY = window.scrollY;
    
    const chart = new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'المستخدمين',
                    data: monthlyData.map(item => item.users),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'الوظائف',
                    data: monthlyData.map(item => item.jobs),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'الطلبات',
                    data: monthlyData.map(item => item.applications),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        maxTicksLimit: 6
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            elements: {
                point: {
                    radius: 3,
                    hoverRadius: 5
                },
                line: {
                    borderWidth: 2
                }
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            }
        }
    });
    
    // إعادة الـ scroll لمكانه الأصلي
    setTimeout(() => {
        window.scrollTo(0, currentScrollY);
    }, 100);
});
</script>
@endsection 

<style>
.dashboard-link {
    padding: 2px 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.dashboard-link:hover {
    background-color: rgba(25, 135, 84, 0.1);
    transform: translateX(-2px);
}

.dashboard-link:hover .text-success {
    color: #198754 !important;
}
</style> 