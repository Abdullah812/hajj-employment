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
                            <li class="breadcrumb-item active">تقرير الوظائف المفصل</li>
                        </ol>
                    </nav>
                    <h2 class="mb-1">
                        <i class="fas fa-briefcase text-primary me-2"></i>
                        تقرير الوظائف المفصل
                    </h2>
                    <p class="text-muted mb-0">تحليل شامل ومفصل لجميع الوظائف مع إحصائيات متقدمة</p>
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
                                <a class="dropdown-item" href="{{ route('admin.reports.jobs.export') }}">
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
                            <h6 class="text-muted mb-1">إجمالي الوظائف</h6>
                            <h3 class="mb-0 text-primary">{{ $jobs->total() }}</h3>
                            <small class="text-muted">من أصل {{ $jobs->total() }} وظيفة</small>
                        </div>
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded">
                            <i class="fas fa-briefcase fa-lg text-primary"></i>
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
                            <h6 class="text-muted mb-1">وظائف نشطة</h6>
                            <h3 class="mb-0 text-success" id="activeJobsCount">{{ $jobs->where('status', 'active')->count() }}</h3>
                            <small class="text-success">
                                {{ $jobs->total() > 0 ? round(($jobs->where('status', 'active')->count() / $jobs->total()) * 100, 1) : 0 }}% من الإجمالي
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
                            <h6 class="text-muted mb-1">إجمالي الطلبات</h6>
                            <h3 class="mb-0 text-info">{{ $jobs->sum(function($job) { return $job->applications->count(); }) }}</h3>
                            <small class="text-info">
                                متوسط {{ $jobs->count() > 0 ? round($jobs->sum(function($job) { return $job->applications->count(); }) / $jobs->count(), 1) : 0 }} طلب/وظيفة
                            </small>
                        </div>
                        <div class="avatar-sm bg-info bg-opacity-10 rounded">
                            <i class="fas fa-users fa-lg text-info"></i>
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
                            <h6 class="text-muted mb-1">أقسام نشطة</h6>
                            <h3 class="mb-0 text-warning">{{ $jobs->unique('department_id')->count() }}</h3>
                            <small class="text-warning">قسم يطرح وظائف</small>
                        </div>
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded">
                            <i class="fas fa-building fa-lg text-warning"></i>
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
                    <form id="jobsFilterForm" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">القسم</label>
                                <select class="form-select" name="department" id="departmentFilter">
                                    <option value="">جميع الأقسام</option>
                                    @foreach($jobs->unique('department.name')->pluck('department') as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">نوع التوظيف</label>
                                <select class="form-select" name="employment_type" id="employmentTypeFilter">
                                    <option value="">جميع الأنواع</option>
                                    <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                    <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                    <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>مؤقت</option>
                                    <option value="seasonal" {{ request('employment_type') == 'seasonal' ? 'selected' : '' }}>موسمي</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">الحالة</label>
                                <select class="form-select" name="status" id="statusFilter">
                                    <option value="">جميع الحالات</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">ترتيب حسب</label>
                                <select class="form-select" name="sort" id="sortFilter">
                                    <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>الأحدث أولاً</option>
                                    <option value="created_at_asc" {{ request('sort') == 'created_at_asc' ? 'selected' : '' }}>الأقدم أولاً</option>
                                    <option value="applications_desc" {{ request('sort') == 'applications_desc' ? 'selected' : '' }}>الأكثر طلبات</option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>أبجدياً أ-ي</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>تطبيق الفلاتر
                                    </button>
                                    <a href="{{ route('admin.reports.jobs') }}" class="btn btn-outline-secondary">
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
                    قائمة الوظائف التفصيلية
                    <span class="badge bg-primary ms-2">{{ $jobs->count() }} وظيفة</span>
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" id="selectAll">
                        <i class="fas fa-check-square me-1"></i>تحديد الكل
                    </button>
                    <button class="btn btn-outline-warning" id="bulkActions" disabled>
                        <i class="fas fa-cogs me-1"></i>عمليات مجمعة
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="jobsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                            </th>
                            <th>رقم الوظيفة</th>
                            <th>المسمى الوظيفي</th>
                            <th>القسم</th>
                            <th>نوع التوظيف</th>
                            <th>عدد الطلبات</th>
                            <th>تاريخ النشر</th>
                            <th>آخر تحديث</th>
                            <th>الحالة</th>
                            <th width="120">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                        <tr class="job-row" data-job-id="{{ $job->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input job-checkbox" value="{{ $job->id }}">
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">#{{ $job->id }}</span>
                            </td>
                            <td>
                                <div>
                                    <strong class="text-dark">{{ $job->title }}</strong>
                                    @if($job->employment_type == 'seasonal')
                                        <span class="badge bg-warning text-dark ms-1">موسمي</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ Str::limit($job->description, 50) }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-primary bg-opacity-10 rounded me-2">
                                        <i class="fas fa-building fa-sm text-primary"></i>
                                    </div>
                                    <span>{{ $job->department->name ?? 'غير محدد' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeLabels = [
                                        'full_time' => ['دوام كامل', 'primary'],
                                        'part_time' => ['دوام جزئي', 'info'],
                                        'temporary' => ['مؤقت', 'warning'],
                                        'seasonal' => ['موسمي', 'success']
                                    ];
                                    $type = $typeLabels[$job->employment_type] ?? ['غير محدد', 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $type[1] }}">{{ $type[0] }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $job->applications->count() }}</span>
                                    @if($job->applications->count() > 10)
                                        <i class="fas fa-fire text-danger" title="وظيفة مطلوبة"></i>
                                    @elseif($job->applications->count() == 0)
                                        <i class="fas fa-exclamation-triangle text-warning" title="لا توجد طلبات"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="text-dark">{{ $job->created_at->format('Y/m/d') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $job->updated_at->format('Y/m/d') }}</span>
                            </td>
                            <td>
                                @if($job->status === 'active')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>نشط
                                    </span>
                                @elseif($job->status === 'inactive')
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-pause-circle me-1"></i>غير نشط
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>مغلق
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="عرض التفاصيل" onclick="viewJobDetails({{ $job->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="عرض الطلبات" onclick="viewApplications({{ $job->id }})">
                                        <i class="fas fa-users"></i>
                                    </button>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>تعديل</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-copy me-2"></i>نسخ</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>حذف</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد وظائف</h5>
                                    <p class="text-muted">لم يتم العثور على وظائف تطابق معايير البحث</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($jobs->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    عرض {{ $jobs->firstItem() }} إلى {{ $jobs->lastItem() }} من أصل {{ $jobs->total() }} وظيفة
                </div>
                {{ $jobs->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal تفاصيل الوظيفة -->
<div class="modal fade" id="jobDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-briefcase me-2"></i>
                    تفاصيل الوظيفة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="jobDetailsContent">
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
        const checkboxes = document.querySelectorAll('.job-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkActions();
    });
    
    // مراقبة تحديد العناصر الفردية
    document.querySelectorAll('.job-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActions);
    });
    
    // تفعيل/تعطيل العمليات المجمعة
    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.job-checkbox:checked');
        const bulkActionsBtn = document.getElementById('bulkActions');
        bulkActionsBtn.disabled = checkedBoxes.length === 0;
        
        if (checkedBoxes.length > 0) {
            bulkActionsBtn.textContent = `عمليات مجمعة (${checkedBoxes.length})`;
        } else {
            bulkActionsBtn.textContent = 'عمليات مجمعة';
        }
    }
    
    // تحديث البيانات
    document.getElementById('refreshData').addEventListener('click', function() {
        location.reload();
    });
});

// عرض تفاصيل الوظيفة
function viewJobDetails(jobId) {
    const modal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
    modal.show();
    
    fetch(`/admin/jobs/${jobId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('jobDetailsContent').innerHTML = generateJobDetailsHTML(data);
        })
        .catch(error => {
            document.getElementById('jobDetailsContent').innerHTML = 
                '<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>';
        });
}

// عرض طلبات الوظيفة
function viewApplications(jobId) {
    window.open(`/admin/jobs/${jobId}/applications`, '_blank');
}

// إنشاء HTML لتفاصيل الوظيفة
function generateJobDetailsHTML(job) {
    return `
        <div class="row g-4">
            <div class="col-12">
                <h6 class="text-primary">معلومات أساسية</h6>
                <table class="table table-borderless table-sm">
                    <tr><td><strong>المسمى الوظيفي:</strong></td><td>${job.title}</td></tr>
                    <tr><td><strong>القسم:</strong></td><td>${job.department}</td></tr>
                    <tr><td><strong>نوع التوظيف:</strong></td><td>${job.employment_type}</td></tr>
                    <tr><td><strong>الموقع:</strong></td><td>${job.location}</td></tr>
                </table>
            </div>
            <div class="col-12">
                <h6 class="text-primary">الوصف</h6>
                <p class="text-muted">${job.description}</p>
            </div>
            <div class="col-12">
                <h6 class="text-primary">إحصائيات</h6>
                <div class="row g-3">
                    <div class="col-4 text-center">
                        <div class="border rounded p-2">
                            <div class="h5 mb-0 text-primary">${job.applications_count}</div>
                            <small class="text-muted">طلبات</small>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="border rounded p-2">
                            <div class="h5 mb-0 text-success">${job.approved_applications}</div>
                            <small class="text-muted">مقبول</small>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="border rounded p-2">
                            <div class="h5 mb-0 text-warning">${job.pending_applications}</div>
                            <small class="text-muted">معلق</small>
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

.job-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
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