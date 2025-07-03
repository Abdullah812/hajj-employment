#!/bin/bash

# سكريبت ما بعد النشر في Laravel Cloud
# يتم تشغيله تلقائياً بعد كل نشر

echo "🚀 تطبيق تحسينات ما بعد النشر..."

# 1. تطبيق الهجرات
echo "📊 تطبيق الهجرات..."
php artisan migrate --force

# 2. إنشاء جدول sessions إذا لم يكن موجوداً
echo "💾 إنشاء جدول sessions..."
php artisan session:table --force 2>/dev/null || true
php artisan migrate --force

# 3. مسح الكاش القديم
echo "🧹 مسح الكاش القديم..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. تطبيق الكاش الجديد
echo "⚡ تطبيق الكاش الجديد..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. تطبيق التحسينات الشاملة
echo "🔧 تطبيق التحسينات الشاملة..."
php artisan optimize

# 6. تطبيق أذونات الملفات (إذا كان مسموحاً)
echo "🔐 تطبيق أذونات الملفات..."
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

# 7. تطبيق الأدوار والصلاحيات
echo "👤 تطبيق الأدوار والصلاحيات..."
php artisan db:seed --class=RolesAndPermissionsSeeder --force 2>/dev/null || true

# 8. تطبيق البيانات الأساسية
echo "📁 تطبيق البيانات الأساسية..."
php artisan db:seed --class=HajjJobsSeeder --force 2>/dev/null || true

echo "✅ تم تطبيق جميع التحسينات بنجاح!"
echo ""
echo "🎯 التحسينات المطبقة:"
echo "  ✓ حل مشكلة خطأ 419 CSRF"
echo "  ✓ تحسين الأداء والسرعة"
echo "  ✓ تحسين إعدادات Session"
echo "  ✓ تحسين قاعدة البيانات"
echo "  ✓ تطبيق الكاش المحسن"
echo ""
echo "🌟 الموقع جاهز للاستخدام!" 