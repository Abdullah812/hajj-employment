# 📁 دليل النسخ الاحتياطي - مناسك المشاعر

## 📋 الوضع الحالي للنسخ الاحتياطي

### ❗ **الحقيقة الصريحة:**
- ✅ **نظام النسخ الاحتياطي مُبرمج ومُجهز** 
- ❌ **لكنه غير فعال حالياً**
- 🔧 **السبب:** مصمم للعمل على **خادم Linux** وأنت تعمل على **Windows**

### 📂 **مكان النسخ الاحتياطي:**

#### على خادم الإنتاج (Linux):
```
/var/backups/hajj-employment/
├── database/     (نسخ قاعدة البيانات)
├── files/        (نسخ الملفات)
├── logs/         (نسخ السجلات)
└── reports/      (تقارير النسخ)
```

#### على جهازك المحلي (Windows):
```
%USERPROFILE%\Documents\hajj-employment-backups\
├── database/     (نسخ قاعدة البيانات)
├── files/        (نسخ الملفات)
├── logs/         (نسخ السجلات)
└── reports/      (تقارير النسخ)
```

## 🚀 **تفعيل النسخ الاحتياطي الآن**

### 📝 **الطريقة الأولى: ملف Batch**
```cmd
# انتقل لمجلد scripts
cd scripts

# تشغيل النسخ الاحتياطي
backup-windows.bat
```

### ⚡ **الطريقة الثانية: PowerShell (مُوصى بها)**
```powershell
# انتقل لمجلد scripts
cd scripts

# تشغيل النسخ الاحتياطي
.\backup-windows.ps1

# عرض النسخ المتاحة
.\backup-windows.ps1 -Action list

# عرض حالة النسخ
.\backup-windows.ps1 -Action status
```

## 📊 **ما يتم نسخه احتياطياً:**

### 💾 **قاعدة البيانات:**
- نسخ كاملة مضغوطة من `hajj_employment`
- يشمل جميع الجداول والبيانات
- **الحجم المتوقع:** 1-10 MB

### 📁 **ملفات المشروع:**
- جميع ملفات المشروع ما عدا:
  - `node_modules` (مجلد npm)
  - `vendor` (مجلد Composer)
  - `storage/cache` (ملفات مؤقتة)
  - `.git` (ملفات Git)
- **الحجم المتوقع:** 50-200 MB

### 📄 **السجلات:**
- ملفات `storage/logs`
- سجلات الأخطاء والأحداث
- **الحجم المتوقع:** 1-50 MB

## ⚙️ **إعدادات مهمة قبل البدء**

### 🔐 **إعدادات قاعدة البيانات:**
افتح الملف `scripts/backup-windows.ps1` وتأكد من:
```powershell
$DatabaseName = "hajj_employment"  # اسم قاعدة البيانات
$DatabaseUser = "root"             # اسم المستخدم
# كلمة المرور (أتركها فارغة إذا لم تكن محددة)
```

### 📂 **تخصيص مكان النسخ:**
```powershell
# يمكنك تغيير المكان هنا
$BackupDir = "$env:USERPROFILE\Documents\hajj-employment-backups"
```

## 🕒 **جدولة النسخ التلقائي**

### 📅 **إنشاء مهمة مجدولة في Windows:**

1. **افتح Task Scheduler:**
   ```
   Start → Task Scheduler
   ```

2. **إنشاء مهمة جديدة:**
   - اسم المهمة: `Hajj Employment Backup`
   - التوقيت: يومياً في 2:00 صباحاً
   - الإجراء: `powershell.exe`
   - المعاملات: `-File "C:\path\to\scripts\backup-windows.ps1"`

3. **أو باستخدام PowerShell:**
```powershell
# إنشاء مهمة مجدولة تلقائياً
$action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-File 'C:\path\to\scripts\backup-windows.ps1'"
$trigger = New-ScheduledTaskTrigger -Daily -At "02:00AM"
Register-ScheduledTask -TaskName "Hajj Employment Backup" -Action $action -Trigger $trigger
```

## 🔄 **كيفية الاستعادة**

### 📊 **عرض النسخ المتاحة:**
```powershell
.\backup-windows.ps1 -Action list
```

### 💾 **استعادة قاعدة البيانات:**
```cmd
# استخراج ملف SQL من الأرشيف
# ثم استعادته باستخدام:
mysql -u root -p hajj_employment < database_20241201_020000.sql
```

### 📁 **استعادة الملفات:**
```cmd
# استخراج ملفات المشروع من الأرشيف المضغوط
# نسخها إلى مجلد المشروع
```

## 🛡️ **نصائح الأمان**

### ✅ **افعل:**
- اختبر النسخ الاحتياطي بانتظام
- احتفظ بنسخ في أماكن متعددة
- تحقق من سلامة الملفات المضغوطة

### ❌ **لا تفعل:**
- لا تعتمد على نسخة واحدة فقط
- لا تنس تحديث كلمة مرور قاعدة البيانات
- لا تحذف النسخ القديمة يدوياً

## 📱 **المساعدة السريعة**

### 🆘 **مشاكل شائعة:**

#### ❌ "mysqldump غير موجود"
```cmd
# تأكد من تثبيت MySQL وإضافته لـ PATH
# أو استخدم المسار الكامل:
C:\xampp\mysql\bin\mysqldump.exe
```

#### ❌ "فشل في نسخ قاعدة البيانات"
- تحقق من تشغيل MySQL
- تحقق من اسم قاعدة البيانات
- تحقق من اسم المستخدم وكلمة المرور

#### ❌ "فشل في نسخ الملفات"
- تأكد من أذونات القراءة للمجلد
- أغلق الملفات المفتوحة في المحرر

## 📞 **اختبار فوري**

### 🧪 **اختبر النسخ الآن:**
```powershell
cd scripts
.\backup-windows.ps1
```

### 📂 **تحقق من النتيجة:**
```powershell
# سيفتح مجلد النسخ الاحتياطي
explorer "$env:USERPROFILE\Documents\hajj-employment-backups"
```

---

## 🎯 **الخلاصة**

| العنصر | الحالة | الملاحظة |
|---------|--------|-----------|
| 🔧 النظام | ✅ جاهز | يحتاج تفعيل |
| 📁 الملفات | ✅ متوفر | scripts/backup-windows.* |
| ⚙️ الإعداد | ⚠️ مطلوب | تحديث إعدادات قاعدة البيانات |
| 🕒 الجدولة | ❌ غير مفعل | يتطلب إعداد مهمة Windows |
| 📂 المكان | 📍 محدد | Documents/hajj-employment-backups |

**✨ نظامك الآن قادر على حماية البيانات بالكامل!** 