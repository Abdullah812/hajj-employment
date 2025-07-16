<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\Contract;
use App\Models\News;
use App\Models\Notification;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * عرض لوحة الإحصائيات الرئيسية
     */
    public function index()
    {
        // الإحصائيات الأساسية
        $stats = $this->getBasicStats();
        
        // إحصائيات الرسوم البيانية
        $charts = $this->getChartsData();
        
        // الأنشطة الأخيرة
        $recentActivities = $this->getRecentActivities();
        
        // تنبيهات الأداء
        $alerts = $this->getPerformanceAlerts();

        return view('admin.analytics.index', compact('stats', 'charts', 'recentActivities', 'alerts'));
    }

    /**
     * الحصول على الإحصائيات الأساسية
     */
    private function getBasicStats()
    {
        return [
            'total_users' => User::count(),
            'total_jobs' => HajjJob::count(),
            'total_applications' => JobApplication::count(),
            'total_contracts' => Contract::count(),
            'active_jobs' => HajjJob::where('status', 'active')->count(),
            'pending_applications' => JobApplication::where('status', 'pending')->count(),
            'approved_applications' => JobApplication::where('status', 'approved')->count(),
            'signed_contracts' => Contract::where('status', 'signed')->count(),
            'total_departments' => Department::where('status', 'active')->count(),
            'total_news' => News::where('status', 'published')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_applications_today' => JobApplication::whereDate('created_at', today())->count(),
        ];
    }

    /**
     * بيانات الرسوم البيانية
     */
    private function getChartsData()
    {
        return [
            'monthly_registrations' => $this->getMonthlyRegistrations(),
            'applications_by_status' => $this->getApplicationsByStatus(),
            'jobs_by_department' => $this->getJobsByDepartment(),
            'daily_activities' => $this->getDailyActivities(),
            'salary_distribution' => $this->getSalaryDistribution(),
        ];
    }

    /**
     * التسجيلات الشهرية
     */
    private function getMonthlyRegistrations()
    {
        $data = User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                  'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $data->firstWhere('month', $i);
            $result[] = [
                'month' => $months[$i - 1],
                'count' => $monthData ? $monthData->count : 0
            ];
        }

        return $result;
    }

    /**
     * الطلبات حسب الحالة
     */
    private function getApplicationsByStatus()
    {
        return JobApplication::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                $statusLabels = [
                    'pending' => 'قيد المراجعة',
                    'approved' => 'مقبول',
                    'rejected' => 'مرفوض'
                ];
                
                return [
                    'status' => $statusLabels[$item->status] ?? $item->status,
                    'count' => $item->count
                ];
            });
    }

    /**
     * الوظائف حسب القسم
     */
    private function getJobsByDepartment()
    {
        return HajjJob::join('departments', 'hajj_jobs.department_id', '=', 'departments.id')
            ->select('departments.name', DB::raw('count(*) as count'))
            ->where('hajj_jobs.status', 'active')
            ->groupBy('departments.id', 'departments.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * الأنشطة اليومية لآخر 30 يوم
     */
    private function getDailyActivities()
    {
        $activities = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $activities[] = [
                'date' => $date->format('Y-m-d'),
                'date_ar' => $date->format('d/m'),
                'registrations' => User::whereDate('created_at', $date)->count(),
                'applications' => JobApplication::whereDate('created_at', $date)->count(),
                'jobs_posted' => HajjJob::whereDate('created_at', $date)->count(),
            ];
        }

        return $activities;
    }

    /**
     * توزيع الرواتب
     */
    private function getSalaryDistribution()
    {
        $ranges = [
            ['min' => 0, 'max' => 3000, 'label' => 'أقل من 3000 ريال'],
            ['min' => 3000, 'max' => 5000, 'label' => '3000 - 5000 ريال'],
            ['min' => 5000, 'max' => 8000, 'label' => '5000 - 8000 ريال'],
            ['min' => 8000, 'max' => 12000, 'label' => '8000 - 12000 ريال'],
            ['min' => 12000, 'max' => 999999, 'label' => 'أكثر من 12000 ريال'],
        ];

        $distribution = [];
        
        foreach ($ranges as $range) {
            $count = HajjJob::where('salary_min', '>=', $range['min'])
                ->where('salary_max', '<=', $range['max'])
                ->where('status', 'active')
                ->count();
                
            $distribution[] = [
                'range' => $range['label'],
                'count' => $count
            ];
        }

        return $distribution;
    }

    /**
     * الأنشطة الأخيرة
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // آخر التسجيلات
        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user_registration',
                    'icon' => 'user-plus',
                    'color' => 'success',
                    'message' => "تسجيل مستخدم جديد: {$user->name}",
                    'time' => $user->created_at,
                    'url' => route('admin.users.index')
                ];
            });

        // آخر الطلبات
        $recentApplications = JobApplication::with(['user', 'job'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($app) {
                $colors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                return [
                    'type' => 'job_application',
                    'icon' => 'file-alt',
                    'color' => $colors[$app->status] ?? 'info',
                    'message' => "طلب توظيف جديد: {$app->user->name} للوظيفة {$app->job->title}",
                    'time' => $app->created_at,
                    'url' => route('admin.applications.index')
                ];
            });

        // آخر الوظائف
        $recentJobs = HajjJob::with('department')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($job) {
                return [
                    'type' => 'job_posting',
                    'icon' => 'briefcase',
                    'color' => 'primary',
                    'message' => "وظيفة جديدة: {$job->title} - {$job->department->name}",
                    'time' => $job->created_at,
                    'url' => route('admin.jobs.index')
                ];
            });

        return $activities->concat($recentUsers)
            ->concat($recentApplications)
            ->concat($recentJobs)
            ->sortByDesc('time')
            ->take(15)
            ->values();
    }

    /**
     * تنبيهات الأداء
     */
    private function getPerformanceAlerts()
    {
        $alerts = [];

        // تحقق من انخفاض التطبيقات
        $lastWeekApps = JobApplication::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->count();

        $thisWeekApps = JobApplication::whereBetween('created_at', [
            Carbon::now()->subWeek(),
            Carbon::now()
        ])->count();

        if ($lastWeekApps > 0 && $thisWeekApps < ($lastWeekApps * 0.8)) {
            $percentage = round((($lastWeekApps - $thisWeekApps) / $lastWeekApps) * 100);
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'message' => "انخفاض في طلبات التوظيف بنسبة {$percentage}% هذا الأسبوع",
                'action' => 'مراجعة استراتيجية التسويق'
            ];
        }

        // تحقق من الوظائف المنتهية الصلاحية
        $expiredJobs = HajjJob::where('application_deadline', '<', Carbon::now())
            ->where('status', 'active')
            ->count();

        if ($expiredJobs > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'clock',
                'message' => "{$expiredJobs} وظيفة انتهت مدة التقديم عليها",
                'action' => 'تحديث حالة الوظائف'
            ];
        }

        // تحقق من الطلبات المعلقة لفترة طويلة
        $oldPendingApps = JobApplication::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->count();

        if ($oldPendingApps > 10) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'hourglass-half',
                'message' => "{$oldPendingApps} طلب توظيف معلق لأكثر من أسبوع",
                'action' => 'مراجعة الطلبات المعلقة'
            ];
        }

        return $alerts;
    }

    /**
     * تصدير البيانات
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'overview');
        $format = $request->get('format', 'pdf');

        switch ($type) {
            case 'users':
                return $this->exportUsersReport($format);
            case 'jobs':
                return $this->exportJobsReport($format);
            case 'applications':
                return $this->exportApplicationsReport($format);
            default:
                return $this->exportOverviewReport($format);
        }
    }

    /**
     * تقرير المستخدمين
     */
    private function exportUsersReport($format)
    {
        $users = User::with(['roles', 'profile'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'title' => 'تقرير المستخدمين',
            'users' => $users,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ];

        if ($format === 'pdf') {
            // سيتم تطبيقه لاحقاً
            return response()->json(['message' => 'PDF export will be implemented']);
        }

        return response()->json($data);
    }

    /**
     * API للحصول على البيانات الحية
     */
    public function liveData()
    {
        return response()->json([
            'stats' => $this->getBasicStats(),
            'recent_activities' => $this->getRecentActivities()->take(5),
            'alerts' => $this->getPerformanceAlerts(),
            'timestamp' => now()->toISOString()
        ]);
    }
} 