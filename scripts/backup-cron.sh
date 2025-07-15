#!/bin/bash

# =============================================================================
# إعداد مهام cron للنسخ الاحتياطي التلقائي
# =============================================================================

# التحقق من الأذونات
if [[ $EUID -ne 0 ]]; then
    echo "❌ يجب تشغيل السكريبت كمدير نظام (root)"
    exit 1
fi

# إعدادات
BACKUP_SCRIPT="/usr/local/bin/hajj-backup.sh"
CRON_FILE="/etc/cron.d/hajj-backup"
PROJECT_DIR="/var/www/hajj-employment"

echo "🚀 إعداد النسخ الاحتياطي التلقائي لمناسك المشاعر..."

# نسخ سكريبت النسخ الاحتياطي
echo "📁 نسخ سكريبت النسخ الاحتياطي..."
cp "$PROJECT_DIR/scripts/backup-system.sh" "$BACKUP_SCRIPT"
chmod +x "$BACKUP_SCRIPT"

# إنشاء مجلد سجلات النسخ الاحتياطي
echo "📂 إنشاء مجلد السجلات..."
mkdir -p /var/log/hajj-employment
mkdir -p /var/backups/hajj-employment

# إنشاء ملف cron
echo "⏰ إعداد مهام cron..."
cat > "$CRON_FILE" << 'EOF'
# النسخ الاحتياطي التلقائي لمناسك المشاعر
# يتم تشغيله كل يوم في الساعة 2:00 صباحاً

SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

# النسخ الاحتياطي اليومي
0 2 * * * root /usr/local/bin/hajj-backup.sh > /var/log/hajj-employment/backup-cron.log 2>&1

# التحقق من حالة النسخ الاحتياطي كل أسبوع
0 8 * * 0 root /usr/local/bin/hajj-backup.sh verify >> /var/log/hajj-employment/backup-verify.log 2>&1

# تنظيف السجلات القديمة كل شهر
0 3 1 * * root find /var/log/hajj-employment -name "*.log" -mtime +30 -delete
EOF

# تعيين أذونات ملف cron
chmod 644 "$CRON_FILE"

# إعادة تشغيل خدمة cron
echo "🔄 إعادة تشغيل خدمة cron..."
systemctl restart cron

# إنشاء سكريبت التحقق من حالة النسخ الاحتياطي
echo "📊 إنشاء سكريبت التحقق من الحالة..."
cat > "/usr/local/bin/hajj-backup-status.sh" << 'EOF'
#!/bin/bash

# سكريبت التحقق من حالة النسخ الاحتياطي

BACKUP_DIR="/var/backups/hajj-employment"
LOG_FILE="/var/log/hajj-backup.log"

echo "======================================"
echo "حالة النسخ الاحتياطي - مناسك المشاعر"
echo "======================================"
echo "التاريخ: $(date '+%Y-%m-%d %H:%M:%S')"
echo

# التحقق من آخر نسخة احتياطية
echo "🕒 آخر نسخة احتياطية:"
if [[ -d "$BACKUP_DIR/database" ]]; then
    LAST_DB_BACKUP=$(ls -1t "$BACKUP_DIR/database/database_"*.sql.gz 2>/dev/null | head -1)
    if [[ -n "$LAST_DB_BACKUP" ]]; then
        echo "  - قاعدة البيانات: $(basename "$LAST_DB_BACKUP") ($(date -r "$LAST_DB_BACKUP" '+%Y-%m-%d %H:%M:%S'))"
        echo "  - الحجم: $(ls -lh "$LAST_DB_BACKUP" | awk '{print $5}')"
    else
        echo "  - ❌ لم يتم العثور على نسخة لقاعدة البيانات"
    fi
else
    echo "  - ❌ مجلد النسخ الاحتياطي غير موجود"
fi

echo

# إحصائيات النسخ الاحتياطي
echo "📊 إحصائيات النسخ الاحتياطي:"
if [[ -d "$BACKUP_DIR" ]]; then
    DB_COUNT=$(find "$BACKUP_DIR/database" -name "database_*.sql.gz" 2>/dev/null | wc -l)
    FILES_COUNT=$(find "$BACKUP_DIR/files" -name "files_*.tar.gz" 2>/dev/null | wc -l)
    LOGS_COUNT=$(find "$BACKUP_DIR/logs" -name "logs_*.tar.gz" 2>/dev/null | wc -l)
    TOTAL_SIZE=$(du -sh "$BACKUP_DIR" 2>/dev/null | awk '{print $1}')
    
    echo "  - نسخ قاعدة البيانات: $DB_COUNT"
    echo "  - نسخ الملفات: $FILES_COUNT"
    echo "  - نسخ السجلات: $LOGS_COUNT"
    echo "  - المساحة المستخدمة: $TOTAL_SIZE"
else
    echo "  - ❌ مجلد النسخ الاحتياطي غير موجود"
fi

echo

# آخر سجلات النسخ الاحتياطي
echo "📝 آخر سجلات النسخ الاحتياطي:"
if [[ -f "$LOG_FILE" ]]; then
    echo "  آخر 5 رسائل:"
    tail -5 "$LOG_FILE" | while read line; do
        echo "    $line"
    done
else
    echo "  - ⚠️ لم يتم العثور على ملف السجلات"
fi

echo
echo "======================================"
EOF

chmod +x "/usr/local/bin/hajj-backup-status.sh"

# إنشاء سكريبت تشغيل النسخ الاحتياطي يدوياً
echo "🎯 إنشاء سكريبت التشغيل اليدوي..."
cat > "/usr/local/bin/hajj-backup-manual.sh" << 'EOF'
#!/bin/bash

# سكريپت لتشغيل النسخ الاحتياطي يدوياً

echo "🚀 تشغيل النسخ الاحتياطي يدوياً..."
echo "=================================="

# التحقق من الأذونات
if [[ $EUID -ne 0 ]]; then
    echo "❌ يجب تشغيل السكريبت كمدير نظام (root)"
    exit 1
fi

# تشغيل النسخ الاحتياطي
/usr/local/bin/hajj-backup.sh

echo "=================================="
echo "✅ انتهى تشغيل النسخ الاحتياطي"
echo "💡 للتحقق من الحالة: hajj-backup-status.sh"
EOF

chmod +x "/usr/local/bin/hajj-backup-manual.sh"

# إنشاء سكريبت الاستعادة
echo "🔄 إنشاء سكريبت الاستعادة..."
cat > "/usr/local/bin/hajj-backup-restore.sh" << 'EOF'
#!/bin/bash

# سكريبت استعادة النسخة الاحتياطية

BACKUP_DIR="/var/backups/hajj-employment"

echo "🔄 استعادة النسخة الاحتياطية"
echo "=================================="

# عرض النسخ المتاحة
echo "النسخ الاحتياطية المتاحة:"
echo

if [[ -d "$BACKUP_DIR/database" ]]; then
    ls -1t "$BACKUP_DIR/database/database_"*.sql.gz 2>/dev/null | head -10 | while read backup_file; do
        backup_date=$(basename "$backup_file" .sql.gz | sed 's/database_//')
        file_date=$(date -r "$backup_file" '+%Y-%m-%d %H:%M:%S')
        file_size=$(ls -lh "$backup_file" | awk '{print $5}')
        echo "  📅 $backup_date ($file_date) - الحجم: $file_size"
    done
else
    echo "  ❌ لم يتم العثور على نسخ احتياطية"
    exit 1
fi

echo
echo "=================================="
echo "💡 للاستعادة: hajj-backup.sh restore التاريخ"
echo "   مثال: hajj-backup.sh restore 20241201_020000"
echo "=================================="
EOF

chmod +x "/usr/local/bin/hajj-backup-restore.sh"

# اختبار النسخ الاحتياطي
echo "🧪 تشغيل اختبار النسخ الاحتياطي..."
if /usr/local/bin/hajj-backup.sh; then
    echo "✅ تم تشغيل النسخ الاحتياطي بنجاح"
else
    echo "❌ فشل في تشغيل النسخ الاحتياطي"
    exit 1
fi

echo
echo "✅ تم إعداد النسخ الاحتياطي التلقائي بنجاح!"
echo
echo "📋 الأوامر المتاحة:"
echo "  hajj-backup-manual.sh       - تشغيل النسخ الاحتياطي يدوياً"
echo "  hajj-backup-status.sh       - التحقق من حالة النسخ الاحتياطي"
echo "  hajj-backup-restore.sh      - عرض النسخ المتاحة للاستعادة"
echo "  hajj-backup.sh restore DATE - استعادة نسخة محددة"
echo
echo "⏰ جدولة النسخ الاحتياطي:"
echo "  - يومياً في الساعة 2:00 صباحاً"
echo "  - التحقق الأسبوعي كل أحد في الساعة 8:00 صباحاً"
echo "  - تنظيف السجلات شهرياً"
echo
echo "📂 مواقع الملفات:"
echo "  - النسخ الاحتياطية: $BACKUP_DIR"
echo "  - السجلات: /var/log/hajj-employment/"
echo "  - ملف cron: $CRON_FILE"
echo