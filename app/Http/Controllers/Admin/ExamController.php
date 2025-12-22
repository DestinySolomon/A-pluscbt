<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Question;
use App\Models\ExamAttempt;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $type = $request->get('type');
        $status = $request->get('status');
        $search = $request->get('search');
        
        // Start query
        $query = Exam::withCount(['subjects', 'attempts', 'results'])
                     ->with(['subjects' => function($query) {
                         $query->select('name', 'code');
                     }]);
        
        // Apply filters
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status) {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'published':
                    $query->where('is_published', true);
                    break;
                case 'draft':
                    $query->where('is_published', false);
                    break;
                case 'available':
                    $query->where('is_active', true)->where('is_published', true);
                    break;
            }
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Order and paginate
        $exams = $query->latest()
                      ->paginate(15)
                      ->withQueryString();
        
        // Statistics for dashboard
        $totalExams = Exam::count();
        $activeExams = Exam::where('is_active', true)->count();
        $publishedExams = Exam::where('is_published', true)->count();
        $totalAttempts = ExamAttempt::count();
        
        return view('admin.exams.index', compact(
            'exams', 
            'totalExams', 
            'activeExams', 
            'publishedExams', 
            'totalAttempts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all active subjects for selection
        $subjects = Subject::active()->ordered()->get();
        
        // Get JAMB compulsory subjects (English should be included)
        $compulsorySubjects = Subject::whereIn('code', ['ENG', 'ENGLISH'])
                                    ->active()
                                    ->get();
        
        return view('admin.exams.create', compact('subjects', 'compulsorySubjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate exam data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:exams,code',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:full_jamb,subject_test,topic_test,mixed',
            'duration_minutes' => 'required|integer|min:10|max:300',
            'total_questions' => 'required|integer|min:1|max:200',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:0',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            
            // Subjects validation
            'subjects' => 'required|array|min:1',
            'subjects.*.subject_id' => 'required|exists:subjects,id',
            'subjects.*.question_count' => 'required|integer|min:1',
            'subjects.*.topic_ids' => 'nullable|array',
            'subjects.*.topic_ids.*' => 'exists:topics,id',
            'subjects.*.difficulty_distribution.easy' => 'nullable|integer|min:0|max:100',
            'subjects.*.difficulty_distribution.medium' => 'nullable|integer|min:0|max:100',
            'subjects.*.difficulty_distribution.hard' => 'nullable|integer|min:0|max:100',
        ], [
            'subjects.required' => 'Please add at least one subject to the exam.',
            'subjects.*.question_count.required' => 'Question count is required for each subject.',
            'code.unique' => 'This exam code is already in use. Please choose a different code.',
        ]);
        
        // Additional validation for JAMB exams
        if ($request->type === 'full_jamb') {
            $validator->after(function($validator) use ($request) {
                $totalQuestions = array_sum(array_column($request->subjects, 'question_count'));
                if ($totalQuestions !== 180) {
                    $validator->errors()->add(
                        'total_questions', 
                        'JAMB exams must have exactly 180 questions total.'
                    );
                }
                
                // Check for English subject
                $englishSubjects = array_filter($request->subjects, function($subject) {
                    $subjectModel = Subject::find($subject['subject_id']);
                    return $subjectModel && in_array($subjectModel->code, ['ENG', 'ENGLISH']);
                });
                
                if (empty($englishSubjects)) {
                    $validator->errors()->add(
                        'subjects', 
                        'JAMB exams must include English Language.'
                    );
                }
            });
        }
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Create the exam
            $exam = Exam::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'type' => $request->type,
                'duration_minutes' => $request->duration_minutes,
                'total_questions' => $request->total_questions,
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'shuffle_questions' => $request->boolean('shuffle_questions'),
                'shuffle_options' => $request->boolean('shuffle_options'),
                'show_results_immediately' => $request->boolean('show_results_immediately'),
                'is_active' => $request->boolean('is_active'),
                'is_published' => $request->boolean('is_published'),
            ]);
            
            // Attach subjects with their configuration
            foreach ($request->subjects as $subjectData) {
                $difficultyDistribution = isset($subjectData['difficulty_distribution']) 
                    ? json_encode($subjectData['difficulty_distribution'])
                    : null;
                
                $topicIds = isset($subjectData['topic_ids']) && !empty($subjectData['topic_ids'])
                    ? json_encode($subjectData['topic_ids'])
                    : null;
                
                $exam->subjects()->attach($subjectData['subject_id'], [
                    'question_count' => $subjectData['question_count'],
                    'topic_ids' => $topicIds,
                    'difficulty_distribution' => $difficultyDistribution,
                ]);
            }
            
            // Generate exam preview/questions if requested
            if ($request->has('generate_preview')) {
                $this->generateExamPreview($exam);
            }
            
            DB::commit();
            
            return redirect()->route('admin.exams.show', $exam)
                             ->with('success', 'Exam created successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error creating exam: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exam = Exam::with(['subjects' => function($query) {
            $query->withPivot(['question_count', 'topic_ids', 'difficulty_distribution']);
        }, 'attempts' => function($query) {
            $query->latest()->limit(10);
        }, 'results' => function($query) {
            $query->latest()->limit(5);
        }])
        ->withCount(['attempts', 'results', 'subjects'])
        ->findOrFail($id);
        
        // Calculate subject breakdown
        $subjectBreakdown = [];
        foreach ($exam->subjects as $subject) {
            $subjectBreakdown[] = [
                'id' => $subject->id,
                'name' => $subject->name,
                'question_count' => $subject->pivot->question_count,
                'percentage' => ($subject->pivot->question_count / $exam->total_questions) * 100,
            ];
        }
        
        // Calculate statistics
        $completedAttempts = $exam->attempts()->whereIn('status', ['completed', 'submitted'])->get();
        $averageScore = $completedAttempts->avg('percentage') ?? 0;
        $passedAttempts = $completedAttempts->where('is_passed', true)->count();
        $totalCompletedAttempts = $completedAttempts->count();
        
        $stats = [
            'total_attempts' => $exam->attempts_count,
            'completed_attempts' => $totalCompletedAttempts,
            'average_score' => $averageScore,
            'pass_rate' => $totalCompletedAttempts > 0 ? ($passedAttempts / $totalCompletedAttempts) * 100 : 0,
            'top_score' => $completedAttempts->max('percentage') ?? 0,
            'average_time' => $completedAttempts->avg(function($attempt) {
                return $attempt->time_spent;
            }) ?? 0,
        ];
        
        // Get recent attempts
        $recentAttempts = $exam->attempts()
                              ->with('user')
                              ->latest()
                              ->limit(5)
                              ->get();
        
        // Get top performers
        $topPerformers = $exam->results()
                             ->with('user')
                             ->orderByDesc('percentage')
                             ->limit(5)
                             ->get();
        
        return view('admin.exams.show', compact(
            'exam', 
            'subjectBreakdown', 
            'stats', 
            'recentAttempts', 
            'topPerformers'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $exam = Exam::with(['subjects' => function($query) {
            $query->withPivot(['question_count', 'topic_ids', 'difficulty_distribution']);
        }])->findOrFail($id);
        
        // Get all active subjects for selection
        $allSubjects = Subject::active()->ordered()->get();
        
        // Get English subjects
        $englishSubjects = Subject::whereIn('code', ['ENG', 'ENGLISH'])
                                ->active()
                                ->ordered()
                                ->get();
        
        // Get other subjects (non-English)
        $otherSubjects = Subject::whereNotIn('code', ['ENG', 'ENGLISH'])
                               ->active()
                               ->ordered()
                               ->get();
        
        // Separate English and other subjects from the exam
        $englishSubject = null;
        $otherExamSubjects = [];
        
        foreach ($exam->subjects as $subject) {
            if (in_array($subject->code, ['ENG', 'ENGLISH'])) {
                $englishSubject = $subject;
            } else {
                $otherExamSubjects[] = $subject;
            }
        }
        
        // Decode JSON fields for editing
        foreach ($exam->subjects as $subject) {
            if ($subject->pivot->topic_ids) {
                $subject->pivot->topic_ids = json_decode($subject->pivot->topic_ids, true);
            }
            if ($subject->pivot->difficulty_distribution) {
                $subject->pivot->difficulty_distribution = json_decode($subject->pivot->difficulty_distribution, true);
            }
        }
        
        return view('admin.exams.edit', compact(
            'exam', 
            'allSubjects',
            'englishSubjects', 
            'englishSubject',
            'otherSubjects', 
            'otherExamSubjects'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exam = Exam::findOrFail($id);
        
        // Prepare the subjects array from the form data
        $subjects = [];
        
        // Add English subject
        if ($request->has('english_subject_id') && $request->english_subject_id) {
            $subjects[] = [
                'subject_id' => $request->english_subject_id,
                'question_count' => $request->english_question_count ?? 60,
            ];
        }
        
        // Add other subjects
        if ($request->has('other_subjects')) {
            foreach ($request->other_subjects as $otherSubject) {
                if (!empty($otherSubject['subject_id'])) {
                    $subjects[] = [
                        'subject_id' => $otherSubject['subject_id'],
                        'question_count' => $otherSubject['question_count'] ?? 40,
                    ];
                }
            }
        }
        
        // Add subjects to request for validation
        $request->merge(['subjects' => $subjects]);
        
        // Calculate total questions
        $totalQuestions = array_sum(array_column($subjects, 'question_count'));
        $request->merge(['total_questions' => $totalQuestions]);
        
        // Validate exam data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:exams,code,' . $id,
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:full_jamb,subject_test,topic_test,mixed',
            'duration_minutes' => 'required|integer|min:10|max:300',
            'total_questions' => 'required|integer|min:1|max:200',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:0',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            
            // Subjects validation
            'subjects' => 'required|array|min:4', // JAMB needs 4 subjects
            'subjects.*.subject_id' => 'required|exists:subjects,id',
            'subjects.*.question_count' => 'required|integer|min:1',
        ], [
            'subjects.required' => 'Please add all 4 subjects (English + 3 other subjects).',
            'subjects.min' => 'JAMB exams must have exactly 4 subjects (English + 3 other subjects).',
            'subjects.*.question_count.required' => 'Question count is required for each subject.',
            'code.unique' => 'This exam code is already in use. Please choose a different code.',
        ]);
        
        // Additional validation for JAMB exams
        if ($request->type === 'full_jamb') {
            $validator->after(function($validator) use ($request, $totalQuestions) {
                // Check total questions
                if ($totalQuestions !== 180) {
                    $validator->errors()->add(
                        'total_questions', 
                        'JAMB exams must have exactly 180 questions total.'
                    );
                }
                
                // Check for exactly 4 subjects
                if (count($request->subjects) !== 4) {
                    $validator->errors()->add(
                        'subjects', 
                        'JAMB exams must have exactly 4 subjects (English + 3 other subjects).'
                    );
                }
                
                // Check for English subject
                $hasEnglish = false;
                foreach ($request->subjects as $subject) {
                    $subjectModel = Subject::find($subject['subject_id']);
                    if ($subjectModel && in_array($subjectModel->code, ['ENG', 'ENGLISH'])) {
                        $hasEnglish = true;
                        break;
                    }
                }
                
                if (!$hasEnglish) {
                    $validator->errors()->add(
                        'english_subject_id', 
                        'JAMB exams must include English Language.'
                    );
                }
            });
        }
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Update the exam
            $exam->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'type' => $request->type,
                'duration_minutes' => $request->duration_minutes,
                'total_questions' => $totalQuestions,
                'passing_score' => $request->passing_score,
                'max_attempts' => $request->max_attempts,
                'shuffle_questions' => $request->boolean('shuffle_questions'),
                'shuffle_options' => $request->boolean('shuffle_options'),
                'show_results_immediately' => $request->boolean('show_results_immediately'),
                'is_active' => $request->boolean('is_active'),
                'is_published' => $request->boolean('is_published'),
            ]);
            
            // Sync subjects with their configuration
            $subjectData = [];
            foreach ($request->subjects as $subjectConfig) {
                $subjectData[$subjectConfig['subject_id']] = [
                    'question_count' => $subjectConfig['question_count'],
                    'topic_ids' => null,
                    'difficulty_distribution' => null,
                ];
            }
            
            $exam->subjects()->sync($subjectData);
            
            DB::commit();
            
            return redirect()->route('admin.exams.show', $exam)
                             ->with('success', 'Exam updated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error updating exam: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Check if exam has attempts
            if ($exam->attempts()->exists()) {
                return redirect()->back()
                               ->with('error', 'Cannot delete exam that has attempts. Delete attempts first or deactivate the exam.');
            }
            
            // Detach subjects first
            $exam->subjects()->detach();
            
            // Delete the exam
            $exam->delete();
            
            DB::commit();
            
            return redirect()->route('admin.exams.index')
                             ->with('success', 'Exam deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error deleting exam: ' . $e->getMessage());
        }
    }

    /**
     * Publish an exam.
     */
    public function publish($id)
    {
        $exam = Exam::findOrFail($id);
        
        // Validate that exam has subjects
        if ($exam->subjects()->count() < 4) {
            return redirect()->back()
                ->with('error', 'Exam must have 4 subjects (English + 3) to be published.');
        }
        
        // Validate that there are enough questions
        foreach ($exam->subjects as $subject) {
            $requiredCount = $subject->pivot->question_count;
            $availableCount = $subject->questions()->count();
            
            if ($availableCount < $requiredCount) {
                return redirect()->back()
                    ->with('error', "Not enough questions in {$subject->name}. Required: {$requiredCount}, Available: {$availableCount}");
            }
        }
        
        $exam->update(['is_published' => true]);
        
        return redirect()->back()
            ->with('success', 'Exam published successfully! Students can now attempt it.');
    }

    /**
     * Unpublish an exam.
     */
    public function unpublish($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->update(['is_published' => false]);
        
        return redirect()->back()
            ->with('success', 'Exam unpublished successfully!');
    }

    /**
     * Duplicate an exam.
     */
    public function duplicate(string $id)
    {
        $originalExam = Exam::with(['subjects' => function($query) {
            $query->withPivot(['question_count', 'topic_ids', 'difficulty_distribution']);
        }])->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Create new exam with "(Copy)" suffix
            $newExam = $originalExam->replicate();
            $newExam->name = $originalExam->name . ' (Copy)';
            $newExam->code = $originalExam->code . '-COPY-' . time();
            $newExam->is_published = false; // Keep copy as draft
            $newExam->save();
            
            // Duplicate subject configurations
            foreach ($originalExam->subjects as $subject) {
                $newExam->subjects()->attach($subject->id, [
                    'question_count' => $subject->pivot->question_count,
                    'topic_ids' => $subject->pivot->topic_ids,
                    'difficulty_distribution' => $subject->pivot->difficulty_distribution,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.exams.edit', $newExam)
                             ->with('success', 'Exam duplicated successfully. You can now edit the copy.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error duplicating exam: ' . $e->getMessage());
        }
    }

    /**
     * Preview exam as student would see it.
     */
    public function preview($id)
    {
        $exam = Exam::with(['subjects' => function($query) {
            $query->withPivot(['question_count', 'topic_ids', 'difficulty_distribution']);
        }])->findOrFail($id);
        
        // Generate preview questions
        $previewQuestions = $this->generatePreviewQuestions($exam, 5); // Get 5 sample questions per subject
        
        return view('admin.exams.preview', compact('exam', 'previewQuestions'));
    }

    /**
     * Show exam statistics.
     */
    public function stats($id)
    {
        $exam = Exam::findOrFail($id);
        
        // Get completion statistics
        $completedAttempts = $exam->attempts()->whereIn('status', ['completed', 'submitted'])->get();
        $averageTimeSpent = $completedAttempts->avg(function($attempt) {
            return $attempt->time_spent;
        });
        
        $stats = [
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $completedAttempts->count(),
            'in_progress_attempts' => $exam->attempts()->where('status', 'in_progress')->count(),
            'average_score' => $completedAttempts->avg('percentage') ?? 0,
            'average_time_spent' => $averageTimeSpent ?? 0,
            'pass_rate' => $completedAttempts->where('is_passed', true)->count() / max($completedAttempts->count(), 1) * 100,
        ];
        
        // Get score distribution
        $scoreDistribution = DB::table('exam_attempts')
            ->selectRaw('
                CASE 
                    WHEN percentage >= 75 THEN "A (75-100%)"
                    WHEN percentage >= 60 THEN "B (60-74%)"
                    WHEN percentage >= 50 THEN "C (50-59%)"
                    WHEN percentage >= 40 THEN "D (40-49%)"
                    ELSE "F (Below 40%)"
                END as grade_range,
                COUNT(*) as count
            ')
            ->where('exam_id', $id)
            ->whereIn('status', ['completed', 'submitted'])
            ->groupBy('grade_range')
            ->orderByRaw('MIN(percentage) DESC')
            ->get();
        
        // Get recent attempts
        $recentAttempts = $exam->attempts()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get subject-wise performance
        $subjectPerformance = [];
        foreach ($exam->subjects as $subject) {
            // Calculate subject performance from results
            $subjectResults = Result::where('exam_id', $id)
                ->get()
                ->pluck('subject_breakdown')
                ->filter()
                ->map(function($breakdown) use ($subject) {
                    return collect($breakdown)->firstWhere('name', $subject->name);
                })
                ->filter();
            
            if ($subjectResults->count() > 0) {
                $avgPercentage = $subjectResults->avg('percentage') ?? 0;
                $subjectPerformance[] = [
                    'name' => $subject->name,
                    'average_score' => $avgPercentage,
                    'question_count' => $subject->pivot->question_count,
                ];
            }
        }
        
        return view('admin.exams.stats', compact('exam', 'stats', 'scoreDistribution', 'recentAttempts', 'subjectPerformance'));
    }

    /**
     * Export exam data.
     */
    public function export($id)
    {
        $exam = Exam::with(['subjects' => function($query) {
            $query->withPivot(['question_count', 'topic_ids', 'difficulty_distribution']);
        }])->findOrFail($id);
        
        // Generate CSV content
        $csvContent = "Exam Name,Exam Code,Description,Type,Duration (min),Total Questions,Passing Score\n";
        $csvContent .= "\"{$exam->name}\",\"{$exam->code}\",\"{$exam->description}\",{$exam->type},{$exam->duration_minutes},{$exam->total_questions},{$exam->passing_score}\n\n";
        
        $csvContent .= "Subjects Configuration\n";
        $csvContent .= "Subject,Question Count,Topics,Difficulty Distribution\n";
        
        foreach ($exam->subjects as $subject) {
            $topicNames = 'All';
            if ($subject->pivot->topic_ids) {
                $topicIds = json_decode($subject->pivot->topic_ids, true);
                $topics = \App\Models\Topic::whereIn('id', $topicIds)->pluck('name')->toArray();
                $topicNames = implode(', ', $topics);
            }
            
            $difficulty = 'Default';
            if ($subject->pivot->difficulty_distribution) {
                $dist = json_decode($subject->pivot->difficulty_distribution, true);
                $difficulty = "Easy: {$dist['easy']}%, Medium: {$dist['medium']}%, Hard: {$dist['hard']}%";
            }
            
            $csvContent .= "\"{$subject->name}\",{$subject->pivot->question_count},\"{$topicNames}\",\"{$difficulty}\"\n";
        }
        
        $filename = "exam-{$exam->code}-" . date('Y-m-d') . ".csv";
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Import exams from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);
        
        try {
            // Implementation for importing exams from CSV
            // You can use Laravel Excel package or manual CSV parsing
            
            return redirect()->route('admin.exams.index')
                ->with('success', 'Exams imported successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to import exams: ' . $e->getMessage());
        }
    }

    /**
     * Export all exams.
     */
    public function exportAll(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Implementation for exporting all exams
        // You can use Laravel Excel package
        
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Get questions for a subject based on configuration.
     */
      private function getQuestionsForSubject($subjectId, $questionCount, $topicIds = null, $difficultyDistribution = null)
{
    $query = Question::where('subject_id', $subjectId)
                    ->where('is_active', true)
                    // CRITICAL FIX: Eager load options BEFORE retrieving questions
                    ->with(['options' => function($query) {
                        $query->orderBy('option_letter');
                    }]);
    
    // Filter by topics if specified
    if ($topicIds && is_array($topicIds)) {
        $query->whereIn('topic_id', $topicIds);
    }
    
    // Apply difficulty distribution if specified
    if ($difficultyDistribution && is_array($difficultyDistribution)) {
        $totalPercentage = array_sum($difficultyDistribution);
        if ($totalPercentage > 0) {
            $easyCount = round(($difficultyDistribution['easy'] / $totalPercentage) * $questionCount);
            $mediumCount = round(($difficultyDistribution['medium'] / $totalPercentage) * $questionCount);
            $hardCount = $questionCount - $easyCount - $mediumCount;
            
            // This would need more complex logic for actual implementation
        }
    }
    
    // Get questions with options already loaded
    return $query->inRandomOrder()
                ->limit($questionCount)
                ->get();
}

    /**
     * Generate preview questions for exam.
     */
    private function generatePreviewQuestions($exam, $questionsPerSubject = 5)
    {
        $previewQuestions = [];
        
        foreach ($exam->subjects as $subject) {
            $topicIds = $subject->pivot->topic_ids 
                ? json_decode($subject->pivot->topic_ids, true) 
                : null;
            
            $difficultyDistribution = $subject->pivot->difficulty_distribution
                ? json_decode($subject->pivot->difficulty_distribution, true)
                : null;
            
            $questions = $this->getQuestionsForSubject(
                $subject->id,
                min($questionsPerSubject, $subject->pivot->question_count),
                $topicIds,
                $difficultyDistribution
            );
            
            $previewQuestions[$subject->name] = $questions;
        }
        
        return $previewQuestions;
    }

    /**
     * Get exam statistics for dashboard.
     */
    public function statistics(string $id)
    {
        $exam = Exam::findOrFail($id);
        
        // Get completion statistics
        $completedAttempts = $exam->attempts()->whereIn('status', ['completed', 'submitted'])->get();
        $averageTimeSpent = $completedAttempts->avg(function($attempt) {
            return $attempt->time_spent;
        });
        
        $statistics = [
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $completedAttempts->count(),
            'in_progress_attempts' => $exam->attempts()->where('status', 'in_progress')->count(),
            'average_score' => $completedAttempts->avg('percentage') ?? 0,
            'pass_rate' => $completedAttempts->where('is_passed', true)->count() / max($exam->attempts()->count(), 1) * 100,
            'average_time_spent' => $averageTimeSpent ?? 0,
            'top_score' => $exam->attempts()->max('percentage') ?? 0,
            'lowest_score' => $exam->attempts()->min('percentage') ?? 0,
        ];
        
        // Get score distribution
        $scoreDistribution = [
            '0-20' => $exam->attempts()->whereBetween('percentage', [0, 20])->count(),
            '21-40' => $exam->attempts()->whereBetween('percentage', [21, 40])->count(),
            '41-60' => $exam->attempts()->whereBetween('percentage', [41, 60])->count(),
            '61-80' => $exam->attempts()->whereBetween('percentage', [61, 80])->count(),
            '81-100' => $exam->attempts()->whereBetween('percentage', [81, 100])->count(),
        ];
        
        // Get daily attempts for last 30 days
        $dailyAttempts = $exam->attempts()
            ->selectRaw('DATE(started_at) as date, COUNT(*) as count')
            ->where('started_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json([
            'statistics' => $statistics,
            'score_distribution' => $scoreDistribution,
            'daily_attempts' => $dailyAttempts,
        ]);
    }

    /**
     * Generate exam preview (helper method).
     */
    private function generateExamPreview($exam)
    {
        // This method can be used to generate sample questions for preview
        // Implementation depends on your specific requirements
        return true;
    }
}