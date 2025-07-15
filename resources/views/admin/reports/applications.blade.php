@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تقرير الطلبات</h2>
        <a href="{{ route('admin.reports.applications.export') }}" class="btn btn-success">
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
                            <th>المتقدم</th>
                            <th>الوظيفة</th>
                            <th>القسم</th>
                            <th>تاريخ التقديم</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                        <tr>
                            <td>{{ $application->id }}</td>
                            <td>{{ $application->user->name }}</td>
                            <td>{{ $application->job->title }}</td>
                            <td>{{ $application->job->department->name }}</td>
                            <td>{{ $application->created_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ $application->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $applications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 