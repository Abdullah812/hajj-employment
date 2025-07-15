@extends('admin.layouts.app')

@section('admin_content')
<div class="container-fluid">
    <h2 class="mb-4">لوحة التقارير</h2>

    <div class="row">
        <!-- إحصائيات عامة -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إجمالي الوظائف</h5>
                    <p class="card-text display-4">{{ $totalJobs }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إجمالي الطلبات</h5>
                    <p class="card-text display-4">{{ $totalApplications }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الطلبات قيد المراجعة</h5>
                    <p class="card-text display-4">{{ $pendingApplications }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">الطلبات المقبولة</h5>
                    <p class="card-text display-4">{{ $approvedApplications }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">تقارير الوظائف</h5>
                    <p class="card-text">عرض وتصدير تقارير تفصيلية عن الوظائف المتاحة</p>
                    <a href="{{ route('admin.reports.jobs') }}" class="btn btn-primary">عرض التقرير</a>
                    <a href="{{ route('admin.reports.jobs.export') }}" class="btn btn-success">تصدير Excel</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">تقارير الطلبات</h5>
                    <p class="card-text">عرض وتصدير تقارير تفصيلية عن طلبات التوظيف</p>
                    <a href="{{ route('admin.reports.applications') }}" class="btn btn-primary">عرض التقرير</a>
                    <a href="{{ route('admin.reports.applications.export') }}" class="btn btn-success">تصدير Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 