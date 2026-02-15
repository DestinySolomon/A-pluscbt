@extends('layouts.user-dashboard')

@section('title', $testimonial->student_name . "'s Testimonial")
@section('page-title', 'Testimonial Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('user.testimonials.index') }}">Testimonials</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <!-- Testimonial Header -->
                <div class="d-flex align-items-start mb-4">
                    @if($testimonial->photo_path)
                    <img src="{{ asset('storage/' . $testimonial->photo_path) }}" 
                         alt="{{ $testimonial->student_name }}"
                         class="rounded-circle me-4"
                         style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-4"
                         style="width: 100px; height: 100px;">
                        <i class="ri-user-line fs-2"></i>
                    </div>
                    @endif
                    
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-1">{{ $testimonial->student_name }}</h4>
                                <p class="text-muted mb-2">
                                    <i class="ri-book-open-line me-1"></i> {{ $testimonial->student_course }}
                                </p>
                                
                                <!-- Rating -->
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rating-display me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="ri-star-{{ $i <= $testimonial->rating ? 'fill' : 'line' }} text-warning fs-5"></i>
                                        @endfor
                                    </div>
                                    <span class="text-muted">
                                        ({{ $testimonial->rating }}/5)
                                    </span>
                                </div>
                                
                                <!-- JAMB Score -->
                                @if($testimonial->score_achieved)
                                <div class="mb-3">
                                    <span class="badge bg-success fs-6">
                                        <i class="ri-trophy-line me-1"></i> JAMB Score: {{ $testimonial->score_achieved }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="text-end">
                                @if($testimonial->is_featured)
                                <span class="badge bg-warning text-dark">
                                    <i class="ri-star-line me-1"></i> Featured
                                </span>
                                @endif
                                
                                @if(!$testimonial->is_approved)
                                <span class="badge bg-secondary">
                                    <i class="ri-time-line me-1"></i> Pending Review
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial Content -->
                <div class="testimonial-content mb-5">
                    <div class="position-relative">
                        <i class="ri-double-quotes-l text-primary fs-1 opacity-10 position-absolute top-0 start-0"></i>
                        <div class="ps-5">
                            <p class="lead fs-5 text-dark mb-4">
                                "{{ $testimonial->testimonial_text }}"
                            </p>
                        </div>
                        <i class="ri-double-quotes-r text-primary fs-1 opacity-10 position-absolute bottom-0 end-0"></i>
                    </div>
                </div>
                
                <!-- Metadata -->
                <div class="border-top pt-4 mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-calendar-line text-muted me-2"></i>
                                <small class="text-muted">
                                    Submitted: {{ $testimonial->created_at->format('F d, Y') }}
                                </small>
                            </div>
                            
                            @if($testimonial->updated_at->gt($testimonial->created_at))
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-refresh-line text-muted me-2"></i>
                                <small class="text-muted">
                                    Updated: {{ $testimonial->updated_at->format('F d, Y') }}
                                </small>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6 text-md-end">
                            @if(Auth::id() === $testimonial->user_id)
                            <div class="d-flex align-items-center justify-content-md-end mb-2">
                                <i class="ri-user-line text-muted me-2"></i>
                                <small class="text-muted">Your Testimonial</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                @if(Auth::id() === $testimonial->user_id && !$testimonial->is_approved)
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.testimonials.edit', $testimonial) }}" 
                           class="btn btn-primary">
                            <i class="ri-edit-line me-1"></i> Edit Testimonial
                        </a>
                        
                        <form action="{{ route('user.testimonials.destroy', $testimonial) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="ri-delete-bin-line me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                    
                    @if(!$testimonial->is_approved)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="ri-information-line me-2"></i>
                        Your testimonial is pending review by our team. Once approved, it will appear publicly on the site.
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- Comments Section (Optional - Can be added later) -->
        <!--
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="ri-chat-3-line me-2"></i> Comments & Reactions
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted text-center mb-0">Comments feature coming soon!</p>
            </div>
        </div>
        -->
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Related Testimonials -->
        @if($relatedTestimonials->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="ri-group-line me-2"></i> Related Testimonials
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Other students who studied {{ $testimonial->student_course }}
                </p>
                
                <div class="related-testimonials">
                    @foreach($relatedTestimonials as $related)
                    <div class="related-item mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-start">
                            @if($related->photo_path)
                            <img src="{{ asset('storage/' . $related->photo_path) }}" 
                                 alt="{{ $related->student_name }}"
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-user-line"></i>
                            </div>
                            @endif
                            
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $related->student_name }}</h6>
                                <div class="d-flex align-items-center mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="ri-star-{{ $i <= $related->rating ? 'fill' : 'line' }} text-warning me-1 small"></i>
                                    @endfor
                                </div>
                                <p class="text-muted small mb-0">
                                    "{{ Str::limit($related->testimonial_text, 80) }}"
                                </p>
                                <a href="{{ route('user.testimonials.show', $related) }}" 
                                   class="btn btn-sm btn-link p-0 mt-1">
                                    Read full story →
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Statistics Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="ri-bar-chart-line me-2"></i> Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Subject/Course:</span>
                        <span class="fw-semibold">{{ $testimonial->student_course }}</span>
                    </div>
                </div>
                
                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Rating:</span>
                        <span class="fw-semibold">{{ $testimonial->rating }}/5</span>
                    </div>
                </div>
                
                @if($testimonial->score_achieved)
                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">JAMB Score:</span>
                        <span class="fw-semibold text-success">{{ $testimonial->score_achieved }}/400</span>
                    </div>
                </div>
                @endif
                
                <div class="stat-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Status:</span>
                        <span class="fw-semibold {{ $testimonial->is_approved ? 'text-success' : 'text-warning' }}">
                            {{ $testimonial->is_approved ? 'Approved' : 'Pending Review' }}
                        </span>
                    </div>
                </div>
                
                @if($testimonial->is_featured)
                <div class="stat-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Featured:</span>
                        <span class="fw-semibold text-warning">Yes</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Call to Action -->
        @if(Auth::id() !== $testimonial->user_id)
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
                <i class="ri-edit-line display-6 mb-3 opacity-75"></i>
                <h5 class="mb-3">Share Your Story Too!</h5>
                <p class="mb-4 opacity-75">
                    Inspired by this testimonial? Share your own JAMB preparation experience.
                </p>
                <a href="{{ route('user.testimonials.create') }}" class="btn btn-light btn-lg w-100">
                    <i class="ri-edit-line me-2"></i> Write Your Testimonial
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Back to All Testimonials -->
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('user.testimonials.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i> Back to All Testimonials
            </a>
            
            @if(Auth::id() === $testimonial->user_id)
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="testimonialActions" data-bs-toggle="dropdown">
                    <i class="ri-more-2-line me-1"></i> Actions
                </button>
                <ul class="dropdown-menu" aria-labelledby="testimonialActions">
                    <li>
                        <a class="dropdown-item" href="{{ route('user.testimonials.edit', $testimonial) }}">
                            <i class="ri-edit-line me-2"></i> Edit Testimonial
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('user.testimonials.destroy', $testimonial) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ri-delete-bin-line me-2"></i> Delete Testimonial
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('user.testimonials.my-testimonials') }}">
                            <i class="ri-list-check me-2"></i> View My Testimonials
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .testimonial-content {
        position: relative;
        background: linear-gradient(135deg, #f8fafc 0%, #f0fdfa 100%);
        border-radius: 12px;
        padding: 2rem;
        border-left: 4px solid var(--user-primary);
    }
    
    .rating-display i {
        font-size: 1.5rem;
    }
    
    .related-item {
        transition: all 0.2s ease;
    }
    
    .related-item:hover {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.5rem;
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .related-item:hover .border-bottom {
        border-bottom: 1px solid transparent !important;
    }
    
    .stat-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .bg-primary .btn-light:hover {
        background: #f8f9fa;
        color: var(--user-primary);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Copy testimonial link to clipboard
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', function() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="ri-check-line me-1"></i> Copied!';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-primary');
                }, 2000);
            });
        });
    }
});
</script>
@endpush