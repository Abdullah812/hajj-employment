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

        // تجربة private disk أولاً (Laravel Cloud default)
        try {
            if (Storage::disk('private')->exists($filePath)) {
                // إنشاء signed URL للملفات في private disk
                return URL::temporarySignedRoute(
                    'files.download',
                    now()->addHours(2),
                    ['file' => base64_encode($filePath)]
                );
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ والمتابعة للـ disk التالي
        }

        // تجربة S3 disk
        try {
            if (Storage::disk('s3')->exists($filePath)) {
                return Storage::disk('s3')->temporaryUrl($filePath, now()->addHours(1));
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ والمتابعة للـ disk التالي
        }
        
        // تجربة public disk كبديل
        try {
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->url($filePath);
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ والمتابعة للـ disk التالي
        }

        // تجربة local disk كآخر بديل
        try {
            if (Storage::disk('local')->exists($filePath)) {
                return URL::temporarySignedRoute(
                    'files.download',
                    now()->addHours(2),
                    ['file' => base64_encode($filePath)]
                );
            }
        } catch (\Exception $e) {
            // تجاهل الخطأ
        }
        
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
