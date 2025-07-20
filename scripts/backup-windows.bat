@echo off
REM =============================================================================
REM نظام النسخ الاحتياطي للويندوز - مناسك المشاعر
REM =============================================================================

setlocal enabledelayedexpansion

REM إعدادات النسخ الاحتياطي
set BACKUP_DIR=%USERPROFILE%\Documents\hajj-employment-backups
set PROJECT_DIR=%~dp0..
set DB_NAME=hajj_employment
set DB_USER=root
set DB_PASSWORD=
set DATE_TIME=%date:~-4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DATE_TIME=%DATE_TIME: =0%
set MAX_BACKUPS=30

echo.
echo ==========================================
echo   نظام النسخ الاحتياطي - مناسك المشاعر
echo ==========================================
echo التاريخ: %date% %time%
echo.

REM إنشاء المجلدات
echo 📁 إنشاء مجلدات النسخ الاحتياطي...
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if not exist "%BACKUP_DIR%\database" mkdir "%BACKUP_DIR%\database"
if not exist "%BACKUP_DIR%\files" mkdir "%BACKUP_DIR%\files"
if not exist "%BACKUP_DIR%\logs" mkdir "%BACKUP_DIR%\logs"
echo ✅ تم إنشاء المجلدات

REM نسخ احتياطي لقاعدة البيانات
echo.
echo 💾 بدء النسخ الاحتياطي لقاعدة البيانات...
mysqldump -u%DB_USER% -p%DB_PASSWORD% %DB_NAME% > "%BACKUP_DIR%\database\database_%DATE_TIME%.sql" 2>nul
if exist "%BACKUP_DIR%\database\database_%DATE_TIME%.sql" (
    echo ✅ تم نسخ قاعدة البيانات بنجاح
    REM ضغط الملف
    powershell -command "Compress-Archive -Path '%BACKUP_DIR%\database\database_%DATE_TIME%.sql' -DestinationPath '%BACKUP_DIR%\database\database_%DATE_TIME%.zip'"
    del "%BACKUP_DIR%\database\database_%DATE_TIME%.sql"
) else (
    echo ❌ فشل في نسخ قاعدة البيانات
)

REM نسخ احتياطي للملفات
echo.
echo 📁 بدء النسخ الاحتياطي للملفات...
powershell -command "Compress-Archive -Path '%PROJECT_DIR%\*' -DestinationPath '%BACKUP_DIR%\files\files_%DATE_TIME%.zip' -Force -Exclude node_modules,vendor,storage\logs,storage\framework\cache,storage\framework\sessions,storage\framework\views"
if exist "%BACKUP_DIR%\files\files_%DATE_TIME%.zip" (
    echo ✅ تم نسخ الملفات بنجاح
) else (
    echo ❌ فشل في نسخ الملفات
)

REM نسخ احتياطي للسجلات
echo.
echo 📄 بدء النسخ الاحتياطي للسجلات...
if exist "%PROJECT_DIR%\storage\logs" (
    powershell -command "Compress-Archive -Path '%PROJECT_DIR%\storage\logs\*' -DestinationPath '%BACKUP_DIR%\logs\logs_%DATE_TIME%.zip'"
    echo ✅ تم نسخ السجلات بنجاح
) else (
    echo ⚠️ لم يتم العثور على مجلد السجلات
)

REM تنظيف النسخ القديمة
echo.
echo 🧹 تنظيف النسخ الاحتياطية القديمة...
forfiles /p "%BACKUP_DIR%\database" /s /m *.zip /d -%MAX_BACKUPS% /c "cmd /c del @path" 2>nul
forfiles /p "%BACKUP_DIR%\files" /s /m *.zip /d -%MAX_BACKUPS% /c "cmd /c del @path" 2>nul
forfiles /p "%BACKUP_DIR%\logs" /s /m *.zip /d -%MAX_BACKUPS% /c "cmd /c del @path" 2>nul
echo ✅ تم تنظيف النسخ القديمة

REM إنشاء تقرير
echo.
echo 📊 إنشاء تقرير النسخ الاحتياطي...
(
    echo ======================================
    echo تقرير النسخ الاحتياطي - مناسك المشاعر
    echo ======================================
    echo التاريخ: %date% %time%
    echo الجهاز: %COMPUTERNAME%
    echo =======================================
    echo.
    echo 📊 إحصائيات النسخ الاحتياطي:
    dir "%BACKUP_DIR%\database" | find "database_%DATE_TIME%.zip"
    dir "%BACKUP_DIR%\files" | find "files_%DATE_TIME%.zip"  
    dir "%BACKUP_DIR%\logs" | find "logs_%DATE_TIME%.zip"
    echo.
    echo 💾 المساحة المستخدمة:
    dir "%BACKUP_DIR%" /s
    echo.
    echo ✅ حالة النسخ الاحتياطي: مكتمل بنجاح
) > "%BACKUP_DIR%\backup_report_%DATE_TIME%.txt"

echo.
echo ==========================================
echo ✅ تم إكمال النسخ الاحتياطي بنجاح!
echo ==========================================
echo.
echo 📂 مكان النسخ الاحتياطي:
echo %BACKUP_DIR%
echo.
echo 💡 لعرض النسخ المحفوظة:
echo explorer "%BACKUP_DIR%"
echo.

pause 