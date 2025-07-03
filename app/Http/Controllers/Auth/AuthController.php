<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        // إذا كان المستخدم مسجل دخول بالفعل، وجهه إلى لوحة التحكم المناسبة
        if (Auth::check()) {
            $user = Auth::user();
            
            // تحسين الأداء: استعلام واحد للحصول على الدور
            $userRole = \DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->value('roles.name');
            
            if ($userRole === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($userRole === 'company') {
                return redirect('/company/dashboard');
            } elseif ($userRole === 'employee') {
                return redirect('/employee/dashboard');
            }
            return redirect('/dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        try {
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();

                // توجيه المستخدم حسب دوره - محسن للأداء
                $user = Auth::user();
                
                // تحسين الأداء: استعلام واحد للحصول على الدور
                $userRole = \DB::table('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('model_has_roles.model_id', $user->id)
                    ->where('model_has_roles.model_type', 'App\\Models\\User')
                    ->value('roles.name');
                
                if ($userRole === 'admin') {
                    return redirect('/admin/dashboard')->with('success', 'مرحباً بك في لوحة الإدارة');
                } elseif ($userRole === 'company') {
                    return redirect('/company/dashboard')->with('success', 'مرحباً بك في لوحة تحكم الشركة');
                } elseif ($userRole === 'employee') {
                    return redirect('/employee/dashboard')->with('success', 'مرحباً بك في لوحة تحكم الموظف');
                }

                return redirect('/dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
            }
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تسجيل الدخول. حاول مرة أخرى.')
                ->withInput();
        }

        return redirect()->back()
            ->with('error', 'بيانات الدخول غير صحيحة')
            ->withInput();
    }

    /**
     * عرض صفحة التسجيل
     */
    public function showRegisterForm()
    {
        // إذا كان المستخدم مسجل دخول بالفعل، وجهه إلى لوحة التحكم المناسبة
        if (Auth::check()) {
            $user = Auth::user();
            
            // تحسين الأداء: استعلام واحد للحصول على الدور
            $userRole = \DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->value('roles.name');
            
            if ($userRole === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($userRole === 'company') {
                return redirect('/company/dashboard');
            } elseif ($userRole === 'employee') {
                return redirect('/employee/dashboard');
            }
            return redirect('/dashboard');
        }
        
        return view('auth.register');
    }

    /**
     * معالجة التسجيل
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:employee,company',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'role.required' => 'نوع الحساب مطلوب',
            'role.in' => 'نوع الحساب غير صحيح',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // استخدام Database Transaction للأداء والأمان
        \DB::beginTransaction();
        
        try {
            // إنشاء المستخدم
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // تعيين الدور مع تحسين الأداء
            \DB::table('model_has_roles')->insert([
                'role_id' => \DB::table('roles')->where('name', $request->role)->value('id'),
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);

            \DB::commit();
            
            // تنظيف cache الأدوار
            \Cache::forget('spatie.permission.cache');
            
            // تسجيل دخول المستخدم تلقائياً
            Auth::login($user);
            
            // إعادة إنشاء الجلسة للأمان
            $request->session()->regenerate();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الحساب. حاول مرة أخرى.');
        }

        // توجيه المستخدم حسب دوره
        if ($request->role === 'company') {
            return redirect('/company/dashboard')->with('success', "مرحباً {$user->name}! تم إنشاء حساب الشركة بنجاح");
        } elseif ($request->role === 'employee') {
            return redirect('/employee/dashboard')->with('success', "مرحباً {$user->name}! تم إنشاء حساب الموظف بنجاح");
        }

        return redirect('/dashboard')->with('success', "مرحباً {$user->name}! تم إنشاء الحساب بنجاح");
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        // التأكد من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect('/')->with('info', 'أنت غير مسجل دخول');
        }

        $userName = Auth::user()->name;
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', "تم تسجيل خروج {$userName} بنجاح");
    }
}
