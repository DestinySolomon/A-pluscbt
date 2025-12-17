@extends('layouts.guest')

@section('title', 'Login - A-plus CBT')

@section('content')
<div class="text-center mb-4">
    <h5 class="fw-bold">Welcome Back</h5>
    <p class="text-muted">Sign in to your account</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" required autocomplete="current-password">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Remember Me
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        <i class="ri-login-box-line me-2"></i> Sign In
    </button>

    @if (Route::has('password.request'))
        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}" class="text-decoration-none">
                Forgot Your Password?
            </a>
        </div>
    @endif

    <div class="text-center mt-4">
        <p class="text-muted mb-0">Don't have an account?</p>
        <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
            Create Account
        </a>
    </div>
</form>
@endsection