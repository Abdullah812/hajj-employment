@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 text-secondary mb-1">
                        <i class="fas fa-images me-2"></i>إدارة معرض الصور
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active">معرض الصور</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.content.gallery.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة صورة جديدة
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title mb-0">صور المعرض</h5>
        </div>
        <div class="card-body">
            @if(isset($galleries) && $galleries->count() > 0)
                <div class="row">
                    @foreach($galleries as $gallery)
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100">
                                <img src="{{ asset('storage/' . $gallery->image_path) }}" 
                                     class="card-img-top" 
                                     style="height: 200px; object-fit: cover;"
                                     alt="{{ $gallery->title }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($gallery->title, 30) }}</h6>
                                    <p class="card-text small text-muted">{{ Str::limit($gallery->description, 60) }}</p>
                                    <span class="badge bg-secondary">{{ $gallery->category }}</span>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('admin.content.gallery.edit', $gallery) }}" 
                                           class="btn btn-outline-primary">تعديل</a>
                                        <form action="{{ route('admin.content.gallery.destroy', $gallery) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
                                                    onclick="return confirm('حذف الصورة؟')">حذف</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد صور في المعرض</h5>
                    <a href="{{ route('admin.content.gallery.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة أول صورة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 