@extends('layouts.admin')

@section('title', 'Edit User - A-plus CBT')
@section('page-title', 'Edit User')
@section('mobile-title', 'Edit User')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.users.show', $user->id) }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Back to User
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Edit User Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="role" class="form-label">Role *</label>
                                <select name="role" 
                                        id="role" 
                                        class="form-select @error('role') is-invalid @enderror" 
                                        required>
                                    <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="is_active" class="form-label">Account Status</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           id="is_active" 
                                           class="form-check-input" 
                                           value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label for="is_active" class="form-check-label">
                                        Active Account
                                    </label>
                                </div>
                                <small class="text-muted">Inactive users cannot log in to the system.</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Change Section -->
                    <div class="admin-card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Change Password (Optional)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" 
                                               name="password" 
                                               id="password" 
                                               class="form-control @error('password') is-invalid @enderror">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Danger Zone -->
                    @if(auth()->id() !== $user->id)
                    <div class="admin-card mt-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Danger Zone</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Delete This User</h6>
                                    <p class="text-muted mb-0">
                                        Once deleted, all user data including exam attempts and results will be permanently removed.
                                    </p>
                                </div>
                                <div>
                                    <button type="button" 
                                            class="btn-admin btn-admin-danger" 
                                            onclick="confirmDelete()">
                                        <i class="ri-delete-bin-line me-2"></i> Delete User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn-admin btn-admin-primary">
                            <i class="ri-save-line me-2"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn-admin btn-admin-secondary">
                            <i class="ri-close-line me-2"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">User Summary</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="user-avatar-lg mb-3 mx-auto">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>
                
                <div class="user-stats">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Role:</span>
                        <span>
                            @if($user->isAdmin())
                                <span class="badge bg-primary">Admin</span>
                            @else
                                <span class="badge bg-secondary">Student</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Status:</span>
                        <span>
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Member Since:</span>
                        <span class="fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Exam Attempts:</span>
                        <span class="fw-medium">{{ $user->exam_attempts_count ?? 0 }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Average Score:</span>
                        <span class="fw-medium">{{ number_format($user->average_percentage, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Role Information</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    <strong>Admin Role:</strong> Can access admin panel, manage all content, and view all user data.
                </div>
                
                <div class="alert alert-secondary">
                    <i class="ri-user-line me-2"></i>
                    <strong>Student Role:</strong> Can take exams, view their own results, and access learning materials.
                </div>
                
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    <strong>Note:</strong> At least one admin user must remain in the system at all times.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $user->name }}</strong>?</p>
                <div class="alert alert-danger">
                    <i class="ri-alert-line me-2"></i>
                    This action will permanently delete all user data including:
                    <ul class="mb-0 mt-2">
                        <li>All exam attempts</li>
                        <li>All results and scores</li>
                        <li>User profile and settings</li>
                    </ul>
                </div>
                <p class="mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-admin btn-admin-danger">Delete User Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.user-avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 28px;
}

.user-stats {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1.5rem;
}

.border-danger {
    border: 2px solid #dc3545 !important;
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush