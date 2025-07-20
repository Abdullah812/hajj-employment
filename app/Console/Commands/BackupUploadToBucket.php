<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupUploadToBucket extends Command
{
    protected $signature = 'backup:upload-to-bucket 
                            {--latest : Upload only the latest backup}
                            {--all : Upload all available backups}
                            {--date= : Upload backups from specific date (YYYY-MM-DD)}
                            {--force : Force upload even if file exists}';

    protected $description = 'Upload backup files to Laravel Cloud bucket storage';

    public function handle()
    {
        $this->info('🚀 بدء رفع النسخ الاحتياطية للـ Bucket...');
        $this->newLine();

        // Check if bucket is configured
        if (!$this->checkBucketConfiguration()) {
            return Command::FAILURE;
        }

        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            $this->error('❌ مجلد النسخ الاحتياطية غير موجود!');
            $this->info('💡 قم بتشغيل: php artisan backup:cloud-database أولاً');
            return Command::FAILURE;
        }

        $option = $this->getSelectedOption();
        $uploadedFiles = [];

        switch ($option) {
            case 'latest':
                $uploadedFiles = $this->uploadLatestBackup($backupPath);
                break;
            case 'all':
                $uploadedFiles = $this->uploadAllBackups($backupPath);
                break;
            case 'date':
                $date = $this->option('date');
                $uploadedFiles = $this->uploadBackupsByDate($backupPath, $date);
                break;
        }

        $this->displayUploadSummary($uploadedFiles);
        return Command::SUCCESS;
    }

    private function checkBucketConfiguration(): bool
    {
        try {
            // Test bucket connection
            Storage::disk('s3')->exists('test');
            $this->info('✅ تم التحقق من اتصال الـ Bucket بنجاح');
            return true;
        } catch (\Exception $e) {
            $this->error('❌ خطأ في اتصال الـ Bucket!');
            $this->error('💡 تأكد من إعداد الـ Bucket في Laravel Cloud');
            $this->error("📝 الخطأ: {$e->getMessage()}");
            return false;
        }
    }

    private function getSelectedOption(): string
    {
        if ($this->option('latest')) return 'latest';
        if ($this->option('all')) return 'all';
        if ($this->option('date')) return 'date';

        // Interactive selection
        $choice = $this->choice(
            '📋 اختر نوع الرفع:',
            [
                'latest' => 'رفع أحدث نسخة احتياطية',
                'all' => 'رفع جميع النسخ الاحتياطية',
                'date' => 'رفع نسخ من تاريخ معين'
            ],
            'latest'
        );

        if ($choice === 'date') {
            $date = $this->ask('📅 أدخل التاريخ (YYYY-MM-DD):');
            $this->input->setOption('date', $date);
        }

        return $choice;
    }

    private function uploadLatestBackup(string $backupPath): array
    {
        $this->info('🔍 البحث عن أحدث نسخة احتياطية...');
        
        $files = File::glob($backupPath . '/*.sql.gz');
        if (empty($files)) {
            $this->error('❌ لا توجد ملفات نسخ احتياطية!');
            return [];
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestFile = $files[0];
        $this->info("📂 أحدث ملف: " . basename($latestFile));

        return $this->uploadSingleFile($latestFile);
    }

    private function uploadAllBackups(string $backupPath): array
    {
        $this->info('🔍 البحث عن جميع النسخ الاحتياطية...');
        
        $files = File::glob($backupPath . '/*.{sql.gz,txt}', GLOB_BRACE);
        if (empty($files)) {
            $this->error('❌ لا توجد ملفات نسخ احتياطية!');
            return [];
        }

        $this->info("📊 تم العثور على " . count($files) . " ملف");

        $uploadedFiles = [];
        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($files as $file) {
            $result = $this->uploadSingleFile($file, false);
            $uploadedFiles = array_merge($uploadedFiles, $result);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $uploadedFiles;
    }

    private function uploadBackupsByDate(string $backupPath, string $date): array
    {
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('❌ تنسيق التاريخ غير صحيح! استخدم: YYYY-MM-DD');
            return [];
        }

        $this->info("🔍 البحث عن النسخ من تاريخ: {$date}");
        
        $pattern = $backupPath . '/*' . $date . '*.{sql.gz,txt}';
        $files = File::glob($pattern, GLOB_BRACE);

        if (empty($files)) {
            $this->error("❌ لا توجد نسخ احتياطية من تاريخ: {$date}");
            return [];
        }

        $this->info("📊 تم العثور على " . count($files) . " ملف");

        $uploadedFiles = [];
        foreach ($files as $file) {
            $result = $this->uploadSingleFile($file);
            $uploadedFiles = array_merge($uploadedFiles, $result);
        }

        return $uploadedFiles;
    }

    private function uploadSingleFile(string $filePath, bool $showProgress = true): array
    {
        $filename = basename($filePath);
        $fileDate = $this->extractDateFromFilename($filename);
        $bucketPath = "backups/{$fileDate}/{$filename}";

        if ($showProgress) {
            $this->info("📤 رفع: {$filename}");
        }

        try {
            // Check if file already exists
            if (Storage::disk('s3')->exists($bucketPath) && !$this->option('force')) {
                if ($showProgress) {
                    $this->warn("⚠️  الملف موجود مسبقاً: {$filename}");
                    $this->info("💡 استخدم --force للاستبدال");
                }
                return [];
            }

            // Upload file
            $fileContent = File::get($filePath);
            $success = Storage::disk('s3')->put($bucketPath, $fileContent);

            if ($success) {
                $fileSize = $this->formatFileSize(File::size($filePath));
                if ($showProgress) {
                    $this->info("✅ تم الرفع بنجاح: {$filename} ({$fileSize})");
                }
                
                return [[
                    'file' => $filename,
                    'path' => $bucketPath,
                    'size' => $fileSize,
                    'date' => $fileDate,
                    'status' => 'success'
                ]];
            } else {
                if ($showProgress) {
                    $this->error("❌ فشل في رفع: {$filename}");
                }
                return [[
                    'file' => $filename,
                    'status' => 'failed'
                ]];
            }

        } catch (\Exception $e) {
            if ($showProgress) {
                $this->error("❌ خطأ في رفع {$filename}: " . $e->getMessage());
            }
            return [[
                'file' => $filename,
                'status' => 'error',
                'error' => $e->getMessage()
            ]];
        }
    }

    private function extractDateFromFilename(string $filename): string
    {
        // Extract date from filename like: hajj_employment_backup_2025-07-20_05-51-45.sql.gz
        preg_match('/(\d{4}-\d{2}-\d{2})/', $filename, $matches);
        return $matches[1] ?? Carbon::now()->format('Y-m-d');
    }

    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function displayUploadSummary(array $uploadedFiles): void
    {
        $this->newLine();
        $this->info('📊 ملخص الرفع:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if (empty($uploadedFiles)) {
            $this->warn('⚠️  لم يتم رفع أي ملفات');
            return;
        }

        $successful = array_filter($uploadedFiles, fn($f) => $f['status'] === 'success');
        $failed = array_filter($uploadedFiles, fn($f) => $f['status'] !== 'success');

        $this->info("✅ تم رفع: " . count($successful) . " ملف");
        if (count($failed) > 0) {
            $this->error("❌ فشل: " . count($failed) . " ملف");
        }

        // Show successful uploads
        foreach ($successful as $file) {
            $this->line("  📤 {$file['file']} ({$file['size']}) → {$file['path']}");
        }

        // Show failed uploads
        foreach ($failed as $file) {
            if (isset($file['error'])) {
                $this->line("  ❌ {$file['file']} - {$file['error']}");
            } else {
                $this->line("  ❌ {$file['file']} - فشل");
            }
        }

        $this->newLine();
        $this->info('🎯 لعرض الملفات في الـ Bucket:');
        $this->line('   php artisan backup:list-bucket-files');
    }
} 