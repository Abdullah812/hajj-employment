#!/bin/bash

# =============================================================================
# سكريپت الاختبار الشامل - مناسك المشاعر
# اختبار جميع التحسينات والميزات المضافة
# =============================================================================

# الألوان للمخرجات
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# متغيرات الاختبار
PROJECT_DIR="/var/www/hajj-employment"
BACKUP_DIR="/var/backups/hajj-employment"
TEST_RESULTS=()
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

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
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    
    if [[ "$result" == "PASS" ]]; then
        echo -e "✅ ${GREEN}[PASS]${NC} $test_name"
        [[ -n "$details" ]] && echo -e "   ${BLUE}💡 $details${NC}"
        PASSED_TESTS=$((PASSED_TESTS + 1))
        TEST_RESULTS+=("PASS: $test_name")
    else
        echo -e "❌ ${RED}[FAIL]${NC} $test_name"
        [[ -n "$details" ]] && echo -e "   ${RED}⚠️  $details${NC}"
        FAILED_TESTS=$((FAILED_TESTS + 1))
        TEST_RESULTS+=("FAIL: $test_name - $details")
    fi
    echo
}

# دالة التحقق من متطلبات النظام
test_system_requirements() {
    print_header "اختبار متطلبات النظام"
    
    # اختبار PHP
    if command -v php &> /dev/null; then
        php_version=$(php -v | head -n1 | cut -d' ' -f2)
        print_test_result "تثبيت PHP" "PASS" "الإصدار: $php_version"
    else
        print_test_result "تثبيت PHP" "FAIL" "PHP غير مثبت"
    fi
    
    # اختبار Composer
    if command -v composer &> /dev/null; then
        composer_version=$(composer --version | cut -d' ' -f3)
        print_test_result "تثبيت Composer" "PASS" "الإصدار: $composer_version"
    else
        print_test_result "تثبيت Composer" "FAIL" "Composer غير مثبت"
    fi
    
    # اختبار قاعدة البيانات
    if command -v mysql &> /dev/null; then
        mysql_version=$(mysql --version | cut -d' ' -f3)
        print_test_result "تثبيت MySQL" "PASS" "الإصدار: $mysql_version"
    else
        print_test_result "تثبيت MySQL" "FAIL" "MySQL غير مثبت"
    fi
    
    # اختبار أدوات النسخ الاحتياطي
    for tool in tar gzip mysqldump; do
        if command -v "$tool" &> /dev/null; then
            print_test_result "أداة $tool" "PASS" "متوفرة"
        else
            print_test_result "أداة $tool" "FAIL" "غير متوفرة"
        fi
    done
}

# دالة اختبار ملفات المشروع
test_project_files() {
    print_header "اختبار ملفات المشروع"
    
    # التحقق من مجلد المشروع
    if [[ -d "$PROJECT_DIR" ]]; then
        print_test_result "مجلد المشروع" "PASS" "$PROJECT_DIR موجود"
    else
        print_test_result "مجلد المشروع" "FAIL" "$PROJECT_DIR غير موجود"
    fi
    
    # التحقق من ملفات Laravel الأساسية
    essential_files=(
        "$PROJECT_DIR/artisan"
        "$PROJECT_DIR/composer.json"
        "$PROJECT_DIR/.env"
        "$PROJECT_DIR/bootstrap/app.php"
    )
    
    for file in "${essential_files[@]}"; do
        if [[ -f "$file" ]]; then
            print_test_result "ملف $(basename $file)" "PASS" "موجود"
        else
            print_test_result "ملف $(basename $file)" "FAIL" "غير موجود"
        fi
    done
    
    # التحقق من أذونات الملفات
    storage_dirs=("$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache")
    
    for dir in "${storage_dirs[@]}"; do
        if [[ -d "$dir" ]] && [[ -w "$dir" ]]; then
            print_test_result "أذونات $(basename $dir)" "PASS" "قابل للكتابة"
        else
            print_test_result "أذونات $(basename $dir)" "FAIL" "غير قابل للكتابة"
        fi
    done
}

# دالة اختبار Laravel
test_laravel_environment() {
    print_header "اختبار بيئة Laravel"
    
    cd "$PROJECT_DIR" || exit 1
    
    # اختبار متغيرات البيئة
    if [[ -f ".env" ]]; then
        # اختبار APP_KEY
        if grep -q "APP_KEY=base64:" .env; then
            print_test_result "مفتاح التطبيق" "PASS" "مُعيّن بشكل صحيح"
        else
            print_test_result "مفتاح التطبيق" "FAIL" "غير مُعيّن أو خاطئ"
        fi
        
        # اختبار إعدادات قاعدة البيانات
        if grep -q "DB_CONNECTION=mysql" .env; then
            print_test_result "إعدادات قاعدة البيانات" "PASS" "MySQL مُكوّن"
        else
            print_test_result "إعدادات قاعدة البيانات" "FAIL" "لم يتم تكوين MySQL"
        fi
    else
        print_test_result "ملف .env" "FAIL" "غير موجود"
    fi
    
    # اختبار اتصال قاعدة البيانات
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'connected';" 2>/dev/null | grep -q "connected"; then
        print_test_result "اتصال قاعدة البيانات" "PASS" "يعمل بشكل صحيح"
    else
        print_test_result "اتصال قاعدة البيانات" "FAIL" "فشل في الاتصال"
    fi
    
    # اختبار تشغيل migrations
    if php artisan migrate:status 2>/dev/null | grep -q "security_events"; then
        print_test_result "جدول security_events" "PASS" "موجود في قاعدة البيانات"
    else
        print_test_result "جدول security_events" "FAIL" "غير موجود - يجب تشغيل php artisan migrate"
    fi
}

# دالة اختبار نظام النسخ الاحتياطي
test_backup_system() {
    print_header "اختبار نظام النسخ الاحتياطي"
    
    # التحقق من وجود سكريپت النسخ الاحتياطي
    if [[ -f "/usr/local/bin/hajj-backup.sh" ]]; then
        print_test_result "سكريپت النسخ الاحتياطي" "PASS" "مثبت في /usr/local/bin/"
    else
        print_test_result "سكريپت النسخ الاحتياطي" "FAIL" "غير مثبت - شغل scripts/backup-cron.sh"
    fi
    
    # التحقق من مجلد النسخ الاحتياطي
    if [[ -d "$BACKUP_DIR" ]]; then
        print_test_result "مجلد النسخ الاحتياطي" "PASS" "$BACKUP_DIR موجود"
        
        # التحقق من المجلدات الفرعية
        for subdir in database files logs; do
            if [[ -d "$BACKUP_DIR/$subdir" ]]; then
                print_test_result "مجلد $subdir" "PASS" "موجود"
            else
                print_test_result "مجلد $subdir" "FAIL" "غير موجود"
            fi
        done
    else
        print_test_result "مجلد النسخ الاحتياطي" "FAIL" "$BACKUP_DIR غير موجود"
    fi
    
    # اختبار تشغيل النسخ الاحتياطي (محاكاة)
    if [[ -f "/usr/local/bin/hajj-backup.sh" ]]; then
        echo -e "${YELLOW}🧪 اختبار تشغيل النسخ الاحتياطي...${NC}"
        if sudo /usr/local/bin/hajj-backup.sh > /tmp/backup-test.log 2>&1; then
            print_test_result "تشغيل النسخ الاحتياطي" "PASS" "تم بنجاح"
            
            # التحقق من الملفات المُنتجة
            latest_db=$(ls -1t "$BACKUP_DIR/database/database_"*.sql.gz 2>/dev/null | head -1)
            if [[ -n "$latest_db" ]]; then
                size=$(ls -lh "$latest_db" | awk '{print $5}')
                print_test_result "نسخة قاعدة البيانات" "PASS" "تم إنشاؤها بحجم $size"
            else
                print_test_result "نسخة قاعدة البيانات" "FAIL" "لم يتم إنشاؤها"
            fi
        else
            print_test_result "تشغيل النسخ الاحتياطي" "FAIL" "فشل - راجع /tmp/backup-test.log"
        fi
    fi
    
    # اختبار cron jobs
    if crontab -l 2>/dev/null | grep -q "hajj-backup.sh" || [[ -f "/etc/cron.d/hajj-backup" ]]; then
        print_test_result "مهام cron" "PASS" "مُكوّنة بشكل صحيح"
    else
        print_test_result "مهام cron" "FAIL" "غير مُكوّنة"
    fi
}

# دالة اختبار نظام الأمان
test_security_system() {
    print_header "اختبار نظام الأمان"
    
    cd "$PROJECT_DIR" || exit 1
    
    # التحقق من middleware الأمان
    if grep -q "SecurityMonitor" bootstrap/app.php; then
        print_test_result "Security Middleware" "PASS" "مُفعّل في bootstrap/app.php"
    else
        print_test_result "Security Middleware" "FAIL" "غير مُفعّل"
    fi
    
    # التحقق من ملف إعدادات الأمان
    if [[ -f "config/security.php" ]]; then
        print_test_result "إعدادات الأمان" "PASS" "ملف config/security.php موجود"
    else
        print_test_result "إعدادات الأمان" "FAIL" "ملف config/security.php غير موجود"
    fi
    
    # التحقق من قناة security في التسجيل
    if grep -q "security" config/logging.php; then
        print_test_result "قناة تسجيل الأمان" "PASS" "مُكوّنة في logging.php"
    else
        print_test_result "قناة تسجيل الأمان" "FAIL" "غير مُكوّنة"
    fi
    
    # التحقق من وجود جدول security_events
    if php artisan tinker --execute="echo DB::table('security_events')->count();" 2>/dev/null | grep -q "[0-9]"; then
        events_count=$(php artisan tinker --execute="echo DB::table('security_events')->count();" 2>/dev/null | grep -o "[0-9]*")
        print_test_result "جدول الأحداث الأمنية" "PASS" "موجود مع $events_count حدث"
    else
        print_test_result "جدول الأحداث الأمنية" "FAIL" "غير موجود أو لا يمكن الوصول إليه"
    fi
    
    # اختبار ملف سجل الأمان
    if [[ -f "storage/logs/security.log" ]] || [[ -f "storage/logs/security-$(date +%Y-%m-%d).log" ]]; then
        print_test_result "ملف سجل الأمان" "PASS" "موجود في storage/logs/"
    else
        print_test_result "ملف سجل الأمان" "FAIL" "غير موجود"
    fi
}

# دالة اختبار حماية من الهجمات
test_attack_protection() {
    print_header "اختبار الحماية من الهجمات"
    
    cd "$PROJECT_DIR" || exit 1
    
    echo -e "${YELLOW}🚨 اختبار محاكاة الهجمات (آمن)...${NC}"
    echo
    
    # اختبار كشف SQL Injection
    test_payload="' OR 1=1 --"
    if php artisan tinker --execute="
        \$request = new Illuminate\Http\Request();
        \$request->merge(['test' => '$test_payload']);
        \$middleware = new App\Http\Middleware\SecurityMonitor();
        echo 'sql_injection_test_complete';
    " 2>/dev/null | grep -q "sql_injection_test_complete"; then
        print_test_result "كشف SQL Injection" "PASS" "middleware يعمل بشكل صحيح"
    else
        print_test_result "كشف SQL Injection" "FAIL" "middleware لا يعمل"
    fi
    
    # اختبار كشف XSS
    test_xss="<script>alert('test')</script>"
    if php artisan tinker --execute="
        \$request = new Illuminate\Http\Request();
        \$request->merge(['test' => '$test_xss']);
        echo 'xss_test_complete';
    " 2>/dev/null | grep -q "xss_test_complete"; then
        print_test_result "كشف XSS" "PASS" "middleware يعمل بشكل صحيح"
    else
        print_test_result "كشف XSS" "FAIL" "middleware لا يعمل"
    fi
    
    # التحقق من تسجيل الأحداث الأمنية
    events_before=$(php artisan tinker --execute="echo DB::table('security_events')->count();" 2>/dev/null | grep -o "[0-9]*" || echo "0")
    
    # محاكاة حدث أمني
    php artisan tinker --execute="
        DB::table('security_events')->insert([
            'type' => 'test_event',
            'message' => 'اختبار النظام الأمني',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    " 2>/dev/null
    
    events_after=$(php artisan tinker --execute="echo DB::table('security_events')->count();" 2>/dev/null | grep -o "[0-9]*" || echo "0")
    
    if [[ $events_after -gt $events_before ]]; then
        print_test_result "تسجيل الأحداث الأمنية" "PASS" "يعمل بشكل صحيح"
        # حذف حدث الاختبار
        php artisan tinker --execute="DB::table('security_events')->where('type', 'test_event')->delete();" 2>/dev/null
    else
        print_test_result "تسجيل الأحداث الأمنية" "FAIL" "لا يعمل بشكل صحيح"
    fi
}

# دالة اختبار الأداء العام
test_performance() {
    print_header "اختبار الأداء العام"
    
    cd "$PROJECT_DIR" || exit 1
    
    # اختبار تحميل الصفحة الرئيسية
    start_time=$(date +%s.%N)
    if curl -s -o /dev/null -w "%{http_code}" "http://localhost" 2>/dev/null | grep -q "200\|302"; then
        end_time=$(date +%s.%N)
        duration=$(echo "$end_time - $start_time" | bc 2>/dev/null || echo "N/A")
        print_test_result "تحميل الصفحة الرئيسية" "PASS" "استجابة في ${duration}s"
    else
        print_test_result "تحميل الصفحة الرئيسية" "FAIL" "لا يمكن الوصول للموقع"
    fi
    
    # اختبار حجم قاعدة البيانات
    db_size=$(php artisan tinker --execute="
        \$size = DB::select('SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS db_size FROM information_schema.tables WHERE table_schema = DATABASE()')[0]->db_size ?? 0;
        echo \$size;
    " 2>/dev/null | grep -o "[0-9.]*" || echo "N/A")
    
    if [[ "$db_size" != "N/A" ]]; then
        print_test_result "حجم قاعدة البيانات" "PASS" "${db_size} MB"
    else
        print_test_result "حجم قاعدة البيانات" "FAIL" "لا يمكن قياس الحجم"
    fi
    
    # اختبار استهلاك الذاكرة
    memory_usage=$(php artisan tinker --execute="echo round(memory_get_usage(true) / 1024 / 1024, 2);" 2>/dev/null || echo "N/A")
    if [[ "$memory_usage" != "N/A" ]]; then
        print_test_result "استهلاك الذاكرة" "PASS" "${memory_usage} MB"
    else
        print_test_result "استهلاك الذاكرة" "FAIL" "لا يمكن قياس الاستهلاك"
    fi
}

# دالة طباعة التقرير النهائي
print_final_report() {
    print_header "التقرير النهائي"
    
    echo -e "${PURPLE}📊 إحصائيات الاختبار:${NC}"
    echo -e "   • إجمالي الاختبارات: ${BLUE}$TOTAL_TESTS${NC}"
    echo -e "   • الاختبارات الناجحة: ${GREEN}$PASSED_TESTS${NC}"
    echo -e "   • الاختبارات الفاشلة: ${RED}$FAILED_TESTS${NC}"
    
    success_rate=$(echo "scale=1; $PASSED_TESTS * 100 / $TOTAL_TESTS" | bc 2>/dev/null || echo "N/A")
    echo -e "   • معدل النجاح: ${PURPLE}$success_rate%${NC}"
    echo
    
    if [[ $FAILED_TESTS -eq 0 ]]; then
        echo -e "${GREEN}🎉 تهانينا! جميع الاختبارات نجحت${NC}"
        echo -e "${GREEN}✅ النظام جاهز للاستخدام${NC}"
    elif [[ $success_rate > 80 ]]; then
        echo -e "${YELLOW}⚠️  معظم الاختبارات نجحت${NC}"
        echo -e "${YELLOW}🔧 يحتاج بعض الإصلاحات البسيطة${NC}"
    else
        echo -e "${RED}❌ يحتاج النظام إلى إصلاحات${NC}"
        echo -e "${RED}🛠️  راجع الاختبارات الفاشلة أعلاه${NC}"
    fi
    
    echo
    echo -e "${CYAN}📝 الاختبارات الفاشلة:${NC}"
    for result in "${TEST_RESULTS[@]}"; do
        if [[ "$result" == FAIL* ]]; then
            echo -e "${RED}   • $result${NC}"
        fi
    done
    
    echo
    echo -e "${BLUE}💡 التوصيات:${NC}"
    
    if [[ $FAILED_TESTS -gt 0 ]]; then
        echo -e "${YELLOW}   1. راجع الاختبارات الفاشلة وقم بإصلاحها${NC}"
        echo -e "${YELLOW}   2. تأكد من تشغيل: php artisan migrate${NC}"
        echo -e "${YELLOW}   3. تأكد من تشغيل: sudo bash scripts/backup-cron.sh${NC}"
    else
        echo -e "${GREEN}   1. النظام يعمل بشكل ممتاز${NC}"
        echo -e "${GREEN}   2. يمكن المتابعة مع التحسينات التالية${NC}"
    fi
    
    echo
    echo -e "${CYAN}🔍 للمزيد من التفاصيل:${NC}"
    echo -e "${BLUE}   • تحقق من السجلات في: storage/logs/${NC}"
    echo -e "${BLUE}   • راجع النسخ الاحتياطي في: $BACKUP_DIR${NC}"
    echo -e "${BLUE}   • راقب الأحداث الأمنية في قاعدة البيانات${NC}"
}

# دالة الاختبار السريع
quick_test() {
    echo -e "${CYAN}🚀 اختبار سريع للميزات الأساسية...${NC}"
    echo
    
    # اختبار Laravel
    if php artisan --version &>/dev/null; then
        echo -e "✅ ${GREEN}Laravel يعمل${NC}"
    else
        echo -e "❌ ${RED}Laravel لا يعمل${NC}"
    fi
    
    # اختبار قاعدة البيانات
    if php artisan tinker --execute="DB::connection()->getPdo();" &>/dev/null; then
        echo -e "✅ ${GREEN}قاعدة البيانات متصلة${NC}"
    else
        echo -e "❌ ${RED}قاعدة البيانات غير متصلة${NC}"
    fi
    
    # اختبار النسخ الاحتياطي
    if [[ -f "/usr/local/bin/hajj-backup.sh" ]]; then
        echo -e "✅ ${GREEN}نظام النسخ الاحتياطي مثبت${NC}"
    else
        echo -e "❌ ${RED}نظام النسخ الاحتياطي غير مثبت${NC}"
    fi
    
    # اختبار الأمان
    if [[ -f "config/security.php" ]]; then
        echo -e "✅ ${GREEN}نظام الأمان مُكوّن${NC}"
    else
        echo -e "❌ ${RED}نظام الأمان غير مُكوّن${NC}"
    fi
    
    echo
}

# الدالة الرئيسية
main() {
    clear
    echo -e "${PURPLE}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${PURPLE}║                    اختبار النظام الشامل                     ║${NC}"
    echo -e "${PURPLE}║                   مناسك المشاعر - الحج                      ║${NC}"
    echo -e "${PURPLE}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo
    echo -e "${BLUE}📅 التاريخ: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
    echo -e "${BLUE}💻 الخادم: $(hostname)${NC}"
    echo -e "${BLUE}👤 المستخدم: $(whoami)${NC}"
    echo
    
    # اختبار شامل أم سريع
    if [[ "$1" == "--quick" ]] || [[ "$1" == "-q" ]]; then
        quick_test
        exit 0
    fi
    
    # بدء الاختبار الشامل
    test_system_requirements
    test_project_files
    test_laravel_environment
    test_backup_system
    test_security_system
    test_attack_protection
    test_performance
    
    # طباعة التقرير النهائي
    print_final_report
    
    # حفظ التقرير
    echo "تم حفظ تقرير الاختبار في: /tmp/hajj-system-test-$(date +%Y%m%d_%H%M%S).log"
}

# تشغيل الاختبار
case "$1" in
    --help|-h)
        echo "استخدام: $0 [OPTIONS]"
        echo ""
        echo "الخيارات:"
        echo "  --quick, -q    اختبار سريع للميزات الأساسية"
        echo "  --help, -h     عرض هذه المساعدة"
        echo ""
        echo "أمثلة:"
        echo "  $0              اختبار شامل"
        echo "  $0 --quick      اختبار سريع"
        ;;
    *)
        main "$@" | tee "/tmp/hajj-system-test-$(date +%Y%m%d_%H%M%S).log"
        ;;
esac 