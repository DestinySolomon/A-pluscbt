<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug: Check if user is authenticated
        if (!Auth::check()) {
            // return redirect()->route('login');
            // For debugging, show what's happening
            abort(401, 'Not authenticated. Please login first.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Debug: Check user role
        if (!$user->isAdmin()) {
            // For debugging, show user info
            abort(403, 'Unauthorized. User role: ' . ($user->role ?? 'none') . '. Admin required.');
        }

        return $next($request);
    }
}