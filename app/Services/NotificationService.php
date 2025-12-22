<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log; // Add this import

class NotificationService
{
    /**
     * Send notification to a user.
     */
  public function sendToUser(User $user, string $type, string $title, string $message, ?array $data = null, ?string $link = null): Notification
{
    // Check user's notification preferences
    if (!$this->shouldSendNotification($user, $type)) {
        throw new \Exception("User has disabled {$type} notifications");
    }

    /** @var Notification $notification */
    $notification = $user->notify($type, $title, $message, $data, $link);
    return $notification;
}

    /**
     * Send notification to multiple users.
     */
    public function sendToUsers($users, string $type, string $title, string $message, ?array $data = null, ?string $link = null): array
    {
        $notifications = [];
        
        foreach ($users as $user) {
            try {
                if ($this->shouldSendNotification($user, $type)) {
                    $notifications[] = $user->notify($type, $title, $message, $data, $link);
                }
            } catch (\Exception $e) {
                // Log error but continue with other users
                Log::error('Failed to send notification to user ' . $user->id, [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $notifications;
    }

    /**
     * Send notification to all admins.
     */
    public function sendToAdmins(string $type, string $title, string $message, ?array $data = null, ?string $link = null): array
    {
        $admins = User::where('role', 'admin')->get();
        return $this->sendToUsers($admins, $type, $title, $message, $data, $link);
    }

    /**
     * Send notification to all students.
     */
    public function sendToStudents(string $type, string $title, string $message, ?array $data = null, ?string $link = null): array
    {
        $students = User::where('role', 'student')->get();
        return $this->sendToUsers($students, $type, $title, $message, $data, $link);
    }

    /**
     * Check if user should receive notification based on preferences.
     */
    private function shouldSendNotification(User $user, string $type): bool
    {
        $preferences = [
            'exam' => 'exam_notifications',
            'result' => 'result_notifications',
            'system' => 'system_notifications',
            'user' => 'email_notifications', // User registrations go to email
            'question' => 'email_notifications', // Question alerts go to email
        ];

        if (!isset($preferences[$type])) {
            return true; // Default to true for unknown types
        }

        $preferenceField = $preferences[$type];
        
        // Check if the field exists on user model
        if (property_exists($user, $preferenceField) || isset($user->$preferenceField)) {
            return (bool) $user->$preferenceField;
        }

        return true; // Default to true if preference field doesn't exist
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Delete old read notifications (older than 30 days).
     */
    public function cleanupOldNotifications(int $days = 30): int
    {
        return Notification::where('is_read', true)
            ->where('read_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get notification statistics for a user.
     */
    public function getStatistics(User $user): array
    {
        $total = $user->notifications()->count();
        $unread = $user->unreadNotifications()->count();
        $read = $total - $unread;
        
        $types = $user->notifications()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
            'types' => $types,
        ];
    }
}