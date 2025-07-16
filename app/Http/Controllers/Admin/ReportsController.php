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

    /**
     * API endpoint للبيانات المفلترة
     */
    public function getFilteredData(Request $request)
    {
        $filters = $request->only(['department', 'status', 'employment_type', 'salary_range', 'date_from', 'date_to']);
        
        // إحصائيات أساسية مفلترة
        $query = HajjJob::query();
        $appQuery = JobApplication::query();
        
        // تطبيق الفلاتر
        if (!empty($filters['department'])) {
            $query->where('department_id', $filters['department']);
            $appQuery->whereHas('job', function($q) use ($filters) {
                $q->where('department_id', $filters['department']);
            });
        }
        
        if (!empty($filters['status'])) {
            if (in_array($filters['status'], ['active', 'inactive'])) {
                $query->where('status', $filters['status']);
            } elseif (in_array($filters['status'], ['pending', 'approved', 'rejected'])) {
                $appQuery->where('status', $filters['status']);
            }
        }
        
        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
            $appQuery->whereHas('job', function($q) use ($filters) {
                $q->where('employment_type', $filters['employment_type']);
            });
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
            $appQuery->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
            $appQuery->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // حساب الإحصائيات
        $totalJobs = $query->count();
        $totalApplications = $appQuery->count();
        $pendingApplications = $appQuery->where('status', 'pending')->count();
        $approvedApplications = $appQuery->where('status', 'approved')->count();
        $rejectedApplications = $appQuery->where('status', 'rejected')->count();
        
        // بيانات شهرية للرسم البياني
        $monthlyJobs = [];
        $monthlyApplications = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            $jobsCount = (clone $query)->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
            $appsCount = (clone $appQuery)->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
                
            $monthlyJobs[] = $jobsCount;
            $monthlyApplications[] = $appsCount;
        }
        
        return response()->json([
            'total_jobs' => $totalJobs,
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'approved_applications' => $approvedApplications,
            'rejected_applications' => $rejectedApplications,
            'monthly_jobs' => $monthlyJobs,
            'monthly_applications' => $monthlyApplications,
        ]);
    }
    
    /**
     * إحصائيات إضافية متقدمة
     */
    public function getAdditionalStats()
    {
        $activeJobs = HajjJob::where('status', 'active')->count();
        $totalJobs = HajjJob::count();
        $totalApplications = JobApplication::count();
        
        $avgApplicationsPerJob = $totalJobs > 0 ? round($totalApplications / $totalJobs, 1) : 0;
        
        // متوسط وقت المراجعة
        $avgReviewTime = JobApplication::whereNotNull('reviewed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, reviewed_at)) as avg_days')
            ->value('avg_days');
        $avgReviewTime = round($avgReviewTime ?? 0, 1);
        
        return response()->json([
            'active_jobs' => $activeJobs,
            'avg_applications_per_job' => $avgApplicationsPerJob,
            'avg_review_time' => $avgReviewTime,
        ]);
    }
    
    /**
     * تحليل متقدم ذكي
     */
    public function getAdvancedAnalysis($type)
    {
        if ($type === 'jobs') {
            return $this->analyzeJobs();
        } elseif ($type === 'applications') {
            return $this->analyzeApplications();
        }
        
        return response()->json(['error' => 'Invalid analysis type'], 400);
    }
    
    /**
     * تحليل الوظائف
     */
    private function analyzeJobs()
    {
        $trends = [];
        $recommendations = [];
        
        // تحليل الاتجاهات
        $currentMonthJobs = HajjJob::whereMonth('created_at', now()->month)->count();
        $lastMonthJobs = HajjJob::whereMonth('created_at', now()->subMonth()->month)->count();
        
        if ($currentMonthJobs > $lastMonthJobs) {
            $increase = round((($currentMonthJobs - $lastMonthJobs) / max($lastMonthJobs, 1)) * 100, 1);
            $trends[] = "زيادة في نشر الوظائف بنسبة {$increase}% هذا الشهر";
        } else {
            $decrease = round((($lastMonthJobs - $currentMonthJobs) / max($lastMonthJobs, 1)) * 100, 1);
            $trends[] = "انخفاض في نشر الوظائف بنسبة {$decrease}% هذا الشهر";
        }
        
        // أكثر الأقسام نشاطاً
        $topDepartment = HajjJob::join('departments', 'hajj_jobs.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('count(*) as jobs_count'))
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('jobs_count', 'desc')
            ->first();
            
        if ($topDepartment) {
            $trends[] = "قسم {$topDepartment->name} هو الأكثر نشاطاً بـ {$topDepartment->jobs_count} وظيفة";
        }
        
        // نوع التوظيف الأكثر طلباً
        $topEmploymentType = HajjJob::select('employment_type', DB::raw('count(*) as count'))
            ->groupBy('employment_type')
            ->orderBy('count', 'desc')
            ->first();
            
        $employmentTypes = [
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي', 
            'temporary' => 'مؤقت',
            'seasonal' => 'موسمي'
        ];
        
        if ($topEmploymentType) {
            $typeName = $employmentTypes[$topEmploymentType->employment_type] ?? $topEmploymentType->employment_type;
            $trends[] = "نوع التوظيف الأكثر طلباً هو {$typeName}";
        }
        
        // التوصيات
        $inactiveJobs = HajjJob::where('status', 'inactive')->count();
        if ($inactiveJobs > 0) {
            $recommendations[] = "توجد {$inactiveJobs} وظيفة غير نشطة - يُنصح بمراجعتها وتفعيلها أو حذفها";
        }
        
        $oldJobs = HajjJob::where('created_at', '<', now()->subMonths(3))->count();
        if ($oldJobs > 0) {
            $recommendations[] = "توجد {$oldJobs} وظيفة قديمة (أكثر من 3 أشهر) - يُنصح بتحديثها";
        }
        
        $jobsWithoutApplications = HajjJob::doesntHave('applications')->count();
        if ($jobsWithoutApplications > 0) {
            $recommendations[] = "توجد {$jobsWithoutApplications} وظيفة بدون طلبات - قد تحتاج إعادة صياغة أو ترويج";
        }
        
        return response()->json([
            'trends' => $trends,
            'recommendations' => $recommendations
        ]);
    }
    
    /**
     * تحليل الطلبات
     */
    private function analyzeApplications()
    {
        $trends = [];
        $recommendations = [];
        
        // معدل القبول
        $totalApplications = JobApplication::count();
        $approvedApplications = JobApplication::where('status', 'approved')->count();
        $approvalRate = $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 1) : 0;
        
        $trends[] = "معدل القبول الحالي هو {$approvalRate}%";
        
        // الطلبات المعلقة
        $pendingApplications = JobApplication::where('status', 'pending')->count();
        $oldPendingApplications = JobApplication::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))->count();
            
        if ($oldPendingApplications > 0) {
            $trends[] = "توجد {$oldPendingApplications} طلبات معلقة لأكثر من أسبوع";
        }
        
        // متوسط وقت المراجعة
        $avgReviewTime = JobApplication::whereNotNull('reviewed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, reviewed_at)) as avg_days')
            ->value('avg_days');
        $avgReviewTime = round($avgReviewTime ?? 0, 1);
        
        $trends[] = "متوسط وقت مراجعة الطلبات هو {$avgReviewTime} يوم";
        
        // أكثر الوظائف طلباً
        $topJob = JobApplication::join('hajj_jobs', 'job_applications.job_id', '=', 'hajj_jobs.id')
            ->select('hajj_jobs.title', DB::raw('count(*) as applications_count'))
            ->groupBy('hajj_jobs.id', 'hajj_jobs.title')
            ->orderBy('applications_count', 'desc')
            ->first();
            
        if ($topJob) {
            $trends[] = "الوظيفة الأكثر طلباً هي '{$topJob->title}' بـ {$topJob->applications_count} طلب";
        }
        
        // التوصيات
        if ($approvalRate < 30) {
            $recommendations[] = "معدل القبول منخفض - يُنصح بمراجعة معايير القبول أو تحسين جودة الوظائف";
        } elseif ($approvalRate > 80) {
            $recommendations[] = "معدل القبول عالي جداً - قد تحتاج إلى رفع معايير القبول";
        }
        
        if ($avgReviewTime > 5) {
            $recommendations[] = "وقت المراجعة طويل - يُنصح بتسريع عملية مراجعة الطلبات";
        }
        
        if ($pendingApplications > 20) {
            $recommendations[] = "عدد كبير من الطلبات المعلقة - يُنصح بزيادة فريق المراجعة";
        }
        
        return response()->json([
            'trends' => $trends,
            'recommendations' => $recommendations
        ]);
    }
} 