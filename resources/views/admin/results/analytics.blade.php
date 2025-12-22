@extends('layouts.admin')

@section('title', 'Results Analytics - A-plus CBT')
@section('page-title', 'Results Analytics')
@section('mobile-title', 'Analytics')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.results.index') }}">Results</a></li>
<li class="breadcrumb-item active">Analytics</li>
@endsection

@section('page-actions')
<div class="page-actions-wrapper">
    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="{{ route('admin.results.index') }}" class="btn-admin btn-admin-secondary mb-2 mb-md-0">
            <i class="ri-arrow-left-line me-2"></i> Back to Results
        </a>
        
        <a href="{{ route('admin.results.subject-performance') }}" class="btn-admin btn-admin-primary mb-2 mb-md-0">
            <i class="ri-bar-chart-2-line me-2"></i> Subject Performance
        </a>
        
        <a href="{{ route('admin.results.top-performers') }}" class="btn-admin btn-admin-secondary">
            <i class="ri-award-line me-2"></i> Top Performers
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Period Selector -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.results.analytics') }}" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label for="period" class="form-label">Analytics Period</label>
                <select name="period" id="period" class="form-select" onchange="this.form.submit()">
                    <option value="7days" {{ $period == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30days" {{ $period == '30days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90days" {{ $period == '90days' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Last Quarter</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Last Year</option>
                    <option value="all" {{ $period == 'all' ? 'selected' : '' }}>All Time</option>
                </select>
            </div>
            <div class="col-md-9">
                <div class="text-end">
                    <small class="text-muted">Analytics based on {{ $period == 'all' ? 'all available data' : 'selected period' }}</small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="ri-file-list-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalResults) }}</h3>
                <p>Total Results</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="ri-user-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($totalStudents) }}</h3>
                <p>Unique Students</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="ri-line-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($averageScore, 1) }}%</h3>
                <p>Average Score</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($passRate, 1) }}%</h3>
                <p>Pass Rate</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daily Trend Chart -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Daily Results Trend (Last 30 Days)</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grade Distribution -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Grade Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="gradeDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Exam Performance -->
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Top 10 Exams by Average Score</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Attempts</th>
                                <th>Avg. Score</th>
                                <th>Pass Rate</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($examPerformance as $performance)
                            @php
                                $exam = $performance->exam;
                                $passRate = $exam->results()->where('is_passed', true)->count() / max($performance->attempts, 1) * 100;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1">{{ $exam->name ?? 'Unknown' }}</h6>
                                        <small class="text-muted">
                                            <code>{{ $exam->code ?? 'N/A' }}</code>
                                        </small>
                                    </div>
                                </td>
                                <td>{{ $performance->attempts }}</td>
                                <td>
                                    <span class="fw-medium">{{ number_format($performance->avg_score, 1) }}%</span>
                                </td>
                                <td>
                                    <span class="fw-medium {{ $passRate >= 50 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($passRate, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $trend = $performance->avg_score >= 70 ? 'up' : ($performance->avg_score >= 50 ? 'steady' : 'down');
                                        $trendColors = [
                                            'up' => 'text-success',
                                            'steady' => 'text-warning',
                                            'down' => 'text-danger'
                                        ];
                                        $trendIcons = [
                                            'up' => 'ri-arrow-up-line',
                                            'steady' => 'ri-arrow-right-line',
                                            'down' => 'ri-arrow-down-line'
                                        ];
                                    @endphp
                                    <i class="{{ $trendIcons[$trend] }} {{ $trendColors[$trend] }}"></i>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Time Analysis -->
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Performance by Hour of Day</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="timeAnalysisChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Insights Card -->
<div class="admin-card">
    <div class="card-header">
        <h6 class="mb-0"><i class="ri-lightbulb-line me-2"></i> Key Insights</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="insight-item mb-3">
                    <h6 class="mb-2">Performance Peak Hours</h6>
                    @php
                        $bestHour = $timeAnalysis->sortByDesc('avg_score')->first();
                        $worstHour = $timeAnalysis->sortBy('avg_score')->first();
                    @endphp
                    <p class="text-muted mb-1">
                        Best performance: <strong>{{ $bestHour->hour ?? 'N/A' }}:00</strong> ({{ number_format($bestHour->avg_score ?? 0, 1) }}% avg)
                    </p>
                    <p class="text-muted mb-0">
                        Lowest performance: <strong>{{ $worstHour->hour ?? 'N/A' }}:00</strong> ({{ number_format($worstHour->avg_score ?? 0, 1) }}% avg)
                    </p>
                </div>
                
                <div class="insight-item">
                    <h6 class="mb-2">Grade Analysis</h6>
                    @php
                        $topGrade = $gradeDistribution->sortByDesc('count')->first();
                    @endphp
                    <p class="text-muted mb-0">
                        Most common grade: <strong>{{ $topGrade->grade ?? 'N/A' }}</strong> ({{ $topGrade->count ?? 0 }} results)
                    </p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="insight-item mb-3">
                    <h6 class="mb-2">Activity Trends</h6>
                    @php
                        $busiestDay = $dailyTrend->sortByDesc('count')->first();
                        $highestScoreDay = $dailyTrend->sortByDesc('avg_score')->first();
                    @endphp
                    <p class="text-muted mb-1">
                        Busiest day: <strong>{{ $busiestDay->date ?? 'N/A' }}</strong> ({{ $busiestDay->count ?? 0 }} attempts)
                    </p>
                    <p class="text-muted mb-0">
                        Highest scores: <strong>{{ $highestScoreDay->date ?? 'N/A' }}</strong> ({{ number_format($highestScoreDay->avg_score ?? 0, 1) }}% avg)
                    </p>
                </div>
                
                <div class="insight-item">
                    <h6 class="mb-2">Overall Performance</h6>
                    <p class="text-muted mb-0">
                        @if($averageScore >= 70)
                            <span class="text-success">Excellent overall performance!</span> Students are mastering the material.
                        @elseif($averageScore >= 50)
                            <span class="text-warning">Moderate performance.</span> Room for improvement in certain areas.
                        @else
                            <span class="text-danger">Needs attention.</span> Consider reviewing study materials and exam difficulty.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Trend Chart
    const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: @json($dailyTrend->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'))),
            datasets: [
                {
                    label: 'Number of Attempts',
                    data: @json($dailyTrend->pluck('count')),
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Average Score (%)',
                    data: @json($dailyTrend->pluck('avg_score')),
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Attempts'
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Score (%)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    min: 0,
                    max: 100
                }
            }
        }
    });
    
    // Grade Distribution Chart
    const gradeCtx = document.getElementById('gradeDistributionChart').getContext('2d');
    new Chart(gradeCtx, {
        type: 'pie',
        data: {
            labels: @json($gradeDistribution->pluck('grade')),
            datasets: [{
                data: @json($gradeDistribution->pluck('count')),
                backgroundColor: [
                    'rgba(20, 184, 166, 0.8)',   // A
                    'rgba(13, 148, 136, 0.8)',   // B
                    'rgba(15, 118, 110, 0.8)',   // C
                    'rgba(245, 158, 11, 0.8)',   // D
                    'rgba(220, 38, 38, 0.8)'     // F
                ],
                borderColor: [
                    'rgb(20, 184, 166)',
                    'rgb(13, 148, 136)',
                    'rgb(15, 118, 110)',
                    'rgb(245, 158, 11)',
                    'rgb(220, 38, 38)'
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
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} results (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Time Analysis Chart
    const timeCtx = document.getElementById('timeAnalysisChart').getContext('2d');
    new Chart(timeCtx, {
        type: 'bar',
        data: {
            labels: @json($timeAnalysis->pluck('hour')->map(fn($hour) => $hour + ':00')),
            datasets: [{
                label: 'Average Score (%)',
                data: @json($timeAnalysis->pluck('avg_score')),
                backgroundColor: 'rgba(20, 184, 166, 0.7)',
                borderColor: 'rgb(20, 184, 166)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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
});
</script>
@endpush