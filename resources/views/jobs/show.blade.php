@extends('layouts.app')

@section('title', $job->title . ' - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- العودة للوظائف -->
    <div class="mb-4">
        <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>العودة للوظائف
        </a>
    </div>

    <div class="row">
        <!-- تفاصيل الوظيفة -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="h2 text-primary mb-2">{{ $job->title }}</h1>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-building text-muted me-2"></i>
                                <span class="text-muted">{{ optional($job->department)->name ?? 'قسم غير معروف' }}</span>
                            </div>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $job->employment_type_text }}</span>
                    </div>

                    <!-- معلومات أساسية -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span><strong>الموقع:</strong> {{ $job->location }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tags text-muted me-2"></i>
                                <span><strong>القسم:</strong> {{ optional($job->department)->name ?? 'قسم غير معروف' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                <span><strong>الراتب:</strong> {{ $job->salary_range }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar text-muted me-2"></i>
                                <span><strong>آخر موعد للتقديم:</strong> {{ $job->application_deadline->format('Y/m/d') }}</span>
                            </div>
                        </div>
                        @if($job->max_applicants)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users text-muted me-2"></i>
                                <span><strong>عدد المطلوبين:</strong> {{ $job->max_applicants }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-muted me-2"></i>
                                <span><strong>تاريخ النشر:</strong> {{ $job->created_at->format('Y/m/d') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- الوصف -->
                    <div class="mb-4">
                        <h3 class="h5 text-primary mb-3">وصف الوظيفة</h3>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $job->description }}</p>
                        </div>
                    </div>

                    <!-- المتطلبات -->
                    <div class="mb-4">
                        <h3 class="h5 text-primary mb-3">المتطلبات المطلوبة</h3>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($job->requirements)) !!}
                        </div>
                    </div>

                    <!-- المزايا -->
                    @if($job->benefits)
                    <div class="mb-4">
                        <h3 class="h5 text-primary mb-3">المزايا المقدمة</h3>
                        <div class="bg-light p-3 rounded">
                            {!! nl2br(e($job->benefits)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- معلومات إضافية -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">معلومات مهمة:</h6>
                        <ul class="mb-0">
                            <li>سيتم التواصل مع المرشحين المؤهلين خلال 5 أيام عمل</li>
                            <li>يجب تقديم جميع المستندات المطلوبة</li>
                            <li>العمل موسمي خلال فترة الحج</li>
                            <li>سيتم إجراء مقابلة شخصية للمرشحين المقبولين</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- جانب التقديم -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top">
                <div class="card-body p-4">
                    <h3 class="h5 text-center mb-4">التقديم على الوظيفة</h3>
                    
                    @auth
                        @if(auth()->user()->hasRole('employee'))
                            @php
                                $application = \App\Models\JobApplication::where('user_id', auth()->id())
                                    ->where('job_id', $job->id)
                                    ->first();
                            @endphp
                            
                            @if($application)
                                <!-- حالة التقديم -->
                                <div class="alert alert-success text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                    <strong>تم التقديم بنجاح!</strong>
                                    <p class="mb-0">تم تقديم طلبك في {{ $application->applied_at->format('Y/m/d') }}</p>
                                </div>
                                
                                <!-- حالة الطلب -->
                                <div class="text-center">
                                    <span class="badge {{ $application->status_color }} fs-6">
                                        {{ $application->status_text }}
                                    </span>
                                </div>
                                
                                @if($application->notes)
                                    <div class="mt-3">
                                        <h6>ملاحظات من الشركة:</h6>
                                        <div class="bg-light p-2 rounded">
                                            {{ $application->notes }}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- نموذج التقديم -->
                                <form method="POST" action="{{ route('employee.jobs.apply', $job) }}">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="cover_letter" class="form-label">رسالة التقديم <span class="text-muted">(اختيارية)</span></label>
                                        <textarea class="form-control" id="cover_letter" name="cover_letter" rows="4" 
                                                  placeholder="اكتب رسالة قصيرة توضح سبب اهتمامك بهذه الوظيفة..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="terms" required>
                                            <label class="form-check-label" for="terms">
                                                أوافق على <a href="#" class="text-primary">الشروط والأحكام</a>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>قدم الآن
                                    </button>
                                </form>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        تأكد من تحديث <a href="{{ route('employee.profile') }}" class="text-primary">ملفك الشخصي</a> قبل التقديم
                                    </small>
                                </div>
                            @endif
                        @elseif(auth()->user()->hasRole('department'))
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle mb-2 d-block"></i>
                                <strong>لا يمكن للأقسام التقديم على الوظائف</strong>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle mb-2 d-block"></i>
                                <strong>يجب تحديد دورك كموظف للتقديم</strong>
                            </div>
                        @endif
                    @else
                        <!-- غير مسجل الدخول -->
                        <div class="text-center">
                            <i class="fas fa-sign-in-alt fa-3x text-muted mb-3"></i>
                            <h5>سجل دخولك للتقديم</h5>
                            <p class="text-muted mb-4">يجب تسجيل الدخول أولاً للتقديم على هذه الوظيفة</p>
                            
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                            </a>
                            
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>إنشاء حساب جديد
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
            
            <!-- معلومات القسم -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h3 class="h6 mb-3">معلومات القسم</h3>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-building text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ optional($job->department)->name ?? 'قسم غير معروف' }}</h6>
                            <small class="text-muted">{{ optional($job->department)->email ?? 'بريد إلكتروني غير متوفر' }}</small>
                        </div>
                    </div>
                    
                    @if(optional($job->department)->profile && optional($job->department->profile)->department_description)
                        <p class="text-muted small">{{ Str::limit($job->department->profile->department_description, 150) }}</p>
                    @endif
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            عضو منذ {{ optional($job->department)->created_at ? $job->department->created_at->format('Y') : 'غير معروف' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 