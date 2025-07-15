@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة الوظائف</h1>
        <a href="{{ route('department.jobs.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> إضافة وظيفة جديدة
        </a>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إجمالي الوظائف</h5>
                    <p class="h2 mb-0 text-primary">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الوظائف النشطة</h5>
                    <p class="h2 mb-0 text-success">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الوظائف غير النشطة</h5>
                    <p class="h2 mb-0 text-warning">{{ $stats['inactive'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الوظائف المغلقة</h5>
                    <p class="h2 mb-0 text-danger">{{ $stats['closed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الوظائف -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>عنوان الوظيفة</th>
                            <th>القسم</th>
                            <th>الموقع</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-start">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->department }}</td>
                            <td>{{ $job->location }}</td>
                            <td>
                                <span class="badge {{ $job->status == 'active' ? 'bg-success' : ($job->status == 'inactive' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $job->status == 'active' ? 'نشط' : ($job->status == 'inactive' ? 'غير نشط' : 'مغلق') }}
                                </span>
                            </td>
                            <td>{{ $job->created_at->format('Y-m-d') }}</td>
                            <td class="text-start">
                                <div class="btn-group">
                                    <a href="{{ route('department.jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('department.jobs.edit', $job) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('department.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذه الوظيفة؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                لا توجد وظائف حالياً
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- الترقيم -->
    <div class="mt-3">
        {{ $jobs->links() }}
    </div>
</div>
@endsection 