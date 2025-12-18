@extends('layouts.admin')

@section('title', 'Edit Topic - A-plus CBT')
@section('page-title', 'Edit Topic: ' . $topic->name)
@section('mobile-title', 'Edit Topic')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.topics.index') }}">Topics</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.topics.show', $topic) }}">{{ $topic->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="admin-card">
    <form action="{{ route('admin.topics.update', $topic) }}" method="POST" id="topicForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Topic Information</h6>
                            <div class="badge bg-light text-dark">
                                ID: {{ $topic->id }}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">Subject *</label>
                                <select name="subject_id" 
                                        id="subject_id" 
                                        class="form-select @error('subject_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Select a Subject</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" 
                                            {{ old('subject_id', $topic->subject_id) == $subject->id ? 'selected' : '' }}
                                            data-code="{{ $subject->code }}">
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Topic Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $topic->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3">{{ old('description', $topic->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="syllabus_ref" class="form-label">Syllabus Reference</label>
                                <input type="text" 
                                       name="syllabus_ref" 
                                       id="syllabus_ref" 
                                       class="form-control @error('syllabus_ref') is-invalid @enderror" 
                                       value="{{ old('syllabus_ref', $topic->syllabus_ref) }}">
                                @error('syllabus_ref')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Topic Stats -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Topic Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">{{ $topic->questions_count ?? 0 }}</div>
                                    <small class="text-muted">Questions</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <div class="h4 mb-1">0</div>
                                    <small class="text-muted">Average Score</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="admin-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="syllabus_order" class="form-label">Syllabus Order</label>
                            <input type="number" 
                                   name="syllabus_order" 
                                   id="syllabus_order" 
                                   class="form-control @error('syllabus_order') is-invalid @enderror" 
                                   value="{{ old('syllabus_order', $topic->syllabus_order) }}" 
                                   min="0">
                            @error('syllabus_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1" 
                                       {{ old('is_active', $topic->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Topic
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-0">
                            <label class="form-label">Created</label>
                            <div class="text-muted small">
                                {{ $topic->created_at ? $topic->created_at->format('M d, Y \a\t h:i A') : 'N/A' }}
                            </div>
                        </div>
                        
                        @if($topic->updated_at && $topic->updated_at != $topic->created_at)
                        <div class="mt-2">
                            <label class="form-label">Last Updated</label>
                            <div class="text-muted small">
                                {{ $topic->updated_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="admin-card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.topics.show', $topic) }}" class="btn-admin btn-admin-secondary">
                                <i class="ri-eye-line me-2"></i> View Details
                            </a>
                            <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" 
                               class="btn-admin btn-admin-secondary">
                                <i class="ri-question-line me-2"></i> Add Question
                            </a>
                            <button type="button" 
                                    class="btn-admin btn-admin-danger" 
                                    onclick="confirmDelete('{{ addslashes($topic->name) }}')">
                                <i class="ri-delete-bin-line me-2"></i> Delete Topic
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
    <div class="row align-items-center">
        <div class="col-md-6 mb-3 mb-md-0">
            <a href="{{ route('admin.topics.index') }}" class="btn-admin btn-admin-secondary w-100 w-md-auto">
                <i class="ri-arrow-left-line me-2"></i> Back to List
            </a>
        </div>
        
        <div class="col-md-6">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                <a href="{{ route('admin.topics.show', $topic) }}" class="btn-admin btn-admin-secondary flex-fill flex-md-auto">
                    <i class="ri-close-line me-2"></i> Cancel
                </a>
                <button type="submit" class="btn-admin btn-admin-primary flex-fill flex-md-auto">
                    <i class="ri-save-line me-2"></i> Update Topic
                </button>
            </div>
        </div>
    </div>
</div>
    </form>
    
    <!-- Delete Form (hidden) -->
    <form id="deleteForm" action="{{ route('admin.topics.destroy', $topic) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(topicName) {
    if (confirm(`Are you sure you want to delete "${topicName}"? This will also delete all associated questions. This action cannot be undone.`)) {
        document.getElementById('deleteForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('topicForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const subjectId = document.getElementById('subject_id').value;
            const name = document.getElementById('name').value.trim();
            
            if (!subjectId || !name) {
                e.preventDefault();
                alert('Subject and Topic Name are required.');
                return false;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line spin me-2"></i> Updating...';
            submitBtn.disabled = true;
        });
    }
});
</script>

<style>
/* Improved button spacing for edit page */
@media (max-width: 767.98px) {
    .btn-admin {
        padding: 0.625rem 1rem;
        font-size: 14px;
    }
    
    .flex-fill {
        width: 100%;
    }
    
    .gap-2 {
        gap: 1rem !important;
    }
}

@media (min-width: 768px) {
    .flex-md-auto {
        width: auto !important;
        min-width: 140px;
    }
    
    .w-md-auto {
        width: auto !important;
    }
}

/* Better button hover effects */
.btn-admin {
    transition: all 0.2s ease;
}

.btn-admin:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-admin:active {
    transform: translateY(0);
}
</style>
@endpush
