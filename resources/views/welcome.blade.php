@extends('layouts.app')

@section('title', 'شركة مناسك المشاعر - التوظيف الموسمي')

@section('content')
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">شركة مناسك المشاعر</h1>
                <h2 class="h3 mb-3">التوظيف الموسمي لخدمات الحجاج</h2>
                <p class="lead mb-4">شركة سعودية متخصصة في تقديم الخدمات الشاملة لحجاج الخارج، وخاصة حجاج الجمهورية الإسلامية الإيرانية ودول أخرى، مع خبرة تمتد منذ عام 1984م</p>
                <div class="d-flex gap-3 flex-wrap">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>التقديم للوظائف
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم
                        </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fas fa-kaaba fa-8x opacity-75 mb-3"></i>
                <p class="small">مرخصة من وزارة الحج والعمرة</p>
            </div>
        </div>
    </div>
</div>

<!-- Company Info Banner -->
<div class="bg-light py-3">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4">
                <i class="fas fa-certificate text-primary me-2"></i>
                <span class="fw-bold">مرخصة رسمياً من وزارة الحج والعمرة</span>
            </div>
            <div class="col-md-4">
                <i class="fas fa-calendar text-success me-2"></i>
                <span class="fw-bold">خبرة منذ عام 1984م</span>
            </div>
            <div class="col-md-4">
                <i class="fas fa-globe text-info me-2"></i>
                <span class="fw-bold">خدمات لحجاج متعددة الجنسيات</span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 mb-3">أرقام تتحدث عن إنجازاتنا</h2>
            <p class="lead">إحصائيات حقيقية تعكس التزامنا بالتميز في خدمة ضيوف الرحمن</p>
        </div>
        
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-transparent border-light text-white h-100">
                    <div class="card-body">
                        <div class="display-4 fw-bold mb-2">
                            <span data-count="40">0</span>+
                        </div>
                        <h5 class="mb-3">عام من الخبرة</h5>
                        <p class="mb-0">منذ 1984م في خدمة الحجاج</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-transparent border-light text-white h-100">
                    <div class="card-body">
                        <div class="display-4 fw-bold mb-2">
                            <span data-count="250000">0</span>+
                        </div>
                        <h5 class="mb-3">حاج خدمناهم</h5>
                        <p class="mb-0">من مختلف دول العالم</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-transparent border-light text-white h-100">
                    <div class="card-body">
                        <div class="display-4 fw-bold mb-2">
                            <span data-count="15">0</span>+
                        </div>
                        <h5 class="mb-3">دولة نخدم حجاجها</h5>
                        <p class="mb-0">انتشار واسع عالمياً</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card bg-transparent border-light text-white h-100">
                    <div class="card-body">
                        <div class="display-4 fw-bold mb-2">
                            <span data-count="98">0</span>%
                        </div>
                        <h5 class="mb-3">نسبة رضا العملاء</h5>
                        <p class="mb-0">تقييمات ممتازة من الحجاج</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">الوظائف المتاحة في خدماتنا</h2>
            <p class="lead text-muted">انضم إلى فريقنا المتخصص في خدمة ضيوف الرحمن</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-utensils fa-2x text-primary"></i>
                        </div>
                        <h4 class="card-title">قسم الإعاشة</h4>
                        <p class="card-text">وظائف في التغذية الكاملة وخدمات الطعام للحجاج أثناء الحج</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary">الوظائف المتاحة</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-bed fa-2x text-success"></i>
                        </div>
                        <h4 class="card-title">قسم الإقامة</h4>
                        <p class="card-text">وظائف في السكن بمكة والمدينة ومشعر منى وعرفات</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-success">الوظائف المتاحة</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-bus fa-2x text-warning"></i>
                        </div>
                        <h4 class="card-title">قسم النقل</h4>
                        <p class="card-text">وظائف في تأمين وسائل التنقل من الوصول حتى العودة</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-warning">الوظائف المتاحة</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-route fa-2x text-info"></i>
                        </div>
                        <h4 class="card-title">الإرشاد والسفر</h4>
                        <p class="card-text">مرشدين ومنسقي الترتيبات اللوجستية مع شركات الطيران</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-info">الوظائف المتاحة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">معرض صور خدماتنا</h2>
            <p class="lead text-muted">صور حقيقية من خدماتنا المتميزة للحجاج</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-utensils fa-4x text-primary mb-3"></i>
                                <h5 class="text-primary">خدمات الإعاشة المتميزة</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">وجبات متنوعة وصحية</h5>
                        <p class="card-text">نقدم أفضل الوجبات المتوازنة التي تناسب جميع الحجاج من مختلف الثقافات</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <div class="bg-success bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-bed fa-4x text-success mb-3"></i>
                                <h5 class="text-success">إقامة مريحة وآمنة</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">سكن قريب من الحرم</h5>
                        <p class="card-text">غرف مكيفة ومجهزة بالكامل في أقرب المسافات من المسجد الحرام</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <div class="bg-warning bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-bus fa-4x text-warning mb-3"></i>
                                <h5 class="text-warning">نقل آمن ومريح</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">حافلات حديثة ومكيفة</h5>
                        <p class="card-text">أسطول من الحافلات الحديثة المجهزة بأحدث تقنيات الراحة والأمان</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <div class="bg-info bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-kaaba fa-4x text-info mb-3"></i>
                                <h5 class="text-info">إرشاد متخصص</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">مرشدين ذوي خبرة</h5>
                        <p class="card-text">فريق من المرشدين المتخصصين لضمان أداء المناسك بأفضل صورة</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <div class="bg-danger bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-medkit fa-4x text-danger mb-3"></i>
                                <h5 class="text-danger">رعاية طبية متكاملة</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">عيادات طبية متنقلة</h5>
                        <p class="card-text">فرق طبية متخصصة متاحة على مدار الساعة لضمان سلامة الحجاج</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fas fa-users fa-4x text-secondary mb-3"></i>
                                <h5 class="text-secondary">خدمة عملاء متميزة</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">دعم متواصل 24/7</h5>
                        <p class="card-text">فريق خدمة عملاء متخصص متاح على مدار الساعة لحل جميع الاستفسارات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Section -->
<div class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">فيديو تعريفي عن الشركة</h2>
            <p class="lead text-muted">تعرف على شركة مناسك المشاعر وخدماتها المتميزة</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        <div class="position-relative" style="height: 400px; background: linear-gradient(45deg, var(--primary-gold), var(--primary-orange));">
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <div class="text-center text-white">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                        <i class="fas fa-play fa-2x"></i>
                                    </div>
                                    <h3 class="mb-3">شاهد رحلتنا في خدمة ضيوف الرحمن</h3>
                                    <p class="lead mb-4">من 1984 حتى اليوم.. قصة نجاح في خدمة الحجاج</p>
                                    <button class="btn btn-light btn-lg px-5" onclick="playVideo()">
                                        <i class="fas fa-play me-2"></i>تشغيل الفيديو
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Video Description -->
                <div class="text-center mt-4">
                    <div class="row">
                        <div class="col-md-4">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <span class="fw-bold">مدة الفيديو: 5 دقائق</span>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-language text-success me-2"></i>
                            <span class="fw-bold">متوفر بعدة لغات</span>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-hd-video text-info me-2"></i>
                            <span class="fw-bold">جودة عالية HD</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Countries Served -->
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">الدول التي نخدم حجاجها</h2>
            <p class="lead text-muted">نقدم خدماتنا المتخصصة لحجاج متعددي الجنسيات</p>
        </div>
        
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-flag fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold">الجمهورية الإسلامية الإيرانية</h5>
                        <p class="text-muted">الشريك الأساسي</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-flag fa-3x text-warning mb-3"></i>
                        <h5 class="fw-bold">جمهورية السنغال</h5>
                        <p class="text-muted">عقود متجددة</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-flag fa-3x text-info mb-3"></i>
                        <h5 class="fw-bold">جمهورية موريشيوس</h5>
                        <p class="text-muted">شراكة استراتيجية</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">دول أخرى</h5>
                        <p class="text-muted">في إطار التوسع</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">شهادات عملائنا</h2>
            <p class="lead text-muted">كلمات من القلب من الحجاج الذين خدمناهم</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-primary mb-3"></i>
                        </div>
                        <p class="card-text fst-italic mb-4">"خدمة استثنائية وتنظيم رائع. لقد كانت رحلة الحج معهم تجربة لا تُنسى. الطعام ممتاز والإقامة مريحة والمرشدين محترفين."</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">أحمد محمدي</h6>
                                <small class="text-muted">حاج من إيران - 2023</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-success mb-3"></i>
                        </div>
                        <p class="card-text fst-italic mb-4">"المشرفون والمرشدون كانوا في غاية الاحترام والمهنية. النقل منظم والسكن قريب من الحرم. أنصح بشدة بخدمات هذه الشركة."</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">فاطمة ديالو</h6>
                                <small class="text-muted">حاجة من السنغال - 2023</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-info mb-3"></i>
                        </div>
                        <p class="card-text fst-italic mb-4">"تنظيم مثالي من اللحظة الأولى. الفريق الطبي كان متاحاً دائماً والخدمة اللوجستية ممتازة. بارك الله فيكم على هذا العمل العظيم."</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">محمد راميش</h6>
                                <small class="text-muted">حاج من موريشيوس - 2024</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-warning mb-3"></i>
                        </div>
                        <p class="card-text fst-italic mb-4">"الطعام متنوع ولذيذ ويناسب جميع الأذواق. الغرف نظيفة ومريحة. شكراً لكم على جعل رحلة الحج سهلة وميسرة."</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">علي حسن زاده</h6>
                                <small class="text-muted">حاج من إيران - 2024</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-danger mb-3"></i>
                        </div>
                        <p class="card-text fst-italic mb-4">"خدمة عملاء ممتازة ومتاحة على مدار الساعة. حل سريع لجميع المشاكل. أشعر بالأمان والراحة مع هذه الشركة الموثوقة."</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">خديجة بامبا</h6>
                                <small class="text-muted">حاجة من السنغال - 2024</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x text-secondary mb-3"></i>
                        </div>
                        <p class="card-text fst-italic mb-4">"تجربة رائعة مع فريق مناسك المشاعر. الاهتمام بالتفاصيل والحرص على راحة الحجاج يظهر في كل خدمة يقدمونها."</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">أمينة راجا</h6>
                                <small class="text-muted">حاجة من موريشيوس - 2024</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Testimonials Summary -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body text-center py-4">
                        <div class="row">
                            <div class="col-md-3">
                                <h3 class="text-primary fw-bold">4.9/5</h3>
                                <p class="mb-0">متوسط التقييم</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-success fw-bold">1,250+</h3>
                                <p class="mb-0">تقييم إيجابي</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-warning fw-bold">98%</h3>
                                <p class="mb-0">نسبة الرضا</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-info fw-bold">95%</h3>
                                <p class="mb-0">يوصون بخدماتنا</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Values -->
<div class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">رؤيتنا وقيمنا</h2>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h4 class="card-title text-primary">
                            <i class="fas fa-eye me-2"></i>الرؤية
                        </h4>
                        <p class="card-text">تسهيل الحج بما يفوق توقعات الحاج وتقديم خدمة متميزة ترقى لمستوى المشاعر المقدسة</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h4 class="card-title text-success">
                            <i class="fas fa-heart me-2"></i>الرسالة
                        </h4>
                        <p class="card-text">تقديم خدمة إنسانية ومتكاملة تجعل من تجربة الحج ذكرى لا تُنسى لضيوف الرحمن</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h5>الجودة</h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="text-center">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h5>الشفافية</h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="text-center">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h5>الريادة</h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="text-center">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h5>الإبداع</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- News and Blog Section -->
<div class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 text-primary">الأخبار والمقالات</h2>
            <p class="lead text-muted">آخر الأخبار والمقالات المفيدة للحجاج والموظفين</p>
        </div>
        
        <div class="row g-4">
            @if($news && count($news) > 0)
                @foreach($news->take(6) as $article)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                @if($article->image)
                                    <img src="{{ asset('storage/' . $article->image) }}" 
                                         alt="{{ $article->title }}" 
                                         class="w-100 h-100" 
                                         style="object-fit: cover;">
                                @else
                                    <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-newspaper fa-4x text-primary"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary">{{ $article->category ?? 'أخبار' }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $article->published_at ? $article->published_at->format('d F Y') : $article->created_at->format('d F Y') }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>
                                        {{ number_format($article->views ?? 0) }} مشاهدة
                                    </small>
                                </div>
                                <h5 class="card-title">{{ Str::limit($article->title, 60) }}</h5>
                                <p class="card-text">{{ Str::limit($article->excerpt ?: strip_tags($article->content), 120) }}</p>
                                <a href="{{ route('news.show', $article->id) }}" class="btn btn-outline-primary btn-sm">قراءة المزيد</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                                <i class="fas fa-newspaper fa-4x text-primary"></i>
                            </div>
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary">أخبار</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    15 يناير 2025
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>
                                    250 مشاهدة
                                </small>
                            </div>
                            <h5 class="card-title">إطلاق برنامج التوظيف الموسمي لحج 2025</h5>
                            <p class="card-text">نعلن عن بدء استقبال طلبات التوظيف الموسمي لموسم حج 2025 في جميع الأقسام مع رواتب تنافسية وبرامج تدريبية متطورة.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">قراءة المزيد</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- View All News Button -->
        <div class="text-center mt-5">
            <a href="{{ route('news.index') }}" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-newspaper me-2"></i>عرض جميع الأخبار
            </a>
        </div>
    </div>
</div>

<!-- Contact CTA -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="h1 mb-3">انضم إلى فريق مناسك المشاعر</h2>
                <p class="lead mb-0">كن جزءاً من خدمة ضيوف الرحمن في رحلتهم المقدسة</p>
            </div>
            <div class="col-md-4 text-md-end">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-paper-plane me-2"></i>قدم الآن
                    </a>
                @else
                    <a href="{{ route('jobs.index') }}" class="btn btn-light btn-lg px-5">
                        <i class="fas fa-search me-2"></i>تصفح الوظائف
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Contact Info -->
<div class="bg-light py-4">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4">
                <i class="fas fa-phone text-primary me-2"></i>
                <span class="fw-bold">+966 53 245 4696</span>
            </div>
            <div class="col-md-4">
                <i class="fas fa-envelope text-success me-2"></i>
                <span class="fw-bold">info@manasek.sa</span>
            </div>
            <div class="col-md-4">
                <i class="fas fa-map-marker-alt text-info me-2"></i>
                <span class="fw-bold">مكة المكرمة - حي الزايدي</span>
            </div>
        </div>
    </div>
</div>

<script>
// Statistics Counter Animation
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 20);
}

// Initialize counters when they come into view
function initCounters() {
    const counterElements = document.querySelectorAll('[data-count]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.getAttribute('data-count'));
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    });
    
    counterElements.forEach(element => {
        observer.observe(element);
    });
}

// Video placeholder function
function playVideo() {
    alert('سيتم إضافة رابط الفيديو الفعلي هنا!\n\nVideo will be embedded here with actual video URL.');
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initCounters();
});
</script>
@endsection 
