@extends('layouts.admin')

@section('title', 'Exam Statistics - ' . $exam->name)
@section('page-title', 'Exam Statistics')
@section('mobile-title', 'Stats: ' . Str::limit($exam->name, 15))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.index') }}">Exams</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.show', $exam->id) }}">{{ Str::limit($exam->name, 15) }}</a>
</li>
<li class="breadcrumb-item active">Statistics</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn-admin btn-admin-secondary">
        <i class="ri-arrow-left-line me-2"></i> Back to Exam
    </a>
    <button type="button" class="btn-admin btn-admin-primary" onclick="exportStatistics()">
        <i class="ri-download-line me-2"></i> Export Statistics
    </button>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Left Column: Key Statistics -->
    <div class="col-lg-8">
        <!-- Overall Statistics -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Overall Performance</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Total Attempts</div>
                            <div class="h3 fw-bold text-primary">{{ $stats['total_attempts'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Completed</div>
                            <div class="h3 fw-bold text-success">{{ $stats['completed_attempts'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">In Progress</div>
                            <div class="h3 fw-bold text-info">{{ $stats['in_progress_attempts'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Pass Rate</div>
                            <div class="h3 fw-bold {{ $stats['pass_rate'] >= 50 ? 'text-success' : 'text-danger' }}">
                                {{ round($stats['pass_rate'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Average Score</div>
                            <div class="h4 fw-bold">{{ round($stats['average_score'], 1) }}%</div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-{{ $stats['average_score'] >= 50 ? 'success' : 'warning' }}" 
                                     style="width: {{ $stats['average_score'] }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Avg Time Spent</div>
                            <div class="h4 fw-bold">{{ round($stats['average_time_spent'] / 60, 1) }} min</div>
                            <small class="text-muted">
                                {{ round(($stats['average_time_spent'] / ($exam->duration_minutes * 60)) * 100, 1) }}% of total time
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Completion Rate</div>
                            <div class="h4 fw-bold">
                                {{ $stats['total_attempts'] > 0 ? round(($stats['completed_attempts'] / $stats['total_attempts']) * 100, 1) : 0 }}%
                            </div>
                            <small class="text-muted">
                                {{ $stats['completed_attempts'] }}/{{ $stats['total_attempts'] }} attempts
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Score Distribution -->
        <div class="admin-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Score Distribution</h5>
                <div class="dropdown">
                    <button class="btn-admin btn-admin-sm btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ri-filter-line me-2"></i> Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="filterScores('all')">All Scores</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterScores('passed')">Passed Only</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterScores('failed')">Failed Only</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Grade Range</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scoreDistribution as $distribution)
                                @php
                                    $percentage = ($distribution->count / $stats['completed_attempts']) * 100;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="grade-indicator" style="background-color: {{ getGradeColor($distribution->grade_range) }}"></div>
                                            {{ $distribution->grade_range }}
                                        </div>
                                    </td>
                                    <td>{{ $distribution->count }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar" 
                                                     style="width: {{ $percentage }}%; background-color: {{ getGradeColor($distribution->grade_range) }}"></div>
                                            </div>
                                            <span>{{ round($percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <canvas id="scoreDistributionChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Performance -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Subject-wise Performance</h5>
            </div>
            <div class="card-body">
                @if(!empty($subjectPerformance))
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Questions</th>
                                <th>Average Score</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectPerformance as $subject)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($loop->first)
                                            <i class="ri-book-line text-primary"></i>
                                        @else
                                            <i class="ri-book-2-line text-info"></i>
                                        @endif
                                        <span>{{ $subject['name'] }}</span>
                                    </div>
                                </td>
                                <td>{{ $subject['question_count'] }}</td>
                                <td>
                                    <div class="fw-medium">{{ round($subject['average_score'], 1) }}%</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $subject['average_score'] >= 50 ? 'success' : ($subject['average_score'] >= 30 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $subject['average_score'] }}%"></div>
                                        </div>
                                        @if($subject['average_score'] >= 70)
                                            <i class="ri-award-line text-success"></i>
                                        @elseif($subject['average_score'] >= 50)
                                            <i class="ri-check-line text-warning"></i>
                                        @else
                                            <i class="ri-error-warning-line text-danger"></i>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="ri-bar-chart-line text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">No subject performance data available yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column: Additional Insights -->
    <div class="col-lg-4">
        <!-- Recent Attempts -->
        <div class="admin-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Attempts</h5>
                <a href="{{ route('admin.attempts.index', ['exam_id' => $exam->id]) }}" class="text-decoration-none small">View All</a>
            </div>
            <div class="card-body">
                @if($recentAttempts->isEmpty())
                <div class="text-center py-2">
                    <p class="text-muted">No recent attempts</p>
                </div>
                @else
                <div class="list-group list-group-flush">
                    @foreach($recentAttempts as $attempt)
                    <div class="list-group-item border-0 px-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-medium">{{ $attempt->user->name ?? 'Unknown User' }}</div>
                                <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                    {{ $attempt->percentage }}%
                                </div>
                                <small class="text-muted">
                                    {{ round($attempt->time_spent / 60, 1) }} min
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Exam Insights -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Exam Insights</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small mb-1">Difficulty Level</label>
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $difficultyLevel = 'Medium';
                            if ($stats['average_score'] >= 70) $difficultyLevel = 'Easy';
                            if ($stats['average_score'] <= 40) $difficultyLevel = 'Hard';
                        @endphp
                        <span class="badge bg-{{ $difficultyLevel == 'Easy' ? 'success' : ($difficultyLevel == 'Medium' ? 'warning' : 'danger') }}">
                            {{ $difficultyLevel }}
                        </span>
                        <small class="text-muted">Based on average score</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Completion Time</label>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px;">
                            <div class="progress-bar bg-info" 
                                 style="width: {{ ($stats['average_time_spent'] / ($exam->duration_minutes * 60)) * 100 }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ round(($stats['average_time_spent'] / ($exam->duration_minutes * 60)) * 100, 1) }}% used
                        </small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Popularity</label>
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $popularity = 'Low';
                            if ($stats['total_attempts'] >= 50) $popularity = 'High';
                            elseif ($stats['total_attempts'] >= 20) $popularity = 'Medium';
                        @endphp
                        <span class="badge bg-{{ $popularity == 'High' ? 'success' : ($popularity == 'Medium' ? 'warning' : 'secondary') }}">
                            {{ $popularity }}
                        </span>
                        <small class="text-muted">{{ $stats['total_attempts'] }} attempts</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted small mb-1">Success Rate</label>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-{{ $stats['pass_rate'] >= 70 ? 'success' : ($stats['pass_rate'] >= 50 ? 'warning' : 'danger') }}">
                            {{ round($stats['pass_rate'], 1) }}%
                        </span>
                        <small class="text-muted">students passed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.exams.preview', $exam->id) }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-eye-line me-2"></i> Preview Exam
                    </a>
                    
                    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-admin btn-admin-primary">
                        <i class="ri-edit-line me-2"></i> Edit Exam
                    </a>
                    
                    <a href="{{ route('admin.exams.export', $exam->id) }}" class="btn-admin btn-admin-info">
                        <i class="ri-download-line me-2"></i> Export Data
                    </a>
                    
                    <button type="button" class="btn-admin btn-admin-warning" onclick="refreshStatistics()">
                        <i class="ri-refresh-line me-2"></i> Refresh Stats
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Export Format</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="exportFormat" id="formatCSV" value="csv" checked>
                        <label class="form-check-label" for="formatCSV">
                            CSV (Excel compatible)
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="exportFormat" id="formatJSON" value="json">
                        <label class="form-check-label" for="formatJSON">
                            JSON (API data)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="formatPDF" value="pdf">
                        <label class="form-check-label" for="formatPDF">
                            PDF Report
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Data Range</label>
                    <select class="form-select" id="dateRange">
                        <option value="all">All Time</option>
                        <option value="last7">Last 7 Days</option>
                        <option value="last30">Last 30 Days</option>
                        <option value="last90">Last 90 Days</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                
                <div class="row g-2 mb-3 d-none" id="customDateRange">
                    <div class="col-6">
                        <label class="form-label">From</label>
                        <input type="date" class="form-control" id="dateFrom">
                    </div>
                    <div class="col-6">
                        <label class="form-label">To</label>
                        <input type="date" class="form-control" id="dateTo">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Include</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeAttempts" checked>
                        <label class="form-check-label" for="includeAttempts">
                            Attempt Details
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeCharts" checked>
                        <label class="form-check-label" for="includeCharts">
                            Charts & Graphs
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeRecommendations" checked>
                        <label class="form-check-label" for="includeRecommendations">
                            Recommendations
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-admin btn-admin-primary" onclick="processExport()">
                    <i class="ri-download-line me-2"></i> Export Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Helper function to get grade colors
function getGradeColor(gradeRange) {
    if (gradeRange.includes('A')) return '#198754'; // Success green
    if (gradeRange.includes('B')) return '#20c997'; // Teal
    if (gradeRange.includes('C')) return '#ffc107'; // Warning yellow
    if (gradeRange.includes('D')) return '#fd7e14'; // Orange
    return '#dc3545'; // Danger red for F
}

// Initialize score distribution chart
function initScoreChart() {
    const ctx = document.getElementById('scoreDistributionChart').getContext('2d');
    
    const labels = [];
    const data = [];
    const colors = [];
    
    @foreach($scoreDistribution as $distribution)
        labels.push('{{ $distribution->grade_range }}');
        data.push({{ $distribution->count }});
        colors.push(getGradeColor('{{ $distribution->grade_range }}'));
    @endforeach
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Export statistics
function exportStatistics() {
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

function processExport() {
    const format = document.querySelector('input[name="exportFormat"]:checked').value;
    const dateRange = document.getElementById('dateRange').value;
    
    let url = '{{ route("admin.exams.export", $exam->id) }}';
    url += `?format=${format}&range=${dateRange}`;
    
    if (dateRange === 'custom') {
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        if (dateFrom && dateTo) {
            url += `&from=${dateFrom}&to=${dateTo}`;
        }
    }
    
    window.location.href = url;
    bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
}

function filterScores(filter) {
    // In a real implementation, this would filter the displayed data
    alert(`Filtering by ${filter}. In a real implementation, this would update the chart and table.`);
}

function refreshStatistics() {
    location.reload();
}

// Show/hide custom date range
document.getElementById('dateRange').addEventListener('change', function() {
    const customRangeDiv = document.getElementById('customDateRange');
    if (this.value === 'custom') {
        customRangeDiv.classList.remove('d-none');
    } else {
        customRangeDiv.classList.add('d-none');
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initScoreChart();
    
    // Set default dates for custom range
    const today = new Date().toISOString().split('T')[0];
    const lastMonth = new Date();
    lastMonth.setMonth(lastMonth.getMonth() - 1);
    const lastMonthStr = lastMonth.toISOString().split('T')[0];
    
    document.getElementById('dateFrom').value = lastMonthStr;
    document.getElementById('dateTo').value = today;
});
</script>
<style>
.grade-indicator {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    display: inline-block;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.progress {
    background-color: #e9ecef;
}

.bg-success { background-color: #198754 !important; }
.bg-warning { background-color: #ffc107 !important; color: #000; }
.bg-danger { background-color: #dc3545 !important; }
.bg-info { background-color: #0dcaf0 !important; color: #000; }
.bg-secondary { background-color: #6c757d !important; }

.text-success { color: #198754 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #0dcaf0 !important; }

.admin-table th {
    font-weight: 600;
    background-color: #f8f9fa;
}
</style>
@endpush

@php
function getGradeColor($gradeRange) {
    if (str_contains($gradeRange, 'A')) return '#198754';
    if (str_contains($gradeRange, 'B')) return '#20c997';
    if (str_contains($gradeRange, 'C')) return '#ffc107';
    if (str_contains($gradeRange, 'D')) return '#fd7e14';
    return '#dc3545';
}
@endphp