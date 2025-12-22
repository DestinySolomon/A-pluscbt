<nav class="admin-topnav">
    <div class="topnav-left">
        <button class="menu-toggle">
            <i class="ri-menu-line"></i>
        </button>
    </div>
    
    <div class="topnav-right">
        <!-- Notification Icon - Mobile & Desktop -->
        <div class="dropdown">
            <button class="btn btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ri-notification-3-line"></i>
                <span class="notification-badge">3</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                <div class="notification-header">
                    <h6 class="mb-0">Notifications</h6>
                    <a href="#" class="small text-primary">Mark all as read</a>
                </div>
                <div class="notification-list">
                    <a href="#" class="notification-item">
                        <div class="notification-icon">
                            <i class="ri-user-add-line"></i>
                        </div>
                        <div class="notification-content">
                            <p class="mb-0">New student registered</p>
                            <small class="text-muted">2 minutes ago</small>
                        </div>
                    </a>
                    <a href="#" class="notification-item">
                        <div class="notification-icon">
                            <i class="ri-file-text-line"></i>
                        </div>
                        <div class="notification-content">
                            <p class="mb-0">Exam completed by student</p>
                            <small class="text-muted">1 hour ago</small>
                        </div>
                    </a>
                    <a href="#" class="notification-item">
                        <div class="notification-icon">
                            <i class="ri-question-line"></i>
                        </div>
                        <div class="notification-content">
                            <p class="mb-0">New question added</p>
                            <small class="text-muted">3 hours ago</small>
                        </div>
                    </a>
                </div>
                <div class="notification-footer">
                    <a href="#" class="text-primary">View all notifications</a>
                </div>
            </div>
        </div>
        
        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="admin-user" data-bs-toggle="dropdown">
                <!-- Mobile: Show only icon/image -->
                <div class="user-avatar d-md-none">
                    @if(Auth::user()->profile_image_url)
                        <img src="{{ Auth::user()->profile_image_url }}" 
                             alt="{{ Auth::user()->name }}"
                             class="user-avatar-img"
                             onerror="this.style.display='none'; this.parentNode.innerHTML='{{ Auth::user()->initials }}';">
                    @else
                        {{ Auth::user()->initials }}
                    @endif
                </div>
                
                <!-- Desktop: Show full info -->
                <div class="d-none d-md-flex align-items-center gap-3">
                    <div class="user-avatar">
                        @if(Auth::user()->profile_image_url)
                            <img src="{{ Auth::user()->profile_image_url }}" 
                                 alt="{{ Auth::user()->name }}"
                                 class="user-avatar-img"
                                 onerror="this.style.display='none'; this.parentNode.innerHTML='{{ Auth::user()->initials }}';">
                        @else
                            {{ Auth::user()->initials }}
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="fw-medium">{{ Auth::user()->name }}</div>
                        <small class="text-muted">Administrator</small>
                    </div>
                    <i class="ri-arrow-down-s-line"></i>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="ri-user-line me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="ri-settings-3-line me-2"></i>System Settings</a></li>
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