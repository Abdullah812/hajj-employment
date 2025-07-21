@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-secondary mb-1">
                        <i class="fas fa-chart-bar me-2"></i>لوحة الإحصائيات المتقدمة
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">الإحصائيات المتقدمة</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-1"></i>تحديث البيانات
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>تصدير
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportData('overview', 'pdf')">
                                <i class="fas fa-file-pdf me-2"></i>تقرير شامل PDF
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportData('users', 'excel')">
                                <i class="fas fa-file-excel me-2"></i>تقرير المستخدمين Excel
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportData('jobs', 'excel')">
                                <i class="fas fa-briefcase me-2"></i>تقرير الوظائف Excel
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Alerts -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <h6 class="alert-heading mb-2">
                    <i class="fas fa-bell me-2"></i>تنبيهات الأداء
                </h6>
                @foreach($alerts as $alert)
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-{{ $alert['icon'] }} text-{{ $alert['type'] }} me-2"></i>
                        <span>{{ $alert['message'] }}</span>
                    </div>
                    <small class="text-muted">{{ $alert['action'] }}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Key Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h6 class="mb-0 opacity-75">إجمالي المستخدمين</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h2>
                            <small class="opacity-75">
                                <i class="fas fa-plus me-1"></i>
                                {{ $stats['new_users_today'] }} جديد اليوم
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-briefcase fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h6 class="mb-0 opacity-75">الوظائف النشطة</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['active_jobs']) }}</h2>
                            <small class="opacity-75">
                                من أصل {{ $stats['total_jobs'] }} وظيفة
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-gradient bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt fa-2x opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 text-end">
                            <h6 class="mb-0 opacity-75">طلبات التوظيف</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_applications']) }}</h2>
                            <small class="opacity-75">
                                <i class="fas fa-plus me-1"></i>
                                {{ $stats['new_applications_today'] }} جديد اليوم
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- العقود الموقعة - تم حذف النظام -->
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Registrations Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>التسجيلات الشهرية
                    </h5>
                    <p class="text-muted small mb-0">عدد المستخدمين الجدد خلال العام الحالي</p>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRegistrationsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Applications Status Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-success me-2"></i>حالة الطلبات
                    </h5>
                    <p class="text-muted small mb-0">توزيع طلبات التوظيف حسب الحالة</p>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="applicationsStatusChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row mb-4">
        <!-- Jobs by Department -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building text-warning me-2"></i>الوظائف حسب القسم
                    </h5>
                    <p class="text-muted small mb-0">أكثر الأقسام نشاطاً في التوظيف</p>
                </div>
                <div class="card-body">
                    <canvas id="jobsByDepartmentChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Salary Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-coins text-info me-2"></i>توزيع الرواتب
                    </h5>
                    <p class="text-muted small mb-0">توزيع الوظائف حسب فئات الراتب</p>
                </div>
                <div class="card-body">
                    <canvas id="salaryDistributionChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline and Recent Activities -->
    <div class="row mb-4">
        <!-- Daily Activities Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area text-secondary me-2"></i>النشاط اليومي
                    </h5>
                    <p class="text-muted small mb-0">تسجيلات، طلبات، ووظائف جديدة خلال آخر 30 يوم</p>
                </div>
                <div class="card-body">
                    <canvas id="dailyActivitiesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>الأنشطة الأخيرة
                    </h5>
                    <p class="text-muted small mb-0">آخر الفعاليات في النظام</p>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @foreach($recentActivities->take(10) as $activity)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-{{ $activity['color'] }} bg-opacity-10 text-{{ $activity['color'] }} rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="fas fa-{{ $activity['icon'] }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1 small">{{ $activity['message'] }}</p>
                                    <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table text-dark me-2"></i>ملخص سريع
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ number_format($stats['pending_applications']) }}</h4>
                                <small class="text-muted">طلبات معلقة</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-success mb-1">{{ number_format($stats['approved_applications']) }}</h4>
                                <small class="text-muted">طلبات مقبولة</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-info mb-1">{{ number_format($stats['total_departments']) }}</h4>
                                <small class="text-muted">أقسام نشطة</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-warning mb-1">{{ number_format($stats['total_news']) }}</h4>
                                <small class="text-muted">أخبار منشورة</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-secondary mb-1">
                                    {{ $stats['total_applications'] > 0 ? round(($stats['approved_applications'] / $stats['total_applications']) * 100) : 0 }}%
                                </h4>
                                <small class="text-muted">معدل القبول</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <h4 class="text-dark mb-1">
                                {{ $stats['total_jobs'] > 0 ? round(($stats['active_jobs'] / $stats['total_jobs']) * 100) : 0 }}%
                            </h4>
                            <small class="text-muted">الوظائف النشطة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// بيانات الرسوم البيانية
const chartsData = @json($charts);

// إعدادات الألوان
const colors = {
    primary: '#0d6efd',
    success: '#198754',
    warning: '#ffc107',
    info: '#0dcaf0',
    danger: '#dc3545',
    secondary: '#6c757d'
};

// رسم بياني للتسجيلات الشهرية
const monthlyRegistrationsCtx = document.getElementById('monthlyRegistrationsChart').getContext('2d');
new Chart(monthlyRegistrationsCtx, {
    type: 'line',
    data: {
        labels: chartsData.monthly_registrations.map(item => item.month),
        datasets: [{
            label: 'عدد المستخدمين الجدد',
            data: chartsData.monthly_registrations.map(item => item.count),
            borderColor: colors.primary,
            backgroundColor: colors.primary + '20',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f8f9fa'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// رسم دائري لحالة الطلبات
const applicationsStatusCtx = document.getElementById('applicationsStatusChart').getContext('2d');
new Chart(applicationsStatusCtx, {
    type: 'doughnut',
    data: {
        labels: chartsData.applications_by_status.map(item => item.status),
        datasets: [{
            data: chartsData.applications_by_status.map(item => item.count),
            backgroundColor: [colors.warning, colors.success, colors.danger],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// رسم بياني للوظائف حسب القسم
const jobsByDepartmentCtx = document.getElementById('jobsByDepartmentChart').getContext('2d');
new Chart(jobsByDepartmentCtx, {
    type: 'bar',
    data: {
        labels: chartsData.jobs_by_department.map(item => item.name),
        datasets: [{
            label: 'عدد الوظائف',
            data: chartsData.jobs_by_department.map(item => item.count),
            backgroundColor: colors.warning,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f8f9fa'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// رسم بياني لتوزيع الرواتب
const salaryDistributionCtx = document.getElementById('salaryDistributionChart').getContext('2d');
new Chart(salaryDistributionCtx, {
    type: 'bar',
    data: {
        labels: chartsData.salary_distribution.map(item => item.range),
        datasets: [{
            label: 'عدد الوظائف',
            data: chartsData.salary_distribution.map(item => item.count),
            backgroundColor: colors.info,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: '#f8f9fa'
                }
            },
            y: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// رسم بياني للأنشطة اليومية
const dailyActivitiesCtx = document.getElementById('dailyActivitiesChart').getContext('2d');
new Chart(dailyActivitiesCtx, {
    type: 'line',
    data: {
        labels: chartsData.daily_activities.map(item => item.date_ar),
        datasets: [
            {
                label: 'تسجيلات جديدة',
                data: chartsData.daily_activities.map(item => item.registrations),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 2,
                fill: false
            },
            {
                label: 'طلبات توظيف',
                data: chartsData.daily_activities.map(item => item.applications),
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                borderWidth: 2,
                fill: false
            },
            {
                label: 'وظائف جديدة',
                data: chartsData.daily_activities.map(item => item.jobs_posted),
                borderColor: colors.warning,
                backgroundColor: colors.warning + '20',
                borderWidth: 2,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f8f9fa'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// وظائف JavaScript
function refreshData() {
    // إظهار مؤشر التحميل
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري التحديث...';
    btn.disabled = true;

    // تحديث البيانات
    fetch('{{ route("admin.analytics.live-data") }}')
        .then(response => response.json())
        .then(data => {
            // هنا يمكن تحديث البيانات المعروضة
            console.log('تم تحديث البيانات:', data);
            
            // عرض رسالة نجاح
            showNotification('تم تحديث البيانات بنجاح', 'success');
        })
        .catch(error => {
            console.error('خطأ في تحديث البيانات:', error);
            showNotification('حدث خطأ في تحديث البيانات', 'error');
        })
        .finally(() => {
            // إرجاع الزر لحالته الأصلية
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function exportData(type, format) {
    const url = `{{ route('admin.analytics.export') }}?type=${type}&format=${format}`;
    window.open(url, '_blank');
}

function showNotification(message, type) {
    // إظهار إشعار بسيط
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    // إزالة الإشعار بعد 3 ثواني
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 3000);
}

// تحديث البيانات تلقائياً كل 5 دقائق
setInterval(() => {
    fetch('{{ route("admin.analytics.live-data") }}')
        .then(response => response.json())
        .then(data => {
            console.log('تحديث تلقائي للبيانات:', data.timestamp);
        })
        .catch(error => console.error('خطأ في التحديث التلقائي:', error));
}, 300000); // 5 دقائق
</script>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.bg-gradient {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary) 100%);
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .col-md-2 {
        border-bottom: 1px solid #dee2e6 !important;
        border-end: none !important;
    }
    
    .col-md-2:last-child {
        border-bottom: none !important;
    }
}
</style>
@endsection 