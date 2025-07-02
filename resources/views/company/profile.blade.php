@extends('layouts.app')

@section('title', 'الملف الشخصي للشركة - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary">الملف الشخصي للشركة</h1>
                    <p class="text-muted mb-0">إدارة معلومات شركتك وتحديث بياناتها</p>
                </div>
                <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>العودة للوحة التحكم
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

    <div class="row">
        <!-- النموذج الأساسي -->
        <div class="col-lg-8">
            <!-- المعلومات الأساسية -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building me-2 text-primary"></i>المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('company.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- اسم الشركة -->
                            <div class="col-12">
                                <label for="name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', auth()->user()->name) }}" required>
                            </div>
                            
                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', auth()->user()->email) }}" required>
                            </div>
                            
                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $profile->phone ?? '') }}" placeholder="05xxxxxxxx">
                            </div>
                            
                            <!-- الموقع الإلكتروني -->
                            <div class="col-md-6">
                                <label for="website" class="form-label">الموقع الإلكتروني</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="{{ old('website', $profile->website ?? '') }}" placeholder="https://example.com">
                            </div>
                            
                            <!-- رقم السجل التجاري -->
                            <div class="col-md-6">
                                <label for="commercial_register" class="form-label">رقم السجل التجاري</label>
                                <input type="text" class="form-control" id="commercial_register" name="commercial_register" 
                                       value="{{ old('commercial_register', $profile->commercial_register ?? '') }}" placeholder="1010xxxxxx">
                            </div>
                            
                            <!-- العنوان -->
                            <div class="col-12">
                                <label for="address" class="form-label">العنوان الكامل</label>
                                <textarea class="form-control" id="address" name="address" rows="2" 
                                          placeholder="أدخل عنوان الشركة الكامل">{{ old('address', $profile->address ?? '') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>حفظ المعلومات الأساسية
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- معلومات الشركة -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-success"></i>معلومات الشركة التفصيلية
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('company.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- نوع النشاط -->
                            <div class="col-md-6">
                                <label for="industry" class="form-label">نوع النشاط</label>
                                <select class="form-select" id="industry" name="industry">
                                    <option value="">اختر نوع النشاط</option>
                                    <option value="خدمات الحج والعمرة" {{ old('industry', $profile->industry ?? '') == 'خدمات الحج والعمرة' ? 'selected' : '' }}>خدمات الحج والعمرة</option>
                                    <option value="السياحة الدينية" {{ old('industry', $profile->industry ?? '') == 'السياحة الدينية' ? 'selected' : '' }}>السياحة الدينية</option>
                                    <option value="النقل والمواصلات" {{ old('industry', $profile->industry ?? '') == 'النقل والمواصلات' ? 'selected' : '' }}>النقل والمواصلات</option>
                                    <option value="الإعاشة والضيافة" {{ old('industry', $profile->industry ?? '') == 'الإعاشة والضيافة' ? 'selected' : '' }}>الإعاشة والضيافة</option>
                                    <option value="الإقامة والفنادق" {{ old('industry', $profile->industry ?? '') == 'الإقامة والفنادق' ? 'selected' : '' }}>الإقامة والفنادق</option>
                                    <option value="أخرى" {{ old('industry', $profile->industry ?? '') == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                </select>
                            </div>
                            
                            <!-- عدد الموظفين -->
                            <div class="col-md-6">
                                <label for="company_size" class="form-label">عدد الموظفين</label>
                                <select class="form-select" id="company_size" name="company_size">
                                    <option value="">اختر حجم الشركة</option>
                                    <option value="1-10" {{ old('company_size', $profile->company_size ?? '') == '1-10' ? 'selected' : '' }}>1-10 موظفين</option>
                                    <option value="11-50" {{ old('company_size', $profile->company_size ?? '') == '11-50' ? 'selected' : '' }}>11-50 موظف</option>
                                    <option value="51-200" {{ old('company_size', $profile->company_size ?? '') == '51-200' ? 'selected' : '' }}>51-200 موظف</option>
                                    <option value="201-500" {{ old('company_size', $profile->company_size ?? '') == '201-500' ? 'selected' : '' }}>201-500 موظف</option>
                                    <option value="500+" {{ old('company_size', $profile->company_size ?? '') == '500+' ? 'selected' : '' }}>أكثر من 500 موظف</option>
                                </select>
                            </div>
                            
                            <!-- سنة التأسيس -->
                            <div class="col-md-6">
                                <label for="founded_year" class="form-label">سنة التأسيس</label>
                                <input type="number" class="form-control" id="founded_year" name="founded_year" 
                                       value="{{ old('founded_year', $profile->founded_year ?? '') }}" 
                                       min="1900" max="{{ date('Y') }}" placeholder="1984">
                            </div>
                            
                            <!-- رقم الترخيص -->
                            <div class="col-md-6">
                                <label for="license_number" class="form-label">رقم الترخيص</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" 
                                       value="{{ old('license_number', $profile->license_number ?? '') }}" 
                                       placeholder="رقم ترخيص وزارة الحج والعمرة">
                            </div>
                            
                            <!-- نبذة عن الشركة -->
                            <div class="col-12">
                                <label for="description" class="form-label">نبذة عن الشركة</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="اكتب نبذة مختصرة عن الشركة وخدماتها">{{ old('description', $profile->description ?? '') }}</textarea>
                            </div>
                            
                            <!-- الخدمات المقدمة -->
                            <div class="col-12">
                                <label for="services" class="form-label">الخدمات المقدمة</label>
                                <textarea class="form-control" id="services" name="services" rows="3" 
                                          placeholder="اذكر الخدمات التي تقدمها الشركة، كل خدمة في سطر منفصل">{{ old('services', $profile->services ?? '') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>حفظ معلومات الشركة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- معلومات الاتصال -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-address-book me-2 text-info"></i>معلومات الاتصال والتواصل
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('company.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- جهة الاتصال -->
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">جهة الاتصال</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       value="{{ old('contact_person', $profile->contact_person ?? '') }}" 
                                       placeholder="اسم مسؤول التوظيف">
                            </div>
                            
                            <!-- هاتف جهة الاتصال -->
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label">هاتف جهة الاتصال</label>
                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                       value="{{ old('contact_phone', $profile->contact_phone ?? '') }}" placeholder="05xxxxxxxx">
                            </div>
                            
                            <!-- بريد إلكتروني للتوظيف -->
                            <div class="col-md-6">
                                <label for="hr_email" class="form-label">بريد الموارد البشرية</label>
                                <input type="email" class="form-control" id="hr_email" name="hr_email" 
                                       value="{{ old('hr_email', $profile->hr_email ?? '') }}" placeholder="hr@company.com">
                            </div>
                            
                            <!-- فاكس -->
                            <div class="col-md-6">
                                <label for="fax" class="form-label">رقم الفاكس</label>
                                <input type="tel" class="form-control" id="fax" name="fax" 
                                       value="{{ old('fax', $profile->fax ?? '') }}" placeholder="011xxxxxxx">
                            </div>
                            
                            <!-- LinkedIn -->
                            <div class="col-md-6">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                       value="{{ old('linkedin', $profile->linkedin ?? '') }}" placeholder="https://linkedin.com/company/...">
                            </div>
                            
                            <!-- Twitter -->
                            <div class="col-md-6">
                                <label for="twitter" class="form-label">Twitter</label>
                                <input type="url" class="form-control" id="twitter" name="twitter" 
                                       value="{{ old('twitter', $profile->twitter ?? '') }}" placeholder="https://twitter.com/...">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>حفظ معلومات الاتصال
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- الجانب الأيمن -->
        <div class="col-lg-4">
            <!-- معاينة ملف الشركة -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-eye me-2 text-warning"></i>معاينة ملف الشركة
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-building fa-2x text-primary"></i>
                    </div>
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                    
                    <!-- إكمال الملف -->
                    @php
                        $completionPercentage = 0;
                        $completionPercentage += auth()->user()->name ? 15 : 0;
                        $completionPercentage += auth()->user()->email ? 15 : 0;
                        $completionPercentage += ($profile && $profile->phone) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->address) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->description) ? 15 : 0;
                        $completionPercentage += ($profile && $profile->industry) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->services) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->contact_person) ? 5 : 0;
                        $completionPercentage += ($profile && $profile->hr_email) ? 5 : 0;
                        $completionPercentage += ($profile && $profile->commercial_register) ? 5 : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">اكتمال الملف</span>
                            <span class="small">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $completionPercentage }}%;" 
                                 aria-valuenow="{{ $completionPercentage }}" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    @if($completionPercentage < 100)
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                أكمل ملف شركتك لجذب أفضل المواهب
                            </small>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <small>
                                <i class="fas fa-check-circle me-1"></i>
                                ملف الشركة مكتمل!
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات الشركة -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-success"></i>إحصائيات شركتك
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="text-primary">{{ $stats['jobs'] ?? 0 }}</h4>
                            <small class="text-muted">الوظائف المنشورة</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success">{{ $stats['active_jobs'] ?? 0 }}</h4>
                            <small class="text-muted">وظائف نشطة</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $stats['applications'] ?? 0 }}</h4>
                            <small class="text-muted">إجمالي الطلبات</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">{{ $stats['approved'] ?? 0 }}</h4>
                            <small class="text-muted">طلبات مقبولة</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- نصائح -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>نصائح مهمة
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>أكمل جميع معلومات الشركة</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>اكتب نبذة جذابة عن الشركة</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>حدث معلومات الاتصال بانتظام</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>اذكر جميع الخدمات المقدمة</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>أضف وسائل التواصل الاجتماعي</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 