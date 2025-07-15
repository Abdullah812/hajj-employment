#!/bin/bash

# =============================================================================
# سكريپت اختبار قاعدة البيانات - مناسك المشاعر
# فحص شامل لقاعدة البيانات والجداول والبيانات
# =============================================================================

# الألوان للمخرجات
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

PROJECT_DIR="/var/www/hajj-employment"
DATABASE_NAME=""
DATABASE_USER=""
DATABASE_PASSWORD=""

# دالة طباعة العنوان
print_header() {
    echo -e "${CYAN}================================${NC}"
    echo -e "${CYAN}$1${NC}"
    echo -e "${CYAN}================================${NC}"
    echo
}

# دالة طباعة نتيجة الاختبار
print_test_result() {
    local test_name="$1"
    local result="$2"
    local details="$3"
    
    if [[ "$result" == "PASS" ]]; then
        echo -e "✅ ${GREEN}[PASS]${NC} $test_name"
        [[ -n "$details" ]] && echo -e "   ${BLUE}💡 $details${NC}"
    else
        echo -e "❌ ${RED}[FAIL]${NC} $test_name"
        [[ -n "$details" ]] && echo -e "   ${RED}⚠️  $details${NC}"
    fi
    echo
}

# دالة قراءة إعدادات قاعدة البيانات
read_database_config() {
    cd "$PROJECT_DIR" || exit 1
    
    if [[ -f ".env" ]]; then
        DATABASE_NAME=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
        DATABASE_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
        DATABASE_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)
        
        print_test_result "قراءة إعدادات قاعدة البيانات" "PASS" "تم قراءة الإعدادات من .env"
    else
        print_test_result "قراءة إعدادات قاعدة البيانات" "FAIL" "ملف .env غير موجود"
        exit 1
    fi
}

# دالة اختبار الاتصال بقاعدة البيانات
test_database_connection() {
    print_header "اختبار الاتصال بقاعدة البيانات"
    
    # اختبار الاتصال بـ MySQL
    if mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -e "SELECT 1;" &>/dev/null; then
        print_test_result "الاتصال بـ MySQL" "PASS" "الاتصال ناجح"
    else
        print_test_result "الاتصال بـ MySQL" "FAIL" "فشل في الاتصال"
        return 1
    fi
    
    # اختبار الاتصال بقاعدة البيانات المحددة
    if mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT 1;" &>/dev/null; then
        print_test_result "الاتصال بقاعدة البيانات" "PASS" "قاعدة البيانات: $DATABASE_NAME"
    else
        print_test_result "الاتصال بقاعدة البيانات" "FAIL" "قاعدة البيانات غير موجودة: $DATABASE_NAME"
        return 1
    fi
    
    # اختبار Laravel Connection
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Laravel DB Connected';" 2>/dev/null | grep -q "Laravel DB Connected"; then
        print_test_result "اتصال Laravel" "PASS" "Laravel متصل بقاعدة البيانات"
    else
        print_test_result "اتصال Laravel" "FAIL" "Laravel غير متصل بقاعدة البيانات"
    fi
}

# دالة اختبار حالة migrations
test_migrations() {
    print_header "اختبار حالة Migrations"
    
    cd "$PROJECT_DIR" || exit 1
    
    # التحقق من جدول migrations
    if php artisan migrate:status &>/dev/null; then
        print_test_result "جدول migrations" "PASS" "موجود ويعمل"
        
        # عرض حالة migrations
        migrations_output=$(php artisan migrate:status 2>/dev/null)
        total_migrations=$(echo "$migrations_output" | grep -c "migration")
        pending_migrations=$(echo "$migrations_output" | grep -c "Pending")
        
        if [[ $pending_migrations -eq 0 ]]; then
            print_test_result "حالة Migrations" "PASS" "جميع migrations تم تنفيذها ($total_migrations)"
        else
            print_test_result "حالة Migrations" "FAIL" "$pending_migrations migrations معلقة من أصل $total_migrations"
        fi
    else
        print_test_result "جدول migrations" "FAIL" "غير موجود أو معطل"
    fi
}

# دالة اختبار الجداول الأساسية
test_core_tables() {
    print_header "اختبار الجداول الأساسية"
    
    cd "$PROJECT_DIR" || exit 1
    
    # قائمة الجداول المطلوبة
    essential_tables=(
        "users"
        "user_profiles"
        "departments"
        "hajj_jobs"
        "job_applications"
        "contracts"
        "notifications"
        "security_events"
        "permissions"
        "roles"
        "model_has_permissions"
        "model_has_roles"
        "role_has_permissions"
        "attendance"
        "evaluations"
        "documents"
    )
    
    for table in "${essential_tables[@]}"; do
        if mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "DESCRIBE $table;" &>/dev/null; then
            # حساب عدد الصفوف
            count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM $table;" 2>/dev/null | tail -1)
            print_test_result "جدول $table" "PASS" "موجود مع $count صف"
        else
            print_test_result "جدول $table" "FAIL" "غير موجود"
        fi
    done
}

# دالة اختبار بيانات الأدوار والصلاحيات
test_roles_permissions() {
    print_header "اختبار الأدوار والصلاحيات"
    
    cd "$PROJECT_DIR" || exit 1
    
    # التحقق من وجود الأدوار الأساسية
    required_roles=("admin" "department" "employee")
    
    for role in "${required_roles[@]}"; do
        if php artisan tinker --execute="echo App\Models\User::role('$role')->count();" 2>/dev/null | grep -q "[0-9]"; then
            count=$(php artisan tinker --execute="echo App\Models\User::role('$role')->count();" 2>/dev/null | grep -o "[0-9]*")
            print_test_result "دور $role" "PASS" "يحتوي على $count مستخدم"
        else
            print_test_result "دور $role" "FAIL" "غير موجود أو لا يحتوي على مستخدمين"
        fi
    done
    
    # التحقق من الصلاحيات الأساسية
    if php artisan tinker --execute="echo Spatie\Permission\Models\Permission::count();" 2>/dev/null | grep -q "[0-9]"; then
        permissions_count=$(php artisan tinker --execute="echo Spatie\Permission\Models\Permission::count();" 2>/dev/null | grep -o "[0-9]*")
        print_test_result "الصلاحيات" "PASS" "يحتوي على $permissions_count صلاحية"
    else
        print_test_result "الصلاحيات" "FAIL" "غير موجودة أو فارغة"
    fi
}

# دالة اختبار البيانات الأساسية
test_basic_data() {
    print_header "اختبار البيانات الأساسية"
    
    cd "$PROJECT_DIR" || exit 1
    
    # التحقق من وجود مستخدم أدمن
    if php artisan tinker --execute="echo App\Models\User::role('admin')->count();" 2>/dev/null | grep -q "[1-9]"; then
        admin_count=$(php artisan tinker --execute="echo App\Models\User::role('admin')->count();" 2>/dev/null | grep -o "[0-9]*")
        print_test_result "مستخدمين الأدمن" "PASS" "يوجد $admin_count مستخدم أدمن"
    else
        print_test_result "مستخدمين الأدمن" "FAIL" "لا يوجد مستخدمين أدمن"
    fi
    
    # التحقق من وجود أقسام
    if php artisan tinker --execute="echo App\Models\Department::count();" 2>/dev/null | grep -q "[0-9]"; then
        departments_count=$(php artisan tinker --execute="echo App\Models\Department::count();" 2>/dev/null | grep -o "[0-9]*")
        print_test_result "الأقسام" "PASS" "يوجد $departments_count قسم"
    else
        print_test_result "الأقسام" "FAIL" "لا يوجد أقسام"
    fi
    
    # التحقق من وجود وظائف
    if php artisan tinker --execute="echo App\Models\HajjJob::count();" 2>/dev/null | grep -q "[0-9]"; then
        jobs_count=$(php artisan tinker --execute="echo App\Models\HajjJob::count();" 2>/dev/null | grep -o "[0-9]*")
        print_test_result "الوظائف" "PASS" "يوجد $jobs_count وظيفة"
    else
        print_test_result "الوظائف" "FAIL" "لا يوجد وظائف"
    fi
}

# دالة اختبار الأمان في قاعدة البيانات
test_database_security() {
    print_header "اختبار الأمان في قاعدة البيانات"
    
    cd "$PROJECT_DIR" || exit 1
    
    # التحقق من جدول الأحداث الأمنية
    if mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "DESCRIBE security_events;" &>/dev/null; then
        events_count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM security_events;" 2>/dev/null | tail -1)
        print_test_result "جدول الأحداث الأمنية" "PASS" "موجود مع $events_count حدث"
        
        # اختبار إضافة حدث أمني
        if php artisan tinker --execute="
            DB::table('security_events')->insert([
                'type' => 'database_test',
                'message' => 'اختبار قاعدة البيانات',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo 'Security event inserted';
        " 2>/dev/null | grep -q "Security event inserted"; then
            print_test_result "إضافة حدث أمني" "PASS" "تم بنجاح"
            
            # حذف حدث الاختبار
            php artisan tinker --execute="DB::table('security_events')->where('type', 'database_test')->delete();" 2>/dev/null
        else
            print_test_result "إضافة حدث أمني" "FAIL" "فشل في الإضافة"
        fi
    else
        print_test_result "جدول الأحداث الأمنية" "FAIL" "غير موجود"
    fi
}

# دالة اختبار الأداء
test_database_performance() {
    print_header "اختبار أداء قاعدة البيانات"
    
    cd "$PROJECT_DIR" || exit 1
    
    # قياس حجم قاعدة البيانات
    db_size=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "
        SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Database Size (MB)'
        FROM information_schema.tables
        WHERE table_schema = '$DATABASE_NAME';
    " 2>/dev/null | tail -1)
    
    if [[ -n "$db_size" ]] && [[ "$db_size" != "NULL" ]]; then
        print_test_result "حجم قاعدة البيانات" "PASS" "${db_size} MB"
    else
        print_test_result "حجم قاعدة البيانات" "FAIL" "لا يمكن قياس الحجم"
    fi
    
    # اختبار سرعة الاستعلام
    start_time=$(date +%s.%N)
    mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM users;" &>/dev/null
    end_time=$(date +%s.%N)
    query_time=$(echo "$end_time - $start_time" | bc 2>/dev/null || echo "N/A")
    
    if [[ "$query_time" != "N/A" ]]; then
        print_test_result "سرعة الاستعلام" "PASS" "${query_time}s"
    else
        print_test_result "سرعة الاستعلام" "FAIL" "لا يمكن قياس الوقت"
    fi
    
    # التحقق من الفهارس
    indexes_info=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "
        SELECT COUNT(*) as total_indexes 
        FROM information_schema.statistics 
        WHERE table_schema = '$DATABASE_NAME';
    " 2>/dev/null | tail -1)
    
    if [[ -n "$indexes_info" ]]; then
        print_test_result "الفهارس" "PASS" "يوجد $indexes_info فهرس"
    else
        print_test_result "الفهارس" "FAIL" "لا يمكن قراءة الفهارس"
    fi
}

# دالة اختبار النسخ الاحتياطي للبيانات
test_database_backup() {
    print_header "اختبار النسخ الاحتياطي للبيانات"
    
    # التحقق من وجود نسخ احتياطية
    backup_dir="/var/backups/hajj-employment/database"
    
    if [[ -d "$backup_dir" ]]; then
        backup_files=($(ls -t "$backup_dir"/database_*.sql.gz 2>/dev/null))
        
        if [[ ${#backup_files[@]} -gt 0 ]]; then
            latest_backup="${backup_files[0]}"
            backup_size=$(ls -lh "$latest_backup" | awk '{print $5}')
            backup_date=$(ls -l "$latest_backup" | awk '{print $6, $7, $8}')
            
            print_test_result "النسخ الاحتياطي" "PASS" "آخر نسخة: $backup_date ($backup_size)"
            
            # اختبار استعادة نسخة احتياطية (محاكاة)
            if gunzip -t "$latest_backup" &>/dev/null; then
                print_test_result "سلامة النسخة الاحتياطية" "PASS" "الملف سليم وقابل للاستعادة"
            else
                print_test_result "سلامة النسخة الاحتياطية" "FAIL" "الملف تالف"
            fi
        else
            print_test_result "النسخ الاحتياطي" "FAIL" "لا يوجد نسخ احتياطية"
        fi
    else
        print_test_result "النسخ الاحتياطي" "FAIL" "مجلد النسخ الاحتياطي غير موجود"
    fi
}

# دالة طباعة تقرير قاعدة البيانات
print_database_report() {
    print_header "تقرير قاعدة البيانات"
    
    cd "$PROJECT_DIR" || exit 1
    
    echo -e "${PURPLE}📊 إحصائيات قاعدة البيانات:${NC}"
    
    # عدد المستخدمين
    if command -v mysql &>/dev/null; then
        users_count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM users;" 2>/dev/null | tail -1)
        echo -e "   • المستخدمين: ${BLUE}$users_count${NC}"
        
        departments_count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM departments;" 2>/dev/null | tail -1)
        echo -e "   • الأقسام: ${BLUE}$departments_count${NC}"
        
        jobs_count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM hajj_jobs;" 2>/dev/null | tail -1)
        echo -e "   • الوظائف: ${BLUE}$jobs_count${NC}"
        
        applications_count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM job_applications;" 2>/dev/null | tail -1)
        echo -e "   • الطلبات: ${BLUE}$applications_count${NC}"
        
        security_events_count=$(mysql -u"$DATABASE_USER" -p"$DATABASE_PASSWORD" -D"$DATABASE_NAME" -e "SELECT COUNT(*) FROM security_events;" 2>/dev/null | tail -1)
        echo -e "   • الأحداث الأمنية: ${BLUE}$security_events_count${NC}"
    fi
    
    echo
    echo -e "${CYAN}🔍 توصيات لتحسين الأداء:${NC}"
    echo -e "${YELLOW}   1. راجع الفهارس المضافة على الجداول الكبيرة${NC}"
    echo -e "${YELLOW}   2. نظف الجداول من البيانات القديمة${NC}"
    echo -e "${YELLOW}   3. راقب أداء الاستعلامات بانتظام${NC}"
    echo -e "${YELLOW}   4. تأكد من عمل النسخ الاحتياطي التلقائي${NC}"
}

# الدالة الرئيسية
main() {
    clear
    echo -e "${PURPLE}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${PURPLE}║                  اختبار قاعدة البيانات                      ║${NC}"
    echo -e "${PURPLE}║                   مناسك المشاعر - الحج                      ║${NC}"
    echo -e "${PURPLE}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo
    echo -e "${BLUE}📅 التاريخ: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
    echo
    
    # قراءة إعدادات قاعدة البيانات
    read_database_config
    
    # تنفيذ الاختبارات
    test_database_connection
    test_migrations
    test_core_tables
    test_roles_permissions
    test_basic_data
    test_database_security
    test_database_performance
    test_database_backup
    
    # طباعة التقرير
    print_database_report
    
    echo
    echo -e "${GREEN}✅ تم إنهاء اختبار قاعدة البيانات${NC}"
}

# تشغيل الاختبار
main "$@" 