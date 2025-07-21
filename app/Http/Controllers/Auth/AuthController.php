<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
            if ($user->hasRole('admin')) {
                return redirect('/admin/dashboard');
            } elseif ($user->hasRole('company')) {
                return redirect('/company/dashboard');
            } elseif ($user->hasRole('employee')) {
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

                // توجيه المستخدم حسب دوره - استخدام redirect عادي بدلاً من intended
                $user = Auth::user();
                
                if ($user->hasRole('admin')) {
                    return redirect('/admin/dashboard')->with('success', 'مرحباً بك في لوحة الإدارة');
                } elseif ($user->hasRole('company')) {
                    return redirect('/company/dashboard')->with('success', 'مرحباً بك في لوحة تحكم الشركة');
                } elseif ($user->hasRole('employee')) {
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
            if ($user->hasRole('admin')) {
                return redirect('/admin/dashboard');
            } elseif ($user->hasRole('company')) {
                return redirect('/company/dashboard');
            } elseif ($user->hasRole('employee')) {
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
        try {
            $validatedData = $request->validate([
                // معلومات الحساب
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role' => ['required', 'string', 'in:employee,department'],
                
                // المعلومات الشخصية
                'national_id' => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/'],
                'phone' => ['required', 'string', 'size:10', 'regex:/^05[0-9]{8}$/'],
                'address' => ['required', 'string', 'max:500'],
                'date_of_birth' => ['required', 'date', 'before:today'],
                
                // المؤهلات والخبرات
                'qualification' => ['required', 'string', 'in:ثانوي,دبلوم,بكالوريوس,ماجستير,دكتوراه'],
                'academic_experience' => ['nullable', 'string', 'max:1000'],
                
                // المعلومات البنكية
                'iban_number' => ['required', 'string', 'size:24', 'regex:/^SA[0-9]{22}$/'],
                
                // المرفقات - قاعدة البيانات فقط
                'cv_path' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'iban_attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'national_id_attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'national_address_attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'experience_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ]);

            Log::info('بدء عملية التسجيل', ['email' => $request->email]);

            DB::beginTransaction();

            // إنشاء المستخدم
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'approval_status' => 'pending', // تعيين حالة الموافقة كمعلق
            ]);

            Log::info('تم إنشاء المستخدم', ['user_id' => $user->id]);

            // تعيين الدور
            $user->assignRole($validatedData['role']);

            Log::info('تم تعيين الدور', ['role' => $validatedData['role']]);

            // معالجة المرفقات - حفظ في قاعدة البيانات
            $fileMapping = [
                'cv_path' => 'cv',
                'iban_attachment' => 'iban',
                'national_id_attachment' => 'national_id',
                'national_address_attachment' => 'national_address',
                'experience_certificate' => 'experience'
            ];

            // إنشاء الملف الشخصي أولاً
            $profileData = [
                'user_id' => $user->id,
                'national_id' => $validatedData['national_id'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'qualification' => $validatedData['qualification'] ?? null,
                'iban_number' => $validatedData['iban_number'] ?? null,
                'academic_experience' => $validatedData['academic_experience'] ?? null,
            ];

            $profile = UserProfile::create($profileData);
            Log::info('تم إنشاء الملف الشخصي', ['profile_id' => $profile->id]);

            // حفظ الملفات في قاعدة البيانات
            foreach ($fileMapping as $inputField => $dbField) {
                if ($request->hasFile($inputField)) {
                    try {
                        $file = $request->file($inputField);
                        if ($profile->saveFileToDatabase($file, $dbField)) {
                            Log::info("تم حفظ الملف في قاعدة البيانات", [
                                'field' => $inputField,
                                'db_field' => $dbField,
                                'file_name' => $file->getClientOriginalName()
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error("خطأ في حفظ الملف", [
                            'field' => $inputField,
                            'error' => $e->getMessage()
                        ]);
                        // لا نرمي الخطأ هنا لعدم إيقاف عملية التسجيل
                    }
                }
            }

            DB::commit();
            Log::info('تم إكمال عملية التسجيل بنجاح');

            // تسجيل الدخول تلقائياً
            Auth::login($user);

            // التوجيه حسب الدور
            if ($user->hasRole('employee')) {
                return redirect('/employee/dashboard')->with('success', 'تم التسجيل بنجاح! سيتم مراجعة حسابك من قبل الإدارة قبل أن تتمكن من التقديم على الوظائف.');
            // } elseif ($user->hasRole('department')) { - تم حذف نظام الأقسام
                // return redirect('/department/dashboard')->with('success', 'تم التسجيل بنجاح! سيتم مراجعة حسابك من قبل الإدارة قبل أن تتمكن من إضافة الوظائف.'); - تم حذف النظام
            }

            return redirect('/dashboard')->with('success', 'تم التسجيل بنجاح! سيتم مراجعة حسابك من قبل الإدارة.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('خطأ في التحقق من البيانات', [
                'errors' => $e->errors()
            ]);
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في عملية التسجيل', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // لا حاجة لحذف ملفات - النظام يعتمد على قاعدة البيانات فقط
            // إذا فشل التسجيل، سيتم حذف المستخدم والملف الشخصي تلقائياً بـ rollback

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء التسجيل. الرجاء المحاولة مرة أخرى.');
        }
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
