<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'duration_minutes',
        'total_questions',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_results_immediately',
        'is_active',
        'is_published',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_results_immediately' => 'boolean',
        'duration_minutes' => 'integer',
        'total_questions' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
    ];

    /**
     * Get the subjects included in this exam.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'exam_subject')
                    ->withPivot('question_count')
                    ->withTimestamps();
    }

    /**
     * Get the attempts for this exam.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Scope for active exams.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for published exams.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for available exams (active and published).
     */
    public function scopeAvailable($query)
    {
        return $query->active()->published();
    }

    /**
     * Get exam duration in seconds.
     */
    public function getDurationSecondsAttribute(): int
    {
        return $this->duration_minutes * 60;
    }

    /**
     * Check if exam has attempts limit.
     */
    public function hasAttemptsLimit(): bool
    {
        return $this->max_attempts > 0;
    }

    /**
     * Check if user can attempt this exam.
     */
    public function canUserAttempt($userId): bool
    {
        if (!$this->is_active || !$this->is_published) {
            return false;
        }

        if ($this->hasAttemptsLimit()) {
            $userAttempts = $this->attempts()
                ->where('user_id', $userId)
                ->count();
            
            return $userAttempts < $this->max_attempts;
        }

        return true;
    }


    /**
 * Get completed attempts for this exam.
 */
public function completedAttempts(): HasMany
{
    return $this->attempts()->whereIn('status', ['completed', 'submitted', 'time_expired']);
}

/**
 * Get average score for this exam.
 */
public function getAverageScoreAttribute(): float
{
    $completed = $this->completedAttempts();
    
    if ($completed->count() === 0) {
        return 0;
    }
    
    return $completed->avg('percentage');
}

/**
 * Get the results for this exam.
 */
public function results(): HasMany
{
    return $this->hasMany(Result::class);
}

/**
 * Get top performers for this exam.
 */
public function topPerformers($limit = 10)
{
    return $this->results()
        ->orderByDesc('percentage')
        ->orderBy('time_spent_seconds')
        ->limit($limit)
        ->get();
}


}