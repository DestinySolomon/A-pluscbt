@extends('layouts.admin')

@section('title', $passage->title ?? 'Passage Details - A-plus CBT')
@section('page-title', 'Passage Details')
@section('mobile-title', 'Passage Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.passages.index') }}">Passages</a></li>
<li class="breadcrumb-item active">{{ $passage->title ? Str::limit($passage->title, 30) : 'Passage #'.$passage->id }}</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.passages.index') }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Back to List
    </a>
    <a href="{{ route('admin.passages.edit', $passage) }}" class="btn-admin btn-admin-primary">
        <i class="ri-edit-line me-2"></i> Edit Passage
    </a>
    <button type="button" 
            class="btn-admin btn-admin-danger" 
            onclick="confirmDelete()">
        <i class="ri-delete-bin-line me-2"></i> Delete
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Passage Display Card -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-1">{{ $passage->title ?: 'Untitled Passage' }}</h3>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary">{{ $passage->subject->name }}</span>
                            @if($passage->topic)
                            <span class="badge bg-info">{{ $passage->topic->name }}</span>
                            @endif
                            @if($passage->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-muted small text-end">
                        <div>ID: {{ $passage->id }}</div>
                        <div>{{ $passage->questions_count }} Questions</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($passage->source)
                <div class="mb-3">
                    <span class="badge bg-secondary">Source: {{ $passage->source }}</span>
                </div>
                @endif
                
                @if($passage->instruction)
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    {{ $passage->instruction }}
                </div>
                @endif
                
                <div class="passage-content p-4 bg-light rounded mb-4">
                    {!! nl2br(e($passage->content)) !!}
                </div>
                
                @if($passage->image_path)
                <div class="text-center">
                    <img src="{{ Storage::url($passage->image_path) }}" 
                         alt="Passage Image" 
                         class="img-fluid rounded" 
                         style="max-height: 300px;">
                </div>
                @endif
            </div>
        </div>
        
        <!-- Questions Card -->
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Questions ({{ $passage->questions_count }})</h5>
                <button type="button" class="btn-admin btn-admin-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="ri-add-line me-1"></i> Add Question
                </button>
            </div>
            <div class="card-body">
                @if($passage->questions->isEmpty())
                <div class="text-center py-4">
                    <i class="ri-questionnaire-line text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">No questions added to this passage yet.</p>
                    <button type="button" class="btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="ri-add-line me-2"></i> Add First Question
                    </button>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Q.No</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Correct</th>
                                <th>Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($passage->questions as $question)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $question->question_number }}</span></td>
                                <td>{{ Str::limit(strip_tags($question->question_text), 60) }}</td>
                                <td>
                                    @foreach($question->options as $option)
                                    <span class="badge bg-light text-dark me-1">{{ $option->option_letter }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        $correct = $question->options->where('is_correct', true)->first();
                                    @endphp
                                    @if($correct)
                                    <span class="badge bg-success">{{ $correct->option_letter }}</span>
                                    @endif
                                </td>
                                <td>{{ $question->marks }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('admin.questions.edit', $question) }}" 
                                           class="btn-admin btn-admin-secondary btn-sm">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <form action="{{ route('admin.questions.destroy', $question) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn-admin btn-admin-danger btn-sm"
                                                    onclick="return confirm('Delete this question?')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Statistics Card -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Questions:</span>
                            <strong>{{ $passage->questions_count }}</strong>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Word Count:</span>
                            <strong>{{ str_word_count(strip_tags($passage->content)) }}</strong>
                        </div>
                    </li>
                    <li class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Created:</span>
                            <strong>{{ $passage->created_at->format('M d, Y') }}</strong>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Passage Info</h6>
            </div>
            <div class="card-body">
                <p><strong>Subject:</strong> {{ $passage->subject->name }}</p>
                @if($passage->topic)
                <p><strong>Topic:</strong> {{ $passage->topic->name }}</p>
                @endif
                <p><strong>Status:</strong> {{ $passage->is_active ? 'Active' : 'Inactive' }}</p>
                @if($passage->source)
                <p><strong>Source:</strong> {{ $passage->source }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.passages.add-question', $passage) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Question to Passage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="question_number" class="form-label">Question Number *</label>
                        <input type="number" 
                               name="question_number" 
                               id="question_number" 
                               class="form-control" 
                               value="{{ $passage->questions_count + 1 }}" 
                               min="1" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question Text *</label>
                        <textarea name="question_text" 
                                  id="question_text" 
                                  class="form-control" 
                                  rows="3" 
                                  required></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="marks" class="form-label">Marks *</label>
                            <input type="number" name="marks" id="marks" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="correct_option" class="form-label">Correct Option *</label>
                            <select name="correct_option" id="correct_option" class="form-select" required>
                                <option value="">Select Correct Option</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Options</h6>
                    
                    @foreach(['A', 'B', 'C', 'D', 'E'] as $index => $letter)
                    <div class="row mb-2">
                        <div class="col-1">
                            <span class="badge bg-secondary">{{ $letter }}</span>
                        </div>
                        <div class="col-11">
                            <textarea name="options[{{ $index }}][text]" 
                                      class="form-control" 
                                      rows="1" 
                                      required 
                                      placeholder="Option {{ $letter }} text..."></textarea>
                            <input type="hidden" name="options[{{ $index }}][is_correct]" value="0">
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="mb-3">
                        <label for="explanation" class="form-label">Explanation (Optional)</label>
                        <textarea name="explanation" id="explanation" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-save-line me-2"></i> Save Question
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" action="{{ route('admin.passages.destroy', $passage) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this passage? All questions linked to it will also be deleted. This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.passage-content {
    font-family: 'Georgia', serif;
    font-size: 1.1rem;
    line-height: 1.8;
    white-space: pre-wrap;
}
.table-actions {
    display: flex;
    gap: 0.25rem;
}
</style>
@endpush