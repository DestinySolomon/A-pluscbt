<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $subjectId = $request->get('subject_id');
        $topicId = $request->get('topic_id');
        $difficulty = $request->get('difficulty');
        $status = $request->get('status');
        $search = $request->get('search');
        
        // Start query with relationships
        $query = Question::with(['subject', 'topic', 'options'])
                         ->withCount('options');
        
        // Apply filters
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
            
            // If subject selected, get topics for this subject
            if ($topicId) {
                $query->where('topic_id', $topicId);
            }
        }
        
        if ($difficulty) {
            $query->where('difficulty', $difficulty);
        }
        
        if ($status !== null) {
            $query->where('is_active', $status == 'active');
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('explanation', 'like', "%{$search}%")
                  ->orWhereHas('options', function($q) use ($search) {
                      $q->where('option_text', 'like', "%{$search}%");
                  });
            });
        }
        
        // Order and paginate
        $questions = $query->latest()
                          ->paginate(15)
                          ->withQueryString();
        
        // Get subjects and topics for filters
        $subjects = Subject::active()->ordered()->get();
        $topics = $subjectId ? Topic::where('subject_id', $subjectId)->active()->get() : collect();
        
        return view('admin.questions.index', compact('questions', 'subjects', 'topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.questions.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate main question data
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'question_text' => 'required|string|min:10|max:5000',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|integer|min:1|max:10',
            'time_estimate' => 'required|integer|min:10|max:300',
            'explanation' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            
            // Options validation
            'options' => 'required|array|size:4',
            'options.*.text' => 'required|string|max:1000',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'correct_option' => 'required|in:0,1,2,3',
        ], [
            'options.size' => 'Exactly 4 options are required for JAMB questions.',
            'correct_option.required' => 'Please select the correct answer.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Handle question image upload
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }
            
            // Create the question
            $question = Question::create([
                'subject_id' => $request->subject_id,
                'topic_id' => $request->topic_id,
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'difficulty' => $request->difficulty,
                'marks' => $request->marks,
                'time_estimate' => $request->time_estimate,
                'explanation' => $request->explanation,
                'is_active' => $request->boolean('is_active'),
            ]);
            
            // Create options (A, B, C, D)
            $optionLetters = ['A', 'B', 'C', 'D'];
            foreach ($optionLetters as $index => $letter) {
                $optionImagePath = null;
                
                // Handle option image upload
                if ($request->hasFile("options.{$index}.image")) {
                    $optionImagePath = $request->file("options.{$index}.image")->store('options', 'public');
                }
                
                Option::create([
                    'question_id' => $question->id,
                    'option_letter' => $letter,
                    'option_text' => $request->input("options.{$index}.text"),
                    'image_path' => $optionImagePath,
                    'is_correct' => $request->correct_option == $index,
                    'order' => $index,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.questions.show', $question)
                             ->with('success', 'Question created successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded images if transaction fails
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            return redirect()->back()
                           ->with('error', 'Error creating question: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $question = Question::with(['subject', 'topic', 'options' => function($query) {
            $query->orderBy('option_letter');
        }])->findOrFail($id);
        
        // Calculate statistics
        $question->loadCount('options');
        
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $question = Question::with(['options' => function($query) {
            $query->orderBy('option_letter');
        }])->findOrFail($id);
        
        $subjects = Subject::active()->ordered()->get();
        $topics = Topic::where('subject_id', $question->subject_id)->active()->get();
        
        return view('admin.questions.edit', compact('question', 'subjects', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $question = Question::findOrFail($id);
        
        // Validate main question data
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'question_text' => 'required|string|min:10|max:5000',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'difficulty' => 'required|in:easy,medium,hard',
            'marks' => 'required|integer|min:1|max:10',
            'time_estimate' => 'required|integer|min:10|max:300',
            'explanation' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            
            // Options validation
            'options' => 'required|array|size:4',
            'options.*.text' => 'required|string|max:1000',
            'options.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'correct_option' => 'required|in:0,1,2,3',
        ], [
            'options.size' => 'Exactly 4 options are required for JAMB questions.',
            'correct_option.required' => 'Please select the correct answer.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        DB::beginTransaction();
        
        try {
            // Handle question image upload/update
            $imagePath = $question->image_path;
            if ($request->hasFile('question_image')) {
                // Delete old image if exists
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                // Upload new image
                $imagePath = $request->file('question_image')->store('questions', 'public');
            } elseif ($request->has('remove_question_image')) {
                // Remove image if checkbox checked
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            }
            
            // Update the question
            $question->update([
                'subject_id' => $request->subject_id,
                'topic_id' => $request->topic_id,
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'difficulty' => $request->difficulty,
                'marks' => $request->marks,
                'time_estimate' => $request->time_estimate,
                'explanation' => $request->explanation,
                'is_active' => $request->boolean('is_active'),
            ]);
            
            // Update options
            $optionLetters = ['A', 'B', 'C', 'D'];
            foreach ($optionLetters as $index => $letter) {
                $option = $question->options()->where('option_letter', $letter)->first();
                
                if ($option) {
                    $optionImagePath = $option->image_path;
                    
                    // Handle option image upload/update
                    if ($request->hasFile("options.{$index}.image")) {
                        // Delete old image if exists
                        if ($optionImagePath && Storage::disk('public')->exists($optionImagePath)) {
                            Storage::disk('public')->delete($optionImagePath);
                        }
                        // Upload new image
                        $optionImagePath = $request->file("options.{$index}.image")->store('options', 'public');
                    } elseif ($request->has("remove_option_image.{$index}")) {
                        // Remove image if checkbox checked
                        if ($optionImagePath && Storage::disk('public')->exists($optionImagePath)) {
                            Storage::disk('public')->delete($optionImagePath);
                        }
                        $optionImagePath = null;
                    }
                    
                    $option->update([
                        'option_text' => $request->input("options.{$index}.text"),
                        'image_path' => $optionImagePath,
                        'is_correct' => $request->correct_option == $index,
                        'order' => $index,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.questions.show', $question)
                             ->with('success', 'Question updated successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error updating question: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Question::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Delete question image if exists
            if ($question->image_path && Storage::disk('public')->exists($question->image_path)) {
                Storage::disk('public')->delete($question->image_path);
            }
            
            // Delete option images
            foreach ($question->options as $option) {
                if ($option->image_path && Storage::disk('public')->exists($option->image_path)) {
                    Storage::disk('public')->delete($option->image_path);
                }
            }
            
            // Delete the question (options will cascade delete)
            $question->delete();
            
            DB::commit();
            
            return redirect()->route('admin.questions.index')
                             ->with('success', 'Question deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error deleting question: ' . $e->getMessage());
        }
    }

    /**
     * Import questions from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:5120',
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
        ]);
        
        // Basic import implementation
        // For production, consider using maatwebsite/excel package
        
        return redirect()->route('admin.questions.index')
                         ->with('info', 'CSV import feature coming soon. For now, add questions manually.');
    }

    /**
     * Export questions to CSV.
     */
    public function export(Request $request)
    {
        $subjectId = $request->get('subject_id');
        $topicId = $request->get('topic_id');
        
        // Basic export implementation
        // For production, consider using maatwebsite/excel package
        
        return redirect()->route('admin.questions.index')
                         ->with('info', 'CSV export feature coming soon.');
    }

    /**
     * Get topics by subject for AJAX requests.
     */
    public function getTopicsBySubject($subjectId)
    {
        $topics = Topic::where('subject_id', $subjectId)
                      ->active()
                      ->orderBy('syllabus_order')
                      ->get(['id', 'name']);
        
        return response()->json($topics);
    }

    /**
     * Bulk actions (delete, activate, deactivate).
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);
        
        $action = $request->action;
        $questionIds = $request->question_ids;
        
        DB::beginTransaction();
        
        try {
            switch ($action) {
                case 'delete':
                    Question::whereIn('id', $questionIds)->delete();
                    $message = 'Selected questions deleted successfully.';
                    break;
                    
                case 'activate':
                    Question::whereIn('id', $questionIds)->update(['is_active' => true]);
                    $message = 'Selected questions activated successfully.';
                    break;
                    
                case 'deactivate':
                    Question::whereIn('id', $questionIds)->update(['is_active' => false]);
                    $message = 'Selected questions deactivated successfully.';
                    break;
            }
            
            DB::commit();
            
            return redirect()->route('admin.questions.index')
                             ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }
}