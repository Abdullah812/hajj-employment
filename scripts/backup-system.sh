#!/bin/bash

# =============================================================================
# نظام النسخ الاحتياطي التلقائي - مناسك المشاعر
# =============================================================================

# إعدادات النسخ الاحتياطي
BACKUP_DIR="/var/backups/hajj-employment"
PROJECT_DIR="/var/www/hajj-employment"
DB_NAME="hajj_employment"
DB_USER="hajj_user"
DB_PASSWORD="your_password"
DATE=$(date +%Y%m%d_%H%M%S)
MAX_BACKUPS=30  # الاحتفاظ بـ 30 نسخة احتياطية
LOG_FILE="/var/log/hajj-backup.log"

# الألوان للمخرجات
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# دالة التسجيل
log_message() {
    echo -e "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# دالة الأخطاء
error_exit() {
    log_message "${RED}خطأ: $1${NC}"
    # إرسال تنبيه بالبريد الإلكتروني (إذا كان مُكوّناً)
    send_alert_email "فشل النسخ الاحتياطي" "$1"
    exit 1
}

# دالة إرسال التنبيهات
send_alert_email() {
    local subject="$1"
    local message="$2"
    
    # إرسال إيميل للمشرفين (يتطلب تكوين البريد الإلكتروني)
    if command -v mail &> /dev/null; then
        echo "$message" | mail -s "$subject" admin@hajj-employment.com
    fi
}

# دالة إنشاء المجلدات
create_backup_dirs() {
    log_message "${YELLOW}إنشاء مجلدات النسخ الاحتياطي...${NC}"
    
    mkdir -p "$BACKUP_DIR/database"
    mkdir -p "$BACKUP_DIR/files"
    mkdir -p "$BACKUP_DIR/logs"
    
    if [[ $? -eq 0 ]]; then
        log_message "${GREEN}✅ تم إنشاء المجلدات بنجاح${NC}"
    else
        error_exit "فشل في إنشاء مجلدات النسخ الاحتياطي"
    fi
}

# دالة نسخ احتياطي لقاعدة البيانات
backup_database() {
    log_message "${YELLOW}بدء النسخ الاحتياطي لقاعدة البيانات...${NC}"
    
    local db_backup_file="$BACKUP_DIR/database/database_$DATE.sql"
    local compressed_file="$BACKUP_DIR/database/database_$DATE.sql.gz"
    
    # نسخ احتياطي مع ضغط
    if mysqldump -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" > "$db_backup_file" 2>/dev/null; then
        # ضغط الملف
        gzip "$db_backup_file"
        
        # التحقق من حجم الملف
        local file_size=$(stat -f%z "$compressed_file" 2>/dev/null || stat -c%s "$compressed_file" 2>/dev/null)
        if [[ $file_size -gt 1024 ]]; then
            log_message "${GREEN}✅ تم نسخ قاعدة البيانات بنجاح (الحجم: $(($file_size / 1024)) KB)${NC}"
        else
            error_exit "حجم نسخة قاعدة البيانات صغير جداً - قد يكون هناك خطأ"
        fi
    else
        error_exit "فشل في نسخ قاعدة البيانات"
    fi
}

# دالة نسخ احتياطي للملفات
backup_files() {
    log_message "${YELLOW}بدء النسخ الاحتياطي للملفات...${NC}"
    
    local files_backup="$BACKUP_DIR/files/files_$DATE.tar.gz"
    
    # نسخ الملفات مع استثناء المجلدات غير المهمة
    if tar -czf "$files_backup" \
        --exclude="$PROJECT_DIR/node_modules" \
        --exclude="$PROJECT_DIR/vendor" \
        --exclude="$PROJECT_DIR/storage/logs" \
        --exclude="$PROJECT_DIR/storage/framework/cache" \
        --exclude="$PROJECT_DIR/storage/framework/sessions" \
        --exclude="$PROJECT_DIR/storage/framework/views" \
        -C "$(dirname "$PROJECT_DIR")" "$(basename "$PROJECT_DIR")" 2>/dev/null; then
        
        # التحقق من حجم الملف
        local file_size=$(stat -f%z "$files_backup" 2>/dev/null || stat -c%s "$files_backup" 2>/dev/null)
        log_message "${GREEN}✅ تم نسخ الملفات بنجاح (الحجم: $(($file_size / 1024 / 1024)) MB)${NC}"
    else
        error_exit "فشل في نسخ الملفات"
    fi
}

# دالة نسخ احتياطي للسجلات
backup_logs() {
    log_message "${YELLOW}بدء النسخ الاحتياطي للسجلات...${NC}"
    
    local logs_backup="$BACKUP_DIR/logs/logs_$DATE.tar.gz"
    
    if [[ -d "$PROJECT_DIR/storage/logs" ]]; then
        tar -czf "$logs_backup" -C "$PROJECT_DIR/storage" logs 2>/dev/null
        log_message "${GREEN}✅ تم نسخ السجلات بنجاح${NC}"
    else
        log_message "${YELLOW}⚠️  لم يتم العثور على مجلد السجلات${NC}"
    fi
}

# دالة تنظيف النسخ القديمة
cleanup_old_backups() {
    log_message "${YELLOW}تنظيف النسخ الاحتياطية القديمة...${NC}"
    
    # حذف نسخ قاعدة البيانات القديمة
    find "$BACKUP_DIR/database" -name "database_*.sql.gz" -mtime +$MAX_BACKUPS -delete 2>/dev/null
    
    # حذف نسخ الملفات القديمة
    find "$BACKUP_DIR/files" -name "files_*.tar.gz" -mtime +$MAX_BACKUPS -delete 2>/dev/null
    
    # حذف نسخ السجلات القديمة
    find "$BACKUP_DIR/logs" -name "logs_*.tar.gz" -mtime +$MAX_BACKUPS -delete 2>/dev/null
    
    log_message "${GREEN}✅ تم تنظيف النسخ القديمة${NC}"
}

# دالة التحقق من سلامة النسخ
verify_backups() {
    log_message "${YELLOW}التحقق من سلامة النسخ الاحتياطية...${NC}"
    
    local db_backup="$BACKUP_DIR/database/database_$DATE.sql.gz"
    local files_backup="$BACKUP_DIR/files/files_$DATE.tar.gz"
    
    # التحقق من ملف قاعدة البيانات
    if [[ -f "$db_backup" ]] && gzip -t "$db_backup" 2>/dev/null; then
        log_message "${GREEN}✅ نسخة قاعدة البيانات سليمة${NC}"
    else
        error_exit "نسخة قاعدة البيانات تالفة"
    fi
    
    # التحقق من ملف الملفات
    if [[ -f "$files_backup" ]] && tar -tzf "$files_backup" >/dev/null 2>&1; then
        log_message "${GREEN}✅ نسخة الملفات سليمة${NC}"
    else
        error_exit "نسخة الملفات تالفة"
    fi
}

# دالة تقرير النسخ الاحتياطي
generate_backup_report() {
    log_message "${YELLOW}إنشاء تقرير النسخ الاحتياطي...${NC}"
    
    local report_file="$BACKUP_DIR/backup_report_$DATE.txt"
    
    {
        echo "======================================"
        echo "تقرير النسخ الاحتياطي - مناسك المشاعر"
        echo "======================================"
        echo "التاريخ: $(date '+%Y-%m-%d %H:%M:%S')"
        echo "الخادم: $(hostname)"
        echo "======================================="
        echo
        echo "📊 إحصائيات النسخ الاحتياطي:"
        echo "- نسخة قاعدة البيانات: $(ls -lh "$BACKUP_DIR/database/database_$DATE.sql.gz" 2>/dev/null | awk '{print $5}' || echo 'غير متوفر')"
        echo "- نسخة الملفات: $(ls -lh "$BACKUP_DIR/files/files_$DATE.tar.gz" 2>/dev/null | awk '{print $5}' || echo 'غير متوفر')"
        echo "- نسخة السجلات: $(ls -lh "$BACKUP_DIR/logs/logs_$DATE.tar.gz" 2>/dev/null | awk '{print $5}' || echo 'غير متوفر')"
        echo
        echo "💾 المساحة المستخدمة:"
        echo "- مجلد النسخ الاحتياطي: $(du -sh "$BACKUP_DIR" 2>/dev/null | awk '{print $1}' || echo 'غير متوفر')"
        echo "- المساحة المتوفرة: $(df -h "$BACKUP_DIR" 2>/dev/null | awk 'NR==2 {print $4}' || echo 'غير متوفر')"
        echo
        echo "🗂️ عدد النسخ الاحتياطية:"
        echo "- نسخ قاعدة البيانات: $(find "$BACKUP_DIR/database" -name "database_*.sql.gz" 2>/dev/null | wc -l)"
        echo "- نسخ الملفات: $(find "$BACKUP_DIR/files" -name "files_*.tar.gz" 2>/dev/null | wc -l)"
        echo "- نسخ السجلات: $(find "$BACKUP_DIR/logs" -name "logs_*.tar.gz" 2>/dev/null | wc -l)"
        echo
        echo "✅ حالة النسخ الاحتياطي: مكتمل بنجاح"
    } > "$report_file"
    
    log_message "${GREEN}✅ تم إنشاء تقرير النسخ الاحتياطي: $report_file${NC}"
}

# دالة الاستعادة (للطوارئ)
restore_backup() {
    local backup_date="$1"
    
    if [[ -z "$backup_date" ]]; then
        echo "الاستخدام: $0 restore YYYYMMDD_HHMMSS"
        exit 1
    fi
    
    log_message "${YELLOW}بدء استعادة النسخة الاحتياطية للتاريخ: $backup_date${NC}"
    
    # استعادة قاعدة البيانات
    local db_backup="$BACKUP_DIR/database/database_$backup_date.sql.gz"
    if [[ -f "$db_backup" ]]; then
        log_message "${YELLOW}استعادة قاعدة البيانات...${NC}"
        zcat "$db_backup" | mysql -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME"
        log_message "${GREEN}✅ تم استعادة قاعدة البيانات${NC}"
    else
        error_exit "لم يتم العثور على نسخة قاعدة البيانات للتاريخ المحدد"
    fi
    
    # استعادة الملفات
    local files_backup="$BACKUP_DIR/files/files_$backup_date.tar.gz"
    if [[ -f "$files_backup" ]]; then
        read -p "هل تريد استعادة الملفات؟ (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            log_message "${YELLOW}استعادة الملفات...${NC}"
            tar -xzf "$files_backup" -C "$(dirname "$PROJECT_DIR")"
            log_message "${GREEN}✅ تم استعادة الملفات${NC}"
        fi
    fi
}

# الدالة الرئيسية
main() {
    log_message "${GREEN}===========================================${NC}"
    log_message "${GREEN}بدء النسخ الاحتياطي التلقائي${NC}"
    log_message "${GREEN}===========================================${NC}"
    
    # التحقق من الأذونات
    if [[ $EUID -ne 0 ]]; then
        error_exit "يجب تشغيل السكريبت كمدير نظام (root)"
    fi
    
    # التحقق من وجود الأدوات المطلوبة
    for tool in mysqldump tar gzip; do
        if ! command -v "$tool" &> /dev/null; then
            error_exit "الأداة المطلوبة غير موجودة: $tool"
        fi
    done
    
    # تنفيذ النسخ الاحتياطي
    create_backup_dirs
    backup_database
    backup_files
    backup_logs
    verify_backups
    cleanup_old_backups
    generate_backup_report
    
    log_message "${GREEN}===========================================${NC}"
    log_message "${GREEN}✅ تم إكمال النسخ الاحتياطي بنجاح${NC}"
    log_message "${GREEN}===========================================${NC}"
    
    # إرسال تنبيه بالنجاح
    send_alert_email "نجح النسخ الاحتياطي" "تم إكمال النسخ الاحتياطي بنجاح في $DATE"
}

# تشغيل السكريبت
case "$1" in
    restore)
        restore_backup "$2"
        ;;
    *)
        main
        ;;
esac 