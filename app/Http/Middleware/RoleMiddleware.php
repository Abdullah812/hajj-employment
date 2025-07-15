<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();

        // استخدام Cache لتخزين صلاحيات المستخدم
        $cacheKey = 'user_roles_' . $user->id;
        $userRoles = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($user) {
            return $user->roles()->pluck('name')->toArray();
        });

        // التحقق من الصلاحيات حسب المسار
        if (str_starts_with($routeName, 'admin.') && !in_array('admin', $userRoles)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        if (str_starts_with($routeName, 'department.') && !in_array('department', $userRoles) && !in_array('admin', $userRoles)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        if (str_starts_with($routeName, 'employee.') && !in_array('employee', $userRoles)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // تخزين الأدوار في الجلسة لتسريع الوصول
        if (!session()->has('user_roles')) {
            session(['user_roles' => $userRoles]);
        }

        // التحقق من الأدوار المطلوبة
        foreach ($roles as $role) {
            // السماح للأدمن بالوصول إلى جميع المسارات
            if (in_array('admin', $userRoles)) {
                // تجاوز التحقق من معلومات القسم للأدمن
                return $next($request);
            }
            
            if ($user->hasRole($role)) {
                // التحقق من معلومات القسم إذا كان الدور هو department
                if ($role === 'department') {
                    $department = $user->department;
                    
                    // التحقق من وجود معلومات القسم
                    if (!$department || !$department->name || !$department->description || !$department->manager_id) {
                        return redirect()->route('department.profile')
                            ->with('error', 'يرجى إكمال معلومات القسم الخاص بك أولاً');
                    }
                }
                
                // التحقق من حالة الموافقة للموظفين والأقسام
                if (in_array($role, ['employee', 'department']) && $user->approval_status !== 'approved') {
                    if ($request->is('*/dashboard') || $request->is('*/profile')) {
                        return $next($request); // السماح بالوصول إلى لوحة التحكم والملف الشخصي
                    }
                    return redirect()->route($role . '.dashboard')
                        ->with('error', 'عذراً، يجب أن يتم اعتماد حسابك من قبل المدير قبل الوصول إلى هذه الصفحة');
                }
                
                return $next($request);
            }
        }

        return redirect()->back()
            ->with('error', 'ليس لديك صلاحية الوصول لهذه الصفحة');
    }
}
