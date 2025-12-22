@extends('layouts.admin')

@section('title', 'Top Performers - A-plus CBT')
@section('page-title', 'Top Performers Leaderboard')
@section('mobile-title', 'Top Performers')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.results.index') }}">Results</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.results.analytics') }}">Analytics</a></li>
<li class="breadcrumb-item active">Top Performers</li>
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
        
        <a href="{{ route('admin.results.subject-performance') }}" class="btn-admin btn-admin-secondary">
            <i class="ri-bar-chart-2-line me-2"></i> Subject Performance
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.results.top-performers') }}" class="row g-3">
            <div class="col-md-4">
                <label for="exam_id" class="form-label">Filter by Exam</label>
                <select name="exam_id" id="exam_id" class="form-select">
                    <option value="">All Exams</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }} ({{ $exam->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            
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
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.results.top-performers') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Leaderboard -->
<div class="admin-card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Top 50 Performers Leaderboard</h6>
    </div>
    <div class="card-body">
        @if($topResults->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-award-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Top Performers Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['exam_id', 'date_from', 'date_to']))
                    Try adjusting your filters
                    @else
                    No exam results available yet
                    @endif
                </p>
            </div>
        </div>
        @else
        <div class="table-responsive">
            <table class="table admin-table data-table">
                <thead>
                    <tr>
                        <th width="60">Rank</th>
                        <th>Student</th>
                        <th>Exam</th>
                        <th>Performance</th>
                        <th>Grade</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Certificate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topResults as $index => $result)
                    @php
                        $rank = $index + 1;
                        $medalClass = '';
                        if ($rank == 1) {
                            $medalClass = 'gold';
                        } elseif ($rank == 2) {
                            $medalClass = 'silver';
                        } elseif ($rank == 3) {
                            $medalClass = 'bronze';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="rank-display">
                                @if($medalClass)
                                    <div class="medal {{ $medalClass }}">
                                        <i class="ri-medal-fill"></i>
                                    </div>
                                @endif
                                <span class="rank-number {{ $medalClass ? 'text-' . $medalClass : '' }}">
                                    {{ $rank }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-sm">
                                    {{ strtoupper(substr($result->user->name ?? '??', 0, 2)) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.users.show', $result->user_id) }}" class="text-decoration-none">
                                            {{ $result->user->name ?? 'Unknown User' }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $result->user->email ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1">
                                    <a href="{{ route('admin.exams.show', $result->exam_id) }}" class="text-decoration-none">
                                        {{ $result->exam->name ?? 'Unknown Exam' }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <code>{{ $result->exam->code ?? 'N/A' }}</code>
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-success">
                                        {{ number_format($result->percentage, 1) }}%
                                    </span>
                                    <small class="text-muted">
                                        {{ $result->correct_answers }}/{{ $result->total_questions }}
                                    </small>
                                </div>
                                @if($result->rank && $result->total_participants)
                                <small class="text-muted">
                                    Rank: {{ $result->rank_formatted }} of {{ $result->total_participants }}
                                </small>
                                @endif
                            </div>
                        </td>
                        <td>
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
                                {{ $result->grade }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $result->time_spent_minutes }} min
                            </small>
                            <br>
                            <small class="text-muted">
                                {{ number_format($result->average_time_per_question, 1) }}s/q
                            </small>
                        </td>
                        <td>
                            <small>{{ $result->exam_date->format('M d, Y') }}</small>
                        </td>
                        <td>
                            @if($result->certificate_number)
                                <span class="badge bg-success">Issued</span>
                            @elseif($result->is_passed)
                                <span class="badge bg-warning">Eligible</span>
                            @else
                                <span class="badge bg-secondary">Not Eligible</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.results.show', $result->id) }}" 
                                   class="btn-admin btn-admin-secondary btn-sm" 
                                   title="View Details">
                                    <i class="ri-eye-line"></i>
                                </a>
                                
                                @if($result->is_passed && !$result->certificate_number)
                                    <form action="{{ route('admin.results.issue-certificate', $result->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Issue certificate for this result?')">
                                        @csrf
                                        <button type="submit" 
                                                class="btn-admin btn-admin-primary btn-sm" 
                                                title="Issue Certificate">
                                            <i class="ri-certificate-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Grouped by Exam -->
@if($groupedResults->isNotEmpty())
<div class="admin-card">
    <div class="card-header">
        <h6 class="mb-0">Top Performers by Exam</h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($groupedResults as $examId => $results)
            @php
                $exam = $results->first()->exam;
                $topThree = $results->take(3);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="exam-leaderboard-card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $exam->name }}</h6>
                        <small class="text-muted">{{ $exam->code }}</small>
                    </div>
                    <div class="card-body">
                        <div class="top-three">
                            @foreach($topThree as $index => $result)
                            @php
                                $medalClass = $index == 0 ? 'gold' : ($index == 1 ? 'silver' : 'bronze');
                            @endphp
                            <div class="top-performer d-flex align-items-center mb-3">
                                <div class="medal {{ $medalClass }} me-3">
                                    <i class="ri-medal-fill"></i>
                                </div>
                                <div class="user-avatar-xs me-2">
                                    {{ strtoupper(substr($result->user->name ?? '??', 0, 1)) }}
                                </div>
                                <div class="flex-fill">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-medium">{{ $result->user->name ?? 'Unknown' }}</span>
                                        <span class="text-success fw-bold">{{ number_format($result->percentage, 1) }}%</span>
                                    </div>
                                    <small class="text-muted">{{ $result->time_spent_minutes }} min</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.results.top-performers') }}?exam_id={{ $examId }}" class="btn-admin btn-admin-secondary btn-sm">
                                View All
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.user-avatar-sm {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.user-avatar-xs {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
    flex-shrink: 0;
}

.rank-display {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rank-number {
    font-size: 18px;
    font-weight: 700;
    min-width: 24px;
}

.medal {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.medal.gold {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #8B4513;
}

.medal.silver {
    background: linear-gradient(135deg, #C0C0C0, #A9A9A9);
    color: #696969;
}

.medal.bronze {
    background: linear-gradient(135deg, #CD7F32, #8B4513);
    color: white;
}

.text-gold {
    color: #FFD700 !important;
}

.text-silver {
    color: #C0C0C0 !important;
}

.text-bronze {
    color: #CD7F32 !important;
}

.exam-leaderboard-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
}

.exam-leaderboard-card .card-header {
    background: #f9fafb;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.exam-leaderboard-card .card-body {
    padding: 1rem;
}

.top-performer {
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
    .rank-display {
        flex-direction: column;
        gap: 4px;
    }
    
    .table-actions {
        flex-direction: column;
        gap: 0.25rem;
    }
    
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
        order: [[0, 'asc']], // Order by rank ascending
        columnDefs: [
            { orderable: false, targets: [8] }, // Disable sorting for actions column
            { responsivePriority: 1, targets: [1] }, // Student
            { responsivePriority: 2, targets: [3] }, // Performance
            { responsivePriority: 3, targets: [0] }, // Rank
            { responsivePriority: 4, targets: [2] }, // Exam
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search performers...",
            lengthMenu: "_MENU_ performers per page",
            info: "Showing _START_ to _END_ of _TOTAL_ performers",
            infoEmpty: "Showing 0 to 0 of 0 performers",
            infoFiltered: "(filtered from _MAX_ total performers)",
            zeroRecords: "No matching performers found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        searching: true,
        paging: true,
        info: true,
        pageLength: 25
    });
    
    // Auto-submit filter form when exam filter changes
    $('#exam_id').change(function() {
        if ($(this).val()) {
            $(this).closest('form').submit();
        }
    });
});
</script>
@endpush