<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;

class DebugCloudStorage extends Command
{
    protected $signature = 'cloud:debug-storage';
    protected $description = 'تشخيص مشاكل الملفات في Laravel Cloud';

    public function handle()
    {
        $this->info('🔍 تشخيص شامل لملفات Laravel Cloud...');
        $this->newLine();

        // 1. فحص إعدادات Storage
        $this->checkStorageConfig();
        
        // 2. فحص قاعدة البيانات
        $this->checkDatabase();
        
        // 3. فحص الملفات الفعلية
        $this->checkActualFiles();
        
        // 4. اختبار رفع ملف
        $this->testFileUpload();

        return Command::SUCCESS;
    }

    private function checkStorageConfig(): void
    {
        $this->info('📊 1. فحص إعدادات Storage:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $disks = ['public', 's3', 'local'];
        
        foreach ($disks as $disk) {
            try {
                $config = config("filesystems.disks.{$disk}");
                if ($config) {
                    $this->line("✅ {$disk} disk: معرف");
                    if ($disk === 's3') {
                        $this->line("   - Bucket: " . ($config['bucket'] ?? 'غير محدد'));
                        $this->line("   - Region: " . ($config['region'] ?? 'غير محدد'));
                        $this->line("   - Endpoint: " . ($config['endpoint'] ?? 'غير محدد'));
                    }
                } else {
                    $this->error("❌ {$disk} disk: غير معرف");
                }
            } catch (\Exception $e) {
                $this->error("❌ {$disk} disk: خطأ - " . $e->getMessage());
            }
        }
        
        $this->line("📁 Default disk: " . config('filesystems.default'));
        $this->newLine();
    }

    private function checkDatabase(): void
    {
        $this->info('📋 2. فحص قاعدة البيانات:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $profiles = UserProfile::with('user')->get();
        
        $this->line("👥 إجمالي المستخدمين: " . User::count());
        $this->line("📁 إجمالي الملفات الشخصية: " . $profiles->count());
        $this->newLine();
        
        $fileFields = [
            'cv_path' => 'السيرة الذاتية',
            'national_id_attachment' => 'الهوية الوطنية',
            'iban_attachment' => 'الآيبان',
            'national_address_attachment' => 'العنوان الوطني',
            'experience_certificate' => 'شهادة الخبرة'
        ];
        
        foreach ($fileFields as $field => $name) {
            $count = UserProfile::whereNotNull($field)->count();
            $this->line("📄 {$name}: {$count} مرجع");
        }
        
        $this->newLine();
        
        // عرض تفاصيل المراجع
        $this->line("📋 تفاصيل المراجع الموجودة:");
        foreach ($profiles as $profile) {
            if ($profile->cv_path || $profile->iban_attachment || $profile->national_id_attachment) {
                $this->line("👤 {$profile->user->name}:");
                foreach ($fileFields as $field => $name) {
                    if ($profile->$field) {
                        $this->line("   📁 {$name}: {$profile->$field}");
                    }
                }
                $this->newLine();
            }
        }
    }

    private function checkActualFiles(): void
    {
        $this->info('🔍 3. فحص الملفات الفعلية:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $profiles = UserProfile::whereNotNull('cv_path')
            ->orWhereNotNull('iban_attachment')
            ->orWhereNotNull('national_id_attachment')
            ->orWhereNotNull('national_address_attachment')
            ->orWhereNotNull('experience_certificate')
            ->with('user')
            ->get();
            
        foreach ($profiles as $profile) {
            $this->line("👤 فحص ملفات: {$profile->user->name}");
            
            $fileFields = [
                'cv_path' => 'السيرة الذاتية',
                'national_id_attachment' => 'الهوية الوطنية', 
                'iban_attachment' => 'الآيبان',
                'national_address_attachment' => 'العنوان الوطني',
                'experience_certificate' => 'شهادة الخبرة'
            ];
            
            foreach ($fileFields as $field => $name) {
                if ($profile->$field) {
                    $filePath = $profile->$field;
                    
                    // فحص في public disk
                    if (Storage::disk('public')->exists($filePath)) {
                        $this->line("   ✅ {$name}: موجود في Public");
                        $size = Storage::disk('public')->size($filePath);
                        $this->line("      حجم: " . $this->formatFileSize($size));
                    }
                    // فحص في S3
                    elseif (Storage::disk('s3')->exists($filePath)) {
                        $this->line("   ✅ {$name}: موجود في S3");
                        try {
                            $size = Storage::disk('s3')->size($filePath);
                            $this->line("      حجم: " . $this->formatFileSize($size));
                        } catch (\Exception $e) {
                            $this->line("      خطأ في قراءة الحجم: " . $e->getMessage());
                        }
                    }
                    // غير موجود
                    else {
                        $this->error("   ❌ {$name}: مفقود ({$filePath})");
                    }
                }
            }
            $this->newLine();
        }
    }

    private function testFileUpload(): void
    {
        $this->info('🧪 4. اختبار رفع ملف:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $testContent = 'هذا ملف اختبار لـ Laravel Cloud - ' . now()->toDateTimeString();
        $testFileName = 'test-file-' . time() . '.txt';
        
        // اختبار Public disk
        try {
            Storage::disk('public')->put("test/{$testFileName}", $testContent);
            $this->line("✅ Public disk: تم رفع الملف بنجاح");
            
            if (Storage::disk('public')->exists("test/{$testFileName}")) {
                $this->line("✅ Public disk: الملف قابل للقراءة");
                $url = Storage::disk('public')->url("test/{$testFileName}");
                $this->line("🔗 URL: {$url}");
                
                // حذف ملف الاختبار
                Storage::disk('public')->delete("test/{$testFileName}");
                $this->line("🗑️ تم حذف ملف الاختبار");
            }
        } catch (\Exception $e) {
            $this->error("❌ Public disk: فشل - " . $e->getMessage());
        }
        
        // اختبار S3 disk
        try {
            Storage::disk('s3')->put("test/{$testFileName}", $testContent);
            $this->line("✅ S3 disk: تم رفع الملف بنجاح");
            
            if (Storage::disk('s3')->exists("test/{$testFileName}")) {
                $this->line("✅ S3 disk: الملف قابل للقراءة");
                $url = Storage::disk('s3')->temporaryUrl("test/{$testFileName}", now()->addMinutes(5));
                $this->line("🔗 Temporary URL: " . substr($url, 0, 100) . "...");
                
                // حذف ملف الاختبار
                Storage::disk('s3')->delete("test/{$testFileName}");
                $this->line("🗑️ تم حذف ملف الاختبار");
            }
        } catch (\Exception $e) {
            $this->error("❌ S3 disk: فشل - " . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
} 