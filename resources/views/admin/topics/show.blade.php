@extends('layouts.admin')

@section('title', $topic->name . ' - Topic Details - A-plus CBT')
@section('page-title', 'Topic Details')
@section('mobile-title', $topic->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.topics.index') }}">Topics</a></li>
<li class="breadcrumb-item active">{{ $topic->name }}</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.topics.edit', $topic) }}" class="btn-admin btn-admin-primary">
        <i class="ri-edit-line me-2"></i> Edit Topic
    </a>
    <button type="button" 
            class="btn-admin btn-admin-danger" 
            onclick="confirmDelete('{{ addslashes($topic->name) }}')">
        <i class="ri-delete-bin-line me-2"></i> Delete
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Main Information -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-1">{{ $topic->name }}</h3>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary">Topic</span>
                            @if($topic->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                            <span class="badge bg-light text-dark border">Order: {{ $topic->syllabus_order }}</span>
                        </div>
                    </div>
                    <div class="text-muted small text-end">
                        <div>ID: {{ $topic->id }}</div>
                        <div>Created: {{ $topic->created_at ? $topic->created_at->format('M d, Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Subject Information -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Subject</h6>
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('admin.subjects.show', $topic->subject_id) }}" 
                           class="d-flex align-items-center gap-2 text-decoration-none p-3 bg-light rounded">
                            @if($topic->subject->icon_class)
                            <i class="{{ $topic->subject->icon_class }} fs-4 text-primary"></i>
                            @endif
                            <div>
                                <h5 class="mb-1">{{ $topic->subject->name }}</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary">{{ $topic->subject->code }}</span>
                                    <span class="text-muted small">
                                        {{ $topic->subject->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                @if($topic->description)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Description</h6>
                    <p class="mb-0">{{ $topic->description }}</p>
                </div>
                @endif
                
                @if($topic->syllabus_ref)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Syllabus Reference</h6>
                    <code class="bg-light p-2 rounded d-inline-block">{{ $topic->syllabus_ref }}</code>
                </div>
                @endif
                
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="ri-question-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-number">{{ $topic->questions_count ?? 0 }}</h4>
                                <p class="stat-label">Questions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="ri-bar-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-number">0</h4>
                                <p class="stat-label">Average Score</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Questions -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Recent Questions</h6>
                        <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" 
                           class="btn-admin btn-admin-sm btn-admin-primary">
                            <i class="ri-add-line me-1"></i> Add Question
                        </a>
                    </div>
                    
                    @php
                        $questions = $topic->questions ?? collect();
                    @endphp
                    
                    @if($questions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($questions as $question)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div style="max-width: 70%;">
                                    <h6 class="mb-1">{{ Str::limit(strip_tags($question->question_text), 100) }}</h6>
                                    <div class="d-flex gap-2">
                                        <small class="text-muted">Type: {{ ucfirst($question->question_type) }}</small>
                                        <small class="text-muted">Difficulty: {{ ucfirst($question->difficulty) }}</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.questions.show', $question) }}" 
                                       class="btn-admin btn-admin-secondary btn-sm">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('admin.questions.edit', $question) }}" 
                                       class="btn-admin btn-admin-secondary btn-sm">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-end mt-2">
                        <a href="{{ route('admin.questions.index', ['topic_id' => $topic->id]) }}" 
                           class="btn-admin btn-admin-link btn-sm">
                            View All Questions →
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <div class="empty-state-sm">
                            <i class="ri-question-line text-muted" style="font-size: 36px;"></i>
                            <h6 class="mt-3">No Questions Yet</h6>
                            <p class="text-muted mb-3">Add questions to this topic</p>
                            <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" 
                               class="btn-admin btn-admin-primary btn-sm">
                                <i class="ri-add-line me-1"></i> Add Question
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Topic Details Card -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Topic Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Status</label>
                    <div>
                        @if($topic->is_active)
                        <span class="badge bg-success">Active</span>
                        <small class="text-muted d-block mt-1">Available for question creation</small>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        <small class="text-muted d-block mt-1">Hidden from question creation</small>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Syllabus Order</label>
                    <div class="h5">{{ $topic->syllabus_order }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Created</label>
                    <div>{{ $topic->created_at ? $topic->created_at->format('F d, Y \a\t h:i A') : 'N/A' }}</div>
                </div>
                
                @if($topic->updated_at && $topic->updated_at != $topic->created_at)
                <div class="mb-0">
                    <label class="form-label text-muted mb-1">Last Updated</label>
                    <div>{{ $topic->updated_at->format('F d, Y \a\t h:i A') }}</div>
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
                    <!-- Back to List button added here -->
                    <a href="{{ route('admin.topics.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-arrow-left-line me-2"></i> Back to List
                    </a>
                    <a href="{{ route('admin.topics.edit', $topic) }}" class="btn-admin btn-admin-primary">
                        <i class="ri-edit-line me-2"></i> Edit Topic
                    </a>
                    <a href="{{ route('admin.questions.create', ['topic_id' => $topic->id]) }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-question-line me-2"></i> Add Question
                    </a>
                    <a href="{{ route('admin.subjects.show', $topic->subject_id) }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-book-line me-2"></i> View Subject
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

<!-- Delete Form (hidden) -->
<form id="deleteForm" action="{{ route('admin.topics.destroy', $topic) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(topicName) {
    if (confirm(`Are you sure you want to delete "${topicName}"? This will also delete all associated questions. This action cannot be undone.`)) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endpush

@push('styles')
<style>
.stat-card {
    background: var(--admin-card-bg);
    border: 1px solid var(--admin-border-color);
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--admin-text-color);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--admin-text-muted);
    margin-bottom: 0;
}

.empty-state-sm {
    padding: 2rem 1rem;
}

.empty-state-sm i {
    opacity: 0.5;
}

.list-group-item {
    border-color: var(--admin-border-color);
    background: var(--admin-card-bg);
}

.list-group-item:hover {
    background: var(--admin-hover-bg);
}

@media (max-width: 768px) {
    .stat-card {
        padding: 0.75rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
}
</style>
@endpush