@extends('layouts.admin')

@section('title', 'Testimonial Details - A-plus CBT')
@section('page-title', 'Testimonial Details')
@section('mobile-title', Str::limit($testimonial->student_name, 20))

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.testimonials.index') }}">Testimonials</a>
    </li>
    <li class="breadcrumb-item active">{{ Str::limit($testimonial->student_name, 15) }}</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn-admin btn-admin-primary">
            <i class="ri-edit-line me-2"></i> Edit
        </a>
        
        @if(!$testimonial->is_approved)
            <form action="{{ route('admin.testimonials.approve', $testimonial->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-admin btn-admin-success">
                    <i class="ri-check-double-line me-2"></i> Approve
                </button>
            </form>
        @endif

        <form action="{{ route('admin.testimonials.feature', $testimonial->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-admin {{ $testimonial->is_featured ? 'btn-admin-warning' : 'btn-admin-secondary' }}">
                <i class="{{ $testimonial->is_featured ? 'ri-star-fill' : 'ri-star-line' }} me-2"></i>
                {{ $testimonial->is_featured ? 'Remove Featured' : 'Mark as Featured' }}
            </button>
        </form>

        <a href="{{ route('admin.testimonials.index') }}" class="btn-admin btn-admin-light">
            <i class="ri-arrow-left-line me-2"></i> Back
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <!-- Student Profile Card -->
            <div class="admin-card mb-4">
                <div class="card-body text-center">
                    @if($testimonial->hasPhoto())
                        <img src="{{ $testimonial->photo_url }}" alt="{{ $testimonial->student_name }}" class="rounded-circle border mb-4" style="width: 180px; height: 180px; object-fit: cover;">
                    @else
                        <div class="avatar-xl mx-auto mb-4 bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center">
                            <span class="display-3 fw-bold">{{ $testimonial->initials }}</span>
                        </div>
                    @endif

                    <h3 class="mb-2">{{ $testimonial->student_name }}</h3>
                    
                    @if($testimonial->student_course)
                        <p class="text-muted mb-3">
                            <i class="ri-book-open-line me-1"></i>
                            {{ $testimonial->student_course }}
                        </p>
                    @endif
                    
                    <!-- Rating Display -->
                    <div class="mb-4">
                        <div class="star-display-large mb-2">
                            {!! $testimonial->star_rating !!}
                        </div>
                        <span class="badge bg-light text-dark fs-6">{{ $testimonial->rating }}.0 Rating</span>
                    </div>
                    
                    <!-- Score Achievement -->
                    @if($testimonial->score_achieved)
                        <div class="mb-4">
                            <h5 class="text-success">{{ $testimonial->score_achieved }}% Score</h5>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $testimonial->score_achieved }}%;" 
                                     aria-valuenow="{{ $testimonial->score_achieved }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $testimonial->score_achieved }}%
                                </div>
                            </div>
                            <small class="text-muted">JAMB Score Achieved</small>
                        </div>
                    @endif
                    
                    <!-- Associated User -->
                    @if($testimonial->user)
                        <div class="border-top pt-3 mt-3">
                            <h6 class="mb-2">Linked Student Account</h6>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                                    {{ substr($testimonial->user->name, 0, 1) }}
                                </div>
                                <div class="text-start">
                                    <strong>{{ $testimonial->user->name }}</strong><br>
                                    <small class="text-muted">{{ $testimonial->user->email }}</small>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $testimonial->user->id) }}" 
                               class="btn-admin btn-admin-sm btn-admin-outline-primary mt-2">
                                <i class="ri-external-link-line me-1"></i> View Profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Testimonial Info Card -->
            <div class="admin-card">
                <div class="card-header">
                    <h5 class="mb-0">Testimonial Information</h5>
                </div>
                <div class="card-body">
                    <table class="table admin-table-sm">
                        <tr>
                            <th width="40%">Status:</th>
                            <td>
                                @if($testimonial->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending Approval</span>
                                @endif
                                
                                @if($testimonial->is_featured)
                                    <span class="badge bg-info ms-1">Featured</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Display Order:</th>
                            <td>{{ $testimonial->display_order }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>
                                {{ $testimonial->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $testimonial->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>
                                {{ $testimonial->updated_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $testimonial->updated_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                        @if($testimonial->user)
                        <tr>
                            <th>Student ID:</th>
                            <td>#{{ $testimonial->user->id }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Testimonial ID:</th>
                            <td>#{{ $testimonial->id }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Testimonial Content Card -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Testimonial Content</h5>
                </div>
                <div class="card-body">
                    <div class="testimonial-content">
                        <div class="quote-section mb-5">
                            <i class="ri-double-quotes-l display-4 text-primary opacity-25 float-start me-3"></i>
                            <p class="lead mb-0">{{ $testimonial->testimonial_text }}</p>
                            <i class="ri-double-quotes-r display-4 text-primary opacity-25 float-end mt-3"></i>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" 
                               class="btn-admin btn-admin-primary">
                                <i class="ri-edit-line me-2"></i> Edit Testimonial
                            </a>
                            
                            @if(!$testimonial->is_approved)
                                <form action="{{ route('admin.testimonials.approve', $testimonial->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-admin btn-admin-success">
                                        <i class="ri-check-double-line me-2"></i> Approve Testimonial
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.testimonials.feature', $testimonial->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn-admin {{ $testimonial->is_featured ? 'btn-admin-warning' : 'btn-admin-outline-warning' }}">
                                    <i class="{{ $testimonial->is_featured ? 'ri-star-fill' : 'ri-star-line' }} me-2"></i>
                                    {{ $testimonial->is_featured ? 'Remove Featured' : 'Mark as Featured' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.testimonials.destroy', $testimonial->id) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this testimonial? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-admin btn-admin-danger">
                                    <i class="ri-delete-bin-line me-2"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Associated User Details (if exists) -->
            @if($testimonial->user)
                <div class="admin-card">
                    <div class="card-header">
                        <h5 class="mb-0">Associated Student Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Full Name</label>
                                    <p class="fs-5 mb-0">{{ $testimonial->user->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Email Address</label>
                                    <p class="fs-5 mb-0">{{ $testimonial->user->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Account Status</label>
                                    <p class="mb-0">
                                        @if($testimonial->user->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Role</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ ucfirst($testimonial->user->role) }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Member Since</label>
                                    <p class="mb-0">{{ $testimonial->user->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Last Login</label>
                                    <p class="mb-0">
                                        @if($testimonial->user->last_login_at)
                                            {{ $testimonial->user->last_login_at->format('M d, Y h:i A') }}
                                        @else
                                            <span class="text-muted">Never logged in</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.users.show', $testimonial->user->id) }}" 
                               class="btn-admin btn-admin-primary">
                                <i class="ri-external-link-line me-2"></i> View Full Student Profile
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .avatar-xl {
            width: 180px;
            height: 180px;
        }
        
        .avatar-sm {
            width: 32px;
            height: 32px;
        }
        
        .star-display-large {
            font-size: 1.75rem;
        }
        
        .quote-section {
            position: relative;
            padding: 20px 0;
        }
        
        .bg-primary-light {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }
        
        .btn-admin-outline-primary {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-admin-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-admin-outline-warning {
            background-color: transparent;
            border: 1px solid var(--bs-warning);
            color: var(--bs-warning);
        }
        
        .btn-admin-outline-warning:hover {
            background-color: var(--bs-warning);
            color: black;
        }
        
        .admin-table-sm th {
            font-weight: 500;
            color: var(--bs-secondary);
            padding: 8px 0;
        }
        
        .admin-table-sm td {
            padding: 8px 0;
        }
        
        .lead {
            font-size: 1.25rem;
            line-height: 1.8;
            color: var(--bs-dark);
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Copy testimonial ID
            $('#copyTestimonialId').click(function(e) {
                e.preventDefault();
                const testimonialId = '{{ $testimonial->id }}';
                navigator.clipboard.writeText(testimonialId).then(function() {
                    const originalText = $(this).html();
                    $(this).html('<i class="ri-check-line me-2"></i> Copied!');
                    setTimeout(() => {
                        $(this).html(originalText);
                    }, 2000);
                });
            });
            
            // Quick actions
            $('.quick-action-btn').click(function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                const testimonialId = '{{ $testimonial->id }}';
                
                if (action === 'approve' && !confirm('Are you sure you want to approve this testimonial?')) {
                    return;
                }
                
                if (action === 'delete' && !confirm('Are you sure you want to delete this testimonial? This action cannot be undone.')) {
                    return;
                }
                
                // Submit the appropriate form
                if (action === 'approve') {
                    $('#approveForm').submit();
                } else if (action === 'delete') {
                    $('#deleteForm').submit();
                }
            });
        });
    </script>
@endpush