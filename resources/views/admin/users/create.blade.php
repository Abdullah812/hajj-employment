@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد - لوحة تحكم المدير')

@section('content')
<div class="container py-4">
    <!-- الترحيب والعنوان -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-primary mb-1">إضافة مستخدم جديد</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">إدارة المستخدمين</a></li>
                            <li class="breadcrumb-item active">إضافة مستخدم</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
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

    @if($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>يرجى تصحيح الأخطاء التالية:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- نموذج إضافة المستخدم -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>معلومات المستخدم الجديد
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        
                        <!-- المعلومات الأساسية -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="role" class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required onchange="toggleRoleFields()">
                                    <option value="">اختر نوع المستخدم</option>
                                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>موظف</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مدير</option>
                                    <!-- <option value="department">قسم</option> - تم حذف نظام الأقسام -->
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- حقول خاصة بالشركة -->
                        <div id="company-fields" class="row g-3 mt-3" style="display: none;">
                            <div class="col-12">
                                <hr>
                                <h6 class="text-success">
                                    <i class="fas fa-building me-2"></i>معلومات الشركة
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="company_phone" class="form-label">هاتف الشركة</label>
                                <input type="text" class="form-control @error('company_phone') is-invalid @enderror" 
                                       id="company_phone" name="company_phone" value="{{ old('company_phone') }}">
                                @error('company_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="company_address" class="form-label">عنوان الشركة</label>
                                <textarea class="form-control @error('company_address') is-invalid @enderror" 
                                          id="company_address" name="company_address" rows="3">{{ old('company_address') }}</textarea>
                                @error('company_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- حقول خاصة بالقسم -->
                        <div id="department-fields" class="row g-3 mt-3" style="display: none;">
                            <div class="col-12">
                                <h6 class="text-primary">معلومات القسم</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="department_name" class="form-label">اسم القسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('department_name') is-invalid @enderror"
                                       id="department_name" name="department_name" value="{{ old('department_name') }}">
                                @error('department_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="department_phone" class="form-label">هاتف القسم</label>
                                <input type="text" class="form-control @error('department_phone') is-invalid @enderror"
                                       id="department_phone" name="department_phone" value="{{ old('department_phone') }}">
                                @error('department_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="department_address" class="form-label">عنوان القسم</label>
                                <textarea class="form-control @error('department_address') is-invalid @enderror"
                                          id="department_address" name="department_address" rows="3">{{ old('department_address') }}</textarea>
                                @error('department_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- حقول خاصة بالموظف -->
                        <div id="employee-fields" class="row g-3 mt-3" style="display: none;">
                            <div class="col-12">
                                <hr>
                                <h6 class="text-warning">
                                    <i class="fas fa-user-tie me-2"></i>معلومات الموظف
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>إنشاء المستخدم
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- نصائح مهمة -->
    <div class="row mt-4">
        <div class="col-lg-8 mx-auto">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="fas fa-lightbulb me-2"></i>نصائح مهمة:
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li><strong>المدير:</strong> له صلاحيات كاملة على النظام</li>
                        <li><strong>الشركة:</strong> يمكنها نشر الوظائف وإدارة طلبات التوظيف</li>
                        <li><strong>الموظف:</strong> يمكنه التقديم على الوظائف وإدارة ملفه الشخصي</li>
                        <li>سيتم إرسال بيانات تسجيل الدخول للمستخدم عبر البريد الإلكتروني</li>
                        <li>المستخدم الجديد سيكون مفعل تلقائياً</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const departmentFields = document.getElementById('department-fields');
    const employeeFields = document.getElementById('employee-fields');
    
    // إخفاء جميع الحقول أولاً
    departmentFields.style.display = 'none';
    employeeFields.style.display = 'none';
    
    // إظهار الحقول المناسبة حسب نوع المستخدم
    if (role === 'department') {
        departmentFields.style.display = 'flex';
        // جعل حقل اسم القسم مطلوباً
        document.getElementById('department_name').required = true;
    } else if (role === 'employee') {
        employeeFields.style.display = 'flex';
        // إلغاء جعل حقل اسم القسم مطلوباً
        document.getElementById('department_name').required = false;
    }
}

// تشغيل الدالة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    toggleRoleFields();
});
</script>
@endsection 