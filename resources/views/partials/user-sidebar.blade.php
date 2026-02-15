<aside class="user-sidebar" id="userSidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            @php
                $logo = App\Models\Setting::get('logo');
                $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
            @endphp
            
            @if($logo && Storage::disk('public')->exists($logo))
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}" class="sidebar-logo">
            @else
                <div class="sidebar-logo">
                    <i class="ri-graduation-cap-fill"></i>
                </div>
            @endif
            <div>
                <strong>{{ $siteName }}</strong>
                <small>Student Portal</small>
            </div>
        </a>
    </div>

    <div class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('user.exams.index') }}" class="sidebar-item {{ request()->routeIs('user.exams.*') ? 'active' : '' }}">
            <i class="ri-book-line"></i>
            <span>Available Exams</span>
        </a>

        <a href="{{ route('user.results.index') }}" class="sidebar-item {{ request()->routeIs('user.results.*') ? 'active' : '' }}">
            <i class="ri-bar-chart-line"></i>
            <span>Results & Reports</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="ri-user-line"></i>
            <span>My Profile</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('user.testimonials.index') }}" class="sidebar-item {{ request()->routeIs('user.testimonials.*') ? 'active' : '' }}">
            <i class="ri-chat-quote-line"></i>
            <span>Student Testimonials</span>
        </a>

        <a href="{{ route('instructions') }}" class="sidebar-item {{ request()->routeIs('instructions') ? 'active' : '' }}">
            <i class="ri-information-line"></i>
            <span>Exam Instructions</span>
        </a>

       <a href="{{ route('study.materials') }}" class="sidebar-item {{ request()->routeIs('study.materials') ? 'active' : '' }}">
    <i class="ri-download-line"></i>
    <span>Study Materials</span>
</a>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-item w-100 text-start" style="border: none; background: none; color: inherit;">
                <i class="ri-logout-box-r-line"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>