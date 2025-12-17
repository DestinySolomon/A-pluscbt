<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Support\Facades\DB; // Added this import

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => User::students()->count(),
            'total_subjects' => Subject::count(),
            'total_questions' => Question::count(),
            'total_exams' => Exam::count(),
            'exams_taken' => Result::count(),
            'pass_rate' => Result::count() > 0 
                ? round(Result::where('is_passed', true)->count() / Result::count() * 100, 2)
                : 0,
        ];
        
        // Recent activities
        $recentResults = Result::with(['user', 'exam'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Top performers - FIXED: Using DB facade
        $topPerformers = Result::with('user')
            ->select('user_id', DB::raw('AVG(percentage) as avg_score')) // Fixed: DB::raw
            ->groupBy('user_id')
            ->orderBy('avg_score', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard.index', compact('stats', 'recentResults', 'topPerformers'));
    }
}