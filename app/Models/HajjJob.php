<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HajjJob extends Model
{
    protected $fillable = [
        'department_id',
        'region',
        'application_type',
        'requires_registration',
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
    
    /**
     * العلاقة مع طلبات مكة المفتوحة
     */
    public function meccaApplications()
    {
        return $this->hasMany(MeccaApplication::class, 'job_id');
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
    
    /**
     * الحصول على نص المنطقة بالعربية
     */
    public function getRegionTextAttribute()
    {
        $regions = [
            'mecca' => 'مكة المكرمة',
            'medina' => 'المدينة المنورة',
            'jeddah' => 'جدة',
            'taif' => 'الطائف',
            'other' => 'أخرى',
        ];
        
        return $regions[$this->region] ?? $this->region;
    }
    
    /**
     * الحصول على نص نوع التقديم
     */
    public function getApplicationTypeTextAttribute()
    {
        $types = [
            'registered' => 'يتطلب تسجيل دخول',
            'open' => 'تقديم مفتوح',
            'both' => 'كلا النوعين',
        ];
        
        return $types[$this->application_type] ?? $this->application_type;
    }
    
    /**
     * التحقق من كون الوظيفة في مكة
     */
    public function isMeccaJob()
    {
        return $this->region === 'mecca';
    }
    
    /**
     * التحقق من كون الوظيفة تتطلب تسجيل دخول
     */
    public function requiresLogin()
    {
        return $this->requires_registration || $this->region !== 'mecca';
    }
}
