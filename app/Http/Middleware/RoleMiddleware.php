<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // تحسين الأداء: التحقق من الدور بـ query محسن مع cache
        $userRole = \Cache::remember("user_role_{$user->id}", 300, function () use ($user) {
            return \DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->value('roles.name');
        });
        
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
    }
}
