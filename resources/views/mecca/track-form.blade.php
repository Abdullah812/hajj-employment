@extends('layouts.app')

@section('title', 'تتبع طلب التوظيف')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- عنوان الصفحة -->
            <div class="text-center mb-5">
                <i class="fas fa-search fa-3x text-primary mb-3"></i>
                <h2 class="mb-2">تتبع طلب التوظيف</h2>
                <p class="text-muted">أدخل رقم المرجع لمعرفة حالة طلبك</p>
            </div>

            <!-- نموذج التتبع -->
            <div class="card border-0 shadow-lg">
                <div class="card-body p-4">
                    <form id="trackingForm">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">رقم المرجع</label>
                                <input type="text" class="form-control form-control-lg" 
                                       id="referenceNumber" 
                                       placeholder="مثال: MC202501001234" 
                                       required>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">
                                    رقم المرجع الذي تم إرساله لك عند تقديم الطلب
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>تتبع الطلب
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- نتائج التتبع -->
            <div id="trackingResults" class="mt-4" style="display: none;">
                <!-- سيتم ملء هذا القسم ديناميكياً -->
            </div>

            <!-- معلومات مساعدة -->
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                            <h6>في انتظار المراجعة</h6>
                            <p class="small text-muted">تم استلام طلبك وهو في قائمة الانتظار للمراجعة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                            <h6>مقبول</h6>
                            <p class="small text-muted">تم قبول طلبك وسيتم التواصل معك قريباً</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle fa-2x text-danger mb-3"></i>
                            <h6>مرفوض</h6>
                            <p class="small text-muted">لم يتم قبول طلبك هذه المرة، يمكنك التقديم مرة أخرى</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('trackingForm');
    const input = document.getElementById('referenceNumber');
    const resultsDiv = document.getElementById('trackingResults');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const referenceNumber = input.value.trim();
        if (!referenceNumber) {
            showInputError('يرجى إدخال رقم المرجع');
            return;
        }

        await trackApplication(referenceNumber);
    });

    async function trackApplication(referenceNumber) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري البحث...';
        
        try {
            const response = await fetch(`{{ url('/mecca/track') }}/${referenceNumber}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showApplicationDetails(result.application);
                clearInputError();
            } else {
                showInputError(result.message);
                hideResults();
            }
            
        } catch (error) {
            showInputError('حدث خطأ في الشبكة. يرجى المحاولة مرة أخرى.');
            hideResults();
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }

    function showApplicationDetails(app) {
        const statusClass = getStatusClass(app.status);
        const statusIcon = getStatusIcon(app.status);
        
        resultsDiv.innerHTML = `
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>تفاصيل الطلب
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- معلومات أساسية -->
                        <div class="col-md-6">
                            <div class="card h-100 border-start border-primary border-4">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>معلومات الطلب
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-4"><strong>رقم المرجع:</strong></div>
                                        <div class="col-8">${app.reference_number}</div>
                                        
                                        <div class="col-4"><strong>اسم المتقدم:</strong></div>
                                        <div class="col-8">${app.applicant_name}</div>
                                        
                                        <div class="col-4"><strong>الوظيفة:</strong></div>
                                        <div class="col-8">${app.job_title}</div>
                                        
                                        <div class="col-4"><strong>تاريخ التقديم:</strong></div>
                                        <div class="col-8">${app.applied_at}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- حالة الطلب -->
                        <div class="col-md-6">
                            <div class="card h-100 border-start border-${statusClass} border-4">
                                <div class="card-body">
                                    <h6 class="text-${statusClass} mb-3">
                                        <i class="${statusIcon} me-2"></i>حالة الطلب
                                    </h6>
                                    <div class="text-center">
                                        <div class="badge bg-${statusClass} fs-6 mb-3">
                                            ${app.status_text}
                                        </div>
                                        
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-${statusClass}" 
                                                 style="width: ${app.completion_percentage}%"></div>
                                        </div>
                                        <small class="text-muted">اكتمال البيانات: ${app.completion_percentage}%</small>
                                        
                                        ${app.reviewed_at ? `
                                            <div class="mt-3">
                                                <small class="text-muted">تاريخ المراجعة: ${app.reviewed_at}</small>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ملاحظات إضافية -->
                    ${app.review_notes || app.rejection_reason ? `
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-${statusClass === 'success' ? 'info' : 'warning'}">
                                    <h6 class="mb-2">
                                        <i class="fas fa-comment me-2"></i>
                                        ${app.rejection_reason ? 'سبب الرفض' : 'ملاحظات'}:
                                    </h6>
                                    <p class="mb-0">${app.rejection_reason || app.review_notes}</p>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    <!-- خطوات التالية -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">
                                        <i class="fas fa-lightbulb me-2"></i>الخطوات التالية:
                                    </h6>
                                    ${getNextStepsText(app.status)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            آخر تحديث: ${new Date().toLocaleString('ar-SA')}
                        </small>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>طباعة
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        resultsDiv.style.display = 'block';
        resultsDiv.scrollIntoView({ behavior: 'smooth' });
    }

    function getStatusClass(status) {
        const classes = {
            'pending': 'warning',
            'approved': 'success',
            'rejected': 'danger',
            'withdrawn': 'secondary'
        };
        return classes[status] || 'primary';
    }

    function getStatusIcon(status) {
        const icons = {
            'pending': 'fas fa-clock',
            'approved': 'fas fa-check-circle',
            'rejected': 'fas fa-times-circle',
            'withdrawn': 'fas fa-ban'
        };
        return icons[status] || 'fas fa-file';
    }

    function getNextStepsText(status) {
        const texts = {
            'pending': `
                <ul class="mb-0">
                    <li>سيتم مراجعة طلبك من قبل فريق الموارد البشرية</li>
                    <li>قد تستغرق عملية المراجعة من 3-7 أيام عمل</li>
                    <li>سيتم إشعارك بالنتيجة عبر الجوال أو البريد الإلكتروني</li>
                    <li>تأكد من صحة بيانات التواصل المدخلة</li>
                </ul>
            `,
            'approved': `
                <ul class="mb-0">
                    <li>تهانينا! تم قبول طلبك</li>
                    <li>سيتم التواصل معك خلال 48 ساعة لتحديد موعد المقابلة</li>
                    <li>تأكد من إبقاء هاتفك مفتوحاً لاستقبال المكالمات</li>
                    <li>احتفظ برقم المرجع للمراجعة</li>
                </ul>
            `,
            'rejected': `
                <ul class="mb-0">
                    <li>نشكرك لاهتمامك بالعمل معنا</li>
                    <li>يمكنك التقديم على وظائف أخرى متاحة</li>
                    <li>تأكد من مطابقة مؤهلاتك مع متطلبات الوظيفة</li>
                    <li>نتطلع لاستقبال طلبك في المستقبل</li>
                </ul>
            `,
            'withdrawn': `
                <ul class="mb-0">
                    <li>تم سحب طلبك كما طلبت</li>
                    <li>يمكنك التقديم مرة أخرى في أي وقت</li>
                    <li>نتطلع لاستقبال طلبك في المستقبل</li>
                </ul>
            `
        };
        return texts[status] || '<p class="mb-0">لا توجد خطوات إضافية حالياً.</p>';
    }

    function showInputError(message) {
        input.classList.add('is-invalid');
        const feedback = input.parentElement.querySelector('.invalid-feedback');
        feedback.textContent = message;
    }

    function clearInputError() {
        input.classList.remove('is-invalid');
    }

    function hideResults() {
        resultsDiv.style.display = 'none';
    }
});
</script>

<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.progress-bar {
    transition: width 0.5s ease;
}

.badge {
    padding: 8px 12px;
}

@media print {
    .container {
        max-width: none !important;
    }
    
    .card-footer,
    button {
        display: none !important;
    }
}
</style>
@endsection 