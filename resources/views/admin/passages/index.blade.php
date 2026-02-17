@extends('layouts.admin')

@section('title', 'Passages Management - A-plus CBT')
@section('page-title', 'Comprehension Passages')
@section('mobile-title', 'Passages')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Passages</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.passages.create') }}" class="btn-admin btn-admin-primary">
        <i class="ri-add-line me-2"></i> Add New Passage
    </a>
    
    <!-- Bulk Actions Dropdown -->
    <div class="dropdown">
        <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="ri-more-2-line me-2"></i> Bulk Actions
        </button>
        <ul class="dropdown-menu">
            <li>
                <button type="button" class="dropdown-item" onclick="confirmBulkAction('activate')">
                    <i class="ri-checkbox-circle-line me-2 text-success"></i> Activate Selected
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item" onclick="confirmBulkAction('deactivate')">
                    <i class="ri-close-circle-line me-2 text-warning"></i> Deactivate Selected
                </button>
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
        <form method="GET" action="{{ route('admin.passages.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="subject_id" class="form-label">Subject</label>
                <select name="subject_id" id="subject_id" class="form-select">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Search by title, content, source..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.passages.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" action="{{ route('admin.passages.bulk-action') }}">
    @csrf
    <input type="hidden" name="action" id="bulkActionType">
    <input type="hidden" name="passage_ids" id="selectedPassageIds">
</form>

<!-- Passages List -->
<div class="admin-card">
    <div class="card-body">
        @if($passages->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-file-text-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Passages Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['subject_id', 'status', 'search']))
                    Try adjusting your filters
                    @else
                    Get started by adding your first comprehension passage
                    @endif
                </p>
                <a href="{{ route('admin.passages.create') }}" class="btn-admin btn-admin-primary mt-3">
                    <i class="ri-add-line me-2"></i> Add First Passage
                </a>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>ID</th>
                        <th>Passage Details</th>
                        <th>Subject</th>
                        <th>Questions</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passages as $passage)
                    <tr>
                        <td>
                            <input type="checkbox" name="passage_ids[]" value="{{ $passage->id }}" 
                                   class="form-check-input passage-checkbox">
                        </td>
                        <td>{{ $passage->id }}</td>
                        <td>
                            <div class="d-flex align-items-start gap-2">
                                @if($passage->image_path)
                                <div class="flex-shrink-0">
                                    <div class="passage-thumbnail">
                                        <img src="{{ Storage::url($passage->image_path) }}" 
                                             alt="Passage image" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    </div>
                                </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.passages.show', $passage) }}" class="text-decoration-none">
                                            {{ $passage->title ?: 'Untitled Passage' }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        {{ Str::limit(strip_tags($passage->content), 100) }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $passage->subject->name ?? 'N/A' }}</span>
                            @if($passage->topic)
                            <br><small class="text-muted">{{ $passage->topic->name }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $passage->questions_count }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $passage->source ?: '—' }}</small>
                        </td>
                        <td>
                            @if($passage->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.passages.show', $passage) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('admin.passages.edit', $passage) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('admin.passages.toggle-status', $passage) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn-admin btn-admin-{{ $passage->is_active ? 'warning' : 'success' }} btn-sm"
                                            title="{{ $passage->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="ri-{{ $passage->is_active ? 'pause' : 'play' }}-line"></i>
                                    </button>
                                </form>
                                <button type="button" 
                                        class="btn-admin btn-admin-danger btn-sm"
                                        onclick="confirmDelete('{{ $passage->title ?: 'Passage' }}', {{ $passage->id }})"
                                        title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Delete Form (hidden) -->
        <form id="deleteForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $passages->firstItem() }} to {{ $passages->lastItem() }} of {{ $passages->total() }} passages
            </div>
            {{ $passages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(passageTitle, passageId) {
    if (confirm(`Are you sure you want to delete "${passageTitle}"? This action cannot be undone.`)) {
        const form = document.getElementById('deleteForm');
        form.action = `/admin/passages/${passageId}`;
        form.submit();
    }
}

function confirmBulkAction(action) {
    const selectedIds = getSelectedPassageIds();
    
    if (selectedIds.length === 0) {
        alert('Please select at least one passage.');
        return;
    }
    
    let actionText = action === 'delete' ? 'delete' : (action === 'activate' ? 'activate' : 'deactivate');
    let confirmText = `Are you sure you want to ${actionText} ${selectedIds.length} selected passage(s)?`;
    
    if (action === 'delete') {
        confirmText += ' This action cannot be undone.';
    }
    
    if (confirm(confirmText)) {
        document.getElementById('bulkActionType').value = action;
        document.getElementById('selectedPassageIds').value = JSON.stringify(selectedIds);
        document.getElementById('bulkActionForm').submit();
    }
}

function getSelectedPassageIds() {
    const checkboxes = document.querySelectorAll('.passage-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

$(document).ready(function() {
    // Select all checkboxes
    $('#selectAll').change(function() {
        $('.passage-checkbox').prop('checked', this.checked);
    });
    
    // Update select all checkbox when individual checkboxes change
    $('.passage-checkbox').change(function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        } else {
            const allChecked = $('.passage-checkbox:checked').length === $('.passage-checkbox').length;
            $('#selectAll').prop('checked', allChecked);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.passage-thumbnail {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}
.table-actions {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}
.empty-state {
    padding: 3rem 1rem;
}
</style>
@endpush