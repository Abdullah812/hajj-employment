<?php

namespace App\Exports;

use App\Models\HajjJob;

class JobsExport
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = HajjJob::with(['department', 'applications']);

        if (!empty($this->filters)) {
            if (!empty($this->filters['department'])) {
                $query->where('department_id', $this->filters['department']);
            }

            if (!empty($this->filters['employment_type'])) {
                $query->where('employment_type', $this->filters['employment_type']);
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

    public function map($job)
    {
        return [
            'المعرف' => $job->id,
            'المسمى الوظيفي' => $job->title,
            'القسم' => $job->department->name,
            'نوع التوظيف' => $job->employment_type,
            'عدد الطلبات' => $job->applications_count ?? 0,
            'تاريخ النشر' => $job->created_at->format('Y-m-d'),
            'الحالة' => $job->status,
        ];
    }
} 