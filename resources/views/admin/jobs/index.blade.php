@extends('layouts.app')

@section('title', 'إدارة الوظائف - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-info mb-1">إدارة الوظائف</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة الوظائف</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('department.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>إضافة وظيفة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-briefcase fa-2x text-info mb-2"></i>
                    <h4 class="text-info mb-1">{{ $jobs->total() }}</h4>
                    <small class="text-muted">إجمالي الوظائف</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $jobs->where('status', 'active')->count() }}</h4>
                    <small class="text-muted">وظائف نشطة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-pause-circle fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $jobs->where('status', 'inactive')->count() }}</h4>
                    <small class="text-muted">وظائف معطلة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x text-secondary mb-2"></i>
                    <h4 class="text-secondary mb-1">{{ $jobs->where('status', 'closed')->count() }}</h4>
                    <small class="text-muted">وظائف مغلقة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الوظائف -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-info"></i>قائمة جميع الوظائف
            </h5>
        </div>
        <div class="card-body p-0">
            @if($jobs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>الوظيفة</th>
                                <th>القسم</th>
                                <th>الطلبات</th>
                                <th>الحالة</th>
                                <th>تاريخ النشر</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="fas fa-briefcase text-info"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $job->title }}</h6>
                                                <small class="text-muted">{{ optional($job->department)->name ?? 'قسم غير معروف' }}</small>
                                                @if($job->salary_min && $job->salary_max)
                                                    <br>
                                                    <small class="text-success">{{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }} ريال</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0 small">{{ optional($job->department)->name ?? 'قسم غير معروف' }}</h6>
                                            @php
                                                $department = optional($job->department);
                                                $email = $department ? optional($department->user)->email : null;
                                            @endphp
                                            <small class="text-muted">{{ $email ?? 'بريد إلكتروني غير متوفر' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span class="badge bg-primary fs-6">{{ $job->applications_count }}</span>
                                            <br>
                                            <small class="text-muted">طلب</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'warning', 
                                                'closed' => 'secondary'
                                            ];
                                            $statusTexts = [
                                                'active' => 'نشطة',
                                                'inactive' => 'معطلة',
                                                'closed' => 'مغلقة'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$job->status] ?? 'secondary' }}">
                                            {{ $statusTexts[$job->status] ?? $job->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $job->created_at->format('Y/m/d') }}
                                            <br>
                                            {{ $job->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-info" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.jobs.toggle-status', $job) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $job->status == 'active' ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $job->status == 'active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الوظيفة؟\nسيتم حذف جميع الطلبات المرتبطة بها.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد وظائف منشورة</h5>
                    <p class="text-muted">لم يتم نشر أي وظائف بعد</p>
                </div>
            @endif
        </div>
        
        @if($jobs->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $jobs->links() }}
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
                        <i class="fas fa-lightbulb me-2"></i>إدارة الوظائف:
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0 small text-muted">
                                <li><strong>نشطة:</strong> الوظيفة متاحة للتقديم</li>
                                <li><strong>معطلة:</strong> الوظيفة غير متاحة مؤقتاً</li>
                                <li><strong>مغلقة:</strong> انتهى التقديم على الوظيفة</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0 small text-muted">
                                <li>يمكنك تفعيل/إيقاف الوظائف باستخدام زر التشغيل</li>
                                <li>عند حذف وظيفة سيتم حذف جميع طلبات التوظيف المرتبطة</li>
                                <li>يمكنك عرض تفاصيل الوظيفة بالنقر على زر العين</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 