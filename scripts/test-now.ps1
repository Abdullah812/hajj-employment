# Quick Test Script - Hajj Employment System
Clear-Host
Write-Host "Hajj Employment System - Quick Test" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

$passed = 0
$failed = 0

function Test-Item {
    param([string]$name, [bool]$success, [string]$info = "")
    
    if ($success) {
        Write-Host "✅ $name" -ForegroundColor Green
        if ($info) { Write-Host "   $info" -ForegroundColor Blue }
        $script:passed++
    } else {
        Write-Host "❌ $name" -ForegroundColor Red
        if ($info) { Write-Host "   $info" -ForegroundColor Yellow }
        $script:failed++
    }
}

Write-Host "Testing System Requirements..." -ForegroundColor Yellow
Write-Host ""

# Test PHP
try {
    $phpResult = php -v 2>$null
    if ($phpResult) {
        $version = ($phpResult | Select-String "PHP").ToString().Split()[1]
        Test-Item "PHP Installation" $true "Version: $version"
    } else {
        Test-Item "PHP Installation" $false "Not found"
    }
} catch {
    Test-Item "PHP Installation" $false "Not found"
}

# Test Composer
try {
    $composerResult = composer --version 2>$null
    if ($composerResult) {
        $version = $composerResult.Split()[2]
        Test-Item "Composer Installation" $true "Version: $version"
    } else {
        Test-Item "Composer Installation" $false "Not found"
    }
} catch {
    Test-Item "Composer Installation" $false "Not found"
}

Write-Host ""
Write-Host "Testing Project Files..." -ForegroundColor Yellow
Write-Host ""

# Test files
$files = @("artisan", "composer.json", ".env", "bootstrap\app.php", "config\security.php")
foreach ($file in $files) {
    $exists = Test-Path $file
    Test-Item "File: $(Split-Path $file -Leaf)" $exists
}

Write-Host ""
Write-Host "Testing Laravel..." -ForegroundColor Yellow
Write-Host ""

# Test Laravel
try {
    $laravelResult = php artisan --version 2>$null
    if ($laravelResult) {
        Test-Item "Laravel Framework" $true "$laravelResult"
    } else {
        Test-Item "Laravel Framework" $false "Cannot run artisan"
    }
} catch {
    Test-Item "Laravel Framework" $false "Cannot run artisan"
}

# Test Database
try {
    $dbResult = php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>$null
    if ($dbResult -match "OK") {
        Test-Item "Database Connection" $true "Connected"
    } else {
        Test-Item "Database Connection" $false "Failed"
    }
} catch {
    Test-Item "Database Connection" $false "Failed"
}

Write-Host ""
Write-Host "Results:" -ForegroundColor Magenta
Write-Host "Passed: $passed" -ForegroundColor Green
Write-Host "Failed: $failed" -ForegroundColor Red

$total = $passed + $failed
if ($total -gt 0) {
    $rate = [math]::Round(($passed * 100) / $total, 1)
    Write-Host "Success Rate: $rate%" -ForegroundColor Magenta
}

Write-Host ""
if ($failed -eq 0) {
    Write-Host "All tests passed! System is ready." -ForegroundColor Green
} else {
    Write-Host "Some tests failed. Please review and fix." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Blue
Write-Host "1. php artisan migrate" -ForegroundColor Yellow
Write-Host "2. php artisan db:seed" -ForegroundColor Yellow
Write-Host "3. php artisan serve" -ForegroundColor Yellow 