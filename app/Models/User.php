<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
            'password' => 'hashed',
        ];
    }
    
    // العلاقات
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    
    public function jobs()
    {
        return $this->hasMany(HajjJob::class, 'company_id');
    }
    
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function employeeContracts()
    {
        return $this->hasMany(Contract::class, 'employee_id');
    }

    public function companyContracts()
    {
        return $this->hasMany(Contract::class, 'company_id');
    }

    /**
     * علاقة مع الشركة (إذا كان المستخدم شركة)
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

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

    public function isCompany()
    {
        return $this->hasRole('company');
    }

    public function isEmployee()
    {
        return $this->hasRole('employee');
    }
    
    /**
     * الحصول على اسم الشركة أو اسم المستخدم
     */
    public function getCompanyNameAttribute()
    {
        if ($this->isCompany() && $this->profile && $this->profile->company_name) {
            return $this->profile->company_name;
        }
        
        return $this->name;
    }
}
