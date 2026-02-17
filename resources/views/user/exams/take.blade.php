@extends('layouts.exam')

@php
    $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
@endphp

@section('styles')
<style>
    /* Additional exam styles */
    .exam-container {
        min-height: calc(100vh - 80px);
    }
    
    /* Ensure proper spacing */
    .question-content-card {
        min-height: 400px;
    }
    
    /* Image styling */
    .question-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Option image styling */
    .option-image {
        max-width: 200px;
        max-height: 150px;
        margin-top: 10px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    /* NEW: Passage Styles */
    .passage-container {
        background-color: #f8f9fa;
        border-left: 4px solid #14b8a6;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .passage-title {
        color: #14b8a6;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.2rem;
    }
    
    .passage-content {
        font-size: 1rem;
        line-height: 1.7;
        color: #374151;
    }
    
    .passage-instruction {
        font-style: italic;
        color: #6b7280;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #dee2e6;
    }
    
    .passage-badge {
        display: inline-block;
        background-color: #e5e7eb;
        color: #4b5563;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        margin-right: 0.5rem;
    }
    
    .passage-ref-link {
        color: #14b8a6;
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
    }
    
    .passage-ref-link:hover {
        text-decoration: underline;
    }
    
    .passage-collapsed {
        background-color: #f0fdf4;
        border: 1px solid #14b8a6;
        border-radius: 6px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .question-passage-info {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        font-size: 0.95rem;
    }
    /* END: Passage Styles */
</style>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
        <p class="mt-3">Loading...</p>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<div id="examData" 
     data-exam-id="{{ $exam->id }}"
     data-attempt-id="{{ $attempt->id }}"
     data-duration="{{ $exam->duration_minutes }}"
     data-total-questions="{{ $totalQuestions }}"
     data-current-index="{{ $currentQuestionIndex }}"
     data-answered-count="{{ $answeredCount }}"
     data-marked-count="{{ $markedCount }}"
     style="display: none;">
</div>

<!-- NEW: Hidden passages data for JavaScript -->
@if(isset($passages) && $passages->count() > 0)
<div id="passagesData" style="display: none;">
    @json($passages)
</div>
@endif

<!-- JavaScript for exam functionality -->
<script>
// Exam data from server
const examData = {
    examId: {{ $exam->id }},
    attemptId: {{ $attempt->id }},
    duration: {{ $exam->duration_minutes }},
    totalQuestions: {{ $totalQuestions }},
    currentIndex: {{ $currentQuestionIndex }},
    answeredCount: {{ $answeredCount }},
    markedCount: {{ $markedCount }},
    csrfToken: '{{ csrf_token() }}'
};

// NEW: Store passages for reference
let passagesData = {};
@if(isset($passages) && $passages->count() > 0)
    passagesData = @json($passages);
@endif

// State management
let examState = {
    timeRemaining: {{ $attempt->time_remaining ?? $exam->duration_minutes * 60 }},
    userAnswers: @json($userAnswers),
    markedQuestions: @json($markedQuestions),
    timerInterval: null,
    currentQuestion: @json($currentQuestion)
};

// Initialize exam
$(document).ready(function() {
    initializeExam();
    startTimer();
    updateQuestionDisplay();
    generateQuestionButtons();
    updateSummary();
    
    // Setup event listeners
    setupEventListeners();
});

function initializeExam() {
    // Set exam title
    $('#examTitle').text("{{ $exam->name }}");
    $('#totalQuestions').text(examData.totalQuestions);
    $('#modalTotalQuestions').text(examData.totalQuestions);
    
    // Update subject info if available
    @if($currentQuestion && $currentQuestion->subject)
        $('#subjectInfo').text("{{ $currentQuestion->subject->name }}");
    @endif
}

function startTimer() {
    clearInterval(examState.timerInterval);
    
    examState.timerInterval = setInterval(function() {
        examState.timeRemaining--;
        updateTimerDisplay();
        
        if (examState.timeRemaining <= 0) {
            clearInterval(examState.timerInterval);
            autoSubmitExam();
        }
        
        // Warning at 5 minutes
        if (examState.timeRemaining === 300) {
            showTimeWarning();
        }
        
        // Auto-save time remaining every 30 seconds
        if (examState.timeRemaining % 30 === 0) {
            saveTimeRemaining();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const hours = Math.floor(examState.timeRemaining / 3600);
    const minutes = Math.floor((examState.timeRemaining % 3600) / 60);
    const seconds = examState.timeRemaining % 60;
    
    $('#timerHours').text(hours.toString().padStart(2, '0'));
    $('#timerMinutes').text(minutes.toString().padStart(2, '0'));
    $('#timerSeconds').text(seconds.toString().padStart(2, '0'));
    $('#summaryTimer').text(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
}

// NEW: Function to display passage
function displayPassage(question) {
    if (!question.passage) return '';
    
    const passage = question.passage;
    const isFirstQuestionInPassage = question.question_number === 1;
    
    if (isFirstQuestionInPassage) {
        // Full passage display for first question
        return `
            <div class="passage-container" id="currentPassage">
                <h5 class="passage-title">
                    <i class="ri-file-text-line me-2"></i>
                    ${passage.title || 'Comprehension Passage'}
                </h5>
                <div class="passage-content">
                    ${passage.content.replace(/\n/g, '<br>')}
                </div>
                ${passage.instruction ? `
                    <div class="passage-instruction">
                        <i class="ri-questionnaire-line me-2"></i>
                        ${passage.instruction}
                    </div>
                ` : ''}
                ${passage.image_path ? `
                    <div class="mt-3 text-center">
                        <img src="/storage/${passage.image_path}" alt="Passage Image" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                ` : ''}
            </div>
        `;
    } else {
        // Collapsed view for subsequent questions
        return `
            <div class="passage-collapsed" id="currentPassage">
                <div>
                    <i class="ri-file-copy-line me-2 text-primary"></i>
                    <strong>${passage.title || 'Comprehension Passage'}</strong>
                    <span class="passage-badge ms-2">Question ${question.question_number} of ${getPassageQuestionCount(passage.id)}</span>
                </div>
                <a href="#" onclick="scrollToPassage(); return false;" class="passage-ref-link">
                    <i class="ri-eye-line me-1"></i> View Passage
                </a>
            </div>
        `;
    }
}

// NEW: Helper to get passage question count
function getPassageQuestionCount(passageId) {
    if (!passagesData[passageId]) return 0;
    return passagesData[passageId].questions ? passagesData[passageId].questions.length : 0;
}

// NEW: Scroll to passage function
function scrollToPassage() {
    const passageElement = document.getElementById('currentPassage');
    if (passageElement) {
        passageElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function updateQuestionDisplay() {
    if (!examState.currentQuestion) return;
    
    const question = examState.currentQuestion;
    const questionNumber = examData.currentIndex + 1;
    const userAnswer = examState.userAnswers[question.id];
    const isMarked = examState.markedQuestions.includes(question.id);
    
    // Update question header
    $('#currentQuestionNumber').text(`Question ${questionNumber}`);
    $('#questionSubject').text(question.subject?.name || 'General');
    
    // NEW: Display passage if question belongs to one
    if (question.passage) {
        $('#passageContainer').html(displayPassage(question));
        $('#passageContainer').show();
    } else {
        $('#passageContainer').hide();
    }
    
    // Update question text
    $('#questionText').html(`<p>${question.question_text}</p>`);
    
    // Show/hide question image
    const imageContainer = $('#questionImageContainer');
    const questionImage = $('#questionImage');
    
    if (question.image_path) {
        questionImage.attr('src', `/storage/${question.image_path}`);
        imageContainer.show();
    } else {
        imageContainer.hide();
    }
    
    // Clear and load options
    $('#optionsContainer').empty();
    
    if (question.options && question.options.length > 0) {
        question.options.forEach(option => {
            const isSelected = userAnswer === option.option_letter;
            const optionHtml = `
                <div class="option-item">
                    <label class="option-label ${isSelected ? 'selected' : ''}" 
                           data-question-id="${question.id}" 
                           data-option="${option.option_letter}">
                        <span class="option-letter">${option.option_letter}</span>
                        <span class="option-text">${option.option_text}</span>
                        ${option.image_path ? `
                            <div class="mt-2">
                                <img src="/storage/${option.image_path}" alt="Option Image" class="option-image img-fluid">
                            </div>
                        ` : ''}
                    </label>
                </div>
            `;
            $('#optionsContainer').append(optionHtml);
        });
    }
    
    // Update mark button
    $('#markForReviewBtn').html(`
        <i class="${isMarked ? 'ri-flag-fill' : 'ri-flag-line'} me-1"></i>
        ${isMarked ? 'Unmark Review' : 'Mark for Review'}
    `);
    $('#markForReviewBtn')
        .toggleClass('btn-warning', isMarked)
        .toggleClass('btn-outline-warning', !isMarked);
    
    // Update question navigation buttons
    updateQuestionButtons();
}

function generateQuestionButtons() {
    $('#questionButtons').empty();
    
    for (let i = 0; i < examData.totalQuestions; i++) {
        // In a real implementation, you'd need to track which questions are answered
        // This is simplified - you should enhance based on your data structure
        const isAnswered = false; 
        const isMarked = examState.markedQuestions.includes(i + 1);
        const isCurrent = i === examData.currentIndex;
        
        let buttonClass = 'question-btn';
        if (isCurrent) buttonClass += ' current';
        else if (isMarked) buttonClass += ' marked';
        else if (isAnswered) buttonClass += ' answered';
        else buttonClass += ' not-answered';
        
        const buttonHtml = `
            <button class="${buttonClass}" data-question-index="${i}">
                ${i + 1}
            </button>
        `;
        $('#questionButtons').append(buttonHtml);
    }
    
    // Add click handlers
    $('.question-btn').on('click', function() {
        const index = $(this).data('question-index');
        if (index !== examData.currentIndex) {
            loadQuestionByIndex(index);
        }
    });
}

function updateQuestionButtons() {
    $('.question-btn').removeClass('current');
    $(`.question-btn[data-question-index="${examData.currentIndex}"]`).addClass('current');
}

function updateSummary() {
    const answeredCount = Object.keys(examState.userAnswers).length;
    const markedCount = examState.markedQuestions.length;
    
    $('#answeredCount').text(answeredCount);
    $('#notAnsweredCount').text(examData.totalQuestions - answeredCount);
    $('#markedCount').text(markedCount);
    
    $('#modalAnsweredCount').text(answeredCount);
    $('#modalMarkedCount').text(markedCount);
}

function setupEventListeners() {
    // Option selection
    $(document).on('click', '.option-label', function() {
        const questionId = $(this).data('question-id');
        const option = $(this).data('option');
        
        // Remove selection from other options
        $(`.option-label[data-question-id="${questionId}"]`).removeClass('selected');
        
        // Add selection to clicked option
        $(this).addClass('selected');
        
        // Save answer
        saveAnswer(questionId, option);
    });
    
    // Mark for review
    $('#markForReviewBtn').click(function() {
        const questionId = examState.currentQuestion.id;
        const isCurrentlyMarked = examState.markedQuestions.includes(questionId);
        
        toggleMarkForReview(questionId, !isCurrentlyMarked);
    });
    
    // Navigation buttons
    $('#prevQuestionBtn').click(function() {
        loadQuestionByIndex(examData.currentIndex - 1);
    });
    
    $('#nextQuestionBtn').click(function() {
        loadQuestionByIndex(examData.currentIndex + 1);
    });
    
    // Clear selection
    $('#clearSelectionBtn').click(function() {
        const questionId = examState.currentQuestion.id;
        clearAnswer(questionId);
    });
    
    // Submit exam
    $('#submitExamBtn').click(function() {
        $('#submitExamModal').modal('show');
    });
    
    $('#confirmSubmitBtn').click(function() {
        submitExam();
    });
    
    // Prevent accidental navigation
    $(window).on('beforeunload', function() {
        if (examState.timeRemaining > 0) {
            return "Are you sure you want to leave? Your exam progress will be lost.";
        }
    });
}

function loadQuestionByIndex(index) {
    if (index < 0 || index >= examData.totalQuestions) return;
    
    showLoading();
    
    $.ajax({
        url: '{{ route("user.exams.get-question", $exam->id) }}',
        method: 'GET',
        data: {
            direction: index > examData.currentIndex ? 'next' : 'prev',
            current_index: examData.currentIndex,
            _token: examData.csrfToken
        },
        success: function(response) {
            if (response.success) {
                examData.currentIndex = response.current_index;
                examState.currentQuestion = response.question;
                
                // Update user answer
                if (response.user_answer) {
                    examState.userAnswers[response.question.id] = response.user_answer;
                } else {
                    delete examState.userAnswers[response.question.id];
                }
                
                // Update marked status
                const questionId = response.question.id;
                if (response.is_marked) {
                    if (!examState.markedQuestions.includes(questionId)) {
                        examState.markedQuestions.push(questionId);
                    }
                } else {
                    examState.markedQuestions = examState.markedQuestions.filter(id => id !== questionId);
                }
                
                updateQuestionDisplay();
                updateSummary();
            }
        },
        error: function() {
            alert('Error loading question. Please try again.');
        },
        complete: function() {
            hideLoading();
        }
    });
}

function saveAnswer(questionId, option) {
    examState.userAnswers[questionId] = option;
    
    $.ajax({
        url: '{{ route("user.exams.save-answer", $exam->id) }}',
        method: 'POST',
        data: {
            question_id: questionId,
            selected_option: option,
            _token: examData.csrfToken
        },
        success: function(response) {
            if (response.success) {
                examData.answeredCount = response.answered_count;
                updateSummary();
                generateQuestionButtons();
            }
        },
        error: function() {
            alert('Error saving answer. Please try again.');
        }
    });
}

function toggleMarkForReview(questionId, mark) {
    $.ajax({
        url: '{{ route("user.exams.save-answer", $exam->id) }}',
        method: 'POST',
        data: {
            question_id: questionId,
            marked: mark,
            _token: examData.csrfToken
        },
        success: function(response) {
            if (response.success) {
                if (mark) {
                    if (!examState.markedQuestions.includes(questionId)) {
                        examState.markedQuestions.push(questionId);
                    }
                } else {
                    examState.markedQuestions = examState.markedQuestions.filter(id => id !== questionId);
                }
                
                examData.markedCount = examState.markedQuestions.length;
                updateQuestionDisplay();
                updateSummary();
                generateQuestionButtons();
            }
        }
    });
}

function clearAnswer(questionId) {
    delete examState.userAnswers[questionId];
    
    $(`.option-label[data-question-id="${questionId}"]`).removeClass('selected');
    
    $.ajax({
        url: '{{ route("user.exams.save-answer", $exam->id) }}',
        method: 'POST',
        data: {
            question_id: questionId,
            selected_option: '',
            _token: examData.csrfToken
        },
        success: function(response) {
            if (response.success) {
                examData.answeredCount = response.answered_count;
                updateSummary();
                generateQuestionButtons();
            }
        }
    });
}

function saveTimeRemaining() {
    $.ajax({
        url: '{{ route("user.exams.save-time", $exam->id) }}',
        method: 'POST',
        data: {
            time_remaining: examState.timeRemaining,
            _token: examData.csrfToken
        }
    });
}

function submitExam() {
    // First save remaining time
    saveTimeRemaining();
    
    // Submit the exam
    $.ajax({
        url: '{{ route("user.exams.submit", $exam->id) }}',
        method: 'POST',
        data: {
            _token: examData.csrfToken
        },
        success: function(response) {
            if (response.success || response.redirect) {
                window.onbeforeunload = null; // Remove navigation warning
                window.location.href = response.redirect || '{{ route("user.results.show", $attempt->id) }}';
            }
        },
        error: function() {
            alert('Error submitting exam. Please try again.');
        }
    });
}

function autoSubmitExam() {
    if (confirm('Time is up! Your exam will be automatically submitted.')) {
        submitExam();
    }
}

function showTimeWarning() {
    const warningHtml = `
        <div class="alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1060;">
            <i class="ri-alarm-warning-line me-2"></i>
            <strong>Time Warning!</strong> Only 5 minutes remaining.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(warningHtml);
    
    // Auto remove after 10 seconds
    setTimeout(() => {
        $('.alert-warning.position-fixed').alert('close');
    }, 10000);
}

function showLoading() {
    $('#loadingOverlay').removeClass('d-none');
}

function hideLoading() {
    $('#loadingOverlay').addClass('d-none');
}

// Initialize on page load
$(window).on('load', function() {
    hideLoading();
});
</script>
@endsection

@section('content')
<!-- This section needs to be added to your existing layout -->
<div class="exam-container">
    <!-- Header with timer and controls -->
    <div class="exam-header">
        <!-- Your existing header code -->
    </div>
    
    <!-- NEW: Passage Container -->
    <div id="passageContainer" style="display: none;"></div>
    
    <!-- Question Display -->
    <div class="question-card">
        <div class="question-header">
            <span id="currentQuestionNumber" class="question-number"></span>
            <span id="questionSubject" class="question-subject"></span>
        </div>
        
        <!-- Question Image -->
        <div id="questionImageContainer" style="display: none;" class="mb-3">
            <img id="questionImage" src="" alt="Question Image" class="img-fluid">
        </div>
        
        <!-- Question Text -->
        <div id="questionText" class="question-text mb-4"></div>
        
        <!-- Options Container -->
        <div id="optionsContainer" class="options-container"></div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button id="clearSelectionBtn" class="btn btn-outline-secondary">
                <i class="ri-eraser-line me-1"></i> Clear
            </button>
            <button id="markForReviewBtn" class="btn btn-outline-warning">
                <i class="ri-flag-line me-1"></i> Mark for Review
            </button>
        </div>
    </div>
    
    <!-- Navigation -->
    <div class="navigation-buttons">
        <button id="prevQuestionBtn" class="btn btn-primary">
            <i class="ri-arrow-left-line me-1"></i> Previous
        </button>
        <button id="nextQuestionBtn" class="btn btn-primary">
            Next <i class="ri-arrow-right-line me-1"></i>
        </button>
    </div>
    
    <!-- Question Palette -->
    <div class="question-palette">
        <h5>Questions</h5>
        <div id="questionButtons" class="question-grid"></div>
    </div>
    
    <!-- Submit Button -->
    <div class="submit-section">
        <button id="submitExamBtn" class="btn btn-success btn-lg">
            <i class="ri-check-double-line me-2"></i> Submit Exam
        </button>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitExamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit your exam?</p>
                <div class="alert alert-info">
                    <strong>Summary:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Answered: <span id="modalAnsweredCount">0</span>/<span id="modalTotalQuestions">{{ $totalQuestions }}</span></li>
                        <li>Marked for Review: <span id="modalMarkedCount">0</span></li>
                        <li>Unanswered: <span id="modalUnansweredCount">0</span></li>
                    </ul>
                </div>
                <p class="text-warning">
                    <i class="ri-alert-line me-1"></i>
                    You cannot change your answers after submission.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmSubmitBtn">
                    <i class="ri-check-double-line me-1"></i> Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>
@endsection