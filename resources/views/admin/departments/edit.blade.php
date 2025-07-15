@extends('layouts.app')

@section('title', 'تعديل القسم - ' . $department->name)

@section('content')
<div class="container py-5">
    <!-- عنوان الصفحة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">تعديل القسم</h2>
                            <p class="card-text mb-0">تعديل بيانات القسم: {{ $department->name }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <i class="fas fa-edit fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نموذج التعديل -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- اسم القسم -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">اسم القسم</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $department->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="col-md-6">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $department->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- رقم الهاتف -->
                    <div class="col-md-6">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $department->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- العنوان -->
                    <div class="col-md-6">
                        <label for="address" class="form-label">العنوان</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" 
                               id="address" name="address" value="{{ old('address', $department->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نوع النشاط -->
                    <div class="col-md-6">
                        <label for="activity_type" class="form-label">نوع النشاط</label>
                        <input type="text" class="form-control @error('activity_type') is-invalid @enderror" 
                               id="activity_type" name="activity_type" value="{{ old('activity_type', $department->activity_type) }}">
                        @error('activity_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- حجم القسم -->
                    <div class="col-md-6">
                        <label for="department_size" class="form-label">حجم القسم</label>
                        <input type="text" class="form-control @error('department_size') is-invalid @enderror" 
                               id="department_size" name="department_size" value="{{ old('department_size', $department->department_size) }}">
                        @error('department_size')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الموقع الإلكتروني -->
                    <div class="col-md-6">
                        <label for="website" class="form-label">الموقع الإلكتروني</label>
                        <input type="url" class="form-control @error('website') is-invalid @enderror" 
                               id="website" name="website" value="{{ old('website', $department->website) }}">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- رقم التسجيل -->
                    <div class="col-md-6">
                        <label for="registration_number" class="form-label">رقم التسجيل</label>
                        <input type="text" class="form-control @error('registration_number') is-invalid @enderror" 
                               id="registration_number" name="registration_number" value="{{ old('registration_number', $department->registration_number) }}">
                        @error('registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- أزرار التحكم -->
                    <div class="col-12 mt-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
}
</style>
@endsection 