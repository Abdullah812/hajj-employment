<!-- السايد بار المتطور والقابل للطي -->
<nav class="sidebar" id="adminSidebar">
    <!-- هيدر السايد بار -->
    <div class="sidebar-header">
        <div class="brand">
            <div class="brand-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <span class="brand-text">نظام التوظيف</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- محتوى السايد بار -->
    <div class="sidebar-content">
        <div class="nav-section">
            <h6 class="nav-title">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحات التحكم</span>
            </h6>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="لوحة التحكم التقليدية">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم التقليدية</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.unified-dashboard') }}" class="nav-link {{ request()->routeIs('admin.unified-dashboard') ? 'active' : '' }}" title="لوحة التحكم الموحدة">
                        <i class="fas fa-th-large"></i>
                        <span>لوحة التحكم الموحدة</span>
                        <span class="badge">جديد</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h6 class="nav-title">
                <i class="fas fa-database"></i>
                <span>إدارة البيانات</span>
            </h6>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" title="الأقسام">
                        <i class="fas fa-building"></i>
                        <span>الأقسام</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" title="الوظائف">
                        <i class="fas fa-briefcase"></i>
                        <span>الوظائف</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" title="الطلبات">
                        <i class="fas fa-file-alt"></i>
                        <span>الطلبات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="المستخدمين">
                        <i class="fas fa-users"></i>
                        <span>المستخدمين</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" title="الموظفين">
                        <i class="fas fa-user-tie"></i>
                        <span>الموظفين</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h6 class="nav-title">
                <i class="fas fa-edit"></i>
                <span>إدارة المحتوى</span>
            </h6>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('admin.content.news.index') }}" class="nav-link {{ request()->routeIs('admin.content.news.*') ? 'active' : '' }}" title="الأخبار">
                        <i class="fas fa-newspaper"></i>
                        <span>الأخبار والمقالات</span>
                        <span class="badge">جديد</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.content.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.content.gallery.*') ? 'active' : '' }}" title="معرض الصور">
                        <i class="fas fa-images"></i>
                        <span>معرض الصور</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="nav-section">
            <h6 class="nav-title">
                <i class="fas fa-chart-line"></i>
                <span>التقارير والإحصائيات</span>
            </h6>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" title="الإحصائيات المتقدمة">
                        <i class="fas fa-chart-bar"></i>
                        <span>الإحصائيات المتقدمة</span>
                        <span class="badge">متقدم</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" title="التقارير">
                        <i class="fas fa-file-chart-line"></i>
                        <span>التقارير</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- فوتر السايد بار -->
    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'المشرف' }}</div>
                <div class="user-role">مشرف النظام</div>
            </div>
        </div>
    </div>
</nav>

<!-- زر التبديل للموبايل -->
<button class="mobile-toggle d-lg-none" id="mobileToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- طبقة الخلفية للموبايل -->
<div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>

<style>
/* المتغيرات */
:root {
    --sidebar-width: 280px;
    --sidebar-collapsed: 70px;
    --primary-color: #b47e13;
    --text-color: #2d3748;
    --border-color: #e2e8f0;
    --bg-light: #f8fafc;
    --transition: all 0.3s ease;
}

/* السايد بار الأساسي */
.sidebar {
    width: var(--sidebar-width);
    height: calc(100vh - 60px);
    background: #fff;
    border-left: 1px solid var(--border-color);
    position: fixed;
    top: 60px;
    right: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: var(--transition);
    box-shadow: -2px 0 10px rgba(0,0,0,0.1);
}

/* هيدر السايد بار */
.sidebar-header {
    padding: 1.5rem 1rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
}

.brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.brand-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.brand-text {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-color);
    transition: var(--transition);
}

.sidebar-toggle {
    width: 35px;
    height: 35px;
    border: none;
    background: var(--bg-light);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-color);
    cursor: pointer;
    transition: var(--transition);
}

.sidebar-toggle:hover {
    background: var(--primary-color);
    color: white;
}

/* محتوى السايد بار */
.sidebar-content {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.75rem;
    padding: 0 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
}

.nav-title i {
    width: 16px;
    text-align: center;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: var(--text-color);
    text-decoration: none;
    border-radius: 10px;
    transition: var(--transition);
    position: relative;
}

.nav-link:hover {
    background: rgba(180, 126, 19, 0.1);
    color: var(--primary-color);
    text-decoration: none;
}

.nav-link.active {
    background: var(--primary-color);
    color: white;
}

.nav-link i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
}

.nav-link span:first-of-type {
    flex: 1;
    white-space: nowrap;
    transition: var(--transition);
}

.badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    background: #28a745;
    color: white;
    border-radius: 8px;
    transition: var(--transition);
}

/* فوتر السايد بار */
.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid var(--border-color);
    background: var(--bg-light);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 10px;
    transition: var(--transition);
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.user-info {
    flex: 1;
    transition: var(--transition);
}

.user-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-color);
    margin-bottom: 0.2rem;
}

.user-role {
    font-size: 0.75rem;
    color: #64748b;
}

/* حالة السايد بار المطوي */
.sidebar.collapsed {
    width: var(--sidebar-collapsed);
}

.sidebar.collapsed .brand-text,
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .badge,
.sidebar.collapsed .nav-title span,
.sidebar.collapsed .user-info {
    opacity: 0;
    width: 0;
    overflow: hidden;
}

.sidebar.collapsed .sidebar-toggle i {
    transform: rotate(180deg);
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.75rem 0.5rem;
}

.sidebar.collapsed .nav-title {
    justify-content: center;
}

.sidebar.collapsed .user-profile {
    justify-content: center;
}

/* Tooltips للوضع المطوي */
.sidebar.collapsed .nav-link {
    position: relative;
}

.sidebar.collapsed .nav-link:hover::after {
    content: attr(title);
    position: absolute;
    left: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    background: var(--text-color);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
    z-index: 9999;
}

/* زر الموبايل */
.mobile-toggle {
    position: fixed;
    top: 70px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 1.2rem;
    z-index: 1001;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* طبقة الخلفية */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: var(--transition);
}

.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* الموبايل */
@media (max-width: 991.98px) {
    .sidebar {
        right: -100%;
        top: 0;
        height: 100vh;
        transition: right 0.3s ease;
    }
    
    .sidebar.mobile-open {
        right: 0;
    }
    
    .sidebar-header {
        padding-top: 2rem;
    }
}

/* Scrollbar */
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

/* تأثيرات */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.nav-item {
    animation: fadeIn 0.3s ease forwards;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    // تحميل حالة السايد بار
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
    
    // تبديل السايد بار في الديسكتوب
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });
    }
    
    // تبديل السايد بار في الموبايل
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // إغلاق السايد بار
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // إغلاق عند تغيير الحجم
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
         });
 });
 </script> 