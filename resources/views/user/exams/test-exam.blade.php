@extends('layouts.exam')

@section('styles')
<style>
    /* Additional styles for testing */
</style>
@endsection

@section('content')
<script>
    // Mock exam data for testing
    const examData = {
        id: 1,
        name: "JAMB Practice Test 2024",
        duration: 120, // minutes
        totalQuestions: 10,
        questions: [
            {
                id: 1,
                number: 1,
                subject: "Mathematics",
                text: "If the sum of two numbers is 20 and their product is 75, what are the numbers?",
                options: [
                    { letter: "A", text: "5 and 15" },
                    { letter: "B", text: "10 and 10" },
                    { letter: "C", text: "8 and 12" },
                    { letter: "D", text: "7 and 13" }
                ],
                correctAnswer: "A",
                hasImage: false
            },
            {
                id: 2,
                number: 2,
                subject: "English",
                text: "Choose the word that best completes the sentence: She was ______ tired that she went to bed early.",
                options: [
                    { letter: "A", text: "so" },
                    { letter: "B", text: "such" },
                    { letter: "C", text: "very" },
                    { letter: "D", text: "too" }
                ],
                correctAnswer: "A",
                hasImage: false
            },
            {
                id: 3,
                number: 3,
                subject: "Physics",
                text: "What is the SI unit of force?",
                options: [
                    { letter: "A", text: "Joule" },
                    { letter: "B", text: "Watt" },
                    { letter: "C", text: "Newton" },
                    { letter: "D", text: "Pascal" }
                ],
                correctAnswer: "C",
                hasImage: false
            }
        ]
    };

    // User answers
    let userAnswers = {};
    let markedQuestions = new Set();
    let currentQuestionIndex = 0;
    let timeRemaining = examData.duration * 60; // Convert to seconds

    // Initialize exam
    $(document).ready(function() {
        initializeExam();
        startTimer();
        loadQuestion(currentQuestionIndex);
        generateQuestionButtons();
        updateSummary();
    });

    function initializeExam() {
        // Set exam title
        $('#examTitle').text(examData.name);
        $('#totalQuestions').text(examData.totalQuestions);
        $('#modalTotalQuestions').text(examData.totalQuestions);
    }

    function startTimer() {
        const timerInterval = setInterval(function() {
            timeRemaining--;
            updateTimerDisplay();
            
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                autoSubmitExam();
            }
            
            // Warning at 5 minutes
            if (timeRemaining === 300) {
                showTimeWarning();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const hours = Math.floor(timeRemaining / 3600);
        const minutes = Math.floor((timeRemaining % 3600) / 60);
        const seconds = timeRemaining % 60;
        
        $('#timerHours').text(hours.toString().padStart(2, '0'));
        $('#timerMinutes').text(minutes.toString().padStart(2, '0'));
        $('#timerSeconds').text(seconds.toString().padStart(2, '0'));
        $('#summaryTimer').text(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
    }

    function loadQuestion(index) {
        if (index < 0 || index >= examData.questions.length) return;
        
        currentQuestionIndex = index;
        const question = examData.questions[index];
        
        // Update question header
        $('#currentQuestionNumber').text(`Question ${question.number}`);
        $('#questionSubject').text(question.subject);
        
        // Update question text
        $('#questionText').html(`<p>${question.text}</p>`);
        
        // Clear and load options
        $('#optionsContainer').empty();
        
        question.options.forEach(option => {
            const isSelected = userAnswers[question.id] === option.letter;
            const optionHtml = `
                <div class="option-item">
                    <label class="option-label ${isSelected ? 'selected' : ''}" data-question-id="${question.id}" data-option="${option.letter}">
                        <span class="option-letter">${option.letter}</span>
                        <span class="option-text">${option.text}</span>
                    </label>
                </div>
            `;
            $('#optionsContainer').append(optionHtml);
        });
        
        // Update mark button
        const isMarked = markedQuestions.has(question.id);
        $('#markForReviewBtn').html(`
            <i class="${isMarked ? 'ri-flag-fill' : 'ri-flag-line'} me-1"></i>
            ${isMarked ? 'Unmark' : 'Mark for Review'}
        `);
        $('#markForReviewBtn').toggleClass('btn-warning', isMarked).toggleClass('btn-outline-warning', !isMarked);
        
        // Update question navigation buttons
        updateQuestionButtons();
        
        // Update modal counts
        updateModalSummary();
    }

    function generateQuestionButtons() {
        $('#questionButtons').empty();
        
        for (let i = 0; i < examData.totalQuestions; i++) {
            const question = examData.questions[i] || { id: i + 1 };
            const isAnswered = userAnswers[question.id] !== undefined;
            const isMarked = markedQuestions.has(question.id);
            const isCurrent = i === currentQuestionIndex;
            
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
            loadQuestion(index);
        });
    }

    function updateQuestionButtons() {
        $('.question-btn').removeClass('current');
        $(`.question-btn[data-question-index="${currentQuestionIndex}"]`).addClass('current');
    }

    function updateSummary() {
        const answeredCount = Object.keys(userAnswers).length;
        const markedCount = markedQuestions.size;
        
        $('#answeredCount').text(answeredCount);
        $('#notAnsweredCount').text(examData.totalQuestions - answeredCount);
        $('#markedCount').text(markedCount);
        
        $('#modalAnsweredCount').text(answeredCount);
        $('#modalMarkedCount').text(markedCount);
    }

    function updateModalSummary() {
        const answeredCount = Object.keys(userAnswers).length;
        const markedCount = markedQuestions.size;
        
        $('#modalAnsweredCount').text(answeredCount);
        $('#modalMarkedCount').text(markedCount);
    }

    // Event Handlers
    $(document).on('click', '.option-label', function() {
        const questionId = $(this).data('question-id');
        const option = $(this).data('option');
        
        // Remove selection from other options
        $(`.option-label[data-question-id="${questionId}"]`).removeClass('selected');
        
        // Add selection to clicked option
        $(this).addClass('selected');
        
        // Save answer
        userAnswers[questionId] = option;
        
        // Update UI
        generateQuestionButtons();
        updateSummary();
    });

    $('#markForReviewBtn').click(function() {
        const currentQuestion = examData.questions[currentQuestionIndex];
        
        if (markedQuestions.has(currentQuestion.id)) {
            markedQuestions.delete(currentQuestion.id);
        } else {
            markedQuestions.add(currentQuestion.id);
        }
        
        loadQuestion(currentQuestionIndex);
        generateQuestionButtons();
        updateSummary();
    });

    $('#prevQuestionBtn').click(function() {
        if (currentQuestionIndex > 0) {
            loadQuestion(currentQuestionIndex - 1);
        }
    });

    $('#nextQuestionBtn').click(function() {
        if (currentQuestionIndex < examData.questions.length - 1) {
            loadQuestion(currentQuestionIndex + 1);
        }
    });

    $('#clearSelectionBtn').click(function() {
        const currentQuestion = examData.questions[currentQuestionIndex];
        delete userAnswers[currentQuestion.id];
        
        $(`.option-label[data-question-id="${currentQuestion.id}"]`).removeClass('selected');
        generateQuestionButtons();
        updateSummary();
    });

    $('#submitExamBtn').click(function() {
        $('#submitExamModal').modal('show');
    });

    $('#confirmSubmitBtn').click(function() {
        submitExam();
    });

    function submitExam() {
        // Calculate score
        let score = 0;
        examData.questions.forEach(question => {
            if (userAnswers[question.id] === question.correctAnswer) {
                score++;
            }
        });
        
        // Show results
        alert(`Exam submitted!\n\nScore: ${score}/${examData.questions.length}\nPercentage: ${((score / examData.questions.length) * 100).toFixed(1)}%`);
        
        // In real app, redirect to results page
        // window.location.href = '/results';
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
    }
</script>
@endsection