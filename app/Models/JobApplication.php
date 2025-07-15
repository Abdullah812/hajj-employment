<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobApplication extends Model
{
    protected $fillable = [
        'user_id',
        'job_id',
        'cover_letter',
        'status',
        'notes',
        'applied_at',
        'reviewed_at',
        'is_locked',
        'locked_at',
        'locked_by'
    ];
    
    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'locked_at' => 'datetime',
        'is_locked' => 'boolean'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($application) {
            $application->applied_at = now();
        });
    }
    
    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function job()
    {
        return $this->belongsTo(HajjJob::class, 'job_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(Contract::class);
    }
    
    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }
    
    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
    
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
        ];
        
        return $colors[$this->status] ?? 'bg-secondary';
    }
}
