<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupCloudDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cloud-database {--local : Save backup locally only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the Laravel Cloud database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Laravel Cloud Database Backup...');
        $this->info('==========================================');
        
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupName = "hajj_employment_backup_{$timestamp}";
        
        try {
            // Test database connection
            $this->info('📡 Testing database connection...');
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
            
            // Get database info
            $dbConfig = config('database.connections.' . config('database.default'));
            $this->info("📊 Database: {$dbConfig['database']}");
            $this->info("🌐 Host: {$dbConfig['host']}");
            
            // Create backup directory locally
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
                $this->info('📁 Created backup directory: ' . $backupDir);
            }
            
            // Get all tables
            $this->info('📋 Getting list of tables...');
            $tables = $this->getAllTables();
            $tableCount = count($tables);
            $this->info("📊 Found {$tableCount} tables to backup");
            
            // Start backup process
            $sqlContent = $this->generateSQLBackup($tables, $backupName);
            
            // Save to local file
            $localFile = $backupDir . "/{$backupName}.sql";
            file_put_contents($localFile, $sqlContent);
            
            // Compress the file
            $this->info('🗜️ Compressing backup file...');
            $compressedFile = $this->compressBackup($localFile);
            
            // Generate report
            $report = $this->generateBackupReport($backupName, $localFile, $compressedFile, $tables);
            
            $this->info('==========================================');
            $this->info('✅ Backup completed successfully!');
            $this->info("📁 Local file: {$compressedFile}");
            $this->info("📊 Report: {$report}");
            $this->info('==========================================');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Get all tables from database
     */
    private function getAllTables(): array
    {
        $tables = [];
        $databaseName = config('database.connections.' . config('database.default'))['database'];
        
        try {
            $results = DB::select('SHOW TABLES');
            
            foreach ($results as $result) {
                // Get table name from result object
                $resultArray = (array) $result;
                $tableName = reset($resultArray); // Get first value
                
                if (!empty($tableName)) {
                    $tables[] = $tableName;
                }
            }
            
            $this->info("🔍 Found tables: " . implode(', ', array_slice($tables, 0, 5)) . (count($tables) > 5 ? '...' : ''));
            
        } catch (\Exception $e) {
            $this->error("Failed to get tables: " . $e->getMessage());
            throw $e;
        }
        
        return $tables;
    }
    
    /**
     * Generate SQL backup content
     */
    private function generateSQLBackup(array $tables, string $backupName): string
    {
        $sqlContent = "-- Laravel Cloud Database Backup\n";
        $sqlContent .= "-- Backup Name: {$backupName}\n";
        $sqlContent .= "-- Created: " . Carbon::now()->toDateTimeString() . "\n";
        $sqlContent .= "-- Database: " . config('database.connections.' . config('database.default'))['database'] . "\n";
        $sqlContent .= "-- ==========================================\n\n";
        
        $totalRecords = 0;
        
        foreach ($tables as $table) {
            $this->info("📄 Backing up table: {$table}");
            
            // Get table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
            $sqlContent .= "-- Table structure for {$table}\n";
            $sqlContent .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sqlContent .= $createTable->{'Create Table'} . ";\n\n";
            
            // Get table data
            $records = DB::table($table)->get();
            $recordCount = count($records);
            $totalRecords += $recordCount;
            
            if ($recordCount > 0) {
                $sqlContent .= "-- Data for table {$table} ({$recordCount} records)\n";
                
                foreach ($records as $record) {
                    $values = [];
                    foreach ($record as $value) {
                        if (is_null($value)) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    
                    $columns = implode('`, `', array_keys((array) $record));
                    $valuesList = implode(', ', $values);
                    
                    $sqlContent .= "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$valuesList});\n";
                }
                $sqlContent .= "\n";
            }
        }
        
        $sqlContent .= "-- Backup completed\n";
        $sqlContent .= "-- Total tables: " . count($tables) . "\n";
        $sqlContent .= "-- Total records: {$totalRecords}\n";
        
        $this->info("📊 Total records backed up: {$totalRecords}");
        
        return $sqlContent;
    }
    
    /**
     * Compress backup file
     */
    private function compressBackup(string $filePath): string
    {
        $compressedPath = $filePath . '.gz';
        
        $sourceFile = fopen($filePath, 'rb');
        $compressedFile = gzopen($compressedPath, 'wb9');
        
        while (!feof($sourceFile)) {
            gzwrite($compressedFile, fread($sourceFile, 8192));
        }
        
        fclose($sourceFile);
        gzclose($compressedFile);
        
        // Remove original file
        unlink($filePath);
        
        $size = number_format(filesize($compressedPath) / 1024 / 1024, 2);
        $this->info("📦 Compressed size: {$size} MB");
        
        return $compressedPath;
    }
    
    /**
     * Generate backup report
     */
    private function generateBackupReport(string $backupName, string $originalFile, string $compressedFile, array $tables): string
    {
        $reportPath = storage_path('app/backups') . "/backup_report_" . Carbon::now()->format('Y-m-d_H-i-s') . ".txt";
        
        $report = "==========================================\n";
        $report .= "Laravel Cloud Database Backup Report\n";
        $report .= "==========================================\n";
        $report .= "Backup Name: {$backupName}\n";
        $report .= "Date: " . Carbon::now()->toDateTimeString() . "\n";
        $report .= "Database: " . config('database.connections.' . config('database.default'))['database'] . "\n";
        $report .= "Host: " . config('database.connections.' . config('database.default'))['host'] . "\n";
        $report .= "==========================================\n\n";
        
        $report .= "Backup Statistics:\n";
        $report .= "- Total tables: " . count($tables) . "\n";
        $report .= "- Compressed file: " . basename($compressedFile) . "\n";
        $report .= "- File size: " . number_format(filesize($compressedFile) / 1024 / 1024, 2) . " MB\n";
        $report .= "- Location: " . dirname($compressedFile) . "\n\n";
        
        $report .= "Tables backed up:\n";
        foreach ($tables as $index => $table) {
            $recordCount = DB::table($table)->count();
            $report .= sprintf("%2d. %-30s (%d records)\n", $index + 1, $table, $recordCount);
        }
        
        $report .= "\nBackup Status: ✅ SUCCESS\n";
        $report .= "==========================================\n";
        
        file_put_contents($reportPath, $report);
        
        return $reportPath;
    }
} 