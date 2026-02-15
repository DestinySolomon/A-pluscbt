<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @php
        $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
    @endphp
    
    <title>Exam: {{ $exam->name ?? 'JAMB CBT' }} - {{ $siteName }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Custom Exam CSS -->
    <link href="{{ asset('assets/css/exam.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="exam-body">
    <!-- Exam Header with Timer -->
    <header class="exam-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="exam-brand">
                        <i class="ri-graduation-cap-fill me-2"></i>
                        <strong>{{ $siteName }}</strong>
                        <small class="ms-2">JAMB CBT</small>
                    </div>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="exam-info">
                        <h5 class="mb-0" id="examTitle">{{ $exam->name ?? 'Exam' }}</h5>
                        <small id="subjectInfo">{{ $currentSubject ?? 'General' }}</small>
                    </div>
                </div>
                
                <div class="col-md-4 text-end">
                    <div class="exam-timer-container">
                        <div class="exam-timer" id="examTimer">
                            <i class="ri-time-line me-2"></i>
                            <span id="timerHours">00</span>:<span id="timerMinutes">00</span>:<span id="timerSeconds">00</span>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm ms-3" id="submitExamBtn">
                            <i class="ri-stop-circle-line me-1"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Exam Container -->
    <main class="exam-container">
        <div class="container-fluid">
            <div class="row">
                <!-- Question Navigation Sidebar -->
                <aside class="col-lg-3 col-md-4 exam-sidebar">
                    <div class="exam-sidebar-content">
                        <!-- Question Navigation -->
                        <div class="question-navigation-card">
                            <h6 class="card-title mb-3">
                                <i class="ri-questionnaire-line me-2"></i> Question Navigation
                            </h6>
                            <div class="question-buttons" id="questionButtons">
                                <!-- Question buttons will be generated here -->
                            </div>
                            
                            <div class="navigation-legend mt-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="legend-item">
                                            <span class="legend-color answered"></span>
                                            <small>Answered</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="legend-item">
                                            <span class="legend-color current"></span>
                                            <small>Current</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="legend-item">
                                            <span class="legend-color marked"></span>
                                            <small>Marked</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="legend-item">
                                            <span class="legend-color not-answered"></span>
                                            <small>Not Answered</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Exam Summary -->
                        <div class="exam-summary-card mt-4">
                            <h6 class="card-title mb-3">
                                <i class="ri-information-line me-2"></i> Exam Summary
                            </h6>
                            <ul class="exam-summary-list">
                                <li>
                                    <span class="summary-label">Total Questions:</span>
                                    <span class="summary-value" id="totalQuestions">0</span>
                                </li>
                                <li>
                                    <span class="summary-label">Answered:</span>
                                    <span class="summary-value text-success" id="answeredCount">0</span>
                                </li>
                                <li>
                                    <span class="summary-label">Not Answered:</span>
                                    <span class="summary-value text-danger" id="notAnsweredCount">0</span>
                                </li>
                                <li>
                                    <span class="summary-label">Marked for Review:</span>
                                    <span class="summary-value text-warning" id="markedCount">0</span>
                                </li>
                                <li>
                                    <span class="summary-label">Time Remaining:</span>
                                    <span class="summary-value text-primary" id="summaryTimer">00:00:00</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
                
                <!-- Main Question Area -->
                <section class="col-lg-9 col-md-8 exam-main">
                    <div class="exam-main-content">
                        <!-- Question Header -->
                        <div class="question-header mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 id="currentQuestionNumber">Question 1</h4>
                                    <small class="text-muted" id="questionSubject">Subject</small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-warning btn-sm" id="markForReviewBtn">
                                        <i class="ri-flag-line me-1"></i> Mark for Review
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Question Content -->
                        <div class="question-content-card">
                            <div class="question-text mb-4" id="questionText">
                                <!-- Question text will be loaded here -->
                            </div>
                            
                            <!-- Question Image (if any) -->
                            <div class="question-image mb-4" id="questionImageContainer" style="display: none;">
                                <img src="" alt="Question Image" id="questionImage" class="img-fluid rounded">
                            </div>
                            
                            <!-- Options -->
                            <div class="options-container" id="optionsContainer">
                                <!-- Options will be loaded here -->
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="question-navigation mt-5">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-primary" id="prevQuestionBtn">
                                    <i class="ri-arrow-left-line me-1"></i> Previous
                                </button>
                                
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2" id="clearSelectionBtn">
                                        <i class="ri-close-circle-line me-1"></i> Clear
                                    </button>
                                    <button type="button" class="btn btn-primary" id="nextQuestionBtn">
                                        Next <i class="ri-arrow-right-line ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Submit Modal -->
    <div class="modal fade" id="submitExamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-danger">
                    <h5 class="modal-title text-danger">
                        <i class="ri-alarm-warning-line me-2"></i> Submit Exam
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="ri-question-line display-4 text-warning"></i>
                    </div>
                    <h5 class="text-center mb-3">Are you sure you want to submit?</h5>
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <strong>Note:</strong> Once submitted, you cannot change your answers.
                    </div>
                    <div class="submit-summary">
                        <p><strong>Questions Answered:</strong> <span id="modalAnsweredCount">0</span> of <span id="modalTotalQuestions">0</span></p>
                        <p><strong>Marked for Review:</strong> <span id="modalMarkedCount">0</span> questions</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmSubmitBtn">
                        <i class="ri-check-line me-1"></i> Yes, Submit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom Exam JS -->
    <script src="{{ asset('assets/js/exam.js') }}"></script>
    
    @stack('scripts')
    
    <script>
        // Prevent leaving exam page
        window.onbeforeunload = function() {
            return "Are you sure you want to leave? Your exam progress will be lost.";
        };
    </script>
</body>
</html>