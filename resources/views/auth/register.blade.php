@extends('layouts.guest')

@section('title', 'Register - A-plus CBT')

@section('content')
<div class="text-center mb-4">
    <h5 class="fw-bold">Create Account</h5>
    <p class="text-muted">Sign up to get started</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
               name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" required autocomplete="new-password">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password-confirm" class="form-label">Confirm Password</label>
        <input id="password-confirm" type="password" class="form-control" 
               name="password_confirmation" required autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-primary w-100">
        <i class="ri-user-add-line me-2"></i> Create Account
    </button>

    <div class="text-center mt-4">
        <p class="text-muted mb-0">Already have an account?</p>
        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
            Sign In
        </a>
    </div>
</form>
@endsection