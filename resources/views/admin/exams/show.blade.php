@extends('layouts.admin')

@section('title', $exam->name . ' - A-plus CBT')
@section('page-title', 'Exam Details')
@section('mobile-title', $exam->name)

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('admin.exams.index') }}">Exams</a>
</li>
<li class="breadcrumb-item active">{{ Str::limit($exam->name, 20) }}</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    @if(!$exam->is_published)
        <form action="{{ route('admin.exams.publish', $exam->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-admin btn-admin-success">
                <i class="ri-check-line me-2"></i> Publish Exam
            </button>
        </form>
    @else
        <form action="{{ route('admin.exams.unpublish', $exam->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-admin btn-admin-warning">
                <i class="ri-eye-off-line me-2"></i> Unpublish
            </button>
        </form>
    @endif
    
    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-admin btn-admin-primary">
        <i class="ri-edit-line me-2"></i> Edit Exam
    </a>
    
    <a href="{{ route('admin.exams.preview', $exam->id) }}" class="btn-admin btn-admin-info" target="_blank">
        <i class="ri-eye-line me-2"></i> Preview
    </a>
    
    <div class="dropdown">
        <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="ri-more-2-line"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('admin.exams.duplicate', $exam->id) }}" onclick="return confirm('Duplicate this exam?')">
                    <i class="ri-file-copy-line me-2"></i> Duplicate Exam
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.exams.stats', $exam->id) }}">
                    <i class="ri-bar-chart-line me-2"></i> View Statistics
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.exams.export', $exam->id) }}">
                    <i class="ri-download-line me-2"></i> Export Data
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.exams.destroy', $exam->id) }}" 
                      method="POST" 
                      id="deleteForm"
                      onsubmit="return confirm('Are you sure you want to delete this exam? All attempts and results will be lost.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="ri-delete-bin-line me-2"></i> Delete Exam
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Left Column: Exam Details -->
    <div class="col-lg-8">
        <!-- Exam Overview -->
        <div class="admin-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Exam Overview</h5>
                <div>
                    @if($exam->is_published)
                        <span class="badge bg-success">Published</span>
                    @else
                        <span class="badge bg-warning">Draft</span>
                    @endif
                    @if(!$exam->is_active)
                        <span class="badge bg-danger ms-2">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted">Exam Code</label>
                            <div class="fw-medium">
                                <code>{{ $exam->code }}</code>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Description</label>
                            <div>{{ $exam->description ?: 'No description provided' }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Type</label>
                            <div>
                                @if($exam->type == 'full_jamb')
                                    <span class="badge bg-primary">Full JAMB</span>
                                @elseif($exam->type == 'subject_test')
                                    <span class="badge bg-info">Subject Test</span>
                                @elseif($exam->type == 'topic_test')
                                    <span class="badge bg-success">Topic Test</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($exam->type) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="text-muted">Duration</label>
                                    <div class="fw-medium">{{ $exam->duration_minutes }} min</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="text-muted">Questions</label>
                                    <div class="fw-medium">{{ $exam->total_questions }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="text-muted">Passing Score</label>
                                    <div class="fw-medium">{{ $exam->passing_score }}%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="text-muted">Max Attempts</label>
                                    <div class="fw-medium">
                                        {{ $exam->max_attempts == 0 ? 'Unlimited' : $exam->max_attempts }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Created</label>
                            <div>{{ $exam->created_at->format('F d, Y \a\t h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Breakdown -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Subject Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Questions</th>
                                <th>Percentage</th>
                                <th>Available Questions</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectBreakdown as $subject)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($loop->first)
                                            <i class="ri-book-line text-primary"></i>
                                        @else
                                            <i class="ri-book-2-line text-info"></i>
                                        @endif
                                        <span class="fw-medium">{{ $subject['name'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $subject['question_count'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar" 
                                                 role="progressbar" 
                                                 style="width: {{ $subject['percentage'] }}%;"
                                                 aria-valuenow="{{ $subject['percentage'] }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="text-muted">{{ round($subject['percentage'], 1) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $available = $exam->subjects->where('id', $subject['id'])->first()->questions->count() ?? 0;
                                        $status = $available >= $subject['question_count'] ? 'success' : 'danger';
                                    @endphp
                                    <span class="badge bg-{{ $status }}">
                                        {{ $available }} available
                                    </span>
                                </td>
                                <td>
                                    @if($available >= $subject['question_count'])
                                        <span class="badge bg-success">
                                            <i class="ri-check-line me-1"></i> Ready
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="ri-error-warning-line me-1"></i> Insufficient
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-medium">
                                <td>Total</td>
                                <td>{{ $exam->total_questions }}</td>
                                <td>100%</td>
                                <td colspan="2">
                                    @php
                                        $allReady = true;
                                        foreach($subjectBreakdown as $subject) {
                                            $available = $exam->subjects->where('id', $subject['id'])->first()->questions->count() ?? 0;
                                            if($available < $subject['question_count']) {
                                                $allReady = false;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($allReady)
                                        <span class="badge bg-success">All subjects ready</span>
                                    @else
                                        <span class="badge bg-danger">Some subjects insufficient</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Attempts</h5>
                <a href="#" class="text-decoration-none small">View All</a>
            </div>
            <div class="card-body">
                @if($exam->attempts->isEmpty())
                <div class="text-center py-4">
                    <i class="ri-time-line text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">No attempts yet</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exam->attempts as $attempt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-light text-primary rounded-circle">
                                                {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $attempt->user->name }}</div>
                                            <small class="text-muted">{{ $attempt->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $attempt->created_at->format('M d') }}</div>
                                    <small class="text-muted">{{ $attempt->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    @if($attempt->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($attempt->status == 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($attempt->status == 'time_expired')
                                        <span class="badge bg-warning">Time Expired</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-medium">{{ $attempt->percentage }}%</span>
                                        @if($attempt->is_passed)
                                            <i class="ri-checkbox-circle-line text-success"></i>
                                        @else
                                            <i class="ri-close-circle-line text-danger"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ round($attempt->time_spent / 60, 1) }} min
                                </td>
                                <td>
                                    <a href="#" class="btn-admin btn-admin-secondary btn-sm">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Right Column: Stats & Actions -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Total Attempts</div>
                            <div class="h4 fw-bold">{{ $stats['total_attempts'] }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Completed</div>
                            <div class="h4 fw-bold">{{ $stats['completed_attempts'] }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Average Score</div>
                            <div class="h4 fw-bold">{{ round($stats['average_score'], 1) }}%</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 border rounded">
                            <div class="text-muted small mb-1">Pass Rate</div>
                            <div class="h4 fw-bold">{{ round($stats['pass_rate'], 1) }}%</div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="mb-3">
                    <label class="text-muted">Top Score</label>
                    <div class="fw-medium">{{ round($stats['top_score'], 1) }}%</div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Exam Readiness</label>
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $readySubjects = 0;
                            foreach($subjectBreakdown as $subject) {
                                $available = $exam->subjects->where('id', $subject['id'])->first()->questions->count() ?? 0;
                                if($available >= $subject['question_count']) $readySubjects++;
                            }
                            $readinessPercentage = ($readySubjects / count($subjectBreakdown)) * 100;
                        @endphp
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-{{ $readinessPercentage == 100 ? 'success' : 'warning' }}" 
                                 role="progressbar" 
                                 style="width: {{ $readinessPercentage }}%;"
                                 aria-valuenow="{{ $readinessPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <span class="fw-medium">{{ $readySubjects }}/{{ count($subjectBreakdown) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Behavior Settings -->
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Behavior Settings</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Shuffle Questions</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               disabled
                               {{ $exam->shuffle_questions ? 'checked' : '' }}>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Shuffle Options</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               disabled
                               {{ $exam->shuffle_options ? 'checked' : '' }}>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Show Results Immediately</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" 
                               type="checkbox" 
                               disabled
                               {{ $exam->show_results_immediately ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Actions -->
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-admin btn-admin-primary">
                        <i class="ri-edit-line me-2"></i> Edit Exam
                    </a>
                    
                    <a href="{{ route('admin.exams.preview', $exam->id) }}" class="btn-admin btn-admin-secondary" target="_blank">
                        <i class="ri-eye-line me-2"></i> Preview as Student
                    </a>
                    
                    <form action="{{ route('admin.exams.duplicate', $exam->id) }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn-admin btn-admin-info">
                            <i class="ri-file-copy-line me-2"></i> Duplicate Exam
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.exams.stats', $exam->id) }}" class="btn-admin btn-admin-warning">
                        <i class="ri-bar-chart-line me-2"></i> View Statistics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-weight: 600;
    font-size: 14px;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}

.bg-success {
    background-color: #198754 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.bg-info {
    background-color: #0dcaf0 !important;
    color: #000;
}

.bg-primary {
    background-color: var(--primary-color) !important;
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.form-check-input:disabled {
    cursor: not-allowed;
}
</style>
@endpush