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
@endsection 
