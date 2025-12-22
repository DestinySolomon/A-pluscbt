@extends('layouts.admin')

@section('title', 'Testimonial Form - A-plus CBT')
@section('page-title', isset($testimonial) ? 'Edit Testimonial' : 'Add Testimonial')
@section('mobile-title', 'Testimonial Form')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.testimonials.index') }}">Testimonials</a>
    </li>
    <li class="breadcrumb-item active">
        {{ isset($testimonial) ? 'Edit' : 'Add' }} Testimonial
    </li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.testimonials.index') }}" class="btn-admin btn-admin-secondary">
            <i class="ri-arrow-left-line me-2"></i> Back to Testimonials
        </a>
    </div>
@endsection

@section('content')
    <div class="admin-card">
        <div class="card-body">
            <form action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  id="testimonialForm">
                @csrf
                @if(isset($testimonial))
                    @method('PUT')
                @endif
                
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Testimonial Details -->
                        <div class="admin-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Testimonial Details</h5>
                            </div>
                            <div class="card-body">
                                <!-- Student Information -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="student_name" class="form-label required">Student Name</label>
                                            <input type="text" 
                                                   class="form-control @error('student_name') is-invalid @enderror" 
                                                   id="student_name" 
                                                   name="student_name" 
                                                   value="{{ old('student_name', $testimonial->student_name ?? '') }}" 
                                                   required>
                                            @error('student_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_id" class="form-label">Link to Student Account (Optional)</label>
                                            <select name="user_id" id="user_id" 
                                                    class="form-select @error('user_id') is-invalid @enderror">
                                                <option value="">-- Select Student (Optional) --</option>
                                                @if($users && count($users) > 0)
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" 
                                                            {{ old('user_id', $testimonial->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="">No students found</option>
                                                @endif
                                            </select>
                                            <small class="text-muted">Link to existing student account for tracking</small>
                                            @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="student_course" class="form-label">Course/Program</label>
                                            <input type="text" 
                                                   class="form-control @error('student_course') is-invalid @enderror" 
                                                   id="student_course" 
                                                   name="student_course" 
                                                   value="{{ old('student_course', $testimonial->student_course ?? '') }}">
                                            <small class="text-muted">e.g., Medicine, Engineering, Law, etc.</small>
                                            @error('student_course')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="score_achieved" class="form-label">Score Achieved (%)</label>
                                            <input type="number" 
                                                   class="form-control @error('score_achieved') is-invalid @enderror" 
                                                   id="score_achieved" 
                                                   name="score_achieved" 
                                                   value="{{ old('score_achieved', $testimonial->score_achieved ?? '') }}" 
                                                   min="0" 
                                                   max="100">
                                            <small class="text-muted">Optional: Student's JAMB score</small>
                                            @error('score_achieved')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="testimonial_text" class="form-label required">Testimonial Content</label>
                                            <textarea class="form-control @error('testimonial_text') is-invalid @enderror" 
                                                      id="testimonial_text" 
                                                      name="testimonial_text" 
                                                      rows="5" 
                                                      required>{{ old('testimonial_text', $testimonial->testimonial_text ?? '') }}</textarea>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">Share the student's experience and success story (10-1000 characters)</small>
                                                <small>
                                                    <span id="charCount">0</span>/1000 characters
                                                </small>
                                            </div>
                                            @error('testimonial_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Student Photo -->
                        <div class="admin-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Student Photo</h5>
                            </div>
                            <div class="card-body text-center">
                                <!-- Current Photo -->
                                @if(isset($testimonial) && $testimonial->hasPhoto())
                                    <div class="mb-3">
                                        <img src="{{ $testimonial->photo_url }}" 
                                             alt="{{ $testimonial->student_name }}" 
                                             class="img-fluid rounded-circle border" 
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="remove_photo" 
                                               id="remove_photo" 
                                               value="1">
                                        <label class="form-check-label" for="remove_photo">
                                            Remove current photo
                                        </label>
                                    </div>
                                    
                                    <hr class="my-3">
                                @else
                                    <div class="avatar-placeholder mb-3">
                                        <div class="avatar-lg mx-auto bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-user-line display-4"></i>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Photo Upload -->
                                <div class="form-group">
                                    <label for="photo" class="form-label">Upload New Photo</label>
                                    <div class="file-upload-area">
                                        <input type="file" 
                                               name="photo" 
                                               id="photo" 
                                               class="form-control @error('photo') is-invalid @enderror" 
                                               accept="image/*">
                                        <small class="text-muted d-block mt-2">
                                            Recommended: 400x400px, JPG, PNG or GIF (Max 2MB)
                                        </small>
                                        @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Photo Preview -->
                                    <div class="photo-preview mt-3 d-none">
                                        <img id="photoPreview" src="" 
                                             alt="Preview" 
                                             class="img-fluid rounded border" 
                                             style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                    </div>
                                </div>
                                
                                @if(!isset($testimonial) || !$testimonial->hasPhoto())
                                    <div class="alert alert-info mt-3">
                                        <small>
                                            <i class="ri-information-line me-1"></i>
                                            If no photo is uploaded, student initials will be used as avatar.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Rating & Settings -->
                        <div class="admin-card">
                            <div class="card-header">
                                <h5 class="mb-0">Rating & Settings</h5>
                            </div>
                            <div class="card-body">
                                <!-- Rating -->
                                <div class="mb-4">
                                    <label class="form-label required">Rating</label>
                                    <div class="rating-select @error('rating') is-invalid @enderror">
                                        @for($i = 5; $i >= 1; $i--)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="rating" 
                                                       id="rating{{ $i }}" 
                                                       value="{{ $i }}"
                                                       {{ old('rating', $testimonial->rating ?? 5) == $i ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="rating{{ $i }}">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="star-display">
                                                            @for($j = 1; $j <= $i; $j++)
                                                                <i class="ri-star-fill text-warning"></i>
                                                            @endfor
                                                            @for($j = $i + 1; $j <= 5; $j++)
                                                                <i class="ri-star-line text-warning"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="text-muted">{{ $i }} star{{ $i > 1 ? 's' : '' }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Settings -->
                                <div class="settings-section">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_approved" 
                                               name="is_approved" 
                                               value="1" 
                                               {{ old('is_approved', $testimonial->is_approved ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_approved">
                                            <span class="fw-medium">Approved</span>
                                            <small class="text-muted d-block">Testimonial will be visible on the site</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_featured" 
                                               name="is_featured" 
                                               value="1" 
                                               {{ old('is_featured', $testimonial->is_featured ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            <span class="fw-medium">Featured</span>
                                            <small class="text-muted d-block">Will appear in featured testimonials section</small>
                                        </label>
                                    </div>
                                    
                                    @if(isset($testimonial))
                                        <div class="form-group mb-3">
                                            <label for="display_order" class="form-label">Display Order</label>
                                            <input type="number" 
                                                   class="form-control @error('display_order') is-invalid @enderror" 
                                                   id="display_order" 
                                                   name="display_order" 
                                                   value="{{ old('display_order', $testimonial->display_order ?? 0) }}" 
                                                   min="0">
                                            <small class="text-muted">Lower numbers appear first. Set to 0 for default ordering.</small>
                                            @error('display_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.testimonials.index') }}" class="btn-admin btn-admin-secondary">
                                    <i class="ri-close-line me-2"></i> Cancel
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                @if(isset($testimonial))
                                    <button type="submit" class="btn-admin btn-admin-primary">
                                        <i class="ri-save-line me-2"></i> Update Testimonial
                                    </button>
                                    <a href="{{ route('admin.testimonials.show', $testimonial) }}" 
                                       class="btn-admin btn-admin-light">
                                        <i class="ri-eye-line me-2"></i> Preview
                                    </a>
                                @else
                                    <button type="submit" class="btn-admin btn-admin-primary">
                                        <i class="ri-add-circle-line me-2"></i> Create Testimonial
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .required::after {
            content: " *";
            color: #dc3545;
        }
        
        .rating-select .form-check-label {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid var(--bs-border-color);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .rating-select .form-check-input:checked + .form-check-label {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
            border-color: var(--bs-warning);
        }
        
        .rating-select .form-check-label:hover {
            background-color: var(--bs-light);
        }
        
        .star-display {
            font-size: 1.25rem;
        }
        
        .avatar-lg {
            width: 150px;
            height: 150px;
        }
        
        .file-upload-area {
            border: 2px dashed var(--bs-border-color);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: var(--bs-light);
            transition: all 0.3s ease;
        }
        
        .file-upload-area:hover {
            border-color: var(--primary-color);
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }
        
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .bg-primary-light {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Character counter for testimonial text
            const textarea = $('#testimonial_text');
            const charCount = $('#charCount');
            
            function updateCharCount() {
                const length = textarea.val().length;
                charCount.text(length);
                
                // Add warning class if near limit
                if (length > 950) {
                    charCount.addClass('text-danger');
                } else {
                    charCount.removeClass('text-danger');
                }
            }
            
            // Initialize character count
            updateCharCount();
            textarea.on('input', updateCharCount);
            
            // Photo preview
            $('#photo').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photoPreview').attr('src', e.target.result);
                        $('.photo-preview').removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            // Remove photo checkbox handler
            $('#remove_photo').change(function() {
                if ($(this).is(':checked')) {
                    $('.photo-preview').addClass('d-none');
                    $('#photo').prop('disabled', true).val('');
                } else {
                    $('#photo').prop('disabled', false);
                }
            });
            
            // Form validation
            $('#testimonialForm').submit(function(e) {
                const testimonialText = $('#testimonial_text').val().trim();
                
                // Check minimum length
                if (testimonialText.length < 10) {
                    e.preventDefault();
                    alert('Testimonial text must be at least 10 characters long.');
                    $('#testimonial_text').focus();
                    return false;
                }
                
                // Check maximum length
                if (testimonialText.length > 1000) {
                    e.preventDefault();
                    alert('Testimonial text cannot exceed 1000 characters.');
                    $('#testimonial_text').focus();
                    return false;
                }
                
                // Check rating
                if (!$('input[name="rating"]:checked').val()) {
                    e.preventDefault();
                    alert('Please select a rating.');
                    return false;
                }
                
                return true;
            });
        });
    </script>
@endpush