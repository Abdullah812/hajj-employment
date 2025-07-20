<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

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

            // فحص حجم الملف (Laravel Cloud limits)
            $fileSize = $file->getSize();
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if ($fileSize > $maxSize) {
                \Log::error('File too large for database storage', [
                    'fileType' => $fileType,
                    'fileSize' => $fileSize,
                    'maxSize' => $maxSize
                ]);
                return false;
            }

            // محاولة آمنة لقراءة الملف
            $filePath = $file->getRealPath();
            if (!$filePath || !file_exists($filePath)) {
                \Log::error('File path not accessible', ['path' => $filePath]);
                return false;
            }

            // قراءة الملف بشكل تدريجي لتجنب memory issues
            $fileContent = '';
            $handle = fopen($filePath, 'rb');
            if (!$handle) {
                \Log::error('Cannot open file for reading', ['path' => $filePath]);
                return false;
            }

            while (!feof($handle)) {
                $chunk = fread($handle, 8192); // قراءة 8KB في كل مرة
                if ($chunk === false) {
                    fclose($handle);
                    \Log::error('Error reading file chunk');
                    return false;
                }
                $fileContent .= $chunk;
            }
            fclose($handle);

            // تحويل إلى base64
            $fileData = base64_encode($fileContent);
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';

            // التحقق من نجاح التحويل
            if (!$fileData) {
                \Log::error('Failed to encode file to base64', ['fileType' => $fileType]);
                return false;
            }

            // تحديث قاعدة البيانات بشكل آمن
            $updateData = [
                "{$fileType}_file_data" => $fileData,
                "{$fileType}_file_name" => $fileName,
                "{$fileType}_file_type" => $mimeType,
            ];

            // استخدام transaction للأمان
            \DB::transaction(function() use ($updateData) {
                $this->update($updateData);
            });

            \Log::info('File successfully saved to database', [
                'fileType' => $fileType,
                'fileName' => $fileName,
                'mimeType' => $mimeType,
                'dataSize' => strlen($fileData)
            ]);

            // تنظيف memory
            unset($fileContent, $fileData);
            
            return true;

        } catch (\Exception $e) {
            \Log::error('Error saving file to database', [
                'fileType' => $fileType,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
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

        // استخدام route بدلاً من data URL لتجنب مشكلة المتصفح
        return route('profile.file.view', ['type' => $fileType, 'id' => $this->user_id]);
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
