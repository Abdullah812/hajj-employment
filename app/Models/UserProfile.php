<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        // حقول الموظف
        'phone',
        'address',
        'date_of_birth',
        'national_id',
        'education',
        'experience',
        'skills',
        'bio',
        'cv_path',
        // حقول الشركة
        'company_name',
        'company_description',
        'company_website',
        'company_phone',
        'company_address',
        'company_license',
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
    ];
    
    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Accessors
    public function getCvUrlAttribute()
    {
        return $this->cv_path ? asset('storage/' . $this->cv_path) : null;
    }
}
