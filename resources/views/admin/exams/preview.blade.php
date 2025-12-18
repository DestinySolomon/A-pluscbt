@extends('layouts.admin')

@section('title', 'Exam Preview - ' . $exam->name)
@section('page-title', 'Exam Preview')
@section('mobile-title', 'Preview: ' . Str::limit($exam->name, 15))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.index') }}">Exams</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.show', $exam->id) }}">{{ Str::limit($exam->name, 15) }}</a>
</li>
<li class="breadcrumb-item active">Preview</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Back to Exam
    </a>
    <button type="button" class="btn-admin btn-admin-primary" onclick="startFullPreview()">
        <i class="ri-play-circle-line me-2"></i> Start Full Preview
    </button>
</div>
@endsection

@section('content')
<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0 text-muted">Exam Preview</h5>
        <p class="text-muted mb-0">This is how students will see the exam interface</p>
    </div>
    <div class="card-body">
        <!-- Exam Header -->
        <div class="admin-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="mb-2">{{ $exam->name }}</h3>
                        <p class="text-muted mb-3">{{ $exam->description }}</p>
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Duration</div>
                                    <div class="fw-bold h5">{{ $exam->duration_minutes }} min</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Questions</div>
                                    <div class="fw-bold h5">{{ $exam->total_questions }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Passing Score</div>
                                    <div class="fw-bold h5">{{ $exam->passing_score }}%</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Max Attempts</div>
                                    <div class="fw-bold h5">
                                        {{ $exam->max_attempts == 0 ? 'Unlimited' : $exam->max_attempts }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-4">
                            <h6 class="mb-3">Exam Instructions</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Total Time: {{ $exam->duration_minutes }} minutes
                                </li>
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Questions: {{ $exam->total_questions }}
                                </li>
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Passing Score: {{ $exam->passing_score }}%
                                </li>
                                @if($exam->shuffle_questions)
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Questions will be shuffled
                                </li>
                                @endif
                                @if($exam->shuffle_options)
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Options will be shuffled
                                </li>
                                @endif
                                <li>
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Results shown: {{ $exam->show_results_immediately ? 'Immediately' : 'Later' }}
                                </li>
                            </ul>
                            
                            <div class="mt-4">
                                <button type="button" class="btn-admin btn-admin-primary w-100" onclick="startFullPreview()">
                                    <i class="ri-play-circle-line me-2"></i> Start Exam Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Breakdown -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Subject Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Questions</th>
                                <th>Difficulty Distribution</th>
                                <th>Available Questions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exam->subjects as $subject)
                            @php
                                $availableCount = $subject->questions()->count();
                                $requiredCount = $subject->pivot->question_count;
                                $hasEnough = $availableCount >= $requiredCount;
                                
                                $difficulty = $subject->pivot->difficulty_distribution 
                                    ? json_decode($subject->pivot->difficulty_distribution, true)
                                    : ['easy' => 30, 'medium' => 50, 'hard' => 20];
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($subject->icon_class)
                                        <i class="{{ $subject->icon_class }} text-primary"></i>
                                        @endif
                                        <span>{{ $subject->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $subject->pivot->question_count }}</span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $difficulty['easy'] }}%" 
                                             title="Easy: {{ $difficulty['easy'] }}%"></div>
                                        <div class="progress-bar bg-warning" style="width: {{ $difficulty['medium'] }}%"
                                             title="Medium: {{ $difficulty['medium'] }}%"></div>
                                        <div class="progress-bar bg-danger" style="width: {{ $difficulty['hard'] }}%"
                                             title="Hard: {{ $difficulty['hard'] }}%"></div>
                                    </div>
                                    <small class="text-muted">
                                        Easy: {{ $difficulty['easy'] }}%, 
                                        Medium: {{ $difficulty['medium'] }}%, 
                                        Hard: {{ $difficulty['hard'] }}%
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-medium {{ $hasEnough ? 'text-success' : 'text-danger' }}">
                                            {{ $availableCount }}/{{ $requiredCount }}
                                        </span>
                                        @if($hasEnough)
                                            <i class="ri-checkbox-circle-line text-success"></i>
                                        @else
                                            <i class="ri-error-warning-line text-danger"></i>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sample Questions Preview -->
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Sample Questions Preview</h6>
                <small class="text-muted">Showing 2 sample questions per subject</small>
            </div>
            <div class="card-body">
                @if(isset($previewQuestions) && !empty($previewQuestions))
                    @foreach($previewQuestions as $subjectName => $questions)
                    <div class="mb-5">
                        <h6 class="mb-3 border-bottom pb-2">
                            <i class="ri-book-2-line me-2"></i>{{ $subjectName }}
                            <span class="badge bg-primary ms-2">{{ count($questions) }} questions</span>
                        </h6>
                        
                        @foreach($questions as $index => $question)
                        <div class="question-preview mb-4 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark">Q{{ $index + 1 }}</span>
                                    <span class="badge bg-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                    @if($question->topic)
                                    <span class="badge bg-info">{{ $question->topic->name }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">1 mark</small>
                            </div>
                            
                            <!-- Updated Question Display Section -->
                            <div class="mb-3">
                                {!! $question->question_text !!}
                            </div>

                            <div class="row g-2">
                                @php
                                    // Get options from the relationship
                                    $options = [];
                                    $correctOptionLetter = null;
                                    
                                    if ($question->options && $question->options->isNotEmpty()) {
                                        foreach ($question->options as $option) {
                                            $options[$option->option_letter] = $option->option_text;
                                            if ($option->is_correct) {
                                                $correctOptionLetter = $option->option_letter;
                                            }
                                        }
                                    } else {
                                        // Fallback to empty options
                                        $options = ['A' => '', 'B' => '', 'C' => '', 'D' => ''];
                                    }
                                @endphp
                                
                                @foreach($options as $letter => $option)
                                <div class="col-md-6">
                                    <div class="option-preview p-2 border rounded {{ $letter == $correctOptionLetter ? 'bg-success bg-opacity-10 border-success' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <span class="option-letter me-3 fw-medium 
                                                {{ $letter == $correctOptionLetter ? 'text-success' : 'text-muted' }}">
                                                {{ $letter }}.
                                            </span>
                                            <span>{{ $option }}</span>
                                            @if($letter == $correctOptionLetter)
                                            <i class="ri-checkbox-circle-line text-success ms-2"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <!-- End Updated Question Display Section -->
                            
                            @if($question->explanation)
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted d-block mb-1">Explanation:</small>
                                <small>{{ $question->explanation }}</small>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="ri-file-list-line text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">No sample questions available for preview.</p>
                        <button type="button" class="btn-admin btn-admin-secondary mt-2" onclick="generateSampleQuestions()">
                            <i class="ri-refresh-line me-2"></i> Generate Sample Questions
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Full Preview Modal -->
<div class="modal fade" id="fullPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exam Preview - {{ $exam->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="exam-interface-preview">
                    <!-- Timer -->
                    <div class="timer-container mb-4 p-3 bg-dark text-white rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Time Remaining</h5>
                                <small class="text-light">Exam ends in:</small>
                            </div>
                            <div class="text-center">
                                <div class="display-4 fw-bold" id="previewTimer">02:00:00</div>
                                <small class="text-light">hours : minutes : seconds</small>
                            </div>
                            <button class="btn-admin btn-admin-warning">
                                <i class="ri-flag-line me-2"></i> Submit Exam
                            </button>
                        </div>
                    </div>
                    
                    <!-- Question Navigation -->
                    <div class="question-nav mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>Question Navigation</h6>
                            <div>
                                <span class="badge bg-success me-2">Answered: 0</span>
                                <span class="badge bg-secondary me-2">Unanswered: {{ $exam->total_questions }}</span>
                                <span class="badge bg-warning">Flagged: 0</span>
                            </div>
                        </div>
                        
                        <div class="question-grid">
                            @for($i = 1; $i <= min($exam->total_questions, 50); $i++)
                            <button type="button" class="question-number-btn">
                                {{ $i }}
                            </button>
                            @endfor
                            
                            @if($exam->total_questions > 50)
                            <button type="button" class="question-number-btn">
                                ...
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Sample Question Interface -->
                    <div class="question-interface-preview p-4 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h5>Question 1 of {{ $exam->total_questions }}</h5>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-success">Easy</span>
                                    <span class="badge bg-primary">English</span>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flagQuestion">
                                <label class="form-check-label" for="flagQuestion">Flag for Review</label>
                            </div>
                        </div>
                        
                        <div class="question-content mb-4">
                            <p class="lead">What is the capital city of Nigeria?</p>
                        </div>
                        
                        <div class="options-container">
                            <div class="option-item mb-2 p-3 border rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="previewQuestion" id="optionA">
                                    <label class="form-check-label w-100" for="optionA">
                                        A. Lagos
                                    </label>
                                </div>
                            </div>
                            
                            <div class="option-item mb-2 p-3 border rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="previewQuestion" id="optionB">
                                    <label class="form-check-label w-100" for="optionB">
                                        B. Abuja
                                    </label>
                                </div>
                            </div>
                            
                            <div class="option-item mb-2 p-3 border rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="previewQuestion" id="optionC">
                                    <label class="form-check-label w-100" for="optionC">
                                        C. Port Harcourt
                                    </label>
                                </div>
                            </div>
                            
                            <div class="option-item mb-2 p-3 border rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="previewQuestion" id="optionD">
                                    <label class="form-check-label w-100" for="optionD">
                                        D. Kano
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="navigation-buttons mt-4 d-flex justify-content-between">
                            <button class="btn-admin btn-admin-secondary" disabled>
                                <i class="ri-arrow-left-line me-2"></i> Previous
                            </button>
                            <div>
                                <button class="btn-admin btn-admin-warning me-2">
                                    <i class="ri-flag-line me-2"></i> Mark for Review
                                </button>
                                <button class="btn-admin btn-admin-primary">
                                    Next <i class="ri-arrow-right-line ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Instructions -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">Quick Instructions</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">
                                    <i class="ri-keyboard-line me-2"></i>
                                    Use <kbd>1</kbd>-<kbd>4</kbd> keys to select options
                                </small>
                                <small class="text-muted d-block mb-1">
                                    <i class="ri-arrow-left-right-line me-2"></i>
                                    Use <kbd>←</kbd> <kbd>→</kbd> arrows to navigate
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">
                                    <i class="ri-flag-line me-2"></i>
                                    Press <kbd>F</kbd> to flag question
                                </small>
                                <small class="text-muted d-block">
                                    <i class="ri-save-line me-2"></i>
                                    Auto-saves every 30 seconds
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">
                    Close Preview
                </button>
                <button type="button" class="btn-admin btn-admin-danger" onclick="submitPreviewExam()">
                    <i class="ri-send-plane-line me-2"></i> Submit Exam
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function startFullPreview() {
    const modal = new bootstrap.Modal(document.getElementById('fullPreviewModal'));
    modal.show();
    
    // Start timer
    let totalSeconds = {{ $exam->duration_minutes }} * 60;
    const timerElement = document.getElementById('previewTimer');
    
    const timerInterval = setInterval(() => {
        if (totalSeconds <= 0) {
            clearInterval(timerInterval);
            timerElement.textContent = '00:00:00';
            return;
        }
        
        totalSeconds--;
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        timerElement.textContent = 
            `${hours.toString().padStart(2, '0')}:` +
            `${minutes.toString().padStart(2, '0')}:` +
            `${seconds.toString().padStart(2, '0')}`;
    }, 1000);
}

function generateSampleQuestions() {
    // This would normally make an AJAX request to generate sample questions
    alert('This would generate sample questions for preview. In a real implementation, this would make an API call.');
}

function submitPreviewExam() {
    if (confirm('Are you sure you want to submit the exam? This will end the preview.')) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('fullPreviewModal'));
        modal.hide();
        alert('Exam submitted! In real implementation, this would process and show results.');
    }
}

// Keyboard shortcuts for preview
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('fullPreviewModal');
    if (modal && modal.classList.contains('show')) {
        // Only work in preview mode
        switch(e.key) {
            case '1':
            case '2':
            case '3':
            case '4':
                const optionIndex = parseInt(e.key) - 1;
                const options = document.querySelectorAll('input[name="previewQuestion"]');
                if (options[optionIndex]) {
                    options[optionIndex].checked = true;
                }
                break;
            case 'f':
            case 'F':
                const flagCheckbox = document.getElementById('flagQuestion');
                if (flagCheckbox) {
                    flagCheckbox.checked = !flagCheckbox.checked;
                }
                break;
            case 'Escape':
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
                break;
        }
    }
});
</script>
<style>
.question-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 0.5rem;
}

.question-number-btn {
    width: 40px;
    height: 40px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    transition: all 0.2s;
}

.question-number-btn:hover {
    background: #f8f9fa;
    border-color: var(--primary-color);
}

.option-preview {
    transition: all 0.2s;
}

.option-preview:hover {
    background-color: #f8f9fa;
}

.option-letter {
    min-width: 24px;
}

.question-interface-preview {
    background-color: #f8f9fa;
}

.option-item {
    transition: all 0.2s;
    cursor: pointer;
}

.option-item:hover {
    background-color: #e9ecef;
}

.option-item .form-check-input:checked ~ label {
    font-weight: 600;
    color: var(--primary-color);
}

.timer-container {
    background: linear-gradient(135deg, #2c3e50, #4a6491);
}

.display-4 {
    font-family: monospace;
}

@media (max-width: 768px) {
    .question-grid {
        grid-template-columns: repeat(5, 1fr);
    }
    
    .question-number-btn {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .timer-container .display-4 {
        font-size: 2rem;
    }
}
</style>
@endpush