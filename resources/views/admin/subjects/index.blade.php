@extends('layouts.admin')

@section('title', 'Subjects Management - A-plus CBT')
@section('page-title', 'Subjects Management')
@section('mobile-title', 'Subjects')

@section('breadcrumbs')
<li class="breadcrumb-item active">Subjects</li>
@endsection

@section('page-actions')
<a href="{{ route('admin.subjects.create') }}" class="btn-admin btn-admin-primary">
    <i class="ri-add-line me-2"></i> Add Subject
</a>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-body">
        @if($subjects->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-book-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Subjects Found</h5>
                <p class="text-muted">Get started by adding your first subject</p>
                <a href="{{ route('admin.subjects.create') }}" class="btn-admin btn-admin-primary mt-3">
                    <i class="ri-add-line me-2"></i> Add First Subject
                </a>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Subject</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Order</th>
                        <th>Questions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                    <tr>
                        <td>{{ $subject->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($subject->icon_class)
                                <i class="{{ $subject->icon_class }} text-primary"></i>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $subject->name }}</h6>
                                    @if($subject->created_at)
                                    <small class="text-muted">Added {{ $subject->created_at->format('M d, Y') }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $subject->code }}</span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $subject->description }}">
                                {{ $subject->description ?: 'No description' }}
                            </div>
                        </td>
                        <td>
                            @if($subject->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $subject->order }}</span>
                        </td>
                        <td>
                            <span class="fw-medium">{{ $subject->questions_count ?? 0 }}</span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.subjects.show', $subject) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $subject) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirmDelete(this, '{{ $subject->name }}')">
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
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form, subjectName) {
    if (confirm(`Are you sure you want to delete "${subjectName}"? This will also delete all associated topics and questions. This action cannot be undone.`)) {
        form.submit();
    }
    return false;
}

$(document).ready(function() {
    // Initialize DataTable with custom options
    $('.data-table').DataTable({
        responsive: true,
        order: [[5, 'asc']], // Order by order column
        columnDefs: [
            { orderable: false, targets: [7] }, // Disable sorting for actions column
            { responsivePriority: 1, targets: 1 }, // Subject name - highest priority
            { responsivePriority: 2, targets: 7 }, // Actions - second priority
            { responsivePriority: 3, targets: 0 }  // ID - third priority
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search subjects...",
            lengthMenu: "_MENU_ subjects per page",
            info: "Showing _START_ to _END_ of _TOTAL_ subjects",
            infoEmpty: "Showing 0 to 0 of 0 subjects",
            infoFiltered: "(filtered from _MAX_ total subjects)",
            zeroRecords: "No matching subjects found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
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
}

@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .table-actions .btn-admin {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
}
</style>
@endpush