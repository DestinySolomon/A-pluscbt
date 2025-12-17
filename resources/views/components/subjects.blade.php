<section id="subjects" class="py-5" style="background: linear-gradient(135deg, var(--teal-50) 0%, var(--emerald-50) 100%);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Available Subjects</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Practice across multiple subjects with thousands of JAMB-style questions</p>
        </div>
        <div class="row g-4">
            @php
                $subjects = [
                    ['name' => 'English Language', 'questions' => '2,500', 'icon' => 'ri-english-input', 'color' => 'teal'],
                    ['name' => 'Mathematics', 'questions' => '2,800', 'icon' => 'ri-calculator-line', 'color' => 'blue'],
                    ['name' => 'Physics', 'questions' => '1,800', 'icon' => 'ri-flask-line', 'color' => 'purple'],
                    ['name' => 'Chemistry', 'questions' => '1,900', 'icon' => 'ri-test-tube-line', 'color' => 'emerald'],
                    ['name' => 'Biology', 'questions' => '2,100', 'icon' => 'ri-microscope-line', 'color' => 'green'],
                    ['name' => 'Economics', 'questions' => '1,600', 'icon' => 'ri-line-chart-line', 'color' => 'orange'],
                    ['name' => 'Government', 'questions' => '1,500', 'icon' => 'ri-government-line', 'color' => 'indigo'],
                    ['name' => 'Literature', 'questions' => '1,400', 'icon' => 'ri-book-2-line', 'color' => 'pink'],
                ];
            @endphp
            
            @foreach($subjects as $subject)
            <div class="col-sm-6 col-lg-3">
                <div class="subject-card">
                    <div class="feature-icon icon-{{ $subject['color'] }} mb-3">
                        <i class="{{ $subject['icon'] }}"></i>
                    </div>
                    <h6 class="fw-bold mb-2">{{ $subject['name'] }}</h6>
                    <p class="text-muted small mb-0">{{ $subject['questions'] }} Questions</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>