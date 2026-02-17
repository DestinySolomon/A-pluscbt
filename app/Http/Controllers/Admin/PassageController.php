<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Passage;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PassageController extends Controller
{
    /**
     * Display a listing of passages.
     */
    public function index(Request $request)
    {
        $subjectId = $request->get('subject_id');
        $status = $request->get('status');
        $search = $request->get('search');
        
        $query = Passage::with(['subject', 'topic'])->withCount('questions');
        
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($status !== null) {
            $query->where('is_active', $status == 'active');
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }
        
        $passages = $query->latest()->paginate(15)->withQueryString();
        
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.passages.index', compact('passages', 'subjects'));
    }

    /**
     * Show the form for creating a new passage.
     */
    public function create()
    {
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.passages.create', compact('subjects'));
    }

    /**
     * Store a newly created passage in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'instruction' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'passage_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $imagePath = null;
            if ($request->hasFile('passage_image')) {
                $imagePath = $request->file('passage_image')->store('passages', 'public');
            }

            $passage = Passage::create([
                'subject_id' => $request->subject_id,
                'topic_id' => $request->topic_id,
                'title' => $request->title,
                'content' => $request->content,
                'instruction' => $request->instruction,
                'source' => $request->source,
                'image_path' => $imagePath,
                'is_active' => $request->boolean('is_active', true),
                'metadata' => json_encode([
                    'word_count' => str_word_count(strip_tags($request->content)),
                    'created_by' => auth()->id(),
                ]),
            ]);

            DB::commit();

            return redirect()->route('admin.passages.show', $passage)
                ->with('success', 'Passage created successfully. You can now add questions to it.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()->back()
                ->with('error', 'Error creating passage: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified passage.
     */
    public function show(Passage $passage)
    {
        $passage->load(['subject', 'topic', 'questions' => function($query) {
            $query->with('options')
                  ->orderBy('question_number');
        }]);
        
        return view('admin.passages.show', compact('passage'));
    }

    /**
     * Show the form for editing the specified passage.
     */
    public function edit(Passage $passage)
    {
        $subjects = Subject::active()->ordered()->get();
        $topics = Topic::where('subject_id', $passage->subject_id)->active()->get();
        
        return view('admin.passages.edit', compact('passage', 'subjects', 'topics'));
    }

    /**
     * Update the specified passage in storage.
     */
    public function update(Request $request, Passage $passage)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'instruction' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'passage_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $imagePath = $passage->image_path;
            
            if ($request->hasFile('passage_image')) {
                // Delete old image
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                // Upload new image
                $imagePath = $request->file('passage_image')->store('passages', 'public');
            } elseif ($request->has('remove_image')) {
                // Remove image if checkbox checked
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            }

            $passage->update([
                'subject_id' => $request->subject_id,
                'topic_id' => $request->topic_id,
                'title' => $request->title,
                'content' => $request->content,
                'instruction' => $request->instruction,
                'source' => $request->source,
                'image_path' => $imagePath,
                'is_active' => $request->boolean('is_active', true),
                'metadata' => json_encode([
                    'word_count' => str_word_count(strip_tags($request->content)),
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]),
            ]);

            DB::commit();

            return redirect()->route('admin.passages.show', $passage)
                ->with('success', 'Passage updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error updating passage: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified passage from storage.
     */
    public function destroy(Passage $passage)
    {
        DB::beginTransaction();

        try {
            // Check if passage has questions
            if ($passage->questions()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete passage because it has ' . $passage->questions()->count() . ' questions attached. Delete the questions first.');
            }

            // Delete passage image
            if ($passage->image_path && Storage::disk('public')->exists($passage->image_path)) {
                Storage::disk('public')->delete($passage->image_path);
            }

            $passage->delete();

            DB::commit();

            return redirect()->route('admin.passages.index')
                ->with('success', 'Passage deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error deleting passage: ' . $e->getMessage());
        }
    }

    /**
     * Add question to passage.
     */
    public function addQuestion(Request $request, Passage $passage)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'question_number' => 'required|integer|min:1',
            'options' => 'required|array|size:5',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
            'correct_option' => 'required|in:A,B,C,D,E',
            'marks' => 'required|integer|min:1|max:10',
            'explanation' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Check if question number already exists
            $existingQuestion = $passage->questions()
                ->where('question_number', $request->question_number)
                ->first();

            if ($existingQuestion) {
                return redirect()->back()
                    ->with('error', 'Question number ' . $request->question_number . ' already exists in this passage.')
                    ->withInput();
            }

            // Create question
            $question = $passage->questions()->create([
                'subject_id' => $passage->subject_id,
                'topic_id' => $passage->topic_id,
                'question_text' => $request->question_text,
                'question_number' => $request->question_number,
                'marks' => $request->marks,
                'explanation' => $request->explanation,
                'is_active' => true,
            ]);

            // Create options
            $optionLetters = ['A', 'B', 'C', 'D', 'E'];
            foreach ($optionLetters as $index => $letter) {
                $question->options()->create([
                    'option_letter' => $letter,
                    'option_text' => $request->options[$index]['text'],
                    'is_correct' => $request->correct_option == $letter,
                    'order' => $index,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.passages.show', $passage)
                ->with('success', 'Question added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error adding question: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle passage status.
     */
    public function toggleStatus(Passage $passage)
    {
        $passage->is_active = !$passage->is_active;
        $passage->save();

        $status = $passage->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Passage {$status} successfully.");
    }

    /**
     * Get topics by subject for AJAX.
     */
    public function getTopicsBySubject($subjectId)
    {
        $topics = Topic::where('subject_id', $subjectId)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($topics);
    }

    /**
     * Get next available question number.
     */
    public function getNextQuestionNumber(Passage $passage)
    {
        $lastQuestion = $passage->questions()
            ->orderByDesc('question_number')
            ->first();

        $nextNumber = $lastQuestion ? $lastQuestion->question_number + 1 : 1;

        return response()->json(['next_number' => $nextNumber]);
    }

    /**
     * Bulk action on passages.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'passage_ids' => 'required|array',
            'passage_ids.*' => 'exists:passages,id',
        ]);

        $action = $request->action;
        $passageIds = $request->passage_ids;

        DB::beginTransaction();

        try {
            switch ($action) {
                case 'delete':
                    // Check if passages have questions
                    $passagesWithQuestions = Passage::whereIn('id', $passageIds)
                        ->withCount('questions')
                        ->get()
                        ->filter(function($passage) {
                            return $passage->questions_count > 0;
                        });

                    if ($passagesWithQuestions->count() > 0) {
                        $titles = $passagesWithQuestions->pluck('title')->implode(', ');
                        return redirect()->back()
                            ->with('error', "Cannot delete passages that have questions: {$titles}");
                    }

                    Passage::whereIn('id', $passageIds)->delete();
                    $message = 'Selected passages deleted successfully.';
                    break;

                case 'activate':
                    Passage::whereIn('id', $passageIds)->update(['is_active' => true]);
                    $message = 'Selected passages activated successfully.';
                    break;

                case 'deactivate':
                    Passage::whereIn('id', $passageIds)->update(['is_active' => false]);
                    $message = 'Selected passages deactivated successfully.';
                    break;
            }

            DB::commit();

            return redirect()->route('admin.passages.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }
}