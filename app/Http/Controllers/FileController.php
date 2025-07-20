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
                abort(404, 'ملف غير صالح');
            }

            // محاولة العثور على الملف في multiple disks
            $disks = ['private', 'local', 's3', 'public'];
            $foundDisk = null;
            
            foreach ($disks as $disk) {
                try {
                    if (Storage::disk($disk)->exists($filePath)) {
                        $foundDisk = $disk;
                        break;
                    }
                } catch (\Exception $e) {
                    // استمرار البحث في الـ disk التالي
                    continue;
                }
            }
            
            if (!$foundDisk) {
                abort(404, 'الملف غير موجود');
            }

            // إعداد الملف للتحميل
            $storage = Storage::disk($foundDisk);
            $fileName = basename($filePath);
            $mimeType = $storage->mimeType($filePath);
            
            // إنشاء response للتحميل
            return Response::make(
                $storage->get($filePath),
                200,
                [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('خطأ في تحميل الملف: ' . $e->getMessage(), [
                'file' => $file,
                'decoded_path' => $filePath ?? null,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'خطأ في تحميل الملف');
        }
    }
} 