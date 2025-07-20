<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'client_country',
        'testimonial_text',
        'client_image',
        'rating',
        'hajj_year',
        'status',
        'featured',
        'sort_order',
        'created_by',
        // حقول الصورة في قاعدة البيانات
        'client_image_file_data',
        'client_image_file_name',
        'client_image_file_type'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'rating' => 'integer',
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

    public function scopeByCountry($query, $country)
    {
        return $query->where('client_country', $country);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('hajj_year', $year);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('created_at', 'desc');
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
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

    public function getRatingStarsAttribute()
    {
        return str_repeat('⭐', $this->rating);
    }

    public function getShortTestimonialAttribute()
    {
        return \Str::limit($this->testimonial_text, 100);
    }

    public function getClientImageUrlAttribute()
    {
        // قاعدة البيانات أولاً
        if ($this->client_image_file_data) {
            return route('content.image.view', ['type' => 'testimonial', 'id' => $this->id]);
        }
        
        // fallback للصور القديمة
        if ($this->client_image) {
            return asset('storage/' . $this->client_image);
        }
        
        return null;
    }

    /**
     * حفظ صورة العميل في قاعدة البيانات
     */
    public function saveClientImageToDatabase($file)
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
                'client_image_file_data' => $fileData,
                'client_image_file_name' => $fileName,
                'client_image_file_type' => $mimeType,
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error saving testimonial client image to database', [
                'testimonial_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
} 