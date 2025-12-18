@extends('layouts.admin')

@section('title', 'Questions Management - A-plus CBT')
@section('page-title', 'Questions Management')
@section('mobile-title', 'Questions')

@section('breadcrumbs')
<li class="breadcrumb-item active">Questions</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.questions.create') }}" class="btn-admin btn-admin-primary">
        <i class="ri-add-line me-2"></i> Add Question
    </a>
    
    <!-- Import/Export Dropdown -->
    <div class="dropdown">
        <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="ri-download-2-line me-2"></i> Bulk Actions
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="ri-upload-line me-2"></i> Import from CSV
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="ri-download-line me-2"></i> Export to CSV
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <button type="button" class="dropdown-item text-danger" onclick="confirmBulkAction('delete')">
                    <i class="ri-delete-bin-line me-2"></i> Delete Selected
                </button>
            </li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.questions.index') }}" class="row g-3" id="filterForm">
            <div class="col-md-3">
                <label for="subject_id" class="form-label">Subject</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }} ({{ $subject->code }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="topic_id" class="form-label">Topic</label>
                <select name="topic_id" id="topic_id" class="form-select" {{ !request('subject_id') ? 'disabled' : '' }}>
                    <option value="">All Topics</option>
                    @if(request('subject_id') && $topics->isNotEmpty())
                        @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                            {{ $topic->name }}
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="difficulty" class="form-label">Difficulty</label>
                <select name="difficulty" id="difficulty" class="form-select">
                    <option value="">All</option>
                    <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="search" class="form-label">Search</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Search..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.questions.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="card-body">
        @if($questions->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-question-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Questions Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['subject_id', 'topic_id', 'difficulty', 'status', 'search']))
                    Try adjusting your filters
                    @else
                    Get started by adding your first question
                    @endif
                </p>
                <a href="{{ route('admin.questions.create') }}" class="btn-admin btn-admin-primary mt-3">
                    <i class="ri-add-line me-2"></i> Add First Question
                </a>
            </div>
        </div>
        @else
        <!-- Bulk Action Form -->
        <form id="bulkActionForm" method="POST" action="{{ route('admin.questions.bulk-action') }}">
            @csrf
            @method('POST')
            <input type="hidden" name="action" id="bulkActionType">
            <input type="hidden" name="question_ids" id="selectedQuestionIds">
        </form>
        
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th width="60">ID</th>
                        <th>Question</th>
                        <th>Subject & Topic</th>
                        <th>Difficulty</th>
                        <th>Marks</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Stats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                    <tr>
                        <td>
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" 
                                   class="form-check-input question-checkbox">
                        </td>
                        <td>{{ $question->id }}</td>
                        <td>
                            <div class="d-flex align-items-start gap-2">
                                @if($question->image_path)
                                <div class="flex-shrink-0">
                                    <div class="question-thumbnail" 
                                         style="width: 60px; height: 40px; background: #f8f9fa; border-radius: 4px; 
                                                overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ Storage::url($question->image_path) }}" 
                                             alt="Question image" 
                                             style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                                @endif
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h6 class="mb-1 text-truncate" style="max-width: 300px;" 
                                        title="{{ strip_tags($question->question_text) }}">
                                        {!! Str::limit(strip_tags($question->question_text), 80) !!}
                                    </h6>
                                    <div class="text-muted small">
                                        {{ $question->options_count }} options
                                        @if($question->correctOption())
                                        • Correct: {{ $question->correctOption()->option_letter }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <div class="fw-medium">{{ $question->subject->name ?? 'N/A' }}</div>
                                <div class="text-muted">{{ $question->topic->name ?? 'No topic' }}</div>
                            </div>
                        </td>
                        <td>
                            @if($question->difficulty == 'easy')
                            <span class="badge bg-success">Easy</span>
                            @elseif($question->difficulty == 'medium')
                            <span class="badge bg-warning text-dark">Medium</span>
                            @else
                            <span class="badge bg-danger">Hard</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium">{{ $question->marks }}</span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $question->time_estimate }}s</span>
                        </td>
                        <td>
                            @if($question->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($question->times_answered > 0)
                            <div class="small">
                                <div>Answered: {{ $question->times_answered }}</div>
                                <div>Success: {{ number_format($question->success_rate, 1) }}%</div>
                            </div>
                            @else
                            <span class="text-muted small">No data</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.questions.show', $question) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('admin.questions.edit', $question) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('admin.questions.destroy', $question) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirmDelete(this, 'Question #{{ $question->id }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn-admin btn-admin-danger btn-sm" 
                                            title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            @if($questions->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} questions
                </div>
                <div>
                    {{ $questions->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.questions.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Questions from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_subject_id" class="form-label">Subject *</label>
                        <select name="subject_id" id="import_subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="import_topic_id" class="form-label">Topic (Optional)</label>
                        <select name="topic_id" id="import_topic_id" class="form-select" disabled>
                            <option value="">Select Topic</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="import_file" class="form-label">CSV File *</label>
                        <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv" required>
                        <small class="text-muted">
                            CSV format: question_text,option_a,option_b,option_c,option_d,correct_option,difficulty,marks,explanation
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <strong>Note:</strong> Download the template file for correct format.
                        <a href="#" class="alert-link">Download Template</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-upload-line me-2"></i> Import Questions
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.questions.export') }}" method="GET">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Questions to CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_subject_id" class="form-label">Subject (Optional)</label>
                        <select name="subject_id" id="export_subject_id" class="form-select">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="export_topic_id" class="form-label">Topic (Optional)</label>
                        <select name="topic_id" id="export_topic_id" class="form-select" disabled>
                            <option value="">All Topics</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Include Fields</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_options" id="include_options" checked>
                            <label class="form-check-label" for="include_options">Include Options</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_explanation" id="include_explanation" checked>
                            <label class="form-check-label" for="include_explanation">Include Explanation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="include_stats" id="include_stats">
                            <label class="form-check-label" for="include_stats">Include Statistics</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-download-line me-2"></i> Export Questions
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form, questionText) {
    if (confirm(`Are you sure you want to delete ${questionText}? This action cannot be undone.`)) {
        form.submit();
    }
    return false;
}

function confirmBulkAction(action) {
    const selectedIds = getSelectedQuestionIds();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one question.');
        return false;
    }
    
    let actionText = action === 'delete' ? 'delete' : (action === 'activate' ? 'activate' : 'deactivate');
    let confirmText = `Are you sure you want to ${actionText} ${selectedIds.length} selected question(s)?`;
    
    if (action === 'delete') {
        confirmText += ' This action cannot be undone.';
    }
    
    if (confirm(confirmText)) {
        document.getElementById('bulkActionType').value = action;
        document.getElementById('selectedQuestionIds').value = JSON.stringify(selectedIds);
        document.getElementById('bulkActionForm').submit();
    }
}

function getSelectedQuestionIds() {
    const checkboxes = document.querySelectorAll('.question-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

$(document).ready(function() {
    // Initialize DataTable
    $('.data-table').DataTable({
        responsive: true,
        order: [[1, 'desc']], // Order by ID descending
        columnDefs: [
            { orderable: false, targets: [0, 9] }, // Disable sorting for checkbox and actions columns
            { responsivePriority: 1, targets: [2] }, // Question text
            { responsivePriority: 2, targets: [9] }, // Actions
            { responsivePriority: 3, targets: [3] }, // Subject & Topic
            { responsivePriority: 4, targets: [4] }, // Difficulty
            { responsivePriority: 5, targets: [0] }  // Checkbox
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search questions...",
            lengthMenu: "_MENU_ questions per page",
            info: "Showing _START_ to _END_ of _TOTAL_ questions",
            infoEmpty: "Showing 0 to 0 of 0 questions",
            infoFiltered: "(filtered from _MAX_ total questions)",
            zeroRecords: "No matching questions found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        // Disable DataTable's built-in search since we have our own
        searching: false,
        paging: false,
        info: false
    });
    
    // Select all checkboxes
    $('#selectAll').change(function() {
        $('.question-checkbox').prop('checked', this.checked);
    });
    
    // Update select all checkbox when individual checkboxes change
    $('.question-checkbox').change(function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        } else {
            const allChecked = $('.question-checkbox:checked').length === $('.question-checkbox').length;
            $('#selectAll').prop('checked', allChecked);
        }
    });
    
    // Load topics when subject changes in filter form
    $('#subject_id').change(function() {
        const subjectId = $(this).val();
        const topicSelect = $('#topic_id');
        
        if (subjectId) {
            topicSelect.prop('disabled', false);
            
            // Load topics via AJAX
            $.ajax({
                url: '{{ route("admin.questions.get-topics-by-subject", ":subjectId") }}'.replace(':subjectId', subjectId),
                method: 'GET',
                success: function(topics) {
                    topicSelect.html('<option value="">All Topics</option>');
                    topics.forEach(function(topic) {
                        topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                    });
                }
            });
        } else {
            topicSelect.prop('disabled', true).html('<option value="">All Topics</option>');
        }
    });
    
    // Load topics when subject changes in import modal
    $('#import_subject_id').change(function() {
        const subjectId = $(this).val();
        const topicSelect = $('#import_topic_id');
        
        if (subjectId) {
            topicSelect.prop('disabled', false);
            
            $.ajax({
                url: '{{ route("admin.questions.get-topics-by-subject", ":subjectId") }}'.replace(':subjectId', subjectId),
                method: 'GET',
                success: function(topics) {
                    topicSelect.html('<option value="">Select Topic (Optional)</option>');
                    topics.forEach(function(topic) {
                        topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                    });
                }
            });
        } else {
            topicSelect.prop('disabled', true).html('<option value="">Select Topic (Optional)</option>');
        }
    });
    
    // Load topics when subject changes in export modal
    $('#export_subject_id').change(function() {
        const subjectId = $(this).val();
        const topicSelect = $('#export_topic_id');
        
        if (subjectId) {
            topicSelect.prop('disabled', false);
            
            $.ajax({
                url: '{{ route("admin.questions.get-topics-by-subject", ":subjectId") }}'.replace(':subjectId', subjectId),
                method: 'GET',
                success: function(topics) {
                    topicSelect.html('<option value="">All Topics</option>');
                    topics.forEach(function(topic) {
                        topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                    });
                }
            });
        } else {
            topicSelect.prop('disabled', true).html('<option value="">All Topics</option>');
        }
    });
    
    // Auto-submit filter form when difficulty/status changes
    $('#difficulty, #status').change(function() {
        if ($(this).val()) {
            $('#filterForm').submit();
        }
    });
});
</script>

<style>
.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.5;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    min-width: 120px;
}

.question-thumbnail {
    border: 1px solid #dee2e6;
}

.question-thumbnail img {
    transition: transform 0.2s;
}

.question-thumbnail:hover img {
    transform: scale(1.1);
}

/* Checkbox styling */
.form-check-input {
    cursor: pointer;
    width: 18px;
    height: 18px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        gap: 0.25rem;
        min-width: auto;
    }
    
    .table-actions .btn-admin {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    
    .question-thumbnail {
        width: 40px !important;
        height: 30px !important;
    }
    
    .text-truncate {
        max-width: 200px !important;
    }
    
    /* Improve filter form on mobile */
    .row.g-3 {
        row-gap: 1rem !important;
    }
    
    .col-md-3, .col-md-2 {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .text-truncate {
        max-width: 150px !important;
    }
    
    .page-actions .d-flex {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .page-actions .dropdown {
        width: 100%;
    }
    
    .page-actions .dropdown .btn-admin {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush