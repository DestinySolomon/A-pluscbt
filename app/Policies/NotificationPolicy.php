<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Notification;

class NotificationPolicy
{
    /**
     * Determine if the user can view the notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine if the user can update the notification.
     */
    public function update(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    /**
     * Determine if the user can delete the notification.
     */
    public function delete(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }
}