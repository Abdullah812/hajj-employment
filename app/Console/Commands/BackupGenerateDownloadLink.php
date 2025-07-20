<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupGenerateDownloadLink extends Command
{
    protected $signature = 'backup:generate-download-link 
                            {--file= : Specific filename to generate link for}
                            {--latest : Generate link for latest backup}
                            {--hours=24 : Link expiration in hours (default: 24)}';

    protected $description = 'Generate direct download links for backup files from bucket';

    public function handle()
    {
        $this->info('🔗 إنشاء رابط تحميل مباشر من الـ Bucket...');
        $this->newLine();

        try {
            $filename = $this->getTargetFile();
            if (!$filename) {
                return Command::FAILURE;
            }

            $downloadLink = $this->generateDownloadLink($filename);
            if ($downloadLink) {
                $this->displayDownloadInfo($filename, $downloadLink);
                return Command::SUCCESS;
            }

            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->error("❌ خطأ في إنشاء رابط التحميل: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getTargetFile(): ?string
    {
        if ($this->option('file')) {
            return $this->option('file');
        }

        if ($this->option('latest')) {
            return $this->findLatestFile();
        }

        // Interactive selection
        $files = $this->getAllBackupFiles();
        if (empty($files)) {
            $this->error('❌ لا توجد ملفات نسخ احتياطية في الـ Bucket!');
            return null;
        }

        $choices = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $date = $this->extractDateFromPath($file);
            $choices[$filename] = "{$filename} ({$date})";
        }

        $selectedFile = $this->choice(
            '📋 اختر الملف لإنشاء رابط التحميل:',
            $choices
        );

        return $selectedFile;
    }

    private function findLatestFile(): ?string
    {
        $this->info('🔍 البحث عن أحدث ملف...');
        
        $files = Storage::disk('s3')->allFiles('backups');
        $backupFiles = array_filter($files, fn($file) => str_ends_with($file, '.sql.gz'));

        if (empty($backupFiles)) {
            $this->error('❌ لا توجد ملفات نسخ احتياطية!');
            return null;
        }

        // Sort by last modified (newest first)
        usort($backupFiles, function($a, $b) {
            $timeA = Storage::disk('s3')->lastModified($a);
            $timeB = Storage::disk('s3')->lastModified($b);
            return $timeB - $timeA;
        });

        $latestFile = basename($backupFiles[0]);
        $this->info("📂 أحدث ملف: {$latestFile}");
        
        return $latestFile;
    }

    private function getAllBackupFiles(): array
    {
        $files = Storage::disk('s3')->allFiles('backups');
        return array_filter($files, fn($file) => str_ends_with($file, '.sql.gz'));
    }

    private function generateDownloadLink(string $filename): ?string
    {
        $this->info("🔗 إنشاء رابط تحميل للملف: {$filename}");

        // Find the full path of the file
        $files = Storage::disk('s3')->allFiles('backups');
        $targetFile = null;

        foreach ($files as $file) {
            if (basename($file) === $filename) {
                $targetFile = $file;
                break;
            }
        }

        if (!$targetFile) {
            $this->error("❌ الملف غير موجود: {$filename}");
            return null;
        }

        try {
            $expirationHours = (int) $this->option('hours');
            $expiration = Carbon::now()->addHours($expirationHours);

            // Generate presigned URL
            $url = Storage::disk('s3')->temporaryUrl($targetFile, $expiration);

            $this->info("✅ تم إنشاء رابط التحميل بنجاح!");
            return $url;

        } catch (\Exception $e) {
            $this->error("❌ فشل في إنشاء الرابط: " . $e->getMessage());
            return null;
        }
    }

    private function displayDownloadInfo(string $filename, string $downloadLink): void
    {
        $expirationHours = (int) $this->option('hours');
        $expirationTime = Carbon::now()->addHours($expirationHours)->format('Y-m-d H:i:s');

        $this->newLine();
        $this->info('📋 معلومات رابط التحميل:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $this->line("📁 اسم الملف: {$filename}");
        $this->line("⏰ صالح حتى: {$expirationTime}");
        $this->line("🔗 رابط التحميل:");
        $this->newLine();
        
        // Display the URL in a box
        $this->comment("┌" . str_repeat("─", 78) . "┐");
        $this->comment("│ انسخ هذا الرابط والصقه في المتصفح للتحميل المباشر:            │");
        $this->comment("└" . str_repeat("─", 78) . "┘");
        $this->newLine();
        
        $this->line($downloadLink);
        $this->newLine();
        
        $this->info('💡 إرشادات التحميل:');
        $this->line('   1. انسخ الرابط أعلاه');
        $this->line('   2. الصقه في شريط عنوان المتصفح');
        $this->line('   3. اضغط Enter - سيبدأ التحميل تلقائياً');
        $this->line('   4. سيتم حفظ الملف في مجلد التحميلات');
        
        $this->newLine();
        $this->warn("⚠️  هام: الرابط صالح لـ {$expirationHours} ساعة فقط!");
        $this->info('🔄 لإنشاء رابط جديد، شغّل نفس الأمر مرة أخرى');
    }

    private function extractDateFromPath(string $filePath): string
    {
        preg_match('/backups\/(\d{4}-\d{2}-\d{2})\//', $filePath, $matches);
        return $matches[1] ?? 'غير معروف';
    }
} 