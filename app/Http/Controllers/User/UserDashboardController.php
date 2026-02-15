<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Result;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    /**
     * Get dashboard statistics (AJAX)
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        
        // Count available exams (published and active)
        $availableExams = Exam::where('is_published', true)
            ->where('is_active', true)
            ->count();
        
        // Count completed exams
        $completedExams = ExamAttempt::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'submitted'])
            ->count();
        
        // Calculate average score
        $averageScore = ExamAttempt::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'submitted'])
            ->avg('percentage') ?? 0;
        
        // Calculate total time spent (in hours)
        $totalTime = ExamAttempt::where('user_id', $user->id)
            ->sum('time_spent_seconds') / 3600; // Convert seconds to hours
        
        return response()->json([
            'success' => true,
            'data' => [
                'available_exams' => $availableExams,
                'completed_exams' => $completedExams,
                'average_score' => round($averageScore, 1),
                'total_time' => round($totalTime, 1),
            ]
        ]);
    }
    
    /**
     * Get recent exams (AJAX)
     */
    public function getRecentExams(Request $request)
    {
        $user = Auth::user();
        
        $recentExams = ExamAttempt::with('exam')
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'submitted'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($attempt) {
                return [
                    'name' => $attempt->exam->name,
                    'date' => $attempt->created_at->format('M d, Y'),
                    'score' => $attempt->percentage,
                    'status' => $attempt->status,
                    'review_url' => route('user.results.review', $attempt->id),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $recentExams
        ]);
    }
    
    /**
     * Get recommended exams (AJAX)
     */
    public function getRecommendedExams(Request $request)
    {
        $recommendedExams = Exam::where('is_published', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'description' => $exam->description ?? 'Practice exam for JAMB preparation',
                    'duration' => $exam->duration_minutes,
                    'questions' => $exam->total_questions,
                    'start_url' => route('user.exams.instructions', $exam->id),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $recommendedExams
        ]);
    }
}