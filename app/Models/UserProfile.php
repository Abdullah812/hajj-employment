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
        'experience_certificate',
        // إضافة الحقول الجديدة للملفات في قاعدة البيانات
        'cv_file_data',
        'cv_file_name',
        'cv_file_type',
        'national_id_file_data',
        'national_id_file_name',
        'national_id_file_type',
        'iban_file_data',
        'iban_file_name',
        'iban_file_type',
        'national_address_file_data',
        'national_address_file_name',
        'national_address_file_type',
        'experience_file_data',
        'experience_file_name',
        'experience_file_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * حفظ ملف في قاعدة البيانات كـ base64
     */
    public function saveFileToDatabase($file, $fileType)
    {
        try {
            if (!$file || !$file->isValid()) {
                \Log::error('Invalid file provided to saveFileToDatabase', ['fileType' => $fileType]);
                return false;
            }

            // التحقق من وجود الملف
            if (!file_exists($file->getRealPath())) {
                \Log::error('File does not exist at path', ['path' => $file->getRealPath()]);
                return false;
            }

            // قراءة محتوى الملف وتحويله إلى base64
            $fileData = base64_encode(file_get_contents($file->getRealPath()));
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();

            // التحقق من نجاح التحويل
            if (!$fileData) {
                \Log::error('Failed to encode file to base64', ['fileType' => $fileType]);
                return false;
            }

            // تحديث قاعدة البيانات
            $updateData = [
                "{$fileType}_file_data" => $fileData,
                "{$fileType}_file_name" => $fileName,
                "{$fileType}_file_type" => $mimeType,
            ];

            $this->update($updateData);

            \Log::info('File successfully saved to database', [
                'fileType' => $fileType,
                'fileName' => $fileName,
                'mimeType' => $mimeType,
                'dataSize' => strlen($fileData)
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error saving file to database', [
                'fileType' => $fileType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * الحصول على URL لعرض الملف من قاعدة البيانات
     */
    public function getFileUrl($fileType)
    {
        $fileData = $this->{"{$fileType}_file_data"};
        $fileName = $this->{"{$fileType}_file_name"};
        $mimeType = $this->{"{$fileType}_file_type"};

        if (!$fileData) {
            return null;
        }

        // إنشاء data URL للعرض المباشر
        return "data:{$mimeType};base64,{$fileData}";
    }

    /**
     * Helper method to get file URL from multiple sources (database first, then filesystem)
     */
    private function getFileUrlFallback($filePath, $fileType = null)
    {
        // أولاً: تحقق من وجود الملف في قاعدة البيانات
        if ($fileType && $this->getFileUrl($fileType)) {
            return $this->getFileUrl($fileType);
        }

        // ثانياً: إذا لم يوجد في قاعدة البيانات، جرب filesystem
        if (!$filePath) {
            return null;
        }

        try {
            // تجربة S3 disk (Laravel Cloud يستخدمه كتخزين أساسي)
            if (Storage::disk('s3')->exists($filePath)) {
                return Storage::disk('s3')->url($filePath);
            }
        } catch (\Exception $e) {
            \Log::warning('خطأ في الوصول للـ S3 disk', [
                'file' => $filePath, 
                'error' => $e->getMessage()
            ]);
        }

        try {
            // تجربة public disk مع رابط مباشر
            if (Storage::disk('public')->exists($filePath)) {
                return asset('storage/' . $filePath);
            }
        } catch (\Exception $e) {
            \Log::warning('خطأ في الوصول للـ public disk', [
                'file' => $filePath, 
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * إنشاء URL للسيرة الذاتية
     */
    public function getCvUrlAttribute()
    {
        return $this->getFileUrlFallback($this->cv_path, 'cv');
    }

    /**
     * إنشاء URL لمرفق الهوية الوطنية
     */
    public function getNationalIdAttachmentUrlAttribute()
    {
        return $this->getFileUrlFallback($this->national_id_attachment, 'national_id');
    }

    /**
     * إنشاء URL لمرفق الآيبان
     */
    public function getIbanAttachmentUrlAttribute()
    {
        return $this->getFileUrlFallback($this->iban_attachment, 'iban');
    }

    /**
     * إنشاء URL لمرفق العنوان الوطني
     */
    public function getNationalAddressAttachmentUrlAttribute()
    {
        return $this->getFileUrlFallback($this->national_address_attachment, 'national_address');
    }

    /**
     * إنشاء URL لشهادة الخبرة
     */
    public function getExperienceCertificateUrlAttribute()
    {
        return $this->getFileUrlFallback($this->experience_certificate, 'experience');
    }

    /**
     * التحقق من وجود ملف (في قاعدة البيانات أو filesystem)
     */
    public function hasFile($fileType, $filePath = null)
    {
        // تحقق من قاعدة البيانات أولاً
        if ($this->{"{$fileType}_file_data"}) {
            return true;
        }

        // تحقق من filesystem
        if ($filePath) {
            try {
                return Storage::disk('public')->exists($filePath) || 
                       Storage::disk('s3')->exists($filePath);
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }
}
