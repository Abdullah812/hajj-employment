@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-secondary mb-1">
                        <i class="fas fa-plus me-2"></i>إضافة خبر جديد
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.content.news.index') }}">إدارة الأخبار</a></li>
                            <li class="breadcrumb-item active">إضافة خبر جديد</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.content.news.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i>العودة إلى القائمة
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.content.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">محتوى الخبر</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">العنوان *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">المقدمة</label>
                            <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                      id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                            <div class="form-text">ملخص مختصر للخبر (اختياري)</div>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label">المحتوى *</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publishing Options -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">خيارات النشر</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>منشور</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">التصنيف</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">اختر التصنيف</option>
                                <option value="أخبار الشركة" {{ old('category') === 'أخبار الشركة' ? 'selected' : '' }}>أخبار الشركة</option>
                                <option value="الحج والعمرة" {{ old('category') === 'الحج والعمرة' ? 'selected' : '' }}>الحج والعمرة</option>
                                <option value="التوظيف" {{ old('category') === 'التوظيف' ? 'selected' : '' }}>التوظيف</option>
                                <option value="الإنجازات" {{ old('category') === 'الإنجازات' ? 'selected' : '' }}>الإنجازات</option>
                                <option value="التطوير" {{ old('category') === 'التطوير' ? 'selected' : '' }}>التطوير</option>
                                <option value="عام" {{ old('category') === 'عام' ? 'selected' : '' }}>عام</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Published Date -->
                        <div class="mb-3">
                            <label for="published_at" class="form-label">تاريخ النشر</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                   id="published_at" name="published_at" value="{{ old('published_at') }}">
                            <div class="form-text">اتركه فارغاً للنشر الآن</div>
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">الصورة المميزة</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                   id="featured_image" name="featured_image" accept="image/*">
                            <div class="form-text">يُفضل الصور بمقاس 800x600 بكسل أو أكبر</div>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="d-none">
                            <img src="" alt="معاينة الصورة" class="img-fluid rounded mb-2" style="max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-outline-danger w-100" id="remove-image">
                                <i class="fas fa-trash me-1"></i>إزالة الصورة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>حفظ الخبر
                    </button>
                    <a href="{{ route('admin.content.news.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('featured_image');
    const imagePreview = document.getElementById('image-preview');
    const removeButton = document.getElementById('remove-image');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.querySelector('img').src = e.target.result;
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    removeButton.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.classList.add('d-none');
    });
});
</script>
@endsection 