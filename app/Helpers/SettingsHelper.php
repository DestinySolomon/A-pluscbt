<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class SettingsHelper
{
    /**
     * Get a setting value
     */
    public static function get($key, $default = null)
    {
        return config("system.{$key}", $default);
    }

    /**
     * Get site logo URL
     */
    public static function logoUrl()
    {
        $logo = self::get('logo');
        
        if ($logo && Storage::disk('public')->exists($logo)) {
            return Storage::url($logo);
        }
        
        // Return default logo or placeholder
        return asset('images/logo.png');
    }

    /**
     * Get favicon URL
     */
    public static function faviconUrl()
    {
        $favicon = self::get('favicon');
        
        if ($favicon && Storage::disk('public')->exists($favicon)) {
            return Storage::url($favicon);
        }
        
        // Return default favicon or placeholder
        return asset('images/favicon.ico');
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode()
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Get maintenance message
     */
    public static function maintenanceMessage()
    {
        return self::get('maintenance_message', 'System is under maintenance. Please check back later.');
    }

    /**
     * Get all settings
     */
    public static function all()
    {
        return config('system', []);
    }
}