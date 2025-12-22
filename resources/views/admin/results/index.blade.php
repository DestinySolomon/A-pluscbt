@extends('layouts.admin')

@section('title', 'Results Management - A-plus CBT')
@section('page-title', 'Results Management')
@section('mobile-title', 'Results')

@section('breadcrumbs')
<li class="breadcrumb-item active">Results</li>
@endsection

@section('page-actions')
<div class="page-actions-wrapper">
    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="{{ route('admin.results.analytics') }}" class="btn-admin btn-admin-primary mb-2 mb-md-0">
            <i class="ri-bar-chart-line me-2"></i> View Analytics
        </a>
        
        <a href="{{ route('admin.results.top-performers') }}" class="btn-admin btn-admin-secondary mb-2 mb-md-0">
            <i class="ri-award-line me-2"></i> Top Performers
        </a>
        
        <!-- Export Dropdown -->
        <div class="dropdown">
            <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="ri-download-2-line me-2"></i> Export
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.results.export') . '?' . http_build_query(request()->query()) }}">
                        <i class="ri-file-excel-line me-2"></i> Export to CSV
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="ri-file-text-line me-2"></i> Export to PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Summary Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="ri-file-list-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($summary['total_results']) }}</h3>
                <p>Total Results</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="ri-line-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($summary['average_percentage'], 1) }}%</h3>
                <p>Avg. Score</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($summary['pass_rate'], 1) }}%</h3>
                <p>Pass Rate</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="ri-certificate-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ number_format($summary['total_certificates']) }}</h3>
                <p>Certificates</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.results.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="exam_id" class="form-label">Exam</label>
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
                <label for="date_range" class="form-label">Date Range</label>
                <div class="input-group">
                    <input type="date" 
                           name="date_from" 
                           id="date_from" 
                           class="form-control" 
                           placeholder="From"
                           value="{{ request('date_from') }}">
                    <span class="input-group-text">to</span>
                    <input type="date" 
                           name="date_to" 
                           id="date_to" 
                           class="form-control" 
                           placeholder="To"
                           value="{{ request('date_to') }}">
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <div class="input-group">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           class="form-control" 
                           placeholder="Search student, exam, email..." 
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <small class="text-muted">Search by student name, email, or exam name</small>
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="ri-filter-line me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.results.index') }}" class="btn-admin btn-admin-secondary">
                        <i class="ri-refresh-line me-2"></i> Clear All Filters
                    </a>
                    
                    <!-- Quick Date Filters -->
                    <div class="btn-group ms-auto">
                        <button type="button" class="btn-admin btn-admin-outline-secondary btn-sm" onclick="setDateRange('today')">
                            Today
                        </button>
                        <button type="button" class="btn-admin btn-admin-outline-secondary btn-sm" onclick="setDateRange('week')">
                            This Week
                        </button>
                        <button type="button" class="btn-admin btn-admin-outline-secondary btn-sm" onclick="setDateRange('month')">
                            This Month
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="card-body">
        @if($results->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="ri-file-list-line text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3">No Results Found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['exam_id', 'date_from', 'date_to', 'search']))
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
                        <th width="50">ID</th>
                        <th>Student</th>
                        <th>Exam</th>
                        <th>Performance</th>
                        <th>Grade & Status</th>
                        <th>Time Analysis</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    <tr>
                        <td>{{ $result->id }}</td>
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
                                    <span class="fw-bold {{ $result->percentage >= 50 ? 'text-success' : 'text-danger' }}">
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
                            <div class="d-flex flex-column gap-1">
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
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small class="text-muted">
                                    {{ $result->time_spent_minutes }} min
                                </small>
                                <small class="text-muted">
                                    {{ number_format($result->average_time_per_question, 1) }}s/q
                                </small>
                            </div>
                        </td>
                        <td>
                            <small>{{ $result->exam_date->format('M d, Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $result->exam_date->format('h:i A') }}</small>
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
                                
                                @if($result->certificate_number)
                                    <a href="{{ route('admin.results.certificate', $result->id) }}" 
                                       class="btn-admin btn-admin-success btn-sm" 
                                       title="View Certificate"
                                       target="_blank">
                                        <i class="ri-certificate-2-line"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Custom Pagination -->
            @if($results->hasPages())
            <div class="custom-pagination-wrapper">
                <div class="pagination-info">
                    <span class="text-muted">
                        Showing {{ $results->firstItem() }} to {{ $results->lastItem() }} of {{ $results->total() }} results
                    </span>
                </div>
                
                <nav class="custom-pagination" aria-label="Results pagination">
                    <ul class="pagination-list">
                        {{-- Previous Button --}}
                        @if ($results->onFirstPage())
                            <li class="pagination-item disabled">
                                <span class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $results->previousPageUrl() }}" class="pagination-link">
                                    <i class="ri-arrow-left-s-line"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach(range(1, $results->lastPage()) as $page)
                            @if($page == $results->currentPage())
                                <li class="pagination-item active">
                                    <span class="pagination-link">{{ $page }}</span>
                                </li>
                            @elseif($page == 1 || $page == $results->lastPage() || abs($page - $results->currentPage()) <= 2)
                                <li class="pagination-item">
                                    <a href="{{ $results->url($page) }}" class="pagination-link">{{ $page }}</a>
                                </li>
                            @elseif(abs($page - $results->currentPage()) == 3)
                                <li class="pagination-item disabled">
                                    <span class="pagination-link">...</span>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($results->hasMorePages())
                            <li class="pagination-item">
                                <a href="{{ $results->nextPageUrl() }}" class="pagination-link">
                                    <i class="ri-arrow-right-s-line"></i>
                                </a>
                            </li>
                        @else
                            <li class="pagination-item disabled">
                                <span class="pagination-link">
                                    <i class="ri-arrow-right-s-line"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
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

/* Table improvements */
.table-actions {
    min-width: 120px;
}

/* Mobile responsive page actions */
.page-actions-wrapper {
    width: 100%;
}

.btn-admin-outline-secondary {
    background: transparent;
    border: 1px solid #dee2e6;
    color: #495057;
}

.btn-admin-outline-secondary:hover {
    background: #f8f9fa;
    border-color: #dee2e6;
}

@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .user-avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 12px;
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
    
    .page-actions-wrapper .dropdown {
        width: 100%;
    }
    
    .page-actions-wrapper .dropdown .btn-admin {
        width: 100%;
    }
    
    .btn-group {
        width: 100%;
        margin-top: 0.5rem;
    }
    
    .btn-group .btn-admin {
        flex: 1;
    }
}

/* Desktop layout */
@media (min-width: 768px) {
    .page-actions-wrapper .d-flex {
        align-items: center;
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
        order: [[0, 'desc']], // Order by ID descending
        columnDefs: [
            { orderable: false, targets: [7] }, // Disable sorting for actions column
            { responsivePriority: 1, targets: [1, 2] }, // Student and Exam
            { responsivePriority: 2, targets: [7] }, // Actions
            { responsivePriority: 3, targets: [3] }, // Performance
            { responsivePriority: 4, targets: [4] }, // Grade & Status
            { responsivePriority: 5, targets: [0] }  // ID
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search results...",
            lengthMenu: "_MENU_ results per page",
            info: "Showing _START_ to _END_ of _TOTAL_ results",
            infoEmpty: "Showing 0 to 0 of 0 results",
            infoFiltered: "(filtered from _MAX_ total results)",
            zeroRecords: "No matching results found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        searching: false,
        paging: false,
        info: false
    });
    
    // Clear search button
    $('#clearSearch').click(function() {
        $('#search').val('');
        $(this).closest('form').submit();
    });
    
    // Auto-submit when exam filter changes
    $('#exam_id').change(function() {
        $(this).closest('form').submit();
    });
    
    // Auto-submit when both dates are filled
    $('#date_from, #date_to').change(function() {
        if ($('#date_from').val() && $('#date_to').val()) {
            $(this).closest('form').submit();
        }
    });
    
    // Set quick date ranges
    window.setDateRange = function(range) {
        const today = new Date();
        let dateFrom, dateTo;
        
        switch(range) {
            case 'today':
                dateFrom = today.toISOString().split('T')[0];
                dateTo = dateFrom;
                break;
            case 'week':
                const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
                const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                dateFrom = firstDay.toISOString().split('T')[0];
                dateTo = lastDay.toISOString().split('T')[0];
                break;
            case 'month':
                const firstDayMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDayMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                dateFrom = firstDayMonth.toISOString().split('T')[0];
                dateTo = lastDayMonth.toISOString().split('T')[0];
                break;
        }
        
        $('#date_from').val(dateFrom);
        $('#date_to').val(dateTo);
        $('#exam_id').closest('form').submit();
    };
});
</script>
@endpush