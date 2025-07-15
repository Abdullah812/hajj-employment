<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'category',
        'status',
        'sort_order',
        'featured',
        'alt_text',
        'created_by'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getCategoryTextAttribute()
    {
        $categories = [
            'services' => 'الخدمات',
            'food' => 'الإعاشة',
            'accommodation' => 'الإقامة',
            'transportation' => 'النقل',
            'guidance' => 'الإرشاد',
            'medical' => 'الرعاية الطبية',
            'customer_service' => 'خدمة العملاء',
            'general' => 'عام'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط'
        ];

        return $statuses[$this->status] ?? $this->status;
    }
} 