<section id="testimonials" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">What Students Say</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                Hear from students who have achieved success using our platform
            </p>
        </div>

        @php
            // Fetch approved testimonials from database
            $testimonials = App\Models\Testimonial::approved()
                ->orderBy('display_order')
                ->orderBy('created_at', 'desc')
                ->take(9) // Limit to 9 testimonials
                ->get();
            
            // Get featured testimonials first
            $featuredTestimonials = $testimonials->where('is_featured', true);
            $otherTestimonials = $testimonials->where('is_featured', false);
        @endphp
        
        @if($testimonials->count() > 0)
            <!-- Featured Testimonials -->
            @if($featuredTestimonials->count() > 0)
                <div class="row mb-5">
                    <div class="col-12">
                        <h4 class="text-center mb-4 text-primary">
                            <i class="ri-star-fill me-2"></i> Featured Stories
                        </h4>
                    </div>
                    @foreach($featuredTestimonials as $testimonial)
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card h-100">
                                <div class="mb-3">
                                    {!! $testimonial->star_rating !!}
                                    @if($testimonial->is_featured)
                                        <span class="badge bg-info ms-2">Featured</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-4">{{ $testimonial->testimonial_text }}</p>
                                <div class="d-flex align-items-center gap-3">
                                    @if($testimonial->hasPhoto())
                                        <img src="{{ $testimonial->photo_url }}" 
                                             alt="{{ $testimonial->student_name }}" 
                                             class="rounded-circle" 
                                             style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 48px; height: 48px;">
                                            {{ $testimonial->initials }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $testimonial->student_name }}</h6>
                                        @if($testimonial->student_course)
                                            <small class="text-muted d-block">{{ $testimonial->student_course }}</small>
                                        @endif
                                        @if($testimonial->score_achieved)
                                            <small class="text-success fw-medium">JAMB Score: {{ $testimonial->score_achieved }}%</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- All Testimonials -->
            <div class="row g-4">
                @foreach($otherTestimonials as $testimonial)
                    <div class="col-md-4">
                        <div class="testimonial-card h-100">
                            <div class="mb-3">
                                {!! $testimonial->star_rating !!}
                            </div>
                            <p class="text-muted mb-4">{{ $testimonial->testimonial_text }}</p>
                            <div class="d-flex align-items-center gap-3">
                                @if($testimonial->hasPhoto())
                                    <img src="{{ $testimonial->photo_url }}" 
                                         alt="{{ $testimonial->student_name }}" 
                                         class="rounded-circle" 
                                         style="width: 48px; height: 48px; object-fit: cover;">
                                @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 48px; height: 48px;">
                                        {{ $testimonial->initials }}
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $testimonial->student_name }}</h6>
                                    @if($testimonial->student_course)
                                        <small class="text-muted d-block">{{ $testimonial->student_course }}</small>
                                    @endif
                                    @if($testimonial->score_achieved)
                                        <small class="text-success fw-medium">Score: {{ $testimonial->score_achieved }}%</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- View More Button -->
            @if($testimonials->count() >= 3)
                <div class="text-center mt-5">
                   
<a href="{{ route('testimonials.index') }}" class="btn btn-primary btn-lg">
                        <i class="ri-eye-line me-2"></i> View More Success Stories
                    </a>
                </div>
            @endif
            
        @else
            <!-- Fallback if no testimonials -->
            <div class="row">
                @php
                    $fallbackTestimonials = [
                        [
                            'name' => 'Adewale Musa',
                            'initials' => 'AM',
                            'score' => '287',
                            'text' => '"This platform helped me improve my exam speed and accuracy. The timed practice sessions made me more confident for the actual JAMB exam."'
                        ],
                        [
                            'name' => 'Chioma Okonkwo',
                            'initials' => 'CO',
                            'score' => '294',
                            'text' => '"The instant feedback feature is amazing! I could identify my weak areas and focus on improving them. Highly recommended for all JAMB candidates."'
                        ],
                        [
                            'name' => 'Ibrahim Bello',
                            'initials' => 'IB',
                            'score' => '301',
                            'text' => '"Best CBT practice platform I\'ve used. The interface is clean, questions are quality, and the performance tracking helps me see my progress clearly."'
                        ],
                    ];
                @endphp
                
                @foreach($fallbackTestimonials as $testimonial)
                    <div class="col-md-4">
                        <div class="testimonial-card">
                            <div class="mb-3">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="ri-star-fill text-warning"></i>
                                @endfor
                            </div>
                            <p class="text-muted mb-4">{{ $testimonial['text'] }}</p>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                     style="width: 48px; height: 48px;">
                                    {{ $testimonial['initials'] }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $testimonial['name'] }}</h6>
                                    <small class="text-muted">JAMB Score: {{ $testimonial['score'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@push('styles')
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
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        
        .testimonial-card p {
            flex: 1;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        
        .bg-primary {
            background: linear-gradient(135deg, var(--primary-color), #4a6bff) !important;
        }
        
        .bg-info {
            background: linear-gradient(135deg, #17a2b8, #2dc3d9) !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .star-rating i {
            font-size: 1.1rem;
            margin-right: 2px;
        }
    </style>
@endpush