@extends('layouts.user-dashboard')

@section('title', 'Exam Instructions - ' . $exam->name)
@section('page-title', 'Exam Instructions')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('user.exams.index') }}">Exams</a></li>
    <li class="breadcrumb-item active">Instructions</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="ri-information-line me-2"></i> {{ $exam->name }} - Instructions
                </h5>
            </div>
            <div class="card-body">
                <!-- Exam Overview -->
                <div class="exam-overview mb-5">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle p-3 me-3">
                                    <i class="ri-time-line text-white"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Duration</small>
                                    <strong>{{ $exam->duration_minutes }} minutes</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle p-3 me-3">
                                    <i class="ri-question-line text-white"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Total Questions</small>
                                    <strong>{{ $exam->total_questions }} questions</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle p-3 me-3">
                                    <i class="ri-pass-valid-line text-white"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Passing Score</small>
                                    <strong>{{ $exam->passing_score }}%</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-info rounded-circle p-3 me-3">
                                    <i class="ri-repeat-line text-white"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Attempts Allowed</small>
                                    <strong>
                                        @if($exam->max_attempts == 0)
                                            Unlimited
                                        @else
                                            {{ $exam->max_attempts }} attempt(s)
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="instructions mb-5">
                    <h6 class="mb-3 text-primary">
                        <i class="ri-guide-line me-2"></i> Important Instructions
                    </h6>
                    
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <strong>Note:</strong> This is a JAMB-standard Computer Based Test (CBT)
                    </div>
                    
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item border-0 ps-0">
                            The exam duration is <strong>{{ $exam->duration_minutes }} minutes</strong>. Timer will start immediately when you begin.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            Total of <strong>{{ $exam->total_questions }} questions</strong> to be answered.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            Each question carries equal marks unless specified otherwise.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            You can navigate between questions using the Previous/Next buttons or the question navigation panel.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            Use the "Mark for Review" button to flag questions you want to review later.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            Your answers are saved automatically as you progress through the exam.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            You can change your answer anytime before submitting the exam.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            The exam will auto-submit when time expires.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            Once submitted, you cannot re-take the exam unless allowed by attempt limits.
                        </li>
                        <li class="list-group-item border-0 ps-0">
                            Do not refresh the page or close the browser during the exam.
                        </li>
                    </ol>
                </div>

                <!-- Subjects Covered -->
                @if($exam->subjects && $exam->subjects->count() > 0)
                <div class="subjects-covered mb-5">
                    <h6 class="mb-3 text-primary">
                        <i class="ri-book-line me-2"></i> Subjects Covered
                    </h6>
                    
                    <div class="row">
                        @foreach($exam->subjects as $examSubject)
                            @if($examSubject->subject)
                            <div class="col-md-4 mb-2">
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="ri-checkbox-circle-fill text-success me-2"></i>
                                    <span>{{ $examSubject->subject->name }}</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="action-buttons mt-5">
                    <div class="d-grid gap-3">
                        <form action="{{ route('user.exams.start', $exam->id) }}" method="POST" id="startExamForm">
                            @csrf
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    I have read and understood all the instructions. I agree to abide by the exam rules and conditions.
                                </label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="startExamBtn">
                                    <i class="ri-play-circle-line me-2"></i> Start Exam Now
                                </button>
                                
                                <a href="{{ route('user.exams.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> Back to Exams
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startExamBtn = document.getElementById('startExamBtn');
    const agreeTermsCheckbox = document.getElementById('agreeTerms');
    const startExamForm = document.getElementById('startExamForm');
    
    // Initially disable the start button
    startExamBtn.disabled = true;
    
    // Enable/disable button based on checkbox
    agreeTermsCheckbox.addEventListener('change', function() {
        startExamBtn.disabled = !this.checked;
    });
    
    // Form submission
    startExamForm.addEventListener('submit', function(e) {
        if (!agreeTermsCheckbox.checked) {
            e.preventDefault();
            alert('Please agree to the terms and conditions to start the exam.');
            return false;
        }
        
        // Show loading state
        startExamBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Starting Exam...
        `;
        startExamBtn.disabled = true;
        
        return true;
    });
    
    // Confirm before starting
    startExamBtn.addEventListener('click', function(e) {
        if (!agreeTermsCheckbox.checked) {
            e.preventDefault();
            return false;
        }
        
        const confirmation = confirm('Are you ready to start the exam?\n\nTimer will begin immediately. Make sure you are in a quiet environment with stable internet connection.');
        
        if (!confirmation) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .exam-overview .rounded-circle {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
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
    
    .action-buttons .btn-lg {
        padding: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .form-check-input:checked {
        background-color: var(--user-primary);
        border-color: var(--user-primary);
    }
    
    .form-check-label {
        cursor: pointer;
        user-select: none;
    }
</style>
@endpush