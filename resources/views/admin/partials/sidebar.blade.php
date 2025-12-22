@php
    $logo = App\Models\Setting::get('logo');
    $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
@endphp

<aside class="admin-sidebar">
    <div class="sidebar-header">
        @if($logo && Storage::disk('public')->exists($logo))
            <div class="sidebar-logo-image">
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }} Logo">
            </div>
        @else
            <div class="sidebar-logo">A+</div>
        @endif
        <div class="sidebar-brand">{{ $siteName }} Admin</div>
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
        
        <a href="{{ route('admin.testimonials.index') }}" class="sidebar-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
            <i class="ri-chat-quote-line"></i>
            <span>Testimonials</span>
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

<style>
    /* Add this to your admin.css or in a style block */
    .sidebar-logo-image {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 12px;
        padding: 8px;
        margin: 0 auto;
    }
    
    .sidebar-logo-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>