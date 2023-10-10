<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('assets/admin/images/logo-hcdoc.png') }}" width="120px" alt="logo">
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item {{ active_class(['admin/dashboard']) }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item {{ active_class(['document', 'document/*']) }}">
                <a href="{{ route('document.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="file-text"></i>
                    <span class="link-title">Document</span>
                </a>
            </li>
            
            @can('view master')
                <li class="nav-item nav-category">Master</li>
                <li class="nav-item {{ active_class(['admin/master/jenis', 'admin/master/jenis/*']) }}">
                    <a href="{{ route('admin.jenis.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="user"></i>
                        <span class="link-title">Jenis Document</span>
                    </a>
                </li>
                <li class="nav-item {{ active_class(['admin/master/karyawan', 'admin/master/karyawan/*']) }}">
                    <a href="{{ route('admin.karyawan.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="user"></i>
                        <span class="link-title">Karyawan</span>
                    </a>
                </li>
            @endcan
            
            @can('assign permission')
                <li class="nav-item nav-category">Role & Permission</li>
                <li class="nav-item {{ active_class(['admin/roles-and-permission/roles', 'admin/roles-and-permission/roles/*']) }}">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="settings"></i>
                        <span class="link-title">Roles</span>
                    </a>
                </li>
                <li class="nav-item {{ active_class(['admin/roles-and-permission/permissions', 'admin/roles-and-permission/permissions/*']) }}">
                    <a href="{{ route('admin.permissions.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="settings"></i>
                        <span class="link-title">Permissions</span>
                    </a>
                </li>
                <li class="nav-item {{ active_class(['admin/roles-and-permission/assignable', 'admin/roles-and-permission/assignable/*']) }}">
                    <a href="{{ route('admin.assign.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="settings"></i>
                        <span class="link-title">Assign Permissions</span>
                    </a>
                </li>
                <li class="nav-item {{ active_class(['admin/roles-and-permission/assign', 'admin/roles-and-permission/assign/*']) }}">
                    <a href="{{ route('admin.assign.user.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="settings"></i>
                        <span class="link-title">Permission To User</span>
                    </a>
                </li>
            @endcan
            
            {{-- <li class="nav-item {{ active_class(['admin/master/specialists', 'admin/master/specialists/*']) }}">
                <a href="{{ route('admin.specialists.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">Specialists</span>
                </a>
            </li>
            <li class="nav-item {{ active_class(['admin/master/sub-specialists', 'admin/master/sub-specialists/*']) }}">
                <a href="{{ route('admin.sub-specialists.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">Sub Specialists</span>
                </a>
            </li>
            <li class="nav-item {{ active_class(['admin/master/sites', 'admin/master/sites/*']) }}">
                <a href="{{ route('admin.sites.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Sites</span>
                </a>
            </li>
            <li class="nav-item {{ active_class(['admin/master/doctors', 'admin/master/doctors/*']) }}">
                <a href="{{ route('admin.doctors.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">Doctors</span>
                </a>
            </li> --}}
        </ul>
    </div>
</nav>
