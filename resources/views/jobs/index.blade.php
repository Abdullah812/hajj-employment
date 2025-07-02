@extends('layouts.app')

@section('title', 'الوظائف المتاحة - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary">الوظائف المتاحة</h1>
        <p class="lead text-muted">اكتشف الفرص الوظيفية في موسم الحج مع شركة مناسك المشاعر</p>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('jobs.index') }}">
                <div class="row g-3">
                    <!-- البحث النصي -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="ابحث عن وظيفة...">
                    </div>
                    
                    <!-- الموقع -->
                    <div class="col-md-3">
                        <label for="location" class="form-label">الموقع</label>
                        <select class="form-select" id="location" name="location">
                            <option value="">جميع المواقع</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- القسم -->
                    <div class="col-md-3">
                        <label for="department" class="form-label">القسم</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">جميع الأقسام</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                    {{ $department }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- نوع العمل -->
                    <div class="col-md-2">
                        <label for="employment_type" class="form-label">نوع العمل</label>
                        <select class="form-select" id="employment_type" name="employment_type">
                            <option value="">جميع الأنواع</option>
                            <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>دوام كامل</option>
                            <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>دوام جزئي</option>
                            <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>مؤقت</option>
                            <option value="seasonal" {{ request('employment_type') == 'seasonal' ? 'selected' : '' }}>موسمي</option>
                        </select>
                    </div>
                    
                    <!-- الراتب الأدنى -->
                    <div class="col-md-6">
                        <label for="salary_min" class="form-label">الراتب الأدنى</label>
                        <input type="number" class="form-control" id="salary_min" name="salary_min" 
                               value="{{ request('salary_min') }}" placeholder="مثل: 3000">
                    </div>
                    
                    <!-- ترتيب النتائج -->
                    <div class="col-md-4">
                        <label for="sort_by" class="form-label">ترتيب حسب</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>الأحدث</option>
                            <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>اسم الوظيفة</option>
                            <option value="salary_min" {{ request('sort_by') == 'salary_min' ? 'selected' : '' }}>الراتب</option>
                            <option value="application_deadline" {{ request('sort_by') == 'application_deadline' ? 'selected' : '' }}>موعد الانتهاء</option>
                        </select>
                    </div>
                    
                    <!-- أزرار البحث -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['search', 'location', 'department', 'employment_type', 'salary_min']))
                    <div class="mt-3">
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> مسح الفلاتر
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- عدد النتائج -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">
            <i class="fas fa-briefcase me-2"></i>
            تم العثور على <strong>{{ $jobs->total() }}</strong> وظيفة
        </p>
        
        @if($jobs->hasPages())
            <div class="d-flex align-items-center">
                <span class="text-muted me-2">عرض {{ $jobs->firstItem() }}-{{ $jobs->lastItem() }} من {{ $jobs->total() }}</span>
            </div>
        @endif
    </div>

    <!-- قائمة الوظائف -->
    <div class="row g-4">
        @forelse($jobs as $job)
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <!-- عنوان الوظيفة -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                                    {{ $job->title }}
                                </a>
                            </h5>
                            <span class="badge bg-primary">{{ $job->employment_type_text }}</span>
                        </div>
                        
                        <!-- معلومات الشركة -->
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-building text-muted me-2"></i>
                            <span class="text-muted">{{ $job->company->name }}</span>
                        </div>
                        
                        <!-- الموقع -->
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <span class="text-muted">{{ $job->location }}</span>
                        </div>
                        
                        <!-- القسم -->
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-tags text-muted me-2"></i>
                            <span class="text-muted">{{ $job->department }}</span>
                        </div>
                        
                        <!-- الوصف -->
                        <p class="card-text">{{ Str::limit($job->description, 120) }}</p>
                        
                        <!-- الراتب -->
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                            <span class="fw-bold text-success">{{ $job->salary_range }}</span>
                        </div>
                        
                        <!-- معلومات إضافية -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar text-muted me-1"></i>
                                    ينتهي: {{ $job->application_deadline->format('Y/m/d') }}
                                </small>
                            </div>
                            @if($job->max_applicants)
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="fas fa-users text-muted me-1"></i>
                                        مطلوب: {{ $job->max_applicants }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- أزرار العمل -->
                    <div class="card-footer bg-light border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> عرض التفاصيل
                            </a>
                            
                            @auth
                                @if(auth()->user()->hasRole('employee'))
                                    @php
                                        $hasApplied = \App\Models\JobApplication::where('user_id', auth()->id())
                                            ->where('job_id', $job->id)
                                            ->exists();
                                    @endphp
                                    
                                    @if($hasApplied)
                                        <span class="badge bg-success">تم التقديم</span>
                                    @else
                                        <form method="POST" action="{{ route('employee.jobs.apply', $job) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane me-1"></i> قدم الآن
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-1"></i> تسجيل الدخول للتقديم
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- رسالة عدم وجود وظائف -->
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">لا توجد وظائف متاحة</h3>
                    <p class="text-muted">جرب تغيير معايير البحث أو تحقق لاحقاً للوظائف الجديدة</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">مشاهدة جميع الوظائف</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($jobs->hasPages())
        <div class="d-flex justify-content-center mt-5">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection 