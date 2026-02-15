@extends('layouts.user-dashboard')

@section('title', $exam->name . ' - Details')
@section('page-title', $exam->name)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('user.exams.index') }}">Exams</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="ri-information-line me-2"></i> Exam Details
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-primary mb-3">Description</h6>
                    <p class="mb-0">{{ $exam->description ?? 'No description available.' }}</p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Exam Information</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="ri-time-line text-primary me-2"></i>
                                <strong>Duration:</strong> {{ $exam->duration_minutes }} minutes
                            </li>
                            <li class="mb-2">
                                <i class="ri-question-line text-primary me-2"></i>
                                <strong>Total Questions:</strong> {{ $exam->total_questions }}
                            </li>
                            <li class="mb-2">
                                <i class="ri-pass-valid-line text-primary me-2"></i>
                                <strong>Passing Score:</strong> {{ $exam->passing_score }}%
                            </li>
                            <li class="mb-2">
                                <i class="ri-repeat-line text-primary me-2"></i>
                                <strong>Max Attempts:</strong> 
                                @if($exam->max_attempts == 0)
                                    Unlimited
                                @else
                                    {{ $exam->max_attempts }}
                                @endif
                            </li>
                            <li class="mb-2">
                                <i class="ri-shuffle-line text-primary me-2"></i>
                                <strong>Shuffle Questions:</strong> {{ $exam->shuffle_questions ? 'Yes' : 'No' }}
                            </li>
                            <li class="mb-2">
                                <i class="ri-shuffle-line text-primary me-2"></i>
                                <strong>Shuffle Options:</strong> {{ $exam->shuffle_options ? 'Yes' : 'No' }}
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Your Attempts</h6>
                        @php
                            $userAttempts = auth()->user()->examAttempts()
                                ->where('exam_id', $exam->id)
                                ->whereIn('status', ['completed', 'submitted'])
                                ->get();
                            
                            $bestScore = $userAttempts->max('percentage') ?? 0;
                            $totalAttempts = $userAttempts->count();
                        @endphp
                        
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="ri-trophy-line text-primary me-2"></i>
                                <strong>Best Score:</strong> {{ $bestScore }}%
                            </li>
                            <li class="mb-2">
                                <i class="ri-history-line text-primary me-2"></i>
                                <strong>Total Attempts:</strong> {{ $totalAttempts }}
                            </li>
                            <li class="mb-2">
                                <i class="ri-checkbox-circle-line text-primary me-2"></i>
                                <strong>Status:</strong> 
                                @if($exam->is_published && $exam->is_active)
                                    <span class="badge bg-success">Available</span>
                                @else
                                    <span class="badge bg-danger">Unavailable</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
                
                @if($subjects && $subjects->count() > 0)
                <div class="mb-4">
                    <h6 class="text-primary mb-3">Subjects Covered</h6>
                    <div class="row">
                        @foreach($subjects as $examSubject)
                            @if($examSubject->subject)
                            <div class="col-md-4 mb-2">
                                <div class="border rounded p-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ri-book-line text-primary me-2"></i>
                                        <strong>{{ $examSubject->subject->name }}</strong>
                                    </div>
                                    @if($examSubject->question_count > 0)
                                    <small class="text-muted">{{ $examSubject->question_count }} questions</small>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="ri-play-circle-line me-2"></i> Start Exam
                </h5>
            </div>
            <div class="card-body">
                @if($exam->is_published && $exam->is_active)
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.exams.instructions', $exam->id) }}" class="btn btn-primary btn-lg">
                            <i class="ri-play-line me-2"></i> Start Exam
                        </a>
                        
                        <a href="{{ route('user.exams.index') }}" class="btn btn-outline-primary">
                            <i class="ri-arrow-left-line me-1"></i> Back to Exams
                        </a>
                        
                        @if($totalAttempts > 0)
                        <a href="#" class="btn btn-outline-info">
                            <i class="ri-history-line me-1"></i> View Previous Attempts
                        </a>
                        @endif
                    </div>
                    
                    @if($exam->max_attempts > 0 && $totalAttempts >= $exam->max_attempts)
                    <div class="alert alert-warning mt-3">
                        <i class="ri-alarm-warning-line me-2"></i>
                        You have reached the maximum number of attempts for this exam.
                    </div>
                    @endif
                @else
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i>
                        This exam is currently unavailable.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Tips -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0">
                <h6 class="card-title mb-0">
                    <i class="ri-lightbulb-line me-2"></i> Quick Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-2"></i>
                        Read questions carefully
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-2"></i>
                        Manage your time wisely
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-2"></i>
                        Mark difficult questions for review
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-success me-2"></i>
                        Review all answers before submitting
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection