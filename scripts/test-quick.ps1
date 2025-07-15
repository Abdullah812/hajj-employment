# سكريپت اختبار سريع - مناسك المشاعر
# ===========================================

Clear-Host
Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Magenta
Write-Host "║                    اختبار النظام السريع                     ║" -ForegroundColor Magenta
Write-Host "║                   مناسك المشاعر - الحج                      ║" -ForegroundColor Magenta
Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Magenta
Write-Host ""
Write-Host "📅 التاريخ: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Blue
Write-Host "💻 النظام: $env:COMPUTERNAME" -ForegroundColor Blue
Write-Host "👤 المستخدم: $env:USERNAME" -ForegroundColor Blue
Write-Host ""

# متغيرات
$passed = 0
$failed = 0
$total = 0

# دالة نتيجة الاختبار
function Test-Result {
    param(
        [string]$name,
        [bool]$success,
        [string]$details = ""
    )
    
    $script:total++
    
    if ($success) {
        Write-Host "✅ $name" -ForegroundColor Green
        if ($details) {
            Write-Host "   💡 $details" -ForegroundColor Blue
        }
        $script:passed++
    } else {
        Write-Host "❌ $name" -ForegroundColor Red
        if ($details) {
            Write-Host "   ⚠️  $details" -ForegroundColor Yellow
        }
        $script:failed++
    }
}

Write-Host "🔍 اختبار المتطلبات الأساسية..." -ForegroundColor Cyan
Write-Host ""

# اختبار PHP
try {
    $phpResult = php -v 2>$null
    if ($phpResult) {
        $phpVersion = ($phpResult | Select-String "PHP" | Select-Object -First 1).ToString().Split()[1]
        Test-Result "تثبيت PHP" $true "الإصدار: $phpVersion"
    } else {
        Test-Result "تثبيت PHP" $false "غير مثبت"
    }
} catch {
    Test-Result "تثبيت PHP" $false "غير مثبت"
}

# اختبار Composer
try {
    $composerResult = composer --version 2>$null
    if ($composerResult) {
        $composerVersion = $composerResult.Split()[2]
        Test-Result "تثبيت Composer" $true "الإصدار: $composerVersion"
    } else {
        Test-Result "تثبيت Composer" $false "غير مثبت"
    }
} catch {
    Test-Result "تثبيت Composer" $false "غير مثبت"
}

# اختبار MySQL
try {
    $mysqlResult = mysql --version 2>$null
    if ($mysqlResult) {
        $mysqlVersion = $mysqlResult.Split()[2]
        Test-Result "تثبيت MySQL" $true "الإصدار: $mysqlVersion"
    } else {
        Test-Result "تثبيت MySQL" $false "غير مثبت"
    }
} catch {
    Test-Result "تثبيت MySQL" $false "غير مثبت"
}

Write-Host ""
Write-Host "🔍 اختبار ملفات المشروع..." -ForegroundColor Cyan
Write-Host ""

# اختبار الملفات الأساسية
$files = @(
    "artisan",
    "composer.json",
    ".env",
    "bootstrap\app.php",
    "config\security.php",
    "scripts\backup-system.sh"
)

foreach ($file in $files) {
    $exists = Test-Path $file
    $name = "ملف $(Split-Path $file -Leaf)"
    Test-Result $name $exists
}

Write-Host ""
Write-Host "🔍 اختبار Laravel..." -ForegroundColor Cyan
Write-Host ""

# اختبار Laravel
try {
    $laravelResult = php artisan --version 2>$null
    if ($laravelResult) {
        Test-Result "تشغيل Laravel" $true "$laravelResult"
    } else {
        Test-Result "تشغيل Laravel" $false "فشل في التشغيل"
    }
} catch {
    Test-Result "تشغيل Laravel" $false "فشل في التشغيل"
}

# اختبار قاعدة البيانات
try {
    $dbResult = php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB_OK';" 2>$null
    if ($dbResult -match "DB_OK") {
        Test-Result "اتصال قاعدة البيانات" $true "متصل بنجاح"
    } else {
        Test-Result "اتصال قاعدة البيانات" $false "فشل في الاتصال"
    }
} catch {
    Test-Result "اتصال قاعدة البيانات" $false "فشل في الاتصال"
}

Write-Host ""
Write-Host "📊 النتائج النهائية:" -ForegroundColor Magenta
Write-Host "══════════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "   • إجمالي الاختبارات: $total" -ForegroundColor Blue
Write-Host "   • نجح: $passed" -ForegroundColor Green
Write-Host "   • فشل: $failed" -ForegroundColor Red

if ($total -gt 0) {
    $successRate = [math]::Round(($passed * 100) / $total, 1)
    Write-Host "   • معدل النجاح: $successRate%" -ForegroundColor Magenta
}

Write-Host ""

if ($failed -eq 0) {
    Write-Host "🎉 تهانينا! جميع الاختبارات نجحت" -ForegroundColor Green
    Write-Host "✅ النظام جاهز للاستخدام" -ForegroundColor Green
} elseif ($successRate -gt 70) {
    Write-Host "⚠️  معظم الاختبارات نجحت" -ForegroundColor Yellow
    Write-Host "🔧 يحتاج بعض الإصلاحات البسيطة" -ForegroundColor Yellow
} else {
    Write-Host "❌ يحتاج النظام إلى إصلاحات جوهرية" -ForegroundColor Red
    Write-Host "🛠️  راجع الاختبارات الفاشلة أعلاه" -ForegroundColor Red
}

Write-Host ""
Write-Host "💡 الخطوات التالية:" -ForegroundColor Blue
Write-Host "   1. تشغيل migrations: php artisan migrate" -ForegroundColor Yellow
Write-Host "   2. تشغيل seeders: php artisan db:seed" -ForegroundColor Yellow
Write-Host "   3. تشغيل النظام: php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "   للاختبار الشامل (في Linux): bash scripts/test-system.sh" -ForegroundColor Cyan
Write-Host "" 