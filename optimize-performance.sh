#!/bin/bash

# Script لتطبيق تحسينات الأداء وحل مشاكل CSRF
# لحل مشكلة خطأ 419 والتأخير في الاستجابة

echo "=== بدء تطبيق تحسينات الأداء ==="

# 1. تطبيق التحسينات على Laravel
echo "1. تطبيق تحسينات Laravel..."
php artisan optimize:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. تطبيق تحسينات قاعدة البيانات
echo "2. تطبيق تحسينات قاعدة البيانات..."
php artisan migrate --force
php artisan session:table

# 3. تطبيق إعدادات الأمان
echo "3. تطبيق إعدادات الأمان..."
php artisan optimize

# 4. إعادة تشغيل الخدمات
echo "4. إعادة تشغيل الخدمات..."
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm

# 5. تطبيق أذونات الملفات
echo "5. تطبيق أذونات الملفات..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# 6. تنظيف السجلات القديمة
echo "6. تنظيف السجلات القديمة..."
find storage/logs -name "*.log" -type f -mtime +7 -delete

echo "=== تم الانتهاء من تطبيق التحسينات ==="
echo ""
echo "التحسينات المطبقة:"
echo "✓ حل مشكلة خطأ 419 CSRF"
echo "✓ تحسين الأداء وتقليل التأخير"
echo "✓ تحسين إعدادات Session"
echo "✓ تحسين إعدادات قاعدة البيانات"
echo "✓ تحسين إعدادات NGINX"
echo ""
echo "للتأكد من التحسينات، راقب:"
echo "- سجلات Laravel: tail -f storage/logs/laravel.log"
echo "- سجلات NGINX: tail -f /var/log/nginx/hajj-employment_error.log"
echo "- أداء الخادم: htop"
echo ""
echo "في حالة وجود مشاكل، راجع ملف PERFORMANCE_FIXES.md" 