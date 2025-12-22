<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        if (SettingsHelper::isMaintenanceMode() && !$request->user()?->is_admin) {
            return response()->view('maintenance', [
                'message' => SettingsHelper::maintenanceMessage()
            ], 503);
        }

        return $next($request);
    }
}