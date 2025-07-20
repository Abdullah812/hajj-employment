<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'national_id',
        'phone',
        'address',
        'date_of_birth',
        'qualification',
        'academic_experience',
        'iban_number',
        'cv_path',
        'iban_attachment',
        'national_id_attachment',
        'national_address_attachment',
        'experience_certificate'
    ];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method to get file URL from multiple storage disks
     */
    private function getFileUrl($filePath)
    {
        if (!$filePath) {
            return null;
        }

        try {
            // أولاً: تجربة S3 disk (Laravel Cloud يستخدمه كتخزين أساسي)
            if (Storage::disk('s3')->exists($filePath)) {
                return Storage::disk('s3')->url($filePath); // استخدام direct URL بدلاً من temporary
            }
        } catch (\Exception $e) {
            \Log::warning('خطأ في الوصول للـ S3 disk', [
                'file' => $filePath, 
                'error' => $e->getMessage()
            ]);
        }

        try {
            // ثانياً: تجربة public disk (للملفات المحلية)
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->url($filePath);
            }
        } catch (\Exception $e) {
            \Log::warning('خطأ في الوصول للـ public disk', [
                'file' => $filePath, 
                'error' => $e->getMessage()
            ]);
        }

        try {
            // ثالثاً: تجربة private disk مع روابط عادية (بدون signed)
            if (Storage::disk('private')->exists($filePath)) {
                return route('files.download', ['file' => base64_encode($filePath)]);
            }
        } catch (\Exception $e) {
            \Log::warning('خطأ في الوصول للـ private disk', [
                'file' => $filePath, 
                'error' => $e->getMessage()
            ]);
        }

        try {
            // رابعاً: تجربة local disk كآخر بديل
            if (Storage::disk('local')->exists($filePath)) {
                return route('files.download', ['file' => base64_encode($filePath)]);
            }
        } catch (\Exception $e) {
            \Log::warning('خطأ في الوصول للـ local disk', [
                'file' => $filePath, 
                'error' => $e->getMessage()
            ]);
        }
        
        // إذا لم نجد الملف في أي مكان، سجل الخطأ
        \Log::error('الملف غير موجود في أي disk', [
            'file' => $filePath,
            'user_id' => $this->user_id
        ]);
        
        return null;
    }

    /**
     * إنشاء URL للسيرة الذاتية
     */
    public function getCvUrlAttribute()
    {
        return $this->getFileUrl($this->cv_path);
    }

    /**
     * إنشاء URL لمرفق الهوية الوطنية
     */
    public function getNationalIdAttachmentUrlAttribute()
    {
        return $this->getFileUrl($this->national_id_attachment);
    }

    /**
     * إنشاء URL لمرفق الآيبان
     */
    public function getIbanAttachmentUrlAttribute()
    {
        return $this->getFileUrl($this->iban_attachment);
    }

    /**
     * إنشاء URL لمرفق العنوان الوطني
     */
    public function getNationalAddressAttachmentUrlAttribute()
    {
        return $this->getFileUrl($this->national_address_attachment);
    }

    /**
     * إنشاء URL لشهادة الخبرة
     */
    public function getExperienceCertificateUrlAttribute()
    {
        return $this->getFileUrl($this->experience_certificate);
    }
}
