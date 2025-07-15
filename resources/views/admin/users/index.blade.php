@extends('layouts.app')

@section('title', 'إدارة المستخدمين - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-primary mb-1">إدارة المستخدمين</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة المستخدمين</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>إضافة مستخدم جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h4 class="text-primary mb-1">{{ $users->total() }}</h4>
                    <small class="text-muted">إجمالي المستخدمين</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-2x text-success mb-2"></i>
                    <h4 class="text-success mb-1">{{ $users->where('roles.0.name', 'department')->count() }}</h4>
                    <small class="text-muted">الشركات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-tie fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning mb-1">{{ $users->where('roles.0.name', 'employee')->count() }}</h4>
                    <small class="text-muted">الموظفون</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-crown fa-2x text-info mb-2"></i>
                    <h4 class="text-info mb-1">{{ $users->where('roles.0.name', 'admin')->count() }}</h4>
                    <small class="text-muted">المديرون</small>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة المستخدمين -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>قائمة المستخدمين
            </h5>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>المستخدم</th>
                                <th>الدور</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge {{ $role->name == 'admin' ? 'bg-danger' : ($role->name == 'department' ? 'bg-success' : 'bg-warning') }}">
                                                {{ $role->name == 'admin' ? 'مدير' : ($role->name == 'department' ? 'قسم' : 'موظف') }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير مفعل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('Y/m/d') }}
                                            <br>
                                            {{ $user->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $user->email_verified_at ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $user->email_verified_at ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            @if(!$user->hasRole('admin') || \App\Models\User::role('admin')->count() > 1)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" 
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
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
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا يوجد مستخدمون</h5>
                    <p class="text-muted">لم يتم تسجيل أي مستخدمين بعد</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>إضافة أول مستخدم
                    </a>
                </div>
            @endif
        </div>
        
        @if($users->hasPages())
            <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
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
                        <i class="fas fa-lightbulb me-2"></i>نصائح مهمة:
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>يمكنك تفعيل/إلغاء تفعيل المستخدمين باستخدام زر التشغيل/الإيقاف</li>
                        <li>لا يمكن حذف المدير الوحيد في النظام</li>
                        <li>عند حذف مستخدم سيتم حذف جميع بياناته المرتبطة</li>
                        <li>المستخدمون غير المفعلين لا يمكنهم تسجيل الدخول</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 