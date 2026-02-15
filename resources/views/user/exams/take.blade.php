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

function updateQuestionDisplay() {
    if (!examState.currentQuestion) return;
    
    const question = examState.currentQuestion;
    const questionNumber = examData.currentIndex + 1;
    const userAnswer = examState.userAnswers[question.id];
    const isMarked = examState.markedQuestions.includes(question.id);
    
    // Update question header
    $('#currentQuestionNumber').text(`Question ${questionNumber}`);
    $('#questionSubject').text(question.subject?.name || 'General');
    
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
        const isAnswered = false; // We'll update this after loading question data
        const isMarked = examState.markedQuestions.includes(i + 1); // Temporary
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
        url: '{{ route("user.exams.save-time", $exam->id) }}', // You'll need to create this route
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