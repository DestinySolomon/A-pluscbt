@extends('layouts.admin')

@section('title', $user->name . ' - User Details - A-plus CBT')
@section('page-title', 'User Details')
@section('mobile-title', 'User Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
<li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('page-actions')
<div class="page-actions-wrapper">
    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn-admin btn-admin-secondary mb-2 mb-md-0">
            <i class="ri-arrow-left-line me-2"></i> Back to Users
        </a>
        
        <div class="d-flex flex-column flex-md-row gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-admin btn-admin-primary">
                <i class="ri-edit-line me-2"></i> Edit User
            </a>
            
            <a href="{{ route('admin.results.index') }}?user={{ $user->id }}" class="btn-admin btn-admin-secondary">
                <i class="ri-bar-chart-line me-2"></i> View Results
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <!-- User Profile Card -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-body text-center">
                <div class="user-avatar-lg mb-3 mx-auto">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    @if($user->isAdmin())
                        <span class="badge bg-primary">Admin</span>
                    @else
                        <span class="badge bg-secondary">Student</span>
                    @endif
                    
                    @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
                
                <div class="user-meta text-start">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Member Since:</span>
                        <span class="fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Last Active:</span>
                        <span class="fw-medium">
                            @if($stats['last_active'])
                                {{ $stats['last_active']->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Email Verified:</span>
                        <span class="fw-medium">
                            @if($user->email_verified_at)
                                <i class="ri-checkbox-circle-fill text-success"></i> {{ $user->email_verified_at->format('M d, Y') }}
                            @else
                                <i class="ri-close-circle-fill text-danger"></i> Not Verified
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($user->is_active)
                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-admin btn-admin-warning w-100">
                                <i class="ri-user-unfollow-line me-2"></i> Deactivate User
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-admin btn-admin-success w-100">
                                <i class="ri-user-follow-line me-2"></i> Activate User
                            </button>
                        </form>
                    @endif
                    
                    @if($user->isAdmin())
                        @if(auth()->id() !== $user->id && User::where('role', 'admin')->count() > 1)
                            <form action="{{ route('admin.users.remove-admin', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-admin btn-admin-warning w-100">
                                    <i class="ri-shield-user-line me-2"></i> Remove Admin Role
                                </button>
                            </form>
                        @endif
                    @else
                        <form action="{{ route('admin.users.make-admin', $user->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-admin btn-admin-primary w-100">
                                <i class="ri-shield-star-line me-2"></i> Make Admin
                            </button>
                        </form>
                    @endif
                    
                    @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user->id) }}" 
                              method="POST" 
                              onsubmit="return confirmDelete('{{ addslashes($user->name) }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-admin btn-admin-danger w-100">
                                <i class="ri-delete-bin-line me-2"></i> Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Stats & Activity -->
    <div class="col-lg-8">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="ri-file-list-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['total_attempts'] }}</h3>
                        <p>Total Attempts</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['completed_attempts'] }}</h3>
                        <p>Completed</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="ri-award-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $stats['passed_results'] }}</h3>
                        <p>Passed Exams</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="ri-line-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ number_format($stats['average_score'], 1) }}%</h3>
                        <p>Avg. Score</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Exam Attempts -->
        <div class="admin-card">
            <div class="card-header">
                <h6 class="mb-0">Recent Exam Attempts</h6>
            </div>
            <div class="card-body">
                @if($user->examAttempts->isEmpty())
                    <div class="text-center py-4">
                        <i class="ri-file-list-line text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">No exam attempts yet</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Exam</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Time Spent</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->examAttempts as $attempt)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.exams.show', $attempt->exam_id) }}" class="text-decoration-none">
                                            {{ $attempt->exam->name ?? 'Unknown Exam' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($attempt->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($attempt->status == 'in_progress')
                                            <span class="badge bg-warning">In Progress</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $attempt->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->result)
                                            <span class="fw-medium">{{ number_format($attempt->result->percentage, 1) }}%</span>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->time_spent)
                                            {{ gmdate('H:i:s', $attempt->time_spent) }}
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $attempt->updated_at->format('M d, Y H:i') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.attempts.index') }}?user={{ $user->id }}" class="btn-admin btn-admin-secondary btn-sm">
                            View All Attempts
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Recent Results -->
        <div class="admin-card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Recent Results</h6>
            </div>
            <div class="card-body">
                @if($user->results->isEmpty())
                    <div class="text-center py-4">
                        <i class="ri-award-line text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2">No results yet</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table admin-table">
                            <thead>
                                <tr>
                                    <th>Exam</th>
                                    <th>Score</th>
                                    <th>Correct</th>
                                    <th>Time</th>
                                    <th>Passed</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->results as $result)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.exams.show', $result->exam_id) }}" class="text-decoration-none">
                                            {{ $result->exam->name ?? 'Unknown Exam' }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ number_format($result->percentage, 1) }}%</span>
                                    </td>
                                    <td>
                                        <small>{{ $result->correct_answers }}/{{ $result->total_questions }}</small>
                                    </td>
                                    <td>
                                        <small>{{ gmdate('H:i:s', $result->time_spent) }}</small>
                                    </td>
                                    <td>
                                        @if($result->is_passed)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $result->created_at->format('M d, Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.results.index') }}?user={{ $user->id }}" class="btn-admin btn-admin-secondary btn-sm">
                            View All Results
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.user-avatar-lg {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 36px;
}

.user-meta {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1.5rem;
}

/* Mobile responsive page actions */
.page-actions-wrapper {
    width: 100%;
}

@media (max-width: 768px) {
    .user-avatar-lg {
        width: 80px;
        height: 80px;
        font-size: 28px;
    }
    
    .page-actions-wrapper .d-flex {
        flex-direction: column !important;
    }
    
    .page-actions-wrapper .d-flex .mb-2 {
        margin-bottom: 0.5rem !important;
    }
    
    .page-actions-wrapper .d-flex > div {
        width: 100%;
    }
    
    .page-actions-wrapper .btn-admin {
        width: 100%;
        justify-content: center;
    }
}

/* Desktop layout */
@media (min-width: 768px) {
    .page-actions-wrapper .d-flex {
        align-items: center;
    }
    
    .page-actions-wrapper .d-flex > a:first-child {
        margin-right: auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(userName) {
    return confirm(`Are you sure you want to delete "${userName}"? All their exam attempts and results will be deleted. This action cannot be undone.`);
}
</script>
@endpush