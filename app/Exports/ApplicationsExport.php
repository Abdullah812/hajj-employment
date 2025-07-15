<?php

namespace App\Exports;

use App\Models\JobApplication;
use InvalidArgumentException;

/**
 * فئة تصدير طلبات التوظيف
 * تقوم بتصدير بيانات طلبات التوظيف مع إمكانية الترشيح حسب معايير مختلفة
 */
class ApplicationsExport
{
    /**
     * حالات الطلب المقبولة
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_UNDER_REVIEW = 'under_review';

    /**
     * مصفوفة المرشحات
     *
     * @var array
     */
    protected $filters;

    /**
     * إنشاء نموذج تصدير جديد
     *
     * @param array $filters المرشحات المطلوبة
     * @throws InvalidArgumentException
     */
    public function __construct(array $filters = [])
    {
        $this->validateFilters($filters);
        $this->filters = $filters;
    }

    /**
     * التحقق من صحة المرشحات
     *
     * @param array $filters
     * @throws InvalidArgumentException
     */
    protected function validateFilters(array $filters): void
    {
        if (!empty($filters['status']) && !in_array($filters['status'], [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_UNDER_REVIEW
        ])) {
            throw new InvalidArgumentException('حالة الطلب غير صالحة');
        }

        if (!empty($filters['date_from']) && !strtotime($filters['date_from'])) {
            throw new InvalidArgumentException('تاريخ البداية غير صالح');
        }

        if (!empty($filters['date_to']) && !strtotime($filters['date_to'])) {
            throw new InvalidArgumentException('تاريخ النهاية غير صالح');
        }
    }

    /**
     * إنشاء استعلام قاعدة البيانات
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = JobApplication::with(['user', 'job.department']);

        if (!empty($this->filters)) {
            if (!empty($this->filters['department'])) {
                $query->whereHas('job', function ($q) {
                    $q->where('department_id', $this->filters['department']);
                });
            }

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['date_from'])) {
                $query->whereDate('created_at', '>=', $this->filters['date_from']);
            }

            if (!empty($this->filters['date_to'])) {
                $query->whereDate('created_at', '<=', $this->filters['date_to']);
            }
        }

        return $query;
    }

    /**
     * تحويل بيانات الطلب إلى تنسيق التصدير
     *
     * @param JobApplication $application
     * @return array
     */
    public function map($application)
    {
        return [
            'المعرف' => $application->id,
            'المتقدم' => $application->user->name,
            'الوظيفة' => $application->job->title,
            'القسم' => $application->job->department->name,
            'تاريخ التقديم' => $application->created_at->format('Y-m-d'),
            'تاريخ المراجعة' => $application->reviewed_at ? $application->reviewed_at->format('Y-m-d') : '-',
            'الحالة' => $this->getStatusInArabic($application->status),
        ];
    }

    /**
     * تحويل حالة الطلب إلى اللغة العربية
     *
     * @param string $status
     * @return string
     */
    protected function getStatusInArabic(string $status): string
    {
        return match($status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_APPROVED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_UNDER_REVIEW => 'قيد المراجعة',
            default => $status,
        };
    }
} 