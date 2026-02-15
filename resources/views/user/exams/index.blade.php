@extends('layouts.user-dashboard')

@section('title', 'Available Exams')
@section('page-title', 'Available Exams')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Exams</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="ri-book-line me-2"></i> Available Practice Tests
                </h5>
            </div>
            <div class="card-body">
                @if($exams->count() > 0)
                    <div class="row">
                        @foreach($exams as $exam)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="exam-card h-100">
                                <div class="exam-header mb-3">
                                    <div class="exam-badge bg-primary text-white px-3 py-1 rounded-pill d-inline-block mb-2">
                                        {{ ucfirst(str_replace('_', ' ', $exam->type)) }}
                                    </div>
                                    <h5 class="exam-title mb-2">{{ $exam->name }}</h5>
                                    <p class="text-muted small mb-0">{{ $exam->description }}</p>
                                </div>
                                
                                <div class="exam-details mb-4">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <i class="ri-time-line text-primary me-1"></i>
                                                <small>{{ $exam->duration_minutes }} mins</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <i class="ri-question-line text-primary me-1"></i>
                                                <small>{{ $exam->total_questions }} Qs</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <i class="ri-pass-valid-line text-primary me-1"></i>
                                                <small>Pass: {{ $exam->passing_score }}%</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <i class="ri-repeat-line text-primary me-1"></i>
                                                <small>
                                                    @if($exam->max_attempts == 0)
                                                        Unlimited
                                                    @else
                                                        {{ $exam->max_attempts }} attempts
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="exam-subjects mb-4">
                                    <small class="text-muted d-block mb-2">Subjects:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($exam->subjects->take(3) as $examSubject)
                                            <span class="badge bg-light text-dark">{{ $examSubject->subject->name ?? 'General' }}</span>
                                        @endforeach
                                        @if($exam->subjects->count() > 3)
                                            <span class="badge bg-light text-dark">+{{ $exam->subjects->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('user.exams.instructions', $exam->id) }}" class="btn btn-primary">
                                        <i class="ri-play-line me-1"></i> Start Exam
                                    </a>
                                    <a href="{{ route('user.exams.show', $exam->id) }}" class="btn btn-outline-primary">
                                        <i class="ri-information-line me-1"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $exams->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-inbox-line display-4 text-muted"></i>
                        <h5 class="text-muted mt-3">No Exams Available</h5>
                        <p class="text-muted">No practice tests are available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .exam-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
        background: white;
    }
    
    .exam-card:hover {
        border-color: var(--user-primary);
        box-shadow: 0 8px 25px rgba(20, 184, 166, 0.15);
        transform: translateY(-5px);
    }
    
    .exam-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .exam-badge {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .exam-subjects .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush