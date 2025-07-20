<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
     * إنشاء URL للسيرة الذاتية
     */
    public function getCvUrlAttribute()
    {
        if (!$this->cv_path) {
            return null;
        }
        
        // محاولة استخدام S3 disk أولاً
        try {
            if (Storage::disk('s3')->exists($this->cv_path)) {
                return Storage::disk('s3')->temporaryUrl($this->cv_path, now()->addHours(1));
            }
        } catch (\Exception $e) {
            // في حالة فشل S3، استخدم public disk
        }
        
        // استخدام public disk كبديل
        if (Storage::disk('public')->exists($this->cv_path)) {
            return Storage::disk('public')->url($this->cv_path);
        }
        
        return null;
    }

    /**
     * إنشاء URL لمرفق الهوية الوطنية
     */
    public function getNationalIdAttachmentUrlAttribute()
    {
        if (!$this->national_id_attachment) {
            return null;
        }
        
        try {
            if (Storage::disk('s3')->exists($this->national_id_attachment)) {
                return Storage::disk('s3')->temporaryUrl($this->national_id_attachment, now()->addHours(1));
            }
        } catch (\Exception $e) {
            // استخدام public disk كبديل
        }
        
        if (Storage::disk('public')->exists($this->national_id_attachment)) {
            return Storage::disk('public')->url($this->national_id_attachment);
        }
        
        return null;
    }

    /**
     * إنشاء URL لمرفق الآيبان
     */
    public function getIbanAttachmentUrlAttribute()
    {
        if (!$this->iban_attachment) {
            return null;
        }
        
        try {
            if (Storage::disk('s3')->exists($this->iban_attachment)) {
                return Storage::disk('s3')->temporaryUrl($this->iban_attachment, now()->addHours(1));
            }
        } catch (\Exception $e) {
            // استخدام public disk كبديل
        }
        
        if (Storage::disk('public')->exists($this->iban_attachment)) {
            return Storage::disk('public')->url($this->iban_attachment);
        }
        
        return null;
    }

    /**
     * إنشاء URL لمرفق العنوان الوطني
     */
    public function getNationalAddressAttachmentUrlAttribute()
    {
        if (!$this->national_address_attachment) {
            return null;
        }
        
        try {
            if (Storage::disk('s3')->exists($this->national_address_attachment)) {
                return Storage::disk('s3')->temporaryUrl($this->national_address_attachment, now()->addHours(1));
            }
        } catch (\Exception $e) {
            // استخدام public disk كبديل
        }
        
        if (Storage::disk('public')->exists($this->national_address_attachment)) {
            return Storage::disk('public')->url($this->national_address_attachment);
        }
        
        return null;
    }

    /**
     * إنشاء URL لشهادة الخبرة
     */
    public function getExperienceCertificateUrlAttribute()
    {
        if (!$this->experience_certificate) {
            return null;
        }
        
        try {
            if (Storage::disk('s3')->exists($this->experience_certificate)) {
                return Storage::disk('s3')->temporaryUrl($this->experience_certificate, now()->addHours(1));
            }
        } catch (\Exception $e) {
            // استخدام public disk كبديل
        }
        
        if (Storage::disk('public')->exists($this->experience_certificate)) {
            return Storage::disk('public')->url($this->experience_certificate);
        }
        
        return null;
    }
}
