@extends('layouts.app')

@section('title', 'الموظفين المعتمدين - لوحة تحكم المدير')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg hero-card">
                <div class="card-body p-0">
                    <div class="hero-bg d-flex align-items-center justify-content-between p-4">
                <div>
                            <h1 class="display-6 text-white mb-2 fw-bold">
                                <i class="fas fa-users-cog me-3"></i>
                                الموظفين المعتمدين
                            </h1>
                            <p class="text-white-50 mb-0 fs-5">إدارة ومتابعة الموظفين المعتمدين في النظام</p>
                            <nav aria-label="breadcrumb" class="mt-2">
                                <ol class="breadcrumb breadcrumb-dark">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-white-50">لوحة التحكم</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}" class="text-white-50">الطلبات</a></li>
                                    <li class="breadcrumb-item active text-white">الموظفين المعتمدين</li>
                        </ol>
                    </nav>
                </div>
                        <div class="text-end">
                            <div class="action-buttons">
                                <a href="{{ route('admin.applications.index') }}" class="btn btn-light btn-lg shadow-sm me-2">
                                    <i class="fas fa-arrow-left me-2"></i>عودة للطلبات
                                </a>
                                <div class="btn-group">
                                    <a href="{{ route('applications.export', ['status' => 'approved', 'format' => 'xlsx']) }}" 
                                       class="btn btn-success btn-lg shadow-sm">
                                        <i class="fas fa-file-excel me-2"></i>
                        تصدير إكسل
                    </a>
                                    <a href="{{ route('applications.export', ['status' => 'approved', 'format' => 'pdf']) }}" 
                                       class="btn btn-danger btn-lg shadow-sm">
                                        <i class="fas fa-file-pdf me-2"></i>
                        تصدير PDF
                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_approved_users'] }}</div>
                    <div class="stat-label">موظفين معتمدين</div>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['users_with_applications'] }}</div>
                    <div class="stat-label">تقدموا للوظائف</div>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_applications'] }}</div>
                    <div class="stat-label">إجمالي الطلبات</div>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['approved_applications'] }}</div>
                    <div class="stat-label">طلبات مقبولة</div>
                </div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
                                        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3 text-info"></i>
            <div>
                <strong>معلومات مهمة:</strong>
                <p class="mb-0">هذه الصفحة تعرض الموظفين المعتمدين فقط. يمكنك مراجعة تفاصيل كل موظف وطلبات التوظيف الخاصة به.</p>
            </div>
        </div>
    </div>

    <!-- Employees Grid -->
    @if($approvedUsers->count() > 0)
        <div class="row g-4">
            @foreach($approvedUsers as $user)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="employee-card">
                        <!-- Card Header -->
                        <div class="employee-header">
                            <div class="employee-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="employee-info">
                                <h5 class="employee-name">{{ $user->name }}</h5>
                                <p class="employee-email">{{ $user->email }}</p>
                                <span class="employee-badge">
                                    <i class="fas fa-check-circle me-1"></i>
                                    معتمد
                                </span>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="employee-body">
                            @if($user->profile)
                                <div class="employee-details">
                                    <div class="detail-item">
                                        <i class="fas fa-phone text-primary"></i>
                                        <span>{{ $user->profile->phone ?? 'غير محدد' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-calendar text-primary"></i>
                                        <span>{{ $user->profile->date_of_birth ? $user->profile->date_of_birth->format('Y/m/d') : 'غير محدد' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-graduation-cap text-primary"></i>
                                        <span>{{ $user->profile->qualification ?? 'غير محدد' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <span>{{ $user->profile->address ?? 'غير محدد' }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Applications Section -->
                            <div class="applications-section">
                                <div class="applications-header">
                                    <h6>
                                        <i class="fas fa-briefcase me-2"></i>
                                        الوظائف المتقدم لها
                                    </h6>
                                    <span class="applications-count">{{ $user->applications->count() }}</span>
                                </div>
                                
                                @if($user->applications->count() > 0)
                                    <div class="applications-list">
                                        @foreach($user->applications->take(3) as $application)
                                            <div class="application-item">
                                                <div class="application-content">
                                                    <div class="application-title">{{ $application->job->title }}</div>
                                                    <div class="application-department">
                                                        <i class="fas fa-building me-1"></i>
                                                        {{ $application->job->department->name ?? 'غير محدد' }}
                                                    </div>
                                                </div>
                                                <div class="application-status">
                                                    <span class="status-badge status-{{ $application->status }}">
                                                        {{ $application->status_text }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($user->applications->count() > 3)
                                            <div class="more-applications">
                                                <small class="text-muted">
                                                    <i class="fas fa-ellipsis-h me-1"></i>
                                                    و {{ $user->applications->count() - 3 }} طلبات أخرى
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="no-applications">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">لا توجد طلبات</p>
                                    </div>
                                @endif
                                            </div>
                                        </div>
                        
                        <!-- Card Footer -->
                        <div class="employee-footer">
                            <div class="footer-info">
                                        <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $user->approved_at ? $user->approved_at->diffForHumans() : 'غير محدد' }}
                                        </small>
                            </div>
                            <div class="footer-actions">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal{{ $user->id }}">
                                    <i class="fas fa-eye me-1"></i>
                                    عرض التفاصيل
                                            </button>
                            </div>
                                        </div>
                </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($approvedUsers->hasPages())
            <div class="d-flex justify-content-center mt-5">
                <div class="pagination-wrapper">
                    {{ $approvedUsers->links() }}
                </div>
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-users fa-4x"></i>
            </div>
            <h4 class="empty-title">لا يوجد موظفين معتمدين</h4>
            <p class="empty-description">لا توجد حسابات موظفين معتمدة في النظام حالياً</p>
            <a href="{{ route('admin.users.approvals') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus me-2"></i>
                إدارة موافقات المستخدمين
            </a>
            </div>
        @endif
    </div>

<!-- User Details Modals -->
@foreach($approvedUsers as $user)
    <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-user-circle me-2"></i>
                        تفاصيل الموظف: {{ $user->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Basic Info -->
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <h6>المعلومات الأساسية</h6>
                                </div>
                                <div class="info-content">
                                    <div class="info-item">
                                        <span class="info-label">الاسم:</span>
                                        <span class="info-value">{{ $user->name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">البريد الإلكتروني:</span>
                                        <span class="info-value">{{ $user->email }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">تاريخ التسجيل:</span>
                                        <span class="info-value">{{ $user->created_at->format('Y/m/d H:i') }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">تاريخ الاعتماد:</span>
                                        <span class="info-value">{{ $user->approved_at ? $user->approved_at->format('Y/m/d H:i') : 'غير محدد' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">حالة الحساب:</span>
                                        <span class="info-value"><span class="badge bg-success">معتمد</span></span>
                                    </div>
                                </div>
                            </div>
                    </div>

                        <!-- Personal Info -->
                            <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-header">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <h6>المعلومات الشخصية</h6>
                                </div>
                                <div class="info-content">
                                    @if($user->profile)
                                        <div class="info-item">
                                            <span class="info-label">الهاتف:</span>
                                            <span class="info-value">{{ $user->profile->phone ?? 'غير محدد' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">رقم الهوية:</span>
                                            <span class="info-value">{{ $user->profile->national_id ?? 'غير محدد' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">تاريخ الميلاد:</span>
                                            <span class="info-value">{{ $user->profile->date_of_birth ? $user->profile->date_of_birth->format('Y/m/d') : 'غير محدد' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">العنوان:</span>
                                            <span class="info-value">{{ $user->profile->address ?? 'غير محدد' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">المؤهل:</span>
                                            <span class="info-value">{{ $user->profile->qualification ?? 'غير محدد' }}</span>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            لا توجد معلومات شخصية
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    @if($user->profile)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-header">
                                    <i class="fas fa-paperclip me-2"></i>
                                    <h6>المرفقات</h6>
                                </div>
                                <div class="info-content">
                                    <div class="attachments-grid">
                                        @if($user->profile->cv_path)
                                            @if($user->profile->cv_url)
                                                <a href="{{ $user->profile->cv_url }}" target="_blank" class="attachment-item">
                                            <i class="fas fa-file-pdf"></i>
                                            <span>السيرة الذاتية</span>
                                        </a>
                                            @else
                                                <span class="attachment-item disabled">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <span>السيرة الذاتية (غير متاح)</span>
                                                </span>
                                            @endif
                                        @endif
                                        @if($user->profile->national_id_attachment)
                                            @if($user->profile->national_id_attachment_url)
                                                <a href="{{ $user->profile->national_id_attachment_url }}" target="_blank" class="attachment-item">
                                            <i class="fas fa-id-card"></i>
                                            <span>صورة الهوية</span>
                                        </a>
                                            @else
                                                <span class="attachment-item disabled">
                                                    <i class="fas fa-id-card"></i>
                                                    <span>صورة الهوية (غير متاح)</span>
                                                </span>
                                            @endif
                                        @endif
                                        @if($user->profile->iban_attachment)
                                            @if($user->profile->iban_attachment_url)
                                                <a href="{{ $user->profile->iban_attachment_url }}" target="_blank" class="attachment-item">
                                            <i class="fas fa-university"></i>
                                            <span>صورة الآيبان</span>
                                        </a>
                                            @else
                                                <span class="attachment-item disabled">
                                                    <i class="fas fa-university"></i>
                                                    <span>صورة الآيبان (غير متاح)</span>
                                                </span>
                                            @endif
                                        @endif
                                        @if($user->profile->experience_certificate)
                                            @if($user->profile->experience_certificate_url)
                                                <a href="{{ $user->profile->experience_certificate_url }}" target="_blank" class="attachment-item">
                                            <i class="fas fa-certificate"></i>
                                            <span>شهادة الخبرة</span>
                                        </a>
                                            @else
                                                <span class="attachment-item disabled">
                                                    <i class="fas fa-certificate"></i>
                                                    <span>شهادة الخبرة (غير متاح)</span>
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Applications Details -->
                    @if($user->applications->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="info-card">
                                <div class="info-header">
                                    <i class="fas fa-briefcase me-2"></i>
                                    <h6>طلبات التوظيف ({{ $user->applications->count() }})</h6>
                                </div>
                                <div class="info-content">
                                    <div class="applications-table">
                                        @foreach($user->applications as $application)
                                        <div class="application-row">
                                            <div class="application-info">
                                                <div class="application-job">{{ $application->job->title }}</div>
                                                <div class="application-details">
                                                    <span class="application-department">
                                                        <i class="fas fa-building me-1"></i>
                                                        {{ $application->job->department->name ?? 'غير محدد' }}
                                                    </span>
                                                    <span class="application-date">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $application->created_at->format('Y/m/d') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="application-status">
                                                <span class="status-badge status-{{ $application->status }}">
                                                    {{ $application->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        إغلاق
                    </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

<style>
/* Hero Section */
.hero-card {
    overflow: hidden;
    border-radius: 15px;
}

.hero-bg {
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
    position: relative;
}

.hero-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-bg > * {
    position: relative;
    z-index: 1;
}

.breadcrumb-dark .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
}

.breadcrumb-dark .breadcrumb-item a:hover {
    color: rgba(255, 255, 255, 1);
}

.action-buttons .btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
}

/* Statistics Cards */
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--stat-color);
    border-radius: 15px 15px 0 0;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.25);
    border-color: rgba(180, 126, 19, 0.3);
}

.stat-card-primary {
    --stat-color: #b47e13;
}

.stat-card-success {
    --stat-color: #be7b06;
}

.stat-card-warning {
    --stat-color: #40260d;
}

.stat-card-info {
    --stat-color: #2a2a00;
}

.stat-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    background: var(--stat-color);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--stat-color);
    line-height: 1;
}

.stat-label {
    font-size: 1.1rem;
    color: #666666;
    margin-top: 5px;
}

.stat-trend {
    color: #be7b06;
    font-size: 1.2rem;
}

/* Employee Cards */
.employee-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.employee-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.25);
    border-color: rgba(180, 126, 19, 0.3);
}

.employee-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    text-align: center;
}

.employee-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 2rem;
    color: white;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.3);
}

.employee-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #111111;
    margin-bottom: 5px;
}

.employee-email {
    color: #666666;
    margin-bottom: 10px;
}

.employee-badge {
    background: rgba(190, 123, 6, 0.1);
    color: #be7b06;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.employee-body {
    padding: 25px;
}

.employee-details {
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item i {
    width: 20px;
    text-align: center;
    color: #b47e13;
}

.applications-section {
    border-top: 2px solid #f8f9fa;
    padding-top: 20px;
}

.applications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.applications-header h6 {
    margin: 0;
    color: #111111;
    font-weight: 600;
}

.applications-count {
    background: #b47e13;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.applications-list {
    max-height: 200px;
    overflow-y: auto;
}

.application-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.application-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.application-content {
    flex: 1;
}

.application-title {
    font-weight: 600;
    color: #111111;
    margin-bottom: 3px;
}

.application-department {
    font-size: 0.85rem;
    color: #666666;
}

.application-status {
    margin-left: 10px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-approved {
    background: rgba(190, 123, 6, 0.1);
    color: #be7b06;
}

.status-pending {
    background: rgba(64, 38, 13, 0.1);
    color: #40260d;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.no-applications {
    text-align: center;
    padding: 30px;
    color: #666666;
}

.more-applications {
    text-align: center;
    padding: 10px;
    border-top: 1px solid #e9ecef;
}

.employee-footer {
    background: #f8f9fa;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-actions .btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.footer-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 100px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
}

.empty-icon {
    color: #dee2e6;
    margin-bottom: 30px;
}

.empty-title {
    color: #666666;
    margin-bottom: 15px;
}

.empty-description {
    color: #adb5bd;
    margin-bottom: 30px;
}

/* Modal Styles */
.modal-header {
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
    border-bottom: none;
}

.info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.info-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}

.info-header h6 {
    margin: 0;
    color: #111111;
    font-weight: 600;
}

.info-content {
    padding: 20px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #111111;
    flex: 0 0 40%;
}

.info-value {
    color: #666666;
    flex: 1;
}

.attachments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.attachment-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #111111;
    transition: all 0.3s ease;
}

.attachment-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    color: #b47e13;
}

.attachment-item i {
    font-size: 1.5rem;
}

.applications-table {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.application-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #b47e13;
}

.application-info {
    flex: 1;
}

.application-job {
    font-weight: 600;
    color: #111111;
    margin-bottom: 8px;
}

.application-details {
    display: flex;
    gap: 20px;
    font-size: 0.9rem;
    color: #666666;
}

.pagination-wrapper {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
    border: 1px solid #e9ecef;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-bg {
        text-align: center;
    }
    
    .hero-bg .d-flex {
        flex-direction: column;
        gap: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 10px;
    }
    
    .stat-card {
        padding: 20px;
        gap: 15px;
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .applications-header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .application-row {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .application-details {
        justify-content: center;
    }
    
    .attachments-grid {
        grid-template-columns: 1fr;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.employee-card {
    animation: fadeInUp 0.6s ease-out;
}

.stat-card {
    animation: fadeInUp 0.4s ease-out;
}

/* Scrollbar Styling */
.applications-list::-webkit-scrollbar {
    width: 6px;
}

.applications-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.applications-list::-webkit-scrollbar-thumb {
    background: #b47e13;
    border-radius: 10px;
}

.applications-list::-webkit-scrollbar-thumb:hover {
    background: #be7b06;
}

/* Utility Classes */
.bg-gradient-primary {
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.5) !important;
}

.fw-bold {
    font-weight: 700 !important;
}

.border-0 {
    border: 0 !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}
</style>
@endsection 