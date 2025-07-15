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

        <!-- إضافة رابط للموظفين المقبولين -->
        <li class="nav-item">
            <a href="{{ route('applications.approved') }}" 
               class="nav-link {{ request()->routeIs('applications.approved') ? 'active' : '' }}"
               title="عرض جميع المستخدمين المعتمدين ومعلوماتهم وطلباتهم">
                <i class="nav-icon fas fa-user-check"></i>
                <span>المستخدمين المعتمدين</span>
                @php
                    $approvedCount = \App\Models\User::where('approval_status', 'approved')->count();
                @endphp
                @if($approvedCount > 0)
                    <span class="badge bg-success ms-auto">{{ $approvedCount }}</span>
                @endif
            </a>
        </li>

        <!-- إدارة المستخدمين -->
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>إدارة المستخدمين</p>
            </a>
        </li>

        <!-- طلبات الموافقة -->
        <li class="nav-item">
            <a href="{{ route('admin.users.approvals.index') }}" 
               class="nav-link {{ request()->routeIs('admin.users.approvals.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-user-check"></i>
                <p>
                    طلبات الموافقة
                    @php
                        $pendingCount = \App\Models\User::where('approval_status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge badge-warning right">{{ $pendingCount }}</span>
                    @endif
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('contracts.index') }}" class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-file-contract"></i>
                <span>إدارة العقود</span>
                @php
                    $contractsCount = \App\Models\Contract::count();
                @endphp
                @if($contractsCount > 0)
                    <span class="badge bg-info ms-auto">{{ $contractsCount }}</span>
                @endif
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <span>التقارير</span>
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
</style> 