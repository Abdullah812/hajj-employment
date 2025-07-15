@extends('layouts.app')

@section('title', $news->title . ' - مناسك المشاعر')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm">
                <!-- Featured Image -->
                @if($news->image)
                    <div class="position-relative overflow-hidden">
                        <img src="{{ $news->image_url }}" 
                             alt="{{ $news->title }}" 
                             class="card-img-top"
                             style="height: 400px; object-fit: cover;">
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-primary fs-6">{{ $news->category ?? 'أخبار' }}</span>
                        </div>
                    </div>
                @endif

                <div class="card-body p-4">
                    <!-- Article Header -->
                    <div class="mb-4">
                        <h1 class="display-6 fw-bold text-primary mb-3">{{ $news->title }}</h1>
                        
                        <div class="d-flex flex-wrap align-items-center text-muted mb-3">
                            <div class="me-4 mb-2">
                                <i class="fas fa-calendar me-2"></i>
                                <span>{{ $news->published_at ? $news->published_at->format('d F Y') : $news->created_at->format('d F Y') }}</span>
                            </div>
                            <div class="me-4 mb-2">
                                <i class="fas fa-eye me-2"></i>
                                <span>{{ number_format($news->views ?? 0) }} مشاهدة</span>
                            </div>
                            @if($news->category)
                                <div class="me-4 mb-2">
                                    <i class="fas fa-tag me-2"></i>
                                    <span>{{ $news->category }}</span>
                                </div>
                            @endif
                        </div>

                        @if($news->excerpt)
                            <div class="alert alert-light border-start border-primary border-4 mb-4">
                                <h6 class="fw-bold mb-2">ملخص الخبر:</h6>
                                <p class="mb-0 fs-5 text-muted">{{ $news->excerpt }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Article Content -->
                    <div class="content">
                        {!! nl2br(e($news->content)) !!}
                    </div>
                </div>

                <!-- Article Footer -->
                <div class="card-footer bg-light border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-primary">شارك هذا الخبر:</strong>
                        </div>
                        <div>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm me-2">
                                <i class="fab fa-facebook-f me-1"></i>فيسبوك
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($news->title) }}" 
                               target="_blank" 
                               class="btn btn-outline-info btn-sm me-2">
                                <i class="fab fa-twitter me-1"></i>تويتر
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($news->title . ' - ' . request()->fullUrl()) }}" 
                               target="_blank" 
                               class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp me-1"></i>واتساب
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Navigation -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>جميع الأخبار
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>الصفحة الرئيسية
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related News -->
            @if($relatedNews && $relatedNews->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-newspaper me-2"></i>أخبار ذات صلة
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($relatedNews as $related)
                            <div class="border-bottom p-3">
                                <div class="d-flex">
                                    @if($related->image)
                                        <img src="{{ $related->image_url }}" 
                                             alt="{{ $related->title }}" 
                                             class="rounded me-3" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-newspaper text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2">
                                            <a href="{{ route('news.show', $related->id) }}" 
                                               class="text-decoration-none text-dark">
                                                {{ Str::limit($related->title, 60) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $related->published_at ? $related->published_at->format('M d') : $related->created_at->format('M d') }}
                                        </small>
                                        <span class="mx-2 text-muted">•</span>
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i>
                                            {{ number_format($related->views ?? 0) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('news.index') }}" class="btn btn-outline-primary btn-sm">
                            عرض جميع الأخبار
                        </a>
                    </div>
                </div>
            @endif

            <!-- Latest Jobs -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>أحدث الوظائف
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">تصفح أحدث الوظائف المتاحة في شركة مناسك المشاعر</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-success w-100">
                        <i class="fas fa-search me-2"></i>تصفح الوظائف
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #2c3e50;
}

.content p {
    margin-bottom: 1.5rem;
}

.badge {
    font-size: 0.9rem !important;
}

@media (max-width: 768px) {
    .display-6 {
        font-size: 1.8rem;
    }
    
    .content {
        font-size: 1rem;
    }
}
</style>
@endsection 