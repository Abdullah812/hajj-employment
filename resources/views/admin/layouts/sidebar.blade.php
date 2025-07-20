<!-- القائمة الجانبية للمشرف -->
<div class="sidebar bg-white shadow-sm">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <span>لوحة التحكم التقليدية</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.unified-dashboard') }}" class="nav-link {{ request()->routeIs('admin.unified-dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-th-large"></i>
                <span>لوحة التحكم الموحدة</span>
                <span class="badge bg-success ms-auto">جديد</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-building"></i>
                <span>الأقسام</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.jobs.index') }}" class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-briefcase"></i>
                <span>الوظائف</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-alt"></i>
                <span>الطلبات</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <span>المستخدمين</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.users.approvals.index') }}" class="nav-link {{ request()->routeIs('admin.users.approvals.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-check"></i>
                <span>طلبات الموافقة</span>
                @php
                    $pendingCount = \App\Models\User::where('approval_status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-warning ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-tie"></i>
                <span>الموظفين</span>
            </a>
        </li>

        <!-- إدارة المحتوى -->
        <li class="nav-header">
            <i class="fas fa-edit me-2"></i>إدارة المحتوى
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.content.news.index') }}" class="nav-link {{ request()->routeIs('admin.content.news.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-newspaper"></i>
                <span>الأخبار والمقالات</span>
                <span class="badge bg-primary ms-auto">جديد</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.content.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.content.gallery.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-images"></i>
                <span>معرض الصور</span>
                <span class="badge bg-success ms-auto">جديد</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.content.testimonials.index') }}" class="nav-link {{ request()->routeIs('admin.content.testimonials.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-quote-left"></i>
                <span>شهادات العملاء</span>
                <span class="badge bg-warning ms-auto">جديد</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.content.videos.index') }}" class="nav-link {{ request()->routeIs('admin.content.videos.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-video"></i>
                <span>الفيديوهات التعريفية</span>
                <span class="badge bg-info ms-auto">جديد</span>
            </a>
        </li>

        <!-- إدارة الإحصائيات والتقارير -->
        <li class="nav-header">
            <i class="fas fa-chart-line me-2"></i>الإحصائيات والتحليلات
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <span>لوحة الإحصائيات المتقدمة</span>
                <span class="badge bg-gradient bg-primary ms-auto">متقدم</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-chart-line"></i>
                <span>التقارير التقليدية</span>
            </a>
        </li>
    </ul>
</div>

<style>
.sidebar {
    width: 250px;
    min-height: calc(100vh - 60px);
    padding: 1rem;
}

.sidebar .nav-link {
    color: var(--text-dark);
    padding: 0.75rem 1rem;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.sidebar .nav-link:hover {
    background-color: rgba(180, 126, 19, 0.05);
    color: var(--primary-gold);
}

.sidebar .nav-link.active {
    background-color: var(--primary-gold);
    color: white;
}

.sidebar .nav-icon {
    margin-right: 0.5rem;
    width: 20px;
    text-align: center;
}

.sidebar .nav-link .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.sidebar .nav-link:hover .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
}

.sidebar .nav-header {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 1rem 1rem 0.5rem;
    margin-top: 1rem;
    border-top: 1px solid #eee;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sidebar .nav-header:first-child {
    margin-top: 0;
    border-top: none;
}
</style> 