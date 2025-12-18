@extends('layouts.admin')

@section('title', 'Question #' . $question->id . ' - Question Details - A-plus CBT')
@section('page-title', 'Question Details')
@section('mobile-title', 'Question #' . $question->id)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Questions</a></li>
<li class="breadcrumb-item active">Question #{{ $question->id }}</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.questions.index') }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Back to List
    </a>
    <a href="{{ route('admin.questions.edit', $question) }}" class="btn-admin btn-admin-primary">
        <i class="ri-edit-line me-2"></i> Edit Question
    </a>
    <button type="button" 
            class="btn-admin btn-admin-danger" 
            onclick="confirmDelete('Question #{{ $question->id }}')">
        <i class="ri-delete-bin-line me-2"></i> Delete
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Main Question Content -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-1">Question #{{ $question->id }}</h3>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary">Question</span>
                            @if($question->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                            @if($question->difficulty == 'easy')
                            <span class="badge bg-success">Easy</span>
                            @elseif($question->difficulty == 'medium')
                            <span class="badge bg-warning text-dark">Medium</span>
                            @else
                            <span class="badge bg-danger">Hard</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-muted small text-end">
                        <div>ID: {{ $question->id }}</div>
                        <div>Created: {{ $question->created_at ? $question->created_at->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Subject & Topic -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Subject & Topic</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center gap-2 p-3 bg-light rounded">
                                @if($question->subject->icon_class)
                                <i class="{{ $question->subject->icon_class }} fs-4 text-primary"></i>
                                @endif
                                <div>
                                    <h5 class="mb-1">{{ $question->subject->name }}</h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary">{{ $question->subject->code }}</span>
                                        <span class="text-muted small">
                                            {{ $question->subject->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($question->topic)
                            <div class="p-3 bg-light rounded">
                                <h6 class="mb-1">{{ $question->topic->name }}</h6>
                                <div class="text-muted small">
                                    Order: {{ $question->topic->syllabus_order }}
                                    @if($question->topic->syllabus_ref)
                                    • Ref: {{ $question->topic->syllabus_ref }}
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="p-3 bg-light rounded text-center">
                                <span class="text-muted">No topic assigned</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Question Text -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Question</h6>
                    <div class="question-content p-3 bg-light rounded">
                        {!! $question->question_text !!}
                    </div>
                    
                    @if($question->image_path)
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Question Image</h6>
                        <div class="text-center">
                            <img src="{{ Storage::url($question->image_path) }}" 
                                 alt="Question Image" 
                                 class="img-fluid rounded" 
                                 style="max-height: 300px;">
                            <div class="mt-2">
                                <a href="{{ Storage::url($question->image_path) }}" 
                                   target="_blank" 
                                   class="btn-admin btn-admin-sm btn-admin-secondary">
                                    <i class="ri-external-link-line me-1"></i> View Full Size
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Options -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Options</h6>
                    <div class="row">
                        @foreach($question->options as $option)
                        <div class="col-md-6 mb-3">
                            <div class="option-display p-3 rounded {{ $option->is_correct ? 'border-success border-2' : 'border' }} 
                                      {{ $option->is_correct ? 'bg-success bg-opacity-10' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0">
                                        <span class="badge {{ $option->is_correct ? 'bg-success' : 'bg-secondary' }} me-2">
                                            {{ $option->option_letter }}
                                        </span>
                                        @if($option->is_correct)
                                        <span class="badge bg-success">Correct Answer</span>
                                        @endif
                                    </h5>
                                </div>
                                
                                <div class="mb-2">
                                    {{ $option->option_text }}
                                </div>
                                
                                @if($option->image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($option->image_path) }}" 
                                         alt="Option {{ $option->option_letter }} Image" 
                                         class="img-thumbnail" 
                                         style="max-height: 100px;">
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Explanation -->
                @if($question->explanation)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Explanation</h6>
                    <div class="p-3 bg-info bg-opacity-10 border border-info rounded">
                        {!! $question->explanation !!}
                    </div>
                </div>
                @endif
                
                <!-- Question Statistics -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Question Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $question->times_answered }}</div>
                                    <small class="text-muted">Times Answered</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $question->times_correct }}</div>
                                    <small class="text-muted">Times Correct</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $question->times_answered > 0 ? number_format($question->success_rate, 1) : 0 }}%</div>
                                    <small class="text-muted">Success Rate</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $question->time_estimate }}s</div>
                                    <small class="text-muted">Time Estimate</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($question->times_answered > 0)
                        <div class="mt-3">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $question->success_rate }}%"
                                     aria-valuenow="{{ $question->success_rate }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($question->success_rate, 1) }}% Success
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Question Details Card -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Question Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Marks</label>
                    <div class="h4">{{ $question->marks }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Difficulty</label>
                    <div>
                        @if($question->difficulty == 'easy')
                        <span class="badge bg-success">Easy</span>
                        @elseif($question->difficulty == 'medium')
                        <span class="badge bg-warning text-dark">Medium</span>
                        @else
                        <span class="badge bg-danger">Hard</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Status</label>
                    <div>
                        @if($question->is_active)
                        <span class="badge bg-success">Active</span>
                        <small class="text-muted d-block mt-1">Available for exams</small>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        <small class="text-muted d-block mt-1">Hidden from exams</small>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Created</label>
                    <div>{{ $question->created_at ? $question->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}</div>
                </div>
                
                @if($question->updated_at && $question->updated_at != $question->created_at)
                <div class="mb-0">
                    <label class="form-label text-muted mb-1">Last Updated</label>
                    <div>{{ $question->updated_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.questions.edit', $question) }}" class="btn-admin btn-admin-primary">
                        <i class="ri-edit-line me-2"></i> Edit Question
                    </a>
                    <a href="{{ route('admin.questions.create') }}?subject_id={{ $question->subject_id }}&topic_id={{ $question->topic_id }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-add-line me-2"></i> Add Similar Question
                    </a>
                    <a href="{{ route('admin.subjects.show', $question->subject_id) }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-book-line me-2"></i> View Subject
                    </a>
                    @if($question->topic)
                    <a href="{{ route('admin.topics.show', $question->topic_id) }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-folder-line me-2"></i> View Topic
                    </a>
                    @endif
                    <button type="button" 
                            class="btn-admin btn-admin-danger mt-2" 
                            onclick="confirmDelete('Question #{{ $question->id }}')">
                        <i class="ri-delete-bin-line me-2"></i> Delete Question
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(questionText) {
    if (confirm(`Are you sure you want to delete ${questionText}? This will also delete all associated options and answers. This action cannot be undone.`)) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.question-content {
    font-size: 1.1rem;
    line-height: 1.6;
}

.question-content img {
    max-width: 100%;
    height: auto;
}

.option-display {
    transition: all 0.2s;
    min-height: 120px;
}

.option-display:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.border-success {
    border-color: #14b8a6 !important;
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .option-display {
        min-height: 100px;
    }
    
    .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endpush