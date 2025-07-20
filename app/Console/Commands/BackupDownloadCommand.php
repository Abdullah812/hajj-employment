<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:download 
                            {--latest : Download only the latest backup}
                            {--date= : Download backup from specific date (YYYY-MM-DD)}
                            {--list : List available backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download backup files from Laravel Cloud to local machine';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📥 Laravel Cloud Backup Download Tool');
        $this->info('==========================================');
        
        // Handle list option
        if ($this->option('list')) {
            return $this->listAvailableBackups();
        }
        
        try {
            // Check if running on Laravel Cloud or locally
            $isOnCloud = $this->isRunningOnLaravelCloud();
            
            if ($isOnCloud) {
                $this->info('🌐 Running on Laravel Cloud - downloading to local storage');
                return $this->downloadFromCloudStorage();
            } else {
                $this->info('💻 Running locally - need to fetch from Laravel Cloud');
                $this->error('❌ This command should be run on Laravel Cloud Console first');
                $this->info('💡 Steps to download:');
                $this->info('   1. Run this command on Laravel Cloud Console');
                $this->info('   2. Files will be prepared for download');
                $this->info('   3. Use Laravel Cloud interface to download');
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Download failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Check if running on Laravel Cloud
     */
    private function isRunningOnLaravelCloud(): bool
    {
        // Check for Laravel Cloud environment indicators
        return str_contains(gethostname(), 'laravel.cloud') 
            || str_contains(getcwd(), '/var/www/html')
            || env('LARAVEL_CLOUD') === true;
    }
    
    /**
     * List available backups
     */
    private function listAvailableBackups()
    {
        $this->info('📋 Available Backup Files:');
        $this->info('==========================================');
        
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            $this->warn('⚠️ No backup directory found');
            $this->info('💡 Run "php artisan backup:cloud-database" first');
            return Command::SUCCESS;
        }
        
        $files = glob($backupPath . '/*.gz');
        $reports = glob($backupPath . '/backup_report_*.txt');
        
        if (empty($files)) {
            $this->warn('⚠️ No backup files found');
            $this->info('💡 Run "php artisan backup:cloud-database" first');
            return Command::SUCCESS;
        }
        
        $this->info("📊 Found " . count($files) . " backup files:");
        $this->newLine();
        
        // Sort files by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        foreach ($files as $index => $file) {
            $filename = basename($file);
            $size = $this->formatFileSize(filesize($file));
            $date = Carbon::createFromTimestamp(filemtime($file));
            $isLatest = $index === 0 ? ' 🔥 LATEST' : '';
            
            $this->info("📦 {$filename}");
            $this->info("   📅 Created: {$date->format('Y-m-d H:i:s')} ({$date->diffForHumans()})");
            $this->info("   📏 Size: {$size}{$isLatest}");
            $this->newLine();
        }
        
        // Show reports
        if (!empty($reports)) {
            $this->info("📊 Available Reports:");
            foreach ($reports as $report) {
                $filename = basename($report);
                $this->info("📄 {$filename}");
            }
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Download from cloud storage
     */
    private function downloadFromCloudStorage()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            $this->warn('⚠️ No backup directory found');
            $this->info('💡 Run "php artisan backup:cloud-database" first');
            return Command::FAILURE;
        }
        
        $files = glob($backupPath . '/*.gz');
        $reports = glob($backupPath . '/backup_report_*.txt');
        
        if (empty($files)) {
            $this->warn('⚠️ No backup files found');
            $this->info('💡 Run "php artisan backup:cloud-database" first');
            return Command::FAILURE;
        }
        
        // Determine which files to process
        $targetFiles = [];
        
        if ($this->option('latest')) {
            // Get latest file only
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            $targetFiles[] = $files[0];
            
            // Find corresponding report
            $basename = str_replace('.sql.gz', '', basename($files[0]));
            $reportFile = $backupPath . '/backup_report_' . str_replace('hajj_employment_backup_', '', $basename) . '.txt';
            if (file_exists($reportFile)) {
                $targetFiles[] = $reportFile;
            }
            
        } elseif ($this->option('date')) {
            // Get files from specific date
            $targetDate = $this->option('date');
            $formattedDate = str_replace('-', '', $targetDate); // Convert 2025-07-20 to 20250720
            
            foreach ($files as $file) {
                if (str_contains(basename($file), $formattedDate)) {
                    $targetFiles[] = $file;
                }
            }
            
            foreach ($reports as $report) {
                if (str_contains(basename($report), $formattedDate)) {
                    $targetFiles[] = $report;
                }
            }
            
        } else {
            // Get all files
            $targetFiles = array_merge($files, $reports);
        }
        
        if (empty($targetFiles)) {
            $this->warn('⚠️ No files match the criteria');
            return Command::FAILURE;
        }
        
        // Create download-ready files
        $downloadDir = storage_path('app/downloads');
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
            $this->info("📁 Created download directory: {$downloadDir}");
        }
        
        $this->info('📥 Preparing files for download...');
        $this->newLine();
        
        $totalSize = 0;
        $copiedFiles = [];
        
        foreach ($targetFiles as $file) {
            $filename = basename($file);
            $downloadFile = $downloadDir . '/' . $filename;
            
            if (copy($file, $downloadFile)) {
                $size = filesize($downloadFile);
                $totalSize += $size;
                $copiedFiles[] = $downloadFile;
                
                $this->info("✅ {$filename} ({$this->formatFileSize($size)})");
            } else {
                $this->error("❌ Failed to copy {$filename}");
            }
        }
        
        $this->newLine();
        $this->info('==========================================');
        $this->info('✅ Download preparation completed!');
        $this->info("📊 Files ready: " . count($copiedFiles));
        $this->info("📏 Total size: " . $this->formatFileSize($totalSize));
        $this->info("📂 Location: {$downloadDir}");
        $this->info('==========================================');
        
        // Show how to access files
        $this->newLine();
        $this->info('📖 How to access your files:');
        $this->info('1. From Laravel Cloud Storage section');
        $this->info('2. Navigate to: storage/app/downloads/');
        $this->info('3. Download files to your computer');
        
        return Command::SUCCESS;
    }
    
    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
} 