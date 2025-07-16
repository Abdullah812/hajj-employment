@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <!-- Header محسن مع أدوات متقدمة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        لوحة التقارير المتقدمة
                    </h2>
                    <p class="text-muted mb-0">تقارير شاملة مع فلاتر ذكية ورسوم بيانية تفاعلية</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" id="refreshReports">
                        <i class="fas fa-sync-alt me-1"></i>تحديث
                    </button>
                    <div class="btn-group" role="group">
                        <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>تصدير شامل
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="exportAllExcel">
                                <i class="fas fa-file-excel me-2"></i>Excel شامل
                            </a></li>
                            <li><a class="dropdown-item" href="#" id="exportAllPDF">
                                <i class="fas fa-file-pdf me-2"></i>PDF مفصل
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="scheduleReport">
                                <i class="fas fa-calendar-alt me-2"></i>جدولة تقرير دوري
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- فلاتر متقدمة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        فلاتر التقارير المتقدمة
                        <button class="btn btn-sm btn-outline-secondary float-end" id="toggleFilters">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                    </h5>
                </div>
                <div class="card-body" id="filtersPanel">
                    <form id="reportsFilter">
                        <div class="row g-3">
                            <!-- فلتر التاريخ -->
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1"></i>من تاريخ
                                </label>
                                <input type="date" class="form-control" id="dateFrom" name="date_from">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1"></i>إلى تاريخ
                                </label>
                                <input type="date" class="form-control" id="dateTo" name="date_to">
                            </div>
                            
                            <!-- فلتر القسم -->
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-building me-1"></i>القسم
                                </label>
                                <select class="form-select" id="departmentFilter" name="department">
                                    <option value="">جميع الأقسام</option>
                                    <!-- سيتم ملؤها ديناميكياً -->
                                </select>
                            </div>
                            
                            <!-- فلتر الحالة -->
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>الحالة
                                </label>
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">جميع الحالات</option>
                                    <option value="active">نشط</option>
                                    <option value="inactive">غير نشط</option>
                                    <option value="pending">قيد المراجعة</option>
                                    <option value="approved">مقبول</option>
                                    <option value="rejected">مرفوض</option>
                                </select>
                            </div>
                            
                            <!-- فلتر متقدم إضافي -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-briefcase me-1"></i>نوع التوظيف
                                </label>
                                <select class="form-select" id="employmentType" name="employment_type">
                                    <option value="">جميع الأنواع</option>
                                    <option value="full_time">دوام كامل</option>
                                    <option value="part_time">دوام جزئي</option>
                                    <option value="temporary">مؤقت</option>
                                    <option value="seasonal">موسمي</option>
                                </select>
                            </div>
                            
                            <!-- فلتر الراتب -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    <i class="fas fa-money-bill me-1"></i>نطاق الراتب
                                </label>
                                <select class="form-select" id="salaryRange" name="salary_range">
                                    <option value="">جميع النطاقات</option>
                                    <option value="0-3000">أقل من 3000 ريال</option>
                                    <option value="3000-5000">3000 - 5000 ريال</option>
                                    <option value="5000-8000">5000 - 8000 ريال</option>
                                    <option value="8000-12000">8000 - 12000 ريال</option>
                                    <option value="12000+">أكثر من 12000 ريال</option>
                                </select>
                            </div>
                            
                            <!-- أزرار التحكم -->
                            <div class="col-md-4">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>تطبيق الفلاتر
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                                        <i class="fas fa-undo me-1"></i>إعادة تعيين
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة محسنة -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">إجمالي الوظائف</h6>
                            <h3 class="mb-0 text-primary" id="totalJobsCount">{{ $totalJobs }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <span id="jobsGrowth">+12%</span> من الشهر الماضي
                            </small>
                        </div>
                        <div class="avatar-lg bg-primary bg-opacity-10 rounded-3">
                            <i class="fas fa-briefcase fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">إجمالي الطلبات</h6>
                            <h3 class="mb-0 text-info" id="totalApplicationsCount">{{ $totalApplications }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <span id="applicationsGrowth">+8%</span> من الشهر الماضي
                            </small>
                        </div>
                        <div class="avatar-lg bg-info bg-opacity-10 rounded-3">
                            <i class="fas fa-file-alt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">قيد المراجعة</h6>
                            <h3 class="mb-0 text-warning" id="pendingApplicationsCount">{{ $pendingApplications }}</h3>
                            <small class="text-warning">
                                <i class="fas fa-clock me-1"></i>
                                يحتاج مراجعة عاجلة
                            </small>
                        </div>
                        <div class="avatar-lg bg-warning bg-opacity-10 rounded-3">
                            <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">طلبات مقبولة</h6>
                            <h3 class="mb-0 text-success" id="approvedApplicationsCount">{{ $approvedApplications }}</h3>
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>
                                <span id="approvalRate">{{ $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0 }}%</span> معدل القبول
                            </small>
                        </div>
                        <div class="avatar-lg bg-success bg-opacity-10 rounded-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الرسوم البيانية التفاعلية -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-area me-2 text-primary"></i>
                            اتجاهات التوظيف الشهرية
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active" data-period="6">6 أشهر</button>
                            <button class="btn btn-outline-primary" data-period="12">سنة</button>
                            <button class="btn btn-outline-primary" data-period="24">سنتان</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-success"></i>
                        توزيع حالات الطلبات
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">مقبول</span>
                            <strong>{{ $approvedApplications }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-warning">قيد المراجعة</span>
                            <strong>{{ $pendingApplications }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-danger">مرفوض</span>
                            <strong>{{ $totalApplications - $approvedApplications - $pendingApplications }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تقارير مفصلة محسنة -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>
                            تقارير الوظائف المتقدمة
                        </h5>
                        <span class="badge bg-light text-dark">{{ $totalJobs }} وظيفة</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text mb-3">تقارير شاملة ومفصلة عن الوظائف مع إمكانيات تصدير وتحليل متقدمة</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted">وظائف نشطة</small>
                                <div class="fw-bold text-success" id="activeJobsCount">-</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted">متوسط الطلبات</small>
                                <div class="fw-bold text-info" id="avgApplicationsPerJob">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group w-100">
                        <a href="{{ route('admin.reports.jobs') }}" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>عرض التقرير
                        </a>
                        <button class="btn btn-success" id="exportJobsAdvanced">
                            <i class="fas fa-file-excel me-1"></i>تصدير متقدم
                        </button>
                        <button class="btn btn-info" id="analyzeJobs">
                            <i class="fas fa-chart-bar me-1"></i>تحليل ذكي
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            تقارير الطلبات المتقدمة
                        </h5>
                        <span class="badge bg-light text-dark">{{ $totalApplications }} طلب</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text mb-3">تحليل شامل لطلبات التوظيف مع إحصائيات الأداء ومعدلات القبول</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted">معدل القبول</small>
                                <div class="fw-bold text-success">{{ $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0 }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted">متوسط وقت المراجعة</small>
                                <div class="fw-bold text-warning" id="avgReviewTime">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group w-100">
                        <a href="{{ route('admin.reports.applications') }}" class="btn btn-success">
                            <i class="fas fa-eye me-1"></i>عرض التقرير
                        </a>
                        <button class="btn btn-primary" id="exportApplicationsAdvanced">
                            <i class="fas fa-file-excel me-1"></i>تصدير متقدم
                        </button>
                        <button class="btn btn-warning" id="analyzeApplications">
                            <i class="fas fa-chart-line me-1"></i>تحليل الأداء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal للتحليل المتقدم -->
<div class="modal fade" id="analysisModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>
                    التحليل المتقدم
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="analysisContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحليل...</span>
                    </div>
                    <p class="mt-3">جاري تحليل البيانات وإنشاء الرؤى الذكية...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تهيئة الرسوم البيانية
    initCharts();
    
    // تهيئة الفلاتر
    initFilters();
    
    // تحميل البيانات الإضافية
    loadAdditionalStats();
    
    // معالجات الأحداث
    setupEventHandlers();
});

// تهيئة الرسوم البيانية
function initCharts() {
    // رسم الاتجاهات الشهرية
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    window.trendsChart = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'الوظائف المنشورة',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'الطلبات المستلمة',
                data: [65, 89, 75, 125, 95, 140],
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // رسم توزيع الحالات
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    window.statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['مقبول', 'قيد المراجعة', 'مرفوض'],
            datasets: [{
                data: [{{ $approvedApplications }}, {{ $pendingApplications }}, {{ $totalApplications - $approvedApplications - $pendingApplications }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// تهيئة الفلاتر
function initFilters() {
    // تحميل الأقسام
    fetch('/admin/api/departments')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('departmentFilter');
            data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                select.appendChild(option);
            });
        });
        
    // تطبيق الفلاتر
    document.getElementById('reportsFilter').addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
    
    // إعادة تعيين الفلاتر
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('reportsFilter').reset();
        applyFilters();
    });
}

// تطبيق الفلاتر
function applyFilters() {
    const formData = new FormData(document.getElementById('reportsFilter'));
    const params = new URLSearchParams(formData);
    
    // تحديث الرسوم البيانية
    fetch(`/admin/reports/api/filtered-data?${params}`)
        .then(response => response.json())
        .then(data => {
            updateCharts(data);
            updateStats(data);
        });
}

// تحديث الرسوم البيانية
function updateCharts(data) {
    if (window.trendsChart) {
        window.trendsChart.data.datasets[0].data = data.monthly_jobs;
        window.trendsChart.data.datasets[1].data = data.monthly_applications;
        window.trendsChart.update();
    }
    
    if (window.statusChart) {
        window.statusChart.data.datasets[0].data = [
            data.approved_applications,
            data.pending_applications,
            data.rejected_applications
        ];
        window.statusChart.update();
    }
}

// تحديث الإحصائيات
function updateStats(data) {
    document.getElementById('totalJobsCount').textContent = data.total_jobs;
    document.getElementById('totalApplicationsCount').textContent = data.total_applications;
    document.getElementById('pendingApplicationsCount').textContent = data.pending_applications;
    document.getElementById('approvedApplicationsCount').textContent = data.approved_applications;
}

// تحميل إحصائيات إضافية
function loadAdditionalStats() {
    fetch('/admin/reports/api/additional-stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('activeJobsCount').textContent = data.active_jobs;
            document.getElementById('avgApplicationsPerJob').textContent = data.avg_applications_per_job;
            document.getElementById('avgReviewTime').textContent = data.avg_review_time + ' يوم';
        });
}

// معالجات الأحداث
function setupEventHandlers() {
    // تبديل عرض الفلاتر
    document.getElementById('toggleFilters').addEventListener('click', function() {
        const panel = document.getElementById('filtersPanel');
        const icon = this.querySelector('i');
        
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            icon.className = 'fas fa-chevron-up';
        } else {
            panel.style.display = 'none';
            icon.className = 'fas fa-chevron-down';
        }
    });
    
    // تحديث البيانات
    document.getElementById('refreshReports').addEventListener('click', function() {
        location.reload();
    });
    
    // التحليل المتقدم
    document.getElementById('analyzeJobs').addEventListener('click', function() {
        showAnalysis('jobs');
    });
    
    document.getElementById('analyzeApplications').addEventListener('click', function() {
        showAnalysis('applications');
    });
}

// عرض التحليل المتقدم
function showAnalysis(type) {
    const modal = new bootstrap.Modal(document.getElementById('analysisModal'));
    modal.show();
    
    fetch(`/admin/reports/api/analysis/${type}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('analysisContent').innerHTML = generateAnalysisHTML(data);
        });
}

// إنشاء HTML للتحليل
function generateAnalysisHTML(data) {
    return `
        <div class="row g-4">
            <div class="col-md-6">
                <h6>الاتجاهات الرئيسية</h6>
                <ul class="list-group list-group-flush">
                    ${data.trends.map(trend => `<li class="list-group-item">${trend}</li>`).join('')}
                </ul>
            </div>
            <div class="col-md-6">
                <h6>التوصيات</h6>
                <ul class="list-group list-group-flush">
                    ${data.recommendations.map(rec => `<li class="list-group-item">${rec}</li>`).join('')}
                </ul>
            </div>
        </div>
    `;
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
}

.avatar-lg {
    width: 4rem;
    height: 4rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: white;
}
</style>
@endsection 