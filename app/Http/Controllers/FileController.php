<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    /**
     * تحميل ملف من storage
     *
     * @param string $file Base64 encoded file path
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function download($file)
    {
        try {
            // فك ترميز مسار الملف
            $filePath = base64_decode($file);
            
            if (!$filePath) {
                \Log::warning('محاولة تحميل ملف برمز غير صالح', ['encoded_file' => $file]);
                abort(404, 'ملف غير صالح');
            }

            // التحقق من أن المسار آمن (يجب أن يكون داخل مجلدات المسموحة)
            $allowedPaths = [
                'attachments/',
                'documents/',
                'mecca-applications/',
                'user-attachments/',
                'private/'
            ];
            
            $isPathAllowed = false;
            foreach ($allowedPaths as $allowedPath) {
                if (str_starts_with($filePath, $allowedPath)) {
                    $isPathAllowed = true;
                    break;
                }
            }
            
            if (!$isPathAllowed) {
                \Log::warning('محاولة الوصول لمسار غير مسموح', [
                    'file_path' => $filePath,
                    'user_id' => auth()->id()
                ]);
                abort(403, 'غير مسموح بالوصول لهذا الملف');
            }

            // محاولة العثور على الملف في multiple disks (S3 أولاً لـ Laravel Cloud)
            $disks = ['s3', 'public', 'private', 'local'];
            $foundDisk = null;
            
            foreach ($disks as $disk) {
                try {
                    if (Storage::disk($disk)->exists($filePath)) {
                        $foundDisk = $disk;
                        \Log::info('تم العثور على الملف', [
                            'file_path' => $filePath,
                            'disk' => $disk,
                            'user_id' => auth()->id()
                        ]);
                        break;
                    }
                } catch (\Exception $e) {
                    \Log::debug("فحص disk {$disk} فشل", [
                        'error' => $e->getMessage(),
                        'file_path' => $filePath
                    ]);
                    continue;
                }
            }
            
            if (!$foundDisk) {
                \Log::error('الملف غير موجود في أي disk', [
                    'file_path' => $filePath,
                    'user_id' => auth()->id(),
                    'checked_disks' => $disks
                ]);
                abort(404, 'الملف غير موجود');
            }

            // إعداد الملف للتحميل
            $storage = Storage::disk($foundDisk);
            $fileName = basename($filePath);
            
            // محاولة الحصول على mime type مع fallback
            try {
                $mimeType = $storage->mimeType($filePath);
            } catch (\Exception $e) {
                \Log::warning('لا يمكن تحديد نوع الملف', [
                    'file_path' => $filePath,
                    'error' => $e->getMessage()
                ]);
                $mimeType = 'application/octet-stream';
            }
            
            // إنشاء response للتحميل
            $fileContent = $storage->get($filePath);
            
            return Response::make(
                $fileContent,
                200,
                [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Content-Length' => strlen($fileContent),
                    'Cache-Control' => 'public, max-age=3600', // تحسين للأداء
                    'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT'
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('خطأ في تحميل الملف', [
                'file' => $file,
                'decoded_path' => $filePath ?? null,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            abort(500, 'خطأ في تحميل الملف');
        }
    }
} 