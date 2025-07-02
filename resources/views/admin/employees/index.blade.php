@extends('layouts.app')

@section('title', 'إدارة الموظفين - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-warning mb-1">إدارة الموظفين</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة الموظفين</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-warning">
                        <i class="fas fa-user-plus me-2"></i>إضافة موظف جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-tie fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $employees->total() }}</h4>
                    <small class="text-muted">إجمالي الموظفين</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $employees->where('email_verified_at', '!=', null)->count() }}</h4>
                    <small class="text-muted">موظفون نشطون</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-times fa-2x text-secondary mb-2"></i>
                    <h4 class="text-secondary mb-1">{{ $employees->where('email_verified_at', null)->count() }}</h4>
                    <small class="text-muted">موظفون معطلون</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الموظفين -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-warning"></i>قائمة الموظفين المسجلين
            </h5>
        </div>
        <div class="card-body p-0">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>الموظف</th>
                                <th>معلومات التواصل</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="fas fa-user text-warning"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $employee->name }}</h6>
                                                <small class="text-muted">{{ $employee->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($employee->profile && $employee->profile->phone)
                                            <div class="mb-1">
                                                <i class="fas fa-phone text-muted me-1"></i>
                                                <small>{{ $employee->profile->phone }}</small>
                                            </div>
                                        @endif
                                        @if($employee->profile && $employee->profile->city)
                                            <div>
                                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                <small>{{ $employee->profile->city }}</small>
                                            </div>
                                        @endif
                                        @if(!$employee->profile || (!$employee->profile->phone && !$employee->profile->city))
                                            <small class="text-muted">لم يتم إكمال البيانات</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->email_verified_at)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">معطل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $employee->created_at->format('Y/m/d') }}
                                            <br>
                                            {{ $employee->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.edit', $employee) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $employee) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $employee->email_verified_at ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $employee->email_verified_at ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.users.destroy', $employee) }}" class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')">
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
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا يوجد موظفون مسجلون</h5>
                    <p class="text-muted">لم يتم تسجيل أي موظفين بعد</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-warning">
                        <i class="fas fa-user-plus me-2"></i>إضافة أول موظف
                    </a>
                </div>
            @endif
        </div>
        
        @if($employees->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $employees->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- نصائح مهمة -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="fas fa-lightbulb me-2"></i>نصائح مهمة للموظفين:
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>يمكن للموظفين النشطين تسجيل الدخول والتقديم على الوظائف</li>
                        <li>الموظفون المعطلون لا يمكنهم تسجيل الدخول أو التقديم على وظائف جديدة</li>
                        <li>عند حذف موظف سيتم حذف جميع طلباته وبياناته الشخصية</li>
                        <li>يُنصح بمراجعة الملفات الشخصية للموظفين للتأكد من اكتمال البيانات</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 