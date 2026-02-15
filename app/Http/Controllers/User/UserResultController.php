<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamAttempt;
use App\Models\Result;
use App\Models\Exam;

class UserResultController extends Controller
{
    /**
     * Display a listing of the user's results
     */
    public function index()
    {
        $user = Auth::user();
        
        $results = Result::with(['exam', 'attempt'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate overall statistics
        $totalExams = Result::where('user_id', $user->id)->count();
        $averageScore = Result::where('user_id', $user->id)->avg('percentage') ?? 0;
        $passedExams = Result::where('user_id', $user->id)->where('is_passed', true)->count();
        $totalTimeSpent = Result::where('user_id', $user->id)->sum('time_spent_seconds') / 3600; // Convert to hours
        
        // Get recent exams for chart
        $recentResults = Result::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        
        // Get subject-wise performance
        $subjectPerformance = $this->getSubjectPerformance($user->id);
        
        return view('user.results.index', compact(
            'results', 
            'totalExams', 
            'averageScore', 
            'passedExams',
            'totalTimeSpent',
            'recentResults',
            'subjectPerformance'
        ));
    }
    
    /**
     * Display a specific result
     */
    public function show($attemptId)
    {
        $result = Result::with(['exam', 'attempt.answers.question.options', 'attempt.answers.question.subject'])
            ->where('user_id', Auth::id())
            ->where('exam_attempt_id', $attemptId)
            ->firstOrFail();
        
        // Get subject breakdown if available
        $subjectBreakdown = $result->subject_breakdown ? json_decode($result->subject_breakdown, true) : [];
        $topicBreakdown = $result->topic_breakdown ? json_decode($result->topic_breakdown, true) : [];
        $difficultyBreakdown = $result->difficulty_breakdown ? json_decode($result->difficulty_breakdown, true) : [];
        
        // Calculate time analysis
        $timeAnalysis = [
            'total_time' => $this->formatTime($result->time_spent_seconds),
            'average_per_question' => $result->average_time_per_question . ' seconds',
            'time_efficiency' => $this->calculateTimeEfficiency($result),
        ];
        
        // Get question analysis
        $questionAnalysis = $this->getQuestionAnalysis($result);
        
        return view('user.results.show', compact(
            'result', 
            'subjectBreakdown', 
            'topicBreakdown', 
            'difficultyBreakdown',
            'timeAnalysis',
            'questionAnalysis'
        ));
    }
    
    /**
     * Review exam questions and answers
     */
    public function review($attemptId)
    {
        $attempt = ExamAttempt::with([
                'exam', 
                'answers.question.options', 
                'answers.question.subject',
                'answers.question.topic'
            ])
            ->where('user_id', Auth::id())
            ->where('id', $attemptId)
            ->firstOrFail();
        
        // Group answers by subject for easier review
        $answersBySubject = $attempt->answers->groupBy(function ($answer) {
            return $answer->question->subject->name ?? 'Unknown Subject';
        });
        
        // Calculate performance metrics
        $performance = [
            'total_questions' => $attempt->total_questions,
            'answered' => $attempt->questions_answered,
            'correct' => $attempt->correct_answers,
            'wrong' => $attempt->wrong_answers,
            'percentage' => $attempt->percentage,
            'score' => $attempt->score,
            'grade' => $attempt->grade,
        ];
        
        return view('user.results.review', compact('attempt', 'answersBySubject', 'performance'));
    }
    
    /**
     * Get subject-wise performance
     */
    private function getSubjectPerformance($userId)
    {
        $results = Result::where('user_id', $userId)
            ->whereNotNull('subject_breakdown')
            ->get();
        
        $subjectPerformance = [];
        
        foreach ($results as $result) {
            $breakdown = json_decode($result->subject_breakdown, true);
            
            foreach ($breakdown as $subject => $data) {
                if (!isset($subjectPerformance[$subject])) {
                    $subjectPerformance[$subject] = [
                        'total_questions' => 0,
                        'correct_answers' => 0,
                        'total_score' => 0,
                        'attempts' => 0,
                    ];
                }
                
                $subjectPerformance[$subject]['total_questions'] += $data['total'];
                $subjectPerformance[$subject]['correct_answers'] += $data['correct'];
                $subjectPerformance[$subject]['total_score'] += $data['score'];
                $subjectPerformance[$subject]['attempts']++;
            }
        }
        
        // Calculate percentages
        foreach ($subjectPerformance as &$data) {
            $data['percentage'] = $data['total_questions'] > 0 
                ? round(($data['correct_answers'] / $data['total_questions']) * 100, 2) 
                : 0;
            
            $data['average_score'] = $data['attempts'] > 0 
                ? round($data['total_score'] / $data['attempts'], 2) 
                : 0;
        }
        
        // Sort by percentage (highest first)
        uasort($subjectPerformance, function($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });
        
        return $subjectPerformance;
    }
    
    /**
     * Format time from seconds to readable format
     */
    private function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }
    
    /**
     * Calculate time efficiency
     */
    private function calculateTimeEfficiency($result)
    {
        if ($result->percentage >= 80) {
            return 'Excellent';
        } elseif ($result->percentage >= 60) {
            return 'Good';
        } elseif ($result->percentage >= 40) {
            return 'Average';
        } else {
            return 'Needs Improvement';
        }
    }
    
    /**
     * Get question analysis
     */
    private function getQuestionAnalysis($result)
    {
        $attempt = $result->attempt;
        
        if (!$attempt) {
            return [];
        }
        
        $answers = $attempt->answers()->with('question')->get();
        
        $analysis = [
            'easy_correct' => 0,
            'easy_total' => 0,
            'medium_correct' => 0,
            'medium_total' => 0,
            'hard_correct' => 0,
            'hard_total' => 0,
        ];
        
        foreach ($answers as $answer) {
            $difficulty = $answer->question->difficulty ?? 'medium';
            
            switch ($difficulty) {
                case 'easy':
                    $analysis['easy_total']++;
                    if ($answer->is_correct) $analysis['easy_correct']++;
                    break;
                case 'medium':
                    $analysis['medium_total']++;
                    if ($answer->is_correct) $analysis['medium_correct']++;
                    break;
                case 'hard':
                    $analysis['hard_total']++;
                    if ($answer->is_correct) $analysis['hard_correct']++;
                    break;
            }
        }
        
        // Calculate percentages
        $analysis['easy_percentage'] = $analysis['easy_total'] > 0 
            ? round(($analysis['easy_correct'] / $analysis['easy_total']) * 100, 2) 
            : 0;
            
        $analysis['medium_percentage'] = $analysis['medium_total'] > 0 
            ? round(($analysis['medium_correct'] / $analysis['medium_total']) * 100, 2) 
            : 0;
            
        $analysis['hard_percentage'] = $analysis['hard_total'] > 0 
            ? round(($analysis['hard_correct'] / $analysis['hard_total']) * 100, 2) 
            : 0;
        
        return $analysis;
    }
}