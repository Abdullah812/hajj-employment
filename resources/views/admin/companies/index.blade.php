@extends('layouts.app')

@section('title', 'إدارة الشركات - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-success mb-1">إدارة الشركات</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة الشركات</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                        <i class="fas fa-building me-2"></i>إضافة شركة جديدة
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
                    <h4 class="text-success mb-1">{{ $companies->total() }}</h4>
                    <small class="text-muted">إجمالي الشركات</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary mb-1">{{ $companies->where('email_verified_at', '!=', null)->count() }}</h4>
                    <small class="text-muted">شركات نشطة</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-pause-circle fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $companies->where('email_verified_at', null)->count() }}</h4>
                    <small class="text-muted">شركات معطلة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الشركات -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-success"></i>قائمة الشركات المسجلة
            </h5>
        </div>
        <div class="card-body p-0">
            @if($companies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>الشركة</th>
                                <th>معلومات التواصل</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-success"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $company->profile->company_name ?? $company->name }}</h6>
                                                <small class="text-muted">{{ $company->name }}</small>
                                                <br>
                                                <small class="text-muted">{{ $company->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($company->profile)
                                            @if($company->profile->company_phone)
                                                <div class="mb-1">
                                                    <i class="fas fa-phone text-muted me-1"></i>
                                                    <small>{{ $company->profile->company_phone }}</small>
                                                </div>
                                            @endif
                                            @if($company->profile->company_address)
                                                <div>
                                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                    <small>{{ Str::limit($company->profile->company_address, 50) }}</small>
                                                </div>
                                            @endif
                                        @else
                                            <small class="text-muted">لم يتم إكمال البيانات</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($company->email_verified_at)
                                            <span class="badge bg-success">نشطة</span>
                                        @else
                                            <span class="badge bg-secondary">معطلة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $company->created_at->format('Y/m/d') }}
                                            <br>
                                            {{ $company->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.edit', $company) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $company) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $company->email_verified_at ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $company->email_verified_at ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="{{ route('admin.users.destroy', $company) }}" class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الشركة؟')">
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
                    <h5 class="text-muted">لا توجد شركات مسجلة</h5>
                    <p class="text-muted">لم يتم تسجيل أي شركات بعد</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                        <i class="fas fa-building me-2"></i>إضافة أول شركة
                    </a>
                </div>
            @endif
        </div>
        
        @if($companies->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $companies->links() }}
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
                        <i class="fas fa-lightbulb me-2"></i>نصائح مهمة للشركات:
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>يمكن للشركات النشطة نشر الوظائف وإدارة طلبات التوظيف</li>
                        <li>الشركات المعطلة لا يمكنها تسجيل الدخول أو نشر وظائف جديدة</li>
                        <li>عند حذف شركة سيتم حذف جميع وظائفها وطلبات التوظيف المرتبطة بها</li>
                        <li>يُنصح بالتأكد من صحة بيانات التواصل قبل تفعيل الشركة</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 