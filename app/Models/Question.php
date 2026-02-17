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
        'topic_id', // Now nullable
        'question_text',
        'image_path',
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

    // ADD THESE ACCESSORS FOR BACKWARD COMPATIBILITY
    public function getQuestionAttribute()
    {
        return $this->question_text;
    }

    public function setQuestionAttribute($value)
    {
        $this->attributes['question_text'] = $value;
    }

    public function getOptionAAttribute()
    {
        return $this->options->firstWhere('option_letter', 'A')->option_text ?? null;
    }

    public function getOptionBAttribute()
    {
        return $this->options->firstWhere('option_letter', 'B')->option_text ?? null;
    }

    public function getOptionCAttribute()
    {
        return $this->options->firstWhere('option_letter', 'C')->option_text ?? null;
    }

    public function getOptionDAttribute()
    {
        return $this->options->firstWhere('option_letter', 'D')->option_text ?? null;
    }

    // ADD THIS NEW ACCESSOR FOR OPTION E
    public function getOptionEAttribute()
    {
        return $this->options->firstWhere('option_letter', 'E')->option_text ?? null;
    }

    public function getCorrectOptionAttribute()
    {
        return $this->options->where('is_correct', true)->first()->option_letter ?? null;
    }
    // END OF ACCESSORS

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
     * Get options ordered by letter (A, B, C, D, E).
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
     * Scope for random questions (for exam generation).
     */
    public function scopeRandom($query, $limit = 50)
    {
        return $query->inRandomOrder()->limit($limit);
    }

    /**
     * Scope for questions by subject.
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope for questions by topic.
     */
    public function scopeByTopic($query, $topicId)
    {
        return $query->where('topic_id', $topicId);
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

    /**
     * Generate random exam questions.
     */
    public static function generateExamQuestions($subjectId, $topicIds = null, $limit = 50)
    {
        $query = self::where('subject_id', $subjectId)
                    ->where('is_active', true);
        
        if ($topicIds) {
            $query->whereIn('topic_id', $topicIds);
        }
        
        return $query->inRandomOrder()
                    ->with(['options' => function($query) {
                        $query->orderBy('option_letter');
                    }])
                    ->limit($limit)
                    ->get();
    }


    // Add these relationships to your existing Question model

/**
 * Get the passage this question belongs to (if any).
 */
public function passage(): BelongsTo
{
    return $this->belongsTo(Passage::class);
}

/**
 * Check if this question belongs to a passage.
 */
public function belongsToPassage(): bool
{
    return !is_null($this->passage_id);
}

/**
 * Get the next question in the same passage.
 */
public function nextInPassage()
{
    if (!$this->belongsToPassage()) {
        return null;
    }
    
    return $this->passage->questions()
        ->where('question_number', '>', $this->question_number)
        ->orderBy('question_number')
        ->first();
}

/**
 * Get the previous question in the same passage.
 */
public function previousInPassage()
{
    if (!$this->belongsToPassage()) {
        return null;
    }
    
    return $this->passage->questions()
        ->where('question_number', '<', $this->question_number)
        ->orderByDesc('question_number')
        ->first();
}
}