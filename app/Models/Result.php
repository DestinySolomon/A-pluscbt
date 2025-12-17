<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'exam_attempt_id',
        'total_questions',
        'questions_answered',
        'correct_answers',
        'wrong_answers',
        'score',
        'percentage',
        'grade',
        'is_passed',
        'time_spent_seconds',
        'average_time_per_question',
        'subject_breakdown',
        'topic_breakdown',
        'difficulty_breakdown',
        'rank',
        'total_participants',
        'exam_date',
        'completion_status',
        'student_notes',
        'certificate_number',
        'certificate_issued_at',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'exam_date' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'subject_breakdown' => 'array',
        'topic_breakdown' => 'array',
        'difficulty_breakdown' => 'array',
        'total_questions' => 'integer',
        'questions_answered' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'score' => 'integer',
        'percentage' => 'decimal:2',
        'time_spent_seconds' => 'integer',
        'average_time_per_question' => 'decimal:2',
        'rank' => 'integer',
        'total_participants' => 'integer',
    ];

    /**
     * Get the user this result belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam this result is for.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the exam attempt this result is based on.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    /**
     * Scope for passed results.
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope for failed results.
     */
    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    /**
     * Scope for results with certificates.
     */
    public function scopeWithCertificates($query)
    {
        return $query->whereNotNull('certificate_number');
    }

    /**
     * Scope for results by grade.
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Get time spent in minutes.
     */
    public function getTimeSpentMinutesAttribute(): float
    {
        return round($this->time_spent_seconds / 60, 2);
    }

    /**
     * Get formatted percentage.
     */
    public function getPercentageFormattedAttribute(): string
    {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Get rank with suffix (1st, 2nd, 3rd, etc.).
     */
    public function getRankFormattedAttribute(): ?string
    {
        if (!$this->rank) {
            return null;
        }

        $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if (($this->rank % 100) >= 11 && ($this->rank % 100) <= 13) {
            return $this->rank . 'th';
        }
        
        return $this->rank . $suffix[$this->rank % 10];
    }

    /**
     * Check if result has certificate.
     */
    public function hasCertificate(): bool
    {
        return !is_null($this->certificate_number);
    }

    /**
     * Generate certificate number.
     */
    public function generateCertificateNumber(): string
    {
        $prefix = 'CERT';
        $examCode = strtoupper(substr($this->exam->code, 0, 4));
        $userId = str_pad($this->user_id, 6, '0', STR_PAD_LEFT);
        $date = now()->format('Ymd');
        
        return $prefix . '-' . $examCode . '-' . $date . '-' . $userId;
    }

    /**
     * Issue certificate for this result.
     */
    public function issueCertificate(): void
    {
        if (!$this->certificate_number) {
            $this->certificate_number = $this->generateCertificateNumber();
        }
        
        $this->certificate_issued_at = now();
        $this->save();
    }
}