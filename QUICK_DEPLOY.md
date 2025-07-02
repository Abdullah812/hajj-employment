# نشر سريع - مناسك المشاعر 🚀

## للنشر السريع على خادم Ubuntu

### 1. تحضير الخادم (5 دقائق)
```bash
# تحديث النظام وتثبيت المطلوبات
sudo apt update && sudo apt upgrade -y
sudo apt install nginx mysql-server php8.3-fpm php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml php8.3-zip php8.3-curl php8.3-gd redis-server supervisor git -y

# تثبيت Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. إعداد قاعدة البيانات (2 دقيقة)
```bash
sudo mysql -e "
CREATE DATABASE hajj_employment_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hajj_user'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT ALL PRIVILEGES ON hajj_employment_production.* TO 'hajj_user'@'localhost';
FLUSH PRIVILEGES;
"
```

### 3. رفع المشروع (3 دقائق)
```bash
# استنساخ المشروع
cd /var/www
sudo git clone YOUR_REPO_URL hajj-employment
cd hajj-employment

# إعداد الصلاحيات
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### 4. إعداد Laravel (3 دقائق)
```bash
# نسخ وتعديل ملف البيئة
cp .env.example .env

# تعديل ملف .env بالإعدادات الصحيحة
nano .env
```

**ملف .env المطلوب:**
```env
APP_NAME="مناسك المشاعر"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_LOCALE=ar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=hajj_employment_production
DB_USERNAME=hajj_user
DB_PASSWORD=SecurePassword123!

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

```bash
# إعداد Laravel
php artisan key:generate
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. إعداد Nginx (2 دقيقة)
```bash
# نسخ إعدادات Nginx
sudo cp nginx.conf /etc/nginx/sites-available/hajj-employment
sudo ln -s /etc/nginx/sites-available/hajj-employment /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# اختبار وإعادة تشغيل
sudo nginx -t
sudo systemctl restart nginx
```

### 6. إعداد Supervisor (1 دقيقة)
```bash
sudo cp supervisor.conf /etc/supervisor/conf.d/hajj-employment.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start hajj-employment-worker:*
```

### 7. تفعيل SSL (3 دقائق)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com
```

## ✅ تم! الموقع جاهز

### اختبار سريع:
- افتح المتصفح واذهب إلى `https://yourdomain.com`
- سجل دخول بحساب إداري
- تحقق من عمل الإشعارات

### مراقبة سريعة:
```bash
# حالة الخدمات
sudo systemctl status nginx php8.3-fpm mysql redis
sudo supervisorctl status

# سجلات الأخطاء
tail -f storage/logs/laravel.log
```

---

**ملاحظة**: استبدل `yourdomain.com` و `YOUR_REPO_URL` و `SecurePassword123!` بالقيم الصحيحة

**وقت النشر الإجمالي**: 15-20 دقيقة 🕐 