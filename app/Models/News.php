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
        'created_by',
        // حقول الصورة في قاعدة البيانات
        'image_file_data',
        'image_file_name',
        'image_file_type'
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
        // قاعدة البيانات أولاً
        if ($this->image_file_data) {
            return route('content.image.view', ['type' => 'news', 'id' => $this->id]);
        }
        
        // fallback للصور القديمة
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return null;
    }

    /**
     * حفظ صورة في قاعدة البيانات
     */
    public function saveImageToDatabase($file)
    {
        try {
            if (!$file || !$file->isValid()) {
                \Log::error('Invalid image file provided to News::saveImageToDatabase');
                return false;
            }

            // فحص حجم الملف (max 5MB)
            $fileSize = $file->getSize();
            $maxSize = 5 * 1024 * 1024; 
            
            if ($fileSize > $maxSize) {
                \Log::error('Image too large for database storage', [
                    'fileSize' => $fileSize,
                    'maxSize' => $maxSize
                ]);
                return false;
            }

            // قراءة وتحويل الصورة
            $filePath = $file->getRealPath();
            if (!$filePath || !file_exists($filePath)) {
                return false;
            }

            $fileContent = file_get_contents($filePath);
            $fileData = base64_encode($fileContent);
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType() ?: 'image/jpeg';

            // تحديث النموذج
            $this->update([
                'image_file_data' => $fileData,
                'image_file_name' => $fileName,
                'image_file_type' => $mimeType,
            ]);

            \Log::info('News image saved to database', [
                'news_id' => $this->id,
                'fileName' => $fileName,
                'dataSize' => strlen($fileData)
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error saving news image to database', [
                'news_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
