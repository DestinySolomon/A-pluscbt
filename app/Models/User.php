<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'bio',
        'profile_image',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
        'email_notifications',
        'exam_notifications',
        'result_notifications',
        'system_notifications',
    ];

    // Accessor for profile image URL
    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->profile_image && Storage::disk('public')->exists($this->profile_image)) {
            return asset('storage/' . $this->profile_image);
        }
        return null;
    }

    public function getInitialsAttribute(): string
    {
        $name = $this->name;
        $initials = '';
        $words = explode(' ', $name);
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
                if (strlen($initials) >= 2) break;
            }
        }
        
        return $initials ?: strtoupper(substr($this->email, 0, 2));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the exam attempts for this user.
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Get the completed exam attempts.
     */
    public function completedExamAttempts(): HasMany
    {
        return $this->examAttempts()->whereIn('status', ['completed', 'submitted', 'time_expired']);
    }

    /**
     * Get the results for this user.
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get passed results for this user.
     */
    public function passedResults(): HasMany
    {
        return $this->results()->where('is_passed', true);
    }

    /**
     * Get average percentage across all exams.
     */
    public function getAveragePercentageAttribute(): float
    {
        return $this->results()->avg('percentage') ?? 0;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Scope for admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for student users.
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->unread();
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Create a notification for the user.
     */
    public function notify(string $type, string $title, string $message, ?array $data = null, ?string $link = null): Notification
    {
        return $this->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'link' => $link,
        ]);
    }
}