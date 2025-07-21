<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'approval_status' => 'string'
        ];
    }
    
    // العلاقات
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    
    // public function jobs() - تم حذف نظام الوظائف
    // public function applications() - تم حذف نظام الطلبات

    // public function employeeContracts() - تم حذف نظام العقود
    // public function departmentContracts() - تم حذف نظام العقود

    // علاقات الأقسام - تم حذف النظام

    /**
     * علاقة الإشعارات
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * الحصول على الإشعارات غير المقروءة
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->unread();
    }

    /**
     * عدد الإشعارات غير المقروءة
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * طريقة مساعدة للتحقق من نوع المستخدم
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isDepartment()
    {
        return $this->hasRole('department');
    }

    public function isEmployee()
    {
        return $this->hasRole('employee');
    }
    
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getApprovalStatusTextAttribute()
    {
        return [
            'pending' => 'في انتظار الموافقة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض'
        ][$this->approval_status] ?? $this->approval_status;
    }
    
    /**
     * الحصول على اسم القسم أو اسم المستخدم
     */
    public function getDepartmentNameAttribute()
    {
        if ($this->isDepartment() && $this->department && $this->department->name) {
            return $this->department->name;
        }
        
        return $this->name;
    }
}
