<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\UserProfile;

class CleanMissingFiles extends Command
{
    protected $signature = 'files:clean-missing {--dry-run : عرض المراجع المكسورة دون حذفها}';
    protected $description = 'تنظيف مراجع الملفات المفقودة من قاعدة البيانات';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 وضع العرض فقط - لن يتم حذف أي شيء');
        } else {
            $this->warn('⚠️ وضع التنظيف الفعلي - سيتم حذف المراجع المكسورة');
            if (!$this->confirm('هل تريد المتابعة؟')) {
                $this->info('تم الإلغاء.');
                return Command::SUCCESS;
            }
        }
        
        $this->newLine();
        $this->info('🧹 تنظيف مراجع الملفات المفقودة...');
        $this->newLine();

        $fileFields = [
            'cv_path' => 'السيرة الذاتية',
            'national_id_attachment' => 'الهوية الوطنية',
            'iban_attachment' => 'الآيبان',
            'national_address_attachment' => 'العنوان الوطني',
            'experience_certificate' => 'شهادة الخبرة'
        ];

        $profiles = UserProfile::with('user')->get();
        $totalCleaned = 0;
        $disks = ['private', 'public', 's3', 'local'];

        foreach ($profiles as $profile) {
            $userName = $profile->user->name ?? "مستخدم #{$profile->user_id}";
            $cleanedFields = [];

            foreach ($fileFields as $field => $fieldName) {
                if ($profile->$field) {
                    $filePath = $profile->$field;
                    $fileExists = false;

                    // فحص وجود الملف في جميع الـ disks
                    foreach ($disks as $disk) {
                        try {
                            if (Storage::disk($disk)->exists($filePath)) {
                                $fileExists = true;
                                break;
                            }
                        } catch (\Exception $e) {
                            // تجاهل الأخطاء
                        }
                    }

                    if (!$fileExists) {
                        $this->error("❌ {$userName}: {$fieldName} مفقود ({$filePath})");
                        $cleanedFields[] = $field;
                        
                        if (!$isDryRun) {
                            $profile->$field = null;
                            $totalCleaned++;
                        }
                    } else {
                        $this->line("✅ {$userName}: {$fieldName} موجود");
                    }
                }
            }

            if (!empty($cleanedFields) && !$isDryRun) {
                $profile->save();
                $this->info("🧹 تم تنظيف ملفات {$userName}: " . implode(', ', array_map(function($field) use ($fileFields) {
                    return $fileFields[$field];
                }, $cleanedFields)));
            }
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->info("📊 تم العثور على {$totalCleaned} مرجع مكسور.");
            $this->info("💡 لحذفها فعلياً، شغّل الأمر بدون --dry-run");
        } else {
            $this->info("✅ تم تنظيف {$totalCleaned} مرجع مكسور من قاعدة البيانات.");
        }

        return Command::SUCCESS;
    }
} 