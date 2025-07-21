@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-10 col-lg-9">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center py-4">
                    <h4 class="mb-2">إنشاء حساب جديد</h4>
                    <p class="mb-0">انضم إلى شركة مناسك المشاعر</p>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registrationForm">
                        @csrf
                        
                        <!-- شريط التقدم -->
                        <div class="progress mb-4" style="height: 3px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 25%;" id="formProgress"></div>
                        </div>
                        
                        <div class="steps">
                            <!-- الخطوة 1: معلومات الحساب -->
                            <div class="step" id="step1">
                                <h5 class="text-center mb-4">معلومات الحساب</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="role" class="form-label">نوع الحساب <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">اختر نوع الحساب</option>
                                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>موظف</option>
                                        <!-- <option value="department">قسم</option> - تم حذف نظام الأقسام -->
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- الخطوة 2: المعلومات الشخصية -->
                            <div class="step d-none" id="step2">
                                <h5 class="text-center mb-4">المعلومات الشخصية</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="national_id" class="form-label">رقم الهوية <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                                   id="national_id" name="national_id" value="{{ old('national_id') }}" 
                                                   pattern="\d{10}" maxlength="10" required>
                                            @error('national_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">رقم الجوال <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                                   pattern="05\d{8}" maxlength="10" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_of_birth" class="form-label">تاريخ الميلاد <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                                   id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                            @error('date_of_birth')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الخطوة 3: المؤهلات والخبرات -->
                            <div class="step d-none" id="step3">
                                <h5 class="text-center mb-4">المؤهلات والخبرات</h5>
                                <div class="mb-3">
                                    <label for="qualification" class="form-label">المؤهل العلمي <span class="text-danger">*</span></label>
                                    <select class="form-select @error('qualification') is-invalid @enderror" 
                                            id="qualification" name="qualification" required>
                                        <option value="">اختر المؤهل</option>
                                        <option value="ثانوي">ثانوي</option>
                                        <option value="دبلوم">دبلوم</option>
                                        <option value="بكالوريوس">بكالوريوس</option>
                                        <option value="ماجستير">ماجستير</option>
                                        <option value="دكتوراه">دكتوراه</option>
                                    </select>
                                    @error('qualification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="academic_experience" class="form-label">الخبرات الأكاديمية</label>
                                    <textarea class="form-control @error('academic_experience') is-invalid @enderror" 
                                              id="academic_experience" name="academic_experience" rows="3">{{ old('academic_experience') }}</textarea>
                                    @error('academic_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- الخطوة 4: المعلومات البنكية والمرفقات -->
                            <div class="step d-none" id="step4">
                                <h5 class="text-center mb-4">المعلومات البنكية والمرفقات</h5>
                                <div class="mb-3">
                                    <label for="iban_number" class="form-label">رقم الآيبان <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('iban_number') is-invalid @enderror" 
                                           id="iban_number" name="iban_number" value="{{ old('iban_number') }}" 
                                           pattern="SA\d{22}" maxlength="24" required>
                                    <div class="form-text">يجب أن يبدأ برمز SA متبوعاً بـ 22 رقم</div>
                                    @error('iban_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label d-block">المرفقات المطلوبة <span class="text-danger">*</span></label>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="cv_path" class="form-label">السيرة الذاتية</label>
                                                <input type="file" class="form-control @error('cv_path') is-invalid @enderror" 
                                                       id="cv_path" name="cv_path" accept=".pdf,.doc,.docx" required>
                                                @error('cv_path')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="iban_attachment" class="form-label">صورة الآيبان</label>
                                                <input type="file" class="form-control @error('iban_attachment') is-invalid @enderror" 
                                                       id="iban_attachment" name="iban_attachment" accept=".pdf,.jpg,.jpeg,.png" required>
                                                @error('iban_attachment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="national_id_attachment" class="form-label">صورة الهوية</label>
                                                <input type="file" class="form-control @error('national_id_attachment') is-invalid @enderror" 
                                                       id="national_id_attachment" name="national_id_attachment" accept=".pdf,.jpg,.jpeg,.png" required>
                                                @error('national_id_attachment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="national_address_attachment" class="form-label">صورة العنوان الوطني</label>
                                                <input type="file" class="form-control @error('national_address_attachment') is-invalid @enderror" 
                                                       id="national_address_attachment" name="national_address_attachment" accept=".pdf,.jpg,.jpeg,.png" required>
                                                @error('national_address_attachment')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-0">
                                                <label for="experience_certificate" class="form-label">شهادة الخبرة (اختياري)</label>
                                                <input type="file" class="form-control @error('experience_certificate') is-invalid @enderror" 
                                                       id="experience_certificate" name="experience_certificate" accept=".pdf,.jpg,.jpeg,.png">
                                                @error('experience_certificate')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار التنقل -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary d-none" id="prevBtn" onclick="prevStep()">
                                <i class="fas fa-arrow-right me-1"></i> السابق
                            </button>
                            <button type="button" class="btn btn-success" id="nextBtn" onclick="nextStep()">
                                التالي <i class="fas fa-arrow-left ms-1"></i>
                            </button>
                            <button type="submit" class="btn btn-success d-none" id="submitBtn">
                                <i class="fas fa-check me-1"></i> إنشاء الحساب
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-success">تسجيل الدخول</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.step {
    transition: all 0.3s ease;
}
.progress {
    height: 3px;
    background-color: #e9ecef;
}
.progress-bar {
    transition: width 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('formProgress').style.width = progress + '%';
}

function showStep(step) {
    document.querySelectorAll('.step').forEach(s => s.classList.add('d-none'));
    document.getElementById('step' + step).classList.remove('d-none');
    
    // تحديث الأزرار
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    prevBtn.classList.toggle('d-none', step === 1);
    nextBtn.classList.toggle('d-none', step === totalSteps);
    submitBtn.classList.toggle('d-none', step !== totalSteps);
    
    updateProgress();
}

function validateStep(step) {
    const currentStepElement = document.getElementById('step' + step);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

function nextStep() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

// التحقق من صحة رقم الهوية
document.getElementById('national_id').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// التحقق من صحة رقم الجوال
document.getElementById('phone').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (!this.value.startsWith('05')) {
        this.value = '05';
    }
});

// التحقق من صحة رقم الآيبان
document.getElementById('iban_number').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
    if (!this.value.startsWith('SA')) {
        this.value = 'SA';
    }
    this.value = this.value.replace(/[^A-Z0-9]/g, '');
});

// معاينة الملفات
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const maxSize = 5 * 1024 * 1024; // 5 ميجابايت
            if (this.files[0].size > maxSize) {
                alert('حجم الملف كبير جداً. الحد الأقصى هو 5 ميجابايت');
                this.value = '';
            }
        }
    });
});

// تهيئة النموذج
document.addEventListener('DOMContentLoaded', function() {
    showStep(1);
});
</script>
@endpush
@endsection 