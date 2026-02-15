@extends('layouts.user-dashboard')

@section('title', 'Exam Instructions')
@section('page-title', 'JAMB CBT Exam Instructions')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Instructions</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="ri-information-line me-2"></i> JAMB CBT Examination Instructions
                </h5>
            </div>
            <div class="card-body">
                <!-- Introduction -->
                <div class="alert alert-primary mb-4">
                    <i class="ri-alert-line me-2"></i>
                    <strong>Important:</strong> These instructions apply to all JAMB Computer Based Tests (CBT). Please read carefully before starting any exam.
                </div>

                <!-- General Instructions -->
                <div class="mb-5">
                    <h6 class="text-primary mb-3">
                        <i class="ri-guide-line me-2"></i> General Instructions
                    </h6>
                    
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>Time Management:</strong> Each exam has a fixed duration. The timer starts immediately when you begin and cannot be paused.
                        </li>
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>Navigation:</strong> Use the question navigation panel on the left to jump to any question. Answered questions are marked in green.
                        </li>
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>Answer Selection:</strong> Click on any option (A, B, C, D, E) to select your answer. You can change your answer anytime before submission.
                        </li>
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>Mark for Review:</strong> Use the "Mark for Review" button to flag questions you want to revisit later.
                        </li>
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>Auto-save:</strong> Your answers are saved automatically as you progress through the exam.
                        </li>
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>Submission:</strong> You can submit your exam anytime using the "Submit" button. The exam will also auto-submit when time expires.
                        </li>
                        <li class="list-group-item border-0 ps-0 mb-2">
                            <strong>No Going Back:</strong> Once submitted, you cannot re-enter the exam or change answers.
                        </li>
                    </ol>
                </div>

                <!-- Exam Rules -->
                <div class="mb-5">
                    <h6 class="text-primary mb-3">
                        <i class="ri-shield-check-line me-2"></i> Examination Rules
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ri-computer-line text-primary me-2"></i>
                                    <strong>Technical Requirements</strong>
                                </div>
                                <ul class="mb-0 ps-3">
                                    <li>Stable internet connection</li>
                                    <li>Updated web browser</li>
                                    <li>JavaScript enabled</li>
                                    <li>Do not refresh during exam</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ri-user-line text-primary me-2"></i>
                                    <strong>Candidate Conduct</strong>
                                </div>
                                <ul class="mb-0 ps-3">
                                    <li>No external assistance</li>
                                    <li>No sharing of answers</li>
                                    <li>Complete exam independently</li>
                                    <li>Respect time limits</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Interface Guide -->
                <div class="mb-5">
                    <h6 class="text-primary mb-3">
                        <i class="ri-layout-3-line me-2"></i> Exam Interface Guide
                    </h6>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center border rounded p-3">
                                <div class="mb-2">
                                    <i class="ri-time-line display-6 text-primary"></i>
                                </div>
                                <h6>Timer</h6>
                                <p class="small mb-0">Shows remaining time in red when less than 5 minutes</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center border rounded p-3">
                                <div class="mb-2">
                                    <i class="ri-questionnaire-line display-6 text-primary"></i>
                                </div>
                                <h6>Question Navigation</h6>
                                <p class="small mb-0">Color-coded buttons show question status</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center border rounded p-3">
                                <div class="mb-2">
                                    <i class="ri-flag-line display-6 text-primary"></i>
                                </div>
                                <h6>Mark for Review</h6>
                                <p class="small mb-0">Flag questions to review before submission</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Legend -->
                <div class="mb-5">
                    <h6 class="text-primary mb-3">
                        <i class="ri-palette-line me-2"></i> Color Legend
                    </h6>
                    
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <span class="question-legend answered me-2"></span>
                                <small>Answered</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <span class="question-legend current me-2"></span>
                                <small>Current</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <span class="question-legend marked me-2"></span>
                                <small>Marked</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <span class="question-legend not-answered me-2"></span>
                                <small>Not Answered</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-5">
                    <div class="d-grid gap-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('user.exams.index') }}" class="btn btn-primary">
                                <i class="ri-play-circle-line me-2"></i> Start Practicing Now
                            </a>
                            
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-2"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        background: transparent;
        padding-left: 0;
        padding-right: 0;
        margin-bottom: 0.5rem;
    }
    
    .list-group-item:before {
        font-weight: 600;
        color: var(--user-primary);
    }
    
    .question-legend {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        display: inline-block;
    }
    
    .question-legend.answered {
        background: var(--user-success);
    }
    
    .question-legend.current {
        background: var(--user-primary);
    }
    
    .question-legend.marked {
        background: var(--user-warning);
    }
    
    .question-legend.not-answered {
        background: #e5e7eb;
        border: 1px solid #9ca3af;
    }
    
    .border.rounded {
        transition: all 0.3s ease;
    }
    
    .border.rounded:hover {
        border-color: var(--user-primary);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.1);
    }
</style>
@endpush