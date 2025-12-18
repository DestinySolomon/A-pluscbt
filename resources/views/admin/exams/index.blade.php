@extends('layouts.admin')

@section('title', 'Exams Management - A-plus CBT')
@section('page-title', 'Exams Management')
@section('mobile-title', 'Exams')

@section('breadcrumbs')
<li class="breadcrumb-item active">Exams</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.exams.create') }}" class="btn-admin btn-admin-primary">
        <i class="ri-add-line me-2"></i> Create Exam
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
        <a class="dropdown-item" href="{{ route('admin.exams.export-all') }}">
            <i class="ri-download-line me-2"></i> Export All to CSV
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="{{ route('admin.exams.export-all', ['format' => 'json']) }}">
            <i class="ri-code-s-slash-line me-2"></i> Export All as JSON
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
        <form method="GET" action="{{ route('admin.exams.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="type" class="form-label">Exam Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="full_jamb" {{ request('type') == 'full_jamb' ? 'selected' : '' }}>Full JAMB</option>
                    <option value="subject_test" {{ request('type') == 'subject_test' ? 'selected' : '' }}>Subject Test</option>
                    <option value="topic_test" {{ request('type') == 'topic_test' ? 'selected' : '' }}>Topic Test</option>
                    <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search Exams</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Search by name, code..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-3">
                <label for="sort_by" class="form-label">Sort By</label>
                <select name="sort_by" id="sort_by" class="form-select">
                    <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="popular" {{ request('sort_by') == 'popular' ? 'selected' : '' }}>Most Attempted</option>
                </select>
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.exams.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="card-body">
        @if($exams->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-file-list-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Exams Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['type', 'status', 'search', 'sort_by']))
                    Try adjusting your filters
                    @else
                    Get started by creating your first JAMB exam
                    @endif
                </p>
                <a href="{{ route('admin.exams.create') }}" class="btn-admin btn-admin-primary mt-3">
                    <i class="ri-add-line me-2"></i> Create First Exam
                </a>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Exam Details</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Questions</th>
                        <th>Attempts</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    <tr>
                        <td>{{ $exam->id }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1">
                                    <a href="{{ route('admin.exams.show', $exam->id) }}" class="text-decoration-none">
                                        {{ $exam->name }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <code>{{ $exam->code }}</code>
                                    @if($exam->created_at)
                                    • Created {{ $exam->created_at->format('M d, Y') }}
                                    @endif
                                </small>
                                @if($exam->description)
                                <small class="text-truncate" style="max-width: 300px;" title="{{ $exam->description }}">
                                    {{ Str::limit($exam->description, 80) }}
                                </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($exam->type == 'full_jamb')
                                <span class="badge bg-primary">Full JAMB</span>
                            @elseif($exam->type == 'subject_test')
                                <span class="badge bg-info">Subject Test</span>
                            @elseif($exam->type == 'topic_test')
                                <span class="badge bg-success">Topic Test</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($exam->type) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium">{{ $exam->duration_minutes }} min</span>
                        </td>
                        <td>
                            <span class="fw-medium">{{ $exam->total_questions }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $exam->attempts_count }}</span>
                                @if($exam->completed_attempts_count > 0)
                                <small class="text-muted">{{ $exam->completed_attempts_count }} completed</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($exam->is_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-warning">Draft</span>
                            @endif
                            @if(!$exam->is_active)
                                <span class="badge bg-danger mt-1">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.exams.show', $exam->id) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>

                                  <!-- ADD THIS: Export single exam -->
             <a href="{{ route('admin.exams.export', $exam->id) }}" 
               class="btn-admin btn-admin-secondary btn-sm" 
             title="Export This Exam">
               <i class="ri-download-line"></i>
             </a>

                                <a href="{{ route('admin.exams.edit', $exam->id) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                
                                @if($exam->is_published)
                                    <form action="{{ route('admin.exams.unpublish', $exam->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-admin btn-admin-warning btn-sm" title="Unpublish">
                                            <i class="ri-eye-off-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.exams.publish', $exam->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-admin btn-admin-success btn-sm" title="Publish">
                                            <i class="ri-check-line"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.exams.duplicate', $exam->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-admin btn-admin-info btn-sm" title="Duplicate">
                                        <i class="ri-file-copy-line"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.exams.destroy', $exam->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirmDelete(this, '{{ addslashes($exam->name) }}')">
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
            @if($exams->hasPages())
            <div class="custom-pagination-wrapper">
                <div class="pagination-info">
                    <span class="text-muted">
                        Showing {{ $exams->firstItem() }} to {{ $exams->lastItem() }} of {{ $exams->total() }} exams
                    </span>
                </div>
                
                <nav class="custom-pagination" aria-label="Exams pagination">
                    <ul class="pagination-list">
                        {{-- Previous Button --}}
                        @if ($exams->onFirstPage())
                            <li class="pagination-item disabled">
                                <span class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $exams->previousPageUrl() }}" class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach(range(1, $exams->lastPage()) as $page)
                            @if($page == $exams->currentPage())
                                <li class="pagination-item active">
                                    <span class="pagination-link">{{ $page }}</span>
                                </li>
                            @elseif($page == 1 || $page == $exams->lastPage() || abs($page - $exams->currentPage()) <= 2)
                                <li class="pagination-item">
                                    <a href="{{ $exams->url($page) }}" class="pagination-link">{{ $page }}</a>
                                </li>
                            @elseif(abs($page - $exams->currentPage()) == 3)
                                <li class="pagination-item disabled">
                                    <span class="pagination-link">...</span>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($exams->hasMorePages())
                            <li class="pagination-item">
                                <a href="{{ $exams->nextPageUrl() }}" class="pagination-link">
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

<!-- Quick Actions Modal -->
<div class="modal fade" id="quickActionsModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="ri-download-line me-2"></i> Export Selected
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="ri-toggle-line me-2"></i> Toggle Active Status
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="ri-eye-line me-2"></i> Bulk Publish
                    </a>
                    <a href="#" class="list-group-item list-group-item-action text-danger">
                        <i class="ri-delete-bin-line me-2"></i> Bulk Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.exams.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Exams from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">CSV File *</label>
                        <input type="file" name="import_file" id="import_file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">
                            File should include: name, code, description, type, duration_minutes, passing_score, etc.
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
                        <i class="ri-upload-line me-2"></i> Import Exams
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form, examName) {
    if (confirm(`Are you sure you want to delete "${examName}"? This will also delete all associated attempts and results. This action cannot be undone.`)) {
        form.submit();
    }
    return false;
}

$(document).ready(function() {
    // Initialize DataTable
    $('.data-table').DataTable({
        responsive: true,
        order: [[0, 'desc']], // Order by ID descending
        columnDefs: [
            { orderable: false, targets: [7] }, // Disable sorting for actions column
            { responsivePriority: 1, targets: [1] }, // Exam details
            { responsivePriority: 2, targets: [7] }, // Actions
            { responsivePriority: 3, targets: [6] }, // Status
            { responsivePriority: 4, targets: [5] }, // Attempts
            { responsivePriority: 5, targets: [0] }  // ID
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search exams...",
            lengthMenu: "_MENU_ exams per page",
            info: "Showing _START_ to _END_ of _TOTAL_ exams",
            infoEmpty: "Showing 0 to 0 of 0 exams",
            infoFiltered: "(filtered from _MAX_ total exams)",
            zeroRecords: "No matching exams found",
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
    
    // Auto-submit filter form when filter changes (optional)
    $('#type, #status, #sort_by').change(function() {
        if ($(this).val()) {
            $(this).closest('form').submit();
        }
    });
    
    // Quick actions for selected rows
    $('.select-exam').change(function() {
        updateQuickActions();
    });
    
    function updateQuickActions() {
        const selectedCount = $('.select-exam:checked').length;
        $('#selectedCount').text(selectedCount);
    }
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
    min-width: 180px;
}

/* Badge styles */
.badge {
    font-size: 0.75em;
    font-weight: 500;
    padding: 0.25em 0.6em;
    border-radius: 4px;
}

.bg-primary {
    background-color: var(--primary-color) !important;
}

.bg-info {
    background-color: #0dcaf0 !important;
    color: #000;
}

.bg-success {
    background-color: #198754 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.bg-danger {
    background-color: #dc3545 !important;
}

/* Button admin styles - matching your existing system */
.btn-admin {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    border: 1px solid transparent;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
}

.btn-admin-primary {
    background-color: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.btn-admin-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

.btn-admin-secondary {
    background-color: #f8f9fa;
    color: #495057;
    border-color: #dee2e6;
}

.btn-admin-secondary:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.btn-admin-success {
    background-color: #198754;
    color: white;
    border-color: #198754;
}

.btn-admin-success:hover {
    background-color: #157347;
    border-color: #146c43;
}

.btn-admin-warning {
    background-color: #ffc107;
    color: #000;
    border-color: #ffc107;
}

.btn-admin-warning:hover {
    background-color: #ffca2c;
    border-color: #ffc720;
}

.btn-admin-danger {
    background-color: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-admin-danger:hover {
    background-color: #bb2d3b;
    border-color: #b02a37;
}

.btn-admin-info {
    background-color: #0dcaf0;
    color: #000;
    border-color: #0dcaf0;
}

.btn-admin-info:hover {
    background-color: #31d2f2;
    border-color: #25cff2;
}

.btn-admin-sm {
    padding: 0.25rem 0.5rem;
    font-size: 12px;
    min-width: 32px;
    height: 32px;
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
    
    .row.g-3 {
        row-gap: 1rem !important;
    }
    
    .col-md-3 {
        margin-bottom: 0.5rem;
    }
    
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

/* Ensure table doesn't overflow */
.table-responsive {
    -webkit-overflow-scrolling: touch;
    overflow-x: auto;
    max-width: 100%;
}

/* Exam type badges */
.bg-primary { background-color: #3B71CA !important; }
.bg-info { background-color: #54B4D3 !important; color: #000; }
.bg-success { background-color: #14A44D !important; }
.bg-secondary { background-color: #9FA6B2 !important; color: #000; }
.bg-warning { background-color: #E4A11B !important; color: #000; }
.bg-danger { background-color: #DC4C64 !important; }
</style>
@endpush