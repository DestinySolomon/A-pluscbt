<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttemptController extends Controller
{
    /**
     * Display a listing of exam attempts.
     */
    public function index(Request $request)
    {
        $query = ExamAttempt::with(['exam', 'user'])
            ->orderBy('created_at', 'desc');
        
        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        $attempts = $query->paginate(20);
        $exams = Exam::active()->published()->get();
        
        return view('admin.attempts.index', compact('attempts', 'exams'));
    }

    /**
     * Display the specified exam attempt.
     */
    public function show($id)
    {
        $attempt = ExamAttempt::with(['exam', 'user', 'answers.question', 'answers.selectedOption'])
            ->findOrFail($id);
        
        return view('admin.attempts.show', compact('attempt'));
    }

    /**
     * Remove the specified exam attempt.
     */
    public function destroy($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        $attempt->delete();
        
        return redirect()->route('admin.attempts.index')
            ->with('success', 'Exam attempt deleted successfully.');
    }

    /**
     * Reset an exam attempt (allow user to retake).
     */
    public function reset($id)
    {
        $attempt = ExamAttempt::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Delete all answers associated with this attempt
            $attempt->answers()->delete();
            
            // Reset attempt data
            $attempt->update([
                'status' => 'in_progress',
                'completed_at' => null,
                'questions_answered' => 0,
                'correct_answers' => 0,
                'wrong_answers' => 0,
                'score' => 0,
                'percentage' => 0,
                'grade' => null,
                'is_passed' => false,
                'time_remaining' => $attempt->exam->duration_minutes * 60,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.attempts.show', $attempt->id)
                ->with('success', 'Exam attempt reset successfully. User can now retake the exam.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to reset exam attempt: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete exam attempts.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'attempt_ids' => 'required|array',
            'attempt_ids.*' => 'exists:exam_attempts,id'
        ]);
        
        $deletedCount = ExamAttempt::whereIn('id', $request->attempt_ids)->delete();
        
        return redirect()->route('admin.attempts.index')
            ->with('success', "Successfully deleted {$deletedCount} exam attempts.");
    }
}