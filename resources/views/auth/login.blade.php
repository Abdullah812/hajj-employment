@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>تسجيل الدخول</h4>
                    <p>مرحباً بك في شركة مناسك المشاعر</p>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                تذكرني
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <span class="btn-text">تسجيل الدخول</span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    جاري تسجيل الدخول...
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-primary">إنشاء حساب جديد</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = loginBtn.querySelector('.btn-text');
    const btnLoading = loginBtn.querySelector('.btn-loading');
    
    let isSubmitting = false;
    
    loginForm.addEventListener('submit', function(e) {
        // منع التسليم المتكرر
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        // تحقق من صحة البيانات
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        
        if (!email || !password) {
            e.preventDefault();
            alert('يرجى إدخال البريد الإلكتروني وكلمة المرور');
            return false;
        }
        
        // تحقق من صحة البريد الإلكتروني
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('يرجى إدخال بريد إلكتروني صحيح');
            return false;
        }
        
        // عرض تحميل
        isSubmitting = true;
        loginBtn.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        
        // إعادة تفعيل الزر بعد مهلة زمنية (في حالة فشل التسليم)
        setTimeout(function() {
            if (isSubmitting) {
                isSubmitting = false;
                loginBtn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        }, 30000); // 30 ثانية
    });
    
    // معالجة إعادة التوجيه في حالة وجود session مسبق
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
        // المستخدم عاد من المتصفح، أعد تحميل الصفحة
        window.location.reload();
    }
});
</script>
@endpush 