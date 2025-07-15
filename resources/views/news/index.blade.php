@extends('layouts.app')

@section('title', 'جميع الأخبار - مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary mb-3">
            <i class="fas fa-newspaper me-3"></i>الأخبار والمقالات
        </h1>
        <p class="lead text-muted">آخر أخبار ومقالات شركة مناسك المشاعر</p>
    </div>

    @if($news && $news->count() > 0)
        <div class="row g-4">
            @foreach($news as $article)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <!-- Article Image -->
                        <div class="position-relative overflow-hidden" style="height: 250px;">
                            @if($article->image)
                                <img src="{{ $article->image_url }}" 
                                     alt="{{ $article->title }}" 
                                     class="card-img-top h-100 w-100" 
                                     style="object-fit: cover; transition: transform 0.3s ease;">
                            @else
                                <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                                    <i class="fas fa-newspaper fa-4x text-primary"></i>
                                </div>
                            @endif
                            
                            <!-- Category Badge -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-primary">{{ $article->category ?? 'أخبار' }}</span>
                            </div>
                            
                            <!-- Featured Badge -->
                            @if($article->featured)
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-warning">
                                        <i class="fas fa-star me-1"></i>مميز
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Article Content -->
                        <div class="card-body d-flex flex-column">
                            <!-- Article Meta -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $article->published_at ? $article->published_at->format('d F Y') : $article->created_at->format('d F Y') }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>
                                    {{ number_format($article->views ?? 0) }}
                                </small>
                            </div>

                            <!-- Article Title -->
                            <h5 class="card-title mb-3">
                                <a href="{{ route('news.show', $article->id) }}" 
                                   class="text-decoration-none text-dark hover-link">
                                    {{ $article->title }}
                                </a>
                            </h5>

                            <!-- Article Excerpt -->
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit($article->excerpt ?: strip_tags($article->content), 120) }}
                            </p>

                            <!-- Read More Button -->
                            <div class="mt-auto">
                                <a href="{{ route('news.show', $article->id) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-left me-2"></i>قراءة المزيد
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $news->links() }}
        </div>
    @else
        <!-- No News Message -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-newspaper fa-5x text-muted"></i>
            </div>
            <h3 class="text-muted mb-3">لا توجد أخبار متاحة حالياً</h3>
            <p class="text-muted mb-4">نعمل على تحديث المحتوى. تابعونا للحصول على آخر الأخبار.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>العودة للصفحة الرئيسية
            </a>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="bg-light py-4 mt-5">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-0 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-briefcase fa-2x text-success mb-3"></i>
                        <h5 class="card-title">الوظائف المتاحة</h5>
                        <p class="card-text text-muted">تصفح الوظائف المتاحة والتقدم لها</p>
                        <a href="{{ route('jobs.index') }}" class="btn btn-success">
                            تصفح الوظائف
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-home fa-2x text-primary mb-3"></i>
                        <h5 class="card-title">خدماتنا</h5>
                        <p class="card-text text-muted">تعرف على خدمات الحج والعمرة</p>
                        <a href="{{ route('home') }}#services" class="btn btn-primary">
                            تصفح الخدمات
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope fa-2x text-info mb-3"></i>
                        <h5 class="card-title">تواصل معنا</h5>
                        <p class="card-text text-muted">للاستفسارات والدعم الفني</p>
                        <a href="#contact" class="btn btn-info">
                            اتصل بنا
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.hover-card:hover img {
    transform: scale(1.05);
}

.hover-link:hover {
    color: var(--bs-primary) !important;
}

.badge {
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .display-5 {
        font-size: 2rem;
    }
}
</style>
@endsection 