@extends('layouts.user-dashboard')

@section('title', 'Result: ' . $result->exam->name)
@section('page-title', 'Exam Result')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('user.results.index') }}">Results</a></li>
    <li class="breadcrumb-item active">{{ $result->exam->name }}</li>
@endsection

@section('content')
<div class="row">
    <!-- Result Summary -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-list-line me-2"></i> Result Summary
                    </h5>
                    <span class="badge bg-{{ $result->is_passed ? 'success' : 'danger' }} fs-6">
                        {{ $result->is_passed ? 'PASSED' : 'FAILED' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <!-- Overall Score -->
                <div class="text-center mb-5">
                    <div class="display-2 fw-bold text-{{ $result->percentage >= 50 ? 'success' : 'danger' }}">
                        {{ number_format($result->percentage, 1) }}%
                    </div>
                    <p class="text-muted">Overall Score</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold">{{ $result->score }}/{{ $result->total_questions }}</div>
                                <small class="text-muted">Raw Score</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold">{{ $result->correct_answers }}</div>
                                <small class="text-muted">Correct</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold">{{ $result->wrong_answers }}</div>
                                <small class="text-muted">Wrong</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold">{{ $result->total_questions - $result->questions_answered }}</div>
                                <small class="text-muted">Unanswered</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grade & Time -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <div class="display-4 fw-bold">{{ $result->grade }}</div>
                                <small class="text-muted">Grade</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <div class="display-4 fw-bold">{{ $timeAnalysis['total_time'] }}</div>
                                <small class="text-muted">Time Taken</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject Breakdown -->
                @if(!empty($subjectBreakdown))
                <div class="mb-4">
                    <h6 class="mb-3 text-primary">
                        <i class="ri-book-line me-2"></i> Subject-wise Performance
                    </h6>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Questions</th>
                                    <th>Correct</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjectBreakdown as $subject => $data)
                                <tr>
                                    <td>{{ $subject }}</td>
                                    <td>{{ $data['total'] }}</td>
                                    <td>{{ $data['correct'] }}</td>
                                    <td>{{ $data['score'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $data['percentage'] >= 60 ? 'success' : ($data['percentage'] >= 40 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $data['percentage'] }}%">
                                                </div>
                                            </div>
                                            <span>{{ number_format($data['percentage'], 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Exam Details & Actions -->
    <div class="col-lg-4">
        <!-- Exam Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h6 class="card-title mb-0">
                    <i class="ri-information-line me-2"></i> Exam Details
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="ri-book-line text-primary me-2"></i>
                        <strong>Exam:</strong> {{ $result->exam->name }}
                    </li>
                    <li class="mb-2">
                        <i class="ri-calendar-line text-primary me-2"></i>
                        <strong>Date:</strong> {{ $result->created_at->format('F d, Y h:i A') }}
                    </li>
                    <li class="mb-2">
                        <i class="ri-time-line text-primary me-2"></i>
                        <strong>Duration:</strong> {{ $result->exam->duration_minutes }} minutes
                    </li>
                    <li class="mb-2">
                        <i class="ri-pass-valid-line text-primary me-2"></i>
                        <strong>Passing Score:</strong> {{ $result->exam->passing_score }}%
                    </li>
                    <li class="mb-2">
                        <i class="ri-checkbox-circle-line text-primary me-2"></i>
                        <strong>Status:</strong> 
                        @if($result->is_passed)
                            <span class="badge bg-success">Passed</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h6 class="card-title mb-0">
                    <i class="ri-settings-3-line me-2"></i> Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('user.results.review', $result->exam_attempt_id) }}" 
                       class="btn btn-primary">
                        <i class="ri-question-answer-line me-2"></i> Review Answers
                    </a>
                    
                    <a href="{{ route('user.exams.instructions', $result->exam_id) }}" 
                       class="btn btn-outline-primary">
                        <i class="ri-repeat-line me-2"></i> Retake Exam
                    </a>
                    
                    <a href="{{ route('user.results.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-2"></i> Back to Results
                    </a>
                </div>
            </div>
        </div>

        <!-- Time Analysis -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h6 class="card-title mb-0">
                    <i class="ri-time-line me-2"></i> Time Analysis
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="ri-timer-line text-primary me-2"></i>
                        <strong>Total Time:</strong> {{ $timeAnalysis['total_time'] }}
                    </li>
                    <li class="mb-2">
                        <i class="ri-speed-up-line text-primary me-2"></i>
                        <strong>Avg per Question:</strong> {{ $timeAnalysis['average_per_question'] }}
                    </li>
                    <li class="mb-2">
                        <i class="ri-line-chart-line text-primary me-2"></i>
                        <strong>Time Efficiency:</strong> 
                        <span class="badge bg-{{ $result->percentage >= 80 ? 'success' : ($result->percentage >= 60 ? 'warning' : 'danger') }}">
                            {{ $timeAnalysis['time_efficiency'] }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Question Difficulty Analysis -->
@if(!empty($questionAnalysis) && array_sum([$questionAnalysis['easy_total'], $questionAnalysis['medium_total'], $questionAnalysis['hard_total']]) > 0)
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h6 class="card-title mb-0">
                    <i class="ri-bar-chart-2-line me-2"></i> Question Difficulty Analysis
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="display-6 fw-bold text-success">{{ $questionAnalysis['easy_percentage'] }}%</div>
                            <div class="text-muted">Easy Questions</div>
                            <small>{{ $questionAnalysis['easy_correct'] }}/{{ $questionAnalysis['easy_total'] }} correct</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="display-6 fw-bold text-warning">{{ $questionAnalysis['medium_percentage'] }}%</div>
                            <div class="text-muted">Medium Questions</div>
                            <small>{{ $questionAnalysis['medium_correct'] }}/{{ $questionAnalysis['medium_total'] }} correct</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="display-6 fw-bold text-danger">{{ $questionAnalysis['hard_percentage'] }}%</div>
                            <div class="text-muted">Hard Questions</div>
                            <small>{{ $questionAnalysis['hard_correct'] }}/{{ $questionAnalysis['hard_total'] }} correct</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection