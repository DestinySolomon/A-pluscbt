@extends('layouts.user-dashboard')

@section('title', 'Student Testimonials')
@section('page-title', 'Student Testimonials')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Testimonials</li>
@endsection

@section('content')
    <!-- My Testimonials Section (Only for logged-in user) -->
    @auth
    @php
        $myTestimonials = App\Models\Testimonial::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp
    
    @if($myTestimonials->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="ri-user-line me-2"></i> My Testimonials
                        </h5>
                        <a href="{{ route('user.testimonials.my-testimonials') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ri-list-check me-1"></i> View All & Manage
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach($myTestimonials as $testimonial)
                        <div class="col-lg-4">
                            <div class="testimonial-card {{ $testimonial->is_featured ? 'featured' : '' }} h-100">
                                <!-- Status Badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    @if($testimonial->is_approved)
                                        @if($testimonial->is_featured)
                                        <span class="badge bg-warning text-dark">
                                            <i class="ri-star-line me-1"></i> Featured
                                        </span>
                                        @else
                                        <span class="badge bg-success">
                                            <i class="ri-check-line me-1"></i> Approved
                                        </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="ri-time-line me-1"></i> Pending Review
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="testimonial-header mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($testimonial->photo_path)
                                        <img src="{{ asset('storage/' . $testimonial->photo_path) }}" 
                                             alt="{{ $testimonial->student_name }}"
                                             class="rounded-circle me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="ri-user-line fs-4"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-1">{{ $testimonial->student_name }}</h6>
                                            <small class="text-muted">{{ $testimonial->student_course }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="testimonial-rating mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="ri-star-{{ $i <= $testimonial->rating ? 'fill' : 'line' }} text-warning"></i>
                                    @endfor
                                </div>
                                
                                <div class="testimonial-text mb-3">
                                    <p class="mb-0">"{{ Str::limit($testimonial->testimonial_text, 150) }}"</p>
                                </div>
                                
                                @if($testimonial->score_achieved)
                                <div class="testimonial-score">
                                    <span class="badge bg-success">
                                        <i class="ri-trophy-line me-1"></i> JAMB Score: {{ $testimonial->score_achieved }}
                                    </span>
                                </div>
                                @endif
                                
                                <div class="testimonial-footer mt-3">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('user.testimonials.show', $testimonial) }}" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                        @if(!$testimonial->is_approved)
                                        <a href="{{ route('user.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endauth

    <!-- Featured Testimonials -->
    @if($featuredTestimonials->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="ri-star-line me-2"></i> Featured Testimonials
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach($featuredTestimonials as $testimonial)
                        <div class="col-lg-4">
                            <div class="testimonial-card featured h-100">
                                <div class="testimonial-header mb-3">
                                    <div class="d-flex align-items-center">
                                        @if($testimonial->photo_path)
                                        <img src="{{ asset('storage/' . $testimonial->photo_path) }}" 
                                             alt="{{ $testimonial->student_name }}"
                                             class="rounded-circle me-3"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                             style="width: 60px; height: 60px;">
                                            <i class="ri-user-line fs-4"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-1">{{ $testimonial->student_name }}</h6>
                                            <small class="text-muted">{{ $testimonial->student_course }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="testimonial-rating mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="ri-star-{{ $i <= $testimonial->rating ? 'fill' : 'line' }} text-warning"></i>
                                    @endfor
                                </div>
                                
                                <div class="testimonial-text mb-3">
                                    <p class="mb-0">"{{ Str::limit($testimonial->testimonial_text, 150) }}"</p>
                                </div>
                                
                                @if($testimonial->score_achieved)
                                <div class="testimonial-score">
                                    <span class="badge bg-success">
                                        <i class="ri-trophy-line me-1"></i> JAMB Score: {{ $testimonial->score_achieved }}
                                    </span>
                                </div>
                                @endif
                                
                                <div class="testimonial-footer mt-3">
                                    <a href="{{ route('user.testimonials.show', $testimonial) }}" class="btn btn-sm btn-outline-primary">
                                        Read Full Story
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Testimonials Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0">Student Experiences</h4>
            <p class="text-muted mb-0">See what other students are saying about their JAMB preparation journey</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex gap-2 justify-content-end">
                @auth
                @if($myTestimonials->count() > 0)
                <a href="{{ route('user.testimonials.my-testimonials') }}" class="btn btn-outline-primary">
                    <i class="ri-list-check me-1"></i> My Testimonials
                </a>
                @endif
                @endauth
                <a href="{{ route('user.testimonials.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i> Share Your Experience
                </a>
            </div>
        </div>
    </div>

    <!-- Stats & Filters -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-primary">{{ $totalTestimonials }}</div>
                    <small class="text-muted">Total Testimonials</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-warning">{{ number_format($averageRating, 1) }}/5</div>
                    <small class="text-muted">Average Rating</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">Filter by Subject</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('user.testimonials.index') }}" 
                           class="btn btn-sm {{ !isset($selectedSubject) ? 'btn-primary' : 'btn-outline-primary' }}">
                            All Subjects
                        </a>
                        @foreach($subjects as $subject)
                        <a href="{{ route('user.testimonials.filter.subject', $subject) }}" 
                           class="btn btn-sm {{ (isset($selectedSubject) && $selectedSubject == $subject) ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $subject }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Testimonials Grid -->
    <div class="row">
        @if($testimonials->count() > 0)
            @foreach($testimonials as $testimonial)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="testimonial-card h-100">
                    <div class="testimonial-header mb-3">
                        <div class="d-flex align-items-center">
                            @if($testimonial->photo_path)
                            <img src="{{ asset('storage/' . $testimonial->photo_path) }}" 
                                 alt="{{ $testimonial->student_name }}"
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-user-line"></i>
                            </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $testimonial->student_name }}</h6>
                                <small class="text-muted">{{ $testimonial->student_course }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-rating mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="ri-star-{{ $i <= $testimonial->rating ? 'fill' : 'line' }} text-warning"></i>
                        @endfor
                    </div>
                    
                    <div class="testimonial-text mb-3">
                        <p class="mb-0">"{{ Str::limit($testimonial->testimonial_text, 120) }}"</p>
                    </div>
                    
                    @if($testimonial->score_achieved)
                    <div class="testimonial-score mb-3">
                        <small class="text-success">
                            <i class="ri-trophy-line me-1"></i> Score: {{ $testimonial->score_achieved }}
                        </small>
                    </div>
                    @endif
                    
                    <div class="testimonial-footer mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $testimonial->created_at->diffForHumans() }}
                            </small>
                            <a href="{{ route('user.testimonials.show', $testimonial) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-eye-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="col-12">
                <div class="d-flex justify-content-center mt-4">
                    {{ $testimonials->links() }}
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="ri-inbox-line display-4 text-muted"></i>
                        <h5 class="text-muted mt-3">No Testimonials Yet</h5>
                        <p class="text-muted mb-4">
                            @if(isset($selectedSubject))
                                No testimonials found for "{{ $selectedSubject }}"
                            @else
                                Be the first to share your experience!
                            @endif
                        </p>
                        <a href="{{ route('user.testimonials.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Share Your Experience
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body text-center py-5">
                    <h3 class="mb-3">Share Your Success Story</h3>
                    <p class="mb-4 opacity-75">
                        Your experience can inspire other students. Share how this platform helped you prepare for JAMB.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('user.testimonials.create') }}" class="btn btn-light btn-lg">
                            <i class="ri-edit-line me-2"></i> Write Your Testimonial
                        </a>
                        @auth
                        @if($myTestimonials->count() > 0)
                        <a href="{{ route('user.testimonials.my-testimonials') }}" class="btn btn-outline-light btn-lg">
                            <i class="ri-list-check me-2"></i> Manage My Testimonials
                        </a>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .testimonial-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    
    .testimonial-card:hover {
        border-color: var(--user-primary);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.15);
        transform: translateY(-5px);
    }
    
    .testimonial-card.featured {
        border: 2px solid var(--user-primary);
        background: linear-gradient(135deg, #f8fafc, #f0fdfa);
    }
    
    .testimonial-header h6 {
        font-weight: 600;
        color: #1f2937;
    }
    
    .testimonial-rating {
        font-size: 1.25rem;
    }
    
    .testimonial-text {
        color: #4b5563;
        line-height: 1.6;
        flex-grow: 1;
    }
    
    .testimonial-score .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
    }
    
    .bg-primary .btn-light:hover {
        background: #f8f9fa;
        color: var(--user-primary);
    }
    
    .bg-primary .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }
    
    .position-absolute {
        z-index: 1;
    }
</style>
@endpush