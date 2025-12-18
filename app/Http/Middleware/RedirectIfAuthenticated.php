<?php
// ===== FILE 1: app/Http/Middleware/RedirectIfAuthenticated.php =====

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Redirect based on user role
           /** @var \App\Models\User $user */
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}


