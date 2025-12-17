<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'topic_id',
        'question_text',
        'image_path',
        'difficulty',
        'marks',
        'time_estimate',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'marks' => 'integer',
        'time_estimate' => 'integer',
        'times_answered' => 'integer',
        'times_correct' => 'integer',
    ];

    /**
     * Get the subject this question belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the topic this question belongs to.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    /**
     * Get the correct answer option.
     */
    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }


    /**
 * Get options ordered by letter (A, B, C, D).
 */
public function getOrderedOptionsAttribute()
{
    return $this->options()->orderBy('option_letter')->get();
}

/**
 * Get shuffled options (for exam display).
 */
public function getShuffledOptionsAttribute()
{
    return $this->options()->inRandomOrder()->get();
}

/**
 * Check if an option letter is correct.
 */
public function isOptionCorrect(string $optionLetter): bool
{
    return $this->options()
        ->where('option_letter', $optionLetter)
        ->where('is_correct', true)
        ->exists();
}

    /**
     * Scope for active questions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for questions by difficulty.
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Calculate success rate percentage.
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->times_answered === 0) {
            return 0;
        }
        
        return ($this->times_correct / $this->times_answered) * 100;
    }

    /**
     * Increment answer counters.
     */
    public function incrementAnswerCount(bool $wasCorrect): void
    {
        $this->increment('times_answered');
        
        if ($wasCorrect) {
            $this->increment('times_correct');
        }
        
        $this->save();
    }


    /**
 * Check if this question was answered in an attempt.
 */
public function isAnsweredInAttempt(ExamAttempt $attempt): bool
{
    return $attempt->answers()->where('question_id', $this->id)->exists();
}

/**
 * Get answer for this question in an attempt.
 */
public function getAnswerInAttempt(ExamAttempt $attempt)
{
    return $attempt->answers()->where('question_id', $this->id)->first();
}
}