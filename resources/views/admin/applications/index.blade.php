@extends('layouts.app')

@section('title', 'إدارة طلبات التوظيف - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-secondary mb-1">إدارة طلبات التوظيف</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة طلبات التوظيف</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-paper-plane fa-2x text-secondary mb-2"></i>
                    <h4 class="text-secondary mb-1">{{ $applications->total() }}</h4>
                    <small class="text-muted">إجمالي الطلبات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $applications->where('status', 'pending')->count() }}</h4>
                    <small class="text-muted">قيد المراجعة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $applications->where('status', 'approved')->count() }}</h4>
                    <small class="text-muted">مقبولة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h4 class="text-danger mb-1">{{ $applications->where('status', 'rejected')->count() }}</h4>
                    <small class="text-muted">مرفوضة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة طلبات التوظيف -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-secondary"></i>قائمة جميع طلبات التوظيف
            </h5>
        </div>
        <div class="card-body p-0">
            @if($applications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>المتقدم</th>
                                <th>الوظيفة</th>
                                <th>الشركة</th>
                                <th>الحالة</th>
                                <th>تاريخ التقديم</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-secondary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $application->user->name }}</h6>
                                                <small class="text-muted">{{ $application->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0 small">{{ $application->job->title }}</h6>
                                            <small class="text-muted">{{ $application->job->department }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0 small">{{ $application->job->company->profile->company_name ?? $application->job->company->name }}</h6>
                                            <small class="text-muted">{{ $application->job->company->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $application->status_color }}">
                                            {{ $application->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $application->created_at->format('Y/m/d') }}
                                            <br>
                                            {{ $application->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- زر عرض التفاصيل -->
                                            <button type="button" class="btn btn-outline-info" 
                                                    data-bs-toggle="modal" data-bs-target="#applicationModal{{ $application->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- أزرار تغيير الحالة -->
                                            @if($application->status !== 'approved')
                                                <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-outline-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($application->status !== 'rejected')
                                                <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
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
                    <i class="fas fa-paper-plane fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد طلبات توظيف</h5>
                    <p class="text-muted">لم يتم تقديم أي طلبات توظيف بعد</p>
                </div>
            @endif
        </div>
        
        @if($applications->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal لعرض تفاصيل الطلب -->
    @foreach($applications as $application)
        <div class="modal fade" id="applicationModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تفاصيل طلب التوظيف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">معلومات المتقدم:</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>الاسم:</strong></td><td>{{ $application->user->name }}</td></tr>
                                    <tr><td><strong>البريد:</strong></td><td>{{ $application->user->email }}</td></tr>
                                    @if($application->user->profile && $application->user->profile->phone)
                                        <tr><td><strong>الهاتف:</strong></td><td>{{ $application->user->profile->phone }}</td></tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">معلومات الوظيفة:</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>المسمى:</strong></td><td>{{ $application->job->title }}</td></tr>
                                    <tr><td><strong>القسم:</strong></td><td>{{ $application->job->department }}</td></tr>
                                    <tr><td><strong>الشركة:</strong></td><td>{{ $application->job->company->profile->company_name ?? $application->job->company->name }}</td></tr>
                                </table>
                            </div>
                        </div>
                        
                        @if($application->cover_letter)
                            <div class="mt-3">
                                <h6 class="text-warning">رسالة التقديم:</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $application->cover_letter }}
                                </div>
                            </div>
                        @endif
                        
                        @if($application->notes)
                            <div class="mt-3">
                                <h6 class="text-info">ملاحظات الشركة:</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $application->notes }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>الحالة:</strong> <span class="badge {{ $application->status_color }}">{{ $application->status_text }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>تاريخ التقديم:</strong> {{ $application->created_at->format('Y/m/d H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <form method="POST" action="{{ route('admin.applications.update', $application) }}" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>مقبول</option>
                                <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            </select>
                            <input type="text" name="notes" class="form-control form-control-sm" 
                                   placeholder="ملاحظات (اختياري)" value="{{ $application->notes }}">
                            <button type="submit" class="btn btn-primary btn-sm">حفظ</button>
                        </form>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- نصائح مهمة -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-secondary">
                <div class="card-body">
                    <h6 class="card-title text-secondary">
                        <i class="fas fa-lightbulb me-2"></i>إدارة طلبات التوظيف:
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0 small text-muted">
                                <li><strong>قيد المراجعة:</strong> طلب جديد لم يتم البت فيه</li>
                                <li><strong>مقبول:</strong> تم قبول الطلب من قبل الشركة</li>
                                <li><strong>مرفوض:</strong> تم رفض الطلب من قبل الشركة</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0 small text-muted">
                                <li>يمكنك مراجعة تفاصيل كل طلب بالنقر على زر العين</li>
                                <li>يمكنك تغيير حالة الطلب مباشرة من هذه الصفحة</li>
                                <li>إضافة ملاحظات يساعد في التواصل مع المتقدمين</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 