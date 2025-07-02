#!/bin/bash

# سكريبت نشر مناسك المشاعر
# تأكد من تشغيل هذا السكريبت في دليل المشروع

echo "🚀 بدء عملية النشر..."

# التحقق من وجود Git
if [ ! -d ".git" ]; then
    echo "❌ خطأ: هذا المشروع غير مبدأ بـ Git"
    exit 1
fi

# تنظيف الكاش
echo "🧹 تنظيف الكاش..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# تثبيت الحزم للإنتاج
echo "📦 تثبيت الحزم..."
composer install --optimize-autoloader --no-dev

# تشغيل الهجرات
echo "🗄️ تشغيل الهجرات..."
php artisan migrate --force

# تجميد الملفات للأداء
echo "⚡ تحسين الأداء..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# إنشاء رابط التخزين
echo "🔗 إنشاء رابط التخزين..."
php artisan storage:link

# تعيين الصلاحيات
echo "🔐 تعيين صلاحيات الملفات..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# تشغيل Supervisor للمهام (إذا كان متوفراً)
if command -v supervisorctl &> /dev/null; then
    echo "🔄 إعادة تشغيل المهام..."
    sudo supervisorctl restart hajj-employment-worker:*
fi

# إعادة تشغيل خدمات الويب
echo "🌐 إعادة تشغيل الخدمات..."
if command -v systemctl &> /dev/null; then
    sudo systemctl reload nginx
    sudo systemctl reload php8.3-fpm
fi

echo "✅ تم النشر بنجاح!"
echo "🌟 الموقع جاهز على: $(php artisan tinker --execute='echo config("app.url");')" 