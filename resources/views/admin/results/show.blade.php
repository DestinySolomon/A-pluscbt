@extends('layouts.admin')

@section('title', 'Result Details - ' . $result->user->name . ' - ' . $result->exam->name . ' - A-plus CBT')
@section('page-title', 'Result Details')
@section('mobile-title', 'Result Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.results.index') }}">Results</a></li>
<li class="breadcrumb-item active">{{ $result->user->name }} - {{ $result->exam->name }}</li>
@endsection

@section('page-actions')
<div class="page-actions-wrapper">
    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="{{ route('admin.results.index') }}" class="btn-admin btn-admin-secondary mb-2 mb-md-0">
            <i class="ri-arrow-left-line me-2"></i> Back to Results
        </a>
        
        <div class="d-flex flex-column flex-md-row gap-2">
            @if($result->is_passed && !$result->certificate_number)
                <form action="{{ route('admin.results.issue-certificate', $result->id) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-certificate-line me-2"></i> Issue Certificate
                    </button>
                </form>
            @endif
            
            @if($result->certificate_number)
                <a href="{{ route('admin.results.certificate', $result->id) }}" 
                   class="btn-admin btn-admin-success" 
                   target="_blank">
                    <i class="ri-certificate-2-line me-2"></i> View Certificate
                </a>
            @endif
            
            <a href="{{ route('admin.users.show', $result->user_id) }}" class="btn-admin btn-admin-secondary">
                <i class="ri-user-line me-2"></i> View Student
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Result Overview -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Result Overview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="result-summary">
                            <div class="d-flex align-items-center mb-4">
                                <div class="score-circle me-4">
                                    <div class="circle-progress" 
                                         data-percentage="{{ $result->percentage }}"
                                         data-color="{{ $result->is_passed ? '#14b8a6' : '#dc3545' }}">
                                        <div class="circle-progress-inner">
                                            <span class="percentage">{{ number_format($result->percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $result->user->name }}</h4>
                                    <p class="text-muted mb-2">{{ $result->exam->name }}</p>
                                    <div class="d-flex gap-2">
                                        @php
                                            $gradeColors = [
                                                'A' => 'bg-success',
                                                'B' => 'bg-info',
                                                'C' => 'bg-primary',
                                                'D' => 'bg-warning',
                                                'F' => 'bg-danger',
                                            ];
                                        @endphp
                                        <span class="badge {{ $gradeColors[$result->grade] ?? 'bg-secondary' }}">
                                            Grade: {{ $result->grade }}
                                        </span>
                                        @if($result->is_passed)
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <span class="stat-label">Correct Answers</span>
                                        <span class="stat-value text-success">{{ $result->correct_answers }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <span class="stat-label">Wrong Answers</span>
                                        <span class="stat-value text-danger">{{ $result->wrong_answers }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <span class="stat-label">Questions Answered</span>
                                        <span class="stat-value">{{ $result->questions_answered }}/{{ $result->total_questions }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <span class="stat-label">Score</span>
                                        <span class="stat-value">{{ $result->score }} marks</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="performance-details">
                            <h6 class="mb-3">Performance Details</h6>
                            <div class="performance-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Time Spent</span>
                                    <span class="stat-value">{{ $result->time_spent_minutes }} minutes</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Avg. Time per Question</span>
                                    <span class="stat-value">{{ number_format($result->average_time_per_question, 1) }} seconds</span>
                                </div>
                                @if($result->rank && $result->total_participants)
                                <div class="stat-item">
                                    <span class="stat-label">Rank</span>
                                    <span class="stat-value">{{ $result->rank_formatted }} of {{ $result->total_participants }}</span>
                                </div>
                                @endif
                                <div class="stat-item">
                                    <span class="stat-label">Exam Date</span>
                                    <span class="stat-value">{{ $result->exam_date->format('M d, Y h:i A') }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Exam Average</span>
                                    <span class="stat-value {{ $result->percentage > $examAverage ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($examAverage, 1) }}%
                                    </span>
                                </div>
                                @if($result->certificate_number)
                                <div class="stat-item">
                                    <span class="stat-label">Certificate Number</span>
                                    <span class="stat-value">{{ $result->certificate_number }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Certificate Issued</span>
                                    <span class="stat-value">{{ $result->certificate_issued_at->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Subject Performance Chart -->
        @if(!empty($subjectLabels))
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Subject Performance Breakdown</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="subjectPerformanceChart"></canvas>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Difficulty Breakdown -->
        @if(!empty($difficultyLabels))
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Difficulty Level Performance</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container" style="position: relative; height: 250px;">
                            <canvas id="difficultyChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="difficulty-stats">
                            @foreach(['easy', 'medium', 'hard'] as $index => $difficulty)
                                @php
                                    $correct = $difficultyCorrect[$index] ?? 0;
                                    $total = $difficultyTotal[$index] ?? 0;
                                    $percentage = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
                                    $colors = [
                                        'easy' => '#14b8a6',
                                        'medium' => '#0d9488',
                                        'hard' => '#0f766e'
                                    ];
                                @endphp
                                <div class="difficulty-item mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-capitalize fw-medium">{{ $difficulty }}</span>
                                        <span>{{ $percentage }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%; background-color: {{ $colors[$difficulty] }};">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $correct }}/{{ $total }} correct</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Sidebar - Detailed Breakdown -->
    <div class="col-lg-4">
        <!-- Subject Breakdown Table -->
        @if(!empty($subjectBreakdown))
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Subject Scores</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th class="text-end">Score</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectBreakdown as $subjectCode => $data)
                            @php
                                $percentage = $data['percentage'] ?? 0;
                                $colorClass = $percentage >= 70 ? 'text-success' : ($percentage >= 50 ? 'text-warning' : 'text-danger');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="subject-color-dot me-2" 
                                             style="background-color: {{ $subjectColors[$loop->index] ?? '#14b8a6' }};"></div>
                                        <span>{{ $subjectCode }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    {{ $data['correct'] ?? 0 }}/{{ $data['total'] ?? 0 }}
                                </td>
                                <td class="text-end {{ $colorClass }}">
                                    {{ number_format($percentage, 1) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Topic Breakdown (if available) -->
        @if(!empty($result->topic_breakdown))
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Topic Performance</h6>
            </div>
            <div class="card-body">
                <div class="topic-breakdown">
                    @foreach($result->topic_breakdown as $topicId => $data)
                    @php
                        $percentage = $data['percentage'] ?? 0;
                        $colorClass = $percentage >= 70 ? 'text-success' : ($percentage >= 50 ? 'text-warning' : 'text-danger');
                    @endphp
                    <div class="topic-item mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-truncate" style="max-width: 150px;" title="{{ $data['name'] ?? 'Unknown' }}">
                                {{ $data['name'] ?? 'Unknown' }}
                            </span>
                            <span class="{{ $colorClass }}">{{ number_format($percentage, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%; background-color: {{ $loop->index % 2 == 0 ? '#14b8a6' : '#0d9488' }};">
                            </div>
                        </div>
                        <small class="text-muted">{{ $data['correct'] ?? 0 }}/{{ $data['total'] ?? 0 }} correct</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <!-- Student Notes -->
        @if($result->student_notes)
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Student Notes</h6>
            </div>
            <div class="card-body">
                <div class="student-notes">
                    {{ $result->student_notes }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.score-circle {
    position: relative;
    width: 120px;
    height: 120px;
}

.circle-progress {
    position: relative;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(var(--circle-color, #14b8a6) calc(var(--percentage, 0) * 3.6deg), #e5e7eb 0deg);
    display: flex;
    align-items: center;
    justify-content: center;
}

.circle-progress::before {
    content: '';
    position: absolute;
    width: 100px;
    height: 100px;
    background: white;
    border-radius: 50%;
    top: 10px;
    left: 10px;
}

.circle-progress-inner {
    position: relative;
    z-index: 1;
    text-align: center;
}

.circle-progress-inner .percentage {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

.stat-item {
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    display: block;
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 4px;
}

.stat-value {
    display: block;
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
}

.subject-color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.student-notes {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
    font-size: 14px;
    line-height: 1.6;
    white-space: pre-wrap;
}

@media (max-width: 768px) {
    .score-circle {
        width: 100px;
        height: 100px;
        margin-right: 1rem !important;
    }
    
    .circle-progress {
        width: 100px;
        height: 100px;
    }
    
    .circle-progress::before {
        width: 80px;
        height: 80px;
        top: 10px;
        left: 10px;
    }
    
    .circle-progress-inner .percentage {
        font-size: 20px;
    }
}

@media (max-width: 576px) {
    .page-actions-wrapper .d-flex {
        flex-direction: column !important;
    }
    
    .page-actions-wrapper .btn-admin {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up circle progress
    const circleProgress = document.querySelector('.circle-progress');
    if (circleProgress) {
        const percentage = circleProgress.getAttribute('data-percentage');
        const color = circleProgress.getAttribute('data-color');
        circleProgress.style.setProperty('--percentage', percentage);
        circleProgress.style.setProperty('--circle-color', color);
    }
    
    // Subject Performance Chart
    @if(!empty($subjectLabels))
    const subjectCtx = document.getElementById('subjectPerformanceChart').getContext('2d');
    new Chart(subjectCtx, {
        type: 'bar',
        data: {
            labels: @json($subjectLabels),
            datasets: [{
                label: 'Score (%)',
                data: @json($subjectPercentages),
                backgroundColor: @json($subjectColors),
                borderColor: @json($subjectColors),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @endif
    
    // Difficulty Chart
    @if(!empty($difficultyLabels))
    const difficultyCtx = document.getElementById('difficultyChart').getContext('2d');
    new Chart(difficultyCtx, {
        type: 'doughnut',
        data: {
            labels: @json($difficultyLabels),
            datasets: [{
                data: @json($difficultyCorrect),
                backgroundColor: [
                    'rgba(20, 184, 166, 0.8)',   // Easy - teal
                    'rgba(13, 148, 136, 0.8)',   // Medium - darker teal
                    'rgba(15, 118, 110, 0.8)'    // Hard - darkest teal
                ],
                borderColor: [
                    'rgb(20, 184, 166)',
                    'rgb(13, 148, 136)',
                    'rgb(15, 118, 110)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = @json(array_sum($difficultyCorrect));
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} questions (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush