<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupListBucketFiles extends Command
{
    protected $signature = 'backup:list-bucket-files 
                            {--download= : Download specific file by name}
                            {--delete= : Delete specific file by name}
                            {--download-all : Download all files}
                            {--clean-old : Delete files older than 30 days}';

    protected $description = 'List, download, and manage backup files in Laravel Cloud bucket';

    public function handle()
    {
        $this->info('📋 إدارة ملفات النسخ الاحتياطية في الـ Bucket');
        $this->newLine();

        try {
            // Handle specific actions first
            if ($this->option('download')) {
                return $this->downloadSpecificFile($this->option('download'));
            }

            if ($this->option('delete')) {
                return $this->deleteSpecificFile($this->option('delete'));
            }

            if ($this->option('download-all')) {
                return $this->downloadAllFiles();
            }

            if ($this->option('clean-old')) {
                return $this->cleanOldFiles();
            }

            // Default: list all files
            return $this->listAllFiles();

        } catch (\Exception $e) {
            $this->error("❌ خطأ في الاتصال بالـ Bucket: " . $e->getMessage());
            $this->info('💡 تأكد من إعداد الـ Bucket في Laravel Cloud');
            return Command::FAILURE;
        }
    }

    private function listAllFiles(): int
    {
        $this->info('🔍 جاري البحث عن الملفات...');
        
        $files = Storage::disk('s3')->allFiles('backups');
        
        if (empty($files)) {
            $this->warn('⚠️  لا توجد ملفات في الـ Bucket');
            $this->info('💡 قم برفع النسخ أولاً: php artisan backup:upload-to-bucket --latest');
            return Command::SUCCESS;
        }

        $this->info("📊 تم العثور على " . count($files) . " ملف");
        $this->newLine();

        // Group files by date
        $groupedFiles = $this->groupFilesByDate($files);

        foreach ($groupedFiles as $date => $dateFiles) {
            $this->info("📅 {$date}:");
            $this->line('──────────────────────────────────────');
            
            foreach ($dateFiles as $file) {
                $fileInfo = $this->getFileInfo($file);
                $icon = $this->getFileIcon($file);
                
                $this->line("  {$icon} {$fileInfo['name']} ({$fileInfo['size']})");
                $this->line("     📍 مسار: {$file}");
                $this->line("     🕒 آخر تعديل: {$fileInfo['modified']}");
                
                if ($fileInfo['type'] === 'backup') {
                    $this->line("     📥 تحميل: php artisan backup:list-bucket-files --download=\"{$fileInfo['name']}\"");
                }
                $this->newLine();
            }
        }

        $this->displayManagementOptions();
        return Command::SUCCESS;
    }

    private function groupFilesByDate(array $files): array
    {
        $grouped = [];
        
        foreach ($files as $file) {
            // Extract date from path: backups/2025-07-20/filename
            preg_match('/backups\/(\d{4}-\d{2}-\d{2})\//', $file, $matches);
            $date = $matches[1] ?? 'غير معروف';
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [];
            }
            
            $grouped[$date][] = $file;
        }

        // Sort by date (newest first)
        krsort($grouped);
        
        return $grouped;
    }

    private function getFileInfo(string $file): array
    {
        $filename = basename($file);
        $size = Storage::disk('s3')->size($file);
        $lastModified = Storage::disk('s3')->lastModified($file);
        
        return [
            'name' => $filename,
            'size' => $this->formatFileSize($size),
            'modified' => Carbon::createFromTimestamp($lastModified)->format('Y-m-d H:i:s'),
            'type' => str_ends_with($filename, '.sql.gz') ? 'backup' : 'report'
        ];
    }

    private function getFileIcon(string $file): string
    {
        if (str_ends_with($file, '.sql.gz')) {
            return '💾'; // Database backup
        } elseif (str_ends_with($file, '.txt')) {
            return '📄'; // Report
        }
        return '📁'; // Other
    }

    private function downloadSpecificFile(string $filename): int
    {
        $this->info("🔍 البحث عن الملف: {$filename}");
        
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
            $this->info('💡 استخدم: php artisan backup:list-bucket-files لعرض الملفات المتاحة');
            return Command::FAILURE;
        }

        return $this->downloadFile($targetFile);
    }

    private function downloadFile(string $bucketFile): int
    {
        $filename = basename($bucketFile);
        $localPath = storage_path("app/downloads/{$filename}");
        
        // Create downloads directory if it doesn't exist
        $downloadsDir = storage_path('app/downloads');
        if (!is_dir($downloadsDir)) {
            mkdir($downloadsDir, 0755, true);
        }

        $this->info("📥 تحميل الملف: {$filename}");
        
        try {
            $content = Storage::disk('s3')->get($bucketFile);
            file_put_contents($localPath, $content);
            
            $fileSize = $this->formatFileSize(strlen($content));
            $this->info("✅ تم التحميل بنجاح!");
            $this->info("📍 المسار المحلي: {$localPath}");
            $this->info("📊 حجم الملف: {$fileSize}");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ فشل في التحميل: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function downloadAllFiles(): int
    {
        $files = Storage::disk('s3')->allFiles('backups');
        
        if (empty($files)) {
            $this->warn('⚠️  لا توجد ملفات للتحميل');
            return Command::SUCCESS;
        }

        $this->info("📥 تحميل " . count($files) . " ملف...");
        
        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        $successful = 0;
        $failed = 0;

        foreach ($files as $file) {
            try {
                $this->downloadFile($file);
                $successful++;
            } catch (\Exception $e) {
                $failed++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ تم تحميل: {$successful} ملف");
        if ($failed > 0) {
            $this->error("❌ فشل: {$failed} ملف");
        }

        return Command::SUCCESS;
    }

    private function deleteSpecificFile(string $filename): int
    {
        $this->info("🔍 البحث عن الملف: {$filename}");
        
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
            return Command::FAILURE;
        }

        if (!$this->confirm("⚠️  هل أنت متأكد من حذف: {$filename}؟")) {
            $this->info('❌ تم إلغاء العملية');
            return Command::SUCCESS;
        }

        try {
            Storage::disk('s3')->delete($targetFile);
            $this->info("✅ تم حذف الملف: {$filename}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ فشل في الحذف: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function cleanOldFiles(): int
    {
        $this->info('🔍 البحث عن الملفات القديمة (أكثر من 30 يوم)...');
        
        $files = Storage::disk('s3')->allFiles('backups');
        $cutoffDate = Carbon::now()->subDays(30);
        $oldFiles = [];

        foreach ($files as $file) {
            $lastModified = Storage::disk('s3')->lastModified($file);
            if (Carbon::createFromTimestamp($lastModified)->lt($cutoffDate)) {
                $oldFiles[] = $file;
            }
        }

        if (empty($oldFiles)) {
            $this->info('✅ لا توجد ملفات قديمة للحذف');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  تم العثور على " . count($oldFiles) . " ملف قديم");
        
        if (!$this->confirm('هل تريد حذف هذه الملفات؟')) {
            $this->info('❌ تم إلغاء العملية');
            return Command::SUCCESS;
        }

        $deleted = 0;
        foreach ($oldFiles as $file) {
            try {
                Storage::disk('s3')->delete($file);
                $deleted++;
                $this->line("✅ حذف: " . basename($file));
            } catch (\Exception $e) {
                $this->error("❌ فشل في حذف: " . basename($file));
            }
        }

        $this->info("✅ تم حذف {$deleted} ملف");
        return Command::SUCCESS;
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

    private function displayManagementOptions(): void
    {
        $this->newLine();
        $this->info('🛠️  خيارات الإدارة:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('📥 تحميل ملف معين:');
        $this->line('   php artisan backup:list-bucket-files --download="filename.sql.gz"');
        $this->newLine();
        $this->line('📥 تحميل جميع الملفات:');
        $this->line('   php artisan backup:list-bucket-files --download-all');
        $this->newLine();
        $this->line('🗑️  حذف ملف معين:');
        $this->line('   php artisan backup:list-bucket-files --delete="filename.sql.gz"');
        $this->newLine();
        $this->line('🧹 حذف الملفات القديمة (أكثر من 30 يوم):');
        $this->line('   php artisan backup:list-bucket-files --clean-old');
    }
} 