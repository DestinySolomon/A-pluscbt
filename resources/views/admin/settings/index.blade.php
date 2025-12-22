@extends('layouts.admin')

@section('title', 'System Settings - A-plus CBT')
@section('page-title', 'System Settings')
@section('mobile-title', 'Settings')

@section('breadcrumbs')
<li class="breadcrumb-item active">System Settings</li>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ri-check-line me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ri-error-warning-line me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- General Settings -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-building-line me-2"></i> General Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="site_name" class="form-label required">Site Name</label>
                            <input type="text" 
                                   name="site_name" 
                                   id="site_name" 
                                   class="form-control @error('site_name') is-invalid @enderror" 
                                   value="{{ old('site_name', $settings['site_name'] ?? 'A-plus CBT') }}" 
                                   required>
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="site_tagline" class="form-label">Site Tagline</label>
                            <input type="text" 
                                   name="site_tagline" 
                                   id="site_tagline" 
                                   class="form-control @error('site_tagline') is-invalid @enderror" 
                                   value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                            @error('site_tagline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="contact_email" class="form-label required">Contact Email</label>
                            <input type="email" 
                                   name="contact_email" 
                                   id="contact_email" 
                                   class="form-control @error('contact_email') is-invalid @enderror" 
                                   value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" 
                                   required>
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" 
                                   name="contact_phone" 
                                   id="contact_phone" 
                                   class="form-control @error('contact_phone') is-invalid @enderror" 
                                   value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="contact_address" class="form-label">Contact Address</label>
                            <textarea name="contact_address" 
                                      id="contact_address" 
                                      class="form-control @error('contact_address') is-invalid @enderror" 
                                      rows="2">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                            @error('contact_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Exam Settings -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-list-line me-2"></i> Exam Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="default_exam_duration" class="form-label required">Default Exam Duration (minutes)</label>
                            <input type="number" 
                                   name="default_exam_duration" 
                                   id="default_exam_duration" 
                                   class="form-control @error('default_exam_duration') is-invalid @enderror" 
                                   min="1" 
                                   max="480" 
                                   value="{{ old('default_exam_duration', $settings['default_exam_duration'] ?? 60) }}" 
                                   required>
                            @error('default_exam_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="max_attempts_per_exam" class="form-label required">Max Attempts per Exam</label>
                            <input type="number" 
                                   name="max_attempts_per_exam" 
                                   id="max_attempts_per_exam" 
                                   class="form-control @error('max_attempts_per_exam') is-invalid @enderror" 
                                   min="1" 
                                   max="10" 
                                   value="{{ old('max_attempts_per_exam', $settings['max_attempts_per_exam'] ?? 3) }}" 
                                   required>
                            @error('max_attempts_per_exam')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="passing_percentage" class="form-label required">Passing Percentage</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="passing_percentage" 
                                       id="passing_percentage" 
                                       class="form-control @error('passing_percentage') is-invalid @enderror" 
                                       min="1" 
                                       max="100" 
                                       value="{{ old('passing_percentage', $settings['passing_percentage'] ?? 50) }}" 
                                       required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('passing_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" 
                                       name="show_results_immediately" 
                                       id="show_results_immediately" 
                                       class="form-check-input" 
                                       value="1"
                                       {{ old('show_results_immediately', $settings['show_results_immediately'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_results_immediately">
                                    Show results immediately after exam
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" 
                                       name="allow_question_review" 
                                       id="allow_question_review" 
                                       class="form-check-input" 
                                       value="1"
                                       {{ old('allow_question_review', $settings['allow_question_review'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_question_review">
                                    Allow question review during exam
                                </label>
                            </div>
                            
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" 
                                       name="randomize_questions" 
                                       id="randomize_questions" 
                                       class="form-check-input" 
                                       value="1"
                                       {{ old('randomize_questions', $settings['randomize_questions'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="randomize_questions">
                                    Randomize questions order
                                </label>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input type="checkbox" 
                                       name="randomize_options" 
                                       id="randomize_options" 
                                       class="form-check-input" 
                                       value="1"
                                       {{ old('randomize_options', $settings['randomize_options'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="randomize_options">
                                    Randomize options order
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Email Settings -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-mail-line me-2"></i> Email Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email_from_name" class="form-label required">From Name</label>
                            <input type="text" 
                                   name="email_from_name" 
                                   id="email_from_name" 
                                   class="form-control @error('email_from_name') is-invalid @enderror" 
                                   value="{{ old('email_from_name', $settings['email_from_name'] ?? 'A-plus CBT Admin') }}" 
                                   required>
                            @error('email_from_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email_from_address" class="form-label required">From Email</label>
                            <input type="email" 
                                   name="email_from_address" 
                                   id="email_from_address" 
                                   class="form-control @error('email_from_address') is-invalid @enderror" 
                                   value="{{ old('email_from_address', $settings['email_from_address'] ?? '') }}" 
                                   required>
                            @error('email_from_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email_subject_prefix" class="form-label">Email Subject Prefix</label>
                            <input type="text" 
                                   name="email_subject_prefix" 
                                   id="email_subject_prefix" 
                                   class="form-control @error('email_subject_prefix') is-invalid @enderror" 
                                   value="{{ old('email_subject_prefix', $settings['email_subject_prefix'] ?? '[A-plus CBT]') }}">
                            @error('email_subject_prefix')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Maintenance -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-tools-line me-2"></i> Maintenance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" 
                                       name="maintenance_mode" 
                                       id="maintenance_mode" 
                                       class="form-check-input" 
                                       value="1"
                                       {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">
                                    Enable Maintenance Mode
                                </label>
                                <small class="form-text text-muted d-block">
                                    When enabled, only administrators can access the site
                                </small>
                            </div>
                            
                            <label for="maintenance_message" class="form-label">Maintenance Message</label>
                            <textarea name="maintenance_message" 
                                      id="maintenance_message" 
                                      class="form-control @error('maintenance_message') is-invalid @enderror" 
                                      rows="3">{{ old('maintenance_message', $settings['maintenance_message'] ?? 'System is under maintenance. Please check back later.') }}</textarea>
                            @error('maintenance_message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Branding -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-image-line me-2"></i> Branding
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Logo -->
                    <div class="mb-4">
                        <label for="logo" class="form-label">Logo</label>
                        
                        @if(!empty($settings['logo']) && Storage::disk('public')->exists($settings['logo']))
                        <div class="mb-3">
                            <div class="current-file-preview mb-2">
                                <img src="{{ Storage::url($settings['logo']) }}" 
                                     alt="Current Logo" 
                                     class="img-thumbnail" 
                                     style="max-height: 100px;">
                            </div>
                            <input type="hidden" name="current_logo" value="{{ $settings['logo'] }}">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="remove_logo" 
                                       id="remove_logo" 
                                       class="form-check-input">
                                <label class="form-check-label text-danger" for="remove_logo">
                                    Remove current logo
                                </label>
                            </div>
                        </div>
                        @endif
                        
                        <input type="file" 
                               name="logo" 
                               id="logo" 
                               class="form-control @error('logo') is-invalid @enderror" 
                               accept="image/*">
                        <small class="text-muted">
                            Recommended size: 200x60px, Max: 2MB
                        </small>
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Favicon -->
                    <div class="mb-4">
                        <label for="favicon" class="form-label">Favicon</label>
                        
                        @if(!empty($settings['favicon']) && Storage::disk('public')->exists($settings['favicon']))
                        <div class="mb-3">
                            <div class="current-file-preview mb-2">
                                <img src="{{ Storage::url($settings['favicon']) }}" 
                                     alt="Current Favicon" 
                                     class="img-thumbnail" 
                                     style="max-height: 64px; max-width: 64px;">
                            </div>
                            <input type="hidden" name="current_favicon" value="{{ $settings['favicon'] }}">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="remove_favicon" 
                                       id="remove_favicon" 
                                       class="form-check-input">
                                <label class="form-check-label text-danger" for="remove_favicon">
                                    Remove current favicon
                                </label>
                            </div>
                        </div>
                        @endif
                        
                        <input type="file" 
                               name="favicon" 
                               id="favicon" 
                               class="form-control @error('favicon') is-invalid @enderror" 
                               accept=".ico,.png,.jpg,.gif">
                        <small class="text-muted">
                            Recommended: 64x64px, Max: 1MB
                        </small>
                        @error('favicon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Save Button -->
            <div class="admin-card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn-admin btn-admin-primary">
                            <i class="ri-save-line me-2"></i> Save Settings
                        </button>
                        <button type="reset" class="btn-admin btn-admin-secondary">
                            <i class="ri-refresh-line me-2"></i> Reset Changes
                        </button>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="ri-information-line me-1"></i>
                            Settings are saved to the configuration file.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Maintenance mode toggle
    $('#maintenance_mode').change(function() {
        if ($(this).is(':checked')) {
            $('#maintenance_message').closest('.form-group').slideDown();
        } else {
            $('#maintenance_message').closest('.form-group').slideUp();
        }
    }).trigger('change');
    
    // Remove logo checkbox
    $('#remove_logo').change(function() {
        if ($(this).is(':checked')) {
            $('#logo').prop('disabled', true).closest('.mb-4').find('small').hide();
            $('.current-file-preview').fadeOut();
        } else {
            $('#logo').prop('disabled', false).closest('.mb-4').find('small').show();
            $('.current-file-preview').fadeIn();
        }
    });
    
    // Remove favicon checkbox
    $('#remove_favicon').change(function() {
        if ($(this).is(':checked')) {
            $('#favicon').prop('disabled', true).closest('.mb-4').find('small').hide();
            $('.current-file-preview').fadeOut();
        } else {
            $('#favicon').prop('disabled', false).closest('.mb-4').find('small').show();
            $('.current-file-preview').fadeIn();
        }
    });
    
    // File preview
    $('#logo').change(function(e) {
        previewImage(this, '.logo-preview');
    });
    
    $('#favicon').change(function(e) {
        previewImage(this, '.favicon-preview');
    });
    
    function previewImage(input, previewClass) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewClass).html(
                    '<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 100px;">'
                );
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Add validation styling
    $('input, select, textarea').on('blur', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).addClass('border-danger');
        } else if ($(this).val().trim() !== '' && !$(this).hasClass('is-invalid')) {
            $(this).addClass('border-success');
        } else {
            $(this).removeClass('border-success border-danger');
        }
    });
});
</script>

<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.admin-card .card-header {
    background-color: var(--primary-color, #0d6efd);
    color: white;
    border-bottom: none;
}

.admin-card .card-header .card-title {
    color: white;
    font-weight: 600;
}

.form-switch .form-check-input {
    height: 1.5em;
    width: 3em;
}

.form-switch .form-check-input:checked {
    background-color: var(--primary-color, #0d6efd);
    border-color: var(--primary-color, #0d6efd);
}

.current-file-preview img {
    border: 2px solid #dee2e6;
    padding: 4px;
    border-radius: 6px;
    background: white;
}

input[type="file"] {
    padding: 0.5rem;
    border: 1px dashed #dee2e6;
    border-radius: 6px;
    background: #f8f9fa;
}

input[type="file"]:hover {
    background: #e9ecef;
    border-color: var(--primary-color, #0d6efd);
}

.border-success {
    border-color: #198754 !important;
}

.border-danger {
    border-color: #dc3545 !important;
}

.text-danger {
    color: #dc3545 !important;
}
</style>
@endpush