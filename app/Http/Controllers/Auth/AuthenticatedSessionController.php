<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
{
    // Check if admin login was requested
    $isAdminLogin = $request->has('admin') || $request->routeIs('admin.login');
    
    return view('auth.login', [
        'isAdminLogin' => $isAdminLogin
    ]);
}

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    // Get the authenticated user
    $user = Auth::user();
    
    // Check if user is admin
    if ($user->isAdmin()) {
        // Check if there's an intended URL
        $intended = session()->pull('url.intended');
        
        // If intended URL is admin route or no intended URL, go to admin dashboard
        if (!$intended || str_contains($intended, '/admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        // Otherwise go to intended URL
        return redirect()->to($intended);
    }

    // For regular users, use Laravel's intended redirect
    return redirect()->intended(route('dashboard', absolute: false));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}