@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تقرير الوظائف</h2>
        <a href="{{ route('admin.reports.jobs.export') }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> تصدير Excel
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المسمى الوظيفي</th>
                            <th>القسم</th>
                            <th>نوع التوظيف</th>
                            <th>عدد الطلبات</th>
                            <th>تاريخ النشر</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->department->name }}</td>
                            <td>{{ $job->employment_type }}</td>
                            <td>{{ $job->applications->count() }}</td>
                            <td>{{ $job->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $job->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 