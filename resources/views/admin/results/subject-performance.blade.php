@extends('layouts.admin')

@section('title', 'Subject Performance Analytics - A-plus CBT')
@section('page-title', 'Subject Performance Analytics')
@section('mobile-title', 'Subject Analytics')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.results.index') }}">Results</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.results.analytics') }}">Analytics</a></li>
<li class="breadcrumb-item active">Subject Performance</li>
@endsection

@section('page-actions')
<div class="page-actions-wrapper">
    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="{{ route('admin.results.index') }}" class="btn-admin btn-admin-secondary mb-2 mb-md-0">
            <i class="ri-arrow-left-line me-2"></i> Back to Results
        </a>
        
        <a href="{{ route('admin.results.analytics') }}" class="btn-admin btn-admin-primary mb-2 mb-md-0">
            <i class="ri-dashboard-line me-2"></i> Analytics Dashboard
        </a>
        
        <a href="{{ route('admin.results.top-performers') }}" class="btn-admin btn-admin-secondary">
            <i class="ri-award-line me-2"></i> Top Performers
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.results.subject-performance') }}" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from" 
                       class="form-control" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-4">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       class="form-control" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.results.subject-performance') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Subject Performance Chart -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Subject Performance Overview</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="subjectPerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Subject Stats -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Performance Summary</h6>
            </div>
            <div class="card-body">
                @php
                    $topSubject = $subjectPerformance[0] ?? null;
                    $bottomSubject = $subjectPerformance[count($subjectPerformance) - 1] ?? null;
                    $totalAttempts = array_sum($subjectAttempts);
                    $averageAllSubjects = array_sum($subjectScores) / max(count($subjectScores), 1);
                @endphp
                
                <div class="subject-summary">
                    <div class="summary-item mb-4">
                        <h6 class="mb-2">Overall Average</h6>
                        <div class="d-flex align-items-center">
                            <div class="score-display me-3">
                                <span class="score-value">{{ number_format($averageAllSubjects, 1) }}%</span>
                            </div>
                            <div>
                                <small class="text-muted">Across all subjects</small>
                                <br>
                                <small>{{ $totalAttempts }} total attempts</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($topSubject)
                    <div class="summary-item mb-4">
                        <h6 class="mb-2">Best Performing</h6>
                        <div class="d-flex align-items-center">
                            <div class="subject-color-dot me-2" style="background-color: #14b8a6;"></div>
                            <div>
                                <strong>{{ $topSubject['subject']->name }}</strong>
                                <br>
                                <small class="text-success">{{ number_format($topSubject['average_score'], 1) }}% average</small>
                                <br>
                                <small class="text-muted">{{ $topSubject['attempt_count'] }} attempts</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($bottomSubject)
                    <div class="summary-item">
                        <h6 class="mb-2">Needs Improvement</h6>
                        <div class="d-flex align-items-center">
                            <div class="subject-color-dot me-2" style="background-color: #dc3545;"></div>
                            <div>
                                <strong>{{ $bottomSubject['subject']->name }}</strong>
                                <br>
                                <small class="text-danger">{{ number_format($bottomSubject['average_score'], 1) }}% average</small>
                                <br>
                                <small class="text-muted">{{ $bottomSubject['attempt_count'] }} attempts</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subject Performance Table -->
<div class="admin-card">
    <div class="card-header">
        <h6 class="mb-0">Detailed Subject Performance</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Code</th>
                        <th>Average Score</th>
                        <th>Attempts</th>
                        <th>Total Questions</th>
                        <th>Correct Answers</th>
                        <th>Success Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjectPerformance as $performance)
                    @php
                        $subject = $performance['subject'];
                        $score = $performance['average_score'];
                        $attempts = $performance['attempt_count'];
                        $totalQuestions = $performance['total_questions'];
                        $correctAnswers = $performance['correct_answers'];
                        $successRate = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions * 100) : 0;
                        
                        $statusColor = $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                        $statusText = $score >= 70 ? 'Excellent' : ($score >= 50 ? 'Good' : 'Needs Attention');
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="subject-color-dot me-2" 
                                     style="background-color: {{ $loop->index < 3 ? '#14b8a6' : ($loop->index < 6 ? '#0d9488' : '#0f766e') }};"></div>
                                <span>{{ $subject->name }}</span>
                            </div>
                        </td>
                        <td>
                            <code>{{ $subject->code }}</code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-medium me-2">{{ number_format($score, 1) }}%</span>
                                @if($score >= 70)
                                    <i class="ri-arrow-up-line text-success"></i>
                                @elseif($score >= 50)
                                    <i class="ri-arrow-right-line text-warning"></i>
                                @else
                                    <i class="ri-arrow-down-line text-danger"></i>
                                @endif
                            </div>
                        </td>
                        <td>{{ $attempts }}</td>
                        <td>{{ $totalQuestions }}</td>
                        <td>{{ $correctAnswers }}</td>
                        <td>
                            <div class="progress" style="height: 6px; width: 100px;">
                                <div class="progress-bar bg-{{ $statusColor }}" 
                                     role="progressbar" 
                                     style="width: {{ $successRate }}%">
                                </div>
                            </div>
                            <small>{{ number_format($successRate, 1) }}%</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Insights Card -->
<div class="admin-card mt-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="ri-lightbulb-line me-2"></i> Subject Performance Insights</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="insight-item mb-3">
                    <h6 class="mb-2">Top 3 Subjects</h6>
                    <ul class="mb-0">
                        @foreach(array_slice($subjectPerformance, 0, 3) as $performance)
                        <li>
                            <strong>{{ $performance['subject']->name }}</strong>: 
                            {{ number_format($performance['average_score'], 1) }}% average
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="insight-item">
                    <h6 class="mb-2">Most Attempted Subjects</h6>
                    @php
                        $mostAttempted = collect($subjectPerformance)->sortByDesc('attempt_count')->take(3);
                    @endphp
                    <ul class="mb-0">
                        @foreach($mostAttempted as $performance)
                        <li>
                            <strong>{{ $performance['subject']->name }}</strong>: 
                            {{ $performance['attempt_count'] }} attempts
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="insight-item mb-3">
                    <h6 class="mb-2">Recommendations</h6>
                    @php
                        $lowPerforming = collect($subjectPerformance)->filter(fn($p) => $p['average_score'] < 50)->take(3);
                    @endphp
                    @if($lowPerforming->count() > 0)
                        <p class="text-muted mb-2">Consider focusing on these subjects:</p>
                        <ul class="mb-0">
                            @foreach($lowPerforming as $performance)
                            <li>
                                <strong>{{ $performance['subject']->name }}</strong> - 
                                Review study materials and practice questions
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">All subjects are performing well! Consider introducing more advanced topics.</p>
                    @endif
                </div>
                
                <div class="insight-item">
                    <h6 class="mb-2">Overall Health</h6>
                    @php
                        $excellentCount = collect($subjectPerformance)->filter(fn($p) => $p['average_score'] >= 70)->count();
                        $goodCount = collect($subjectPerformance)->filter(fn($p) => $p['average_score'] >= 50 && $p['average_score'] < 70)->count();
                        $poorCount = collect($subjectPerformance)->filter(fn($p) => $p['average_score'] < 50)->count();
                    @endphp
                    <p class="text-muted mb-0">
                        <span class="text-success">{{ $excellentCount }} subjects</span> excellent, 
                        <span class="text-warning">{{ $goodCount }} subjects</span> good, 
                        <span class="text-danger">{{ $poorCount }} subjects</span> need attention.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.subject-color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.score-display {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    display: flex;
    align-items: center;
    justify-content: center;
}

.score-value {
    color: white;
    font-size: 18px;
    font-weight: 700;
}

.summary-item {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
}

.insight-item ul {
    padding-left: 1.5rem;
    margin-bottom: 0;
}

.insight-item li {
    margin-bottom: 4px;
}

@media (max-width: 768px) {
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
$(document).ready(function() {
    // Initialize DataTable
    $('.data-table').DataTable({
        responsive: true,
        order: [[2, 'desc']], // Order by Average Score descending
        columnDefs: [
            { responsivePriority: 1, targets: [0] }, // Subject name
            { responsivePriority: 2, targets: [2] }, // Average score
            { responsivePriority: 3, targets: [3] }, // Attempts
            { responsivePriority: 4, targets: [7] }, // Status
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search subjects...",
            lengthMenu: "_MENU_ subjects per page",
            info: "Showing _START_ to _END_ of _TOTAL_ subjects",
            infoEmpty: "Showing 0 to 0 of 0 subjects",
            infoFiltered: "(filtered from _MAX_ total subjects)",
            zeroRecords: "No matching subjects found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        searching: true,
        paging: true,
        info: true
    });
    
    // Prepare chart data in PHP first
    @php
        $backgroundColorArray = [];
        $borderColorArray = [];
        
        foreach(array_keys($subjectNames) as $index) {
            if ($index < 3) {
                $backgroundColorArray[] = 'rgba(20, 184, 166, 0.8)';
                $borderColorArray[] = 'rgb(20, 184, 166)';
            } elseif ($index < 6) {
                $backgroundColorArray[] = 'rgba(13, 148, 136, 0.8)';
                $borderColorArray[] = 'rgb(13, 148, 136)';
            } else {
                $backgroundColorArray[] = 'rgba(15, 118, 110, 0.8)';
                $borderColorArray[] = 'rgb(15, 118, 110)';
            }
        }
    @endphp
    
    // Subject Performance Chart
    const subjectCtx = document.getElementById('subjectPerformanceChart').getContext('2d');
    new Chart(subjectCtx, {
        type: 'bar',
        data: {
            labels: @json($subjectNames),
            datasets: [
                {
                    label: 'Average Score (%)',
                    data: @json($subjectScores),
                    backgroundColor: @json($backgroundColorArray),
                    borderColor: @json($borderColorArray),
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Number of Attempts',
                    data: @json($subjectAttempts),
                    backgroundColor: 'rgba(107, 114, 128, 0.3)',
                    borderColor: 'rgb(107, 114, 128)',
                    borderWidth: 1,
                    type: 'line',
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Average Score (%)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Attempts'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
});
</script>
@endpush