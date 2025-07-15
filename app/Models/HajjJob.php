<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HajjJob extends Model
{
    protected $fillable = [
        'department_id',
        'title',
        'description',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'requirements',
        'benefits',
        'application_deadline',
        'max_applicants',
        'status',
    ];
    
    protected $casts = [
        'application_deadline' => 'date',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];
    
    // العلاقات
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }
    
    // Accessors
    public function getEmploymentTypeTextAttribute()
    {
        $types = [
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي',
            'temporary' => 'مؤقت',
            'seasonal' => 'موسمي',
        ];
        
        return $types[$this->employment_type] ?? $this->employment_type;
    }
    
    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'closed' => 'مغلق',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
    
    public function getSalaryRangeAttribute()
    {
        if ($this->salary_min && $this->salary_max) {
            return number_format($this->salary_min) . ' - ' . number_format($this->salary_max) . ' ريال';
        } elseif ($this->salary_min) {
            return 'من ' . number_format($this->salary_min) . ' ريال';
        } elseif ($this->salary_max) {
            return 'حتى ' . number_format($this->salary_max) . ' ريال';
        }
        
        return 'راتب غير محدد';
    }
    
    public function getLocationTextAttribute()
    {
        return $this->location ?? 'غير محدد';
    }
    
    public function getDepartmentTextAttribute()
    {
        return optional($this->department)->name ?? 'عام';
    }
}
