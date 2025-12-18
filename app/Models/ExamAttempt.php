<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'completed_at',
        'time_remaining',
        'status',
        'total_questions',
        'questions_answered',
        'correct_answers',
        'wrong_answers',
        'score',
        'percentage',
        'grade',
        'is_passed',
        'questions_order',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_passed' => 'boolean',
        'questions_order' => 'array',
        'total_questions' => 'integer',
        'questions_answered' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'score' => 'integer',
        'percentage' => 'decimal:2',
        'time_remaining' => 'integer',
    ];

    /**
     * Get the user who attempted the exam.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam that was attempted.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the answers for this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Check if attempt is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if attempt is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'submitted', 'time_expired']);
    }

    /**
     * Calculate time spent in seconds.
     */
    public function getTimeSpentAttribute(): int
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $endTime->diffInSeconds($this->started_at);
    }

    /**
     * Calculate time spent in minutes.
     */
    public function getTimeSpentMinutesAttribute(): float
    {
        return round($this->time_spent / 60, 2);
    }

    /**
     * Get questions that were not answered.
     */
    public function getUnansweredCountAttribute(): int
    {
        return $this->total_questions - $this->questions_answered;
    }

    /**
     * Update score based on answers.
     */
    public function updateScore(): void
    {
        $correct = $this->answers()->where('is_correct', true)->count();
        $wrong = $this->answers()->where('is_correct', false)->count();
        $answered = $correct + $wrong;
        
        $this->update([
            'correct_answers' => $correct,
            'wrong_answers' => $wrong,
            'questions_answered' => $answered,
            'score' => $correct, // 1 mark per correct answer
            'percentage' => $this->total_questions > 0 ? ($correct / $this->total_questions) * 100 : 0,
            'is_passed' => $this->total_questions > 0 ? ($correct / $this->total_questions) * 100 >= $this->exam->passing_score : false,
        ]);
    }

    /**
     * Calculate and set grade.
     */
    public function calculateGrade(): string
    {
        $percentage = $this->percentage;
        
        if ($percentage >= 75) return 'A';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    /**
     * Complete the exam attempt.
     */
    public function complete(string $status = 'completed'): void
    {
        $this->update([
            'completed_at' => now(),
            'status' => $status,
            'grade' => $this->calculateGrade(),
        ]);
    }

    /**
     * Add or update an answer for a question.
     */
    public function addAnswer(Question $question, Option $selectedOption, int $timeSpent = 0, bool $markedForReview = false): Answer
    {
        // Check if answer already exists
        $answer = $this->answers()->where('question_id', $question->id)->first();
        
        if ($answer) {
            // Update existing answer
            $answer->update([
                'selected_option_id' => $selectedOption->id,
                'selected_option' => $selectedOption->option_letter,
                'is_correct' => $selectedOption->is_correct,
                'time_spent_seconds' => $timeSpent,
                'marked_for_review' => $markedForReview,
                'skipped' => false,
            ]);
        } else {
            // Create new answer
            $answer = $this->answers()->create([
                'question_id' => $question->id,
                'selected_option_id' => $selectedOption->id,
                'selected_option' => $selectedOption->option_letter,
                'is_correct' => $selectedOption->is_correct,
                'time_spent_seconds' => $timeSpent,
                'marked_for_review' => $markedForReview,
                'skipped' => false,
            ]);
        }
        
        // Update attempt score
        $this->updateScore();
        
        return $answer;
    }

    /**
     * Skip a question.
     */
    public function skipQuestion(Question $question): Answer
    {
        $answer = $this->answers()->where('question_id', $question->id)->first();
        
        if (!$answer) {
            $answer = $this->answers()->create([
                'question_id' => $question->id,
                'selected_option_id' => null,
                'selected_option' => null,
                'is_correct' => false,
                'skipped' => true,
            ]);
        } else {
            $answer->update(['skipped' => true]);
        }
        
        return $answer;
    }

    /**
     * Get answers for review.
     */
    public function answersForReview()
    {
        return $this->answers()->where('marked_for_review', true)->get();
    }

    /**
     * Get skipped questions.
     */
    public function skippedQuestions()
    {
        return $this->answers()->where('skipped', true)->get();
    }

    /**
     * Generate a result record from this attempt.
     */
    public function generateResult(): Result
    {
        // Calculate subject breakdown
        $subjectBreakdown = [];
        $topicBreakdown = [];
        $difficultyBreakdown = [
            'easy' => ['correct' => 0, 'total' => 0],
            'medium' => ['correct' => 0, 'total' => 0],
            'hard' => ['correct' => 0, 'total' => 0],
        ];
        
        foreach ($this->answers as $answer) {
            $question = $answer->question;
            $subject = $question->subject;
            $topic = $question->topic;
            
            // Subject breakdown
            if (!isset($subjectBreakdown[$subject->code])) {
                $subjectBreakdown[$subject->code] = [
                    'name' => $subject->name,
                    'correct' => 0,
                    'total' => 0,
                ];
            }
            $subjectBreakdown[$subject->code]['total']++;
            if ($answer->is_correct) {
                $subjectBreakdown[$subject->code]['correct']++;
            }
            
            // Topic breakdown
            if ($topic) {
                $topicKey = $topic->id;
                if (!isset($topicBreakdown[$topicKey])) {
                    $topicBreakdown[$topicKey] = [
                        'name' => $topic->name,
                        'correct' => 0,
                        'total' => 0,
                    ];
                }
                $topicBreakdown[$topicKey]['total']++;
                if ($answer->is_correct) {
                    $topicBreakdown[$topicKey]['correct']++;
                }
            }
            
            // Difficulty breakdown
            $difficultyBreakdown[$question->difficulty]['total']++;
            if ($answer->is_correct) {
                $difficultyBreakdown[$question->difficulty]['correct']++;
            }
        }
        
        // Calculate percentages
        foreach ($subjectBreakdown as &$subject) {
            $subject['percentage'] = $subject['total'] > 0 
                ? round(($subject['correct'] / $subject['total']) * 100, 2) 
                : 0;
        }
        
        foreach ($topicBreakdown as &$topic) {
            $topic['percentage'] = $topic['total'] > 0 
                ? round(($topic['correct'] / $topic['total']) * 100, 2) 
                : 0;
        }
        
        // Calculate rank (simplified - would need actual ranking logic)
        $totalParticipants = $this->exam->completedAttempts()->count();
        $rank = $this->exam->completedAttempts()
            ->where('percentage', '>', $this->percentage)
            ->count() + 1;
        
        // Create result
        return Result::create([
            'user_id' => $this->user_id,
            'exam_id' => $this->exam_id,
            'exam_attempt_id' => $this->id,
            'total_questions' => $this->total_questions,
            'questions_answered' => $this->questions_answered,
            'correct_answers' => $this->correct_answers,
            'wrong_answers' => $this->wrong_answers,
            'score' => $this->score,
            'percentage' => $this->percentage,
            'grade' => $this->grade,
            'is_passed' => $this->is_passed,
            'time_spent_seconds' => $this->time_spent,
            'average_time_per_question' => $this->total_questions > 0 
                ? round($this->time_spent / $this->total_questions, 2) 
                : 0,
            'subject_breakdown' => $subjectBreakdown,
            'topic_breakdown' => $topicBreakdown,
            'difficulty_breakdown' => $difficultyBreakdown,
            'rank' => $rank,
            'total_participants' => $totalParticipants,
            'exam_date' => $this->started_at,
            'completion_status' => $this->status,
            'student_notes' => $this->notes,
        ]);
    }
}