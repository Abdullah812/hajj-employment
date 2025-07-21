<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'icon',
        'color',
        'action_url',
        'is_read',
        'is_email_sent',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_email_sent' => 'boolean',
        'read_at' => 'datetime',
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIconClassAttribute(): string
    {
        $icons = [
            'application_status' => 'fa-paper-plane',
            'new_job' => 'fa-briefcase',
            // 'contract_signed' => 'fa-file-signature', - تم حذف نظام العقود
            'message' => 'fa-comment',
            'system' => 'fa-cog',
            'warning' => 'fa-exclamation-triangle',
        ];

        return 'fas ' . ($icons[$this->type] ?? 'fa-bell');
    }

    public function getColorClassAttribute(): string
    {
        $colors = [
            'primary' => 'text-primary',
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'info' => 'text-info',
        ];

        return $colors[$this->color] ?? 'text-primary';
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // دوال مساعدة
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    // إنشاء إشعار جديد
    public static function createForUser($userId, $type, $title, $message, $data = null, $actionUrl = null, $icon = 'bell', $color = 'primary')
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'icon' => $icon,
            'color' => $color,
        ]);
    }

    // إنشاء إشعار لكل المستخدمين من نوع معين
    public static function createForRole($role, $type, $title, $message, $data = null, $actionUrl = null, $icon = 'bell', $color = 'primary')
    {
        $users = User::role($role)->get();
        
        foreach ($users as $user) {
            self::createForUser($user->id, $type, $title, $message, $data, $actionUrl, $icon, $color);
        }
    }
}
