# =============================================================================
# سكريپت الاختبار الشامل - PowerShell
# تشغيل جميع اختبارات النظام
# =============================================================================

param(
    [string]$Mode = "full",  # full, quick, database
    [switch]$Help
)

# الألوان
$Red = "Red"
$Green = "Green"
$Yellow = "Yellow"
$Blue = "Blue"
$Magenta = "Magenta"
$Cyan = "Cyan"

# متغيرات النظام
$ProjectDir = "C:\Users\Manask-2\hajj-employment"
$TestResults = @()
$TotalTests = 0
$PassedTests = 0
$FailedTests = 0

# دالة طباعة العنوان
function Write-Header {
    param([string]$Title)
    
    Write-Host "================================" -ForegroundColor $Cyan
    Write-Host $Title -ForegroundColor $Cyan
    Write-Host "================================" -ForegroundColor $Cyan
    Write-Host ""
}

# دالة طباعة نتيجة الاختبار
function Write-TestResult {
    param(
        [string]$TestName,
        [string]$Result,
        [string]$Details = ""
    )
    
    $script:TotalTests++
    
    if ($Result -eq "PASS") {
        Write-Host "✅ [PASS] $TestName" -ForegroundColor $Green
        if ($Details) {
            Write-Host "   💡 $Details" -ForegroundColor $Blue
        }
        $script:PassedTests++
        $script:TestResults += "PASS: $TestName"
    } else {
        Write-Host "❌ [FAIL] $TestName" -ForegroundColor $Red
        if ($Details) {
            Write-Host "   ⚠️  $Details" -ForegroundColor $Red
        }
        $script:FailedTests++
        $script:TestResults += "FAIL: $TestName - $Details"
    }
    Write-Host ""
}

# دالة اختبار متطلبات النظام
function Test-SystemRequirements {
    Write-Header "اختبار متطلبات النظام"
    
    # اختبار PHP
    try {
        $phpVersion = php -v | Select-String "PHP" | ForEach-Object { $_.ToString().Split()[1] }
        Write-TestResult "تثبيت PHP" "PASS" "الإصدار: $phpVersion"
    } catch {
        Write-TestResult "تثبيت PHP" "FAIL" "PHP غير مثبت"
    }
    
    # اختبار Composer
    try {
        $composerVersion = composer --version | ForEach-Object { $_.ToString().Split()[2] }
        Write-TestResult "تثبيت Composer" "PASS" "الإصدار: $composerVersion"
    } catch {
        Write-TestResult "تثبيت Composer" "FAIL" "Composer غير مثبت"
    }
    
    # اختبار MySQL
    try {
        $mysqlVersion = mysql --version | ForEach-Object { $_.ToString().Split()[2] }
        Write-TestResult "تثبيت MySQL" "PASS" "الإصدار: $mysqlVersion"
    } catch {
        Write-TestResult "تثبيت MySQL" "FAIL" "MySQL غير مثبت"
    }
}

# دالة اختبار ملفات المشروع
function Test-ProjectFiles {
    Write-Header "اختبار ملفات المشروع"
    
    # التحقق من مجلد المشروع
    if (Test-Path $ProjectDir) {
        Write-TestResult "مجلد المشروع" "PASS" "$ProjectDir موجود"
    } else {
        Write-TestResult "مجلد المشروع" "FAIL" "$ProjectDir غير موجود"
    }
    
    # التحقق من الملفات الأساسية
    $essentialFiles = @(
        "artisan",
        "composer.json",
        ".env",
        "bootstrap\app.php"
    )
    
    foreach ($file in $essentialFiles) {
        $filePath = Join-Path $ProjectDir $file
        if (Test-Path $filePath) {
            Write-TestResult "ملف $file" "PASS" "موجود"
        } else {
            Write-TestResult "ملف $file" "FAIL" "غير موجود"
        }
    }
}

# دالة اختبار Laravel
function Test-LaravelEnvironment {
    Write-Header "اختبار بيئة Laravel"
    
    Set-Location $ProjectDir
    
    # اختبار ملف .env
    if (Test-Path ".env") {
        $envContent = Get-Content ".env"
        
        # اختبار APP_KEY
        $appKey = $envContent | Select-String "APP_KEY=base64:"
        if ($appKey) {
            Write-TestResult "مفتاح التطبيق" "PASS" "مُعيّن بشكل صحيح"
        } else {
            Write-TestResult "مفتاح التطبيق" "FAIL" "غير مُعيّن أو خاطئ"
        }
        
        # اختبار إعدادات قاعدة البيانات
        $dbConnection = $envContent | Select-String "DB_CONNECTION=mysql"
        if ($dbConnection) {
            Write-TestResult "إعدادات قاعدة البيانات" "PASS" "MySQL مُكوّن"
        } else {
            Write-TestResult "إعدادات قاعدة البيانات" "FAIL" "لم يتم تكوين MySQL"
        }
    } else {
        Write-TestResult "ملف .env" "FAIL" "غير موجود"
    }
    
    # اختبار اتصال قاعدة البيانات
    try {
        $dbTest = php artisan tinker --execute="DB::connection()->getPdo(); echo 'connected';" 2>$null
        if ($dbTest -match "connected") {
            Write-TestResult "اتصال قاعدة البيانات" "PASS" "يعمل بشكل صحيح"
        } else {
            Write-TestResult "اتصال قاعدة البيانات" "FAIL" "فشل في الاتصال"
        }
    } catch {
        Write-TestResult "اتصال قاعدة البيانات" "FAIL" "فشل في الاتصال"
    }
}

# دالة اختبار الأمان
function Test-SecuritySystem {
    Write-Header "اختبار نظام الأمان"
    
    Set-Location $ProjectDir
    
    # التحقق من middleware الأمان
    $bootstrapContent = Get-Content "bootstrap\app.php" -Raw
    if ($bootstrapContent -match "SecurityMonitor") {
        Write-TestResult "Security Middleware" "PASS" "مُفعّل في bootstrap/app.php"
    } else {
        Write-TestResult "Security Middleware" "FAIL" "غير مُفعّل"
    }
    
    # التحقق من ملف إعدادات الأمان
    if (Test-Path "config\security.php") {
        Write-TestResult "إعدادات الأمان" "PASS" "ملف config/security.php موجود"
    } else {
        Write-TestResult "إعدادات الأمان" "FAIL" "ملف config/security.php غير موجود"
    }
    
    # التحقق من قناة security في التسجيل
    $loggingContent = Get-Content "config\logging.php" -Raw
    if ($loggingContent -match "security") {
        Write-TestResult "قناة تسجيل الأمان" "PASS" "مُكوّنة في logging.php"
    } else {
        Write-TestResult "قناة تسجيل الأمان" "FAIL" "غير مُكوّنة"
    }
}

# دالة اختبار النسخ الاحتياطي
function Test-BackupSystem {
    Write-Header "اختبار نظام النسخ الاحتياطي"
    
    # التحقق من وجود سكريپت النسخ الاحتياطي
    if (Test-Path "scripts\backup-system.sh") {
        Write-TestResult "سكريپت النسخ الاحتياطي" "PASS" "موجود في scripts/"
    } else {
        Write-TestResult "سكريپت النسخ الاحتياطي" "FAIL" "غير موجود"
    }
    
    # التحقق من سكريپت إعداد cron
    if (Test-Path "scripts\backup-cron.sh") {
        Write-TestResult "سكريپت إعداد cron" "PASS" "موجود في scripts/"
    } else {
        Write-TestResult "سكريپت إعداد cron" "FAIL" "غير موجود"
    }
}

# دالة الاختبار السريع
function Test-Quick {
    Write-Header "🚀 اختبار سريع للميزات الأساسية"
    
    Set-Location $ProjectDir
    
    # اختبار Laravel
    try {
        $laravelVersion = php artisan --version
        Write-Host "✅ Laravel يعمل" -ForegroundColor $Green
        Write-Host "   💡 $laravelVersion" -ForegroundColor $Blue
    } catch {
        Write-Host "❌ Laravel لا يعمل" -ForegroundColor $Red
    }
    
    # اختبار قاعدة البيانات
    try {
        $dbTest = php artisan tinker --execute="DB::connection()->getPdo();" 2>$null
        if ($dbTest -notmatch "error") {
            Write-Host "✅ قاعدة البيانات متصلة" -ForegroundColor $Green
        } else {
            Write-Host "❌ قاعدة البيانات غير متصلة" -ForegroundColor $Red
        }
    } catch {
        Write-Host "❌ قاعدة البيانات غير متصلة" -ForegroundColor $Red
    }
    
    # اختبار النسخ الاحتياطي
    if (Test-Path "scripts\backup-system.sh") {
        Write-Host "✅ نظام النسخ الاحتياطي موجود" -ForegroundColor $Green
    } else {
        Write-Host "❌ نظام النسخ الاحتياطي غير موجود" -ForegroundColor $Red
    }
    
    # اختبار الأمان
    if (Test-Path "config\security.php") {
        Write-Host "✅ نظام الأمان مُكوّن" -ForegroundColor $Green
    } else {
        Write-Host "❌ نظام الأمان غير مُكوّن" -ForegroundColor $Red
    }
    
    Write-Host ""
}

# دالة طباعة التقرير النهائي
function Write-FinalReport {
    Write-Header "التقرير النهائي"
    
    Write-Host "📊 إحصائيات الاختبار:" -ForegroundColor $Magenta
    Write-Host "   • إجمالي الاختبارات: $TotalTests" -ForegroundColor $Blue
    Write-Host "   • الاختبارات الناجحة: $PassedTests" -ForegroundColor $Green
    Write-Host "   • الاختبارات الفاشلة: $FailedTests" -ForegroundColor $Red
    
    if ($TotalTests -gt 0) {
        $successRate = [math]::Round(($PassedTests * 100) / $TotalTests, 1)
        Write-Host "   • معدل النجاح: $successRate%" -ForegroundColor $Magenta
    }
    
    Write-Host ""
    
    if ($FailedTests -eq 0) {
        Write-Host "🎉 تهانينا! جميع الاختبارات نجحت" -ForegroundColor $Green
        Write-Host "✅ النظام جاهز للاستخدام" -ForegroundColor $Green
    } elseif ($successRate -gt 80) {
        Write-Host "⚠️  معظم الاختبارات نجحت" -ForegroundColor $Yellow
        Write-Host "🔧 يحتاج بعض الإصلاحات البسيطة" -ForegroundColor $Yellow
    } else {
        Write-Host "❌ يحتاج النظام إلى إصلاحات" -ForegroundColor $Red
        Write-Host "🛠️  راجع الاختبارات الفاشلة أعلاه" -ForegroundColor $Red
    }
    
    Write-Host ""
    Write-Host "📝 الاختبارات الفاشلة:" -ForegroundColor $Cyan
    foreach ($result in $TestResults) {
        if ($result.StartsWith("FAIL")) {
            Write-Host "   • $result" -ForegroundColor $Red
        }
    }
    
    Write-Host ""
    Write-Host "💡 التوصيات:" -ForegroundColor $Blue
    
    if ($FailedTests -gt 0) {
        Write-Host "   1. راجع الاختبارات الفاشلة وقم بإصلاحها" -ForegroundColor $Yellow
        Write-Host "   2. تأكد من تشغيل: php artisan migrate" -ForegroundColor $Yellow
        Write-Host "   3. تأكد من إعداد نظام النسخ الاحتياطي" -ForegroundColor $Yellow
    } else {
        Write-Host "   1. النظام يعمل بشكل ممتاز" -ForegroundColor $Green
        Write-Host "   2. يمكن المتابعة مع التحسينات التالية" -ForegroundColor $Green
    }
}

# دالة المساعدة
function Show-Help {
    Write-Host "اختبار النظام الشامل - مناسك المشاعر" -ForegroundColor $Cyan
    Write-Host ""
    Write-Host "الاستخدام:" -ForegroundColor $Yellow
    Write-Host "  .\scripts\test-all.ps1 [OPTIONS]" -ForegroundColor $Blue
    Write-Host ""
    Write-Host "الخيارات:" -ForegroundColor $Yellow
    Write-Host "  -Mode quick     اختبار سريع للميزات الأساسية" -ForegroundColor $Blue
    Write-Host "  -Mode full      اختبار شامل (الافتراضي)" -ForegroundColor $Blue
    Write-Host "  -Mode database  اختبار قاعدة البيانات فقط" -ForegroundColor $Blue
    Write-Host "  -Help          عرض هذه المساعدة" -ForegroundColor $Blue
    Write-Host ""
    Write-Host "أمثلة:" -ForegroundColor $Yellow
    Write-Host "  .\scripts\test-all.ps1                    # اختبار شامل" -ForegroundColor $Blue
    Write-Host "  .\scripts\test-all.ps1 -Mode quick        # اختبار سريع" -ForegroundColor $Blue
    Write-Host "  .\scripts\test-all.ps1 -Mode database     # اختبار قاعدة البيانات" -ForegroundColor $Blue
}

# الدالة الرئيسية
function Main {
    Clear-Host
    
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor $Magenta
    Write-Host "║                    اختبار النظام الشامل                     ║" -ForegroundColor $Magenta
    Write-Host "║                   مناسك المشاعر - الحج                      ║" -ForegroundColor $Magenta
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor $Magenta
    Write-Host ""
    Write-Host "📅 التاريخ: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor $Blue
    Write-Host "💻 النظام: $env:COMPUTERNAME" -ForegroundColor $Blue
    Write-Host "👤 المستخدم: $env:USERNAME" -ForegroundColor $Blue
    Write-Host ""
    
    if ($Help) {
        Show-Help
        return
    }
    
    switch ($Mode) {
        "quick" {
            Test-Quick
        }
        "database" {
            Write-Host "⚠️  اختبار قاعدة البيانات متوفر في Linux فقط" -ForegroundColor $Yellow
            Write-Host "استخدم: bash scripts/test-database.sh" -ForegroundColor $Blue
        }
        "full" {
            Test-SystemRequirements
            Test-ProjectFiles
            Test-LaravelEnvironment
            Test-SecuritySystem
            Test-BackupSystem
            Write-FinalReport
        }
        default {
            Write-Host "وضع غير معروف: $Mode" -ForegroundColor $Red
            Show-Help
        }
    }
    
    Write-Host ""
    Write-Host "تم حفظ تقرير الاختبار في: $env:TEMP\hajj-system-test-$(Get-Date -Format 'yyyyMMdd_HHmmss').log" -ForegroundColor $Green
}

# تشغيل الاختبار
Main 