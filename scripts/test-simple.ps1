# Simple System Test Script
# Hajj Employment System

Clear-Host
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "   Hajj Employment System - Quick Test   " -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

$passed = 0
$failed = 0
$total = 0

function Test-Component {
    param(
        [string]$name,
        [bool]$success,
        [string]$details = ""
    )
    
    $script:total++
    
    if ($success) {
        Write-Host "✅ $name" -ForegroundColor Green
        if ($details) {
            Write-Host "   Details: $details" -ForegroundColor Blue
        }
        $script:passed++
    } else {
        Write-Host "❌ $name" -ForegroundColor Red
        if ($details) {
            Write-Host "   Error: $details" -ForegroundColor Yellow
        }
        $script:failed++
    }
}

Write-Host "Testing System Requirements..." -ForegroundColor Yellow
Write-Host ""

# Test PHP
try {
    $phpResult = php -v 2>$null
    if ($phpResult) {
        $phpVersion = ($phpResult | Select-String "PHP" | Select-Object -First 1).ToString().Split()[1]
        Test-Component "PHP Installation" $true "Version: $phpVersion"
    } else {
        Test-Component "PHP Installation" $false "Not installed"
    }
} catch {
    Test-Component "PHP Installation" $false "Not installed"
}

# Test Composer
try {
    $composerResult = composer --version 2>$null
    if ($composerResult) {
        $composerVersion = $composerResult.Split()[2]
        Test-Component "Composer Installation" $true "Version: $composerVersion"
    } else {
        Test-Component "Composer Installation" $false "Not installed"
    }
} catch {
    Test-Component "Composer Installation" $false "Not installed"
}

# Test MySQL
try {
    $mysqlResult = mysql --version 2>$null
    if ($mysqlResult) {
        $mysqlVersion = $mysqlResult.Split()[2]
        Test-Component "MySQL Installation" $true "Version: $mysqlVersion"
    } else {
        Test-Component "MySQL Installation" $false "Not installed"
    }
} catch {
    Test-Component "MySQL Installation" $false "Not installed"
}

Write-Host ""
Write-Host "Testing Project Files..." -ForegroundColor Yellow
Write-Host ""

# Test Essential Files
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
    $name = "File: $(Split-Path $file -Leaf)"
    Test-Component $name $exists
}

Write-Host ""
Write-Host "Testing Laravel..." -ForegroundColor Yellow
Write-Host ""

# Test Laravel
try {
    $laravelResult = php artisan --version 2>$null
    if ($laravelResult) {
        Test-Component "Laravel Framework" $true "$laravelResult"
    } else {
        Test-Component "Laravel Framework" $false "Cannot run artisan"
    }
} catch {
    Test-Component "Laravel Framework" $false "Cannot run artisan"
}

# Test Database Connection
try {
    $dbResult = php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB_OK';" 2>$null
    if ($dbResult -match "DB_OK") {
        Test-Component "Database Connection" $true "Connected successfully"
    } else {
        Test-Component "Database Connection" $false "Connection failed"
    }
} catch {
    Test-Component "Database Connection" $false "Connection failed"
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host "               FINAL RESULTS             " -ForegroundColor Magenta
Write-Host "=========================================" -ForegroundColor Magenta
Write-Host "Total Tests: $total" -ForegroundColor Blue
Write-Host "Passed: $passed" -ForegroundColor Green
Write-Host "Failed: $failed" -ForegroundColor Red

if ($total -gt 0) {
    $successRate = [math]::Round(($passed * 100) / $total, 1)
    Write-Host "Success Rate: $successRate%" -ForegroundColor Magenta
}

Write-Host ""

if ($failed -eq 0) {
    Write-Host "🎉 Congratulations! All tests passed" -ForegroundColor Green
    Write-Host "✅ System is ready to use" -ForegroundColor Green
} elseif ($successRate -gt 70) {
    Write-Host "⚠️  Most tests passed" -ForegroundColor Yellow
    Write-Host "🔧 Minor fixes needed" -ForegroundColor Yellow
} else {
    Write-Host "❌ System needs major fixes" -ForegroundColor Red
    Write-Host "🛠️  Review failed tests above" -ForegroundColor Red
}

Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Blue
Write-Host "1. Run migrations: php artisan migrate" -ForegroundColor Yellow
Write-Host "2. Run seeders: php artisan db:seed" -ForegroundColor Yellow
Write-Host "3. Start server: php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "For comprehensive testing on Linux: bash scripts/test-system.sh" -ForegroundColor Cyan
Write-Host "" 