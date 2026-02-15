<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
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
        $user = Auth::user();
        
        // For AJAX requests (notification dropdown)
        if ($request->expectsJson() || $request->ajax() || $request->has('limit')) {
            $limit = $request->get('limit', 10);
            
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'link' => $notification->link,
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at,
                        'icon' => $notification->icon,
                        'color' => $notification->color,
                    ];
                });
            
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $user->unread_notifications_count ?? 0,
                'success' => true
            ]);
        }
        
        // For regular page requests (notifications index page with filters)
        $limit = $request->get('limit', 20);
        
        // Start building query
        $query = $user->notifications()->latest();
        
        // Filter by type if specified
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $query->where('is_read', false);
            }
        }
        
        // Filter by period
        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
            }
        }
        
        $notifications = $query->paginate($limit);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (for AJAX).
     */
    public function unreadCount()
    {
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
        $user = Auth::user();
        
        // Check if user owns the notification
        if ($user->id !== $notification->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }
        
        // Mark as read
        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        
        // Refresh the user to get updated count
        $user->refresh();
        
        return response()->json([
            'success' => true,
            'unread_count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'unread_count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        
        // Check if user owns the notification
        if ($user->id !== $notification->user_id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'unread_count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Clear all read notifications.
     */
    public function clearRead()
    {
        $user = Auth::user();
        $count = $user->notifications()
            ->where('is_read', true)
            ->delete();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get notification statistics.
     */
    public function statistics()
    {
        $user = Auth::user();
        $stats = $this->notificationService->getStatistics($user);
        
        return response()->json($stats);
    }
}