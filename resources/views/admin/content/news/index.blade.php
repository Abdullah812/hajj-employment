@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-secondary mb-1">
                        <i class="fas fa-newspaper me-2"></i>إدارة الأخبار والمقالات
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">إدارة الأخبار</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.content.news.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة خبر جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">إجمالي الأخبار</h6>
                            <h3 class="mb-0">{{ $news->total() ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">الأخبار المنشورة</h6>
                            <h3 class="mb-0">{{ $news->where('status', 'published')->count() ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">أخبار بانتظار النشر</h6>
                            <h3 class="mb-0">{{ $news->where('status', 'draft')->count() ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">مشاهدات اليوم</h6>
                            <h3 class="mb-0">{{ $news->sum('views') ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- News Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">قائمة الأخبار</h5>
        </div>
        <div class="card-body p-0">
            @if($news && $news->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>العنوان</th>
                                <th>التصنيف</th>
                                <th>الحالة</th>
                                <th>المشاهدات</th>
                                <th>تاريخ النشر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($news as $article)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($article->image)
                                                <img src="{{ asset('storage/' . $article->image) }}" 
                                                     alt="{{ $article->title }}" 
                                                     class="rounded me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ Str::limit($article->title, 50) }}</h6>
                                                <small class="text-muted">{{ Str::limit($article->excerpt, 80) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $article->category ?? 'عام' }}</span>
                                    </td>
                                    <td>
                                        @if($article->status === 'published')
                                            <span class="badge bg-success">منشور</span>
                                        @else
                                            <span class="badge bg-warning">مسودة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($article->views ?? 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $article->published_at ? $article->published_at->format('Y/m/d') : 'غير منشور' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.content.news.edit', $article) }}" 
                                               class="btn btn-outline-primary" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.content.news.destroy', $article) }}" 
                                                  method="POST" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="حذف"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الخبر؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-3">
                    {{ $news->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد أخبار حالياً</h5>
                    <p class="text-muted">ابدأ بإضافة أول خبر للموقع</p>
                    <a href="{{ route('admin.content.news.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة خبر جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 