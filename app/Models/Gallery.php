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
        'created_by',
        // حقول الصورة في قاعدة البيانات
        'image_file_data',
        'image_file_name',
        'image_file_type'
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

    public function getImageUrlAttribute()
    {
        // قاعدة البيانات أولاً
        if ($this->image_file_data) {
            return route('content.image.view', ['type' => 'gallery', 'id' => $this->id]);
        }
        
        // fallback للصور القديمة
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
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
                return false;
            }

            $fileSize = $file->getSize();
            if ($fileSize > 5 * 1024 * 1024) { // 5MB
                return false;
            }

            $filePath = $file->getRealPath();
            if (!$filePath || !file_exists($filePath)) {
                return false;
            }

            $fileContent = file_get_contents($filePath);
            $fileData = base64_encode($fileContent);
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType() ?: 'image/jpeg';

            $this->update([
                'image_file_data' => $fileData,
                'image_file_name' => $fileName,
                'image_file_type' => $mimeType,
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error saving gallery image to database', [
                'gallery_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 