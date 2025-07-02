@extends('layouts.app')

@section('title', 'إدارة طلبات التوظيف - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary">إدارة طلبات التوظيف</h1>
                    <p class="text-muted mb-0">مراجعة وإدارة طلبات المتقدمين للوظائف</p>
                </div>
                <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>العودة للوحة التحكم
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- إحصائيات سريعة -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-users text-primary"></i>
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

    <!-- فلترة وبحث -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('company.applications.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="ابحث عن متقدم...">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="job_id" class="form-label">الوظيفة</label>
                        <select class="form-select" id="job_id" name="job_id">
                            <option value="">جميع الوظائف</option>
                            @foreach($jobs as $job)
                                <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                                    {{ $job->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="status" class="form-label">الحالة</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="sort" class="form-label">ترتيب حسب</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>اسم المتقدم</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['search', 'job_id', 'status', 'sort']))
                    <div class="mt-3">
                        <a href="{{ route('company.applications.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> مسح الفلاتر
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- قائمة الطلبات -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>طلبات التوظيف ({{ $applications->total() }})
                </h5>
                
                @if($applications->where('status', 'pending')->count() > 0)
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="bulkAction('approved')">
                            <i class="fas fa-check"></i> قبول المحدد
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="bulkAction('rejected')">
                            <i class="fas fa-times"></i> رفض المحدد
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($applications as $application)
                <div class="border-bottom p-4">
                    <div class="row align-items-center">
                        <!-- معلومات المتقدم -->
                        <div class="col-lg-6">
                            <div class="d-flex align-items-start">
                                @if($application->status == 'pending')
                                    <div class="form-check me-3 mt-1">
                                        <input class="form-check-input application-checkbox" type="checkbox" 
                                               value="{{ $application->id }}" id="app{{ $application->id }}">
                                    </div>
                                @endif
                                
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $application->user->name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-envelope me-1"></i>{{ $application->user->email }}
                                    </p>
                                    @if($application->user->profile && $application->user->profile->phone)
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-phone me-1"></i>{{ $application->user->profile->phone }}
                                        </p>
                                    @endif
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-briefcase me-1"></i>
                                        <a href="{{ route('jobs.show', $application->job) }}" class="text-decoration-none" target="_blank">
                                            {{ $application->job->title }}
                                        </a>
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
                                    {{ $application->applied_at ? $application->applied_at->format('Y/m/d') : $application->created_at->format('Y/m/d') }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $application->applied_at ? $application->applied_at->diffForHumans() : $application->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- الإجراءات -->
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-end gap-1 flex-wrap">
                                <!-- عرض السيرة الذاتية -->
                                @if($application->user->profile && $application->user->profile->cv_path)
                                    <a href="{{ Storage::url($application->user->profile->cv_path) }}" 
                                       target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-file-pdf"></i> CV
                                    </a>
                                @endif
                                
                                <!-- عرض تفاصيل المتقدم -->
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#applicantModal{{ $application->id }}">
                                    <i class="fas fa-eye"></i> التفاصيل
                                </button>
                                
                                @if($application->status == 'pending')
                                    <!-- قبول -->
                                    <form method="POST" action="{{ route('company.applications.update', $application) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> قبول
                                        </button>
                                    </form>
                                    
                                    <!-- رفض -->
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $application->id }}">
                                        <i class="fas fa-times"></i> رفض
                                    </button>
                                @elseif($application->status == 'approved')
                                    <!-- إنشاء عقد -->
                                    @if(!$application->contract)
                                        <form method="POST" action="{{ route('contracts.create', $application) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-file-contract"></i> إنشاء عقد
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('contracts.show', $application->contract) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> عرض العقد
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- رسالة التقديم -->
                    @if($application->cover_letter)
                        <div class="mt-3 pt-3 border-top">
                            <h6 class="small text-primary">رسالة التقديم:</h6>
                            <p class="text-muted small mb-0">{{ Str::limit($application->cover_letter, 200) }}</p>
                        </div>
                    @endif
                    
                    <!-- ملاحظات -->
                    @if($application->notes)
                        <div class="mt-3 pt-3 border-top">
                            <h6 class="small text-info">ملاحظاتكم:</h6>
                            <p class="text-muted small mb-0">{{ $application->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Modal تفاصيل المتقدم -->
                <div class="modal fade" id="applicantModal{{ $application->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">تفاصيل المتقدم: {{ $application->user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <strong>الاسم:</strong> {{ $application->user->name }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>البريد الإلكتروني:</strong> {{ $application->user->email }}
                                    </div>
                                    
                                    @if($application->user->profile)
                                        @if($application->user->profile->phone)
                                            <div class="col-md-6">
                                                <strong>الهاتف:</strong> {{ $application->user->profile->phone }}
                                            </div>
                                        @endif
                                        
                                        @if($application->user->profile->date_of_birth)
                                            <div class="col-md-6">
                                                <strong>تاريخ الميلاد:</strong> {{ $application->user->profile->date_of_birth }}
                                            </div>
                                        @endif
                                        
                                        @if($application->user->profile->address)
                                            <div class="col-12">
                                                <strong>العنوان:</strong> {{ $application->user->profile->address }}
                                            </div>
                                        @endif
                                        
                                        @if($application->user->profile->education)
                                            <div class="col-12">
                                                <strong>التعليم:</strong>
                                                <div class="bg-light p-2 rounded">{{ $application->user->profile->education }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($application->user->profile->experience)
                                            <div class="col-12">
                                                <strong>الخبرة:</strong>
                                                <div class="bg-light p-2 rounded">{{ $application->user->profile->experience }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($application->user->profile->skills)
                                            <div class="col-12">
                                                <strong>المهارات:</strong>
                                                <div class="bg-light p-2 rounded">{{ $application->user->profile->skills }}</div>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($application->cover_letter)
                                        <div class="col-12">
                                            <strong>رسالة التقديم:</strong>
                                            <div class="bg-light p-2 rounded">{{ $application->cover_letter }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                @if($application->user->profile && $application->user->profile->cv_path)
                                    <a href="{{ Storage::url($application->user->profile->cv_path) }}" 
                                       target="_blank" class="btn btn-info">
                                        <i class="fas fa-file-pdf me-1"></i>عرض السيرة الذاتية
                                    </a>
                                @endif
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal رفض الطلب -->
                @if($application->status == 'pending')
                    <div class="modal fade" id="rejectModal{{ $application->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('company.applications.update', $application) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    
                                    <div class="modal-header">
                                        <h5 class="modal-title">رفض طلب {{ $application->user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="notes{{ $application->id }}" class="form-label">سبب الرفض (اختياري)</label>
                                            <textarea class="form-control" id="notes{{ $application->id }}" name="notes" rows="3" 
                                                      placeholder="اكتب سبب الرفض ليتم إرساله للمتقدم"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">رفض الطلب</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">لا توجد طلبات توظيف</h4>
                    <p class="text-muted mb-4">لم يتقدم أحد للوظائف بعد</p>
                    <a href="{{ route('company.jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-briefcase me-2"></i>إدارة الوظائف
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
</div>

<script>
function bulkAction(status) {
    const checkboxes = document.querySelectorAll('.application-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('يرجى تحديد طلب واحد على الأقل');
        return;
    }
    
    const action = status === 'approved' ? 'قبول' : 'رفض';
    if (confirm(`هل أنت متأكد من ${action} ${ids.length} طلب؟`)) {
        // إرسال الطلبات
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('status', status);
        ids.forEach(id => formData.append('applications[]', id));
        
        fetch('{{ route("company.applications.bulk") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                location.reload(); 
            } else {
                alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            }
        })
        .catch(error => {
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
        });
    }
}

// تحديد الكل
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.createElement('button');
    selectAllBtn.type = 'button';
    selectAllBtn.className = 'btn btn-outline-secondary btn-sm me-2';
    selectAllBtn.innerHTML = '<i class="fas fa-check-square"></i> تحديد الكل';
    selectAllBtn.onclick = function() {
        const checkboxes = document.querySelectorAll('.application-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        this.innerHTML = allChecked ? '<i class="fas fa-check-square"></i> تحديد الكل' : '<i class="fas fa-square"></i> إلغاء التحديد';
    };
    
    const bulkActions = document.querySelector('.card-header .d-flex > div');
    if (bulkActions) {
        bulkActions.prepend(selectAllBtn);
    }
    
    // تحسين أداء النوافذ المنبثقة
    const applicationModals = document.querySelectorAll('.modal[id^="applicantModal"], .modal[id^="rejectModal"]');
    
    applicationModals.forEach(function(modal) {
        // تحسين الأداء والحركة
        modal.style.willChange = 'auto';
        
        modal.addEventListener('show.bs.modal', function() {
            // منع التداخل مع تأثيرات أخرى
            document.body.classList.add('modal-opening');
        });
        
        modal.addEventListener('shown.bs.modal', function() {
            document.body.classList.remove('modal-opening');
            document.body.classList.add('modal-open');
        });
        
        modal.addEventListener('hide.bs.modal', function() {
            document.body.classList.add('modal-closing');
        });
        
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open', 'modal-closing');
        });
    });
});
</script>
@endsection 