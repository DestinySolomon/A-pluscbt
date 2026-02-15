@extends('layouts.user-dashboard')

@section('title', 'Results & Reports')
@section('page-title', 'Results & Reports')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Results</li>
@endsection

@section('content')
    <!-- Performance Summary -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="ri-file-list-line"></i>
                </div>
                <div class="stat-value">{{ $totalExams }}</div>
                <div class="stat-label">Exams Taken</div>
                <p class="stat-desc">Total attempts</p>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <div class="stat-value">{{ $passedExams }}</div>
                <div class="stat-label">Exams Passed</div>
                <p class="stat-desc">Successful attempts</p>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="ri-percent-line"></i>
                </div>
                <div class="stat-value">{{ number_format($averageScore, 1) }}%</div>
                <div class="stat-label">Average Score</div>
                <p class="stat-desc">Overall performance</p>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="ri-time-line"></i>
                </div>
                <div class="stat-value">{{ number_format($totalTimeSpent, 1) }}h</div>
                <div class="stat-label">Time Invested</div>
                <p class="stat-desc">Total practice time</p>
            </div>
        </div>
    </div>

    <!-- Results Table & Subject Performance -->
    <div class="row">
        <!-- Results Table -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="ri-history-line me-2"></i> Recent Results
                    </h5>
                </div>
                <div class="card-body">
                    @if($results->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Date</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $result->exam->name }}</strong>
                                                <small class="d-block text-muted">{{ $result->exam->type }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $result->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $result->score >= ($result->total_questions/2) ? 'success' : 'danger' }}">
                                                {{ $result->score }}/{{ $result->total_questions }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $result->percentage >= 50 ? 'success' : 'danger' }}" 
                                                         style="width: {{ $result->percentage }}%">
                                                    </div>
                                                </div>
                                                <span class="fw-bold">{{ number_format($result->percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($result->is_passed)
                                                <span class="badge bg-success">Passed</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('user.results.show', $result->exam_attempt_id) }}" 
                                                   class="btn btn-outline-primary" title="View Details">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <a href="{{ route('user.results.review', $result->exam_attempt_id) }}" 
                                                   class="btn btn-outline-info" title="Review Answers">
                                                    <i class="ri-question-answer-line"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $results->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-inbox-line display-4 text-muted"></i>
                            <h5 class="text-muted mt-3">No Results Yet</h5>
                            <p class="text-muted mb-4">You haven't completed any exams yet. Start practicing to see your results here.</p>
                            <a href="{{ route('user.exams.index') }}" class="btn btn-primary">
                                <i class="ri-play-line me-1"></i> Start Your First Exam
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subject Performance -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="ri-bar-chart-line me-2"></i> Subject Performance
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($subjectPerformance))
                        <div class="subject-performance">
                            @foreach($subjectPerformance as $subject => $data)
                            <div class="subject-item mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-medium">{{ $subject }}</span>
                                    <span class="text-{{ $data['percentage'] >= 60 ? 'success' : ($data['percentage'] >= 40 ? 'warning' : 'danger') }}">
                                        {{ number_format($data['percentage'], 1) }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $data['percentage'] >= 60 ? 'success' : ($data['percentage'] >= 40 ? 'warning' : 'danger') }}" 
                                         style="width: {{ $data['percentage'] }}%">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">
                                        {{ $data['correct_answers'] }}/{{ $data['total_questions'] }} correct
                                    </small>
                                    <small class="text-muted">
                                        {{ $data['attempts'] }} attempt(s)
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-bar-chart-line display-4 text-muted"></i>
                            <p class="text-muted mt-3">No subject data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Performance Chart -->
    @if($recentResults->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">
                        <i class="ri-line-chart-line me-2"></i> Recent Performance Trend
                    </h5>
                </div>
                <div class="card-body">
                    <div class="performance-chart">
                        <canvas id="performanceChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('styles')
<style>
    .subject-item {
        padding: 0.5rem;
        border-radius: 8px;
        background: #f8f9fa;
        transition: background 0.2s;
    }
    
    .subject-item:hover {
        background: #e9ecef;
    }
    
    .progress {
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($recentResults->count() > 0)
    // Initialize performance chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const chartData = {
        labels: [
            @foreach($recentResults->reverse() as $result)
                "{{ $result->created_at->format('M d') }}",
            @endforeach
        ],
        datasets: [{
            label: 'Scores (%)',
            data: [
                @foreach($recentResults->reverse() as $result)
                    {{ $result->percentage }},
                @endforeach
            ],
            borderColor: 'rgb(20, 184, 166)',
            backgroundColor: 'rgba(20, 184, 166, 0.1)',
            tension: 0.4,
            fill: true
        }]
    };
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Score: ' + context.parsed.y + '%';
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
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush