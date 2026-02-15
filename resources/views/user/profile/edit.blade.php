{{-- resources/views/user/profile/edit.blade.php --}}
@extends('layouts.user-dashboard')

@section('title', 'Profile Settings')
@section('page-title', 'My Profile')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Profile Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4 col-xl-3 mb-4">
        <!-- Profile Card -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <!-- Profile Photo -->
                <div class="profile-photo-wrapper mb-4">
                    <div class="profile-photo mx-auto position-relative">
                        @if(auth()->user()->profile_image_url)
                            <img src="{{ auth()->user()->profile_image_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="img-thumbnail rounded-circle border-4">
                        @else
                            <div class="profile-initials rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto">
                                <span class="display-5 fw-bold">{{ auth()->user()->initials }}</span>
                            </div>
                        @endif
                        
                        <!-- Photo Upload Button -->
                        <button type="button" 
                                class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0"
                                data-bs-toggle="modal" 
                                data-bs-target="#uploadPhotoModal">
                            <i class="ri-camera-line"></i>
                        </button>
                    </div>
                </div>
                
                <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                
                @if(auth()->user()->phone)
                    <p class="mb-2">
                        <i class="ri-phone-line me-2 text-primary"></i>
                        {{ auth()->user()->phone }}
                    </p>
                @endif
                
                <div class="badge bg-primary-subtle text-primary mb-4 px-3 py-2 rounded-pill">
                    <i class="ri-award-line me-1"></i>
                    {{ ucfirst(auth()->user()->role) }}
                </div>
                
                <!-- Stats -->
                <div class="border-top pt-4 mt-4">
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="mb-1" id="completedExamsCount">0</h5>
                            <small class="text-muted">Exams Taken</small>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-1" id="averageScore">0%</h5>
                            <small class="text-muted">Avg. Score</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Social Links Card -->
        @if(auth()->user()->facebook_url || auth()->user()->twitter_url || auth()->user()->linkedin_url || auth()->user()->instagram_url)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0"><i class="ri-links-line me-2"></i> Social Links</h6>
            </div>
            <div class="card-body">
                <div class="social-links">
                    @if(auth()->user()->facebook_url)
                        <a href="{{ auth()->user()->facebook_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle me-2">
                            <i class="ri-facebook-fill"></i>
                        </a>
                    @endif
                    @if(auth()->user()->twitter_url)
                        <a href="{{ auth()->user()->twitter_url }}" target="_blank" class="btn btn-outline-info btn-sm rounded-circle me-2">
                            <i class="ri-twitter-fill"></i>
                        </a>
                    @endif
                    @if(auth()->user()->linkedin_url)
                        <a href="{{ auth()->user()->linkedin_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle me-2">
                            <i class="ri-linkedin-fill"></i>
                        </a>
                    @endif
                    @if(auth()->user()->instagram_url)
                        <a href="{{ auth()->user()->instagram_url }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-circle">
                            <i class="ri-instagram-fill"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-8 col-xl-9">
        <!-- Update Profile Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0"><i class="ri-user-settings-line me-2"></i> Profile Information</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-checkbox-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-fill me-2"></i>
                        Please fix the following errors:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary"><i class="ri-user-line me-2"></i> Personal Information</h6>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', auth()->user()->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', auth()->user()->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       placeholder="+234 800 000 0000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" 
                                          name="bio" 
                                          rows="3"
                                          placeholder="Tell us a little about yourself...">{{ old('bio', auth()->user()->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Social Links -->
                        <div class="col-md-6">
                            <h6 class="mb-3 text-primary"><i class="ri-links-line me-2"></i> Social Media Links</h6>
                            
                            <div class="mb-3">
                                <label for="facebook_url" class="form-label">
                                    <i class="ri-facebook-fill text-primary me-2"></i>Facebook
                                </label>
                                <input type="url" 
                                       class="form-control @error('facebook_url') is-invalid @enderror" 
                                       id="facebook_url" 
                                       name="facebook_url" 
                                       value="{{ old('facebook_url', auth()->user()->facebook_url) }}"
                                       placeholder="https://facebook.com/yourusername">
                                @error('facebook_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="twitter_url" class="form-label">
                                    <i class="ri-twitter-fill text-info me-2"></i>Twitter/X
                                </label>
                                <input type="url" 
                                       class="form-control @error('twitter_url') is-invalid @enderror" 
                                       id="twitter_url" 
                                       name="twitter_url" 
                                       value="{{ old('twitter_url', auth()->user()->twitter_url) }}"
                                       placeholder="https://twitter.com/yourusername">
                                @error('twitter_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="linkedin_url" class="form-label">
                                    <i class="ri-linkedin-fill text-primary me-2"></i>LinkedIn
                                </label>
                                <input type="url" 
                                       class="form-control @error('linkedin_url') is-invalid @enderror" 
                                       id="linkedin_url" 
                                       name="linkedin_url" 
                                       value="{{ old('linkedin_url', auth()->user()->linkedin_url) }}"
                                       placeholder="https://linkedin.com/in/yourusername">
                                @error('linkedin_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="instagram_url" class="form-label">
                                    <i class="ri-instagram-fill text-danger me-2"></i>Instagram
                                </label>
                                <input type="url" 
                                       class="form-control @error('instagram_url') is-invalid @enderror" 
                                       id="instagram_url" 
                                       name="instagram_url" 
                                       value="{{ old('instagram_url', auth()->user()->instagram_url) }}"
                                       placeholder="https://instagram.com/yourusername">
                                @error('instagram_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Preferences -->
                    <div class="border-top pt-4 mt-4">
                        <h6 class="mb-3 text-primary"><i class="ri-notification-3-line me-2"></i> Notification Preferences</h6>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="email_notifications" 
                                           name="email_notifications" 
                                           value="1"
                                           {{ old('email_notifications', auth()->user()->email_notifications) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        Email Notifications
                                    </label>
                                    <small class="form-text text-muted d-block">Receive updates via email</small>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="exam_notifications" 
                                           name="exam_notifications" 
                                           value="1"
                                           {{ old('exam_notifications', auth()->user()->exam_notifications) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="exam_notifications">
                                        Exam Notifications
                                    </label>
                                    <small class="form-text text-muted d-block">Get notified about new exams</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="result_notifications" 
                                           name="result_notifications" 
                                           value="1"
                                           {{ old('result_notifications', auth()->user()->result_notifications) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="result_notifications">
                                        Result Notifications
                                    </label>
                                    <small class="form-text text-muted d-block">Get notified when results are ready</small>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="system_notifications" 
                                           name="system_notifications" 
                                           value="1"
                                           {{ old('system_notifications', auth()->user()->system_notifications) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="system_notifications">
                                        System Notifications
                                    </label>
                                    <small class="form-text text-muted d-block">Receive system updates and announcements</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-top pt-4 mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ri-save-line me-2"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resetForm">
                                    <i class="ri-refresh-line me-2"></i> Reset
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('dashboard') }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line me-2"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password Card -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0"><i class="ri-lock-line me-2"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.update') }}" id="passwordForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password *</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password *</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ri-lock-password-line me-2"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Profile Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" id="uploadPhotoForm">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="upload-area rounded-circle border border-dashed border-2 p-4 mx-auto" 
                             style="width: 150px; height: 150px; cursor: pointer;" 
                             id="uploadTrigger">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <i class="ri-upload-cloud-line display-4 text-muted"></i>
                                <small class="text-muted mt-2">Click to upload</small>
                            </div>
                        </div>
                        <input type="file" 
                               class="d-none" 
                               id="profile_image" 
                               name="profile_image" 
                               accept="image/*">
                        <small class="form-text text-muted d-block mt-2">Max 2MB. JPG, PNG, or GIF.</small>
                    </div>
                    
                    <div class="preview-container d-none text-center" id="previewContainer">
                        <img id="imagePreview" class="img-thumbnail rounded-circle mb-2" style="width: 150px; height: 150px;">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="changeImage">
                                <i class="ri-refresh-line me-1"></i> Change
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                <i class="ri-delete-bin-line me-1"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="savePhotoBtn" disabled>
                        <i class="ri-save-line me-1"></i> Save Photo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-photo {
        width: 150px;
        height: 150px;
    }
    
    .profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-initials {
        width: 150px;
        height: 150px;
        font-size: 48px;
        background: linear-gradient(135deg, var(--user-primary), var(--user-secondary));
    }
    
    .upload-area {
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }
    
    .upload-area:hover {
        background-color: #e9ecef;
        border-color: var(--user-primary) !important;
    }
    
    .form-check-input:checked {
        background-color: var(--user-primary);
        border-color: var(--user-primary);
    }
    
    .social-links .btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load user stats
    loadUserStats();
    
    // Reset form
    document.getElementById('resetForm').addEventListener('click', function() {
        if (confirm('Are you sure you want to reset all changes?')) {
            document.getElementById('profileForm').reset();
        }
    });
    
    // Photo upload functionality
    const uploadTrigger = document.getElementById('uploadTrigger');
    const profileImageInput = document.getElementById('profile_image');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const changeImageBtn = document.getElementById('changeImage');
    const removeImageBtn = document.getElementById('removeImage');
    const savePhotoBtn = document.getElementById('savePhotoBtn');
    const uploadPhotoForm = document.getElementById('uploadPhotoForm');
    
    if (uploadTrigger) {
        uploadTrigger.addEventListener('click', () => profileImageInput.click());
        
        profileImageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Client-side validation
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Allowed: JPG, PNG, GIF.');
                    this.value = '';
                    return;
                }

                const maxBytes = 2 * 1024 * 1024; // 2MB
                if (file.size > maxBytes) {
                    alert('File is too large. Maximum size is 2MB.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                    uploadTrigger.classList.add('d-none');
                    savePhotoBtn.disabled = false;
                }

                reader.readAsDataURL(file);
            }
        });        
        changeImageBtn.addEventListener('click', () => {
            previewContainer.classList.add('d-none');
            uploadTrigger.classList.remove('d-none');
            profileImageInput.value = '';
            savePhotoBtn.disabled = true;
            profileImageInput.click();
        });
        
        removeImageBtn.addEventListener('click', () => {
            if (confirm('Remove selected image?')) {
                previewContainer.classList.add('d-none');
                uploadTrigger.classList.remove('d-none');
                profileImageInput.value = '';
                savePhotoBtn.disabled = true;
            }
        });
        
        // Handle form submission
        uploadPhotoForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const file = profileImageInput.files && profileImageInput.files[0];
            if (!file) {
                alert('Please select an image first.');
                return;
            }

            const formData = new FormData(this);
            savePhotoBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Uploading...';
            savePhotoBtn.disabled = true;

            // Safely get CSRF token (fallback to cookie) and include request headers
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

            const headers = {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };
            if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(async response => {
                // If response is JSON, parse it; otherwise extract text for debugging
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error('Upload failed (' + response.status + '): ' + text);
                }

                if (contentType.includes('application/json')) {
                    return response.json();
                }

                const text = await response.text();
                throw new Error('Unexpected response: ' + text);
            })
            .then(data => {
                if (data.success) {
                    const imageUrl = data.image_url || data.imageUrl || data.image || '';
                    const userName = @json(Auth::user()->name);
                    const initials = @json(Auth::user()->initials);

                    // Update all topnav and profile avatars
                    document.querySelectorAll('.user-avatar').forEach(function(el) {
                        el.innerHTML = `<img src="${imageUrl}" alt="${userName}" class="user-avatar-img" onerror="this.style.display='none'; this.parentNode.innerHTML='${initials}';">`;
                    });

                    // Update profile photo area
                    const profilePhotoEl = document.querySelector('.profile-photo');
                    if (profilePhotoEl) {
                        const img = profilePhotoEl.querySelector('img');
                        if (img) {
                            img.src = imageUrl;
                        } else {
                            const initialsEl = profilePhotoEl.querySelector('.profile-initials');
                            if (initialsEl) {
                                initialsEl.outerHTML = `<img src="${imageUrl}" alt="${userName}" class="img-thumbnail rounded-circle border-4">`;
                            } else {
                                profilePhotoEl.insertAdjacentHTML('afterbegin', `<img src="${imageUrl}" alt="${userName}" class="img-thumbnail rounded-circle border-4">`);
                            }
                        }
                    }

                    // Close modal
                    const modalEl = document.getElementById('uploadPhotoModal');
                    if (modalEl) {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bsModal.hide();
                    }

                    // Reset upload UI
                    previewContainer.classList.add('d-none');
                    uploadTrigger.classList.remove('d-none');
                    profileImageInput.value = '';
                    savePhotoBtn.innerHTML = '<i class="ri-save-line me-1"></i> Save Photo';
                    savePhotoBtn.disabled = true;

                    // Show success alert
                    const mainContent = document.querySelector('.main-content');
                    if (mainContent) {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show';
                        alertDiv.innerHTML = '<i class="ri-checkbox-circle-fill me-2"></i> Profile photo updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                        mainContent.prepend(alertDiv);
                        setTimeout(() => {
                            if (alertDiv.classList.contains('show')) {
                                alertDiv.classList.remove('show');
                                alertDiv.classList.add('fade');
                            }
                        }, 4000);
                    }
                } else {
                    alert(data.message || 'Error uploading photo');
                    savePhotoBtn.innerHTML = '<i class="ri-save-line me-1"></i> Save Photo';
                    savePhotoBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error uploading photo. ' + (error.message || 'Please try again.'));
                savePhotoBtn.innerHTML = '<i class="ri-save-line me-1"></i> Save Photo';
                savePhotoBtn.disabled = false;
            });
        });
    }
    
    function loadUserStats() {
        fetch('{{ route("user.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('completedExamsCount').textContent = data.data.completed_exams;
                    document.getElementById('averageScore').textContent = data.data.average_score + '%';
                }
            })
            .catch(error => {
                console.error('Error loading stats:', error);
            });
    }
});
</script>
@endpush