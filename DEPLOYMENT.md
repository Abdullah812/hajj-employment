# دليل نشر مناسك المشاعر

## متطلبات الخادم

### البرامج المطلوبة
- Ubuntu 20.04+ أو CentOS 8+
- PHP 8.3+
- MySQL 8.0+ أو MariaDB 10.5+
- Nginx 1.18+
- Redis 6.0+
- Composer 2.0+
- Node.js 18+ (للـ frontend)
- Supervisor
- Git

### إضافات PHP المطلوبة
```bash
sudo apt install php8.3-fpm php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml php8.3-zip php8.3-curl php8.3-gd php8.3-intl
```

## خطوات النشر

### 1. إعداد الخادم

#### تثبيت LEMP Stack
```bash
# تحديث النظام
sudo apt update && sudo apt upgrade -y

# تثبيت Nginx
sudo apt install nginx -y

# تثبيت MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# تثبيت PHP 8.3
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.3-fpm php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml php8.3-zip php8.3-curl php8.3-gd php8.3-intl -y

# تثبيت Redis
sudo apt install redis-server -y

# تثبيت Supervisor
sudo apt install supervisor -y

# تثبيت Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. إعداد قاعدة البيانات

```sql
-- إنشاء قاعدة البيانات والمستخدم
CREATE DATABASE hajj_employment_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hajj_user'@'localhost' IDENTIFIED BY 'كلمة_مرور_قوية';
GRANT ALL PRIVILEGES ON hajj_employment_production.* TO 'hajj_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. رفع الملفات

```bash
# إنشاء دليل المشروع
sudo mkdir -p /var/www/hajj-employment
sudo chown $USER:www-data /var/www/hajj-employment

# استنساخ المشروع
cd /var/www
git clone YOUR_REPOSITORY_URL hajj-employment
cd hajj-employment

# تعيين الصلاحيات
sudo chown -R www-data:www-data /var/www/hajj-employment
sudo chmod -R 755 /var/www/hajj-employment
sudo chmod -R 775 /var/www/hajj-employment/storage
sudo chmod -R 775 /var/www/hajj-employment/bootstrap/cache
```

### 4. إعداد المشروع

```bash
# نسخ ملف البيئة
cp .env.example .env

# تعديل ملف .env
sudo nano .env
```

#### ملف .env للإنتاج:
```env
APP_NAME="مناسك المشاعر"
APP_ENV=production
APP_KEY=base64:GENERATE_THIS_KEY
APP_DEBUG=false
APP_TIMEZONE=Asia/Riyadh
APP_URL=https://yourdomain.com
APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hajj_employment_production
DB_USERNAME=hajj_user
DB_PASSWORD=كلمة_المرور_القوية

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="مناسك المشاعر"

LOG_CHANNEL=daily
LOG_LEVEL=error
LOG_DAILY_DAYS=14

SESSION_LIFETIME=120
SESSION_ENCRYPT=true
```

```bash
# توليد مفتاح التطبيق
php artisan key:generate

# تثبيت الحزم
composer install --optimize-autoloader --no-dev

# تشغيل الهجرات
php artisan migrate --force

# إنشاء رابط التخزين
php artisan storage:link

# تجميد الملفات للأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. إعداد Nginx

```bash
# نسخ إعدادات Nginx
sudo cp nginx.conf /etc/nginx/sites-available/hajj-employment
sudo ln -s /etc/nginx/sites-available/hajj-employment /etc/nginx/sites-enabled/

# اختبار الإعدادات
sudo nginx -t

# إعادة تشغيل Nginx
sudo systemctl restart nginx
```

### 6. إعداد Supervisor

```bash
# نسخ إعدادات Supervisor
sudo cp supervisor.conf /etc/supervisor/conf.d/hajj-employment.conf

# إعادة قراءة الإعدادات
sudo supervisorctl reread
sudo supervisorctl update

# بدء العمليات
sudo supervisorctl start hajj-employment-worker:*
sudo supervisorctl start hajj-employment-scheduler:*
```

### 7. إعداد SSL باستخدام Let's Encrypt

```bash
# تثبيت Certbot
sudo apt install certbot python3-certbot-nginx -y

# الحصول على شهادة SSL
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# إعداد التجديد التلقائي
sudo crontab -e
# إضافة السطر التالي:
# 0 12 * * * /usr/bin/certbot renew --quiet
```

### 8. تشغيل سكريبت النشر

```bash
# جعل السكريبت قابل للتنفيذ
chmod +x deploy.sh

# تشغيل النشر
./deploy.sh
```

## نصائح الأمان

### 1. إعداد جدار الحماية
```bash
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow mysql
```

### 2. تأمين MySQL
```bash
sudo mysql_secure_installation
```

### 3. إعداد نسخ احتياطية تلقائية
```bash
# إنشاء سكريبت النسخ الاحتياطية
sudo nano /usr/local/bin/backup-hajj-employment.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/hajj-employment"
DB_NAME="hajj_employment_production"
DB_USER="hajj_user"
DB_PASS="كلمة_المرور_القوية"

mkdir -p $BACKUP_DIR

# نسخ احتياطية لقاعدة البيانات
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/database_$DATE.sql

# نسخ احتياطية للملفات
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/hajj-employment

# حذف النسخ القديمة (أكثر من 30 يوم)
find $BACKUP_DIR -type f -mtime +30 -delete
```

```bash
# جعل السكريبت قابل للتنفيذ
sudo chmod +x /usr/local/bin/backup-hajj-employment.sh

# إضافة مهمة يومية
sudo crontab -e
# إضافة السطر التالي:
# 0 2 * * * /usr/local/bin/backup-hajj-employment.sh
```

## مراقبة الأداء

### 1. مراقبة السجلات
```bash
# سجلات Laravel
tail -f /var/www/hajj-employment/storage/logs/laravel.log

# سجلات Nginx
tail -f /var/log/nginx/hajj-employment_access.log
tail -f /var/log/nginx/hajj-employment_error.log

# سجلات Supervisor
tail -f /var/log/supervisor/hajj-employment-worker.log
```

### 2. مراقبة الموارد
```bash
# استخدام الذاكرة والمعالج
htop

# مساحة القرص
df -h

# حالة الخدمات
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
sudo systemctl status mysql
sudo systemctl status redis
sudo supervisorctl status
```

## استكشاف الأخطاء

### مشاكل شائعة وحلولها

1. **خطأ 500 Internal Server Error**
   - تحقق من سجلات Laravel والNginx
   - تأكد من صلاحيات الملفات
   - تحقق من إعدادات PHP

2. **مشاكل قاعدة البيانات**
   - تحقق من إعدادات الاتصال في .env
   - تأكد من تشغيل MySQL
   - تحقق من صلاحيات المستخدم

3. **مشاكل الأداء**
   - تفعيل OPcache
   - تحسين إعدادات MySQL
   - استخدام Redis للكاش

## التحديثات

```bash
# للتحديث إلى إصدار جديد
cd /var/www/hajj-employment
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
./deploy.sh
```

## الدعم الفني

في حالة وجود مشاكل، تحقق من:
1. سجلات الأخطاء في `/var/www/hajj-employment/storage/logs/`
2. سجلات Nginx في `/var/log/nginx/`
3. حالة الخدمات باستخدام `systemctl status`

---

**ملاحظة**: تأكد من تغيير جميع كلمات المرور وأسماء النطاقات قبل النشر الفعلي. 