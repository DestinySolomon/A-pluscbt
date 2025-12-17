<nav class="admin-topnav">
    <div class="topnav-left">
        <button class="menu-toggle">
            <i class="ri-menu-line"></i>
        </button>
        <h5 class="mb-0 d-none d-md-inline">@yield('page-title', 'Dashboard')</h5>
    </div>
    
    <div class="topnav-right">
        <div class="dropdown">
            <div class="admin-user" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="user-info d-none d-md-block">
                    <div class="fw-medium">{{ Auth::user()->name }}</div>
                    <small>Administrator</small>
                </div>
                <i class="ri-arrow-down-s-line d-none d-md-block"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="ri-user-line me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="ri-settings-3-line me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();">
                        <i class="ri-logout-box-r-line me-2"></i>Logout
                    </a>
                    <form id="logout-form-top" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>