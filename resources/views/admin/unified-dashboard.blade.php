@extends('layouts.app')

@section('title', 'لوحة التحكم الموحدة - شركة مناسك المشاعر')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 100px);">
    <!-- الشريط الجانبي -->
    <div class="unified-sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0">
                <i class="fas fa-tachometer-alt me-2"></i>
                لوحة التحكم الموحدة
            </h5>
        </div>
        
        <div class="sidebar-menu">
            <!-- الإحصائيات -->
            <button class="sidebar-item active" data-section="dashboard" type="button">
                <i class="fas fa-chart-pie me-2"></i>
                <span>الإحصائيات الرئيسية</span>
            </button>
            
            <!-- إدارة المستخدمين -->
            <button class="sidebar-item" data-section="users" type="button">
                <i class="fas fa-users me-2"></i>
                <span>إدارة المستخدمين</span>
                <span class="badge bg-primary ms-auto">{{ $stats['total_users'] ?? 0 }}</span>
            </button>
            
            <!-- طلبات الموافقة -->
            <button class="sidebar-item" data-section="approvals" type="button">
                <i class="fas fa-user-check me-2"></i>
                <span>طلبات الموافقة</span>
                @if(($stats['pending_users'] ?? 0) > 0)
                    <span class="badge bg-warning ms-auto">{{ $stats['pending_users'] }}</span>
                @endif
            </button>
            
            <!-- الموظفين المعتمدين -->
            <button class="sidebar-item" data-section="approved" type="button">
                <i class="fas fa-user-shield me-2"></i>
                <span>الموظفين المعتمدين</span>
                <span class="badge bg-success ms-auto">{{ $stats['approved_users'] ?? 0 }}</span>
            </button>
            
            <!-- إدارة الأقسام -->
            <button class="sidebar-item" data-section="departments" type="button">
                <i class="fas fa-building me-2"></i>
                <span>إدارة الأقسام</span>
                <span class="badge bg-info ms-auto">{{ $stats['total_departments'] ?? 0 }}</span>
            </button>
            
            <!-- إدارة الوظائف -->
            <button class="sidebar-item" data-section="jobs" type="button">
                <i class="fas fa-briefcase me-2"></i>
                <span>إدارة الوظائف</span>
                <span class="badge bg-secondary ms-auto">{{ $stats['total_jobs'] ?? 0 }}</span>
            </button>
            
            <!-- طلبات التوظيف -->
            <button class="sidebar-item" data-section="applications" type="button">
                <i class="fas fa-file-alt me-2"></i>
                <span>طلبات التوظيف</span>
                <span class="badge bg-warning ms-auto">{{ $stats['total_applications'] ?? 0 }}</span>
            </button>
            
            <!-- العقود -->
            <button class="sidebar-item" data-section="contracts" type="button">
                <i class="fas fa-file-contract me-2"></i>
                <span>العقود</span>
                <span class="badge bg-info ms-auto">{{ $stats['total_contracts'] ?? 0 }}</span>
            </button>
            
            <!-- التقارير -->
            <button class="sidebar-item" data-section="reports" type="button">
                <i class="fas fa-chart-bar me-2"></i>
                <span>التقارير والإحصائيات</span>
            </button>
        </div>
    </div>
    
    <!-- المحتوى الرئيسي -->
    <div class="unified-content">
        <!-- قسم الإحصائيات الرئيسية -->
        <div class="content-section active" id="dashboard-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    الإحصائيات الرئيسية
                </h3>
                <p class="text-muted">نظرة شاملة على النظام</p>
            </div>
            
            <div class="dashboard-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">جاري تحميل لوحة التحكم الرئيسية...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم إدارة المستخدمين -->
        <div class="content-section" id="users-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-users me-2 text-primary"></i>
                    إدارة المستخدمين
                </h3>
                <button class="btn btn-primary" id="add-user-btn">
                    <i class="fas fa-plus me-2"></i>
                    إضافة مستخدم جديد
                </button>
            </div>
            
            <div class="users-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">جاري تحميل قائمة المستخدمين...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم طلبات الموافقة -->
        <div class="content-section" id="approvals-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-user-check me-2 text-warning"></i>
                    طلبات الموافقة
                </h3>
                <span class="badge bg-warning fs-6">{{ $stats['pending_users'] ?? 0 }} طلب معلق</span>
            </div>
            
            <div class="approvals-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-warning"></i>
                    <p class="mt-2">جاري تحميل طلبات الموافقة...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم الموظفين المعتمدين -->
        <div class="content-section" id="approved-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-user-shield me-2 text-success"></i>
                    الموظفين المعتمدين
                </h3>
                <span class="badge bg-success fs-6">{{ $stats['approved_users'] ?? 0 }} موظف معتمد</span>
            </div>
            
            <div class="approved-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
                    <p class="mt-2">جاري تحميل قائمة الموظفين المعتمدين...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم إدارة الأقسام -->
        <div class="content-section" id="departments-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-building me-2 text-info"></i>
                    إدارة الأقسام
                </h3>
                <button class="btn btn-info" id="add-department-btn">
                    <i class="fas fa-plus me-2"></i>
                    إضافة قسم جديد
                </button>
            </div>
            
            <div class="departments-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                    <p class="mt-2">جاري تحميل قائمة الأقسام...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم إدارة الوظائف -->
        <div class="content-section" id="jobs-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-briefcase me-2 text-secondary"></i>
                    إدارة الوظائف
                </h3>
                <button class="btn btn-secondary" id="add-job-btn">
                    <i class="fas fa-plus me-2"></i>
                    إضافة وظيفة جديدة
                </button>
            </div>
            
            <div class="jobs-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-secondary"></i>
                    <p class="mt-2">جاري تحميل قائمة الوظائف...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم طلبات التوظيف -->
        <div class="content-section" id="applications-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-file-alt me-2 text-warning"></i>
                    طلبات التوظيف
                </h3>
                <div class="filter-buttons">
                    <button class="btn btn-outline-primary btn-sm" data-filter="all">الكل</button>
                    <button class="btn btn-outline-warning btn-sm" data-filter="pending">معلقة</button>
                    <button class="btn btn-outline-success btn-sm" data-filter="approved">مقبولة</button>
                    <button class="btn btn-outline-danger btn-sm" data-filter="rejected">مرفوضة</button>
                </div>
            </div>
            
            <div class="applications-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-warning"></i>
                    <p class="mt-2">جاري تحميل طلبات التوظيف...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم العقود -->
        <div class="content-section" id="contracts-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-file-contract me-2 text-info"></i>
                    إدارة العقود
                </h3>
                <div class="filter-buttons">
                    <button class="btn btn-outline-info btn-sm" data-filter="all">جميع العقود</button>
                    <button class="btn btn-outline-secondary btn-sm" data-filter="draft">مسودة</button>
                    <button class="btn btn-outline-warning btn-sm" data-filter="sent">مرسلة</button>
                    <button class="btn btn-outline-success btn-sm" data-filter="signed">موقعة</button>
                    <button class="btn btn-outline-primary btn-sm" data-filter="active">نشطة</button>
                </div>
            </div>
            
            <div class="contracts-content">
                <div class="loading-placeholder">
                    <i class="fas fa-spinner fa-spin fa-2x text-info"></i>
                    <p class="mt-2">جاري تحميل العقود...</p>
                </div>
            </div>
        </div>
        
        <!-- قسم التقارير -->
        <div class="content-section" id="reports-section">
            <div class="section-header">
                <h3>
                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                    التقارير والإحصائيات
                </h3>
                <div class="export-buttons">
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>
                        تصدير Excel
                    </button>
                    <!-- PDF export button removed - using Word only -->
                </div>
            </div>
            
            <div class="reports-content">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="report-card">
                            <h5>تقرير المستخدمين</h5>
                            <p class="text-muted">إحصائيات شاملة عن المستخدمين</p>
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-1"></i>
                                تحميل التقرير
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="report-card">
                            <h5>تقرير الوظائف</h5>
                            <p class="text-muted">إحصائيات الوظائف والطلبات</p>
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-1"></i>
                                تحميل التقرير
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* الشريط الجانبي */
.unified-sidebar {
    width: 300px;
    background: #fff;
    border-right: 1px solid #e9ecef;
    height: 100%;
    overflow-y: auto;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
    color: white;
}

.sidebar-menu {
    padding: 15px 0;
}

.sidebar-item {
    width: 100%;
    padding: 12px 20px;
    border: none;
    background: none;
    text-align: right;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    color: #111111;
    font-weight: 500;
}

.sidebar-item:hover {
    background: rgba(180, 126, 19, 0.1);
    color: #b47e13;
}

.sidebar-item.active {
    background: #b47e13;
    color: white;
    border-right: 4px solid #be7b06;
}

.sidebar-item .badge {
    font-size: 0.75rem;
}

/* المحتوى الرئيسي */
.unified-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

.content-section {
    display: none;
}

.content-section.active {
    display: block;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.section-header h3 {
    margin: 0;
    color: #111111;
}

/* بطاقات الإحصائيات */
.stats-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(180, 126, 19, 0.2);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stats-primary .stats-icon { background: #b47e13; }
.stats-success .stats-icon { background: #be7b06; }
.stats-warning .stats-icon { background: #40260d; }
.stats-info .stats-icon { background: #2a2a00; }

.stats-content h4 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    color: #111111;
}

.stats-content p {
    margin: 0;
    color: #666666;
    font-weight: 500;
}

/* بطاقات الرسوم البيانية */
.chart-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
    height: 300px;
}

.chart-placeholder {
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #666666;
}

/* بطاقات التقارير */
.report-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
    text-align: center;
}

/* Loading Placeholder */
.loading-placeholder {
    text-align: center;
    padding: 60px 20px;
    color: #666666;
}

/* أزرار الفلترة */
.filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.export-buttons {
    display: flex;
    gap: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .d-flex {
        flex-direction: column;
    }
    
    .unified-sidebar {
        width: 100%;
        height: auto;
    }
    
    .sidebar-menu {
        display: flex;
        overflow-x: auto;
        padding: 10px;
        gap: 10px;
    }
    
    .sidebar-item {
        white-space: nowrap;
        min-width: 150px;
        border-radius: 25px;
        justify-content: center;
    }
    
    .section-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .filter-buttons,
    .export-buttons {
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // إدارة التنقل بين الأقسام
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const contentSections = document.querySelectorAll('.content-section');
    
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            const sectionName = this.dataset.section;
            console.log('Sidebar item clicked:', sectionName); // للتشخيص
            
            // إزالة الفئة النشطة من جميع العناصر
            sidebarItems.forEach(i => i.classList.remove('active'));
            contentSections.forEach(s => s.classList.remove('active'));
            
            // إضافة الفئة النشطة للعنصر المحدد
            this.classList.add('active');
            document.getElementById(sectionName + '-section').classList.add('active');
            
            // تحميل المحتوى بناءً على القسم
            loadSectionContent(sectionName);
        });
    });
    
    // دوال تحميل المحتوى
    window.loadDashboardContent = function() {
        const container = document.querySelector('#dashboard-section .dashboard-content');
        console.log('Loading dashboard...'); // للتشخيص
        
        if (!container) {
            console.error('Dashboard container not found!');
            return;
        }
        
        fetch('/admin/api/dashboard')
            .then(response => {
                console.log('Dashboard Response:', response); // للتشخيص
                return response.json();
            })
            .then(data => {
                console.log('Dashboard Data:', data); // للتشخيص
                if (data.success) {
                    container.innerHTML = buildDashboardContent(data.data);
                } else {
                    container.innerHTML = '<div class="alert alert-warning">لا توجد بيانات لوحة التحكم</div>';
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل لوحة التحكم:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات: ' + error.message + '</div>';
            });
    }
    
    window.loadUsersContent = function() {
        const container = document.querySelector('#users-section .users-content');
        console.log('Loading users...'); // للتشخيص
        console.log('Container found:', container); // للتشخيص
        
        if (!container) {
            console.error('Container not found!');
            return;
        }
        fetch('/admin/api/users')
            .then(response => {
                console.log('Response:', response); // للتشخيص
                return response.json();
            })
            .then(data => {
                console.log('Data:', data); // للتشخيص
                if (data.success) {
                    container.innerHTML = buildUsersTable(data.data);
                } else {
                    container.innerHTML = '<div class="alert alert-warning">لا توجد بيانات</div>';
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل المستخدمين:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات: ' + error.message + '</div>';
            });
    }
    
    window.loadApprovalsContent = function() {
        const container = document.querySelector('#approvals-section .approvals-content');
        console.log('Loading approvals...'); // للتشخيص
        fetch('/admin/api/approvals')
            .then(response => {
                console.log('Approvals Response:', response); // للتشخيص
                return response.json();
            })
            .then(data => {
                console.log('Approvals Data:', data); // للتشخيص
                if (data.success) {
                    container.innerHTML = buildApprovalsTable(data.data);
                } else {
                    container.innerHTML = '<div class="alert alert-warning">لا توجد بيانات</div>';
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل طلبات الموافقة:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات: ' + error.message + '</div>';
            });
    }
    
    window.loadApprovedContent = function() {
        const container = document.querySelector('#approved-section .approved-content');
        fetch('/admin/api/approved-users')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = buildApprovedUsersGrid(data.data);
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الموظفين المعتمدين:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
            });
    }
    
    window.loadDepartmentsContent = function() {
        const container = document.querySelector('#departments-section .departments-content');
        fetch('/admin/api/departments')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = buildDepartmentsTable(data.data);
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الأقسام:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
            });
    }
    
    window.loadJobsContent = function() {
        const container = document.querySelector('#jobs-section .jobs-content');
        fetch('/admin/api/jobs')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = buildJobsTable(data.data);
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الوظائف:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
            });
    }
    
    window.loadApplicationsContent = function() {
        const container = document.querySelector('#applications-section .applications-content');
        fetch('/admin/api/applications')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = buildApplicationsTable(data.data);
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل طلبات التوظيف:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
            });
    }
    
    window.loadContractsContent = function() {
        const container = document.querySelector('#contracts-section .contracts-content');
        fetch('/admin/api/contracts')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = buildContractsTable(data.data);
                } else {
                    container.innerHTML = '<div class="alert alert-warning">لا توجد عقود</div>';
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل العقود:', error);
                container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
            });
    }
    
    window.loadReportsContent = function() {
        const container = document.querySelector('#reports-section .reports-content');
        const html = `
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">تقرير الطلبات</h5>
                            <p class="card-text">تصدير تقرير بجميع طلبات التوظيف</p>
                            <div class="d-flex gap-2">
                                <a href="/admin/reports/applications" class="btn btn-primary btn-sm">عرض التقرير</a>
                                <a href="/admin/reports/applications/export/excel" class="btn btn-success btn-sm">تصدير Excel</a>
                                <!-- PDF export button removed -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">تقرير الوظائف</h5>
                            <p class="card-text">تصدير تقرير بجميع الوظائف المتاحة</p>
                            <div class="d-flex gap-2">
                                <a href="/admin/reports/jobs" class="btn btn-primary btn-sm">عرض التقرير</a>
                                <a href="/admin/reports/jobs/export/excel" class="btn btn-success btn-sm">تصدير Excel</a>
                                <!-- PDF export button removed -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML = html;
    }

    // تحميل محتوى القسم
    function loadSectionContent(section) {
        console.log('Loading section:', section); // للتشخيص
        const contentDiv = document.querySelector(`#${section}-section .${section}-content`);
        const loadingPlaceholder = document.querySelector(`#${section}-section .loading-placeholder`);
        console.log('Content div found:', contentDiv); // للتشخيص
        console.log('Loading placeholder found:', loadingPlaceholder); // للتشخيص
        
        if (contentDiv && loadingPlaceholder) {
            console.log('Starting content load...'); // للتشخيص
            // محاكاة تحميل المحتوى
            setTimeout(() => {
                switch(section) {
                    case 'dashboard':
                        loadDashboardContent();
                        break;
                    case 'users':
                        loadUsersContent();
                        break;
                    case 'approvals':
                        loadApprovalsContent();
                        break;
                    case 'approved':
                        loadApprovedContent();
                        break;
                    case 'departments':
                        loadDepartmentsContent();
                        break;
                    case 'jobs':
                        loadJobsContent();
                        break;
                    case 'applications':
                        loadApplicationsContent();
                        break;
                    case 'contracts':
                        loadContractsContent();
                        break;
                    case 'reports':
                        loadReportsContent();
                        break;
                }
            }, 1000);
        }
    }

    // دوال بناء HTML للجداول
    function buildUsersTable(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا توجد مستخدمين</div>';
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>الهاتف</th><th>الحالة</th><th>تاريخ التسجيل</th></tr></thead><tbody>';
        
        data.forEach(user => {
            html += `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.profile?.phone || 'غير محدد'}</td>
                    <td><span class="badge bg-${user.approval_status === 'approved' ? 'success' : user.approval_status === 'pending' ? 'warning' : 'danger'}">${user.approval_status === 'approved' ? 'معتمد' : user.approval_status === 'pending' ? 'معلق' : 'مرفوض'}</span></td>
                    <td>${new Date(user.created_at).toLocaleDateString('ar-SA')}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        return html;
    }

    function buildApprovalsTable(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا توجد طلبات موافقة معلقة</div>';
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>الهاتف</th><th>تاريخ التقديم</th><th>الإجراءات</th></tr></thead><tbody>';
        
        data.forEach(user => {
            html += `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.profile?.phone || 'غير محدد'}</td>
                    <td>${new Date(user.created_at).toLocaleDateString('ar-SA')}</td>
                    <td>
                        <button class="btn btn-success btn-sm me-1" onclick="approveUser(${user.id})">
                            <i class="fas fa-check"></i> موافقة
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectUser(${user.id})">
                            <i class="fas fa-times"></i> رفض
                        </button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        return html;
    }

    function buildApprovedUsersGrid(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا يوجد موظفين معتمدين</div>';
        }
        
        let html = '<div class="row g-4">';
        
        data.forEach(user => {
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-check text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">${user.name}</h6>
                                    <small class="text-muted">${user.email}</small>
                                </div>
                            </div>
                            <p><i class="fas fa-phone me-2 text-primary"></i>${user.profile?.phone || 'غير محدد'}</p>
                            <p><i class="fas fa-calendar me-2 text-primary"></i>${user.approved_at ? new Date(user.approved_at).toLocaleDateString('ar-SA') : 'غير محدد'}</p>
                            <div class="badge bg-success">${user.applications?.length || 0} طلب توظيف</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        return html;
    }

    function buildDepartmentsTable(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا توجد أقسام</div>';
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>اسم القسم</th><th>المدير</th><th>عدد الوظائف</th><th>الحالة</th><th>تاريخ الإنشاء</th></tr></thead><tbody>';
        
        data.forEach(dept => {
            html += `
                <tr>
                    <td>${dept.name}</td>
                    <td>${dept.user?.name || 'غير محدد'}</td>
                    <td><span class="badge bg-info">${dept.jobs_count || 0}</span></td>
                    <td><span class="badge bg-${dept.status ? 'success' : 'secondary'}">${dept.status ? 'نشط' : 'غير نشط'}</span></td>
                    <td>${new Date(dept.created_at).toLocaleDateString('ar-SA')}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        return html;
    }

    function buildJobsTable(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا توجد وظائف</div>';
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>عنوان الوظيفة</th><th>القسم</th><th>عدد المتقدمين</th><th>الحالة</th><th>آخر موعد</th></tr></thead><tbody>';
        
        data.forEach(job => {
            html += `
                <tr>
                    <td>${job.title}</td>
                    <td>${job.department?.name || 'غير محدد'}</td>
                    <td><span class="badge bg-info">${job.applications_count || 0}</span></td>
                    <td><span class="badge bg-${job.status === 'active' ? 'success' : job.status === 'inactive' ? 'warning' : 'secondary'}">${job.status === 'active' ? 'نشط' : job.status === 'inactive' ? 'غير نشط' : 'مغلق'}</span></td>
                    <td>${job.application_deadline ? new Date(job.application_deadline).toLocaleDateString('ar-SA') : 'غير محدد'}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        return html;
    }

    function buildApplicationsTable(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا توجد طلبات توظيف</div>';
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>المتقدم</th><th>الوظيفة</th><th>القسم</th><th>الحالة</th><th>تاريخ التقديم</th></tr></thead><tbody>';
        
        data.forEach(app => {
            html += `
                <tr>
                    <td>${app.user?.name || 'غير محدد'}</td>
                    <td>${app.job?.title || 'غير محدد'}</td>
                    <td>${app.job?.department?.name || 'غير محدد'}</td>
                    <td><span class="badge bg-${app.status === 'approved' ? 'success' : app.status === 'pending' ? 'warning' : 'danger'}">${app.status === 'approved' ? 'مقبول' : app.status === 'pending' ? 'معلق' : 'مرفوض'}</span></td>
                    <td>${new Date(app.created_at).toLocaleDateString('ar-SA')}</td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        return html;
    }
    
    function buildContractsTable(data) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">لا توجد عقود</div>';
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover"><thead class="table-light"><tr><th>رقم العقد</th><th>اسم الموظف</th><th>القسم</th><th>الراتب</th><th>تاريخ البداية</th><th>تاريخ النهاية</th><th>الحالة</th><th>الإجراءات</th></tr></thead><tbody>';
        
        data.forEach(contract => {
            const statusBadge = getContractStatusBadge(contract.status);
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-contract me-2 text-info"></i>
                            <strong>${contract.contract_number}</strong>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>${contract.employee_name}</strong>
                            <br><small class="text-muted">${contract.employee?.email || 'غير محدد'}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>${contract.department_name}</strong>
                            <br><small class="text-muted">${contract.department?.email || 'غير محدد'}</small>
                        </div>
                    </td>
                    <td>
                        <strong class="text-success">${formatSalary(contract.salary)}</strong>
                    </td>
                    <td>
                        <span class="text-muted">${formatDate(contract.start_date)}</span>
                    </td>
                    <td>
                        <span class="text-muted">${formatDate(contract.end_date)}</span>
                    </td>
                    <td>
                        ${statusBadge}
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewContract(${contract.id})" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </button>
                                                            <!-- PDF button removed - using Word only -->
                            <button class="btn btn-outline-primary" onclick="downloadWordContract(${contract.id})" title="تحميل Word">
                                <i class="fas fa-file-word"></i>
                            </button>
                            ${contract.status === 'draft' ? `
                                <button class="btn btn-outline-info" onclick="sendContractToEmployee(${contract.id})" title="إرسال للموظف">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            ` : ''}
                            ${contract.status === 'signed' ? `
                                <button class="btn btn-outline-warning" onclick="activateContract(${contract.id})" title="تفعيل العقد">
                                    <i class="fas fa-play"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table></div>';
        return html;
    }

    function buildDashboardContent(data) {
        const stats = data.stats;
        const recentJobs = data.recent_jobs;
        const recentApplications = data.recent_applications;
        const kpis = data.kpis;
        
        let html = `
            <!-- ترحيب -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card" style="background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%); color: white;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="card-title mb-2">مرحباً بك في لوحة التحكم</h2>
                                    <p class="card-text mb-0">نظام إدارة التوظيف الموسمي - شركة مناسك المشاعر</p>
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
                    <div class="stats-card stats-primary">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-content">
                            <h4>${stats.total_users}</h4>
                            <p>إجمالي المستخدمين</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card stats-success">
                        <div class="stats-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stats-content">
                            <h4>${stats.total_jobs}</h4>
                            <p>إجمالي الوظائف</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card stats-warning">
                        <div class="stats-icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="stats-content">
                            <h4>${stats.total_applications}</h4>
                            <p>إجمالي الطلبات</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card stats-info">
                        <div class="stats-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stats-content">
                            <h4>${stats.total_departments}</h4>
                            <p>الأقسام المسجلة</p>
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
                                <div class="col-md-4">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">المستخدمون</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">الموظفون</span>
                                        <strong class="text-success">${stats.total_employees}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small">الأقسام</span>
                                        <strong class="text-info">${stats.total_departments}</strong>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <h6 class="text-success border-bottom pb-2 mb-3">الوظائف</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">وظائف نشطة</span>
                                        <strong class="text-success">${stats.active_jobs}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small">وظائف مغلقة</span>
                                        <strong class="text-secondary">${stats.inactive_jobs}</strong>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <h6 class="text-warning border-bottom pb-2 mb-3">الطلبات</h6>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">قيد المراجعة</span>
                                        <strong class="text-warning">${stats.pending_applications}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">مقبولة</span>
                                        <strong class="text-success">${stats.accepted_applications}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small">مرفوضة</span>
                                        <strong class="text-danger">${stats.rejected_applications}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
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
                                            <p class="mb-1 small">${stats.today_registrations} مستخدم جديد اليوم</p>
                                            <small class="text-muted">${new Date().toLocaleDateString('ar-SA')}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                            <i class="fas fa-briefcase text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small">${stats.today_jobs} وظيفة جديدة اليوم</p>
                                            <small class="text-muted">${new Date().toLocaleDateString('ar-SA')}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="d-flex">
                                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
                                            <i class="fas fa-paper-plane text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small">${stats.today_applications} طلب جديد اليوم</p>
                                            <small class="text-muted">${new Date().toLocaleDateString('ar-SA')}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- أحدث الأنشطة -->
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase me-2 text-primary"></i>أحدث الوظائف
                            </h5>
                        </div>
                        <div class="card-body">
        `;
        
        if (recentJobs && recentJobs.length > 0) {
            recentJobs.forEach(job => {
                html += `
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                             style="width: 45px; height: 45px;">
                            <i class="fas fa-briefcase text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${job.title}</h6>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-building me-1"></i>${job.department?.name || 'قسم غير معروف'}
                                <span class="mx-2">•</span>
                                <i class="fas fa-users me-1"></i>${job.applications_count || 0} طلب
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge ${job.status == 'active' ? 'bg-success' : 'bg-secondary'}">
                                ${job.status == 'active' ? 'نشط' : 'غير نشط'}
                            </span>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="text-center py-3">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد وظائف</p>
                </div>
            `;
        }
        
        html += `
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-paper-plane me-2 text-warning"></i>أحدث الطلبات
                            </h5>
                        </div>
                        <div class="card-body">
        `;
        
        if (recentApplications && recentApplications.length > 0) {
            recentApplications.forEach(application => {
                html += `
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 small">${application.user?.name || 'مستخدم غير معروف'}</h6>
                            <p class="text-muted small mb-0">
                                ${application.job?.title?.substring(0, 25) + '...' || 'وظيفة غير معروفة'}
                                <span class="mx-2">•</span>
                                ${application.job?.department?.name || 'قسم غير معروف'}
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge ${application.status === 'approved' ? 'bg-success' : application.status === 'pending' ? 'bg-warning' : 'bg-danger'}">
                                ${application.status === 'approved' ? 'مقبول' : application.status === 'pending' ? 'معلق' : 'مرفوض'}
                            </span>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `
                <div class="text-center py-3">
                    <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد طلبات</p>
                </div>
            `;
        }
        
        html += `
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- تنبيهات النظام -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">
                                <i class="fas fa-bell me-2"></i>تنبيهات النظام
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
        `;
        
        if (stats.pending_applications > 0) {
            html += `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    يوجد ${stats.pending_applications} طلب في انتظار المراجعة
                </div>
            `;
        }
        
        if (stats.today_registrations > 0) {
            html += `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    انضم ${stats.today_registrations} مستخدم جديد اليوم
                </div>
            `;
        }
        
        html += `
                                </div>
                                <div class="col-md-6">
        `;
        
        if (stats.active_jobs == 0) {
            html += `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    لا توجد وظائف نشطة حالياً
                </div>
            `;
        }
        
        if (stats.today_jobs > 0) {
            html += `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    تم إضافة ${stats.today_jobs} وظيفة جديدة اليوم
                </div>
            `;
        }
        
        html += `
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return html;
    }

    // تحميل لوحة التحكم تلقائياً عند تحميل الصفحة
    loadDashboardContent();
});

// دوال إدارة المستخدمين - خارج الـ DOMContentLoaded
window.approveUser = function(userId) {
    if (confirm('هل أنت متأكد من الموافقة على هذا المستخدم؟')) {
        fetch(`/admin/users/${userId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('تم قبول المستخدم بنجاح');
                // إعادة تحميل القائمة
                if (typeof loadApprovalsContent === 'function') {
                    loadApprovalsContent();
                } else {
                    location.reload();
                }
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ تفصيلي:', error);
            alert('حدث خطأ في العملية: ' + error.message);
        });
    }
};

window.rejectUser = function(userId) {
    if (confirm('هل أنت متأكد من رفض هذا المستخدم؟')) {
        fetch(`/admin/users/${userId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Reject Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('تم رفض المستخدم بنجاح');
                // إعادة تحميل القائمة
                if (typeof loadApprovalsContent === 'function') {
                    loadApprovalsContent();
                } else {
                    location.reload();
                }
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في العملية');
        });
    }
};

window.viewUser = function(userId) {
    // فتح نافذة منبثقة لعرض تفاصيل المستخدم
    window.open(`/admin/users/${userId}`, '_blank');
};

window.editUser = function(userId) {
    // الانتقال لصفحة تعديل المستخدم
    window.location.href = `/admin/users/${userId}/edit`;
};

window.deleteUser = function(userId) {
    if (confirm('هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.')) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم حذف المستخدم بنجاح');
                // إعادة تحميل القائمة
                if (typeof loadUsersContent === 'function') {
                    loadUsersContent();
                } else {
                    location.reload();
                }
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في العملية');
        });
    }
};

// دوال مساعدة للعقود
function getContractStatusBadge(status) {
    const statuses = {
        'draft': { text: 'مسودة', class: 'bg-secondary' },
        'sent': { text: 'مرسلة', class: 'bg-info' },
        'reviewed': { text: 'تم الاطلاع', class: 'bg-warning' },
        'signed': { text: 'موقعة', class: 'bg-success' },
        'active': { text: 'نشطة', class: 'bg-primary' },
        'completed': { text: 'مكتملة', class: 'bg-success' },
        'cancelled': { text: 'ملغاة', class: 'bg-danger' }
    };
    
    const statusInfo = statuses[status] || { text: status, class: 'bg-secondary' };
    return `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;
}

function formatSalary(salary) {
    return new Intl.NumberFormat('ar-SA', { 
        style: 'currency', 
        currency: 'SAR',
        minimumFractionDigits: 0
    }).format(salary);
}

function formatDate(dateString) {
    if (!dateString) return 'غير محدد';
    return new Date(dateString).toLocaleDateString('ar-SA');
}

// دوال إجراءات العقود
function viewContract(contractId) {
    window.open(`/contracts/${contractId}`, '_blank');
}

// PDF download function removed - using Word only

function downloadWordContract(contractId) {
    window.open(`/contracts/${contractId}/download-word`, '_blank');
}

function sendContractToEmployee(contractId) {
    if (confirm('هل أنت متأكد من إرسال العقد للموظف؟')) {
        fetch(`/contracts/${contractId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إرسال العقد للموظف بنجاح');
                loadContractsContent();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في العملية');
        });
    }
}

function activateContract(contractId) {
    if (confirm('هل أنت متأكد من تفعيل هذا العقد؟')) {
        fetch(`/contracts/${contractId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: 'active' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم تفعيل العقد بنجاح');
                loadContractsContent();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في العملية');
        });
    }
}

function getContractStatusBadge(status) {
    const statuses = {
        'draft': { text: 'مسودة', class: 'bg-secondary' },
        'sent': { text: 'مرسلة', class: 'bg-info' },
        'reviewed': { text: 'تم الاطلاع', class: 'bg-warning' },
        'signed': { text: 'موقعة', class: 'bg-success' },
        'active': { text: 'نشطة', class: 'bg-primary' },
        'completed': { text: 'مكتملة', class: 'bg-success' },
        'cancelled': { text: 'ملغاة', class: 'bg-danger' }
    };
    
    const statusInfo = statuses[status] || { text: status, class: 'bg-secondary' };
    return `<span class="badge ${statusInfo.class}">${statusInfo.text}</span>`;
}

function formatSalary(salary) {
    return new Intl.NumberFormat('ar-SA', { 
        style: 'currency', 
        currency: 'SAR',
        minimumFractionDigits: 0
    }).format(salary);
}

function formatDate(dateString) {
    if (!dateString) return 'غير محدد';
    return new Date(dateString).toLocaleDateString('ar-SA');
}

// دوال إجراءات العقود
function viewContract(contractId) {
    window.open(`/contracts/${contractId}`, '_blank');
}

// PDF download function removed - using Word only

function sendContractToEmployee(contractId) {
    if (confirm('هل أنت متأكد من إرسال العقد للموظف؟')) {
        fetch(`/contracts/${contractId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إرسال العقد للموظف بنجاح');
                loadContractsContent();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في العملية');
        });
    }
}

function activateContract(contractId) {
    if (confirm('هل أنت متأكد من تفعيل هذا العقد؟')) {
        fetch(`/contracts/${contractId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: 'active' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم تفعيل العقد بنجاح');
                loadContractsContent();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في العملية');
        });
    }
}
</script>

@endsection 