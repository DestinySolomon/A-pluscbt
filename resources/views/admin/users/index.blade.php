@extends('layouts.admin')

@section('title', 'Users Management - A-plus CBT')
@section('page-title', 'Users Management')
@section('mobile-title', 'Users')

@section('breadcrumbs')
<li class="breadcrumb-item active">Users</li>
@endsection

@section('page-actions')
<div class="page-actions-wrapper">
    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="#" class="btn-admin btn-admin-secondary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
            <i class="ri-checkbox-multiple-line me-2"></i> Bulk Actions
        </a>
        
        <!-- Export Dropdown -->
        <div class="dropdown">
            <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="ri-download-2-line me-2"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="ri-file-excel-line me-2"></i> Export to Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="ri-file-text-line me-2"></i> Export to CSV
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search Users</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Search by name, email..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-3">
                <label for="sort_by" class="form-label">Sort By</label>
                <select name="sort_by" id="sort_by" class="form-select">
                    <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                </select>
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="card-body">
        @if($users->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-user-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Users Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['role', 'status', 'search', 'sort_by']))
                    Try adjusting your filters
                    @else
                    No users have registered yet
                    @endif
                </p>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th width="50">ID</th>
                        <th>User Details</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Performance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="form-check-input user-checkbox" 
                                   {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-decoration-none">
                                            {{ $user->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $user->email }}
                                        @if($user->created_at)
                                        • Joined {{ $user->created_at->format('M d, Y') }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-primary">Admin</span>
                            @else
                                <span class="badge bg-secondary">Student</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $user->exam_attempts_count }} attempts</span>
                                <small class="text-muted">{{ $user->results_count }} results</small>
                            </div>
                        </td>
                        <td>
                            @if($user->results_count > 0)
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ number_format($user->average_percentage, 1) }}%</span>
                                    <small class="text-muted">
                                        {{ $user->passed_results_count }}/{{ $user->results_count }} passed
                                    </small>
                                </div>
                            @else
                                <span class="text-muted">No data</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>
                                
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                
                                @if($user->is_active)
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-admin btn-admin-warning btn-sm" title="Deactivate">
                                            <i class="ri-user-unfollow-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-admin btn-admin-success btn-sm" title="Activate">
                                            <i class="ri-user-follow-line"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if($user->isAdmin())
                                    @if(auth()->id() !== $user->id && User::where('role', 'admin')->count() > 1)
                                        <form action="{{ route('admin.users.remove-admin', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-admin btn-admin-warning btn-sm" title="Remove Admin">
                                                <i class="ri-shield-user-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <form action="{{ route('admin.users.make-admin', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-admin btn-admin-primary btn-sm" title="Make Admin">
                                            <i class="ri-shield-star-line"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirmDelete('{{ addslashes($user->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn-admin btn-admin-danger btn-sm" 
                                                title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Custom Pagination -->
            @if($users->hasPages())
            <div class="custom-pagination-wrapper">
                <div class="pagination-info">
                    <span class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                    </span>
                </div>
                
                <nav class="custom-pagination" aria-label="Users pagination">
                    <ul class="pagination-list">
                        {{-- Previous Button --}}
                        @if ($users->onFirstPage())
                            <li class="pagination-item disabled">
                                <span class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $users->previousPageUrl() }}" class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach(range(1, $users->lastPage()) as $page)
                            @if($page == $users->currentPage())
                                <li class="pagination-item active">
                                    <span class="pagination-link">{{ $page }}</span>
                                </li>
                            @elseif($page == 1 || $page == $users->lastPage() || abs($page - $users->currentPage()) <= 2)
                                <li class="pagination-item">
                                    <a href="{{ $users->url($page) }}" class="pagination-link">{{ $page }}</a>
                                </li>
                            @elseif(abs($page - $users->currentPage()) == 3)
                                <li class="pagination-item disabled">
                                    <span class="pagination-link">...</span>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($users->hasMorePages())
                            <li class="pagination-item">
                                <a href="{{ $users->nextPageUrl() }}" class="pagination-link">
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

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.bulk-action') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Selected Users: <span id="selectedCount">0</span></label>
                        <div id="selectedUsersList" class="selected-users-list"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bulkAction" class="form-label">Action to Perform</label>
                        <select name="action" id="bulkAction" class="form-select" required>
                            <option value="">Select Action</option>
                            <option value="activate">Activate Selected Users</option>
                            <option value="deactivate">Deactivate Selected Users</option>
                            <option value="makestudent">Remove Admin Role (Make Student)</option>
                            <option value="delete" class="text-danger">Delete Selected Users</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning" id="bulkActionWarning">
                        <i class="ri-alert-line me-2"></i>
                        <span id="warningText"></span>
                    </div>
                    
                    <input type="hidden" name="users" id="selectedUsersInput">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary" id="submitBulkAction">
                        <i class="ri-check-line me-2"></i> Apply Action
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.user-avatar-sm {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.selected-users-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.5rem;
    background: #f9fafb;
}

.selected-user-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.selected-user-item:last-child {
    border-bottom: none;
}

.selected-user-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #14b8a6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 600;
}

/* Table improvements */
.table-actions {
    min-width: 200px;
}

/* Mobile responsive page actions */
.page-actions-wrapper {
    width: 100%;
}

@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .user-avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    
    .page-actions-wrapper .d-flex {
        flex-direction: column !important;
    }
    
    .page-actions-wrapper .d-flex .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    
    .page-actions-wrapper .d-flex > div {
        width: 100%;
    }
    
    .page-actions-wrapper .btn-admin {
        width: 100%;
        justify-content: center;
    }
    
    .page-actions-wrapper .dropdown {
        width: 100%;
    }
    
    .page-actions-wrapper .dropdown .btn-admin {
        width: 100%;
    }
}

/* Desktop layout */
@media (min-width: 768px) {
    .page-actions-wrapper .d-flex {
        align-items: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(userName) {
    return confirm(`Are you sure you want to delete "${userName}"? All their exam attempts and results will be permanently deleted. This action cannot be undone.`);
}

$(document).ready(function() {
    // Initialize DataTable
    $('.data-table').DataTable({
        responsive: true,
        order: [[1, 'desc']], // Order by ID descending (skip checkbox column)
        columnDefs: [
            { orderable: false, targets: [0, 7] }, // Disable sorting for checkbox and actions
            { responsivePriority: 1, targets: [2] }, // User details
            { responsivePriority: 2, targets: [7] }, // Actions
            { responsivePriority: 3, targets: [6] }, // Status
            { responsivePriority: 4, targets: [1] }, // ID
            { responsivePriority: 5, targets: [3] }, // Role
            { responsivePriority: 6, targets: [4, 5] } // Activity & Performance
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users...",
            lengthMenu: "_MENU_ users per page",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            infoEmpty: "Showing 0 to 0 of 0 users",
            infoFiltered: "(filtered from _MAX_ total users)",
            zeroRecords: "No matching users found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        searching: false,
        paging: false,
        info: false
    });
    
    // Select all checkboxes
    $('#selectAll').change(function() {
        $('.user-checkbox:not(:disabled)').prop('checked', this.checked);
        updateSelectedUsers();
    });
    
    // Update selected users when checkboxes change
    $('.user-checkbox').change(function() {
        updateSelectedUsers();
    });
    
    // Update bulk action modal
    function updateSelectedUsers() {
        const selectedUsers = [];
        const selectedNames = [];
        
        $('.user-checkbox:checked:not(:disabled)').each(function() {
            const userId = $(this).val();
            const userName = $(this).closest('tr').find('h6 a').text().trim();
            const userEmail = $(this).closest('tr').find('small.text-muted').text().split('•')[0].trim();
            
            selectedUsers.push(userId);
            selectedNames.push({name: userName, email: userEmail, id: userId});
        });
        
        $('#selectedCount').text(selectedUsers.length);
        $('#selectedUsersInput').val(selectedUsers.join(','));
        
        // Update selected users list
        const usersList = $('#selectedUsersList');
        usersList.empty();
        
        if (selectedNames.length > 0) {
            selectedNames.forEach(user => {
                usersList.append(`
                    <div class="selected-user-item">
                        <div class="selected-user-avatar">${user.name.substring(0, 2).toUpperCase()}</div>
                        <div class="d-flex flex-column">
                            <small class="fw-medium">${user.name}</small>
                            <small class="text-muted">${user.email}</small>
                        </div>
                    </div>
                `);
            });
        } else {
            usersList.html('<small class="text-muted">No users selected</small>');
        }
        
        // Update warning text based on action
        updateWarningText();
    }
    
    // Update warning text based on selected action
    $('#bulkAction').change(updateWarningText);
    
    function updateWarningText() {
        const action = $('#bulkAction').val();
        const count = parseInt($('#selectedCount').text());
        let warningText = '';
        
        switch(action) {
            case 'delete':
                warningText = `This will permanently delete ${count} user(s) and all their data. This action cannot be undone.`;
                break;
            case 'makestudent':
                warningText = `This will remove admin privileges from ${count} user(s).`;
                break;
            case 'activate':
            case 'deactivate':
                warningText = `This will ${action} ${count} user(s).`;
                break;
            default:
                warningText = 'Please select an action to see details.';
        }
        
        $('#warningText').text(warningText);
        $('#bulkActionWarning').toggleClass('d-none', !action);
    }
    
    // Auto-submit filter form when filter changes
    $('#role, #status, #sort_by').change(function() {
        if ($(this).val()) {
            $(this).closest('form').submit();
        }
    });
    
    // Initialize
    updateSelectedUsers();
});
</script>
@endpush