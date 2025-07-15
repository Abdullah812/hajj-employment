<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'image',
        'category',
        'status',
        'views',
        'featured',
        'published_at',
        'created_by'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = ucfirst($value);
    }

    // Accessors
    public function getCategoryTextAttribute()
    {
        $categories = [
            'news' => 'أخبار',
            'achievements' => 'إنجازات',
            'tips' => 'نصائح',
            'training' => 'تدريب',
            'success_stories' => 'قصص نجاح',
            'development' => 'تطوير'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'مسودة',
            'published' => 'منشور',
            'archived' => 'مؤرشف'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getExcerptAttribute($value)
    {
        return $value ?: \Str::limit(strip_tags($this->content), 150);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        return asset('storage/' . $this->image);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
