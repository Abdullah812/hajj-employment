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
                                <label for="national_id" class="form-label">رقم الهوية الوطنية</label>
                                <input type="text" class="form-control" id="national_id" name="national_id" 
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

            <!-- المعلومات المهنية -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-briefcase me-2 text-success"></i>المعلومات المهنية
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <!-- التعليم -->
                            <div class="col-12">
                                <label for="education" class="form-label">المؤهل العلمي</label>
                                <textarea class="form-control" id="education" name="education" rows="3" 
                                          placeholder="مثل: بكالوريوس إدارة أعمال - جامعة الملك سعود - 2020">{{ old('education', $profile->education ?? '') }}</textarea>
                            </div>
                            
                            <!-- الخبرة -->
                            <div class="col-12">
                                <label for="experience" class="form-label">الخبرة العملية</label>
                                <textarea class="form-control" id="experience" name="experience" rows="4" 
                                          placeholder="اذكر خبراتك العملية السابقة مع الشركات والمدد الزمنية">{{ old('experience', $profile->experience ?? '') }}</textarea>
                            </div>
                            
                            <!-- المهارات -->
                            <div class="col-12">
                                <label for="skills" class="form-label">المهارات</label>
                                <textarea class="form-control" id="skills" name="skills" rows="3" 
                                          placeholder="مثل: إجادة اللغة الإنجليزية، مهارات الحاسوب، خدمة العملاء، إلخ">{{ old('skills', $profile->skills ?? '') }}</textarea>
                            </div>
                            
                            <!-- نبذة شخصية -->
                            <div class="col-12">
                                <label for="bio" class="form-label">نبذة شخصية</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" 
                                          placeholder="اكتب نبذة مختصرة عن نفسك وأهدافك المهنية">{{ old('bio', $profile->bio ?? '') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>حفظ المعلومات المهنية
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
                        $completionPercentage += auth()->user()->name ? 20 : 0;
                        $completionPercentage += auth()->user()->email ? 20 : 0;
                        $completionPercentage += ($profile && $profile->phone) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->address) ? 10 : 0;
                        $completionPercentage += ($profile && $profile->education) ? 15 : 0;
                        $completionPercentage += ($profile && $profile->experience) ? 15 : 0;
                        $completionPercentage += ($profile && $profile->cv_path) ? 10 : 0;
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
                            <small>اكتب خبراتك بالتفصيل</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>ارفع سيرة ذاتية محدثة</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>اذكر مهاراتك التقنية</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>تأكد من صحة رقم الهاتف</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 