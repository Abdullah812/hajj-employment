<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UserProfile;

class TestAttachmentsAccess extends Command
{
    protected $signature = 'attachments:test-access 
                            {--fix : أصلح المشاكل تلقائياً}
                            {--migrate-to-s3 : انقل المرفقات لـ S3}
                            {--deep-clean : تنظيف عميق لكل المراجع المفقودة}';

    protected $description = 'اختبار الوصول لمرفقات المستخدمين وإصلاح المشاكل';

    public function handle()
    {
        $this->info('🔍 اختبار الوصول لمرفقات المستخدمين...');
        $this->info('ℹ️ النظام محسن لـ Laravel Cloud - يفضل استخدام S3');
        $this->newLine();

        $fix = $this->option('fix');
        $migrateToS3 = $this->option('migrate-to-s3');
        $deepClean = $this->option('deep-clean');

        // جلب المستخدمين الذين لديهم مرفقات
        $profiles = UserProfile::whereNotNull('cv_path')
            ->orWhereNotNull('national_id_attachment')
            ->orWhereNotNull('iban_attachment')
            ->orWhereNotNull('national_address_attachment')
            ->orWhereNotNull('experience_certificate')
            ->with('user')
            ->get();

        if ($profiles->isEmpty()) {
            $this->info('ℹ️ لا توجد مرفقات للاختبار');
            return Command::SUCCESS;
        }

        $this->info("📂 تم العثور على {$profiles->count()} ملف شخصي يحتوي على مرفقات");
        $this->newLine();

        $publicCount = 0;
        $s3Count = 0;
        $missingCount = 0;
        $fixedCount = 0;

        foreach ($profiles as $profile) {
            $this->line("🔍 فحص مرفقات: {$profile->user->name}");
            
            $attachmentFields = [
                'cv_path' => 'السيرة الذاتية',
                'national_id_attachment' => 'الهوية الوطنية',
                'iban_attachment' => 'الآيبان',
                'national_address_attachment' => 'العنوان الوطني',
                'experience_certificate' => 'شهادة الخبرة'
            ];

            foreach ($attachmentFields as $field => $name) {
                if (!$profile->$field) {
                    continue;
                }

                $filePath = $profile->$field;
                $status = $this->checkFileLocation($filePath);

                switch ($status) {
                    case 's3':
                        $this->line("  ✅ {$name}: موجود في S3");
                        $s3Count++;
                        break;
                        
                    case 'public':
                        $this->line("  📁 {$name}: موجود في Public");
                        $publicCount++;
                        
                        if ($migrateToS3) {
                            if ($this->migrateFileToS3($profile, $field, $filePath)) {
                                $this->line("    ✅ تم نقله إلى S3");
                                $fixedCount++;
                            }
                        }
                        break;
                        
                    case 'missing':
                        $this->error("  ❌ {$name}: مفقود");
                        $missingCount++;
                        break;
                }
            }
            $this->newLine();
        }

        // عرض الملخص
        $this->displaySummary($publicCount, $s3Count, $missingCount, $fixedCount);

        if ($missingCount > 0 && ($fix || $deepClean)) {
            $this->warn('💡 تشغيل وضع الإصلاح...');
            $this->cleanupMissingReferences();
        }

        if ($deepClean && !$fix) {
            $this->warn('💡 تشغيل التنظيف العميق...');
            $this->deepCleanupReferences();
        }

        return Command::SUCCESS;
    }

    private function checkFileLocation(string $filePath): string
    {
        // فحص S3 أولاً
        try {
            if (Storage::disk('s3')->exists($filePath)) {
                return 's3';
            }
        } catch (\Exception $e) {
            // تجاهل أخطاء S3
        }

        // فحص Public disk
        if (Storage::disk('public')->exists($filePath)) {
            return 'public';
        }

        return 'missing';
    }

    private function migrateFileToS3(UserProfile $profile, string $field, string $filePath): bool
    {
        try {
            // قراءة الملف من public disk
            $fileContent = Storage::disk('public')->get($filePath);
            
            // رفعه إلى S3
            $s3Path = "user-attachments/{$profile->user_id}/" . basename($filePath);
            Storage::disk('s3')->put($s3Path, $fileContent);
            
            // تحديث مسار الملف في قاعدة البيانات
            $profile->update([$field => $s3Path]);
            
            return true;
        } catch (\Exception $e) {
            $this->error("    ❌ فشل النقل: " . $e->getMessage());
            return false;
        }
    }

    private function cleanupMissingReferences(): void
    {
        $profiles = UserProfile::all();
        $cleanedProfiles = 0;
        $cleanedFiles = 0;

        foreach ($profiles as $profile) {
            $updated = false;
            $attachmentFields = ['cv_path', 'national_id_attachment', 'iban_attachment', 'national_address_attachment', 'experience_certificate'];

            foreach ($attachmentFields as $field) {
                if ($profile->$field && $this->checkFileLocation($profile->$field) === 'missing') {
                    $this->line("    🗑️ إزالة مرجع: {$field} للمستخدم {$profile->user->name}");
                    $profile->$field = null;
                    $updated = true;
                    $cleanedFiles++;
                }
            }

            if ($updated) {
                $profile->save();
                $cleanedProfiles++;
            }
        }

        $this->newLine();
        $this->info("🧹 تم تنظيف {$cleanedFiles} مرجع ملف مفقود من {$cleanedProfiles} ملف شخصي");
        
        // تشغيل فحص مرة أخرى للتأكد
        $this->newLine();
        $this->info("🔍 فحص سريع للتأكد من التنظيف...");
        
        $remainingFiles = 0;
        foreach (UserProfile::all() as $profile) {
            $attachmentFields = ['cv_path', 'national_id_attachment', 'iban_attachment', 'national_address_attachment', 'experience_certificate'];
            foreach ($attachmentFields as $field) {
                if ($profile->$field && $this->checkFileLocation($profile->$field) === 'missing') {
                    $remainingFiles++;
                }
            }
        }
        
        if ($remainingFiles == 0) {
            $this->info("✅ تم التنظيف بنجاح - لا توجد مراجع مفقودة متبقية");
        } else {
            $this->warn("⚠️ لا يزال هناك {$remainingFiles} مرجع مفقود - قد تحتاج لتشغيل الأمر مرة أخرى");
        }
    }

    private function deepCleanupReferences(): void
    {
        $this->info('🧹 تنظيف عميق - إزالة جميع المراجع الفارغة والمفقودة...');
        
        $profiles = UserProfile::all();
        $cleanedProfiles = 0;
        $cleanedFields = 0;
        
        foreach ($profiles as $profile) {
            $updated = false;
            $attachmentFields = ['cv_path', 'national_id_attachment', 'iban_attachment', 'national_address_attachment', 'experience_certificate'];
            
            foreach ($attachmentFields as $field) {
                $value = $profile->$field;
                
                // إزالة المراجع الفارغة أو المفقودة
                if ($value && (trim($value) === '' || $this->checkFileLocation($value) === 'missing')) {
                    $this->line("    🗑️ تنظيف {$field} للمستخدم {$profile->user->name}: '{$value}'");
                    $profile->$field = null;
                    $updated = true;
                    $cleanedFields++;
                }
            }
            
            if ($updated) {
                $profile->save();
                $cleanedProfiles++;
            }
        }
        
        $this->newLine();
        $this->info("✨ تم التنظيف العميق: {$cleanedFields} حقل من {$cleanedProfiles} ملف شخصي");
        
        // فحص نهائي سريع
        $this->newLine();
        $this->info("🔍 فحص نهائي للتأكد...");
        
        $remainingFiles = 0;
        foreach (UserProfile::all() as $profile) {
            $attachmentFields = ['cv_path', 'national_id_attachment', 'iban_attachment', 'national_address_attachment', 'experience_certificate'];
            foreach ($attachmentFields as $field) {
                if ($profile->$field && $this->checkFileLocation($profile->$field) === 'missing') {
                    $remainingFiles++;
                }
            }
        }
        
        if ($remainingFiles == 0) {
            $this->info("✅ التنظيف العميق مكتمل - لا توجد مراجع مفقودة");
        } else {
            $this->warn("⚠️ لا يزال هناك {$remainingFiles} مرجع مفقود");
        }
    }

    private function displaySummary(int $publicCount, int $s3Count, int $missingCount, int $fixedCount): void
    {
        $this->newLine();
        $this->info('📊 ملخص النتائج:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        if ($s3Count > 0) {
            $this->line("✅ ملفات في S3: {$s3Count}");
        }
        
        if ($publicCount > 0) {
            $this->line("📁 ملفات في Public: {$publicCount}");
        }
        
        if ($missingCount > 0) {
            $this->error("❌ ملفات مفقودة: {$missingCount}");
        }
        
        if ($fixedCount > 0) {
            $this->info("🔧 ملفات تم إصلاحها: {$fixedCount}");
        }

        $this->newLine();
        
        if ($publicCount > 0) {
            $this->warn('💡 لنقل الملفات إلى S3:');
            $this->line('php artisan attachments:test-access --migrate-to-s3');
        }
        
        if ($missingCount > 0) {
            $this->warn('💡 لتنظيف المراجع المفقودة:');
            $this->line('php artisan attachments:test-access --fix');
        }
    }
} 