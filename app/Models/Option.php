<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'option_letter',
        'option_text',
        'image_path',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the question this option belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Scope for correct options.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope ordered by letter.
     */
    public function scopeOrderedByLetter($query)
    {
        return $query->orderBy('option_letter');
    }

    /**
     * Scope ordered by custom order (for randomization).
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('option_letter');
    }

    /**
     * Get option letter with label (e.g., "A. Option Text").
     */
    public function getLabelAttribute(): string
    {
        return $this->option_letter . '. ' . $this->option_text;
    }
}