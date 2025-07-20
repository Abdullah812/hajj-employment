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

    /**
     * عرض صور المحتوى من قاعدة البيانات
     */
    public function viewContentImage($type, $id)
    {
        try {
            $model = null;
            
            // تحديد النموذج حسب النوع
            switch ($type) {
                case 'news':
                    $model = \App\Models\News::find($id);
                    break;
                case 'gallery':
                    $model = \App\Models\Gallery::find($id);
                    break;
                case 'testimonial':
                    $model = \App\Models\Testimonial::find($id);
                    break;
                case 'video':
                    $model = \App\Models\CompanyVideo::find($id);
                    break;
                default:
                    abort(400, 'نوع محتوى غير صحيح');
            }
            
            if (!$model) {
                abort(404, 'المحتوى غير موجود');
            }
            
            // الحصول على بيانات الصورة حسب النوع
            $fileData = null;
            $fileName = null;
            $mimeType = null;
            
            switch ($type) {
                case 'news':
                    $fileData = $model->image_file_data;
                    $fileName = $model->image_file_name ?: 'news_image.jpg';
                    $mimeType = $model->image_file_type ?: 'image/jpeg';
                    break;
                case 'gallery':
                    $fileData = $model->image_file_data;
                    $fileName = $model->image_file_name ?: 'gallery_image.jpg';
                    $mimeType = $model->image_file_type ?: 'image/jpeg';
                    break;
                case 'testimonial':
                    $fileData = $model->client_image_file_data;
                    $fileName = $model->client_image_file_name ?: 'client_image.jpg';
                    $mimeType = $model->client_image_file_type ?: 'image/jpeg';
                    break;
                case 'video':
                    $fileData = $model->thumbnail_file_data;
                    $fileName = $model->thumbnail_file_name ?: 'video_thumbnail.jpg';
                    $mimeType = $model->thumbnail_file_type ?: 'image/jpeg';
                    break;
            }
            
            if (!$fileData) {
                abort(404, 'الصورة غير موجودة في قاعدة البيانات');
            }
            
            // فك تشفير base64
            $decodedData = base64_decode($fileData);
            
            if (!$decodedData) {
                abort(500, 'خطأ في قراءة بيانات الصورة');
            }
            
            \Log::info('تم عرض صورة محتوى من قاعدة البيانات', [
                'type' => $type,
                'id' => $id,
                'file_name' => $fileName,
                'file_size' => strlen($decodedData)
            ]);
            
            // إرجاع الصورة مع headers صحيحة
            return response($decodedData, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Content-Length' => strlen($decodedData),
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Source' => 'database',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('خطأ في عرض صورة المحتوى', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            abort(500, 'خطأ في عرض الصورة');
        }
    }
} 