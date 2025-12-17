@extends('layouts.guest')

@section('title', 'Login - A-plus CBT')

@section('content')
<div class="text-center mb-4">
    <div class="guest-logo mb-3">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="A-plus CBT Logo" height="40">
        </a>
    </div>
    
    <!-- Role Selection Tabs -->
    <div class="role-selection mb-4">
        <div class="btn-group w-100" role="group">
            <a href="{{ route('login') }}" 
               class="btn btn-outline-primary {{ !request()->has('admin') ? 'active' : '' }}">
                <i class="ri-user-line me-2"></i> Student Login
            </a>
            <a href="{{ route('login', ['admin' => 1]) }}" 
               class="btn btn-outline-teal {{ request()->has('admin') ? 'active' : '' }}">
                <i class="ri-admin-line me-2"></i> Admin Login
            </a>
        </div>
    </div>
    
    <!-- Dynamic Heading Based on Role -->
    @if(request()->has('admin'))
        <h5 class="fw-bold text-teal">Admin Portal</h5>
        <p class="text-muted">Sign in to access the admin dashboard</p>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="ri-information-line me-2"></i>
            <small>Use your administrator credentials to access the admin panel</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @else
        <h5 class="fw-bold">Student Portal</h5>
        <p class="text-muted">Sign in to your student account</p>
    @endif
</div>

<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf

    <!-- Hidden field for admin login -->
    @if(request()->has('admin'))
        <input type="hidden" name="is_admin_login" value="1">
    @endif

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ri-mail-line"></i>
            </span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                   placeholder="Enter your email address">
        </div>
        @error('email')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ri-lock-line"></i>
            </span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password"
                   placeholder="Enter your password">
            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                <i class="ri-eye-line"></i>
            </button>
        </div>
        @error('password')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                Remember Me
            </label>
        </div>
        
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-decoration-none small">
                Forgot Password?
            </a>
        @endif
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
        <i class="ri-login-box-line me-2"></i> 
        @if(request()->has('admin'))
            Sign in as Admin
        @else
            Sign in to Account
        @endif
    </button>

    <!-- Admin/Student Login Notice -->
    @if(request()->has('admin'))
        <div class="alert alert-warning small text-center">
            <i class="ri-alert-line me-1"></i>
            Only users with admin privileges can access this portal
        </div>
    @endif

    <div class="text-center mt-4 pt-3 border-top">
        @if(request()->has('admin'))
            <p class="text-muted mb-2">Looking for student login?</p>
            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                <i class="ri-arrow-left-line me-1"></i> Go to Student Login
            </a>
        @else
            <p class="text-muted mb-2">Don't have an account?</p>
            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
                Create Student Account
            </a>
            <div class="mt-2">
                <small class="text-muted">Are you an administrator?</small>
                <a href="{{ route('login', ['admin' => 1]) }}" class="text-decoration-none fw-semibold d-block">
                    <i class="ri-shield-user-line me-1"></i> Admin Login
                </a>
            </div>
        @endif
    </div>
</form>

<!-- Password Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            }
        });
    });

    // Highlight active tab
    const currentUrl = new URL(window.location.href);
    const isAdminLogin = currentUrl.searchParams.has('admin');
    
    // Auto-focus email field
    document.getElementById('email').focus();
});
</script>

<style>
.role-selection .btn-group .btn {
    border-radius: 8px !important;
    padding: 0.75rem;
    font-weight: 500;
}

.role-selection .btn-outline-primary.active {
    background-color: #14b8a6;
    border-color: #14b8a6;
    color: white;
}

.role-selection .btn-outline-teal {
    border-color: #0d9488;
    color: #0d9488;
}

.role-selection .btn-outline-teal.active {
    background-color: #0d9488;
    border-color: #0d9488;
    color: white;
}

.role-selection .btn-outline-teal:hover:not(.active) {
    background-color: rgba(13, 148, 136, 0.1);
    color: #0d9488;
}

.input-group-text {
    background-color: #f8fafc;
    border-right: 0;
}

.form-control:focus + .input-group-text,
.input-group:focus-within .input-group-text {
    border-color: #14b8a6;
    background-color: #f0fdfa;
}

.toggle-password {
    border-left: 0;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group .form-control {
    border-right: 0;
}

.input-group .form-control:focus {
    box-shadow: none;
    border-color: #14b8a6;
}

.input-group:focus-within .toggle-password {
    border-color: #14b8a6;
    color: #14b8a6;
}

.text-teal {
    color: #0d9488;
}

.border-teal {
    border-color: #0d9488;
}
</style>
@endsection