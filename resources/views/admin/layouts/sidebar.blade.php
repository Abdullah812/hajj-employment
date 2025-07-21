<!-- القائمة الجانبية للمشرف -->
<div class="sidebar bg-white shadow-sm">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <span>لوحة التحكم التقليدية</span>
            </a>
        </li>

        <!-- تم حذف لوحة التحكم الموحدة -->

        <!-- تم حذف قسم الأقسام من القائمة -->

        <!-- تم حذف قسم الوظائف من القائمة -->

        <!-- تم حذف قسم الطلبات من القائمة -->

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

        <!-- تم حذف قسم الموظفين من القائمة -->

        <!-- إدارة المحتوى - تم الحذف -->

        <!-- إدارة الإحصائيات والتقارير -->
        <li class="nav-header">
            <i class="fas fa-chart-line me-2"></i>الإحصائيات والتحليلات
        </li>

        <!-- تم حذف قسم الإحصائيات من القائمة -->

        <!-- تم حذف قسم التقارير من القائمة -->
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