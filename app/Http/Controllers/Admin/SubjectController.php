<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get subjects with counts of related topics and questions
        $subjects = Subject::withCount(['topics', 'questions'])
                          ->orderBy('order')
                          ->orderBy('name')
                          ->paginate(15);
        
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'code' => 'required|string|max:10|unique:subjects,code',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'icon_class' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        
        // Set default order if not provided
        if (!isset($validated['order'])) {
            $maxOrder = Subject::max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }
        
        // Create the subject
        Subject::create($validated);
        
        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the subject with counts
        $subject = Subject::withCount(['topics', 'questions', 'exams'])
                         ->findOrFail($id);
        
        // Load recent topics
        $subject->load(['mainTopics' => function($query) {
            $query->withCount('questions')
                  ->orderBy('syllabus_order')
                  ->limit(10);
        }]);
        
        // Load recent questions
        $subject->load(['questions' => function($query) {
            $query->latest()
                  ->limit(5)
                  ->with('topic');
        }]);
        
        // Load related exams
        $subject->load(['exams' => function($query) {
            $query->limit(5);
        }]);
        
        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subject = Subject::findOrFail($id);
        
        // Get counts for display
        $subject->loadCount(['topics', 'questions']);
        
        return view('admin.subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subject = Subject::findOrFail($id);
        
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $id,
            'code' => 'required|string|max:10|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'icon_class' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        
        // Update the subject
        $subject->update($validated);
        
        return redirect()->route('admin.subjects.show', $subject)
                         ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);
        
        // Check if subject has related data
        if ($subject->questions()->exists() || $subject->topics()->exists()) {
            return redirect()->back()
                           ->with('error', 'Cannot delete subject with existing topics or questions. Delete them first.');
        }
        
        // Delete the subject
        $subject->delete();
        
        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject deleted successfully.');
    }
}