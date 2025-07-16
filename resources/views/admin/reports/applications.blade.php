@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <!-- Header متطور -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.reports.index') }}">
                                    <i class="fas fa-chart-line me-1"></i>التقارير
                                </a>
                            </li>
                            <li class="breadcrumb-item active">تقرير الطلبات المفصل</li>
                        </ol>
                    </nav>
                    <h2 class="mb-1">
                        <i class="fas fa-file-alt text-success me-2"></i>
                        تقرير الطلبات المفصل
                    </h2>
                    <p class="text-muted mb-0">تحليل شامل ومفصل لجميع طلبات التوظيف مع إحصائيات الأداء</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary" id="refreshData">
                        <i class="fas fa-sync-alt me-1"></i>تحديث
                    </button>
                    <div class="btn-group">
                        <button class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>تصدير
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.reports.applications.export') }}">
                                    <i class="fas fa-file-excel me-2"></i>Excel أساسي
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" id="exportAdvanced">
                                    <i class="fas fa-file-excel me-2"></i>Excel متقدم
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" id="exportPDF">
                                    <i class="fas fa-file-pdf me-2"></i>PDF مفصل
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" id="printReport">
                                    <i class="fas fa-print me-2"></i>طباعة
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">إجمالي الطلبات</h6>
                            <h3 class="mb-0 text-primary">{{ $applications->total() }}</h3>
                            <small class="text-muted">طلب توظيف</small>
                        </div>
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded">
                            <i class="fas fa-file-alt fa-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">طلبات مقبولة</h6>
                            <h3 class="mb-0 text-success">{{ $applications->where('status', 'approved')->count() }}</h3>
                            <small class="text-success">
                                {{ $applications->total() > 0 ? round(($applications->where('status', 'approved')->count() / $applications->total()) * 100, 1) : 0 }}% معدل القبول
                            </small>
                        </div>
                        <div class="avatar-sm bg-success bg-opacity-10 rounded">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">قيد المراجعة</h6>
                            <h3 class="mb-0 text-warning">{{ $applications->where('status', 'pending')->count() }}</h3>
                            <small class="text-warning">
                                يحتاج مراجعة عاجلة
                            </small>
                        </div>
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded">
                            <i class="fas fa-hourglass-half fa-lg text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">طلبات مرفوضة</h6>
                            <h3 class="mb-0 text-danger">{{ $applications->where('status', 'rejected')->count() }}</h3>
                            <small class="text-danger">
                                {{ $applications->total() > 0 ? round(($applications->where('status', 'rejected')->count() / $applications->total()) * 100, 1) : 0 }}% معدل الرفض
                            </small>
                        </div>
                        <div class="avatar-sm bg-danger bg-opacity-10 rounded">
                            <i class="fas fa-times-circle fa-lg text-danger"></i>
                        </div>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>فلاتر البحث المتقدم
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" id="toggleAdvancedFilters">
                            <i class="fas fa-chevron-down me-1"></i>إظهار/إخفاء
                        </button>
                    </div>
                </div>
                <div class="card-body collapse" id="advancedFilters">
                    <form id="applicationsFilterForm" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select class="form-select" name="status" id="statusFilter">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبول</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">القسم</label>
                                <select class="form-select" name="department" id="departmentFilter">
                                    <option value="">جميع الأقسام</option>
                                    @foreach($applications->unique('job.department.name')->pluck('job.department')->filter() as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">تاريخ التقديم</label>
                                <select class="form-select" name="date_range" id="dateRangeFilter">
                                    <option value="">جميع التواريخ</option>
                                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>اليوم</option>
                                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                                    <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>هذا الربع</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">ترتيب حسب</label>
                                <select class="form-select" name="sort" id="sortFilter">
                                    <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>الأحدث أولاً</option>
                                    <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>الأقدم أولاً</option>
                                    <option value="reviewed_at_desc" {{ request('sort') == 'reviewed_at_desc' ? 'selected' : '' }}>آخر مراجعة</option>
                                    <option value="user_name_asc" {{ request('sort') == 'user_name_asc' ? 'selected' : '' }}>اسم المتقدم أ-ي</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>تطبيق الفلاتر
                                    </button>
                                    <a href="{{ route('admin.reports.applications') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i>إعادة تعيين
                                    </a>
                                    <button type="button" class="btn btn-info" id="saveFilters">
                                        <i class="fas fa-bookmark me-1"></i>حفظ الفلاتر
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- الجدول المتطور -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    قائمة الطلبات التفصيلية
                    <span class="badge bg-primary ms-2">{{ $applications->count() }} طلب</span>
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" id="selectAll">
                        <i class="fas fa-check-square me-1"></i>تحديد الكل
                    </button>
                    <button class="btn btn-outline-warning" id="bulkActions" disabled>
                        <i class="fas fa-cogs me-1"></i>عمليات مجمعة
                    </button>
                    <button class="btn btn-outline-success" id="bulkApprove" disabled>
                        <i class="fas fa-check me-1"></i>قبول المحدد
                    </button>
                    <button class="btn btn-outline-danger" id="bulkReject" disabled>
                        <i class="fas fa-times me-1"></i>رفض المحدد
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="applicationsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                            </th>
                            <th>رقم الطلب</th>
                            <th>المتقدم</th>
                            <th>الوظيفة</th>
                            <th>القسم</th>
                            <th>تاريخ التقديم</th>
                            <th>تاريخ المراجعة</th>
                            <th>الحالة</th>
                            <th>الأولوية</th>
                            <th width="150">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                        <tr class="application-row" data-application-id="{{ $application->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input application-checkbox" value="{{ $application->id }}">
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $application->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-secondary bg-opacity-10 rounded-circle me-2">
                                        <i class="fas fa-user fa-sm text-secondary"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark">{{ $application->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $application->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="text-dark">{{ $application->job->title }}</span>
                                    <br>
                                    <small class="text-muted">{{ $application->job->employment_type }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-primary bg-opacity-10 rounded me-2">
                                        <i class="fas fa-building fa-sm text-primary"></i>
                                    </div>
                                    <span>{{ $application->job->department->name ?? 'غير محدد' }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="text-dark">{{ $application->created_at->format('Y/m/d') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td>
                                @if($application->reviewed_at)
                                    <span class="text-dark">{{ $application->reviewed_at->format('Y/m/d') }}</span>
                                @else
                                    <span class="text-muted">لم تتم المراجعة</span>
                                @endif
                            </td>
                            <td>
                                @if($application->status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>مقبول
                                    </span>
                                @elseif($application->status === 'pending')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-hourglass-half me-1"></i>قيد المراجعة
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>مرفوض
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $daysSinceSubmission = $application->created_at->diffInDays(now());
                                    $priority = 'low';
                                    $priorityColor = 'success';
                                    $priorityIcon = 'arrow-down';
                                    
                                    if ($application->status === 'pending') {
                                        if ($daysSinceSubmission > 7) {
                                            $priority = 'high';
                                            $priorityColor = 'danger';
                                            $priorityIcon = 'exclamation-triangle';
                                        } elseif ($daysSinceSubmission > 3) {
                                            $priority = 'medium';
                                            $priorityColor = 'warning';
                                            $priorityIcon = 'clock';
                                        }
                                    }
                                @endphp
                                <span class="badge bg-{{ $priorityColor }}">
                                    <i class="fas fa-{{ $priorityIcon }} me-1"></i>
                                    @if($priority === 'high') عاجل
                                    @elseif($priority === 'medium') متوسط
                                    @else عادي
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="عرض التفاصيل" onclick="viewApplicationDetails({{ $application->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="عرض الملف الشخصي" onclick="viewUserProfile({{ $application->user->id }})">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    @if($application->status === 'pending')
                                        <button class="btn btn-outline-success" title="قبول" onclick="updateApplicationStatus({{ $application->id }}, 'approved')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="رفض" onclick="updateApplicationStatus({{ $application->id }}, 'rejected')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد طلبات</h5>
                                    <p class="text-muted">لم يتم العثور على طلبات تطابق معايير البحث</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($applications->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    عرض {{ $applications->firstItem() }} إلى {{ $applications->lastItem() }} من أصل {{ $applications->total() }} طلب
                </div>
                {{ $applications->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal تفاصيل الطلب -->
<div class="modal fade" id="applicationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                    تفاصيل طلب التوظيف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="applicationDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // تبديل الفلاتر المتقدمة
    document.getElementById('toggleAdvancedFilters').addEventListener('click', function() {
        const filters = document.getElementById('advancedFilters');
        const icon = this.querySelector('i');
        
        if (filters.classList.contains('show')) {
            filters.classList.remove('show');
            icon.className = 'fas fa-chevron-down me-1';
        } else {
            filters.classList.add('show');
            icon.className = 'fas fa-chevron-up me-1';
        }
    });
    
    // تحديد/إلغاء تحديد الكل
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.application-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkActions();
    });
    
    // مراقبة تحديد العناصر الفردية
    document.querySelectorAll('.application-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActions);
    });
    
    // تفعيل/تعطيل العمليات المجمعة
    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.application-checkbox:checked');
        const bulkActionsBtn = document.getElementById('bulkActions');
        const bulkApproveBtn = document.getElementById('bulkApprove');
        const bulkRejectBtn = document.getElementById('bulkReject');
        
        const isDisabled = checkedBoxes.length === 0;
        bulkActionsBtn.disabled = isDisabled;
        bulkApproveBtn.disabled = isDisabled;
        bulkRejectBtn.disabled = isDisabled;
        
        if (checkedBoxes.length > 0) {
            bulkActionsBtn.textContent = `عمليات مجمعة (${checkedBoxes.length})`;
            bulkApproveBtn.innerHTML = `<i class="fas fa-check me-1"></i>قبول (${checkedBoxes.length})`;
            bulkRejectBtn.innerHTML = `<i class="fas fa-times me-1"></i>رفض (${checkedBoxes.length})`;
        } else {
            bulkActionsBtn.textContent = 'عمليات مجمعة';
            bulkApproveBtn.innerHTML = '<i class="fas fa-check me-1"></i>قبول المحدد';
            bulkRejectBtn.innerHTML = '<i class="fas fa-times me-1"></i>رفض المحدد';
        }
    }
    
    // تحديث البيانات
    document.getElementById('refreshData').addEventListener('click', function() {
        location.reload();
    });
    
    // العمليات المجمعة للقبول
    document.getElementById('bulkApprove').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.application-checkbox:checked');
        const applicationIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (confirm(`هل تريد قبول ${applicationIds.length} طلب محدد؟`)) {
            bulkUpdateApplicationStatus(applicationIds, 'approved');
        }
    });
    
    // العمليات المجمعة للرفض
    document.getElementById('bulkReject').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.application-checkbox:checked');
        const applicationIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (confirm(`هل تريد رفض ${applicationIds.length} طلب محدد؟`)) {
            bulkUpdateApplicationStatus(applicationIds, 'rejected');
        }
    });
});

// عرض تفاصيل الطلب
function viewApplicationDetails(applicationId) {
    const modal = new bootstrap.Modal(document.getElementById('applicationDetailsModal'));
    modal.show();
    
    fetch(`/admin/applications/${applicationId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('applicationDetailsContent').innerHTML = generateApplicationDetailsHTML(data);
        })
        .catch(error => {
            document.getElementById('applicationDetailsContent').innerHTML = 
                '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
        });
}

// عرض الملف الشخصي
function viewUserProfile(userId) {
    window.open(`/admin/users/${userId}/profile`, '_blank');
}

// تحديث حالة الطلب
function updateApplicationStatus(applicationId, status) {
    const statusText = status === 'approved' ? 'قبول' : 'رفض';
    
    if (confirm(`هل تريد ${statusText} هذا الطلب؟`)) {
        fetch(`/admin/applications/${applicationId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ في تحديث الحالة');
            }
        });
    }
}

// تحديث حالة متعددة
function bulkUpdateApplicationStatus(applicationIds, status) {
    fetch('/admin/applications/bulk-status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ 
            application_ids: applicationIds,
            status: status 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('حدث خطأ في تحديث الحالة');
        }
    });
}

// إنشاء HTML لتفاصيل الطلب
function generateApplicationDetailsHTML(application) {
    return `
        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-primary">معلومات المتقدم</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>الاسم:</strong></td><td>${application.user.name}</td></tr>
                    <tr><td><strong>البريد الإلكتروني:</strong></td><td>${application.user.email}</td></tr>
                    <tr><td><strong>رقم الهاتف:</strong></td><td>${application.user.phone || 'غير متاح'}</td></tr>
                    <tr><td><strong>تاريخ التسجيل:</strong></td><td>${application.user.created_at}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">معلومات الوظيفة</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>المسمى الوظيفي:</strong></td><td>${application.job.title}</td></tr>
                    <tr><td><strong>القسم:</strong></td><td>${application.job.department}</td></tr>
                    <tr><td><strong>نوع التوظيف:</strong></td><td>${application.job.employment_type}</td></tr>
                    <tr><td><strong>الموقع:</strong></td><td>${application.job.location}</td></tr>
                </table>
            </div>
            <div class="col-12">
                <h6 class="text-primary">معلومات الطلب</h6>
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div class="border rounded p-3">
                            <div class="h6 mb-0 text-primary">${application.created_at}</div>
                            <small class="text-muted">تاريخ التقديم</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="border rounded p-3">
                            <div class="h6 mb-0 text-success">${application.status}</div>
                            <small class="text-muted">الحالة الحالية</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="border rounded p-3">
                            <div class="h6 mb-0 text-warning">${application.reviewed_at || 'لم تتم'}</div>
                            <small class="text-muted">تاريخ المراجعة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}
</script>

<style>
.avatar-xs {
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.application-row:hover {
    background-color: rgba(25, 135, 84, 0.05);
}

.empty-state {
    padding: 2rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
</style>
@endsection 