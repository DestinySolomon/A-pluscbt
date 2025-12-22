<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['examAttempts', 'results', 'passedResults']);
        
        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }
        
        $users = $query->paginate(20)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Load user with recent attempts and results
        $user->load(['examAttempts' => function($query) {
            $query->latest()->limit(10);
        }, 'results' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        // Calculate statistics
        $stats = [
            'total_attempts' => $user->examAttempts()->count(),
            'completed_attempts' => $user->completedExamAttempts()->count(),
            'total_results' => $user->results()->count(),
            'passed_results' => $user->passedResults()->count(),
            'average_score' => $user->results()->avg('percentage') ?? 0,
            'last_active' => $user->examAttempts()->latest()->first()?->updated_at,
        ];
        
        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,student',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed'
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User {$status} successfully.");
    }

    public function makeAdmin(User $user)
    {
        $user->update(['role' => 'admin']);
        return redirect()->back()->with('success', 'User promoted to admin successfully.');
    }

    public function removeAdmin(User $user)
    {
        // Prevent removing all admins
        $adminCount = User::where('role', 'admin')->count();
        if ($adminCount <= 1) {
            return redirect()->back()->with('error', 'Cannot remove the last admin user.');
        }
        
        $user->update(['role' => 'student']);
        return redirect()->back()->with('success', 'Admin role removed successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,makestudent',
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        $users = User::whereIn('id', $request->users);
        
        switch ($request->action) {
            case 'activate':
                $users->update(['is_active' => true]);
                $message = 'Selected users activated successfully.';
                break;
                
            case 'deactivate':
                $users->update(['is_active' => false]);
                $message = 'Selected users deactivated successfully.';
                break;
                
            case 'makestudent':
                // Don't allow removing admin from yourself
                $users->where('id', '!=', auth()->id())->update(['role' => 'student']);
                $message = 'Selected users changed to student role.';
                break;
                
            case 'delete':
                // Prevent deleting yourself
                $users->where('id', '!=', auth()->id())->delete();
                $message = 'Selected users deleted successfully.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}