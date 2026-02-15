@extends('layouts.user-dashboard')

@section('title', 'My Testimonials')
@section('page-title', 'My Testimonials')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('user.testimonials.index') }}">Testimonials</a></li>
    <li class="breadcrumb-item active">My Testimonials</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-user-line me-2"></i> My Testimonials
                    </h5>
                    <div>
                        <a href="{{ route('user.testimonials.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Add New
                        </a>
                        <a href="{{ route('user.testimonials.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i> Back to All
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($testimonials->count() > 0)
                    <!-- Stats Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-3">
                                    <div class="display-6 fw-bold text-primary">{{ $testimonials->total() }}</div>
                                    <small class="text-muted">Total Submitted</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-3">
                                    @php
                                        $approvedCount = $testimonials->where('is_approved', true)->count();
                                    @endphp
                                    <div class="display-6 fw-bold text-success">{{ $approvedCount }}</div>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-3">
                                    @php
                                        $pendingCount = $testimonials->where('is_approved', false)->count();
                                    @endphp
                                    <div class="display-6 fw-bold text-warning">{{ $pendingCount }}</div>
                                    <small class="text-muted">Pending Review</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-3">
                                    @php
                                        $featuredCount = $testimonials->where('is_featured', true)->count();
                                    @endphp
                                    <div class="display-6 fw-bold text-warning">{{ $featuredCount }}</div>
                                    <small class="text-muted">Featured</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonials Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course/Subject</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>JAMB Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($testimonials as $testimonial)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($testimonial->photo_path)
                                            <img src="{{ asset('storage/' . $testimonial->photo_path) }}" 
                                                 alt="{{ $testimonial->student_name }}"
                                                 class="rounded-circle me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                 style="width: 40px; height: 40px;">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            @endif
                                            <span>{{ $testimonial->student_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $testimonial->student_course }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ri-star-{{ $i <= $testimonial->rating ? 'fill' : 'line' }} text-warning me-1"></i>
                                            @endfor
                                            <small class="text-muted ms-1">({{ $testimonial->rating }}/5)</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($testimonial->is_approved)
                                            @if($testimonial->is_featured)
                                            <span class="badge bg-warning text-dark">
                                                <i class="ri-star-line me-1"></i> Featured
                                            </span>
                                            @else
                                            <span class="badge bg-success">
                                                <i class="ri-check-line me-1"></i> Approved
                                            </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ri-time-line me-1"></i> Pending Review
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            {{ $testimonial->created_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $testimonial->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($testimonial->score_achieved)
                                            <span class="badge bg-success">
                                                {{ $testimonial->score_achieved }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('user.testimonials.show', $testimonial) }}" 
                                               class="btn btn-outline-primary"
                                               title="View">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <a href="{{ route('user.testimonials.edit', $testimonial) }}" 
                                               class="btn btn-outline-warning"
                                               title="Edit">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $testimonial->id }}"
                                                    title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal{{ $testimonial->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this testimonial?</p>
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="ri-alert-line me-2"></i>
                                                            This action cannot be undone.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('user.testimonials.destroy', $testimonial) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete Testimonial</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $testimonials->firstItem() }} to {{ $testimonials->lastItem() }} of {{ $testimonials->total() }} testimonials
                        </div>
                        <div>
                            {{ $testimonials->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-inbox-line display-4 text-muted"></i>
                        <h5 class="text-muted mt-3">No Testimonials Yet</h5>
                        <p class="text-muted mb-4">You haven't shared any experiences yet.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('user.testimonials.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Share Your Experience
                            </a>
                            <a href="{{ route('user.testimonials.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Back to All Testimonials
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
@if($testimonials->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Quick Tips</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <i class="ri-check-line text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Approved Testimonials</small>
                                <p class="text-muted mb-0 small">Visible to all users on the main testimonials page.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <i class="ri-time-line text-warning me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Pending Review</small>
                                <p class="text-muted mb-0 small">Under review by our team. You can edit until approved.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <i class="ri-star-line text-warning me-2 mt-1"></i>
                            <div>
                                <small class="fw-semibold">Featured</small>
                                <p class="text-muted mb-0 small">Highlighted at the top of the testimonials page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(20, 184, 166, 0.05);
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
    }
</style>
@endpush