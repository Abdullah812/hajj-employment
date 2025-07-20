@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        طلبات الموافقة على المستخدمين
                    </h3>
                    <span class="badge bg-warning fs-6">
                        {{ $pendingUsers->total() }} طلب معلق
                    </span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($pendingUsers->count() > 0)
                        <!-- عرض البطاقات بدلاً من الجدول للوضوح أكثر -->
                        <div class="row g-4">
                            @foreach($pendingUsers as $user)
                                <div class="col-lg-6 col-xl-4">
                                    <div class="card border-0 shadow-sm h-100 user-approval-card">
                                        <div class="card-header bg-light border-0">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-warning"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-0">{{ $user->name }}</h5>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            <!-- معلومات أساسية سريعة -->
                                            <div class="quick-info mb-3">
                                                <div class="info-item">
                                                    <i class="fas fa-phone text-primary me-2"></i>
                                                    <span>{{ optional($user->profile)->phone ?? 'غير محدد' }}</span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-id-card text-primary me-2"></i>
                                                    <span>{{ optional($user->profile)->national_id ?? 'غير محدد' }}</span>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                                    <span>{{ optional($user->profile)->qualification ?? 'غير محدد' }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- المرفقات -->
                                            <div class="attachments-preview mb-3">
                                                <h6 class="text-muted mb-2">
                                                    <i class="fas fa-paperclip me-1"></i>المرفقات:
                                                </h6>
                                                <div class="d-flex flex-wrap gap-1">
                                                @if($user->profile && $user->profile->cv_file_data)
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-file-pdf me-1"></i>السيرة الذاتية
                                                        </span>
                                                    @endif
                                                    @if($user->profile && $user->profile->national_id_file_data)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-id-card me-1"></i>الهوية
                                                        </span>
                                                @endif
                                                @if($user->profile && $user->profile->iban_file_data)
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-university me-1"></i>الآيبان
                                                        </span>
                                                @endif
                                                @if($user->profile && $user->profile->experience_file_data)
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-certificate me-1"></i>الخبرة
                                                        </span>
                                                @endif
                                                @if($user->profile && $user->profile->national_address_file_data)
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-home me-1"></i>العنوان الوطني
                                                        </span>
                                                @endif
                                                </div>
                                            </div>
                                            
                                            <!-- تاريخ التقديم -->
                                            <div class="text-muted small mb-3">
                                                <i class="fas fa-clock me-1"></i>
                                                تاريخ التسجيل: {{ $user->created_at->format('Y-m-d H:i') }}
                                                <span class="ms-2">({{ $user->created_at->diffForHumans() }})</span>
                                            </div>
                                        </div>
                                        
                                        <div class="card-footer bg-transparent border-0">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-info btn-sm flex-fill" 
                                                        onclick="viewUserDetails({{ $user->id }})">
                                                    <i class="fas fa-eye me-1"></i>عرض التفاصيل
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm" 
                                                        onclick="approveUser({{ $user->id }})">
                                                    <i class="fas fa-check me-1"></i>موافقة
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="showRejectModal({{ $user->id }})">
                                                    <i class="fas fa-times me-1"></i>رفض
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                    </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                    {{ $pendingUsers->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-user-check fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted">لا يوجد طلبات موافقة معلقة</h4>
                            <p class="text-muted mb-4">جميع طلبات الموافقة تم الرد عليها</p>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i>إدارة المستخدمين
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal عرض التفاصيل الكاملة -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>تفاصيل المستخدم
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2">جاري تحميل التفاصيل...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Rejection -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رفض المستخدم</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">سبب الرفض</label>
                        <textarea class="form-control" id="rejection_reason" 
                                name="rejection_reason" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.user-approval-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.user-approval-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.quick-info .info-item {
    padding: 4px 0;
    font-size: 0.9rem;
}

.quick-info .info-item i {
    width: 20px;
}

.attachments-preview .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
}

.user-details-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.user-details-section h6 {
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.detail-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #6c757d;
    min-width: 120px;
    display: inline-block;
}

.attachment-links .btn {
    margin: 0.2rem;
}

.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endpush

@push('scripts')
<script>
// عرض تفاصيل المستخدم في نافذة منبثقة
function viewUserDetails(userId) {
    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    const content = document.getElementById('userDetailsContent');
    
    // إعادة تعيين المحتوى
    content.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">جاري تحميل التفاصيل...</p>
        </div>
    `;
    
    modal.show();
    
    // جلب بيانات المستخدم
    fetch(`/admin/api/user-details/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = buildUserDetailsHTML(data.user);
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        خطأ في تحميل البيانات: ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    حدث خطأ غير متوقع: ${error.message}
                </div>
            `;
        });
}

// بناء HTML لتفاصيل المستخدم
function buildUserDetailsHTML(user) {
    const profile = user.profile || {};
    
    return `
        <div class="row">
            <div class="col-md-4">
                <div class="user-details-section">
                    <h6><i class="fas fa-user me-2"></i>المعلومات الشخصية</h6>
                    <div class="detail-item">
                        <span class="detail-label">الاسم:</span>
                        <span>${user.name}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">البريد:</span>
                        <span>${user.email}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">رقم الهوية:</span>
                        <span>${profile.national_id || 'غير محدد'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">تاريخ الميلاد:</span>
                        <span>${profile.date_of_birth || 'غير محدد'}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="user-details-section">
                    <h6><i class="fas fa-phone me-2"></i>معلومات الاتصال</h6>
                    <div class="detail-item">
                        <span class="detail-label">الهاتف:</span>
                        <span>${profile.phone || 'غير محدد'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">العنوان:</span>
                        <span>${profile.address || 'غير محدد'}</span>
                    </div>
                </div>
                
                <div class="user-details-section">
                    <h6><i class="fas fa-university me-2"></i>المعلومات البنكية</h6>
                    <div class="detail-item">
                        <span class="detail-label">رقم الآيبان:</span>
                        <span>${profile.iban_number || 'غير محدد'}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="user-details-section">
                    <h6><i class="fas fa-graduation-cap me-2"></i>المؤهلات والخبرات</h6>
                    <div class="detail-item">
                        <span class="detail-label">المؤهل:</span>
                        <span>${profile.qualification || 'غير محدد'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">الخبرات:</span>
                        <span>${profile.academic_experience || 'غير محدد'}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="user-details-section">
            <h6><i class="fas fa-paperclip me-2"></i>المرفقات والمستندات</h6>
            <div class="attachment-links">
                                    ${profile.cv_file_data ? `<a href="${profile.cv_url}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf me-1"></i>السيرة الذاتية</a>` : ''}
                    ${profile.national_id_file_data ? `<a href="${profile.national_id_attachment_url}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-id-card me-1"></i>الهوية الوطنية</a>` : ''}
                    ${profile.iban_file_data ? `<a href="${profile.iban_attachment_url}" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-university me-1"></i>مرفق الآيبان</a>` : ''}
                    ${profile.national_address_file_data ? `<a href="${profile.national_address_attachment_url}" class="btn btn-warning btn-sm" target="_blank"><i class="fas fa-home me-1"></i>العنوان الوطني</a>` : ''}
                    ${profile.experience_file_data ? `<a href="${profile.experience_certificate_url}" class="btn btn-secondary btn-sm" target="_blank"><i class="fas fa-certificate me-1"></i>شهادة الخبرة</a>` : ''}
            </div>
                            ${!profile.cv_file_data && !profile.national_id_file_data && !profile.iban_file_data && !profile.national_address_file_data && !profile.experience_file_data ? 
                '<p class="text-muted"><i class="fas fa-info-circle me-2"></i>لا توجد مرفقات مرفوعة</p>' : ''}
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="user-details-section">
                    <h6><i class="fas fa-clock me-2"></i>معلومات التسجيل</h6>
                    <div class="detail-item">
                        <span class="detail-label">تاريخ التسجيل:</span>
                        <span>${new Date(user.created_at).toLocaleDateString('ar-SA')}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">الحالة:</span>
                        <span class="badge badge-lg bg-warning">معلق الموافقة</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="user-details-section">
                    <h6><i class="fas fa-cogs me-2"></i>إجراءات سريعة</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="approveUser(${user.id}); bootstrap.Modal.getInstance(document.getElementById('userDetailsModal')).hide();">
                            <i class="fas fa-check me-2"></i>موافقة على المستخدم
                        </button>
                        <button class="btn btn-danger" onclick="showRejectModal(${user.id}); bootstrap.Modal.getInstance(document.getElementById('userDetailsModal')).hide();">
                            <i class="fas fa-times me-2"></i>رفض المستخدم
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// باقي الوظائف الموجودة
function approveUser(userId) {
    if (confirm('هل أنت متأكد من الموافقة على هذا المستخدم؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/approve`;
        form.innerHTML = `@csrf`;
        document.body.appendChild(form);
        form.submit();
    }
}

function showRejectModal(userId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const form = document.getElementById('rejectForm');
    form.action = `/admin/users/${userId}/reject`;
    modal.show();
}
</script>
@endpush 