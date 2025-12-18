<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="A-plus CBT Logo" class="me-2">
            <span class="fw-bold fs-5">A-plus CBT</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#how-it-works">How It Works</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#subjects">Subjects</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#testimonials">Testimonials</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#contact">Contact</a></li>
                @auth
                    <li class="nav-item ms-lg-2">
                        {{-- Fixed: Use isAdmin() method instead of is_admin property --}}
                        @if(auth()->user()->isAdmin())
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="ri-dashboard-line me-1"></i> Admin Dashboard
                            </a>
                        @else
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="ri-dashboard-line me-1"></i> My Dashboard
                            </a>
                        @endif
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                                <i class="ri-logout-box-r-line me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="ri-login-box-line me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary btn-sm px-4" href="{{ route('register') }}">
                            <i class="ri-user-add-line me-1"></i> Get Started
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>