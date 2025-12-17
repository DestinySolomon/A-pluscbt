<section class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Find answers to common questions about our platform</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    @php
                        $faqs = [
                            [
                                'question' => 'How does the CBT platform work?',
                                'answer' => 'Our platform simulates the real JAMB exam environment. After registration, you can select subjects, take timed practice exams with randomized questions, and receive instant results with detailed performance analytics.'
                            ],
                            [
                                'question' => 'Are the questions similar to actual JAMB exams?',
                                'answer' => 'Yes! All questions are created following the official JAMB syllabus and are designed to match the style, difficulty level, and format of actual JAMB examination questions.'
                            ],
                            [
                                'question' => 'Can I track my progress over time?',
                                'answer' => 'Absolutely! Our platform provides detailed analytics showing your performance trends, strengths, weaknesses, and improvement areas across all subjects and topics.'
                            ],
                            [
                                'question' => 'What happens if my internet connection drops during an exam?',
                                'answer' => 'Don\'t worry! Our platform automatically saves your answers as you go. If your connection drops, you can resume from where you left off when you reconnect.'
                            ],
                            [
                                'question' => 'Can I use this platform on my mobile device?',
                                'answer' => 'Yes! Our platform is fully responsive and works seamlessly on computers, tablets, and smartphones, allowing you to practice anywhere, anytime.'
                            ],
                        ];
                    @endphp
                    
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index + 1 }}">
                                {{ $faq['question'] }}
                            </button>
                        </h2>
                        <div id="faq{{ $index + 1 }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>