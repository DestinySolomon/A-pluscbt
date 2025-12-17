@extends('layouts.admin')

@section('title', 'Admin Dashboard - A-plus CBT')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-12">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="ri-user-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_students'] }}</h3>
                    <p>Total Students</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="ri-book-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_subjects'] }}</h3>
                    <p>Subjects</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="ri-question-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_questions'] }}</h3>
                    <p>Questions</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="ri-file-list-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['total_exams'] }}</h3>
                    <p>Exams Created</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Results -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="card-title">Recent Exam Results</h5>
                <a href="{{ route('admin.results.index') }}" class="btn-admin btn-admin-secondary">
                    View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Exam</th>
                            <th>Score</th>
                            <th>Grade</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentResults as $result)
                        <tr>
                            <td>{{ $result->user->name }}</td>
                            <td>{{ $result->exam->name }}</td>
                            <td>
                                <span class="fw-semibold">{{ $result->percentage_formatted }}</span>
                                <br>
                                <small class="text-muted">{{ $result->correct_answers }}/{{ $result->total_questions }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $result->grade == 'A' ? 'success' : ($result->grade == 'B' ? 'info' : ($result->grade == 'C' ? 'warning' : 'danger')) }}">
                                    {{ $result->grade }}
                                </span>
                            </td>
                            <td>{{ $result->exam_date->format('M d, Y') }}</td>
                            <td>
                                @if($result->is_passed)
                                <span class="badge bg-success">Passed</span>
                                @else
                                <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Top Performers & Quick Stats -->
    <div class="col-lg-4">
        <!-- Top Performers -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="card-title">Top Performers</h5>
            </div>
            <div class="list-group list-group-flush">
                @foreach($topPerformers as $index => $performer)
                <div class="list-group-item d-flex align-items-center">
                    <div class="user-avatar me-3">
                        {{ strtoupper(substr($performer->user->name, 0, 2)) }}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $performer->user->name }}</h6>
                        <small class="text-muted">Avg Score: {{ number_format($performer->avg_score, 2) }}%</small>
                    </div>
                    <div class="badge bg-primary rounded-pill">#{{ $index + 1 }}</div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="admin-card">
            <div class="card-header">
                <h5 class="card-title">Quick Stats</h5>
            </div>
            <div class="p-3">
                <div class="d-flex justify-content-between mb-3">
                    <span>Exams Taken</span>
                    <span class="fw-semibold">{{ $stats['exams_taken'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Pass Rate</span>
                    <span class="fw-semibold text-success">{{ $stats['pass_rate'] }}%</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>System Health</span>
                    <span class="fw-semibold text-success">
                        <i class="ri-checkbox-circle-fill"></i> Optimal
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection