@extends('layouts.app')

@section('title', 'إدارة العقود')

@section('content')
<div class="container-fluid">
    <!-- رأس الصفحة -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 text-primary mb-1">إدارة العقود</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">العقود</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- خدمة العقود نشطة ومُفعلة -->

    <!-- رسائل النجاح والخطأ -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-file-contract fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary mb-1">{{ $contracts->total() }}</h4>
                    <small class="text-muted">إجمالي العقود</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-signature fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $contracts->where('status', 'signed')->count() }}</h4>
                    <small class="text-muted">العقود الموقعة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $contracts->whereIn('status', ['sent', 'reviewed'])->count() }}</h4>
                    <small class="text-muted">في انتظار التوقيع</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-play-circle fa-2x text-info mb-2"></i>
                    <h4 class="text-info mb-1">{{ $contracts->where('status', 'active')->count() }}</h4>
                    <small class="text-muted">العقود النشطة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة العقود -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>قائمة العقود
                </h5>
                
                <!-- فلترة -->
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="">جميع الحالات</option>
                        <option value="draft">مسودة</option>
                        <option value="sent">مُرسل</option>
                        <option value="reviewed">تم الاطلاع</option>
                        <option value="signed">موقع</option>
                        <option value="active">نشط</option>
                        <option value="completed">مكتمل</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($contracts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>رقم العقد</th>
                                <th>الموظف</th>
                                @if(auth()->user()->hasRole('admin'))
                                    <th>الشركة</th>
                                @endif
                                <th>الوظيفة</th>
                                <th>الراتب</th>
                                <th>المدة</th>
                                <th>الحالة</th>
                                <th>تاريخ الإنشاء</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-file-contract text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $contract->contract_number }}</h6>
                                                <small class="text-muted">{{ $contract->contract_type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $contract->employee_name }}</h6>
                                            <small class="text-muted">{{ $contract->employee->email }}</small>
                                        </div>
                                    </td>
                                    @if(auth()->user()->hasRole('admin'))
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $contract->department->name }}</h6>
                                                <small class="text-muted">{{ $contract->department->email }}</small>
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <span class="fw-bold">{{ $contract->job_description }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ $contract->formatted_salary }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted d-block">من: {{ $contract->start_date->format('Y/m/d') }}</small>
                                            <small class="text-muted d-block">إلى: {{ $contract->end_date->format('Y/m/d') }}</small>
                                            <small class="badge bg-info">{{ $contract->duration_in_days }} يوم</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $contract->status_color }}">
                                            {{ $contract->status_text }}
                                        </span>
                                        @if($contract->is_expired)
                                            <br><small class="text-danger">منتهي الصلاحية</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $contract->created_at->format('Y/m/d') }}
                                            <br>
                                            {{ $contract->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('contracts.show', $contract) }}" 
                                               class="btn btn-outline-primary" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="{{ route('contracts.download.word', $contract) }}" 
                                               class="btn btn-outline-primary" title="تحميل Word">
                                                <i class="fas fa-file-word"></i>
                                            </a>
                                            
                                            @if(auth()->user()->hasRole('department') && $contract->department_id === auth()->id())
                                                @if($contract->status === 'draft')
                                                    <form method="POST" action="{{ route('contracts.send', $contract) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-info" title="إرسال للموظف">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            
                                            @if(auth()->user()->hasRole('employee') && $contract->employee_id === auth()->id() && $contract->can_be_signed)
                                                <a href="{{ route('contracts.sign-page', $contract) }}" 
                                                   class="btn btn-outline-warning" title="توقيع العقد">
                                                    <i class="fas fa-signature"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد عقود</h5>
                    <p class="text-muted">لم يتم إنشاء أي عقود بعد</p>
                </div>
            @endif
        </div>
        
        @if($contracts->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $contracts->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- نصائح مهمة -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="fas fa-lightbulb me-2"></i>معلومات مهمة عن العقود:
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>مسودة:</strong> العقد تم إنشاؤه ولم يتم إرساله للموظف بعد</li>
                        <li><strong>مُرسل:</strong> تم إرسال العقد للموظف وفي انتظار المراجعة</li>
                        <li><strong>تم الاطلاع:</strong> الموظف اطلع على العقد ولم يوقعه بعد</li>
                        <li><strong>موقع:</strong> تم توقيع العقد من قبل الموظف</li>
                        <li><strong>نشط:</strong> العقد ساري المفعول ويتم العمل به</li>
                        <li><strong>مكتمل:</strong> انتهت مدة العقد بنجاح</li>
                        <li><strong>ملغي:</strong> تم إلغاء العقد لسبب ما</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// فلترة العقود حسب الحالة
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const statusBadge = row.querySelector('.badge');
        if (!status || statusBadge.textContent.trim().includes(getStatusText(status))) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

function getStatusText(status) {
    const statusMap = {
        'draft': 'مسودة',
        'sent': 'مُرسل',
        'reviewed': 'تم الاطلاع',
        'signed': 'موقع',
        'active': 'نشط',
        'completed': 'مكتمل',
        'cancelled': 'ملغي'
    };
    return statusMap[status] || status;
}
</script>
@endsection 