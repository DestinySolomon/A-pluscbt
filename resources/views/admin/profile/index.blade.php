@extends('layouts.admin')

@section('title', 'Profile Settings')

@section('page-title', 'Profile Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Profile Settings</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="admin-card">
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        @if(Auth::user()->profile_image_url)
                            <img src="{{ Auth::user()->profile_image_url }}" 
                                 alt="{{ Auth::user()->name }}" 
                                 class="user-avatar-lg rounded-circle border border-4 border-white shadow">
                        @else
                            <div class="user-avatar-lg border border-4 border-white shadow">
                                {{ Auth::user()->initials }}
                            </div>
                        @endif
                        
                        <!-- Image Upload Form -->
                        <form id="profileImageForm" action="{{ route('admin.profile.image') }}" method="POST" enctype="multipart/form-data" class="position-absolute bottom-0 end-0">
                            @csrf
                            <input type="file" id="profileImageInput" name="profile_image" accept="image/*" style="display: none;">
                            <label for="profileImageInput" class="btn btn-primary btn-sm rounded-circle p-2 shadow-sm cursor-pointer">
                                <i class="ri-camera-line"></i>
                            </label>
                        </form>
                        
                        @if(Auth::user()->profile_image_url)
                            <form id="removeImageForm" action="{{ route('admin.profile.image.remove') }}" method="POST" class="position-absolute top-0 end-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-circle p-1 shadow-sm" 
                                        onclick="return confirm('Remove profile image?')">
                                    <i class="ri-close-line"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <h4 class="mt-3 mb-1">{{ Auth::user()->name }}</h4>
                    <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                    <span class="badge bg-primary mt-2">Administrator</span>
                </div>
                
                <!-- Quick Stats -->
                <div class="user-meta mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Member Since</span>
                        <span class="fw-medium">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Last Updated</span>
                        <span class="fw-medium">{{ Auth::user()->updated_at->format('M d, Y') }}</span>
                    </div>
                    @if(Auth::user()->phone)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Phone</span>
                        <span class="fw-medium">{{ Auth::user()->phone }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Danger Zone -->
                <div class="border border-danger rounded p-3">
                    <h6 class="text-danger mb-3"><i class="ri-alert-line me-2"></i>Danger Zone</h6>
                    <button class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="ri-delete-bin-line me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Settings Tabs -->
            <div class="admin-card">
                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('active_tab', 'profile') == 'profile' ? 'active' : '' }}" 
                                id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                            <i class="ri-user-line me-2"></i>Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('active_tab') == 'social' ? 'active' : '' }}" 
                                id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button">
                            <i class="ri-share-line me-2"></i>Social Links
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('active_tab') == 'notifications' ? 'active' : '' }}" 
                                id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button">
                            <i class="ri-notification-3-line me-2"></i>Notifications
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('active_tab') == 'password' ? 'active' : '' }}" 
                                id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button">
                            <i class="ri-lock-line me-2"></i>Password
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="settingsTabContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade {{ session('active_tab', 'profile') == 'profile' ? 'show active' : '' }}" 
                         id="profile" role="tabpanel">
                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" name="name" class="form-control" 
                                               value="{{ old('name', Auth::user()->name) }}" required>
                                        @error('name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email Address *</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="{{ old('email', Auth::user()->email) }}" required>
                                        @error('email')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone" class="form-control" 
                                               value="{{ old('phone', Auth::user()->phone) }}">
                                        @error('phone')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="Administrator" readonly disabled>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="4" 
                                          placeholder="Tell us about yourself...">{{ old('bio', Auth::user()->bio) }}</textarea>
                                @error('bio')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Max 500 characters</small>
                            </div>
                            
                            <button type="submit" class="btn btn-admin btn-admin-primary">
                                <i class="ri-save-line me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>
                    
                    <!-- Social Links Tab -->
                    <div class="tab-pane fade {{ session('active_tab') == 'social' ? 'show active' : '' }}" 
                         id="social" role="tabpanel">
                        <form action="{{ route('admin.profile.social') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-facebook-fill text-primary me-2"></i>Facebook
                                </label>
                                <input type="url" name="facebook_url" class="form-control" 
                                       value="{{ old('facebook_url', Auth::user()->facebook_url) }}" 
                                       placeholder="https://facebook.com/username">
                                @error('facebook_url')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-twitter-fill text-info me-2"></i>Twitter
                                </label>
                                <input type="url" name="twitter_url" class="form-control" 
                                       value="{{ old('twitter_url', Auth::user()->twitter_url) }}" 
                                       placeholder="https://twitter.com/username">
                                @error('twitter_url')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-linkedin-fill text-primary me-2"></i>LinkedIn
                                </label>
                                <input type="url" name="linkedin_url" class="form-control" 
                                       value="{{ old('linkedin_url', Auth::user()->linkedin_url) }}" 
                                       placeholder="https://linkedin.com/in/username">
                                @error('linkedin_url')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-instagram-fill text-danger me-2"></i>Instagram
                                </label>
                                <input type="url" name="instagram_url" class="form-control" 
                                       value="{{ old('instagram_url', Auth::user()->instagram_url) }}" 
                                       placeholder="https://instagram.com/username">
                                @error('instagram_url')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-admin btn-admin-primary">
                                <i class="ri-save-line me-2"></i>Save Social Links
                            </button>
                        </form>
                    </div>
                    
                    <!-- Notifications Tab -->
                    <div class="tab-pane fade {{ session('active_tab') == 'notifications' ? 'show active' : '' }}" 
                         id="notifications" role="tabpanel">
                        <form action="{{ route('admin.profile.notifications') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="alert alert-info">
                                <i class="ri-information-line me-2"></i>
                                Control what types of notifications you want to receive.
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="email_notifications" 
                                       id="emailNotifications" {{ Auth::user()->email_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="emailNotifications">
                                    <strong>Email Notifications</strong>
                                    <p class="text-muted small mb-0">Receive notifications via email</p>
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="exam_notifications" 
                                       id="examNotifications" {{ Auth::user()->exam_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="examNotifications">
                                    <strong>Exam Notifications</strong>
                                    <p class="text-muted small mb-0">Get notified about new exams and updates</p>
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="result_notifications" 
                                       id="resultNotifications" {{ Auth::user()->result_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="resultNotifications">
                                    <strong>Result Notifications</strong>
                                    <p class="text-muted small mb-0">Receive notifications when results are published</p>
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="system_notifications" 
                                       id="systemNotifications" {{ Auth::user()->system_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="systemNotifications">
                                    <strong>System Notifications</strong>
                                    <p class="text-muted small mb-0">Important system updates and announcements</p>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-admin btn-admin-primary">
                                <i class="ri-save-line me-2"></i>Save Preferences
                            </button>
                        </form>
                    </div>
                    
                    <!-- Password Tab -->
                    <div class="tab-pane fade {{ session('active_tab') == 'password' ? 'show active' : '' }}" 
                         id="password" role="tabpanel">
                        <form action="{{ route('admin.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="alert alert-warning">
                                <i class="ri-alert-line me-2"></i>
                                Changing your password will log you out from all other devices.
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Current Password *</label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">New Password *</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                        @error('new_password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Confirm New Password *</label>
                                        <input type="password" name="new_password_confirmation" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="ri-information-line me-2"></i>
                                Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.
                            </div>
                            
                            <button type="submit" class="btn btn-admin btn-admin-primary">
                                <i class="ri-lock-line me-2"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger">
                        <i class="ri-alert-line me-2"></i>Delete Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. All your data will be permanently deleted.
                    </div>
                    <p>Are you sure you want to delete your account? This will:</p>
                    <ul>
                        <li>Permanently delete your profile</li>
                        <li>Remove all your associated data</li>
                        <li>Cancel any ongoing exams</li>
                        <li>Delete all your results and history</li>
                    </ul>
                    <div class="form-group">
                        <label class="form-label">Please type "DELETE" to confirm</label>
                        <input type="text" class="form-control" id="deleteConfirm" placeholder="Type DELETE here">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="ri-delete-bin-line me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Profile image upload preview
    document.getElementById('profileImageInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const form = document.getElementById('profileImageForm');
            form.submit();
        }
    });

    // Auto-submit image form on file selection
    document.getElementById('profileImageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        this.submit();
    });

    // Delete account confirmation
    document.getElementById('deleteConfirm').addEventListener('input', function() {
        const deleteBtn = document.getElementById('confirmDeleteBtn');
        deleteBtn.disabled = this.value.toUpperCase() !== 'DELETE';
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        // Implement account deletion logic here
        alert('Account deletion functionality to be implemented. This is a demo.');
    });

    // Tab persistence
    const hash = window.location.hash;
    if (hash) {
        const triggerTab = document.querySelector(`[data-bs-target="${hash}"]`);
        if (triggerTab) {
            new bootstrap.Tab(triggerTab).show();
        }
    }
</script>
@endpush