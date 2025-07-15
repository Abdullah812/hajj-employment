@extends('layouts.app')

@section('title', 'إدارة الأقسام - شركة مناسك المشاعر')

@section('content')
<div class="container py-5">
    <!-- عنوان الصفحة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="card-title mb-2">إدارة الأقسام</h2>
                            <p class="card-text mb-0">عرض وإدارة جميع الأقسام المسجلة في النظام</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <i class="fas fa-building fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الأقسام -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>قائمة الأقسام
                </h5>
                <a href="{{ route('admin.users.create') }}?role=department" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>إضافة قسم جديد
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($departments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم القسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>رقم الهاتف</th>
                                <th>العنوان</th>
                                <th>عدد الوظائف</th>
                                <th>نوع النشاط</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $department)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            {{ $department->name }}
                                        </div>
                                    </td>
                                    <td>{{ $department->hr_email ?? $department->user->email }}</td>
                                    <td>{{ $department->phone ?? 'غير محدد' }}</td>
                                    <td>{{ Str::limit($department->address, 30) ?? 'غير محدد' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $department->jobs_count }} وظيفة</span>
                                    </td>
                                    <td>{{ $department->activity_type ?? 'غير محدد' }}</td>
                                    <td>{{ $department->created_at->format('Y/m/d') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.departments.edit', $department->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="if(confirm('هل أنت متأكد من حذف هذا القسم؟')) document.getElementById('delete-form-{{ $department->id }}').submit();">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <form id="delete-form-{{ $department->id }}" 
                                                  action="{{ route('admin.users.destroy', $department->user_id) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- الترقيم -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $departments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد أقسام مسجلة</h5>
                    <p class="text-muted">قم بإضافة قسم جديد من خلال الزر أعلاه</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #b47e13 0%, #be7b06 100%);
}
</style>
@endsection 