@extends('layouts.app')

@section('title', 'إضافة وظيفة جديدة - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary">إضافة وظيفة جديدة</h1>
                    <p class="text-muted mb-0">أضف وظيفة جديدة لجذب أفضل المواهب</p>
                </div>
                <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>العودة لإدارة الوظائف
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>يرجى تصحيح الأخطاء التالية:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('company.jobs.store') }}">
        @csrf
        
        <div class="row">
            <!-- النموذج الأساسي -->
            <div class="col-lg-8">
                <!-- المعلومات الأساسية -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2 text-primary"></i>المعلومات الأساسية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- عنوان الوظيفة -->
                            <div class="col-12">
                                <label for="title" class="form-label">عنوان الوظيفة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="{{ old('title') }}" required 
                                       placeholder="مثل: مرشد حج - مكة المكرمة">
                            </div>
                            
                            <!-- الموقع -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">الموقع <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="{{ old('location') }}" required 
                                       placeholder="مكة المكرمة - المدينة المنورة">
                            </div>
                            
                            <!-- القسم -->
                            <div class="col-md-6">
                                <label for="department" class="form-label">القسم <span class="text-danger">*</span></label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">اختر القسم</option>
                                    <option value="قسم الإعاشة" {{ old('department') == 'قسم الإعاشة' ? 'selected' : '' }}>قسم الإعاشة</option>
                                    <option value="قسم الإقامة" {{ old('department') == 'قسم الإقامة' ? 'selected' : '' }}>قسم الإقامة</option>
                                    <option value="قسم النقل" {{ old('department') == 'قسم النقل' ? 'selected' : '' }}>قسم النقل</option>
                                    <option value="الإرشاد والسفر" {{ old('department') == 'الإرشاد والسفر' ? 'selected' : '' }}>الإرشاد والسفر</option>
                                    <option value="خدمة العملاء" {{ old('department') == 'خدمة العملاء' ? 'selected' : '' }}>خدمة العملاء</option>
                                    <option value="الإدارة العامة" {{ old('department') == 'الإدارة العامة' ? 'selected' : '' }}>الإدارة العامة</option>
                                    <option value="الأمن والسلامة" {{ old('department') == 'الأمن والسلامة' ? 'selected' : '' }}>الأمن والسلامة</option>
                                    <option value="التقنية والمعلومات" {{ old('department') == 'التقنية والمعلومات' ? 'selected' : '' }}>التقنية والمعلومات</option>
                                </select>
                            </div>
                            
                            <!-- نوع العمل -->
                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">نوع العمل <span class="text-danger">*</span></label>
                                <select class="form-select" id="employment_type" name="employment_type" required>
                                    <option value="">اختر نوع العمل</option>
                                    <option value="full_time" {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                                    <option value="part_time" {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                                    <option value="temporary" {{ old('employment_type') == 'temporary' ? 'selected' : '' }}>مؤقت</option>
                                    <option value="seasonal" {{ old('employment_type') == 'seasonal' ? 'selected' : '' }}>موسمي</option>
                                </select>
                            </div>
                            
                            <!-- موعد انتهاء التقديم -->
                            <div class="col-md-6">
                                <label for="application_deadline" class="form-label">آخر موعد للتقديم <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="application_deadline" name="application_deadline" 
                                       value="{{ old('application_deadline') }}" required 
                                       min="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الراتب والتفاصيل -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill-wave me-2 text-success"></i>الراتب والتفاصيل
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- الراتب الأدنى -->
                            <div class="col-md-6">
                                <label for="salary_min" class="form-label">الراتب الأدنى (ريال)</label>
                                <input type="number" class="form-control" id="salary_min" name="salary_min" 
                                       value="{{ old('salary_min') }}" min="0" step="100"
                                       placeholder="3000">
                            </div>
                            
                            <!-- الراتب الأعلى -->
                            <div class="col-md-6">
                                <label for="salary_max" class="form-label">الراتب الأعلى (ريال)</label>
                                <input type="number" class="form-control" id="salary_max" name="salary_max" 
                                       value="{{ old('salary_max') }}" min="0" step="100"
                                       placeholder="5000">
                            </div>
                            
                            <!-- الحد الأقصى للمتقدمين -->
                            <div class="col-md-6">
                                <label for="max_applicants" class="form-label">الحد الأقصى للمتقدمين</label>
                                <input type="number" class="form-control" id="max_applicants" name="max_applicants" 
                                       value="{{ old('max_applicants') }}" min="1"
                                       placeholder="50">
                                <div class="form-text">اتركه فارغاً إذا لم يكن هناك حد أقصى</div>
                            </div>
                            
                            <!-- حالة الوظيفة -->
                            <div class="col-md-6">
                                <label for="status" class="form-label">حالة الوظيفة</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الأوصاف والمتطلبات -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-text me-2 text-info"></i>الأوصاف والمتطلبات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- وصف الوظيفة -->
                            <div class="col-12">
                                <label for="description" class="form-label">وصف الوظيفة <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required 
                                          placeholder="اكتب وصفاً مفصلاً عن طبيعة العمل والمسؤوليات">{{ old('description') }}</textarea>
                            </div>
                            
                            <!-- المتطلبات -->
                            <div class="col-12">
                                <label for="requirements" class="form-label">المتطلبات المطلوبة <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="5" required 
                                          placeholder="اكتب المتطلبات والمؤهلات المطلوبة، كل متطلب في سطر منفصل">{{ old('requirements') }}</textarea>
                                <div class="form-text">اكتب كل متطلب في سطر منفصل</div>
                            </div>
                            
                            <!-- المزايا -->
                            <div class="col-12">
                                <label for="benefits" class="form-label">المزايا المقدمة</label>
                                <textarea class="form-control" id="benefits" name="benefits" rows="4" 
                                          placeholder="اكتب المزايا والحوافز المقدمة، كل ميزة في سطر منفصل">{{ old('benefits') }}</textarea>
                                <div class="form-text">اكتب كل ميزة في سطر منفصل</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الجانب الأيمن -->
            <div class="col-lg-4">
                <!-- معاينة -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2 text-warning"></i>معاينة الوظيفة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="preview-content">
                            <h6 class="preview-title text-primary">عنوان الوظيفة</h6>
                            <p class="text-muted small preview-company">{{ auth()->user()->name }}</p>
                            <p class="text-muted small preview-location">الموقع</p>
                            <p class="text-muted small preview-department">القسم</p>
                            <p class="text-success fw-bold preview-salary">الراتب</p>
                            <p class="text-muted small preview-deadline">آخر موعد للتقديم</p>
                        </div>
                    </div>
                </div>

                <!-- نصائح -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>نصائح
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                اكتب عنواناً واضحاً ومختصراً
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                حدد المتطلبات بدقة
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                اذكر المزايا لجذب المتقدمين
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                حدد موعداً واقعياً للتقديم
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- أزرار الحفظ -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>نشر الوظيفة
                            </button>
                            <button type="submit" name="status" value="inactive" class="btn btn-outline-secondary">
                                <i class="fas fa-draft2digital me-2"></i>حفظ كمسودة
                            </button>
                            <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // معاينة مباشرة
    function updatePreview() {
        const title = document.getElementById('title').value || 'عنوان الوظيفة';
        const location = document.getElementById('location').value || 'الموقع';
        const department = document.getElementById('department').value || 'القسم';
        const salaryMin = document.getElementById('salary_min').value;
        const salaryMax = document.getElementById('salary_max').value;
        const deadline = document.getElementById('application_deadline').value;
        
        document.querySelector('.preview-title').textContent = title;
        document.querySelector('.preview-location').innerHTML = '<i class="fas fa-map-marker-alt me-1"></i>' + location;
        document.querySelector('.preview-department').innerHTML = '<i class="fas fa-tags me-1"></i>' + department;
        
        let salaryText = 'الراتب';
        if (salaryMin && salaryMax) {
            salaryText = salaryMin + ' - ' + salaryMax + ' ريال';
        } else if (salaryMin) {
            salaryText = 'من ' + salaryMin + ' ريال';
        } else if (salaryMax) {
            salaryText = 'حتى ' + salaryMax + ' ريال';
        }
        document.querySelector('.preview-salary').innerHTML = '<i class="fas fa-money-bill-wave me-1"></i>' + salaryText;
        
        if (deadline) {
            document.querySelector('.preview-deadline').innerHTML = '<i class="fas fa-calendar me-1"></i>ينتهي: ' + deadline;
        }
    }
    
    // ربط الأحداث
    ['title', 'location', 'department', 'salary_min', 'salary_max', 'application_deadline'].forEach(function(field) {
        document.getElementById(field).addEventListener('input', updatePreview);
    });
    
    // التحقق من الراتب
    document.getElementById('salary_max').addEventListener('input', function() {
        const min = parseFloat(document.getElementById('salary_min').value) || 0;
        const max = parseFloat(this.value) || 0;
        
        if (max > 0 && min > 0 && max < min) {
            this.setCustomValidity('الراتب الأعلى يجب أن يكون أكبر من الراتب الأدنى');
        } else {
            this.setCustomValidity('');
        }
    });
    
    updatePreview();
});
</script>
@endsection 