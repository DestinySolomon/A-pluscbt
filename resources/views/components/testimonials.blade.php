<section id="testimonials" class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">What Students Say</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Hear from students who have achieved success using our platform</p>
        </div>
        <div class="row g-4">
            @php
                $testimonials = [
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
            
            @foreach($testimonials as $testimonial)
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="mb-3">
                        @for($i = 0; $i < 5; $i++)
                        <i class="ri-star-fill text-warning"></i>
                        @endfor
                    </div>
                    <p class="text-muted mb-4">{{ $testimonial['text'] }}</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 48px; height: 48px;">
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
    </div>
</section>