@extends('layouts.admin')

@section('title', 'Topics Management - A-plus CBT')
@section('page-title', 'Topics Management')
@section('mobile-title', 'Topics')

@section('breadcrumbs')
<li class="breadcrumb-item active">Topics</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.topics.create') }}" class="btn-admin btn-admin-primary">
        <i class="ri-add-line me-2"></i> Add Topic
    </a>
    
    <!-- Import/Export Dropdown -->
    <div class="dropdown">
        <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="ri-download-2-line me-2"></i> Import/Export
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="ri-upload-line me-2"></i> Import from CSV
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.topics.export') }}">
                    <i class="ri-download-line me-2"></i> Export to CSV
                </a>
            </li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.topics.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="subject_id" class="form-label">Filter by Subject</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }} ({{ $subject->code }})
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="search" class="form-label">Search Topics</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Search by name, description..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.topics.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="card-body">
        @if($topics->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-folder-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Topics Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['subject_id', 'search', 'status']))
                    Try adjusting your filters
                    @else
                    Get started by adding your first topic
                    @endif
                </p>
                <a href="{{ route('admin.topics.create') }}" class="btn-admin btn-admin-primary mt-3">
                    <i class="ri-add-line me-2"></i> Add First Topic
                </a>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Topic</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Syllabus Ref</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Questions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                    <tr>
                        <td>{{ $topic->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    <h6 class="mb-0">{{ $topic->name }}</h6>
                                    @if($topic->created_at)
                                    <small class="text-muted">Added {{ $topic->created_at->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.subjects.show', $topic->subject_id) }}" 
                               class="d-flex align-items-center gap-2 text-decoration-none">
                                @if($topic->subject->icon_class)
                                <i class="{{ $topic->subject->icon_class }} text-primary"></i>
                                @endif
                                <span>{{ $topic->subject->name }}</span>
                            </a>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $topic->description }}">
                                {{ $topic->description ?: 'No description' }}
                            </div>
                        </td>
                        <td>
                            @if($topic->syllabus_ref)
                            <span class="badge bg-info">{{ $topic->syllabus_ref }}</span>
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $topic->syllabus_order }}</span>
                        </td>
                        <td>
                            @if($topic->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium">{{ $topic->questions_count ?? 0 }}</span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.topics.show', $topic) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('admin.topics.edit', $topic) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('admin.topics.destroy', $topic) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirmDelete(this, '{{ addslashes($topic->name) }}')">
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
            
            <!-- Custom Pagination -->
            @if($topics->hasPages())
            <div class="custom-pagination-wrapper">
                <div class="pagination-info">
                    <span class="text-muted">
                        Showing {{ $topics->firstItem() }} to {{ $topics->lastItem() }} of {{ $topics->total() }} topics
                    </span>
                </div>
                
                <nav class="custom-pagination" aria-label="Topics pagination">
                    <ul class="pagination-list">
                        {{-- Previous Button --}}
                        @if ($topics->onFirstPage())
                            <li class="pagination-item disabled">
                                <span class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $topics->previousPageUrl() }}" class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach(range(1, $topics->lastPage()) as $page)
                            @if($page == $topics->currentPage())
                                <li class="pagination-item active">
                                    <span class="pagination-link">{{ $page }}</span>
                                </li>
                            @elseif($page == 1 || $page == $topics->lastPage() || abs($page - $topics->currentPage()) <= 2)
                                <li class="pagination-item">
                                    <a href="{{ $topics->url($page) }}" class="pagination-link">{{ $page }}</a>
                                </li>
                            @elseif(abs($page - $topics->currentPage()) == 3)
                                <li class="pagination-item disabled">
                                    <span class="pagination-link">...</span>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($topics->hasMorePages())
                            <li class="pagination-item">
                                <a href="{{ $topics->nextPageUrl() }}" class="pagination-link">
                                    <i class="ri-arrow-right-s-line"></i>
                                </a>
                            </li>
                        @else
                            <li class="pagination-item disabled">
                                <span class="pagination-link">
                                    <i class="ri-arrow-right-s-line"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.topics.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Topics from CSV</h5>
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
                        <label for="import_file" class="form-label">CSV File *</label>
                        <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">
                            File should have columns: name, description (optional), syllabus_ref (optional), syllabus_order (optional), is_active (optional)
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <strong>Note:</strong> Download the template file to see the correct format.
                        <a href="#" class="alert-link">Download Template</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-upload-line me-2"></i> Import Topics
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form, topicName) {
    if (confirm(`Are you sure you want to delete "${topicName}"? This will also delete all associated questions. This action cannot be undone.`)) {
        form.submit();
    }
    return false;
}

$(document).ready(function() {
    // Initialize DataTable
    $('.data-table').DataTable({
        responsive: true,
        order: [[5, 'asc']], // Order by syllabus_order column
        columnDefs: [
            { orderable: false, targets: [8] }, // Disable sorting for actions column
            { responsivePriority: 1, targets: [1] }, // Topic name
            { responsivePriority: 2, targets: [2] }, // Subject
            { responsivePriority: 3, targets: [8] }, // Actions
            { responsivePriority: 4, targets: [7] }, // Questions count
            { responsivePriority: 5, targets: [0] }  // ID
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search topics...",
            lengthMenu: "_MENU_ topics per page",
            info: "Showing _START_ to _END_ of _TOTAL_ topics",
            infoEmpty: "Showing 0 to 0 of 0 topics",
            infoFiltered: "(filtered from _MAX_ total topics)",
            zeroRecords: "No matching topics found",
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
    
    // Auto-submit filter form when subject changes (optional)
    $('#subject_id').change(function() {
        if ($(this).val()) {
            $(this).closest('form').submit();
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

/* ===== CUSTOM PAGINATION STYLES ===== */
.custom-pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    font-size: 14px;
}

.custom-pagination {
    display: flex;
    align-items: center;
}

.pagination-list {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.25rem;
    align-items: center;
}

.pagination-item {
    display: flex;
}

.pagination-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: white;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.pagination-link:hover {
    background: #f9fafb;
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.pagination-item.active .pagination-link {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination-item.disabled .pagination-link {
    background: #f9fafb;
    color: #9ca3af;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-link i {
    font-size: 18px;
    line-height: 1;
}

/* Mobile Responsive Pagination */
@media (max-width: 768px) {
    .custom-pagination-wrapper {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1rem;
    }
    
    .pagination-info {
        order: 2;
        font-size: 13px;
    }
    
    .custom-pagination {
        order: 1;
        width: 100%;
        justify-content: center;
    }
    
    .pagination-list {
        gap: 0.25rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination-link {
        min-width: 32px;
        height: 32px;
        font-size: 13px;
        padding: 0.375rem;
    }
    
    .pagination-link i {
        font-size: 16px;
    }
}

@media (max-width: 576px) {
    .pagination-link {
        min-width: 28px;
        height: 28px;
        font-size: 12px;
        padding: 0.25rem;
    }
    
    .pagination-link i {
        font-size: 14px;
    }
    
    .pagination-info {
        font-size: 12px;
    }
}

/* Fix container width for laptop screens */
@media (min-width: 992px) and (max-width: 1400px) {
    .admin-content .container-fluid {
        max-width: 100%;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
    
    .admin-table {
        font-size: 14px;
    }
    
    .admin-table th,
    .admin-table td {
        padding: 0.75rem 0.5rem;
    }
}

/* Mobile styles */
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
    
    /* Improve filter form on mobile */
    .row.g-3 {
        row-gap: 1rem !important;
    }
    
    .col-md-4 {
        margin-bottom: 0.5rem;
    }
}

/* Fix DataTables on medium screens */
@media (max-width: 1200px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        width: 200px !important;
    }
}

/* Ensure table doesn't overflow */
.table-responsive {
    -webkit-overflow-scrolling: touch;
    overflow-x: auto;
    max-width: 100%;
}
</style>
@endpush