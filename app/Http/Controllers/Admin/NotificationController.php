<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Add this import

class NotificationController extends Controller
{
    use AuthorizesRequests; // Add this trait

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user's notifications.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $limit = $request->get('limit', 20);
        
        $notifications = $user->notifications()
            ->recent()
            ->paginate($limit);
        
        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'total' => $notifications->total(),
                'unread_count' => $user->unread_notifications_count,
            ]);
        }
        
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (for AJAX).
     */
    public function unreadCount()
    {
        /** @var User $user */
        $user = Auth::user();
        return response()->json([
            'count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);
        
        $notification->markAsRead();
        
        if (request()->expectsJson()) {
            /** @var User $user */
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'unread_count' => $user->unread_notifications_count
            ]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'unread_count' => 0
            ]);
        }
        
        return redirect()->back()->with('success', "Marked {$count} notifications as read.");
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        
        $notification->delete();
        
        if (request()->expectsJson()) {
            /** @var User $user */
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'unread_count' => $user->unread_notifications_count
            ]);
        }
        
        return redirect()->back()->with('success', 'Notification deleted.');
    }

    /**
     * Clear all read notifications.
     */
    public function clearRead()
    {
        /** @var User $user */
        $user = Auth::user();
        $count = $user->notifications()
            ->where('is_read', true)
            ->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        }
        
        return redirect()->back()->with('success', "Cleared {$count} read notifications.");
    }

    /**
     * Get notification statistics.
     */
    public function statistics()
    {
        /** @var User $user */
        $user = Auth::user();
        $stats = $this->notificationService->getStatistics($user);
        
        return response()->json($stats);
    }
}