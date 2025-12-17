<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'order',
        'icon_class',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the topics for this subject.
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('syllabus_order');
    }

    /**
     * Get the main topics (no parent) for this subject.
     */
    public function mainTopics(): HasMany
    {
        return $this->hasMany(Topic::class)->whereNull('parent_id')->orderBy('syllabus_order');
    }

    /**
     * Scope for active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');

    }

    public function questions()
{
    return $this->hasMany(Question::class);
}


/**
 * Get the exams that include this subject.
 */
public function exams(): BelongsToMany
{
    return $this->belongsToMany(Exam::class, 'exam_subject')
                ->withPivot('question_count', 'topic_ids', 'difficulty_distribution')
                ->withTimestamps();
}

}