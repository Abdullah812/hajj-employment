@extends('layouts.app')

@section('title', 'توقيع العقد - ' . $contract->contract_number)

@section('content')
<div class="container">
    <!-- رأس الصفحة -->
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-4">
                <h2 class="h3 text-primary mb-1">توقيع العقد الإلكتروني</h2>
                <p class="text-muted">عقد رقم: {{ $contract->contract_number }}</p>
            </div>
        </div>
    </div>

    <!-- معاينة العقد -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>يرجى مراجعة العقد بعناية قبل التوقيع
                    </h5>
                </div>
                <div class="card-body">
                    <div class="contract-summary p-4 bg-light rounded">
                        <!-- معلومات أساسية -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">الوظيفة</h6>
                                <p class="fw-bold">{{ $contract->job_description }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">الراتب</h6>
                                <p class="fw-bold text-success">{{ $contract->formatted_salary }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info">تاريخ البدء</h6>
                                <p>{{ $contract->start_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">تاريخ الانتهاء</h6>
                                <p>{{ $contract->end_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">ساعات العمل</h6>
                                <p>{{ $contract->working_hours_per_day }} ساعة يومياً</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">مدة العقد</h6>
                                <p>{{ $contract->duration_in_days }} يوم</p>
                            </div>
                        </div>

                        <!-- ملخص الشروط -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>ملخص الشروط الرئيسية:
                            </h6>
                            <ul class="mb-0">
                                <li>العمل لمدة {{ $contract->working_hours_per_day }} ساعات يومياً</li>
                                <li>الراتب الإجمالي: {{ $contract->formatted_salary }} يدفع في نهاية الفترة</li>
                                <li>الالتزام بحضور جميع الأوقات المطلوبة</li>
                                <li>المحافظة على أسرار الشركة</li>
                                <li>عدم التغيب أو الاعتذار أكثر من مرتين</li>
                                <li>ضرورة التسجيل في نظام أجير</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- نموذج التوقيع -->
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-signature me-2"></i>التوقيع الإلكتروني
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('contracts.sign', $contract) }}">
                        @csrf
                        
                        <!-- تأكيد البيانات -->
                        <div class="mb-4">
                            <h6 class="text-primary">تأكيد البيانات الشخصية:</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">الاسم الكامل</label>
                                    <input type="text" class="form-control" value="{{ $contract->employee_name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم الهوية</label>
                                    <input type="text" class="form-control" value="{{ $contract->employee_national_id }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم الجوال</label>
                                    <input type="text" class="form-control" value="{{ $contract->employee_phone }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الحساب البنكي</label>
                                    <input type="text" class="form-control" value="{{ $contract->employee_bank_account }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- التوقيع -->
                        <div class="mb-4">
                            <label for="signature" class="form-label">
                                التوقيع الإلكتروني <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('signature') is-invalid @enderror" 
                                   id="signature" name="signature" value="{{ old('signature') }}" 
                                   placeholder="اكتب اسمك الكامل كتوقيع إلكتروني" required>
                            @error('signature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                اكتب اسمك الكامل كما هو موجود في الهوية الوطنية
                            </div>
                        </div>

                        <!-- موافقة على الشروط -->
                        <div class="mb-4">
                            <div class="alert alert-warning">
                                <div class="form-check">
                                    <input class="form-check-input @error('agree_terms') is-invalid @enderror" 
                                           type="checkbox" id="agree_terms" name="agree_terms" required>
                                    <label class="form-check-label" for="agree_terms">
                                        <strong>أوافق على جميع شروط وأحكام هذا العقد</strong>
                                    </label>
                                    @error('agree_terms')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- تأكيدات إضافية -->
                        <div class="mb-4">
                            <div class="border p-3 rounded bg-light">
                                <h6 class="text-danger mb-3">تأكيدات مهمة:</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="confirm1" required>
                                    <label class="form-check-label" for="confirm1">
                                        أؤكد أنني لست موظف حكومي
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="confirm2" required>
                                    <label class="form-check-label" for="confirm2">
                                        أوافق على التسجيل في نظام أجير
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="confirm3" required>
                                    <label class="form-check-label" for="confirm3">
                                        أؤكد صحة الحساب البنكي المذكور أعلاه
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm4" required>
                                    <label class="form-check-label" for="confirm4">
                                        أتعهد بالحضور في جميع الأوقات المطلوبة
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات التوقيع -->
                        <div class="mb-4">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <small class="text-muted">تاريخ التوقيع</small>
                                    <div class="fw-bold">{{ now()->format('Y/m/d') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">الوقت</small>
                                    <div class="fw-bold">{{ now()->format('H:i') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">عنوان IP</small>
                                    <div class="fw-bold">{{ request()->ip() }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i>العودة للعقد
                            </a>
                            <button type="submit" class="btn btn-success btn-lg" id="signButton" disabled>
                                <i class="fas fa-signature me-2"></i>توقيع العقد
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- تحذير مهم -->
            <div class="alert alert-danger mt-4">
                <h6 class="alert-heading">
                    <i class="fas fa-exclamation-triangle me-2"></i>تحذير مهم:
                </h6>
                <p class="mb-0">
                    بمجرد توقيع هذا العقد، ستصبح ملزماً قانونياً بجميع الشروط والأحكام المذكورة. 
                    لا يمكن التراجع عن التوقيع بعد تأكيده. يرجى التأكد من قراءة جميع البنود بعناية.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const signButton = document.getElementById('signButton');
    const signatureInput = document.getElementById('signature');

    function checkFormValid() {
        let allChecked = true;
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                allChecked = false;
            }
        });
        
        const hasSignature = signatureInput.value.trim().length >= 3;
        
        signButton.disabled = !(allChecked && hasSignature);
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkFormValid);
    });

    signatureInput.addEventListener('input', checkFormValid);

    // تأكيد إضافي قبل التوقيع
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!confirm('هل أنت متأكد من توقيع هذا العقد؟ لا يمكن التراجع عن هذا الإجراء.')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection 