@extends('layouts.app')

@section('title', 'إدارة الأقسام - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-success mb-1">إدارة الأقسام</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة الأقسام</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                        <i class="fas fa-building me-2"></i>إضافة قسم جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $departments->total() }}</h4>
                    <small class="text-muted">إجمالي الأقسام</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary mb-1">{{ $departments->where('email_verified_at', '!=', null)->count() }}</h4>
                    <small class="text-muted">أقسام نشطة</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-pause-circle fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $departments->where('email_verified_at', null)->count() }}</h4>
                    <small class="text-muted">أقسام معطلة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الأقسام -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-success"></i>قائمة الأقسام المسجلة
            </h5>
        </div>
        <div class="card-body p-0">
            @if($departments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>القسم</th>
                                <th>معلومات التواصل</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $department->name }}</h6>
                                                <small class="text-muted">{{ $department->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($department->profile)
                                            @if($department->profile->department_phone)
                                                <div class="mb-1">
                                                    <i class="fas fa-phone text-muted me-1"></i>
                                                    <small>{{ $department->profile->department_phone }}</small>
                                                </div>
                                            @endif
                                            @if($department->profile->department_address)
                                                <div>
                                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                    <small>{{ Str::limit($department->profile->department_address, 50) }}</small>
                                                </div>
                                            @endif
                                        @else
                                            <small class="text-muted">لم يتم إكمال البيانات</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($department->email_verified_at)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">معطل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $department->created_at->format('Y/m/d') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $department->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.edit', $department->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $department->id) }}" method="POST" class="d-inline">
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
                    <i class="fas fa-building fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد أقسام مسجلة</h5>
                    <p class="text-muted">لم يتم تسجيل أي أقسام بعد</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                        <i class="fas fa-building me-2"></i>إضافة أول قسم
                    </a>
                </div>
            @endif
        </div>
        
        @if($departments->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $departments->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- نصائح مهمة -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="card-title text-success">
                        <i class="fas fa-lightbulb me-2"></i>نصائح مهمة للأقسام:
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>يمكن للأقسام النشطة نشر الوظائف وإدارة طلبات التوظيف</li>
                        <li>الأقسام المعطلة لا يمكنها تسجيل الدخول أو نشر وظائف جديدة</li>
                        <li>عند حذف أقسام سيتم حذف جميع وظائفها وطلبات التوظيف المرتبطة بها</li>
                        <li>يُنصح بالتأكد من صحة بيانات التواصل قبل تفعيل الأقسام</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 