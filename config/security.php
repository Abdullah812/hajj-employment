<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إعدادات الأمان - مناسك المشاعر
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحتوي على إعدادات الأمان لمراقبة الأحداث الأمنية
    | وإرسال التنبيهات والحماية من الهجمات
    |
    */

    /*
    |--------------------------------------------------------------------------
    | إيميلات المشرفين
    |--------------------------------------------------------------------------
    |
    | قائمة بإيميلات المشرفين الذين سيتم إرسال تنبيهات الأمان إليهم
    |
    */
    'admin_emails' => [
        env('SECURITY_ADMIN_EMAIL', 'admin@hajj-employment.com'),
        // يمكن إضافة المزيد من الإيميلات
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات Rate Limiting
    |--------------------------------------------------------------------------
    |
    | إعدادات تحديد عدد الطلبات المسموحة للمستخدمين
    |
    */
    'rate_limiting' => [
        'authenticated_users' => env('RATE_LIMIT_AUTHENTICATED', 100), // 100 طلب في الدقيقة
        'guests' => env('RATE_LIMIT_GUESTS', 60), // 60 طلب في الدقيقة
        'login_attempts' => env('RATE_LIMIT_LOGIN', 5), // 5 محاولات تسجيل دخول في 15 دقيقة
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات مراقبة الأمان
    |--------------------------------------------------------------------------
    |
    | إعدادات لتفعيل أو إيقاف مراقبة أنواع مختلفة من الهجمات
    |
    */
    'monitoring' => [
        'sql_injection' => env('MONITOR_SQL_INJECTION', true),
        'xss_attempts' => env('MONITOR_XSS', true),
        'path_traversal' => env('MONITOR_PATH_TRAVERSAL', true),
        'suspicious_user_agents' => env('MONITOR_USER_AGENTS', true),
        'sensitive_file_access' => env('MONITOR_SENSITIVE_FILES', true),
        'excessive_login_attempts' => env('MONITOR_LOGIN_ATTEMPTS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات التنبيهات
    |--------------------------------------------------------------------------
    |
    | إعدادات متى يتم إرسال التنبيهات للمشرفين
    |
    */
    'alerts' => [
        'enabled' => env('SECURITY_ALERTS_ENABLED', true),
        'email_enabled' => env('SECURITY_EMAIL_ALERTS', true),
        'critical_events_only' => env('SECURITY_CRITICAL_ONLY', false),
        'alert_threshold' => env('SECURITY_ALERT_THRESHOLD', 5), // عدد الأحداث قبل إرسال التنبيه
    ],

    /*
    |--------------------------------------------------------------------------
    | أنماط الكشف
    |--------------------------------------------------------------------------
    |
    | أنماط regex للكشف عن الهجمات المختلفة
    |
    */
    'detection_patterns' => [
        'sql_injection' => [
            '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
            '/(\bor\b.*\b1\s*=\s*1\b)|(\b1\s*=\s*1\b.*\bor\b)/i',
            '/(\band\b.*\b1\s*=\s*1\b)|(\b1\s*=\s*1\b.*\band\b)/i',
            '/(\bdrop\b.*\btable\b)|(\btable\b.*\bdrop\b)/i',
            '/(\binsert\b.*\binto\b)|(\binto\b.*\binsert\b)/i',
            '/(\bupdate\b.*\bset\b)|(\bset\b.*\bupdate\b)/i',
            '/(\bdelete\b.*\bfrom\b)|(\bfrom\b.*\bdelete\b)/i',
            '/(\bselect\b.*\bfrom\b)|(\bfrom\b.*\bselect\b)/i',
            '/(\bexec\b.*\bxp_)/i',
            '/(\bsp_)/i',
            '/(\bxp_)/i',
        ],
        
        'xss' => [
            '/<script[^>]*>.*?<\/script>/i',
            '/<iframe[^>]*>.*?<\/iframe>/i',
            '/<object[^>]*>.*?<\/object>/i',
            '/<embed[^>]*>.*?<\/embed>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/document\.cookie/i',
            '/document\.write/i',
            '/eval\s*\(/i',
            '/alert\s*\(/i',
        ],
        
        'path_traversal' => [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/',
            '/%2e%2e%5c/',
            '/etc\/passwd/',
            '/etc\/shadow/',
            '/windows\/system32/',
            '/boot\.ini/',
            '/win\.ini/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Agents مشبوهة
    |--------------------------------------------------------------------------
    |
    | قائمة بـ User Agents المشبوهة التي قد تشير إلى أدوات اختراق
    |
    */
    'suspicious_user_agents' => [
        'sqlmap', 'nmap', 'nikto', 'dirb', 'gobuster', 'wfuzz',
        'burp', 'zap', 'acunetix', 'netsparker', 'curl', 'wget',
        'python-requests', 'python-urllib', 'bot', 'crawler',
        'spider', 'scraper', 'harvester', 'extractor'
    ],

    /*
    |--------------------------------------------------------------------------
    | ملفات حساسة
    |--------------------------------------------------------------------------
    |
    | قائمة بالملفات الحساسة التي يجب مراقبة محاولات الوصول إليها
    |
    */
    'sensitive_files' => [
        '.env', '.git', '.htaccess', '.htpasswd',
        'wp-config.php', 'config.php', 'database.php',
        'phpinfo.php', 'info.php', 'test.php', 'admin.php',
        'backup.sql', 'dump.sql', 'database.sql',
        'composer.json', 'package.json', 'artisan',
        'web.config', 'app.config', 'settings.ini'
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات التسجيل
    |--------------------------------------------------------------------------
    |
    | إعدادات تسجيل الأحداث الأمنية
    |
    */
    'logging' => [
        'enabled' => env('SECURITY_LOGGING_ENABLED', true),
        'log_channel' => env('SECURITY_LOG_CHANNEL', 'security'),
        'log_level' => env('SECURITY_LOG_LEVEL', 'warning'),
        'database_logging' => env('SECURITY_DATABASE_LOGGING', true),
        'retention_days' => env('SECURITY_LOG_RETENTION', 30), // الاحتفاظ بالسجلات لمدة 30 يوم
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات الحظر التلقائي
    |--------------------------------------------------------------------------
    |
    | إعدادات حظر عناوين IP المشبوهة تلقائياً
    |
    */
    'auto_blocking' => [
        'enabled' => env('SECURITY_AUTO_BLOCKING', false),
        'block_duration' => env('SECURITY_BLOCK_DURATION', 3600), // حظر لمدة ساعة
        'block_threshold' => env('SECURITY_BLOCK_THRESHOLD', 10), // حظر بعد 10 محاولات
        'whitelist_ips' => [
            '127.0.0.1',
            '::1',
            // يمكن إضافة عناوين IP موثوقة هنا
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات التقارير
    |--------------------------------------------------------------------------
    |
    | إعدادات تقارير الأمان اليومية والأسبوعية
    |
    */
    'reports' => [
        'daily_summary' => env('SECURITY_DAILY_REPORTS', true),
        'weekly_summary' => env('SECURITY_WEEKLY_REPORTS', true),
        'monthly_summary' => env('SECURITY_MONTHLY_REPORTS', true),
        'export_format' => env('SECURITY_REPORT_FORMAT', 'json'), // json, csv, pdf
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات الإشعارات المتقدمة
    |--------------------------------------------------------------------------
    |
    | إعدادات إرسال الإشعارات عبر قنوات مختلفة
    |
    */
    'notification_channels' => [
        'email' => env('SECURITY_EMAIL_NOTIFICATIONS', true),
        'sms' => env('SECURITY_SMS_NOTIFICATIONS', false),
        'slack' => env('SECURITY_SLACK_NOTIFICATIONS', false),
        'telegram' => env('SECURITY_TELEGRAM_NOTIFICATIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | إعدادات التكامل
    |--------------------------------------------------------------------------
    |
    | إعدادات التكامل مع خدمات أمنية خارجية
    |
    */
    'integrations' => [
        'fail2ban' => env('SECURITY_FAIL2BAN', false),
        'cloudflare' => env('SECURITY_CLOUDFLARE', false),
        'maxmind' => env('SECURITY_MAXMIND', false),
        'virustotal' => env('SECURITY_VIRUSTOTAL', false),
    ],
]; 