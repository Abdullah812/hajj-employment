# =============================================================================
# نظام النسخ الاحتياطي لـ PowerShell - مناسك المشاعر
# =============================================================================

param(
    [string]$Action = "backup",
    [string]$BackupDate = "",
    [string]$DatabasePassword = ""
)

# إعدادات النسخ الاحتياطي
$BackupDir = "$env:USERPROFILE\Documents\hajj-employment-backups"
$ProjectDir = Split-Path -Parent $PSScriptRoot
$DatabaseName = "hajj_employment"
$DatabaseUser = "root"
$DateTime = Get-Date -Format "yyyyMMdd_HHmmss"
$MaxBackups = 30

Write-Host ""
Write-Host "===========================================" -ForegroundColor Green
Write-Host "   نظام النسخ الاحتياطي - مناسك المشاعر" -ForegroundColor Green  
Write-Host "===========================================" -ForegroundColor Green
Write-Host "التاريخ: $(Get-Date)" -ForegroundColor Cyan
Write-Host ""

# دالة إنشاء المجلدات
function Create-BackupDirectories {
    Write-Host "📁 إنشاء مجلدات النسخ الاحتياطي..." -ForegroundColor Yellow
    
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
    
    Write-Host "✅ تم إنشاء المجلدات" -ForegroundColor Green
}

# دالة نسخ احتياطي لقاعدة البيانات
function Backup-Database {
    Write-Host ""
    Write-Host "💾 بدء النسخ الاحتياطي لقاعدة البيانات..." -ForegroundColor Yellow
    
    $SqlFile = "$BackupDir\database\database_$DateTime.sql"
    $ZipFile = "$BackupDir\database\database_$DateTime.zip"
    
    # نسخ قاعدة البيانات
    $mysqldumpPath = "mysqldump"
    $arguments = "-u$DatabaseUser"
    if ($DatabasePassword) {
        $arguments += " -p$DatabasePassword"
    }
    $arguments += " $DatabaseName"
    
    try {
        Invoke-Expression "$mysqldumpPath $arguments" | Out-File -FilePath $SqlFile -Encoding UTF8
        
        if (Test-Path $SqlFile) {
            # ضغط الملف
            Compress-Archive -Path $SqlFile -DestinationPath $ZipFile -Force
            Remove-Item $SqlFile
            
            $fileSize = (Get-Item $ZipFile).Length / 1KB
            Write-Host "✅ تم نسخ قاعدة البيانات بنجاح (الحجم: $([math]::Round($fileSize, 2)) KB)" -ForegroundColor Green
        } else {
            Write-Host "❌ فشل في نسخ قاعدة البيانات" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ خطأ في نسخ قاعدة البيانات: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# دالة نسخ احتياطي للملفات
function Backup-Files {
    Write-Host ""
    Write-Host "📁 بدء النسخ الاحتياطي للملفات..." -ForegroundColor Yellow
    
    $FilesBackup = "$BackupDir\files\files_$DateTime.zip"
    
    try {
        # قائمة الملفات والمجلدات المستثناة
        $ExcludePaths = @(
            "node_modules",
            "vendor", 
            "storage\logs",
            "storage\framework\cache",
            "storage\framework\sessions", 
            "storage\framework\views",
            ".git"
        )
        
        # جمع جميع الملفات ما عدا المستثناة
        $AllFiles = Get-ChildItem -Path $ProjectDir -Recurse | Where-Object {
            $_.FullName -notmatch ($ExcludePaths -join '|')
        }
        
        # ضغط الملفات
        $AllFiles | Compress-Archive -DestinationPath $FilesBackup -Force
        
        if (Test-Path $FilesBackup) {
            $fileSize = (Get-Item $FilesBackup).Length / 1MB
            Write-Host "✅ تم نسخ الملفات بنجاح (الحجم: $([math]::Round($fileSize, 2)) MB)" -ForegroundColor Green
        } else {
            Write-Host "❌ فشل في نسخ الملفات" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ خطأ في نسخ الملفات: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# دالة نسخ احتياطي للسجلات
function Backup-Logs {
    Write-Host ""
    Write-Host "📄 بدء النسخ الاحتياطي للسجلات..." -ForegroundColor Yellow
    
    $LogsPath = "$ProjectDir\storage\logs"
    $LogsBackup = "$BackupDir\logs\logs_$DateTime.zip"
    
    if (Test-Path $LogsPath) {
        try {
            Get-ChildItem -Path $LogsPath | Compress-Archive -DestinationPath $LogsBackup -Force
            Write-Host "✅ تم نسخ السجلات بنجاح" -ForegroundColor Green
        } catch {
            Write-Host "❌ خطأ في نسخ السجلات: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "⚠️ لم يتم العثور على مجلد السجلات" -ForegroundColor Yellow
    }
}

# دالة تنظيف النسخ القديمة
function Cleanup-OldBackups {
    Write-Host ""
    Write-Host "🧹 تنظيف النسخ الاحتياطية القديمة..." -ForegroundColor Yellow
    
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
    
    Write-Host "✅ تم تنظيف النسخ القديمة" -ForegroundColor Green
}

# دالة إنشاء التقرير
function Generate-Report {
    Write-Host ""
    Write-Host "📊 إنشاء تقرير النسخ الاحتياطي..." -ForegroundColor Yellow
    
    $ReportFile = "$BackupDir\backup_report_$DateTime.txt"
    
    $Report = @"
======================================
تقرير النسخ الاحتياطي - مناسك المشاعر
======================================
التاريخ: $(Get-Date)
الجهاز: $env:COMPUTERNAME
المستخدم: $env:USERNAME
=======================================

📊 إحصائيات النسخ الاحتياطي:
"@

    # إضافة معلومات الملفات
    $BackupTypes = @("database", "files", "logs")
    foreach ($type in $BackupTypes) {
        $file = "$BackupDir\$type\${type}_$DateTime.zip"
        if (Test-Path $file) {
            $size = (Get-Item $file).Length
            $sizeKB = [math]::Round($size / 1KB, 2)
            $Report += "`n- نسخة $type`: $sizeKB KB"
        } else {
            $Report += "`n- نسخة $type`: غير متوفر"
        }
    }
    
    # إضافة معلومات المساحة
    $totalSize = (Get-ChildItem -Path $BackupDir -Recurse | Measure-Object -Property Length -Sum).Sum
    $totalSizeMB = [math]::Round($totalSize / 1MB, 2)
    
    $Report += @"

💾 المساحة المستخدمة:
- مجلد النسخ الاحتياطي: $totalSizeMB MB

🗂️ عدد النسخ الاحتياطية:
"@

    foreach ($type in $BackupTypes) {
        $path = "$BackupDir\$type"
        if (Test-Path $path) {
            $count = (Get-ChildItem -Path $path -Filter "*.zip").Count
            $Report += "`n- نسخ $type`: $count"
        }
    }
    
    $Report += "`n`n✅ حالة النسخ الاحتياطي: مكتمل بنجاح"
    
    $Report | Out-File -FilePath $ReportFile -Encoding UTF8
    
    Write-Host "✅ تم إنشاء تقرير النسخ الاحتياطي: $ReportFile" -ForegroundColor Green
}

# دالة عرض النسخ المتاحة
function Show-AvailableBackups {
    Write-Host ""
    Write-Host "📂 النسخ الاحتياطية المتاحة:" -ForegroundColor Cyan
    Write-Host ""
    
    $BackupTypes = @("database", "files", "logs")
    
    foreach ($type in $BackupTypes) {
        $path = "$BackupDir\$type"
        if (Test-Path $path) {
            Write-Host "🗂️ $type`:" -ForegroundColor Yellow
            Get-ChildItem -Path $path -Filter "*.zip" | Sort-Object CreationTime -Descending | Select-Object -First 5 | ForEach-Object {
                $size = [math]::Round($_.Length / 1KB, 2)
                Write-Host "  📅 $($_.Name) ($($_.CreationTime.ToString('yyyy-MM-dd HH:mm:ss'))) - $size KB" -ForegroundColor White
            }
            Write-Host ""
        }
    }
}

# الوظيفة الرئيسية
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
            Write-Host "===========================================" -ForegroundColor Green
            Write-Host "✅ تم إكمال النسخ الاحتياطي بنجاح!" -ForegroundColor Green
            Write-Host "===========================================" -ForegroundColor Green
            Write-Host ""
            Write-Host "📂 مكان النسخ الاحتياطي:" -ForegroundColor Cyan
            Write-Host "$BackupDir" -ForegroundColor White
            Write-Host ""
            Write-Host "💡 لعرض النسخ المحفوظة:" -ForegroundColor Cyan
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
                Write-Host "💾 المساحة الإجمالية المستخدمة: $totalSizeMB MB" -ForegroundColor Cyan
            }
        }
        
        default {
            Write-Host "الاستخدام:" -ForegroundColor Yellow
            Write-Host "  .\backup-windows.ps1 -Action backup" -ForegroundColor White
            Write-Host "  .\backup-windows.ps1 -Action list" -ForegroundColor White  
            Write-Host "  .\backup-windows.ps1 -Action status" -ForegroundColor White
        }
    }
}

# تشغيل البرنامج
Main 