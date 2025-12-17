<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">A+</div>
        <div class="sidebar-brand">A-plus CBT Admin</div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <a href="{{ route('admin.subjects.index') }}" class="sidebar-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
            <i class="ri-book-line"></i>
            <span>Subjects</span>
        </a>
        
        <a href="{{ route('admin.topics.index') }}" class="sidebar-item {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}">
            <i class="ri-folder-line"></i>
            <span>Topics</span>
        </a>
        
        <a href="{{ route('admin.questions.index') }}" class="sidebar-item {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
            <i class="ri-question-line"></i>
            <span>Questions</span>
        </a>
        
        <a href="{{ route('admin.exams.index') }}" class="sidebar-item {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
            <i class="ri-file-list-line"></i>
            <span>Exams</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <a href="{{ route('admin.users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="ri-user-line"></i>
            <span>Users</span>
        </a>
        
        <a href="{{ route('admin.results.index') }}" class="sidebar-item {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
            <i class="ri-bar-chart-line"></i>
            <span>Results</span>
        </a>
        
        <div class="sidebar-divider"></div>
        
        <a href="{{ route('admin.settings') }}" class="sidebar-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <i class="ri-settings-3-line"></i>
            <span>Settings</span>
        </a>
        
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
           class="sidebar-item">
            <i class="ri-logout-box-r-line"></i>
            <span>Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>