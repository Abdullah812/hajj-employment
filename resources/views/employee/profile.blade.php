@extends('layouts.app')

@section('title', 'الملف الشخصي - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-primary">الملف الشخصي</h1>
                    <p class="text-muted mb-0">تحديث معلوماتك الشخصية والمهنية</p>
                </div>
                <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">
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
        <!-- معلومات أساسية -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2 text-primary"></i>المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.profile.update') }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="basic">
                        
                        <div class="row g-3">
                            <!-- الاسم -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
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
                            
                            <!-- تاريخ الميلاد -->
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">تاريخ الميلاد</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="{{ old('date_of_birth', $profile->date_of_birth ?? '') }}">
                            </div>
                            
                            <!-- رقم الهوية -->
                            <div class="col-md-6">
                                <label for="national_id_basic" class="form-label">رقم الهوية الوطنية</label>
                                <input type="text" class="form-control" id="national_id_basic" name="national_id" 
                                       value="{{ old('national_id', $profile->national_id ?? '') }}" placeholder="1xxxxxxxxx">
                            </div>
                            
                            <!-- العنوان -->
                            <div class="col-12">
                                <label for="address" class="form-label">العنوان</label>
                                <textarea class="form-control" id="address" name="address" rows="2" 
                                          placeholder="أدخل عنوانك الكامل">{{ old('address', $profile->address ?? '') }}</textarea>
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

            <!-- المعلومات الإضافية والمطلوبة -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2 text-warning"></i>المعلومات الإضافية المطلوبة
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="additional">
                        
                        <div class="row g-3">
                            <!-- المؤهل -->
                            <div class="col-md-6">
                                <label for="qualification" class="form-label">المؤهل <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="qualification" name="qualification" 
                                       value="{{ old('qualification', $profile->qualification ?? '') }}" 
                                       placeholder="مثل: دبلوم، بكالوريوس، ماجستير">
                            </div>
                            
                            <!-- رقم الايبان -->
                            <div class="col-md-6">
                                <label for="iban_number" class="form-label">رقم الايبان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="iban_number" name="iban_number" 
                                       value="{{ old('iban_number', $profile->iban_number ?? '') }}" 
                                       placeholder="SA0000000000000000000000" maxlength="24">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    يجب أن يبدأ الرقم بـ SA ويتكون من 24 رقم
                                </div>
                            </div>
                            
                            <!-- رقم الهوية -->
                            <div class="col-md-6">
                                <label for="national_id_additional" class="form-label">رقم الهوية الوطنية <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="national_id_additional" name="national_id" 
                                       value="{{ old('national_id', $profile->national_id ?? '') }}" 
                                       placeholder="1xxxxxxxxx" maxlength="10">
                            </div>
                            
                            <!-- الخبرات العلمية -->
                            <div class="col-12">
                                <label for="academic_experience" class="form-label">الخبرات العلمية</label>
                                <textarea class="form-control" id="academic_experience" name="academic_experience" rows="3" 
                                          placeholder="اذكر خبراتك العلمية والأكاديمية والبحثية">{{ old('academic_experience', $profile->academic_experience ?? '') }}</textarea>
                            </div>
                            
                            <!-- ارفاق الايبان -->
                            <div class="col-md-6">
                                <label for="iban_attachment" class="form-label">ارفاق صورة الايبان <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="iban_attachment" name="iban_attachment" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @if($profile && $profile->iban_attachment)
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($profile->iban_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> عرض الملف الحالي
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- ارفاق صورة من العنوان الوطني -->
                            <div class="col-md-6">
                                <label for="national_address_attachment" class="form-label">ارفاق صورة العنوان الوطني <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="national_address_attachment" name="national_address_attachment" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @if($profile && $profile->national_address_attachment)
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($profile->national_address_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> عرض الملف الحالي
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- ارفاق صورة من الهوية -->
                            <div class="col-md-6">
                                <label for="national_id_attachment" class="form-label">ارفاق صورة الهوية <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="national_id_attachment" name="national_id_attachment" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @if($profile && $profile->national_id_attachment)
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($profile->national_id_attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> عرض الملف الحالي
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- شهادة الخبرة -->
                            <div class="col-md-6">
                                <label for="experience_certificate" class="form-label">شهادة الخبرة</label>
                                <input type="file" class="form-control" id="experience_certificate" name="experience_certificate" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                                @if($profile && $profile->experience_certificate)
                                    <div class="mt-2">
                                        <a href="{{ Storage::url($profile->experience_certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> عرض الملف الحالي
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>حفظ المعلومات الإضافية
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- رفع السيرة الذاتية -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-pdf me-2 text-danger"></i>السيرة الذاتية
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.profile.upload-cv') }}" enctype="multipart/form-data">
                        @csrf
                        
                        @if($profile && $profile->cv_path)
                            <div class="alert alert-success mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>تم رفع السيرة الذاتية بنجاح</strong>
                                        <p class="mb-0 mt-1">{{ basename($profile->cv_path) }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ Storage::url($profile->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        <a href="{{ Storage::url($profile->cv_path) }}" download class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-download"></i> تحميل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="cv" class="form-label">
                                {{ $profile && $profile->cv_path ? 'تحديث السيرة الذاتية' : 'رفع السيرة الذاتية' }}
                                <span class="text-muted">(PDF فقط - الحد الأقصى 5 ميجا)</span>
                            </label>
                            <input type="file" class="form-control" id="cv" name="cv" accept=".pdf">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                رفع السيرة الذاتية يزيد من فرص قبولك في الوظائف
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-upload me-2"></i>
                            {{ $profile && $profile->cv_path ? 'تحديث السيرة الذاتية' : 'رفع السيرة الذاتية' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- الجانب الأيمن -->
        <div class="col-lg-4">
            <!-- معاينة الملف الشخصي -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-eye me-2 text-info"></i>معاينة الملف
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                    
                    <!-- إكمال الملف الشخصي -->
                    @php
                        $completionPercentage = 0;
                        // المعلومات الأساسية (40%)
                        $completionPercentage += auth()->user()->name ? 10 : 0;
                        $completionPercentage += auth()->user()->email ? 10 : 0;
                        $completionPercentage += ($profile && $profile->phone) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->address) ? 10 : 0;
                        
                        // المعلومات الإضافية المطلوبة (40%)
                        $completionPercentage += ($profile && $profile->qualification) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->iban_number) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->national_id) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->academic_experience) ? 10 : 0;
                        
                        // المرفقات والوثائق (20%)
                        $completionPercentage += ($profile && $profile->cv_path) ? 5 : 0;
                        $completionPercentage += ($profile && $profile->iban_attachment) ? 5 : 0;
                        $completionPercentage += ($profile && $profile->national_id_attachment) ? 5 : 0;
                        $completionPercentage += ($profile && $profile->national_address_attachment) ? 5 : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">إكمال الملف الشخصي</span>
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
                                أكمل ملفك الشخصي لزيادة فرص القبول
                            </small>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <small>
                                <i class="fas fa-check-circle me-1"></i>
                                ملفك الشخصي مكتمل!
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- نصائح مهمة -->
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
                            <small>املأ جميع الحقول المطلوبة</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>أدخل رقم الايبان بشكل صحيح</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>ارفع سيرة ذاتية محدثة</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>ارفع جميع الوثائق المطلوبة</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>تأكد من صحة رقم الهاتف والهوية</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// التحقق من صحة رقم الايبان
const ibanNumber = document.getElementById('iban_number');
if (ibanNumber) {
    ibanNumber.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        
        // إزالة أي مسافات
        value = value.replace(/\s/g, '');
        
        // التأكد من أن الرقم يبدأ بـ SA
        if (value.length > 0 && !value.startsWith('SA')) {
            value = 'SA' + value.replace(/^SA/, '');
        }
        
        // الحد الأقصى 24 رقم
        if (value.length > 24) {
            value = value.substring(0, 24);
        }
        
        e.target.value = value;
        
        // التحقق من صحة التنسيق
        const isValid = /^SA[0-9]{22}$/.test(value);
        const feedbackElement = document.getElementById('iban-feedback');
        
        if (feedbackElement) {
            feedbackElement.remove();
        }
        
        if (value.length > 0) {
            const feedback = document.createElement('div');
            feedback.id = 'iban-feedback';
            feedback.className = isValid ? 'text-success small' : 'text-danger small';
            feedback.innerHTML = isValid ? 
                '<i class="fas fa-check-circle"></i> رقم الايبان صحيح' : 
                '<i class="fas fa-exclamation-triangle"></i> رقم الايبان غير صحيح';
            
            e.target.parentNode.appendChild(feedback);
        }
    });
}

// التحقق من صحة رقم الهوية - للفورم الأساسي
function validateNationalId(inputElement, feedbackId) {
    let value = inputElement.value.replace(/\D/g, ''); // إزالة أي شيء غير رقم
    
    // الحد الأقصى 10 أرقام
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    
    inputElement.value = value;
    
    // التحقق من صحة التنسيق
    const isValid = /^[12][0-9]{9}$/.test(value);
    const feedbackElement = document.getElementById(feedbackId);
    
    if (feedbackElement) {
        feedbackElement.remove();
    }
    
    if (value.length > 0) {
        const feedback = document.createElement('div');
        feedback.id = feedbackId;
        feedback.className = isValid ? 'text-success small' : 'text-danger small';
        feedback.innerHTML = isValid ? 
            '<i class="fas fa-check-circle"></i> رقم الهوية صحيح' : 
            '<i class="fas fa-exclamation-triangle"></i> رقم الهوية يجب أن يبدأ بـ 1 أو 2 ويتكون من 10 أرقام';
        
        inputElement.parentNode.appendChild(feedback);
    }
}

// التحقق من صحة رقم الهوية - للفورم الأساسي
const nationalIdBasic = document.getElementById('national_id_basic');
if (nationalIdBasic) {
    nationalIdBasic.addEventListener('input', function(e) {
        validateNationalId(e.target, 'national-id-basic-feedback');
    });
}

// التحقق من صحة رقم الهوية - للفورم الإضافي
const nationalIdAdditional = document.getElementById('national_id_additional');
if (nationalIdAdditional) {
    nationalIdAdditional.addEventListener('input', function(e) {
        validateNationalId(e.target, 'national-id-additional-feedback');
    });
}

// معاينة الملفات قبل الرفع
function previewFile(input, previewId) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.innerHTML = `
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-file me-2"></i>
                        <strong>ملف مختار:</strong> ${file.name}
                        <br>
                        <small>الحجم: ${(file.size / 1024 / 1024).toFixed(2)} ميجا</small>
                    </div>
                `;
            }
        };
        reader.readAsDataURL(file);
    }
}

// إضافة معاينة الملفات
const ibanAttachment = document.getElementById('iban_attachment');
if (ibanAttachment) {
    ibanAttachment.addEventListener('change', function() {
        previewFile(this, 'iban-preview');
    });
}

const nationalAddressAttachment = document.getElementById('national_address_attachment');
if (nationalAddressAttachment) {
    nationalAddressAttachment.addEventListener('change', function() {
        previewFile(this, 'address-preview');
    });
}

const nationalIdAttachment = document.getElementById('national_id_attachment');
if (nationalIdAttachment) {
    nationalIdAttachment.addEventListener('change', function() {
        previewFile(this, 'id-preview');
    });
}

const experienceCertificate = document.getElementById('experience_certificate');
if (experienceCertificate) {
    experienceCertificate.addEventListener('change', function() {
        previewFile(this, 'experience-preview');
    });
}

const cvUpload = document.getElementById('cv');
if (cvUpload) {
    cvUpload.addEventListener('change', function() {
        previewFile(this, 'cv-preview');
    });
}

// إضافة عناصر المعاينة
document.addEventListener('DOMContentLoaded', function() {
    // إضافة عناصر المعاينة للملفات
    const attachmentFields = [
        'iban_attachment',
        'national_address_attachment', 
        'national_id_attachment',
        'experience_certificate',
        'cv'
    ];
    
    attachmentFields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            const previewDiv = document.createElement('div');
            previewDiv.id = field.replace(/_/g, '-') + '-preview';
            input.parentNode.appendChild(previewDiv);
        }
    });
});
</script>
@endsection 