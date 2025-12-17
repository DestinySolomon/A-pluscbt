<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
        'description',
        'syllabus_ref',
        'syllabus_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'syllabus_order' => 'integer',
    ];

    /**
     * Get the subject this topic belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Scope for active topics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by syllabus.
     */
    public function scopeSyllabusOrder($query)
    {
        return $query->orderBy('syllabus_order')->orderBy('name');
    }

    public function questions()
{
    return $this->hasMany(Question::class);
}
}