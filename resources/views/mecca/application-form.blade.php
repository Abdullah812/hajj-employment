@extends('layouts.app')

@section('title', 'التقديم للوظيفة - ' . $job->title)

@section('content')
<div class="container py-4">
    <!-- معلومات الوظيفة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">🕋 التقديم للوظيفة: {{ $job->title }}</h3>
                            <p class="mb-0">{{ $job->region_text }} - {{ $job->department->name }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-success fs-6">تقديم مفتوح - بدون تسجيل</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>الوصف:</strong> {{ Str::limit($job->description, 200) }}</p>
                            <p><strong>الموقع:</strong> {{ $job->location }}</p>
                            <p><strong>آخر موعد للتقديم:</strong> {{ $job->application_deadline->format('Y/m/d') }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning">
                                <h6>📋 المطلوب:</h6>
                                <ul class="mb-0 small">
                                    <li>جميع البيانات صحيحة</li>
                                    <li>السيرة الذاتية (PDF)</li>
                                    <li>صورة الهوية الوطنية</li>
                                    <li>صورة شهادة الآيبان</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نموذج التقديم -->
    <form id="applicationForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="job_id" value="{{ $job->id }}">
        
        <!-- شريط التقدم -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">تقدم النموذج</span>
                            <span class="small text-muted" id="progressText">0%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- التبويبات -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-white border-0">
                        <ul class="nav nav-tabs card-header-tabs" id="applicationTabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal">
                                    <i class="fas fa-user me-2"></i>البيانات الشخصية
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact">
                                    <i class="fas fa-phone me-2"></i>بيانات التواصل
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="education-tab" data-bs-toggle="tab" href="#education">
                                    <i class="fas fa-graduation-cap me-2"></i>المؤهلات العلمية
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="experience-tab" data-bs-toggle="tab" href="#experience">
                                    <i class="fas fa-briefcase me-2"></i>الخبرات العملية
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="banking-tab" data-bs-toggle="tab" href="#banking">
                                    <i class="fas fa-university me-2"></i>البيانات البنكية
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="files-tab" data-bs-toggle="tab" href="#files">
                                    <i class="fas fa-paperclip me-2"></i>المرفقات
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body">
                        <div class="tab-content" id="applicationTabContent">
                            
                            <!-- تبويب البيانات الشخصية -->
                            <div class="tab-pane fade show active" id="personal">
                                <h5 class="mb-4">📋 البيانات الشخصية</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الهوية الوطنية <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="national_id" pattern="[0-9]{10}" maxlength="10" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="form-text">10 أرقام بدون مسافات</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">تاريخ الميلاد <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="birth_date" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الجنس <span class="text-danger">*</span></label>
                                        <select class="form-select" name="gender" required>
                                            <option value="">اختر الجنس</option>
                                            <option value="male">ذكر</option>
                                            <option value="female">أنثى</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الجنسية</label>
                                        <input type="text" class="form-control" name="nationality" value="سعودي">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الحالة الاجتماعية</label>
                                        <select class="form-select" name="marital_status">
                                            <option value="">اختر الحالة</option>
                                            <option value="single">أعزب</option>
                                            <option value="married">متزوج</option>
                                            <option value="divorced">مطلق</option>
                                            <option value="widowed">أرمل</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- تبويب بيانات التواصل -->
                            <div class="tab-pane fade" id="contact">
                                <h5 class="mb-4">📞 بيانات التواصل</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الجوال <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="phone" pattern="05[0-9]{8}" maxlength="10" required>
                                        <div class="invalid-feedback"></div>
                                        <div class="form-text">مثال: 0501234567</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم جوال بديل</label>
                                        <input type="tel" class="form-control" name="phone_alt" pattern="05[0-9]{8}" maxlength="10">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">المدينة</label>
                                        <input type="text" class="form-control" name="city" value="مكة المكرمة">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">العنوان الحالي <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="address" rows="3" required placeholder="العنوان التفصيلي..."></textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- تبويب المؤهلات العلمية -->
                            <div class="tab-pane fade" id="education">
                                <h5 class="mb-4">🎓 المؤهلات العلمية</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">المؤهل الأعلى <span class="text-danger">*</span></label>
                                        <select class="form-select" name="qualification" required>
                                            <option value="">اختر المؤهل</option>
                                            <option value="ثانوي">ثانوي</option>
                                            <option value="دبلوم">دبلوم</option>
                                            <option value="بكالوريوس">بكالوريوس</option>
                                            <option value="ماجستير">ماجستير</option>
                                            <option value="دكتوراه">دكتوراه</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">التخصص</label>
                                        <input type="text" class="form-control" name="specialization" placeholder="التخصص الدراسي...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الجامعة أو المعهد</label>
                                        <input type="text" class="form-control" name="university" placeholder="اسم الجامعة أو المعهد...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">سنة التخرج</label>
                                        <input type="number" class="form-control" name="graduation_year" min="1950" max="{{ date('Y') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">المعدل (من 5)</label>
                                        <input type="number" class="form-control" name="gpa" step="0.01" min="0" max="5">
                                    </div>
                                </div>
                            </div>

                            <!-- تبويب الخبرات العملية -->
                            <div class="tab-pane fade" id="experience">
                                <h5 class="mb-4">💼 الخبرات العملية</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">سنوات الخبرة</label>
                                        <select class="form-select" name="experience_years">
                                            <option value="0">بدون خبرة</option>
                                            <option value="1">سنة واحدة</option>
                                            <option value="2">سنتان</option>
                                            <option value="3">3 سنوات</option>
                                            <option value="4">4 سنوات</option>
                                            <option value="5">5 سنوات</option>
                                            <option value="6">6-10 سنوات</option>
                                            <option value="11">أكثر من 10 سنوات</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الوظيفة الحالية</label>
                                        <input type="text" class="form-control" name="current_job" placeholder="المسمى الوظيفي الحالي...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">جهة العمل الحالية</label>
                                        <input type="text" class="form-control" name="current_employer" placeholder="اسم الشركة أو المؤسسة...">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الراتب الحالي (ريال)</label>
                                        <input type="number" class="form-control" name="current_salary" min="0" placeholder="الراتب الشهري...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">ملخص الخبرات</label>
                                        <textarea class="form-control" name="experience_summary" rows="4" placeholder="اكتب ملخص عن خبراتك العملية والمهارات المكتسبة..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- تبويب البيانات البنكية -->
                            <div class="tab-pane fade" id="banking">
                                <h5 class="mb-4">🏦 البيانات البنكية</h5>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">رقم الآيبان <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="iban_number" pattern="SA[0-9]{22}" maxlength="24" required placeholder="SA0000000000000000000000">
                                        <div class="invalid-feedback"></div>
                                        <div class="form-text">يجب أن يبدأ بـ SA ويتكون من 24 رقم</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">اسم البنك <span class="text-danger">*</span></label>
                                        <select class="form-select" name="bank_name" required>
                                            <option value="">اختر البنك</option>
                                            <option value="الراجحي">الراجحي</option>
                                            <option value="الأهلي">الأهلي</option>
                                            <option value="سامبا">سامبا</option>
                                            <option value="ساب">ساب</option>
                                            <option value="الرياض">الرياض</option>
                                            <option value="البلاد">البلاد</option>
                                            <option value="الجزيرة">الجزيرة</option>
                                            <option value="الإنماء">الإنماء</option>
                                            <option value="فرنسي">فرنسي</option>
                                            <option value="أخرى">أخرى</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- تبويب المرفقات -->
                            <div class="tab-pane fade" id="files">
                                <h5 class="mb-4">📎 المرفقات المطلوبة</h5>
                                
                                <!-- المرفقات الأساسية -->
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="upload-card required">
                                            <label class="form-label">السيرة الذاتية <span class="text-danger">*</span></label>
                                            <div class="upload-area" data-target="cv_file">
                                                <i class="fas fa-file-pdf fa-2x text-primary mb-2"></i>
                                                <p class="mb-1">اضغط لرفع الملف</p>
                                                <small class="text-muted">PDF, DOC, DOCX (حد أقصى 5MB)</small>
                                            </div>
                                            <input type="file" class="d-none" name="cv_file" accept=".pdf,.doc,.docx" required>
                                            <div class="file-preview"></div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="upload-card required">
                                            <label class="form-label">صورة الهوية الوطنية <span class="text-danger">*</span></label>
                                            <div class="upload-area" data-target="national_id_file">
                                                <i class="fas fa-id-card fa-2x text-warning mb-2"></i>
                                                <p class="mb-1">اضغط لرفع الصورة</p>
                                                <small class="text-muted">PDF, JPG, PNG (حد أقصى 2MB)</small>
                                            </div>
                                            <input type="file" class="d-none" name="national_id_file" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <div class="file-preview"></div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="upload-card required">
                                            <label class="form-label">صورة شهادة الآيبان <span class="text-danger">*</span></label>
                                            <div class="upload-area" data-target="iban_file">
                                                <i class="fas fa-university fa-2x text-success mb-2"></i>
                                                <p class="mb-1">اضغط لرفع الصورة</p>
                                                <small class="text-muted">PDF, JPG, PNG (حد أقصى 2MB)</small>
                                            </div>
                                            <input type="file" class="d-none" name="iban_file" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <div class="file-preview"></div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- المرفقات الاختيارية -->
                                <h6 class="mt-4 mb-3">📎 مرفقات اختيارية</h6>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="upload-card">
                                            <label class="form-label">صورة العنوان الوطني</label>
                                            <div class="upload-area" data-target="address_file">
                                                <i class="fas fa-map-marker-alt fa-2x text-info mb-2"></i>
                                                <p class="mb-1">اضغط لرفع الصورة</p>
                                                <small class="text-muted">PDF, JPG, PNG (حد أقصى 2MB)</small>
                                            </div>
                                            <input type="file" class="d-none" name="address_file" accept=".pdf,.jpg,.jpeg,.png">
                                            <div class="file-preview"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="upload-card">
                                            <label class="form-label">صورة الشهادة</label>
                                            <div class="upload-area" data-target="certificate_file">
                                                <i class="fas fa-certificate fa-2x text-primary mb-2"></i>
                                                <p class="mb-1">اضغط لرفع الصورة</p>
                                                <small class="text-muted">PDF, JPG, PNG (حد أقصى 2MB)</small>
                                            </div>
                                            <input type="file" class="d-none" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                                            <div class="file-preview"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="upload-card">
                                            <label class="form-label">الصورة الشخصية</label>
                                            <div class="upload-area" data-target="photo_file">
                                                <i class="fas fa-camera fa-2x text-secondary mb-2"></i>
                                                <p class="mb-1">اضغط لرفع الصورة</p>
                                                <small class="text-muted">JPG, PNG (حد أقصى 1MB)</small>
                                            </div>
                                            <input type="file" class="d-none" name="photo_file" accept=".jpg,.jpeg,.png">
                                            <div class="file-preview"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- معلومات إضافية -->
                                <h6 class="mt-4 mb-3">✍️ معلومات إضافية</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">المهارات</label>
                                        <textarea class="form-control" name="skills" rows="3" placeholder="اذكر مهاراتك المهنية والتقنية..."></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">اللغات</label>
                                        <textarea class="form-control" name="languages" rows="3" placeholder="اللغات التي تجيدها ومستوى الإجادة..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">رسالة تعريفية</label>
                                        <textarea class="form-control" name="cover_letter" rows="4" placeholder="اكتب نبذة عن نفسك ولماذا تريد العمل في هذه الوظيفة..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أزرار التنقل -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeTab(-1)" disabled>
                                <i class="fas fa-arrow-right me-2"></i>السابق
                            </button>
                            
                            <div id="submitSection" style="display: none;">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        أقر بصحة جميع البيانات المدخلة وأوافق على شروط وأحكام التوظيف
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-paper-plane me-2"></i>تقديم الطلب النهائي
                                </button>
                            </div>
                            
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeTab(1)">
                                التالي<i class="fas fa-arrow-left ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.upload-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s ease;
}

.upload-card.required {
    border-color: #ffc107;
    background-color: #fff8e1;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.upload-area:hover {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}

.upload-area.dragover {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}

.file-preview {
    margin-top: 10px;
}

.file-preview .file-item {
    display: flex;
    align-items: center;
    padding: 8px;
    background-color: #e9ecef;
    border-radius: 6px;
    margin-top: 5px;
}

.file-preview .file-item i {
    margin-left: 8px;
}

.file-preview .file-item .remove-file {
    margin-right: auto;
    color: #dc3545;
    cursor: pointer;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #0d6efd;
    background-color: transparent;
}

.progress-bar {
    transition: width 0.5s ease;
}
</style>

<script>
let currentTab = 0;
const tabs = ['personal', 'contact', 'education', 'experience', 'banking', 'files'];

// تهيئة النموذج
document.addEventListener('DOMContentLoaded', function() {
    updateProgress();
    setupFileUploads();
    setupFormValidation();
    
    // تفعيل زر التقديم عند الموافقة على الشروط
    document.getElementById('agreeTerms').addEventListener('change', function() {
        document.getElementById('submitBtn').disabled = !this.checked;
    });
});

// تغيير التبويب
function changeTab(direction) {
    const currentTabElement = document.getElementById(tabs[currentTab] + '-tab');
    const nextTab = currentTab + direction;
    
    if (nextTab >= 0 && nextTab < tabs.length) {
        currentTab = nextTab;
        document.getElementById(tabs[currentTab] + '-tab').click();
        updateNavigationButtons();
        updateProgress();
    }
}

// تحديث أزرار التنقل
function updateNavigationButtons() {
    document.getElementById('prevBtn').disabled = currentTab === 0;
    document.getElementById('nextBtn').style.display = currentTab === tabs.length - 1 ? 'none' : 'block';
    document.getElementById('submitSection').style.display = currentTab === tabs.length - 1 ? 'block' : 'none';
}

// تحديث شريط التقدم
function updateProgress() {
    const progress = ((currentTab + 1) / tabs.length) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('progressText').textContent = Math.round(progress) + '%';
}

// إعداد رفع الملفات
function setupFileUploads() {
    document.querySelectorAll('.upload-area').forEach(area => {
        const input = area.parentElement.querySelector('input[type="file"]');
        const preview = area.parentElement.querySelector('.file-preview');
        
        area.addEventListener('click', () => input.click());
        
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });
        
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            input.files = e.dataTransfer.files;
            updateFilePreview(input, preview);
        });
        
        input.addEventListener('change', () => {
            updateFilePreview(input, preview);
        });
    });
}

// تحديث معاينة الملف
function updateFilePreview(input, preview) {
    preview.innerHTML = '';
    
    if (input.files.length > 0) {
        const file = input.files[0];
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <i class="fas fa-file"></i>
            <span>${file.name}</span>
            <small class="text-muted">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
            <i class="fas fa-times remove-file" onclick="removeFile('${input.name}')"></i>
        `;
        preview.appendChild(fileItem);
    }
}

// حذف الملف
function removeFile(inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    const preview = input.parentElement.querySelector('.file-preview');
    input.value = '';
    preview.innerHTML = '';
}

// إعداد التحقق من النموذج
function setupFormValidation() {
    const form = document.getElementById('applicationForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإرسال...';
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('{{ route("mecca.submit", $job->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessMessage(result);
            } else {
                showErrorMessage(result);
            }
            
        } catch (error) {
            showErrorMessage({
                message: 'حدث خطأ في الشبكة. يرجى المحاولة مرة أخرى.'
            });
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// التحقق من صحة النموذج
function validateForm() {
    const requiredFields = [
        'full_name', 'national_id', 'birth_date', 'gender',
        'phone', 'email', 'address', 'qualification',
        'iban_number', 'bank_name'
    ];
    
    const requiredFiles = ['cv_file', 'national_id_file', 'iban_file'];
    
    let isValid = true;
    
    // التحقق من الحقول المطلوبة
    requiredFields.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            showFieldError(input, 'هذا الحقل مطلوب');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    
    // التحقق من الملفات المطلوبة
    requiredFiles.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input.files.length) {
            showFieldError(input, 'هذا الملف مطلوب');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    
    return isValid;
}

// إظهار خطأ الحقل
function showFieldError(input, message) {
    input.classList.add('is-invalid');
    const feedback = input.parentElement.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

// إزالة خطأ الحقل
function clearFieldError(input) {
    input.classList.remove('is-invalid');
}

// رسالة النجاح
function showSuccessMessage(result) {
    document.body.innerHTML = `
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h2 class="text-success mb-3">تم تقديم طلبك بنجاح!</h2>
                            <p class="lead mb-4">تم استلام طلبك وسيتم مراجعته من قبل الإدارة</p>
                            
                            <div class="alert alert-info">
                                <h5>📋 معلومات مهمة:</h5>
                                <p><strong>رقم المرجع:</strong> ${result.reference_number}</p>
                                <p class="mb-0">احتفظ بهذا الرقم لتتبع حالة طلبك</p>
                            </div>
                            
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="${result.tracking_url}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>تتبع الطلب
                                </a>
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>العودة للوظائف
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// رسالة الخطأ
function showErrorMessage(result) {
    alert(result.message || 'حدث خطأ أثناء تقديم الطلب');
    
    if (result.errors) {
        Object.keys(result.errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                showFieldError(input, result.errors[field][0]);
            }
        });
    }
}

// تفعيل التنقل بالتبويبات
document.querySelectorAll('.nav-link').forEach((tab, index) => {
    tab.addEventListener('click', () => {
        currentTab = index;
        updateNavigationButtons();
        updateProgress();
    });
});
</script>
@endsection 