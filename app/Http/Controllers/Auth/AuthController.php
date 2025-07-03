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
        return view('auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // معالجة خطأ CSRF
        if (!$request->hasValidSignature() && !$request->hasSession()) {
            return redirect()->route('login')->with('error', 'انتهت صلاحية الجلسة. حاول مرة أخرى.');
        }

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

                // توجيه المستخدم حسب دوره
                $user = Auth::user();
                if ($user->hasRole('admin')) {
                    return redirect()->intended('/admin/dashboard');
                } elseif ($user->hasRole('company')) {
                    return redirect()->intended('/company/dashboard');
                } elseif ($user->hasRole('employee')) {
                    return redirect()->intended('/employee/dashboard');
                }

                return redirect()->intended('/dashboard');
            }
        } catch (\Exception $e) {
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

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // تعيين الدور
        $user->assignRole($request->role);

        // تسجيل دخول المستخدم تلقائياً
        Auth::login($user);

        // توجيه المستخدم حسب دوره
        if ($request->role === 'company') {
            return redirect('/company/dashboard')->with('success', 'تم إنشاء الحساب بنجاح!');
        } elseif ($request->role === 'employee') {
            return redirect('/employee/dashboard')->with('success', 'تم إنشاء الحساب بنجاح!');
        }

        return redirect('/dashboard')->with('success', 'تم إنشاء الحساب بنجاح!');
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
