<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $subjectId = $request->get('subject_id');
        $search = $request->get('search');
        $status = $request->get('status');
        
        // Start query with counts
        $query = Topic::with(['subject'])
                     ->withCount('questions');
        
        // Apply filters
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('syllabus_ref', 'like', "%{$search}%");
            });
        }
        
        if ($status !== null) {
            $query->where('is_active', $status == 'active');
        }
        
        // Order and paginate
        $topics = $query->syllabusOrder()
                       ->paginate(15)
                       ->withQueryString();
        
        // Get all subjects for filter dropdown
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.topics.index', compact('topics', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active subjects for dropdown
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.topics.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate with custom messages
        $validator = Validator::make($request->all(), [
            'subject_id' => [
                'required',
                'exists:subjects,id',
                function ($attribute, $value, $fail) {
                    $subject = Subject::find($value);
                    if ($subject && !$subject->is_active) {
                        $fail('The selected subject is not active.');
                    }
                }
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics')->where(function ($query) use ($request) {
                    return $query->where('subject_id', $request->subject_id);
                })
            ],
            'description' => 'nullable|string|max:1000',
            'syllabus_ref' => 'nullable|string|max:100',
            'syllabus_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.unique' => 'A topic with this name already exists in the selected subject.',
            'subject_id.exists' => 'The selected subject does not exist.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        // Get validated data
        $validated = $validator->validated();
        
        // Set default syllabus order if not provided
        if (!isset($validated['syllabus_order'])) {
            $maxOrder = Topic::where('subject_id', $validated['subject_id'])
                            ->max('syllabus_order') ?? 0;
            $validated['syllabus_order'] = $maxOrder + 1;
        }
        
        // Create the topic
        $topic = Topic::create($validated);
        
        return redirect()->route('admin.topics.show', $topic)
                         ->with('success', 'Topic created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find topic with relationships and counts
        $topic = Topic::with(['subject'])
                     ->withCount('questions')
                     ->findOrFail($id);
        
        // Load recent questions for this topic
        $topic->load(['questions' => function($query) {
            $query->latest()
                  ->limit(5)
                  ->with(['subject', 'topic']);
        }]);
        
        return view('admin.topics.show', compact('topic'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $topic = Topic::with(['subject'])
                     ->withCount('questions')
                     ->findOrFail($id);
        
        // Get active subjects for dropdown
        $subjects = Subject::active()->ordered()->get();
        
        return view('admin.topics.edit', compact('topic', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $topic = Topic::findOrFail($id);
        
        // Validate with custom unique rule (excluding current topic)
        $validator = Validator::make($request->all(), [
            'subject_id' => [
                'required',
                'exists:subjects,id',
                function ($attribute, $value, $fail) {
                    $subject = Subject::find($value);
                    if ($subject && !$subject->is_active) {
                        $fail('The selected subject is not active.');
                    }
                }
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics')->where(function ($query) use ($request) {
                    return $query->where('subject_id', $request->subject_id);
                })->ignore($topic->id)
            ],
            'description' => 'nullable|string|max:1000',
            'syllabus_ref' => 'nullable|string|max:100',
            'syllabus_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.unique' => 'A topic with this name already exists in the selected subject.',
            'subject_id.exists' => 'The selected subject does not exist.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        // Get validated data
        $validated = $validator->validated();
        
        // Update the topic
        $topic->update($validated);
        
        return redirect()->route('admin.topics.show', $topic)
                         ->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $topic = Topic::findOrFail($id);
        
        // Check if topic has related questions
        if ($topic->questions()->exists()) {
            return redirect()->back()
                           ->with('error', 'Cannot delete topic with existing questions. Delete questions first.');
        }
        
        // Delete the topic
        $topic->delete();
        
        return redirect()->route('admin.topics.index')
                         ->with('success', 'Topic deleted successfully.');
    }

    /**
     * Import topics from CSV/Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        
        // This is a basic implementation. You might want to use Maatwebsite/Laravel-Excel package
        // for more robust Excel/CSV handling
        
        return redirect()->route('admin.topics.index')
                         ->with('info', 'Import feature coming soon. For now, add topics manually.');
    }

    /**
     * Export topics to CSV.
     */
    public function export(Request $request)
    {
        $subjectId = $request->get('subject_id');
        
        // This is a basic implementation. You might want to use a package for better CSV/Excel export
        
        return redirect()->route('admin.topics.index')
                         ->with('info', 'Export feature coming soon.');
    }

    /**
     * Get topics by subject for AJAX requests.
     */
    public function bySubject($subjectId)
    {
        $topics = Topic::where('subject_id', $subjectId)
                      ->active()
                      ->syllabusOrder()
                      ->get(['id', 'name']);
        
        return response()->json($topics);
    }
}