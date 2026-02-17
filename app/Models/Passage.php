<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Passage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'source',
        'instruction',
        'subject_id',
        'topic_id',
        'order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the subject this passage belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the topic this passage belongs to.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get all questions for this passage.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('question_number');
    }

    /**
     * Get active questions for this passage.
     */
    public function activeQuestions(): HasMany
    {
        return $this->hasMany(Question::class)->where('is_active', true)->orderBy('question_number');
    }

    /**
     * Scope for active passages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for passages by subject.
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Get the total number of questions in this passage.
     */
    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Get the first question number in this passage.
     */
    public function getFirstQuestionNumberAttribute(): ?int
    {
        $first = $this->questions()->orderBy('question_number')->first();
        return $first ? $first->question_number : null;
    }

    /**
     * Get the last question number in this passage.
     */
    public function getLastQuestionNumberAttribute(): ?int
    {
        $last = $this->questions()->orderByDesc('question_number')->first();
        return $last ? $last->question_number : null;
    }

    /**
     * Check if this passage has any questions.
     */
    public function hasQuestions(): bool
    {
        return $this->questions()->exists();
    }
}