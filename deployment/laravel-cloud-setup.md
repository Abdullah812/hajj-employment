# دليل نشر مناسك المشاعر على Laravel Cloud

## الخطوة 1: إعداد Git Repository

### 1. إنشاء مستودع على GitHub
1. اذهب إلى [GitHub](https://github.com)
2. انقر على "New repository"
3. اسم المستودع: `hajj-employment`
4. اجعله Public للتجربة المجانية
5. لا تضف README (لديك مشروع موجود)

### 2. ربط المشروع المحلي بـ GitHub
```bash
# في مجلد المشروع
git init
git add .
git commit -m "إضافة نظام مناسك المشاعر الكامل"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/hajj-employment.git
git push -u origin main
```

## الخطوة 2: إنشاء حساب Laravel Cloud

1. اذهب إلى [Laravel Cloud](https://cloud.laravel.com)
2. انقر على "Sign up"
3. استخدم حساب GitHub للتسجيل
4. اختر خطة "Sandbox" (مجانية)

## الخطوة 3: نشر التطبيق

### 1. ربط Git Repository
1. في لوحة تحكم Laravel Cloud
2. انقر على "New Application"
3. اختر GitHub كمزود Git
4. اختر مستودع `hajj-employment`
5. اتبع الإرشادات

### 2. إعداد قاعدة البيانات
Laravel Cloud سينشئ قاعدة بيانات MySQL تلقائياً.

### 3. إعداد متغيرات البيئة
أضف هذه المتغيرات في إعدادات البيئة:

```env
APP_NAME="مناسك المشاعر"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Riyadh

APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar
APP_FAKER_LOCALE=ar_SA

LOG_LEVEL=error

QUEUE_CONNECTION=database
CACHE_STORE=redis
SESSION_DRIVER=redis

# إعدادات البريد الإلكتروني (اختيارية)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@hajj-employment.com"
MAIL_FROM_NAME="مناسك المشاعر"

# إعدادات PDF
PDF_DEFAULT_FONT=DejaVu Sans
PDF_DEFAULT_PAPER_SIZE=A4

# إعدادات الإشعارات
NOTIFICATION_QUEUE_ENABLED=true
NOTIFICATION_EMAIL_ENABLED=false
```

## الخطوة 4: تشغيل Migration

بعد النشر، في لوحة تحكم Laravel Cloud:
1. اذهب إلى "Commands"
2. شغل: `php artisan migrate --force`
3. شغل: `php artisan db:seed --force`

## الخطوة 5: الاختبار

1. افتح رابط التطبيق من Laravel Cloud
2. تأكد من:
   - تحميل الصفحة الرئيسية
   - عمل تسجيل الدخول
   - عمل نظام الإشعارات
   - إنشاء PDF للعقود

## الخطوة 6: إعداد نطاق مخصص (اختياري)

إذا كان لديك نطاق خاص:
1. في إعدادات التطبيق
2. أضف "Custom Domain"
3. اتبع إرشادات DNS

## نصائح مهمة:

### الأمان
- Laravel Cloud يدير SSL تلقائياً
- جدار الحماية مُفعل افتراضياً
- النسخ الاحتياطية تلقائية

### الأداء  
- التوسع التلقائي مُفعل
- CDN مُدمج
- Redis للكاش والجلسات

### التكلفة
- خطة Sandbox مجانية للاستخدام المنخفض
- تدفع فقط مقابل الاستخدام الفعلي
- hibernation تلقائي لتوفير التكلفة

## الدعم والصيانة

- Laravel Cloud يدير التحديثات تلقائياً
- مراقبة الأداء مُدمجة
- سجلات الأخطاء متاحة في اللوحة
- دعم فني متاح 24/7 