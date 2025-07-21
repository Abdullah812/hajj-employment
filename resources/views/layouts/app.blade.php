<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'شركة مناسك المشاعر - التوظيف الموسمي')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <meta name="theme-color" content="#b47e13">
    
    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #b47e13;
            --primary-orange: #be7b06;
            --dark-brown: #40260d;
            --dark-olive: #2a2a00;
            --almost-black: #111111;
            --text-dark: #111111;
            --text-muted: #666666;
            --light-gray: #f8f9fa;
        }
        
        html, body {
            height: 100%;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: 700;
        }
        
        /* Primary Colors */
        .text-primary {
            color: var(--primary-gold) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-gold) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            transition: all 0.2s ease;
        }
        
        .btn-outline-primary {
            color: var(--primary-gold);
            border-color: var(--primary-gold);
            transition: all 0.2s ease;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
            color: white;
        }
        
        /* Success Colors */
        .text-success {
            color: var(--primary-orange) !important;
        }
        
        .bg-success {
            background-color: var(--primary-orange) !important;
        }
        
        .btn-success {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
        }
        
        .btn-outline-success {
            color: var(--primary-orange);
            border-color: var(--primary-orange);
        }
        
        .btn-outline-success:hover {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
        }
        
        /* Warning Colors */
        .text-warning {
            color: var(--dark-brown) !important;
        }
        
        .bg-warning {
            background-color: var(--dark-brown) !important;
            color: white !important;
        }
        
        .btn-warning {
            background-color: var(--dark-brown);
            border-color: var(--dark-brown);
            color: white;
        }
        
        .btn-outline-warning {
            color: var(--dark-brown);
            border-color: var(--dark-brown);
        }
        
        .btn-outline-warning:hover {
            background-color: var(--dark-brown);
            border-color: var(--dark-brown);
            color: white;
        }
        
        /* Info Colors */
        .text-info {
            color: var(--dark-olive) !important;
        }
        
        .bg-info {
            background-color: var(--dark-olive) !important;
            color: white !important;
        }
        
        .btn-info {
            background-color: var(--dark-olive);
            border-color: var(--dark-olive);
            color: white;
        }
        
        .btn-outline-info {
            color: var(--dark-olive);
            border-color: var(--dark-olive);
        }
        
        .btn-outline-info:hover {
            background-color: var(--dark-olive);
            border-color: var(--dark-olive);
            color: white;
        }
        
        /* Custom borders */
        .border-primary {
            border-color: var(--primary-gold) !important;
        }
        
        .border-success {
            border-color: var(--primary-orange) !important;
        }
        
        /* Text colors */
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Background opacity variations */
        .bg-primary.bg-opacity-10 {
            background-color: rgba(180, 126, 19, 0.1) !important;
        }
        
        .bg-success.bg-opacity-10 {
            background-color: rgba(190, 123, 6, 0.1) !important;
        }
        
        .bg-warning.bg-opacity-10 {
            background-color: rgba(64, 38, 13, 0.1) !important;
        }
        
        .bg-info.bg-opacity-10 {
            background-color: rgba(42, 42, 0, 0.1) !important;
        }
        
        /* Card enhancements - تحسين مخفف للتأثيرات */
        .card {
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
            border: 1px solid #e9ecef;
        }
        
        .card:hover:not(.modal .card) {
            box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15) !important;
            border-color: rgba(180, 126, 19, 0.3);
        }
        
        /* Modal fixes */
        .modal {
            --bs-modal-zindex: 1055;
        }
        
        .modal-backdrop {
            --bs-backdrop-zindex: 1050;
            --bs-backdrop-bg: #000;
            --bs-backdrop-opacity: 0.5;
        }
        
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }
        
        .modal.show .modal-dialog {
            transform: none;
        }
        
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }
        
        .modal-lg {
            max-width: 800px !important;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-radius: 15px 15px 0 0;
            background-color: #f8f9fa;
        }
        
        .modal-footer {
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 15px 15px;
            background-color: #f8f9fa;
        }
        
        /* Navbar styling */
        .navbar {
            border-bottom: 3px solid var(--primary-gold);
        }
        
        /* تنسيق اللوقو */
        .navbar-brand img {
            height: 70px;
            width: auto;
            margin-left: 15px;
        }
        
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 50px;
            }
        }
        
        /* Main content area */
        main {
            flex: 1;
        }
        
        /* Auth pages styling */
        .min-vh-100 {
            min-height: calc(100vh - 120px) !important;
        }
        
        .card {
            border-radius: 15px;
            border: none;
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem 1rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.2rem rgba(180, 126, 19, 0.25);
        }
        
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        
        .form-select:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.2rem rgba(180, 126, 19, 0.25);
        }
        
        .btn {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        /* Footer styling */
        footer.bg-dark {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
        }
        
        /* Social Media Links */
        .social-links .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }
        
        .social-links .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .social-links .social-link[title="تويتر"]:hover {
            background: #1da1f2;
            color: white;
        }
        
        .social-links .social-link[title="فيسبوك"]:hover {
            background: #4267b2;
            color: white;
        }
        
        .social-links .social-link[title="إنستغرام"]:hover {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            color: white;
        }
        
        .social-links .social-link[title="لينكد إن"]:hover {
            background: #0077b5;
            color: white;
        }
        
        .social-links .social-link[title="يوتيوب"]:hover {
            background: #ff0000;
            color: white;
        }
        
        .social-links .social-link[title="واتساب"]:hover {
            background: #25d366;
            color: white;
        }
        
        footer h6 {
            color: #ffd700;
            font-weight: 600;
        }
        
        footer ul li {
            margin-bottom: 0.5rem;
        }
        
        footer ul li a:hover {
            color: #ffd700 !important;
        }
        
        footer ul li i {
            color: #ffd700;
        }

        /* Loading animation */
        .loading-spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--primary-gold);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Performance optimizations */
        * {
            box-sizing: border-box;
        }
        
        /* Prevent modal flickering */
        .modal-backdrop.show {
            opacity: 0.5;
        }
        
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
        }
        
        .modal.show .modal-dialog {
            transform: none;
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Prevent layout shift */
        .modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }
        
        /* Enhanced modal appearance */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: var(--primary-gold);
            border-radius: 10px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--primary-orange);
        }
        
        /* Additional modal states */
        body.modal-opening,
        body.modal-closing {
            overflow: hidden !important;
        }
        
        body.modal-opening .card,
        body.modal-closing .card {
            transition: none !important;
            transform: none !important;
        }
        
        /* Prevent any interference with modal animation */
        .modal.fade .modal-dialog {
            transition: transform 0.25s ease-out, opacity 0.25s ease-out;
            transform: translateY(-25px);
            opacity: 0;
        }
        
        .modal.show .modal-dialog {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* Ensure smooth backdrop */
        .modal-backdrop {
            transition: opacity 0.25s ease-out;
        }
        
        .modal-backdrop.fade {
            opacity: 0;
        }
        
        .modal-backdrop.show {
            opacity: 0.5;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="شركة مناسك المشاعر" class="img-fluid">
                <span class="text-primary">مناسك المشاعر</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('jobs.index') }}">الوظائف</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        @if(auth()->user()->hasRole('employee'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('employee.dashboard') }}">لوحة التحكم</a>
                            </li>
                                            {{-- تم حذف navigation للأقسام --}}
                        @elseif(auth()->user()->hasRole('admin'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
                            </li>
                        @endif
                        
                        <!-- أيقونة الإشعارات -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationDropdown">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>الإشعارات</span>
                                    <button type="button" class="btn btn-sm btn-link p-0" onclick="markAllAsRead()">
                                        <small>تمييز الكل كمقروء</small>
                                    </button>
                                </div>
                                <div id="notificationsList">
                                    <div class="dropdown-item-text text-center text-muted py-3">
                                        <i class="fas fa-bell-slash me-2"></i>
                                        لا توجد إشعارات
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                    <small>عرض جميع الإشعارات</small>
                                </a>
                            </div>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @if(auth()->user()->hasRole('employee'))
                                    <li><a class="dropdown-item" href="{{ route('employee.profile') }}">الملف الشخصي</a></li>
                                    <!-- <li>عقودي - تم حذف نظام العقود</li> -->
                                @elseif(auth()->user()->hasRole('department'))
                                    <!-- <li>العقود - تم حذف نظام العقود</li> -->
                                @elseif(auth()->user()->hasRole('admin'))
                                    <!-- <li>إدارة العقود - تم حذف نظام العقود</li> -->
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">تسجيل الخروج</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">إنشاء حساب</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>شركة مناسك المشاعر</h5>
                    <p class="mb-3">شركة سعودية مرخصة من وزارة الحج والعمرة منذ 1984م</p>
                    
                    <!-- Social Media Links -->
                    <div class="social-links">
                        <h6 class="mb-3">تابعونا على:</h6>
                        <div class="d-flex gap-3">
                            <a href="https://twitter.com/manasek_almashair" target="_blank" class="social-link" title="تويتر">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://facebook.com/manasekalmashair" target="_blank" class="social-link" title="فيسبوك">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://instagram.com/manasek_almashair" target="_blank" class="social-link" title="إنستغرام">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://linkedin.com/company/manasek-almashair" target="_blank" class="social-link" title="لينكد إن">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://youtube.com/@manasekalmashair" target="_blank" class="social-link" title="يوتيوب">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="https://wa.me/966501234567" target="_blank" class="social-link" title="واتساب">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h6>روابط مفيدة</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-light text-decoration-none">الرئيسية</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="text-light text-decoration-none">الوظائف</a></li>
                        <li><a href="{{ route('news.index') }}" class="text-light text-decoration-none">الأخبار</a></li>
                        <li><a href="#" class="text-light text-decoration-none">خدماتنا</a></li>
                        <li><a href="#" class="text-light text-decoration-none">اتصل بنا</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h6>معلومات التواصل</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i>مكة المكرمة، المملكة العربية السعودية</li>
                        <li><i class="fas fa-phone me-2"></i>+966 12 123 4567</li>
                        <li><i class="fas fa-envelope me-2"></i>info@manasekalmashair.com</li>
                        <li><i class="fas fa-globe me-2"></i>www.manasekalmashair.com</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} شركة مناسك المشاعر - جميع الحقوق محفوظة</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">مرخصة من وزارة الحج والعمرة برقم (HAJ-123456)</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modal Enhancement Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تحسين أداء النوافذ المنبثقة
            const modals = document.querySelectorAll('.modal');
            
            modals.forEach(function(modal) {
                // إصلاح مشكلة الحركة المتقطعة
                modal.addEventListener('show.bs.modal', function() {
                    document.body.style.paddingRight = '0px';
                    document.body.style.overflow = 'hidden';
                });
                
                modal.addEventListener('hidden.bs.modal', function() {
                    document.body.style.paddingRight = '';
                    document.body.style.overflow = '';
                });
                
                // تحسين الأداء
                modal.addEventListener('shown.bs.modal', function() {
                    const firstInput = modal.querySelector('input, textarea, select');
                    if (firstInput) {
                        firstInput.focus();
                    }
                });
            });
            
            // منع تداخل النوافذ
            document.addEventListener('click', function(e) {
                if (e.target.matches('[data-bs-dismiss="modal"]')) {
                    const modal = e.target.closest('.modal');
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                }
            });
            
            // إصلاح مشكلة الـ scrolling
            let scrollY = 0;
            document.addEventListener('show.bs.modal', function() {
                scrollY = window.scrollY;
                document.body.style.position = 'fixed';
                document.body.style.top = `-${scrollY}px`;
                document.body.style.width = '100%';
            });
            
            document.addEventListener('hidden.bs.modal', function() {
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.width = '';
                window.scrollTo(0, scrollY);
            });
        });
    </script>
    
    <!-- Notifications Script -->
    @auth
    <script>
        // نظام الإشعارات
        let notificationUpdateInterval;
        
        document.addEventListener('DOMContentLoaded', function() {
            // تحديث الإشعارات عند تحميل الصفحة
            updateNotifications();
            
            // تحديث الإشعارات كل 30 ثانية
            notificationUpdateInterval = setInterval(updateNotifications, 30000);
            
            // تحديث الإشعارات عند النقر على أيقونة الجرس
            document.getElementById('notificationDropdown').addEventListener('click', function() {
                updateNotifications();
            });
        });
        
        // تحديث الإشعارات
        function updateNotifications() {
            fetch('{{ route("notifications.recent") }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                    updateNotificationsList(data.notifications);
                })
                .catch(error => {
                    console.error('خطأ في تحديث الإشعارات:', error);
                });
        }
        
        // تحديث شارة العداد
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
        
        // تحديث قائمة الإشعارات
        function updateNotificationsList(notifications) {
            const list = document.getElementById('notificationsList');
            
            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="dropdown-item-text text-center text-muted py-3">
                        <i class="fas fa-bell-slash me-2"></i>
                        لا توجد إشعارات
                    </div>
                `;
                return;
            }
            
            list.innerHTML = notifications.map(notification => `
                <div class="dropdown-item notification-item ${notification.is_read ? 'read' : 'unread'}" 
                     onclick="markAsReadAndRedirect(${notification.id}, '${notification.action_url || '#'}')">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <i class="${notification.icon_class} ${notification.color_class}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 ${notification.is_read ? 'text-muted' : ''}">${notification.title}</h6>
                            <p class="mb-1 small ${notification.is_read ? 'text-muted' : ''}">${notification.message}</p>
                            <small class="text-muted">${notification.time_ago}</small>
                        </div>
                        ${!notification.is_read ? '<div class="notification-dot bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>' : ''}
                    </div>
                </div>
            `).join('');
        }
        
        // تمييز الإشعار كمقروء والانتقال
        function markAsReadAndRedirect(notificationId, actionUrl) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث الإشعارات
                    updateNotifications();
                    
                    // الانتقال إلى الرابط إذا كان متوفراً
                    if (actionUrl && actionUrl !== '#') {
                        window.location.href = actionUrl;
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في تمييز الإشعار:', error);
            });
        }
        
        // تمييز جميع الإشعارات كمقروءة
        function markAllAsRead() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث الإشعارات
                    updateNotifications();
                    
                    // إظهار رسالة نجاح
                    showNotificationMessage(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('خطأ في تمييز الإشعارات:', error);
                showNotificationMessage('حدث خطأ أثناء تمييز الإشعارات', 'error');
            });
        }
        
        // إظهار رسالة توضيحية
        function showNotificationMessage(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', alertHTML);
            
            // إزالة التنبيه تلقائياً بعد 3 ثوان
            setTimeout(() => {
                const alert = document.querySelector('.alert.position-fixed');
                if (alert) {
                    alert.remove();
                }
            }, 3000);
        }
        
        // تنظيف Interval عند مغادرة الصفحة
        window.addEventListener('beforeunload', function() {
            if (notificationUpdateInterval) {
                clearInterval(notificationUpdateInterval);
            }
        });
    </script>
    
    <!-- CSS للإشعارات -->
    <style>
        .notification-dropdown {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 15px;
        }
        
        .notification-item {
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: rgba(180, 126, 19, 0.05);
        }
        
        .notification-item.unread:hover {
            background-color: rgba(180, 126, 19, 0.1);
        }
        
        .notification-dot {
            margin-top: 0.5rem;
        }
        
        #notificationBadge {
            font-size: 0.7rem;
            min-width: 18px;
            height: 18px;
            line-height: 18px;
        }
        
        .nav-link:hover .fa-bell {
            color: var(--primary-gold) !important;
            transition: color 0.2s ease;
        }
    </style>
    @endauth
    
    @stack('scripts')
    
    <!-- CSRF and Session Management Script -->
    <script>
        // إعداد CSRF Token للطلبات AJAX
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        
        // إعداد axios للاستخدام مع CSRF
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
        }
        
        // إعداد jQuery للاستخدام مع CSRF
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                }
            });
        }

        // معالجة انتهاء Session وخطأ CSRF
        document.addEventListener('DOMContentLoaded', function() {
            // تحديث CSRF Token كل 10 دقائق
            setInterval(function() {
                fetch('/csrf-token', {
                    method: 'GET',
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.token) {
                        window.Laravel.csrfToken = data.token;
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                        
                        // تحديث جميع input fields hidden للـ CSRF
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.token;
                        });
                    }
                })
                .catch(error => {
                    console.log('خطأ في تحديث CSRF Token:', error);
                });
            }, 600000); // كل 10 دقائق

            // معالجة الأخطاء 419 في النماذج
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // التحقق من وجود CSRF token
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (!csrfInput || !csrfInput.value) {
                        e.preventDefault();
                        alert('انتهت صلاحية الجلسة. سيتم إعادة تحميل الصفحة.');
                        window.location.reload();
                        return false;
                    }
                });
            });

            // معالجة الطلبات AJAX مع خطأ 419
            if (typeof $ !== 'undefined') {
                $(document).ajaxError(function(event, xhr, settings) {
                    if (xhr.status === 419) {
                        alert('انتهت صلاحية الجلسة. سيتم إعادة تحميل الصفحة.');
                        window.location.reload();
                    }
                });
            }

            // معالجة عامة للطلبات fetch
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        if (response.status === 419) {
                            alert('انتهت صلاحية الجلسة. سيتم إعادة تحميل الصفحة.');
                            window.location.reload();
                        }
                        return response;
                    });
            };
        });
    </script>
</body>
</html> 