@extends('layouts.app')

@section('title', 'عرض العقد - ' . $contract->contract_number)

@section('content')
<div class="container-fluid">
    <!-- رأس الصفحة -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 text-primary mb-1">عقد رقم: {{ $contract->contract_number }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('contracts.index') }}">العقود</a></li>
                            <li class="breadcrumb-item active">{{ $contract->contract_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('contracts.pdf', $contract) }}" class="btn btn-success me-2">
                        <i class="fas fa-download me-2"></i>تحميل PDF
                    </a>
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary">
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

    <div class="row">
        <!-- معلومات العقد الأساسية -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>تفاصيل العقد
                    </h5>
                </div>
                <div class="card-body">
                    <!-- معاينة العقد -->
                    <div class="contract-preview p-4 border" style="background: #f8f9fa; min-height: 600px; font-family: 'Arial', sans-serif;">
                        <!-- رأس العقد -->
                        <div class="text-center mb-4">
                            <h3 class="text-primary mb-3">شركة مناسك المشاعر</h3>
                            <h4 class="mb-3">اتفاقية تقديم خدمات لفترة مؤقته (موسم الحج 1446هـ)</h4>
                        </div>

                        <!-- تاريخ العقد -->
                        <div class="mb-4">
                            <p class="text-end">
                                تم بعون الله وتوفيقه في هذا اليوم <strong>{{ $contract->contract_date->format('Y/m/d') }}</strong> 
                                الموافق <strong>{{ $contract->hijri_date }}</strong> الاتفاق والتراضي بين كل من:
                            </p>
                        </div>

                        <!-- الطرف الأول - الشركة -->
                        <div class="mb-4">
                            <h6 class="text-primary">1- الطرف الأول (الشركة):</h6>
                            <p class="mb-2">
                                <strong>{{ $contract->company_name }}</strong>، ومقرها {{ $contract->company_address }} 
                                سجل تجاري رقم: {{ $contract->company_commercial_register }}
                            </p>
                            <p class="mb-2">
                                بريد إلكتروني: {{ $contract->company_email }}
                            </p>
                            <p class="mb-2">
                                ويمثلها في هذا العقد: <strong>{{ $contract->company_representative_name }}</strong> 
                                بصفته {{ $contract->company_representative_title }}
                            </p>
                            <p class="text-muted">ويشار إليه فيما بعد (بالطرف الأول)</p>
                        </div>

                        <!-- الطرف الثاني - الموظف -->
                        <div class="mb-4">
                            <h6 class="text-success">2- الطرف الثاني (الموظف):</h6>
                            <p class="mb-2">
                                الأستاذ: <strong>{{ $contract->employee_name }}</strong>
                            </p>
                            <p class="mb-2">
                                الجنسية: {{ $contract->employee_nationality }} | 
                                هوية وطنية رقم: {{ $contract->employee_national_id }}
                            </p>
                            <p class="mb-2">
                                جوال: {{ $contract->employee_phone }}
                            </p>
                            <p class="mb-2">
                                رقم الحساب البنكي: {{ $contract->employee_bank_account }} | 
                                اسم البنك: {{ $contract->employee_bank_name }}
                            </p>
                            <p class="text-muted">ويشار إليه فيما بعد (بالطرف الثاني)</p>
                        </div>

                        <!-- موضوع العقد -->
                        <div class="mb-4">
                            <h6 class="text-warning">موضوع الاتفاقية:</h6>
                            <p>
                                اتفق الطرفان على أن يقوم الطرف الثاني بتقديم خدمات 
                                <strong>({{ $contract->job_description }})</strong> 
                                لفترة مؤقتة وبمعدل {{ $contract->working_hours_per_day }} ساعات يومياً.
                            </p>
                        </div>

                        <!-- مدة العقد -->
                        <div class="mb-4">
                            <h6 class="text-info">مدة الاتفاقية:</h6>
                            <p>
                                مدة هذه الاتفاقية تبدأ من <strong>{{ $contract->start_date->format('d/m/Y') }}</strong> 
                                وتنتهي في <strong>{{ $contract->end_date->format('d/m/Y') }}</strong>
                                <br>
                                <small class="text-muted">إجمالي المدة: {{ $contract->duration_in_days }} يوم</small>
                            </p>
                        </div>

                        <!-- الأتعاب -->
                        <div class="mb-4">
                            <h6 class="text-success">الأتعاب:</h6>
                            <p>
                                اتفق الطرفان بأن الأتعاب للأعمال التي تشملها هذه الاتفاقية مبلغ مقطوع وقدره 
                                <strong class="text-success">{{ $contract->formatted_salary }}</strong> 
                                تدفع بنهاية الفترة الكلية.
                            </p>
                        </div>

                        <!-- التوقيعات -->
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="text-center border p-3">
                                    <h6>الطرف الأول</h6>
                                    <p class="mb-2"><strong>{{ $contract->company_name }}</strong></p>
                                    @if($contract->company_signed_at)
                                        <div class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>{{ $contract->company_signature }}</strong>
                                            <br>
                                            <small>{{ $contract->company_signed_at->format('Y/m/d H:i') }}</small>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <p>التوقيع: _______________</p>
                                            <p>التاريخ: _______________</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center border p-3">
                                    <h6>الطرف الثاني</h6>
                                    <p class="mb-2"><strong>{{ $contract->employee_name }}</strong></p>
                                    @if($contract->employee_signed_at)
                                        <div class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            <strong>{{ $contract->employee_signature }}</strong>
                                            <br>
                                            <small>{{ $contract->employee_signed_at->format('Y/m/d H:i') }}</small>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <p>التوقيع: _______________</p>
                                            <p>التاريخ: _______________</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الشريط الجانبي -->
        <div class="col-lg-4">
            <!-- معلومات سريعة -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-info"></i>معلومات سريعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small text-muted">حالة العقد</label>
                            <div>
                                <span class="badge bg-{{ $contract->status_color }} fs-6">
                                    {{ $contract->status_text }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">تاريخ الإنشاء</label>
                            <div class="small">{{ $contract->created_at->format('Y/m/d') }}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">رقم العقد</label>
                            <div class="small">{{ $contract->contract_number }}</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">ساعات العمل</label>
                            <div class="small">{{ $contract->working_hours_per_day }} ساعة/يوم</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">نوع العقد</label>
                            <div class="small">{{ $contract->contract_type }}</div>
                        </div>
                        @if($contract->is_expired)
                            <div class="col-12">
                                <div class="alert alert-warning alert-sm">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    العقد منتهي الصلاحية
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- الإجراءات -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs me-2 text-primary"></i>الإجراءات المتاحة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- إجراءات الشركة -->
                        @if(auth()->user()->hasRole('company') && $contract->company_id === auth()->id())
                            @if($contract->status === 'draft')
                                <form method="POST" action="{{ route('contracts.send', $contract) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fas fa-paper-plane me-2"></i>إرسال للموظف
                                    </button>
                                </form>
                            @endif
                            
                            @if(in_array($contract->status, ['draft', 'sent', 'reviewed']))
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i>إلغاء العقد
                                </button>
                            @endif
                        @endif

                        <!-- إجراءات الموظف -->
                        @if(auth()->user()->hasRole('employee') && $contract->employee_id === auth()->id())
                            @if($contract->can_be_signed && !$contract->employee_signed_at)
                                <a href="{{ route('contracts.sign-page', $contract) }}" class="btn btn-success w-100">
                                    <i class="fas fa-signature me-2"></i>توقيع العقد
                                </a>
                            @endif
                        @endif

                        <!-- إجراءات المدير -->
                        @if(auth()->user()->hasRole('admin'))
                            @if($contract->is_signed && $contract->status !== 'active')
                                <form method="POST" action="{{ route('contracts.status', $contract) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-play me-2"></i>تفعيل العقد
                                    </button>
                                </form>
                            @endif
                            
                            @if($contract->status === 'active')
                                <form method="POST" action="{{ route('contracts.status', $contract) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>إكمال العقد
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- الملاحظات -->
            @if($contract->notes)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-3">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2 text-warning"></i>الملاحظات
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small text-muted">
                            {!! nl2br(e($contract->notes)) !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- مودال إلغاء العقد -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إلغاء العقد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('contracts.cancel', $contract) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        هل أنت متأكد من إلغاء هذا العقد؟ هذا الإجراء لا يمكن التراجع عنه.
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required 
                                  placeholder="يرجى توضيح سبب إلغاء العقد..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 