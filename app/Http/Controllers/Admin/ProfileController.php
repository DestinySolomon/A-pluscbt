<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the admin profile settings page.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update basic profile information.
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'profile');
        }

        $user->update($request->only(['name', 'email', 'phone', 'bio']));

        return redirect()->back()
            ->with('success', 'Profile updated successfully!')
            ->with('active_tab', 'profile');
    }

    /**
     * Update profile image.
     */
    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Delete old profile image if exists
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Store new image in profile-images directory
        $path = $request->file('profile_image')->store('profile-images', 'public');
        
        // Update user record
        $user->update(['profile_image' => $path]);

        return redirect()->back()
            ->with('success', 'Profile image updated successfully!')
            ->with('active_tab', 'profile');
    }

    /**
     * Remove profile image.
     */
    public function removeProfileImage()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->update(['profile_image' => null]);

        return redirect()->back()
            ->with('success', 'Profile image removed successfully!')
            ->with('active_tab', 'profile');
    }

    /**
     * Update social media links.
     */
    public function updateSocialLinks(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'social');
        }

        $user->update($request->only([
            'facebook_url',
            'twitter_url',
            'linkedin_url',
            'instagram_url',
        ]));

        return redirect()->back()
            ->with('success', 'Social media links updated successfully!')
            ->with('active_tab', 'social');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'exam_notifications' => $request->has('exam_notifications'),
            'result_notifications' => $request->has('result_notifications'),
            'system_notifications' => $request->has('system_notifications'),
        ]);

        return redirect()->back()
            ->with('success', 'Notification preferences updated successfully!')
            ->with('active_tab', 'notifications');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', 'password');
        }

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Log out from all other devices (optional)
        Auth::logoutOtherDevices($request->new_password);

        return redirect()->back()
            ->with('success', 'Password updated successfully! You have been logged out from other devices.')
            ->with('active_tab', 'password');
    }

    /**
     * Delete account (optional - for future implementation).
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
            'confirmation' => 'required|in:DELETE MY ACCOUNT',
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Optional: Add logic to anonymize or soft delete data
        // $user->delete();
        
        Auth::logout();
        
        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}