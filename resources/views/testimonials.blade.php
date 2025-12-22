@extends('layouts.frontend')

@section('title', 'Student Testimonials - A-plus CBT')
@section('description', 'Read success stories from students who achieved excellent JAMB scores using our platform.')

@section('content')
    <section class="py-5">
        <div class="container py-5">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3">Student Success Stories</h1>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Discover how our platform has helped students achieve their dream JAMB scores
                </p>
            </div>

            <!-- Statistics -->
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="text-center">
                        <div class="display-3 fw-bold text-primary">{{ $stats['total'] }}+</div>
                        <p class="text-muted">Success Stories</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-center">
                        <div class="display-3 fw-bold text-warning">{{ number_format($stats['average_rating'], 1) }}</div>
                        <p class="text-muted">Average Rating</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-center">
                        <div class="display-3 fw-bold text-info">{{ $stats['featured_count'] }}</div>
                        <p class="text-muted">Featured Stories</p>
                    </div>
                </div>
            </div>
            
            <!-- Testimonials Grid -->
            <div class="row g-4">
                @forelse($testimonials as $testimonial)
                    <div class="col-lg-4 col-md-6">
                        <div class="testimonial-card h-100">
                            @if($testimonial->is_featured)
                                <div class="featured-badge">
                                    <i class="ri-star-fill me-1"></i> Featured
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                {!! $testimonial->star_rating !!}
                            </div>
                            
                            <p class="testimonial-text mb-4">{{ $testimonial->testimonial_text }}</p>
                            
                            <div class="d-flex align-items-center gap-3">
                                @if($testimonial->hasPhoto())
                                    <img src="{{ $testimonial->photo_url }}" 
                                         alt="{{ $testimonial->student_name }}" 
                                         class="rounded-circle testimonial-avatar">
                                @else
                                    <div class="testimonial-initials">
                                        {{ $testimonial->initials }}
                                    </div>
                                @endif>
                                
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $testimonial->student_name }}</h6>
                                    @if($testimonial->student_course)
                                        <small class="text-muted d-block">{{ $testimonial->student_course }}</small>
                                    @endif
                                    @if($testimonial->score_achieved)
                                        <div class="score-badge">
                                            <i class="ri-medal-line me-1"></i> {{ $testimonial->score_achieved }}% Score
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="ri-chat-quote-line display-1 text-muted mb-4"></i>
                            <h4>No testimonials yet</h4>
                            <p class="text-muted">Check back soon for student success stories!</p>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($testimonials->hasPages())
                <div class="mt-5">
                    {{ $testimonials->links() }}
                </div>
            @endif
            
            <!-- Back to Home -->
            <div class="text-center mt-5">
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="ri-home-line me-2"></i> Back to Home
                </a>
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        .testimonial-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        
        .featured-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(135deg, #17a2b8, #2dc3d9);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(45, 195, 217, 0.3);
        }
        
        .testimonial-text {
            flex: 1;
            font-size: 0.95rem;
            line-height: 1.7;
            color: #555;
        }
        
        .testimonial-avatar {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        
        .testimonial-initials {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), #4a6bff);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .score-badge {
            display: inline-block;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .star-rating i {
            font-size: 1.1rem;
            margin-right: 2px;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }
        
        .bg-primary {
            background: linear-gradient(135deg, var(--primary-color), #4a6bff) !important;
        }
    </style>
@endsection