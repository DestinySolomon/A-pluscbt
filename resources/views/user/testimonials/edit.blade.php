@extends('layouts.user-dashboard')

@section('title', 'Edit Testimonial')
@section('page-title', 'Edit Testimonial')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('user.testimonials.index') }}">Testimonials</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="ri-edit-line me-2"></i> Edit Your Testimonial
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-4">
                    <i class="ri-alert-line me-2"></i>
                    Editing your testimonial will require re-approval by our team.
                </div>
                
                <form action="{{ route('user.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Photo -->
                    @if($testimonial->photo_path)
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Current Photo</h6>
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('storage/' . $testimonial->photo_path) }}" 
                                 alt="{{ $testimonial->student_name }}"
                                 class="rounded-circle me-3"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo">
                                    <label class="form-check-label text-danger" for="remove_photo">
                                        Remove current photo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Personal Information -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Personal Information</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="student_name" class="form-label">Your Name *</label>
                                <input type="text" 
                                       class="form-control @error('student_name') is-invalid @enderror" 
                                       id="student_name" 
                                       name="student_name" 
                                       value="{{ old('student_name', $testimonial->student_name) }}"
                                       required>
                                @error('student_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="student_course" class="form-label">Subject/Course *</label>
                                <input type="text" 
                                       class="form-control @error('student_course') is-invalid @enderror" 
                                       id="student_course" 
                                       name="student_course" 
                                       value="{{ old('student_course', $testimonial->student_course) }}"
                                       placeholder="e.g., Mathematics, Medicine, Law"
                                       required>
                                @error('student_course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rating -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Your Rating *</h6>
                        
                        <div class="rating-input mb-3">
                            <div class="d-flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <input type="radio" 
                                       id="rating{{ $i }}" 
                                       name="rating" 
                                       value="{{ $i }}" 
                                       class="d-none" 
                                       {{ old('rating', $testimonial->rating) == $i ? 'checked' : '' }}>
                                <label for="rating{{ $i }}" class="rating-star">
                                    <i class="ri-star-line display-6"></i>
                                </label>
                                @endfor
                            </div>
                            <div class="rating-labels mt-2">
                                <small class="text-muted">
                                    <span id="ratingText">
                                        @php
                                            $ratings = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                                            echo $ratings[old('rating', $testimonial->rating) - 1] ?? 'Excellent';
                                        @endphp
                                    </span> 
                                    (<span id="ratingValue">{{ old('rating', $testimonial->rating) }}</span>/5 stars)
                                </small>
                            </div>
                            @error('rating')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Testimonial Text -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Your Experience *</h6>
                        
                        <div class="form-group">
                            <label for="testimonial_text" class="form-label">
                                Share your experience with this platform. How did it help you prepare for JAMB?
                            </label>
                            <textarea class="form-control @error('testimonial_text') is-invalid @enderror" 
                                      id="testimonial_text" 
                                      name="testimonial_text" 
                                      rows="6"
                                      placeholder="Tell us about your experience... (Minimum 50 characters)"
                                      required>{{ old('testimonial_text', $testimonial->testimonial_text) }}</textarea>
                            <div class="form-text">
                                Be specific about what helped you: practice tests, explanations, time management, etc.
                            </div>
                            @error('testimonial_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="text-end mt-1">
                                <small class="text-muted">
                                    <span id="charCount">{{ strlen(old('testimonial_text', $testimonial->testimonial_text)) }}</span>/1000 characters
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- JAMB Score -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">JAMB Score (Optional)</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="score_achieved" class="form-label">Your JAMB Score</label>
                                <input type="number" 
                                       class="form-control @error('score_achieved') is-invalid @enderror" 
                                       id="score_achieved" 
                                       name="score_achieved" 
                                       value="{{ old('score_achieved', $testimonial->score_achieved) }}"
                                       min="120" 
                                       max="400"
                                       placeholder="e.g., 320">
                                <div class="form-text">
                                    Enter your actual JAMB score (between 120-400)
                                </div>
                                @error('score_achieved')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- New Photo Upload -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Update Photo (Optional)</h6>
                        
                        <div class="form-group">
                            <div class="mb-3">
                                <input type="file" 
                                       class="form-control @error('photo') is-invalid @enderror" 
                                       id="photo" 
                                       name="photo"
                                       accept="image/*">
                                <div class="form-text">
                                    Upload a new photo (Max: 2MB, JPG/PNG/GIF)
                                </div>
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="photo-preview d-none">
                                <img id="photoPreview" src="" alt="Preview" class="img-thumbnail mt-2" style="max-width: 200px;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-5">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ri-save-line me-2"></i> Update Testimonial
                            </button>
                            
                            <a href="{{ route('user.testimonials.show', $testimonial) }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-2"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rating-star {
        cursor: pointer;
        color: #e5e7eb;
        transition: color 0.2s;
    }
    
    .rating-star:hover,
    .rating-star:hover ~ .rating-star {
        color: #fbbf24;
    }
    
    input[name="rating"]:checked ~ label .ri-star-line,
    input[name="rating"]:checked ~ label .ri-star-line ~ .ri-star-line {
        color: #fbbf24;
    }
    
    input[name="rating"]:checked + label .ri-star-line {
        color: #f59e0b;
    }
    
    .rating-star i {
        font-size: 2.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating system
    const ratingStars = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.getElementById('ratingText');
    const ratingValue = document.getElementById('ratingValue');
    
    const ratingLabels = {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };
    
    ratingStars.forEach(star => {
        star.addEventListener('change', function() {
            ratingText.textContent = ratingLabels[this.value];
            ratingValue.textContent = this.value;
        });
    });
    
    // Character counter
    const textarea = document.getElementById('testimonial_text');
    const charCount = document.getElementById('charCount');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
    
    // Photo preview
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewContainer = document.querySelector('.photo-preview');
    
    photoInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                photoPreviewContainer.classList.remove('d-none');
            }
            
            reader.readAsDataURL(this.files[0]);
        } else {
            photoPreviewContainer.classList.add('d-none');
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const text = textarea.value.trim();
        
        if (text.length < 50) {
            e.preventDefault();
            alert('Please write at least 50 characters about your experience.');
            textarea.focus();
            return false;
        }
        
        const scoreInput = document.getElementById('score_achieved');
        if (scoreInput.value) {
            const score = parseInt(scoreInput.value);
            if (score < 120 || score > 400) {
                e.preventDefault();
                alert('JAMB score must be between 120 and 400.');
                scoreInput.focus();
                return false;
            }
        }
        
        return true;
    });
});
</script>
@endpush