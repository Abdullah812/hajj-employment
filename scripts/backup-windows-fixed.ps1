# =============================================================================
# Windows Backup System for Hajj Employment
# =============================================================================

param(
    [string]$Action = "backup",
    [string]$DatabasePassword = ""
)

# Backup settings
$BackupDir = "$env:USERPROFILE\Documents\hajj-employment-backups"
$ProjectDir = Split-Path -Parent $PSScriptRoot
$DatabaseName = "hajj_employment"
$DatabaseUser = "root"
$DateTime = Get-Date -Format "yyyyMMdd_HHmmss"
$MaxBackups = 30

Write-Host ""
Write-Host "==========================================="
Write-Host "   Hajj Employment Backup System"
Write-Host "==========================================="
Write-Host "Date: $(Get-Date)"
Write-Host ""

# Create backup directories
function Create-BackupDirectories {
    Write-Host "Creating backup directories..." -ForegroundColor Yellow
    
    $directories = @(
        $BackupDir,
        "$BackupDir\database",
        "$BackupDir\files", 
        "$BackupDir\logs"
    )
    
    foreach ($dir in $directories) {
        if (!(Test-Path $dir)) {
            New-Item -ItemType Directory -Path $dir -Force | Out-Null
        }
    }
    
    Write-Host "Directories created successfully" -ForegroundColor Green
}

# Backup database
function Backup-Database {
    Write-Host ""
    Write-Host "Starting database backup..." -ForegroundColor Yellow
    
    $SqlFile = "$BackupDir\database\database_$DateTime.sql"
    $ZipFile = "$BackupDir\database\database_$DateTime.zip"
    
    try {
        $mysqldumpPath = "mysqldump"
        $arguments = "-u$DatabaseUser"
        if ($DatabasePassword) {
            $arguments += " -p$DatabasePassword"
        }
        $arguments += " $DatabaseName"
        
        $result = Invoke-Expression "$mysqldumpPath $arguments" -ErrorAction Stop
        $result | Out-File -FilePath $SqlFile -Encoding UTF8
        
        if (Test-Path $SqlFile) {
            Compress-Archive -Path $SqlFile -DestinationPath $ZipFile -Force
            Remove-Item $SqlFile
            
            $fileSize = (Get-Item $ZipFile).Length / 1KB
            Write-Host "Database backup completed successfully (Size: $([math]::Round($fileSize, 2)) KB)" -ForegroundColor Green
        } else {
            Write-Host "Database backup failed" -ForegroundColor Red
        }
    } catch {
        Write-Host "Database backup error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Backup files
function Backup-Files {
    Write-Host ""
    Write-Host "Starting files backup..." -ForegroundColor Yellow
    
    $FilesBackup = "$BackupDir\files\files_$DateTime.zip"
    
    try {
        $ExcludePaths = @(
            "node_modules",
            "vendor", 
            "storage\\logs",
            "storage\\framework\\cache",
            "storage\\framework\\sessions", 
            "storage\\framework\\views",
            ".git"
        )
        
        $AllFiles = Get-ChildItem -Path $ProjectDir -Recurse | Where-Object {
            $exclude = $false
            foreach ($excludePath in $ExcludePaths) {
                if ($_.FullName -like "*$excludePath*") {
                    $exclude = $true
                    break
                }
            }
            return !$exclude
        }
        
        $AllFiles | Compress-Archive -DestinationPath $FilesBackup -Force
        
        if (Test-Path $FilesBackup) {
            $fileSize = (Get-Item $FilesBackup).Length / 1MB
            Write-Host "Files backup completed successfully (Size: $([math]::Round($fileSize, 2)) MB)" -ForegroundColor Green
        } else {
            Write-Host "Files backup failed" -ForegroundColor Red
        }
    } catch {
        Write-Host "Files backup error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Backup logs
function Backup-Logs {
    Write-Host ""
    Write-Host "Starting logs backup..." -ForegroundColor Yellow
    
    $LogsPath = "$ProjectDir\storage\logs"
    $LogsBackup = "$BackupDir\logs\logs_$DateTime.zip"
    
    if (Test-Path $LogsPath) {
        try {
            Get-ChildItem -Path $LogsPath | Compress-Archive -DestinationPath $LogsBackup -Force
            Write-Host "Logs backup completed successfully" -ForegroundColor Green
        } catch {
            Write-Host "Logs backup error: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "Logs directory not found" -ForegroundColor Yellow
    }
}

# Cleanup old backups
function Cleanup-OldBackups {
    Write-Host ""
    Write-Host "Cleaning up old backups..." -ForegroundColor Yellow
    
    $CutoffDate = (Get-Date).AddDays(-$MaxBackups)
    $BackupTypes = @("database", "files", "logs")
    
    foreach ($type in $BackupTypes) {
        $path = "$BackupDir\$type"
        if (Test-Path $path) {
            Get-ChildItem -Path $path -Filter "*.zip" | Where-Object { 
                $_.CreationTime -lt $CutoffDate 
            } | Remove-Item -Force
        }
    }
    
    Write-Host "Old backups cleaned up successfully" -ForegroundColor Green
}

# Generate report
function Generate-Report {
    Write-Host ""
    Write-Host "Generating backup report..." -ForegroundColor Yellow
    
    $ReportFile = "$BackupDir\backup_report_$DateTime.txt"
    
    $Report = @"
======================================
Hajj Employment Backup Report
======================================
Date: $(Get-Date)
Computer: $env:COMPUTERNAME
User: $env:USERNAME
=======================================

Backup Statistics:
"@

    $BackupTypes = @("database", "files", "logs")
    foreach ($type in $BackupTypes) {
        $file = "$BackupDir\$type\${type}_$DateTime.zip"
        if (Test-Path $file) {
            $size = (Get-Item $file).Length
            $sizeKB = [math]::Round($size / 1KB, 2)
            $Report += "`n- $type backup: $sizeKB KB"
        } else {
            $Report += "`n- $type backup: Not available"
        }
    }
    
    $totalSize = (Get-ChildItem -Path $BackupDir -Recurse | Measure-Object -Property Length -Sum).Sum
    $totalSizeMB = [math]::Round($totalSize / 1MB, 2)
    
    $Report += @"

Space Usage:
- Total backup size: $totalSizeMB MB

Backup Counts:
"@

    foreach ($type in $BackupTypes) {
        $path = "$BackupDir\$type"
        if (Test-Path $path) {
            $count = (Get-ChildItem -Path $path -Filter "*.zip").Count
            $Report += "`n- $type backups: $count"
        }
    }
    
    $Report += "`n`nBackup Status: Completed Successfully"
    
    $Report | Out-File -FilePath $ReportFile -Encoding UTF8
    
    Write-Host "Backup report generated: $ReportFile" -ForegroundColor Green
}

# Show available backups
function Show-AvailableBackups {
    Write-Host ""
    Write-Host "Available Backups:" -ForegroundColor Cyan
    Write-Host ""
    
    $BackupTypes = @("database", "files", "logs")
    
    foreach ($type in $BackupTypes) {
        $path = "$BackupDir\$type"
        if (Test-Path $path) {
            Write-Host "$type backups:" -ForegroundColor Yellow
            Get-ChildItem -Path $path -Filter "*.zip" | Sort-Object CreationTime -Descending | Select-Object -First 5 | ForEach-Object {
                $size = [math]::Round($_.Length / 1KB, 2)
                Write-Host "  $($_.Name) ($($_.CreationTime.ToString('yyyy-MM-dd HH:mm:ss'))) - $size KB" -ForegroundColor White
            }
            Write-Host ""
        }
    }
}

# Main function
function Main {
    switch ($Action.ToLower()) {
        "backup" {
            Create-BackupDirectories
            Backup-Database
            Backup-Files  
            Backup-Logs
            Cleanup-OldBackups
            Generate-Report
            
            Write-Host ""
            Write-Host "==========================================="
            Write-Host "Backup completed successfully!" -ForegroundColor Green
            Write-Host "==========================================="
            Write-Host ""
            Write-Host "Backup location:" -ForegroundColor Cyan
            Write-Host "$BackupDir" -ForegroundColor White
            Write-Host ""
            Write-Host "To view backups:" -ForegroundColor Cyan
            Write-Host "explorer `"$BackupDir`"" -ForegroundColor White
        }
        
        "list" {
            Show-AvailableBackups
        }
        
        "status" {
            Show-AvailableBackups
            if (Test-Path $BackupDir) {
                $totalSize = (Get-ChildItem -Path $BackupDir -Recurse | Measure-Object -Property Length -Sum).Sum
                $totalSizeMB = [math]::Round($totalSize / 1MB, 2)
                Write-Host "Total space used: $totalSizeMB MB" -ForegroundColor Cyan
            }
        }
        
        default {
            Write-Host "Usage:" -ForegroundColor Yellow
            Write-Host "  .\backup-windows-fixed.ps1 -Action backup" -ForegroundColor White
            Write-Host "  .\backup-windows-fixed.ps1 -Action list" -ForegroundColor White  
            Write-Host "  .\backup-windows-fixed.ps1 -Action status" -ForegroundColor White
        }
    }
}

# Run the program
Main 