<nav class="user-topnav">
    <div class="topnav-left">
        <button class="menu-toggle" id="sidebarToggle">
            <i class="ri-menu-line"></i>
        </button>
        <div class="page-title">
            {{-- <h1>@yield('page-title', 'Dashboard')</h1> --}}
            {{-- Removed the subtitle line --}}
        </div>
    </div>

    <div class="topbar-right">
        <!-- Exam Timer (Hidden by default) -->
        <div class="jamb-timer-container d-none" id="examTimerContainer">
            <div class="jamb-timer">
                <i class="ri-time-line me-2"></i>
                <span class="timer-hours">00</span>:<span class="timer-minutes">00</span>:<span class="timer-seconds">00</span>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="user-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <!-- Mobile: show only avatar/icon -->
                <div class="user-avatar d-md-none">
                    @auth
                        @if(Auth::user()->profile_image_url)
                            <img src="{{ Auth::user()->profile_image_url }}" 
                                 alt="{{ Auth::user()->name }}"
                                 class="user-avatar-img"
                                 onerror="this.style.display='none'; this.parentNode.innerHTML='{{ Auth::user()->initials }}';">
                        @else
                            {{ Auth::user()->initials }}
                        @endif
                    @endauth
                </div>

                <!-- Desktop: show avatar + name -->
                <div class="d-none d-md-flex align-items-center gap-3">
                    <div class="user-avatar">
                        @auth
                            @if(Auth::user()->profile_image_url)
                                <img src="{{ Auth::user()->profile_image_url }}" 
                                     alt="{{ Auth::user()->name }}"
                                     class="user-avatar-img"
                                     onerror="this.style.display='none'; this.parentNode.innerHTML='{{ Auth::user()->initials }}';">
                            @else
                                {{ Auth::user()->initials }}
                            @endif
                        @endauth
                    </div>
                    <div class="user-info">
                        <h6>{{ Auth::user()->name }}</h6>
                        <small>Student</small>
                    </div>
                    <i class="ri-arrow-down-s-line dropdown-arrow"></i>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="ri-user-line me-2"></i> My Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ri-logout-box-r-line me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Mobile Sidebar Fix -->
<style>
@media (max-width: 992px) {
    .user-sidebar {
        transform: translateX(-100%);
        width: 280px;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 9999;
        transition: transform 0.3s ease;
    }

    .user-sidebar.active {
        transform: translateX(0) !important;
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.3);
    }

    .user-dashboard-main {
        margin-left: 0 !important;
    }

    .menu-toggle {
        display: block !important;
        position: relative;
        z-index: 10000;
    }
}

@media (min-width: 993px) {
    .user-sidebar {
        transform: translateX(0) !important;
    }
    
    .menu-toggle {
        display: none !important;
    }
}
</style>