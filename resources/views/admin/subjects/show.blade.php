@extends('layouts.admin')

@section('title', $subject->name . ' - Subject Details - A-plus CBT')
@section('page-title', 'Subject Details')
@section('mobile-title', $subject->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
<li class="breadcrumb-item active">{{ $subject->name }}</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn-admin btn-admin-primary">
        <i class="ri-edit-line me-2"></i> Edit Subject
    </a>
    <button type="button" 
            class="btn-admin btn-admin-danger" 
            onclick="confirmDelete('{{ $subject->name }}')">
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
                    <div class="d-flex align-items-center gap-3">
                        @if($subject->icon_class)
                        <div class="subject-icon-lg">
                            <i class="{{ $subject->icon_class }}"></i>
                        </div>
                        @endif
                        <div>
                            <h3 class="mb-1">{{ $subject->name }}</h3>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-secondary">{{ $subject->code }}</span>
                                @if($subject->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                                <span class="badge bg-light text-dark border">Order: {{ $subject->order }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted small text-end">
                        <div>ID: {{ $subject->id }}</div>
                        <div>Created: {{ $subject->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($subject->description)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Description</h6>
                    <p class="mb-0">{{ $subject->description }}</p>
                </div>
                @endif
                
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="ri-folder-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-number">{{ $subject->topics->count() ?? 0 }}</h4>
                                <p class="stat-label">Topics</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="ri-question-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-number">{{ $subject->questions->count() ?? 0 }}</h4>
                                <p class="stat-label">Questions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="ri-file-list-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-number">{{ $subject->exams->count() ?? 0 }}</h4>
                                <p class="stat-label">Exams</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="ri-bar-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-number">0</h4>
                                <p class="stat-label">Attempts</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Topics -->
                <!-- Recent Topics -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0">Topics</h6>
        <a href="{{ route('admin.topics.create', ['subject_id' => $subject->id]) }}" 
           class="btn-admin btn-admin-sm btn-admin-primary">
            <i class="ri-add-line me-1"></i> Add Topic
        </a>
    </div>
    
    @php
        $topics = $subject->topics ?? collect();
    @endphp
    
    @if($topics->count() > 0)
    <div class="table-responsive">
        <table class="table admin-table table-hover">
            <thead>
                <tr>
                    <th>Topic Name</th>
                    <th>Order</th>
                    <th>Sub-topics</th>
                    <th>Questions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topics as $topic)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($topic->icon_class)
                            <i class="{{ $topic->icon_class }} text-muted"></i>
                            @endif
                            <span>{{ $topic->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $topic->syllabus_order }}</span>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $topic->children_count ?? 0 }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $topic->questions_count ?? 0 }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.topics.show', $topic) }}" 
                               class="btn-admin btn-admin-secondary btn-sm"
                               title="View">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="{{ route('admin.topics.edit', $topic) }}" 
                               class="btn-admin btn-admin-secondary btn-sm"
                               title="Edit">
                                <i class="ri-edit-line"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-end">
        <a href="{{ route('admin.topics.index', ['subject_id' => $subject->id]) }}" 
           class="btn-admin btn-admin-link btn-sm">
            View All Topics →
        </a>
    </div>
    @else
    <div class="text-center py-4">
        <div class="empty-state-sm">
            <i class="ri-folder-line text-muted" style="font-size: 36px;"></i>
            <h6 class="mt-3">No Topics Yet</h6>
            <p class="text-muted mb-3">Start by adding topics to this subject</p>
            <a href="{{ route('admin.topics.create', ['subject_id' => $subject->id]) }}" 
               class="btn-admin btn-admin-primary btn-sm">
                <i class="ri-add-line me-1"></i> Create First Topic
            </a>
        </div>
    </div>
    @endif
</div>
                
                <!-- Recent Questions -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Recent Questions</h6>
                        <a href="{{ route('admin.questions.create', ['subject_id' => $subject->id]) }}" 
                           class="btn-admin btn-admin-sm btn-admin-primary">
                            <i class="ri-add-line me-1"></i> Add Question
                        </a>
                    </div>
                    
                    @if($subject->questions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($subject->questions->take(5) as $question)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div style="max-width: 70%;">
                                    <h6 class="mb-1">{{ Str::limit(strip_tags($question->question_text), 100) }}</h6>
                                    <div class="d-flex gap-2">
                                        <small class="text-muted">Type: {{ ucfirst($question->question_type) }}</small>
                                        @if($question->topic)
                                        <small class="text-muted">Topic: {{ $question->topic->name }}</small>
                                        @endif
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
                        <a href="{{ route('admin.questions.index', ['subject_id' => $subject->id]) }}" 
                           class="btn-admin btn-admin-link btn-sm">
                            View All Questions →
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <div class="empty-state-sm">
                            <i class="ri-question-line text-muted" style="font-size: 36px;"></i>
                            <h6 class="mt-3">No Questions Yet</h6>
                            <p class="text-muted mb-3">Add questions to this subject</p>
                            <a href="{{ route('admin.questions.create', ['subject_id' => $subject->id]) }}" 
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
        <!-- Subject Details Card -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Subject Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Status</label>
                    <div>
                        @if($subject->is_active)
                        <span class="badge bg-success">Active</span>
                        <small class="text-muted d-block mt-1">Available to students</small>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        <small class="text-muted d-block mt-1">Hidden from students</small>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Display Order</label>
                    <div class="h5">{{ $subject->order }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Icon Class</label>
                    <div class="d-flex align-items-center gap-2">
                        @if($subject->icon_class)
                        <i class="{{ $subject->icon_class }} fs-4"></i>
                        <code>{{ $subject->icon_class }}</code>
                        @else
                        <span class="text-muted">No icon set</span>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted mb-1">Created</label>
                    <div>{{ $subject->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                
                @if($subject->updated_at && $subject->updated_at != $subject->created_at)
                <div class="mb-0">
                    <label class="form-label text-muted mb-1">Last Updated</label>
                    <div>{{ $subject->updated_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Related Exams Card -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Related Exams</h6>
            </div>
            <div class="card-body">
                @if($subject->exams->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($subject->exams->take(5) as $exam)
                    <a href="{{ route('admin.exams.show', $exam) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $exam->title }}</h6>
                                <small class="text-muted">
                                    Questions: {{ $exam->pivot->question_count ?? 'N/A' }}
                                </small>
                            </div>
                            <i class="ri-arrow-right-s-line"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
                @if($subject->exams->count() > 5)
                <div class="text-center mt-2">
                    <a href="#" class="btn-admin btn-admin-link btn-sm">
                        View All Exams
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-3">
                    <i class="ri-file-list-line text-muted" style="font-size: 36px;"></i>
                    <p class="text-muted mt-2 mb-0">No exams include this subject</p>
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
                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn-admin btn-admin-primary">
                        <i class="ri-edit-line me-2"></i> Edit Subject
                    </a>
                    <a href="{{ route('admin.topics.create', ['subject_id' => $subject->id]) }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-add-line me-2"></i> Add Topic
                    </a>
                    <a href="{{ route('admin.questions.create', ['subject_id' => $subject->id]) }}" 
                       class="btn-admin btn-admin-secondary">
                        <i class="ri-question-line me-2"></i> Add Question
                    </a>
                    <button type="button" 
                            class="btn-admin btn-admin-danger mt-2" 
                            onclick="confirmDelete('{{ $subject->name }}')">
                        <i class="ri-delete-bin-line me-2"></i> Delete Subject
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
function confirmDelete(subjectName) {
    if (confirm(`Are you sure you want to delete "${subjectName}"? This will also delete all associated topics and questions. This action cannot be undone.`)) {
        document.getElementById('deleteForm').submit();
    }
}

// Initialize tooltips if using Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    if (tooltipTriggerList.length > 0 && typeof bootstrap !== 'undefined') {
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.subject-icon-lg {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
}

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

.table-actions {
    display: flex;
    gap: 0.25rem;
}

@media (max-width: 768px) {
    .subject-icon-lg {
        width: 48px;
        height: 48px;
        font-size: 22px;
    }
    
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