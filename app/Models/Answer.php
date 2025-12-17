<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'selected_option_id',
        'selected_option',
        'is_correct',
        'answered_at',
        'time_spent_seconds',
        'marked_for_review',
        'skipped',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marked_for_review' => 'boolean',
        'skipped' => 'boolean',
        'time_spent_seconds' => 'integer',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the exam attempt this answer belongs to.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    /**
     * Get the question that was answered.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the selected option.
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'selected_option_id');
    }

    /**
     * Get the correct option for this question.
     */
    public function correctOption()
    {
        return $this->question->correctOption();
    }

    /**
     * Check if answer is correct (alternative method).
     */
    public function checkIfCorrect(): bool
    {
        $correctOption = $this->question->correctOption();
        
        if (!$correctOption) {
            return false;
        }
        
        return $this->selected_option === $correctOption->option_letter;
    }

    /**
     * Mark answer as correct or incorrect.
     */
    public function markCorrect(bool $isCorrect): void
    {
        $this->update(['is_correct' => $isCorrect]);
    }

    /**
     * Get time spent in minutes.
     */
    public function getTimeSpentMinutesAttribute(): float
    {
        return round($this->time_spent_seconds / 60, 2);
    }

    /**
     * Check if answer was modified after creation.
     */
    public function getWasModifiedAttribute(): bool
    {
        return $this->created_at != $this->updated_at;
    }
}