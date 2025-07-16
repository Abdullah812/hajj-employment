<!-- القائمة الجانبية للمشرف المتطورة -->
<div class="admin-sidebar" id="adminSidebar">
    <!-- هيدر السايد بار مع زر التبديل -->
    <div class="sidebar-header">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-briefcase text-primary"></i>
            </div>
            <span class="logo-text">نظام التوظيف</span>
        </div>
        <button class="sidebar-toggle-btn" id="sidebarToggle" title="فتح/إغلاق القائمة">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- محتوى السايد بار -->
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <!-- لوحات التحكم الرئيسية -->
            <li class="nav-header">
                <i class="fas fa-tachometer-alt me-2"></i>
                <span class="header-text">لوحات التحكم</span>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-title="لوحة التحكم التقليدية">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <span class="nav-text">لوحة التحكم التقليدية</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.unified-dashboard') }}" class="nav-link {{ request()->routeIs('admin.unified-dashboard') ? 'active' : '' }}" data-title="لوحة التحكم الموحدة">
                    <i class="nav-icon fas fa-th-large"></i>
                    <span class="nav-text">لوحة التحكم الموحدة</span>
                    <span class="nav-badge badge bg-success">جديد</span>
                </a>
            </li>

            <!-- إدارة البيانات -->
            <li class="nav-header">
                <i class="fas fa-database me-2"></i>
                <span class="header-text">إدارة البيانات</span>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" data-title="الأقسام">
                    <i class="nav-icon fas fa-building"></i>
                    <span class="nav-text">الأقسام</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" data-title="الوظائف">
                    <i class="nav-icon fas fa-briefcase"></i>
                    <span class="nav-text">الوظائف</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" data-title="الطلبات">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <span class="nav-text">الطلبات</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-title="المستخدمين">
                    <i class="nav-icon fas fa-users"></i>
                    <span class="nav-text">المستخدمين</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" data-title="الموظفين">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <span class="nav-text">الموظفين</span>
                </a>
            </li>

            <!-- إدارة المحتوى -->
            <li class="nav-header">
                <i class="fas fa-edit me-2"></i>
                <span class="header-text">إدارة المحتوى</span>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.content.news.index') }}" class="nav-link {{ request()->routeIs('admin.content.news.*') ? 'active' : '' }}" data-title="الأخبار والمقالات">
                    <i class="nav-icon fas fa-newspaper"></i>
                    <span class="nav-text">الأخبار والمقالات</span>
                    <span class="nav-badge badge bg-primary">جديد</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.content.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.content.gallery.*') ? 'active' : '' }}" data-title="معرض الصور">
                    <i class="nav-icon fas fa-images"></i>
                    <span class="nav-text">معرض الصور</span>
                    <span class="nav-badge badge bg-success">جديد</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.content.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.content.testimonials.*') ? 'active' : '' }}" data-title="شهادات العملاء">
                    <i class="nav-icon fas fa-quote-left"></i>
                    <span class="nav-text">شهادات العملاء</span>
                    <span class="nav-badge badge bg-warning">جديد</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.content.videos.index') }}" class="nav-link {{ request()->routeIs('admin.content.videos.*') ? 'active' : '' }}" data-title="الفيديوهات التعريفية">
                    <i class="nav-icon fas fa-video"></i>
                    <span class="nav-text">الفيديوهات التعريفية</span>
                    <span class="nav-badge badge bg-info">جديد</span>
                </a>
            </li>

            <!-- إدارة الإحصائيات والتقارير -->
            <li class="nav-header">
                <i class="fas fa-chart-line me-2"></i>
                <span class="header-text">الإحصائيات والتحليلات</span>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" data-title="لوحة الإحصائيات المتقدمة">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <span class="nav-text">لوحة الإحصائيات المتقدمة</span>
                    <span class="nav-badge badge bg-gradient bg-primary">متقدم</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-title="التقارير التقليدية">
                    <i class="nav-icon fas fa-file-chart-line"></i>
                    <span class="nav-text">التقارير التقليدية</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- فوتر السايد بار -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-details">
                <span class="user-name">{{ Auth::user()->name ?? 'المشرف' }}</span>
                <span class="user-role">مشرف النظام</span>
            </div>
        </div>
    </div>
</div>

<!-- زر التبديل للأجهزة المحمولة -->
<button class="mobile-sidebar-toggle d-lg-none" id="mobileSidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Overlay للأجهزة المحمولة -->
<div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>

<style>
/* المتغيرات الأساسية */
:root {
    --sidebar-width: 280px;
    --sidebar-collapsed-width: 70px;
    --sidebar-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --primary-gold: #b47e13;
    --text-dark: #2d3748;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
}

/* السايد بار الرئيسي */
.admin-sidebar {
    width: var(--sidebar-width);
    min-height: calc(100vh - 60px);
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-right: 1px solid var(--border-color);
    position: relative;
    transition: var(--sidebar-transition);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    z-index: 1000;
}

/* هيدر السايد بار */
.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-gold), #d4a017);
    border-radius: 10px;
    color: white;
    font-size: 1.2rem;
}

.logo-text {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-dark);
    white-space: nowrap;
    transition: var(--sidebar-transition);
}

.sidebar-toggle-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--bg-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-dark);
    transition: var(--sidebar-transition);
    cursor: pointer;
}

.sidebar-toggle-btn:hover {
    background: var(--primary-gold);
    color: white;
    transform: scale(1.05);
}

/* محتوى السايد بار */
.sidebar-content {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    overflow-x: hidden;
}

/* عناصر التنقل */
.nav {
    gap: 0.25rem;
}

.nav-header {
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 1rem 1rem 0.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    transition: var(--sidebar-transition);
}

.nav-header:first-child {
    margin-top: 0;
    border-top: none;
}

.header-text {
    transition: var(--sidebar-transition);
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    color: var(--text-dark);
    padding: 0.75rem 1rem;
    border-radius: 12px;
    transition: var(--sidebar-transition);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    overflow: hidden;
}

.nav-link:hover {
    background: linear-gradient(135deg, rgba(180, 126, 19, 0.1), rgba(212, 160, 23, 0.1));
    color: var(--primary-gold);
    transform: translateX(-2px);
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.15);
}

.nav-link.active {
    background: linear-gradient(135deg, var(--primary-gold), #d4a017);
    color: white;
    box-shadow: 0 6px 20px rgba(180, 126, 19, 0.3);
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: white;
    border-radius: 0 4px 4px 0;
}

.nav-icon {
    width: 20px;
    text-align: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.nav-text {
    white-space: nowrap;
    transition: var(--sidebar-transition);
    flex: 1;
}

.nav-badge {
    font-size: 0.65rem;
    padding: 0.2rem 0.4rem;
    border-radius: 10px;
    transition: var(--sidebar-transition);
    margin-left: auto;
}

.nav-link:hover .nav-badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

/* فوتر السايد بار */
.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid var(--border-color);
    background: white;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--bg-light);
    border-radius: 12px;
    transition: var(--sidebar-transition);
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-gold), #d4a017);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.user-details {
    display: flex;
    flex-direction: column;
    transition: var(--sidebar-transition);
}

.user-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-dark);
    white-space: nowrap;
}

.user-role {
    font-size: 0.75rem;
    color: #64748b;
    white-space: nowrap;
}

/* حالة السايد بار المطوي */
.admin-sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
}

.admin-sidebar.collapsed .logo-text,
.admin-sidebar.collapsed .nav-text,
.admin-sidebar.collapsed .nav-badge,
.admin-sidebar.collapsed .header-text,
.admin-sidebar.collapsed .user-details {
    opacity: 0;
    width: 0;
    margin: 0;
    padding: 0;
}

.admin-sidebar.collapsed .sidebar-toggle-btn i {
    transform: rotate(180deg);
}

.admin-sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.75rem;
}

.admin-sidebar.collapsed .nav-header {
    justify-content: center;
    padding: 1rem 0.75rem 0.5rem;
}

.admin-sidebar.collapsed .user-info {
    justify-content: center;
}

/* Tooltip للوضع المطوي */
.admin-sidebar.collapsed .nav-link {
    position: relative;
}

.admin-sidebar.collapsed .nav-link::after {
    content: attr(data-title);
    position: absolute;
    left: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    background: var(--text-dark);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 9999;
    pointer-events: none;
}

.admin-sidebar.collapsed .nav-link::before {
    content: '';
    position: absolute;
    left: calc(100% + 5px);
    top: 50%;
    transform: translateY(-50%);
    border: 5px solid transparent;
    border-left-color: var(--text-dark);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 9999;
}

.admin-sidebar.collapsed .nav-link:hover::after,
.admin-sidebar.collapsed .nav-link:hover::before {
    opacity: 1;
    visibility: visible;
}

/* زر التبديل للأجهزة المحمولة */
.mobile-sidebar-toggle {
    position: fixed;
    top: 20px;
    left: 20px;
    width: 50px;
    height: 50px;
    background: var(--primary-gold);
    border: none;
    border-radius: 12px;
    color: white;
    font-size: 1.2rem;
    z-index: 1001;
    transition: var(--sidebar-transition);
    box-shadow: 0 4px 12px rgba(180, 126, 19, 0.3);
}

.mobile-sidebar-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(180, 126, 19, 0.4);
}

/* Overlay للأجهزة المحمولة */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* الاستجابة للأجهزة المحمولة */
@media (max-width: 991.98px) {
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: var(--sidebar-width);
        height: 100vh;
        z-index: 1000;
        transition: left 0.3s ease;
    }
    
    .admin-sidebar.mobile-open {
        left: 0;
    }
    
    .sidebar-header {
        padding-top: 80px;
    }
}

/* تحسينات إضافية */
.nav-link {
    will-change: transform, background-color;
}

.admin-sidebar {
    will-change: width;
}

/* Scrollbar تخصيص */
.sidebar-content::-webkit-scrollbar {
    width: 4px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-content::-webkit-scrollbar-thumb {
    background: rgba(180, 126, 19, 0.3);
    border-radius: 4px;
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
    background: rgba(180, 126, 19, 0.5);
}

/* تأثيرات متقدمة */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.nav-item {
    animation: fadeInUp 0.3s ease forwards;
}

.nav-item:nth-child(1) { animation-delay: 0.1s; }
.nav-item:nth-child(2) { animation-delay: 0.2s; }
.nav-item:nth-child(3) { animation-delay: 0.3s; }
.nav-item:nth-child(4) { animation-delay: 0.4s; }
.nav-item:nth-child(5) { animation-delay: 0.5s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // تحميل حالة السايد بار من localStorage
    const sidebarState = localStorage.getItem('sidebar-collapsed');
    if (sidebarState === 'true') {
        sidebar.classList.add('collapsed');
    }
    
    // تبديل السايد بار في الديسكتوب
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // حفظ الحالة
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
            
            // تحديث عرض المحتوى الرئيسي
            updateMainContentLayout();
        });
    }
    
    // تبديل السايد بار في الأجهزة المحمولة
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        });
    }
    
    // إغلاق السايد بار عند النقر على الـ overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // إغلاق السايد بار عند تغيير حجم الشاشة
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // تحديث تخطيط المحتوى الرئيسي
    function updateMainContentLayout() {
        const mainContent = document.querySelector('.flex-grow-1');
        if (mainContent) {
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = '0';
            }
        }
    }
    
    // تطبيق التحديث الأولي
    updateMainContentLayout();
    
    // إضافة تأثير الـ loading للروابط
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // إضافة تأثير loading
            const icon = this.querySelector('.nav-icon');
            const originalIcon = icon.className;
            icon.className = 'nav-icon fas fa-spinner fa-spin';
            
            // استعادة الأيقونة الأصلية بعد قليل
            setTimeout(() => {
                icon.className = originalIcon;
            }, 1000);
        });
    });
    
    // تحسين الأداء مع throttle للعمليات المكلفة
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }
    
    // مراقبة التمرير للسايد بار
    const sidebarContent = document.querySelector('.sidebar-content');
    let scrollTimeout;
    
    if (sidebarContent) {
        sidebarContent.addEventListener('scroll', throttle(function() {
            // إضافة shadow عند التمرير
            if (this.scrollTop > 10) {
                document.querySelector('.sidebar-header').style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            } else {
                document.querySelector('.sidebar-header').style.boxShadow = 'none';
            }
        }, 16));
    }
});
</script> 