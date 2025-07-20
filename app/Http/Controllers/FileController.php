<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * عرض ملف من قاعدة البيانات فقط
     */
    public function viewFile($type, $userId)
    {
        try {
            $currentUser = Auth::user();
            
            // التحقق من الصلاحيات - المستخدم يمكنه عرض ملفاته أو المديرين والأقسام
            if ($currentUser->id != $userId && !$currentUser->hasAnyRole(['admin', 'department'])) {
                abort(403, 'غير مسموح بالوصول لهذا الملف');
            }
            
            // الحصول على الملف الشخصي
            $profile = UserProfile::where('user_id', $userId)->first();
            
            if (!$profile) {
                abort(404, 'الملف الشخصي غير موجود');
            }
            
            // التحقق من صحة نوع الملف
            $allowedTypes = ['cv', 'national_id', 'iban', 'national_address', 'experience'];
            if (!in_array($type, $allowedTypes)) {
                abort(400, 'نوع ملف غير صحيح');
            }
            
            // الحصول على بيانات الملف من قاعدة البيانات
            $fileData = $profile->{"{$type}_file_data"};
            $fileName = $profile->{"{$type}_file_name"} ?: "file.pdf";
            $mimeType = $profile->{"{$type}_file_type"} ?: 'application/octet-stream';
            
            if (!$fileData) {
                abort(404, 'الملف غير موجود في قاعدة البيانات');
            }
            
            // فك تشفير base64
            $decodedData = base64_decode($fileData);
            
            if (!$decodedData) {
                abort(500, 'خطأ في قراءة البيانات من قاعدة البيانات');
            }
            
            \Log::info('تم عرض ملف من قاعدة البيانات', [
                'type' => $type,
                'user_id' => $userId,
                'file_name' => $fileName,
                'viewer_id' => $currentUser->id,
                'file_size' => strlen($decodedData)
            ]);
            
            // إرجاع الملف مع headers صحيحة
            return response($decodedData, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Content-Length' => strlen($decodedData),
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Source' => 'database', // للتتبع
            ]);
            
        } catch (\Exception $e) {
            \Log::error('خطأ في عرض الملف من قاعدة البيانات', [
                'type' => $type,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            abort(500, 'خطأ في عرض الملف');
        }
    }
    
    /**
     * تحميل ملف من قاعدة البيانات فقط
     */
    public function downloadFile($type, $userId)
    {
        try {
            $currentUser = Auth::user();
            
            // التحقق من الصلاحيات
            if ($currentUser->id != $userId && !$currentUser->hasAnyRole(['admin', 'department'])) {
                abort(403, 'غير مسموح بتحميل هذا الملف');
            }
            
            // الحصول على الملف الشخصي
            $profile = UserProfile::where('user_id', $userId)->first();
            
            if (!$profile) {
                abort(404, 'الملف الشخصي غير موجود');
            }
            
            // التحقق من صحة نوع الملف
            $allowedTypes = ['cv', 'national_id', 'iban', 'national_address', 'experience'];
            if (!in_array($type, $allowedTypes)) {
                abort(400, 'نوع ملف غير صحيح');
            }
            
            // الحصول على بيانات الملف من قاعدة البيانات
            $fileData = $profile->{"{$type}_file_data"};
            $fileName = $profile->{"{$type}_file_name"} ?: "file.pdf";
            $mimeType = $profile->{"{$type}_file_type"} ?: 'application/octet-stream';
            
            if (!$fileData) {
                abort(404, 'الملف غير موجود في قاعدة البيانات');
            }
            
            // فك تشفير base64
            $decodedData = base64_decode($fileData);
            
            if (!$decodedData) {
                abort(500, 'خطأ في قراءة البيانات من قاعدة البيانات');
            }
            
            \Log::info('تم تحميل ملف من قاعدة البيانات', [
                'type' => $type,
                'user_id' => $userId,
                'file_name' => $fileName,
                'downloader_id' => $currentUser->id,
                'file_size' => strlen($decodedData)
            ]);
            
            // إرجاع الملف للتحميل
            return response($decodedData, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => strlen($decodedData),
                'X-Content-Source' => 'database', // للتتبع
            ]);
            
        } catch (\Exception $e) {
            \Log::error('خطأ في تحميل الملف من قاعدة البيانات', [
                'type' => $type,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            abort(500, 'خطأ في تحميل الملف');
        }
    }
} 