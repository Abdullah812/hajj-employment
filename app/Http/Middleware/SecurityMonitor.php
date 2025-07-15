<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SecurityMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. تسجيل محاولات الوصول المشبوهة
        $this->logSuspiciousActivity($request);
        
        // 2. حماية من الهجمات المتكررة
        if ($this->isRateLimited($request)) {
            $this->logSecurityEvent($request, 'rate_limit_exceeded', 'تم تجاوز الحد المسموح للطلبات');
            return response()->json(['error' => 'تم تجاوز الحد المسموح للطلبات'], 429);
        }
        
        // 3. كشف محاولات SQL Injection
        if ($this->detectSQLInjection($request)) {
            $this->logSecurityEvent($request, 'sql_injection_attempt', 'محاولة SQL Injection');
            return response()->json(['error' => 'طلب غير صالح'], 400);
        }
        
        // 4. كشف محاولات XSS
        if ($this->detectXSS($request)) {
            $this->logSecurityEvent($request, 'xss_attempt', 'محاولة XSS');
            return response()->json(['error' => 'طلب غير صالح'], 400);
        }
        
        // 5. كشف محاولات Path Traversal
        if ($this->detectPathTraversal($request)) {
            $this->logSecurityEvent($request, 'path_traversal_attempt', 'محاولة Path Traversal');
            return response()->json(['error' => 'طلب غير صالح'], 400);
        }
        
        // 6. مراقبة تسجيل الدخول الفاشل
        if ($this->isLoginAttempt($request)) {
            $this->monitorLoginAttempts($request);
        }
        
        $response = $next($request);
        
        // 7. تسجيل الأحداث الأمنية الهامة
        $this->logImportantSecurityEvents($request, $response);
        
        return $response;
    }
    
    /**
     * تسجيل الأنشطة المشبوهة
     */
    private function logSuspiciousActivity(Request $request): void
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $url = $request->fullUrl();
        
        // كشف User Agents مشبوهة
        $suspiciousUserAgents = [
            'sqlmap', 'nmap', 'nikto', 'dirb', 'gobuster', 'wfuzz',
            'burp', 'zap', 'acunetix', 'netsparker', 'curl', 'wget'
        ];
        
        foreach ($suspiciousUserAgents as $suspicious) {
            if (str_contains(strtolower($userAgent), $suspicious)) {
                $this->logSecurityEvent($request, 'suspicious_user_agent', "User Agent مشبوه: $userAgent");
                break;
            }
        }
        
        // كشف طلبات الوصول لملفات حساسة
        $sensitiveFiles = [
            '.env', '.git', 'wp-config.php', 'config.php', 'database.php',
            'phpinfo.php', 'info.php', 'test.php', 'admin.php'
        ];
        
        foreach ($sensitiveFiles as $file) {
            if (str_contains($url, $file)) {
                $this->logSecurityEvent($request, 'sensitive_file_access', "محاولة الوصول لملف حساس: $file");
                break;
            }
        }
    }
    
    /**
     * التحقق من تجاوز الحد المسموح للطلبات
     */
    private function isRateLimited(Request $request): bool
    {
        $key = 'rate_limit:' . $request->ip();
        
        // السماح بـ 60 طلب في الدقيقة للمستخدم العادي
        if (Auth::check()) {
            return RateLimiter::tooManyAttempts($key, 60);
        }
        
        // السماح بـ 30 طلب في الدقيقة للزوار
        return RateLimiter::tooManyAttempts($key, 30);
    }
    
    /**
     * كشف محاولات SQL Injection
     */
    private function detectSQLInjection(Request $request): bool
    {
        $sqlPatterns = [
            '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
            '/(\bor\b.*\b1\s*=\s*1\b)|(\b1\s*=\s*1\b.*\bor\b)/i',
            '/(\band\b.*\b1\s*=\s*1\b)|(\b1\s*=\s*1\b.*\band\b)/i',
            '/(\bor\b.*\b1\s*=\s*2\b)|(\b1\s*=\s*2\b.*\bor\b)/i',
            '/(\bdrop\b.*\btable\b)|(\btable\b.*\bdrop\b)/i',
            '/(\binsert\b.*\binto\b)|(\binto\b.*\binsert\b)/i',
            '/(\bupdate\b.*\bset\b)|(\bset\b.*\bupdate\b)/i',
            '/(\bdelete\b.*\bfrom\b)|(\bfrom\b.*\bdelete\b)/i',
            '/(\bselect\b.*\bfrom\b)|(\bfrom\b.*\bselect\b)/i',
            '/(\bexec\b.*\bxp_)/i',
            '/(\bsp_)/i',
            '/(\bxp_)/i',
            '/(\'|\")(\s*)(\bunion\b|\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b)/i'
        ];
        
        $allInputs = array_merge($request->all(), $request->headers->all());
        
        foreach ($allInputs as $input) {
            if (is_string($input)) {
                foreach ($sqlPatterns as $pattern) {
                    if (preg_match($pattern, $input)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * كشف محاولات XSS
     */
    private function detectXSS(Request $request): bool
    {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/i',
            '/<iframe[^>]*>.*?<\/iframe>/i',
            '/<object[^>]*>.*?<\/object>/i',
            '/<embed[^>]*>.*?<\/embed>/i',
            '/<form[^>]*>.*?<\/form>/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/onfocus\s*=/i',
            '/onblur\s*=/i',
            '/onchange\s*=/i',
            '/onsubmit\s*=/i',
            '/document\.cookie/i',
            '/document\.write/i',
            '/eval\s*\(/i',
            '/alert\s*\(/i',
            '/confirm\s*\(/i',
            '/prompt\s*\(/i'
        ];
        
        $allInputs = array_merge($request->all(), $request->headers->all());
        
        foreach ($allInputs as $input) {
            if (is_string($input)) {
                foreach ($xssPatterns as $pattern) {
                    if (preg_match($pattern, $input)) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * كشف محاولات Path Traversal
     */
    private function detectPathTraversal(Request $request): bool
    {
        $pathPatterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/',
            '/%2e%2e%5c/',
            '/\.\.%2f/',
            '/\.\.%5c/',
            '/%2e%2e\//',
            '/%2e%2e\\\\/',
            '/etc\/passwd/',
            '/etc\/shadow/',
            '/etc\/hosts/',
            '/windows\/system32/',
            '/boot\.ini/',
            '/win\.ini/'
        ];
        
        $allInputs = array_merge($request->all(), [$request->getPathInfo()]);
        
        foreach ($allInputs as $input) {
            if (is_string($input)) {
                foreach ($pathPatterns as $pattern) {
                    if (preg_match($pattern, strtolower($input))) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * التحقق من كون الطلب محاولة تسجيل دخول
     */
    private function isLoginAttempt(Request $request): bool
    {
        return $request->is('login') && $request->isMethod('POST');
    }
    
    /**
     * مراقبة محاولات تسجيل الدخول
     */
    private function monitorLoginAttempts(Request $request): void
    {
        $ip = $request->ip();
        $email = $request->input('email');
        
        // عد محاولات تسجيل الدخول الفاشلة
        $key = 'login_attempts:' . $ip;
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= 5) {
            $this->logSecurityEvent($request, 'excessive_login_attempts', "محاولات تسجيل دخول مفرطة من IP: $ip");
            $this->sendSecurityAlert("محاولات تسجيل دخول مفرطة", "IP: $ip حاول تسجيل الدخول $attempts مرة");
        }
        
        // تسجيل محاولة تسجيل الدخول
        Cache::put($key, $attempts + 1, now()->addMinutes(15));
        
        // مراقبة محاولات تسجيل الدخول بأسماء مستخدمين مختلفة
        if ($email) {
            $emailKey = 'login_emails:' . $ip;
            $emails = Cache::get($emailKey, []);
            
            if (!in_array($email, $emails)) {
                $emails[] = $email;
                Cache::put($emailKey, $emails, now()->addMinutes(15));
                
                if (count($emails) > 3) {
                    $this->logSecurityEvent($request, 'multiple_email_attempts', "محاولة تسجيل دخول بعدة أيميلات من IP: $ip");
                }
            }
        }
    }
    
    /**
     * تسجيل الأحداث الأمنية الهامة
     */
    private function logImportantSecurityEvents(Request $request, Response $response): void
    {
        // تسجيل الدخول الناجح
        if (Auth::check() && $request->is('login') && $request->isMethod('POST')) {
            $user = Auth::user();
            $this->logSecurityEvent($request, 'successful_login', "تسجيل دخول ناجح للمستخدم: {$user->email}");
        }
        
        // تسجيل الخروج
        if ($request->is('logout') && $request->isMethod('POST')) {
            $this->logSecurityEvent($request, 'logout', "تسجيل خروج للمستخدم");
        }
        
        // تغيير كلمة المرور
        if ($request->is('password/*') && $request->isMethod('POST')) {
            $this->logSecurityEvent($request, 'password_change', "تغيير كلمة المرور");
        }
        
        // الوصول للوحة الإدارة
        if ($request->is('admin/*') && $response->getStatusCode() == 200) {
            $user = Auth::user();
            if ($user) {
                $this->logSecurityEvent($request, 'admin_access', "الوصول للوحة الإدارة بواسطة: {$user->email}");
            }
        }
    }
    
    /**
     * تسجيل حدث أمني
     */
    private function logSecurityEvent(Request $request, string $type, string $message): void
    {
        $data = [
            'type' => $type,
            'message' => $message,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toDateTimeString(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'referer' => $request->header('referer'),
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ];
        
        // تسجيل في سجل الأمان
        Log::channel('security')->warning($message, $data);
        
        // تخزين في قاعدة البيانات للتحليل
        $this->storeSecurityEvent($data);
        
        // إرسال تنبيه للأحداث الحرجة
        $criticalEvents = ['sql_injection_attempt', 'xss_attempt', 'path_traversal_attempt', 'excessive_login_attempts'];
        if (in_array($type, $criticalEvents)) {
            $this->sendSecurityAlert($type, $message);
        }
    }
    
    /**
     * تخزين الحدث الأمني في قاعدة البيانات
     */
    private function storeSecurityEvent(array $data): void
    {
        try {
            \DB::table('security_events')->insert([
                'type' => $data['type'],
                'message' => $data['message'],
                'ip_address' => $data['ip'],
                'user_agent' => $data['user_agent'],
                'url' => $data['url'],
                'method' => $data['method'],
                'user_id' => $data['user_id'],
                'session_id' => $data['session_id'],
                'referer' => $data['referer'],
                'headers' => json_encode($data['headers']),
                'payload' => json_encode($data['payload']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('فشل في تخزين الحدث الأمني: ' . $e->getMessage());
        }
    }
    
    /**
     * إرسال تنبيه أمني
     */
    private function sendSecurityAlert(string $type, string $message): void
    {
        try {
            // إرسال إيميل للمشرفين
            $adminEmails = config('security.admin_emails', ['admin@hajj-employment.com']);
            
            foreach ($adminEmails as $email) {
                Mail::raw("تنبيه أمني: $message", function ($mail) use ($email, $type) {
                    $mail->to($email)
                         ->subject("تنبيه أمني - $type")
                         ->from(config('mail.from.address'), config('mail.from.name'));
                });
            }
        } catch (\Exception $e) {
            Log::error('فشل في إرسال التنبيه الأمني: ' . $e->getMessage());
        }
    }
} 