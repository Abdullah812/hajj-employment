# نظام إدارة توظيف الحج - مناسك المشاعر

نظام شامل لإدارة التوظيف في موسم الحج، مطور باستخدام Laravel وأحدث تقنيات الويب.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.3+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## المميزات الرئيسية

### 🧑‍💼 إدارة المستخدمين
- نظام أدوار متقدم (مشرفين، شركات، موظفين)
- تسجيل دخول آمن مع جلسات محمية
- إدارة ملفات شخصية مفصلة

### 💼 إدارة الوظائف
- نشر الوظائف مع تفاصيل شاملة
- نظام تقديم الطلبات المتقدم
- مراجعة وقبول الطلبات
- تتبع حالة التطبيقات

### 📋 إدارة العقود
- توليد عقود PDF تلقائياً
- توقيع إلكتروني للعقود
- تتبع حالة العقود
- أرشفة آمنة للوثائق

### 🔔 نظام الإشعارات المتطور
- إشعارات فورية في الوقت الفعلي
- دعم إرسال البريد الإلكتروني
- تصنيف وفلترة الإشعارات
- إحصائيات مفصلة

### 🌐 دعم اللغة العربية
- واجهة مستخدم عربية كاملة
- اتجاه RTL محسن
- تخصيص ثقافي للمنطقة

### 📊 التقارير والتحليلات
- تقارير شاملة عن التوظيف
- إحصائيات الأداء
- تحليلات تفاعلية

## متطلبات النظام

- **PHP**: 8.3 أو أحدث
- **قاعدة البيانات**: MySQL 8.0+ أو MariaDB 10.5+
- **Composer**: 2.0+
- **Node.js**: 18+ مع NPM
- **Redis**: موصى به للكاش والجلسات
- **خادم ويب**: Nginx أو Apache

## التثبيت السريع

### 1. استنساخ المشروع
```bash
git clone https://github.com/your-username/hajj-employment.git
cd hajj-employment
```

### 2. تثبيت التبعيات
```bash
composer install
npm install
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hajj_employment
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. تشغيل الهجرات والبذور
```bash
php artisan migrate --seed
```

### 6. إنشاء روابط التخزين
```bash
php artisan storage:link
```

### 7. تجميع الأصول
```bash
npm run dev
# أو للإنتاج
npm run build
```

### 8. تشغيل الخادم
```bash
php artisan serve
```

## المستخدمون الافتراضيون

بعد تشغيل البذور، يمكنك تسجيل الدخول بـ:

- **مشرف**: admin@example.com / password
- **شركة**: company@example.com / password  
- **موظف**: employee@example.com / password

## النشر في الإنتاج

### للنشر السريع
اتبع دليل [النشر السريع](QUICK_DEPLOY.md) لنشر الموقع في 15-20 دقيقة.

### للنشر المفصل
راجع دليل [النشر الشامل](DEPLOYMENT.md) للإعدادات المتقدمة والأمان.

### باستخدام Docker
```bash
docker build -t hajj-employment .
docker run -p 80:80 hajj-employment
```

## الهيكل التقني

### Backend
- **Framework**: Laravel 11.x
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Document Generation**: Word documents
- **Queue System**: Redis + Supervisor

### Frontend  
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS + Alpine.js
- **Icons**: FontAwesome
- **Real-time**: Livewire

### Database
- **Primary**: MySQL 8.0+
- **Cache**: Redis
- **Sessions**: Redis/Database
- **File Storage**: Local/S3

## المساهمة

نرحب بالمساهمات! يرجى:

1. Fork المشروع
2. إنشاء branch للميزة الجديدة
3. Commit التغييرات
4. Push إلى Branch
5. إنشاء Pull Request

## الأمان

إذا اكتشفت مشكلة أمنية، يرجى إرسال بريد إلكتروني إلى:
security@hajj-employment.com

## الدعم الفني

- **التوثيق**: [docs.hajj-employment.com](https://docs.hajj-employment.com)
- **Issues**: [GitHub Issues](https://github.com/your-username/hajj-employment/issues)
- **المنتدى**: [community.hajj-employment.com](https://community.hajj-employment.com)

## الترخيص

هذا المشروع مرخص تحت رخصة MIT. راجع ملف [LICENSE](LICENSE) للتفاصيل.

## شكر وتقدير

- فريق Laravel للإطار الممتاز
- مجتمع المطورين العرب
- جميع المساهمين في المشروع

---

**تم تطويره بـ ❤️ للمجتمع العربي**
