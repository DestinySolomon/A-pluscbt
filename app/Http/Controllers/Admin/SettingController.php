<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
   public function index()
{
    // Load all settings from database
    $settings = Setting::getAllSettings();
    
    return view('admin.settings.index', compact('settings'));
}

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            // General Settings
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            
            // Exam Settings
            'default_exam_duration' => 'required|integer|min:1|max:480',
            'max_attempts_per_exam' => 'required|integer|min:1|max:10',
            'passing_percentage' => 'required|integer|min:1|max:100',
            
            // Email Settings
            'email_from_name' => 'required|string|max:255',
            'email_from_address' => 'required|email|max:255',
            'email_subject_prefix' => 'nullable|string|max:50',
            
            // Maintenance
            'maintenance_message' => 'nullable|string|max:1000',
            
            // Files
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle logo upload or removal
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                $oldLogo = Setting::get('logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                
                $logoPath = $this->handleFileUpload($request->file('logo'), 'logo');
                Setting::set('logo', $logoPath);
            } elseif ($request->has('remove_logo') && $request->remove_logo) {
                $oldLogo = Setting::get('logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                Setting::set('logo', null);
            }
            
            // Handle favicon upload or removal
            if ($request->hasFile('favicon')) {
                // Delete old favicon if exists
                $oldFavicon = Setting::get('favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }
                
                $faviconPath = $this->handleFileUpload($request->file('favicon'), 'favicon');
                Setting::set('favicon', $faviconPath);
            } elseif ($request->has('remove_favicon') && $request->remove_favicon) {
                $oldFavicon = Setting::get('favicon');
                if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                    Storage::disk('public')->delete($oldFavicon);
                }
                Setting::set('favicon', null);
            }
            
            // Save all text settings
            Setting::set('site_name', $request->site_name);
            Setting::set('site_tagline', $request->site_tagline);
            Setting::set('contact_email', $request->contact_email);
            Setting::set('contact_phone', $request->contact_phone);
            Setting::set('contact_address', $request->contact_address);
            
            // Exam settings
            Setting::set('default_exam_duration', $request->default_exam_duration);
            Setting::set('max_attempts_per_exam', $request->max_attempts_per_exam);
            Setting::set('passing_percentage', $request->passing_percentage);
            Setting::set('show_results_immediately', $request->has('show_results_immediately') ? '1' : '0');
            Setting::set('allow_question_review', $request->has('allow_question_review') ? '1' : '0');
            Setting::set('randomize_questions', $request->has('randomize_questions') ? '1' : '0');
            Setting::set('randomize_options', $request->has('randomize_options') ? '1' : '0');
            
            // Email settings
            Setting::set('email_from_name', $request->email_from_name);
            Setting::set('email_from_address', $request->email_from_address);
            Setting::set('email_subject_prefix', $request->email_subject_prefix);
            
            // Maintenance settings
            Setting::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
            Setting::set('maintenance_message', $request->maintenance_message);
            
            // Clear settings cache
            Setting::clearCache();
            
            return redirect()->route('admin.settings')
                ->with('success', 'Settings updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file, $type = 'logo')
    {
        // Generate unique filename
        $filename = $type . '-' . time() . '.' . $file->getClientOriginalExtension();
        $directory = 'system';
        
        // Store the file
        $path = $file->storeAs($directory, $filename, 'public');
        
        return $path;
    }
}