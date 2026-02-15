<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Use the new user profile view
        return view('user.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Update basic info
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Update social links and notification preferences
        $user->phone = $request->input('phone');
        $user->bio = $request->input('bio');
        $user->facebook_url = $request->input('facebook_url');
        $user->twitter_url = $request->input('twitter_url');
        $user->linkedin_url = $request->input('linkedin_url');
        $user->instagram_url = $request->input('instagram_url');
        
        // Update notification preferences
        $user->email_notifications = $request->boolean('email_notifications');
        $user->exam_notifications = $request->boolean('exam_notifications');
        $user->result_notifications = $request->boolean('result_notifications');
        $user->system_notifications = $request->boolean('system_notifications');

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Store new photo
        $path = $request->file('profile_image')->store('profile-images', 'public');
        
        $user->profile_image = $path;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'image_url' => asset('storage/' . $path)
            ]);
        }

        return Redirect::route('profile.edit')->with('success', 'Profile photo updated successfully!');
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
            
            $user->profile_image = null;
            $user->save();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo removed successfully!'
            ]);
        }

        return Redirect::route('profile.edit')->with('success', 'Profile photo removed successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}