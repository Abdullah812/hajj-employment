# 🗂️ دليل إعداد Bucket للنسخ الاحتياطية

## 🎯 إعداد Laravel Cloud Bucket

### 📋 خطوة 1: إعداد متغيرات البيئة

بعد إنشاء الـ Bucket في Laravel Cloud، أضف هذه المتغيرات في **Environment Variables**:

```env
# Laravel Cloud Bucket Configuration
AWS_ACCESS_KEY_ID=your_access_key_id
AWS_SECRET_ACCESS_KEY=your_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=hajj-employment-backups
AWS_ENDPOINT=your_bucket_endpoint
AWS_URL=your_bucket_url
AWS_USE_PATH_STYLE_ENDPOINT=true
```

### 🔧 خطوة 2: الحصول على بيانات الـ Bucket

1. **اذهب إلى Laravel Cloud Dashboard**
2. **انقر على Buckets**
3. **اختر الـ Bucket الذي أنشأته**
4. **انقر على "Access Keys"**
5. **انسخ المعلومات التالية:**
   - Access Key ID
   - Secret Access Key
   - Endpoint URL
   - Bucket URL

### 📝 خطوة 3: تحديث Environment Variables

```
🔧 في Laravel Cloud:
├── Dashboard → Project → Environment
├── Environment Variables → Edit
├── أضف المتغيرات المذكورة أعلاه
└── Save Changes
```

---

## 🚀 الأوامر المتاحة

### 📤 رفع النسخ الاحتياطية للـ Bucket

```bash
# رفع أحدث نسخة احتياطية
php artisan backup:upload-to-bucket --latest

# رفع جميع النسخ الاحتياطية
php artisan backup:upload-to-bucket --all

# رفع نسخ من تاريخ معين
php artisan backup:upload-to-bucket --date=2025-07-20

# استبدال الملفات الموجودة
php artisan backup:upload-to-bucket --latest --force
```

### 📋 إدارة ملفات الـ Bucket

```bash
# عرض جميع الملفات في الـ Bucket
php artisan backup:list-bucket-files

# تحميل ملف معين
php artisan backup:list-bucket-files --download="filename.sql.gz"

# تحميل جميع الملفات
php artisan backup:list-bucket-files --download-all

# حذف ملف معين
php artisan backup:list-bucket-files --delete="filename.sql.gz"

# حذف الملفات القديمة (أكثر من 30 يوم)
php artisan backup:list-bucket-files --clean-old
```

---

## 🗂️ هيكل تنظيم الملفات

```
hajj-employment-backups/
├── backups/
│   ├── 2025-07-20/
│   │   ├── hajj_employment_backup_2025-07-20_05-51-45.sql.gz
│   │   └── backup_report_2025-07-20_05-51-45.txt
│   ├── 2025-07-21/
│   │   ├── hajj_employment_backup_2025-07-21_02-00-00.sql.gz
│   │   └── backup_report_2025-07-21_02-00-00.txt
│   └── 2025-07-22/
│       └── ...
```

---

## 🔧 استكشاف الأخطاء

### ❌ خطأ الاتصال بالـ Bucket

```
خطأ: خطأ في اتصال الـ Bucket!
```

**الحل:**
1. تأكد من صحة متغيرات البيئة
2. تأكد من إنشاء الـ Bucket في Laravel Cloud
3. تأكد من صلاحيات مفتاح الوصول (Read and Write)

### ❌ خطأ عدم وجود الـ S3 Driver

```
خطأ: Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found
```

**الحل:**
```bash
# تثبيت AWS S3 Package
composer require league/flysystem-aws-s3-v3
```

### ❌ خطأ الصلاحيات

```
خطأ: Access Denied
```

**الحل:**
1. تأكد من أن Access Key له صلاحية Read and Write
2. تأكد من أن الـ Bucket في وضع Private مع صلاحيات صحيحة

---

## 🎯 أفضل الممارسات

### 📊 جدولة النسخ الاحتياطية

```bash
# إضافة لـ Cron Job في Laravel Cloud
0 2 * * * cd /path/to/project && php artisan backup:cloud-database
5 2 * * * cd /path/to/project && php artisan backup:upload-to-bucket --latest
```

### 🧹 تنظيف دوري

```bash
# تنظيف أسبوعي للملفات القديمة
0 3 * * 0 cd /path/to/project && php artisan backup:list-bucket-files --clean-old
```

### 🔒 الأمان

```
✅ أفضل الممارسات:
├── استخدم Bucket في وضع Private دائماً
├── دوّر Access Keys بانتظام
├── راقب استخدام الـ Bucket
├── احتفظ بنسخ محلية مهمة
└── اختبر عملية الاستعادة بانتظام
```

---

## 📞 المساعدة

إذا واجهت أي مشاكل:

1. **تحقق من الأوامر المتاحة:**
   ```bash
   php artisan list backup
   ```

2. **اختبر الاتصال:**
   ```bash
   php artisan backup:list-bucket-files
   ```

3. **تحقق من الـ Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 🎉 مثال كامل للاستخدام

```bash
# 1. إنشاء نسخة احتياطية
php artisan backup:cloud-database

# 2. رفعها للـ Bucket
php artisan backup:upload-to-bucket --latest

# 3. التحقق من الرفع
php artisan backup:list-bucket-files

# 4. تحميل نسخة للتأكد
php artisan backup:list-bucket-files --download="filename.sql.gz"
```

**🎯 الآن لديك نظام نسخ احتياطي متكامل مع Laravel Cloud Bucket!** 