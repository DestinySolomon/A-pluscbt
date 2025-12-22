<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\User;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['user', 'exam']);
        
        // Apply filters
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        
        if ($request->filled('status')) {
            if ($request->status == 'passed') {
                $query->where('is_passed', true);
            } elseif ($request->status == 'failed') {
                $query->where('is_passed', false);
            }
        }
        
        if ($request->filled('has_certificate')) {
            if ($request->has_certificate == 'yes') {
                $query->whereNotNull('certificate_number');
            } elseif ($request->has_certificate == 'no') {
                $query->whereNull('certificate_number');
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('exam', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('exam_date', 'asc');
                break;
            case 'highest_score':
                $query->orderBy('percentage', 'desc');
                break;
            case 'lowest_score':
                $query->orderBy('percentage', 'asc');
                break;
            case 'user_name':
                $query->join('users', 'results.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')
                      ->select('results.*');
                break;
            case 'exam_name':
                $query->join('exams', 'results.exam_id', '=', 'exams.id')
                      ->orderBy('exams.name', 'asc')
                      ->select('results.*');
                break;
            case 'newest':
            default:
                $query->orderBy('exam_date', 'desc');
                break;
        }
        
        $results = $query->paginate(20)->withQueryString();
        
        // Get data for filters
        $exams = Exam::where('is_published', true)->orderBy('name')->get();
        $users = User::where('role', 'student')->orderBy('name')->get();
        
        // Calculate summary statistics
        $summary = [
            'total_results' => Result::count(),
            'average_percentage' => Result::avg('percentage') ?? 0,
            'pass_rate' => Result::where('is_passed', true)->count() / max(Result::count(), 1) * 100,
            'total_certificates' => Result::whereNotNull('certificate_number')->count(),
        ];
        
        return view('admin.results.index', compact('results', 'exams', 'users', 'summary'));
    }

    public function show(Result $result)
    {
        // Load related data
        $result->load(['user', 'exam', 'attempt']);
        
        // Prepare subject breakdown for chart
        $subjectBreakdown = $result->subject_breakdown ?? [];
        $subjectLabels = [];
        $subjectPercentages = [];
        $subjectColors = [];
        
        $baseColor = '#14b8a6'; // Primary color
        $colorVariations = ['#0d9488', '#0f766e', '#115e59']; // Darker variations
        
        $i = 0;
        foreach ($subjectBreakdown as $subjectCode => $data) {
            $subjectLabels[] = $subjectCode . ': ' . $data['name'];
            $subjectPercentages[] = $data['percentage'] ?? 0;
            $subjectColors[] = $colorVariations[$i % count($colorVariations)];
            $i++;
        }
        
        // Prepare difficulty breakdown
        $difficultyBreakdown = $result->difficulty_breakdown ?? [];
        $difficultyLabels = ['Easy', 'Medium', 'Hard'];
        $difficultyCorrect = [];
        $difficultyTotal = [];
        
        foreach (['easy', 'medium', 'hard'] as $difficulty) {
            $difficultyCorrect[] = $difficultyBreakdown[$difficulty]['correct'] ?? 0;
            $difficultyTotal[] = $difficultyBreakdown[$difficulty]['total'] ?? 0;
        }
        
        // Get exam average for comparison
        $examAverage = Result::where('exam_id', $result->exam_id)->avg('percentage') ?? 0;
        
        return view('admin.results.show', compact(
            'result', 
            'subjectLabels', 
            'subjectPercentages', 
            'subjectColors',
            'difficultyLabels',
            'difficultyCorrect',
            'difficultyTotal',
            'examAverage'
        ));
    }

    public function analyticsOverview(Request $request)
    {
        // Determine date range
        $period = $request->get('period', '30days');
        $dateFrom = match($period) {
            '7days' => Carbon::now()->subDays(7),
            '30days' => Carbon::now()->subDays(30),
            '90days' => Carbon::now()->subDays(90),
            'quarter' => Carbon::now()->subMonths(3),
            'year' => Carbon::now()->subYear(),
            'all' => null,
            default => Carbon::now()->subDays(30),
        };
        
        // Build query
        $query = Result::query();
        if ($dateFrom) {
            $query->where('exam_date', '>=', $dateFrom);
        }
        
        // Overall statistics
        $totalResults = $query->count();
        $totalStudents = $query->distinct('user_id')->count('user_id');
        $averageScore = $query->avg('percentage') ?? 0;
        $passRate = $totalResults > 0 
            ? ($query->where('is_passed', true)->count() / $totalResults * 100) 
            : 0;
        
        // Daily results trend (last 30 days)
        $dailyTrend = Result::where('exam_date', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(exam_date) as date, COUNT(*) as count, AVG(percentage) as avg_score')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Exam performance
           // Exam performance - FIXED VERSION
    $examPerformance = Result::with('exam')
        ->select('exam_id', DB::raw('AVG(percentage) as avg_score'), DB::raw('COUNT(*) as attempts'))
        ->groupBy('exam_id')
        ->orderByDesc('avg_score')
        ->limit(10)
        ->get();
        
        // Grade distribution
        $gradeDistribution = Result::select('grade', DB::raw('COUNT(*) as count'))
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();
        
        // Time of day analysis
        $timeAnalysis = Result::selectRaw('HOUR(exam_date) as hour, COUNT(*) as count, AVG(percentage) as avg_score')
            ->where('exam_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        return view('admin.results.analytics', compact(
            'totalResults',
            'totalStudents',
            'averageScore',
            'passRate',
            'dailyTrend',
            'examPerformance',
            'gradeDistribution',
            'timeAnalysis',
            'period'
        ));
    }
public function subjectPerformance(Request $request)
{
    // Build base query for results
    $resultQuery = Result::query();
    
    if ($request->filled('date_from')) {
        $resultQuery->whereDate('exam_date', '>=', $request->date_from);
    }
    
    if ($request->filled('date_to')) {
        $resultQuery->whereDate('exam_date', '<=', $request->date_to);
    }
    
    // Get all results with subject breakdowns
    $results = $resultQuery->get();
    
    // Get all active subjects
    $subjects = Subject::active()->get();
    
    // Calculate subject performance from results
    $subjectPerformance = [];
    $subjectStats = [];
    
    // Initialize stats for all subjects
    foreach ($subjects as $subject) {
        $subjectStats[$subject->code] = [
            'subject' => $subject,
            'total_questions' => 0,
            'correct_answers' => 0,
            'attempt_count' => 0,
        ];
    }
    
    // Process each result
    foreach ($results as $result) {
        $breakdown = $result->subject_breakdown ?? [];
        
        foreach ($breakdown as $subjectCode => $data) {
            if (isset($subjectStats[$subjectCode])) {
                $subjectStats[$subjectCode]['total_questions'] += $data['total'] ?? 0;
                $subjectStats[$subjectCode]['correct_answers'] += $data['correct'] ?? 0;
                $subjectStats[$subjectCode]['attempt_count']++;
            }
        }
    }
    
    // Convert to subject performance array
    foreach ($subjectStats as $stats) {
        $totalQuestions = $stats['total_questions'];
        $correctAnswers = $stats['correct_answers'];
        $attemptCount = $stats['attempt_count'];
        
        $averageScore = $totalQuestions > 0 
            ? ($correctAnswers / $totalQuestions * 100) 
            : 0;
        
        $subjectPerformance[] = [
            'subject' => $stats['subject'],
            'average_score' => round($averageScore, 2),
            'attempt_count' => $attemptCount,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
        ];
    }
    
    // Sort by average score (descending)
    usort($subjectPerformance, function($a, $b) {
        return $b['average_score'] <=> $a['average_score'];
    });
    
    // Prepare data for charts
    $subjectNames = array_column($subjectPerformance, 'subject.name');
    $subjectScores = array_column($subjectPerformance, 'average_score');
    $subjectAttempts = array_column($subjectPerformance, 'attempt_count');
    
    return view('admin.results.subject-performance', compact(
        'subjectPerformance',
        'subjectNames',
        'subjectScores',
        'subjectAttempts'
    ));
}

    public function topPerformers(Request $request)
    {
        $query = Result::with(['user', 'exam'])
            ->select('*')
            ->addSelect(DB::raw('ROW_NUMBER() OVER (PARTITION BY exam_id ORDER BY percentage DESC, time_spent_seconds ASC) as exam_rank'));
        
        // Apply filters
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', $request->date_to);
        }
        
        // Get top 50 results
        $topResults = $query->orderBy('percentage', 'desc')
            ->orderBy('time_spent_seconds', 'asc')
            ->limit(50)
            ->get();
        
        // Group by exam for display
        $groupedResults = $topResults->groupBy('exam_id');
        
        // Get exams for filter
        $exams = Exam::where('is_published', true)->orderBy('name')->get();
        
        return view('admin.results.top-performers', compact('topResults', 'groupedResults', 'exams'));
    }

    public function export(Request $request)
    {
        // Apply the same filters as index
        $query = $this->buildExportQuery($request);
        $results = $query->with(['user', 'exam'])->get();
        
        $filename = 'results_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'ID',
                'Student Name',
                'Student Email',
                'Exam Name',
                'Exam Code',
                'Total Questions',
                'Correct Answers',
                'Score',
                'Percentage',
                'Grade',
                'Passed',
                'Time Spent (min)',
                'Rank',
                'Total Participants',
                'Exam Date',
                'Certificate Number',
                'Certificate Issued At'
            ]);
            
            // Data
            foreach ($results as $result) {
                fputcsv($file, [
                    $result->id,
                    $result->user->name ?? 'N/A',
                    $result->user->email ?? 'N/A',
                    $result->exam->name ?? 'N/A',
                    $result->exam->code ?? 'N/A',
                    $result->total_questions,
                    $result->correct_answers,
                    $result->score,
                    $result->percentage,
                    $result->grade,
                    $result->is_passed ? 'Yes' : 'No',
                    $result->time_spent_minutes,
                    $result->rank ?? 'N/A',
                    $result->total_participants ?? 'N/A',
                    $result->exam_date->format('Y-m-d H:i:s'),
                    $result->certificate_number ?? 'N/A',
                    $result->certificate_issued_at ? $result->certificate_issued_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
    
    private function buildExportQuery(Request $request)
    {
        $query = Result::query();
        
        // Apply same filters as index method
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        
        if ($request->filled('status')) {
            if ($request->status == 'passed') {
                $query->where('is_passed', true);
            } elseif ($request->status == 'failed') {
                $query->where('is_passed', false);
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('exam_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('exam_date', '<=', $request->date_to);
        }
        
        return $query;
    }

    public function issueCertificate(Result $result)
    {
        // Check if result is passed
        if (!$result->is_passed) {
            return redirect()->back()->with('error', 'Cannot issue certificate for failed result.');
        }
        
        // Check if certificate already issued
        if ($result->certificate_number) {
            return redirect()->back()->with('error', 'Certificate already issued for this result.');
        }
        
        // Issue certificate
        $result->issueCertificate();
        
        return redirect()->back()->with('success', 'Certificate issued successfully.');
    }

    public function viewCertificate(Result $result)
    {
        // Check if certificate exists
        if (!$result->certificate_number) {
            return redirect()->back()->with('error', 'Certificate not issued for this result.');
        }
        
        return view('admin.results.certificate', compact('result'));
    }

    public function downloadCertificate(Result $result)
    {
        // For now, just redirect to view certificate
        // Later can implement PDF generation with DomPDF
        return redirect()->route('admin.results.certificate', $result);
    }
}