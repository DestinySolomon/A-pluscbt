@extends('layouts.user-dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Welcome, ' . Auth::user()->name)

@section('breadcrumbs')
    <li class="breadcrumb-item active">Overview</li>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="ri-book-line"></i>
                </div>
                <div class="stat-value" id="availableExamsCount">0</div>
                <div class="stat-label">Available Exams</div>
                <p class="stat-desc">Ready to practice</p>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <div class="stat-value" id="completedExamsCount">0</div>
                <div class="stat-label">Completed</div>
                <p class="stat-desc">Exams taken</p>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="ri-percent-line"></i>
                </div>
                <div class="stat-value" id="averageScore">0%</div>
                <div class="stat-label">Average Score</div>
                <p class="stat-desc">Your performance</p>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="ri-time-line"></i>
                </div>
                <div class="stat-value" id="totalTime">0h</div>
                <div class="stat-label">Time Spent</div>
                <p class="stat-desc">Total practice</p>
            </div>
        </div>
    </div>

    <!-- Quick Start & Recent Activity -->
    <div class="row">
        <!-- Quick Start Card -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="ri-flashlight-line me-2"></i> Quick Start
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Start practicing immediately with these options:</p>
                    
                    <div class="d-grid gap-3">
                        <a href="{{ route('user.exams.index') }}" class="btn btn-primary btn-lg">
                            <i class="ri-play-circle-line me-2"></i> Start New Exam
                        </a>
                        
                        <a href="#" class="btn btn-outline-primary btn-lg">
                            <i class="ri-bar-chart-line me-2"></i> View Results
                        </a>
                        
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="ri-user-settings-line me-2"></i> Update Profile
                        </a>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="#" class="btn btn-outline-success">
                                <i class="ri-download-line me-2"></i> Download Materials
                            </a>
                            <a href="#" class="btn btn-outline-info">
                                <i class="ri-information-line me-2"></i> View Instructions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="ri-history-line me-2"></i> Recent Activity
                        </h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            View All <i class="ri-arrow-right-line ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(false) <!-- Replace with actual condition when you have data -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Exam</th>
                                    <th>Date</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will go here -->
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="ri-inbox-line display-4 text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-3">No Recent Activity</h5>
                        <p class="text-muted mb-4">You haven't taken any exams yet. Start your first practice session!</p>
                        <a href="{{ route('user.exams.index') }}" class="btn btn-primary">
                            <i class="ri-play-line me-1"></i> Start First Exam
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Practice -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="ri-star-line me-2"></i> Recommended For You
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="recommendedExams">
                        <!-- We'll load real exams here via AJAX -->
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading recommended exams...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Custom styles for this dashboard */
    .stat-card {
        border-left: 4px solid var(--user-primary);
    }
    
    .exam-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .exam-card:hover {
        border-color: var(--user-primary);
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.1);
        transform: translateY(-4px);
    }
    
    .exam-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }
    
    .exam-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .exam-meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .card-title.text-primary {
        color: var(--user-primary) !important;
    }
    
    .btn-primary {
        background-color: var(--user-primary);
        border-color: var(--user-primary);
    }
    
    .btn-primary:hover {
        background-color: var(--user-primary-dark);
        border-color: var(--user-primary-dark);
    }
    
    .btn-outline-primary {
        color: var(--user-primary);
        border-color: var(--user-primary);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--user-primary);
        border-color: var(--user-primary);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load real stats via AJAX
    loadDashboardStats();
    
    // Load recommended exams
    loadRecommendedExams();
    
    function loadDashboardStats() {
        fetch('{{ route("user.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('availableExamsCount').textContent = data.data.available_exams;
                    document.getElementById('completedExamsCount').textContent = data.data.completed_exams;
                    document.getElementById('averageScore').textContent = data.data.average_score + '%';
                    document.getElementById('totalTime').textContent = data.data.total_time + 'h';
                }
            })
            .catch(error => {
                console.error('Error loading stats:', error);
                // Fallback to demo data
                document.getElementById('availableExamsCount').textContent = '0';
                document.getElementById('completedExamsCount').textContent = '0';
                document.getElementById('averageScore').textContent = '0%';
                document.getElementById('totalTime').textContent = '0h';
            });
    }
    
    function loadRecommendedExams() {
        fetch('{{ route("user.exams.recommended") }}')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('recommendedExams');
                
                if (data.success && data.data.length > 0) {
                    let html = '';
                    
                    data.data.forEach(exam => {
                        html += `
                            <div class="col-md-6 col-lg-4">
                                <div class="exam-card h-100">
                                    <div class="exam-badge bg-primary text-white px-3 py-1 rounded-pill d-inline-block mb-3">
                                        <small>Practice Test</small>
                                    </div>
                                    <h6 class="exam-title">${exam.name}</h6>
                                    <div class="exam-meta mb-3">
                                        <span class="exam-meta-item">
                                            <i class="ri-time-line text-primary"></i> ${exam.duration} mins
                                        </span>
                                        <span class="exam-meta-item">
                                            <i class="ri-question-line text-primary"></i> ${exam.questions} Qs
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-4">${exam.description}</p>
                                    <a href="${exam.start_url}" class="btn btn-primary w-100">
                                        <i class="ri-play-line me-1"></i> Start Test
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="col-12 text-center py-4">
                            <i class="ri-inbox-line display-4 text-muted"></i>
                            <p class="mt-2 text-muted">No exams available at the moment</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading recommended exams:', error);
                const container = document.getElementById('recommendedExams');
                container.innerHTML = `
                    <div class="col-12 text-center py-4">
                        <i class="ri-error-warning-line display-4 text-muted"></i>
                        <p class="mt-2 text-muted">Error loading exams. Please try again later.</p>
                    </div>
                `;
            });
    }
});
</script>
@endpush