<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserExamController extends Controller
{
    /**
     * Display a listing of available exams
     */
    public function index()
    {
        $exams = Exam::where('is_published', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('user.exams.index', compact('exams'));
    }
    
    /**
     * Display exam details
     */
    public function show(Exam $exam)
    {
        // Check if exam is published
        if (!$exam->is_published || !$exam->is_active) {
            abort(404, 'Exam not available');
        }
        
        // Get subjects for this exam
        $subjects = $exam->subjects()->with('subject')->get();
        
        return view('user.exams.show', compact('exam', 'subjects'));
    }
    
    /**
     * Show exam instructions
     */
    public function instructions(Exam $exam)
    {
        if (!$exam->is_published || !$exam->is_active) {
            abort(404, 'Exam not available');
        }
        
        // Check if user has reached max attempts
        $userAttempts = ExamAttempt::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['completed', 'submitted'])
            ->count();
        
        if ($exam->max_attempts > 0 && $userAttempts >= $exam->max_attempts) {
            return redirect()->route('user.exams.index')
                ->with('error', 'You have reached the maximum number of attempts for this exam.');
        }
        
        return view('user.exams.instructions', compact('exam'));
    }
    
    /**
     * Start a new exam attempt
     */
    public function start(Request $request, Exam $exam)
    {
        if (!$exam->is_published || !$exam->is_active) {
            abort(404, 'Exam not available');
        }
        
        // Check for existing in-progress attempt
        $existingAttempt = ExamAttempt::where('user_id', Auth::id())
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->first();
        
        if ($existingAttempt) {
            return redirect()->route('user.exams.take', $exam->id);
        }
        
        // Create new exam attempt
        $attempt = ExamAttempt::create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'started_at' => Carbon::now(),
            'status' => 'in_progress',
            'total_questions' => $exam->total_questions,
            'time_remaining' => $exam->duration_minutes * 60,
        ]);
        
        // Generate questions order
        $questions = $this->getExamQuestions($exam);
        $attempt->questions_order = json_encode($questions->pluck('id')->toArray());
        $attempt->save();
        
        return redirect()->route('user.exams.take', $exam->id);
    }
    
    /**
     * Take the exam
     */
    public function take(Exam $exam)
    {
        $user = Auth::user();
        
        // Get or create exam attempt
        $attempt = ExamAttempt::firstOrCreate(
            [
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'status' => 'in_progress'
            ],
            [
                'started_at' => Carbon::now(),
                'total_questions' => $exam->total_questions,
                'time_remaining' => $exam->duration_minutes * 60,
                'questions_order' => $this->generateQuestionsOrder($exam),
            ]
        );
        
        // If attempt was just created or has no questions order, generate it
        if (!$attempt->questions_order) {
            $attempt->questions_order = $this->generateQuestionsOrder($exam);
            $attempt->save();
        }
        
        // Get questions for this exam
        $questions = $this->getExamQuestions($exam, $attempt);
        $questionsOrder = json_decode($attempt->questions_order, true);
        
        // Get current question index from request or default to 0
        $currentQuestionIndex = request()->get('question', 0);
        if ($currentQuestionIndex >= count($questionsOrder)) {
            $currentQuestionIndex = 0;
        }
        
        $currentQuestionId = $questionsOrder[$currentQuestionIndex] ?? null;
        $currentQuestion = $questions->firstWhere('id', $currentQuestionId);
        
        if (!$currentQuestion) {
            abort(404, 'Question not found');
        }
        
        // Get user answers for this attempt
        $userAnswers = $attempt->answers()
            ->pluck('selected_option', 'question_id')
            ->toArray();
        
        // Get marked questions
        $markedQuestions = $attempt->answers()
            ->where('marked_for_review', true)
            ->pluck('question_id')
            ->toArray();
        
        // Calculate stats
        $totalQuestions = count($questionsOrder);
        $answeredCount = count($userAnswers);
        $markedCount = count($markedQuestions);
        
        // Get subjects for this exam
        $subjects = $exam->subjects()->with('subject')->get();
        
        return view('user.exams.take', compact(
            'exam',
            'attempt',
            'questions',
            'currentQuestion',
            'currentQuestionIndex',
            'userAnswers',
            'markedQuestions',
            'totalQuestions',
            'answeredCount',
            'markedCount',
            'subjects'
        ));
    }
    
    /**
     * Save answer (AJAX)
     */
    public function saveAnswer(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();
        
        // Validate request
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option' => 'required|in:A,B,C,D,E',
            'time_spent' => 'nullable|integer|min:0',
            'marked' => 'nullable|boolean',
        ]);
        
        // Get the question to check if option exists
        $question = Question::findOrFail($validated['question_id']);
        
        // Save or update answer
        $answer = $attempt->answers()->updateOrCreate(
            ['question_id' => $validated['question_id']],
            [
                'selected_option' => $validated['selected_option'],
                'time_spent_seconds' => $validated['time_spent'] ?? 0,
                'marked_for_review' => $validated['marked'] ?? false,
                'answered_at' => Carbon::now(),
            ]
        );
        
        // Update attempt statistics
        $attempt->questions_answered = $attempt->answers()->count();
        $attempt->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Answer saved successfully',
            'answered_count' => $attempt->questions_answered,
            'total_questions' => $attempt->total_questions,
        ]);
    }
    
    /**
     * Get next/previous question (AJAX)
     */
    public function getQuestion(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();
        
        $direction = $request->get('direction', 'next');
        $currentIndex = $request->get('current_index', 0);
        
        $questionsOrder = json_decode($attempt->questions_order, true);
        $totalQuestions = count($questionsOrder);
        
        // Calculate new index
        if ($direction === 'next') {
            $newIndex = ($currentIndex + 1) % $totalQuestions;
        } else {
            $newIndex = ($currentIndex - 1 + $totalQuestions) % $totalQuestions;
        }
        
        $questionId = $questionsOrder[$newIndex];
        $question = Question::with(['options' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($questionId);
        
        // Get user answer for this question
        $userAnswer = $attempt->answers()
            ->where('question_id', $questionId)
            ->first();
        
        // Check if marked for review
        $isMarked = $userAnswer ? $userAnswer->marked_for_review : false;
        
        return response()->json([
            'success' => true,
            'question' => [
                'id' => $question->id,
                'number' => $newIndex + 1,
                'subject' => $question->subject->name ?? 'General',
                'text' => $question->question_text,
                'image_path' => $question->image_path,
                'options' => $question->options->map(function($option) {
                    return [
                        'letter' => $option->option_letter,
                        'text' => $option->option_text,
                        'image_path' => $option->image_path,
                    ];
                }),
            ],
            'current_index' => $newIndex,
            'user_answer' => $userAnswer ? $userAnswer->selected_option : null,
            'is_marked' => $isMarked,
            'total_questions' => $totalQuestions,
        ]);
    }
    
    /**
     * Save time remaining (AJAX)
     */
    public function saveTime(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();
        
        $validated = $request->validate([
            'time_remaining' => 'required|integer|min:0',
        ]);
        
        $attempt->time_remaining = $validated['time_remaining'];
        $attempt->save();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Submit exam
     */
    public function submit(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $attempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();
        
        // Calculate score
        $this->calculateScore($attempt);
        
        // Update attempt status
        $attempt->status = 'submitted';
        $attempt->completed_at = Carbon::now();
        $attempt->save();
        
        // Create result record
        $this->createResult($attempt);
        
        return redirect()->route('user.results.show', $attempt->id)
            ->with('success', 'Exam submitted successfully! Your results are ready.');
    }
    
    /**
     * Generate questions order for exam
     */
    private function generateQuestionsOrder(Exam $exam)
    {
        $questions = Question::whereHas('examSubjects', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })
            ->where('is_active', true)
            ->pluck('id');
        
        // Shuffle if enabled
        if ($exam->shuffle_questions) {
            $questions = $questions->shuffle();
        }
        
        // Limit to total questions
        $questions = $questions->take($exam->total_questions);
        
        return json_encode($questions->values()->toArray());
    }
    
    /**
     * Get questions for exam with options
     */
    private function getExamQuestions(Exam $exam, ExamAttempt $attempt = null)
    {
        if ($attempt && $attempt->questions_order) {
            $questionsOrder = json_decode($attempt->questions_order, true);
            
            return Question::with(['options' => function ($query) use ($exam) {
                    if ($exam->shuffle_options) {
                        $query->inRandomOrder();
                    } else {
                        $query->orderBy('order');
                    }
                }, 'subject'])
                ->whereIn('id', $questionsOrder)
                ->get()
                ->keyBy('id');
        }
        
        // Fallback if no attempt
        $questions = Question::whereHas('examSubjects', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })
            ->with(['options' => function ($query) use ($exam) {
                if ($exam->shuffle_options) {
                    $query->inRandomOrder();
                } else {
                    $query->orderBy('order');
                }
            }, 'subject'])
            ->where('is_active', true);
        
        if ($exam->shuffle_questions) {
            $questions = $questions->inRandomOrder();
        }
        
        return $questions->limit($exam->total_questions)->get()->keyBy('id');
    }
    
    /**
     * Calculate score for attempt
     */
    private function calculateScore(ExamAttempt $attempt)
    {
        $answers = $attempt->answers()->with('question.options')->get();
        
        $correctCount = 0;
        $totalMarks = 0;
        $obtainedMarks = 0;
        
        foreach ($answers as $answer) {
            $correctOption = $answer->question->options->where('is_correct', true)->first();
            
            if ($correctOption && $correctOption->option_letter == $answer->selected_option) {
                $answer->is_correct = true;
                $correctCount++;
                $obtainedMarks += $answer->question->marks;
            } else {
                $answer->is_correct = false;
            }
            
            $answer->save();
            $totalMarks += $answer->question->marks;
        }
        
        // Update attempt
        $attempt->correct_answers = $correctCount;
        $attempt->wrong_answers = $attempt->questions_answered - $correctCount;
        $attempt->score = $obtainedMarks;
        $attempt->percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        $attempt->is_passed = $attempt->percentage >= $attempt->exam->passing_score;
        $attempt->grade = $this->calculateGrade($attempt->percentage);
    }
    
    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 75) return 'A';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
    
    /**
     * Create result record
     */
    private function createResult(ExamAttempt $attempt)
    {
        // Calculate subject-wise breakdown
        $subjectBreakdown = $this->calculateSubjectBreakdown($attempt);
        
        // Calculate time spent
        $startTime = Carbon::parse($attempt->started_at);
        $endTime = Carbon::parse($attempt->completed_at);
        $timeSpentSeconds = $endTime->diffInSeconds($startTime);
        
        // Create result
        \App\Models\Result::create([
            'user_id' => $attempt->user_id,
            'exam_id' => $attempt->exam_id,
            'exam_attempt_id' => $attempt->id,
            'total_questions' => $attempt->total_questions,
            'questions_answered' => $attempt->questions_answered,
            'correct_answers' => $attempt->correct_answers,
            'wrong_answers' => $attempt->wrong_answers,
            'score' => $attempt->score,
            'percentage' => $attempt->percentage,
            'grade' => $attempt->grade,
            'is_passed' => $attempt->is_passed,
            'time_spent_seconds' => $timeSpentSeconds,
            'average_time_per_question' => $attempt->questions_answered > 0 
                ? round($timeSpentSeconds / $attempt->questions_answered, 2) 
                : 0,
            'subject_breakdown' => json_encode($subjectBreakdown),
            'completion_status' => 'submitted',
            'exam_date' => $attempt->completed_at,
        ]);
    }
    
    /**
     * Calculate subject-wise breakdown
     */
    private function calculateSubjectBreakdown(ExamAttempt $attempt)
    {
        $answers = $attempt->answers()->with('question.subject')->get();
        
        $breakdown = [];
        
        foreach ($answers as $answer) {
            $subjectName = $answer->question->subject->name ?? 'Unknown';
            
            if (!isset($breakdown[$subjectName])) {
                $breakdown[$subjectName] = [
                    'total' => 0,
                    'correct' => 0,
                    'incorrect' => 0,
                    'score' => 0,
                    'percentage' => 0,
                ];
            }
            
            $breakdown[$subjectName]['total']++;
            
            if ($answer->is_correct) {
                $breakdown[$subjectName]['correct']++;
                $breakdown[$subjectName]['score'] += $answer->question->marks;
            } else {
                $breakdown[$subjectName]['incorrect']++;
            }
        }
        
        // Calculate percentages
        foreach ($breakdown as $subject => &$data) {
            $data['percentage'] = $data['total'] > 0 
                ? round(($data['correct'] / $data['total']) * 100, 2) 
                : 0;
        }
        
        return $breakdown;
    }
}