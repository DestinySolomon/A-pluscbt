<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'link',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for recent notifications (last 30 days).
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Scope by notification type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
        return $this;
    }

    /**
     * Get icon based on notification type.
     */
    public function getIconAttribute()
    {
        $icons = [
            'exam' => 'ri-file-text-line',
            'result' => 'ri-award-line',
            'system' => 'ri-settings-3-line',
            'user' => 'ri-user-add-line',
            'question' => 'ri-question-line',
            'warning' => 'ri-alert-line',
            'success' => 'ri-checkbox-circle-line',
            'info' => 'ri-information-line',
        ];

        return $icons[$this->type] ?? 'ri-notification-3-line';
    }

    /**
     * Get color based on notification type.
     */
    public function getColorAttribute()
    {
        $colors = [
            'exam' => '#14b8a6',
            'result' => '#10b981',
            'system' => '#6366f1',
            'user' => '#8b5cf6',
            'question' => '#f59e0b',
            'warning' => '#f97316',
            'success' => '#10b981',
            'info' => '#3b82f6',
        ];

        return $colors[$this->type] ?? '#6b7280';
    }

    /**
     * Get time ago format.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }


    /**
 * Get color as RGB string.
 */
public function getColorRgbAttribute()
{
    $colors = [
        'exam' => '20, 184, 166',
        'result' => '16, 185, 129',
        'system' => '99, 102, 241',
        'user' => '139, 92, 246',
        'question' => '245, 158, 11',
        'warning' => '249, 115, 22',
        'success' => '16, 185, 129',
        'info' => '59, 130, 246',
    ];

    return $colors[$this->type] ?? '107, 114, 128';
}

/**
 * Get color as HEX string (alias for color attribute).
 */
public function getColorHexAttribute()
{
    return $this->color; // Use the existing color attribute
}

    
}