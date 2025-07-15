<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'user_id',
        'phone',
        'email',
        'address',
        'status',
        'website',
        'department_number',
        'activity_type',
        'department_size',
        'services'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * العلاقة مع المستخدم المرتبط بالقسم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع مدير القسم
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * العلاقة مع الوظائف في القسم
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(HajjJob::class);
    }

    /**
     * التحقق من اكتمال معلومات القسم
     */
    public function isComplete(): bool
    {
        return !empty($this->name) &&
            !empty($this->description) &&
            !empty($this->manager_id) &&
            !empty($this->phone) &&
            !empty($this->email);
    }
} 