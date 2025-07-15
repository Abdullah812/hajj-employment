<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// PDF import removed - using Word documents only
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JobsExport;
use App\Exports\ApplicationsExport;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportsController extends Controller
{
    /**
     * عرض لوحة التقارير
     */
    public function index()
    {
        $totalJobs = HajjJob::count();
        $totalApplications = JobApplication::count();
        $pendingApplications = JobApplication::where('status', 'pending')->count();
        $approvedApplications = JobApplication::where('status', 'approved')->count();

        return view('admin.reports.index', compact(
            'totalJobs',
            'totalApplications',
            'pendingApplications',
            'approvedApplications'
        ));
    }

    /**
     * تقرير تفصيلي للوظائف
     */
    public function jobs(Request $request)
    {
        $jobs = HajjJob::with(['department', 'applications'])->paginate(10);
        return view('admin.reports.jobs', compact('jobs'));
    }

    /**
     * تقرير تفصيلي للطلبات
     */
    public function applications(Request $request)
    {
        $applications = JobApplication::with(['job', 'user'])->paginate(10);
        return view('admin.reports.applications', compact('applications'));
    }

    /**
     * تصدير تقرير الوظائف بصيغة Excel
     */
    public function exportJobs(Request $request)
    {
        $filters = $request->only(['department', 'employment_type', 'status', 'date_from', 'date_to']);
        $exporter = new JobsExport($filters);
        $jobs = $exporter->query()->get();

        if ($jobs->isEmpty()) {
            return back()->with('error', 'لا توجد بيانات للتصدير');
        }

        $filename = 'تقرير-الوظائف-' . now()->format('Y-m-d') . '.xlsx';
        
        return (new FastExcel($jobs))
            ->download($filename, function ($job) {
                return [
                    'المعرف' => $job->id,
                    'المسمى الوظيفي' => $job->title,
                    'القسم' => optional($job->department)->name ?? 'غير محدد',
                    'نوع التوظيف' => $job->employment_type,
                    'عدد الطلبات' => $job->applications_count ?? 0,
                    'تاريخ النشر' => $job->created_at ? $job->created_at->format('Y-m-d') : '-',
                    'الحالة' => $job->status,
                ];
            });
    }

    // PDF export function removed - using Word documents only

    /**
     * تصدير تقرير الطلبات بصيغة Excel
     */
    public function exportApplications(Request $request)
    {
        $filters = $request->only(['department', 'status', 'date_from', 'date_to']);
        $exporter = new ApplicationsExport($filters);
        $applications = $exporter->query()->get();

        if ($applications->isEmpty()) {
            return back()->with('error', 'لا توجد بيانات للتصدير');
        }

        $filename = 'تقرير-الطلبات-' . now()->format('Y-m-d') . '.xlsx';
        
        return (new FastExcel($applications))
            ->download($filename, function ($application) {
                return [
                    'المعرف' => $application->id,
                    'المتقدم' => optional($application->user)->name ?? 'غير محدد',
                    'الوظيفة' => optional($application->job)->title ?? 'غير محدد',
                    'القسم' => optional($application->job->department)->name ?? 'غير محدد',
                    'تاريخ التقديم' => $application->created_at ? $application->created_at->format('Y-m-d') : '-',
                    'تاريخ المراجعة' => $application->reviewed_at ? $application->reviewed_at->format('Y-m-d') : '-',
                    'الحالة' => $application->status,
                ];
            });
    }

    // PDF export function removed - using Word documents only
} 