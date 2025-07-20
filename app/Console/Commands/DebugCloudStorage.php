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
        
        $disks = ['public', 's3', 'local', 'private'];
        
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
        $this->info('🔍 3. فحص الملفات الفعلية في جميع الـ Disks:');
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
                    $found = false;
                    
                    // فحص في جميع الـ disks
                    $disks = ['public', 's3', 'private', 'local'];
                    
                    foreach ($disks as $disk) {
                        try {
                            if (Storage::disk($disk)->exists($filePath)) {
                                $this->line("   ✅ {$name}: موجود في {$disk} disk");
                                $size = Storage::disk($disk)->size($filePath);
                                $this->line("      حجم: " . $this->formatFileSize($size));
                                $found = true;
                                break;
                            }
                        } catch (\Exception $e) {
                            // تجاهل الأخطاء واستمر في البحث
                        }
                    }
                    
                    if (!$found) {
                        $this->error("   ❌ {$name}: مفقود من جميع الـ disks ({$filePath})");
                    }
                }
            }
            $this->newLine();
        }
    }

    private function testFileUpload(): void
    {
        $this->info('🧪 4. اختبار رفع ملف في جميع الـ Disks:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $testContent = 'هذا ملف اختبار لـ Laravel Cloud - ' . now()->toDateTimeString();
        $testFileName = 'test-file-' . time() . '.txt';
        
        $disks = ['public', 's3', 'private', 'local'];
        
        foreach ($disks as $disk) {
            try {
                Storage::disk($disk)->put("test/{$testFileName}", $testContent);
                $this->line("✅ {$disk} disk: تم رفع الملف بنجاح");
                
                if (Storage::disk($disk)->exists("test/{$testFileName}")) {
                    $this->line("✅ {$disk} disk: الملف قابل للقراءة");
                    
                    // محاولة إنشاء URL
                    try {
                        if ($disk === 's3') {
                            $url = Storage::disk($disk)->temporaryUrl("test/{$testFileName}", now()->addMinutes(5));
                            $this->line("🔗 Temporary URL: " . substr($url, 0, 100) . "...");
                        } elseif ($disk === 'public') {
                            $url = Storage::disk($disk)->url("test/{$testFileName}");
                            $this->line("🔗 URL: {$url}");
                        } else {
                            $this->line("🔗 URL: غير متاح للـ {$disk} disk");
                        }
                    } catch (\Exception $e) {
                        $this->error("❌ خطأ في إنشاء URL: " . $e->getMessage());
                    }
                    
                    // حذف ملف الاختبار
                    Storage::disk($disk)->delete("test/{$testFileName}");
                    $this->line("🗑️ تم حذف ملف الاختبار");
                }
            } catch (\Exception $e) {
                $this->error("❌ {$disk} disk: فشل - " . $e->getMessage());
            }
            $this->newLine();
        }
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