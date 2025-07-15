<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'thumbnail',
        'video_type',
        'duration',
        'languages',
        'quality',
        'status',
        'featured',
        'views',
        'created_by'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views' => 'integer',
        'languages' => 'array'
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

    public function scopeByType($query, $type)
    {
        return $query->where('video_type', $type);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getVideoTypeTextAttribute()
    {
        $types = [
            'youtube' => 'يوتيوب',
            'vimeo' => 'فيميو',
            'local' => 'محلي',
            'other' => 'أخرى'
        ];

        return $types[$this->video_type] ?? $this->video_type;
    }

    public function getLanguagesTextAttribute()
    {
        if (!$this->languages) {
            return 'العربية';
        }

        $languageNames = [
            'ar' => 'العربية',
            'en' => 'الإنجليزية',
            'fa' => 'الفارسية',
            'fr' => 'الفرنسية'
        ];

        $displayLanguages = [];
        foreach ($this->languages as $lang) {
            $displayLanguages[] = $languageNames[$lang] ?? $lang;
        }

        return implode(', ', $displayLanguages);
    }

    public function getEmbedCodeAttribute()
    {
        if ($this->video_type === 'youtube') {
            $videoId = $this->extractYouTubeId($this->video_url);
            if ($videoId) {
                return "https://www.youtube.com/embed/{$videoId}";
            }
        } elseif ($this->video_type === 'vimeo') {
            $videoId = $this->extractVimeoId($this->video_url);
            if ($videoId) {
                return "https://player.vimeo.com/video/{$videoId}";
            }
        }

        return $this->video_url;
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    private function extractYouTubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function extractVimeoId($url)
    {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        return $matches[1] ?? null;
    }
} 