@extends('layouts.admin')

@section('title', 'Testimonials - A-plus CBT')
@section('page-title', 'Testimonials Management')
@section('mobile-title', 'Testimonials')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active">Testimonials</li>
@endsection

@section('page-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.testimonials.create') }}" class="btn-admin btn-admin-primary">
            <i class="ri-add-circle-line me-2"></i> Add Testimonial
        </a>
        <button type="button" id="reorderBtn" class="btn-admin btn-admin-secondary d-none" onclick="toggleReorderMode()">
            <i class="ri-sort-desc me-2"></i> Reorder
        </button>
    </div>
@endsection

@section('content')
    <div class="admin-card">
        <div class="card-body">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="admin-stat-card">
                        <div class="stat-icon bg-primary-light text-primary">
                            <i class="ri-chat-quote-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $stats['total'] }}</h3>
                            <p class="stat-label">Total Testimonials</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="admin-stat-card">
                        <div class="stat-icon bg-success-light text-success">
                            <i class="ri-check-double-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $stats['approved'] }}</h3>
                            <p class="stat-label">Approved</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="admin-stat-card">
                        <div class="stat-icon bg-warning-light text-warning">
                            <i class="ri-time-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $stats['pending'] }}</h3>
                            <p class="stat-label">Pending Review</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="admin-stat-card">
                        <div class="stat-icon bg-info-light text-info">
                            <i class="ri-star-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ number_format($stats['average_rating'], 1) }}</h3>
                            <p class="stat-label">Avg. Rating</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filters & Search</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.testimonials.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Featured</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rating" class="form-label">Min. Rating</label>
                                <select name="rating" id="rating" class="form-select">
                                    <option value="">Any Rating</option>
                                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select name="sort_by" id="sort_by" class="form-select">
                                    <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="rating_high" {{ request('sort_by') == 'rating_high' ? 'selected' : '' }}>Highest Rating</option>
                                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                    <option value="order" {{ request('sort_by') == 'order' ? 'selected' : '' }}>Display Order</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search" class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Search name, course or text..." value="{{ request('search') }}">
                                    <button class="btn-admin btn-admin-primary" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn-admin btn-admin-primary">
                                        <i class="ri-filter-line me-2"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('admin.testimonials.index') }}" class="btn-admin btn-admin-secondary">
                                        <i class="ri-close-line me-2"></i> Clear
                                    </a>
                                </div>
                                
                                @if($testimonials->total() > 0)
                                    <span class="text-muted align-self-center">
                                        Showing {{ $testimonials->firstItem() }}-{{ $testimonials->lastItem() }} of {{ $testimonials->total() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Actions -->
            <form action="{{ route('admin.testimonials.bulk-action') }}" method="POST" id="bulkActionForm">
                @csrf
                <input type="hidden" name="action" id="bulkAction">
                <input type="hidden" name="testimonials" id="bulkTestimonials">
                
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-3" id="bulkActionsBar">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">
                            Select all <span id="selectedCount" class="badge bg-primary ms-2">0</span>
                        </label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn-admin btn-admin-secondary dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false" id="bulkActionsBtn" disabled>
                                <i class="ri-play-list-2-line me-2"></i> Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-action="approve">
                                    <i class="ri-check-double-line me-2"></i> Approve Selected
                                </a></li>
                                <li><a class="dropdown-item" href="#" data-action="delete">
                                    <i class="ri-delete-bin-line me-2"></i> Delete Selected
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-action="feature">
                                    <i class="ri-star-line me-2"></i> Mark as Featured
                                </a></li>
                                <li><a class="dropdown-item" href="#" data-action="unfeature">
                                    <i class="ri-star-s-line me-2"></i> Remove Featured
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Testimonials Table -->
                <div class="table-responsive" id="testimonialsTable">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th width="50" class="d-none reorder-col"></th>
                                <th width="50" class="select-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="tableSelectAll">
                                    </div>
                                </th>
                                <th width="70">Photo</th>
                                <th>Student</th>
                                <th>Testimonial</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th width="120">Created</th>
                                <th width="150" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @forelse($testimonials as $testimonial)
                            <tr data-id="{{ $testimonial->id }}" class="{{ !$testimonial->is_approved ? 'table-warning-light' : '' }}">
                                <td class="d-none reorder-col">
                                    <div class="drag-handle text-muted">
                                        <i class="ri-draggable"></i>
                                    </div>
                                </td>
                                <td class="select-col">
                                    <div class="form-check">
                                        <input class="form-check-input testimonial-checkbox" 
                                               type="checkbox" value="{{ $testimonial->id }}">
                                    </div>
                                </td>
                                <td>
                                    @if($testimonial->hasPhoto())
                                        <img src="{{ $testimonial->photo_url }}" alt="{{ $testimonial->student_name }}" 
                                             class="avatar avatar-sm rounded-circle" style="object-fit: cover;">
                                    @else
                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                            {{ $testimonial->initials }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $testimonial->student_name }}</strong>
                                        @if($testimonial->student_course)
                                            <small class="text-muted">{{ $testimonial->student_course }}</small>
                                        @endif
                                        @if($testimonial->score_achieved)
                                            <div class="mt-1">
                                                <span class="badge bg-success">{{ $testimonial->score_achieved }}% Score</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="testimonial-preview">
                                        <p class="mb-0">{{ Str::limit($testimonial->testimonial_text, 100) }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $testimonial->rating)
                                                    <i class="ri-star-fill text-warning"></i>
                                                @else
                                                    <i class="ri-star-line text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="badge bg-light text-dark">{{ $testimonial->rating }}.0</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-badges">
                                        @if($testimonial->is_approved)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                        
                                        @if($testimonial->is_featured)
                                            <span class="badge bg-info mt-1">Featured</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $testimonial->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.testimonials.show', $testimonial) }}" 
                                           class="btn-admin-icon btn-admin-light" title="View">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" 
                                           class="btn-admin-icon btn-admin-light" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        
                                        @if(!$testimonial->is_approved)
                                            <form action="{{ route('admin.testimonials.approve', $testimonial) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-admin-icon btn-admin-light" title="Approve">
                                                    <i class="ri-check-double-line"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.testimonials.feature', $testimonial) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-admin-icon btn-admin-light {{ $testimonial->is_featured ? 'text-warning' : '' }}" 
                                                    title="{{ $testimonial->is_featured ? 'Remove Featured' : 'Mark as Featured' }}">
                                                <i class="{{ $testimonial->is_featured ? 'ri-star-fill' : 'ri-star-line' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-admin-icon btn-admin-light text-danger" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-chat-quote-line display-4 text-muted mb-3"></i>
                                        <h5>No testimonials found</h5>
                                        <p class="text-muted mb-4">Start by adding a new testimonial</p>
                                        <a href="{{ route('admin.testimonials.create') }}" class="btn-admin btn-admin-primary">
                                            <i class="ri-add-circle-line me-2"></i> Add Testimonial
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            @if($testimonials->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <p class="text-muted mb-0">Showing {{ $testimonials->firstItem() }} to {{ $testimonials->lastItem() }} of {{ $testimonials->total() }} testimonials</p>
                </div>
                <div>
                    {{ $testimonials->links('vendor.pagination.admin') }}
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .admin-stat-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: var(--bs-white);
            border-radius: 8px;
            border: 1px solid var(--bs-border-color);
            transition: all 0.3s ease;
        }
        
        .admin-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--bs-dark);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--bs-secondary);
            margin-bottom: 0;
        }
        
        .table-warning-light {
            background-color: rgba(var(--bs-warning-rgb), 0.05) !important;
        }
        
        .star-rating {
            font-size: 0.875rem;
        }
        
        .testimonial-preview {
            max-width: 250px;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .bg-primary-light {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }
        
        .bg-success-light {
            background-color: rgba(var(--bs-success-rgb), 0.1) !important;
        }
        
        .bg-warning-light {
            background-color: rgba(var(--bs-warning-rgb), 0.1) !important;
        }
        
        .bg-info-light {
            background-color: rgba(var(--bs-info-rgb), 0.1) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .drag-handle {
            cursor: move;
            opacity: 0.5;
            transition: opacity 0.2s;
        }
        
        .drag-handle:hover {
            opacity: 1;
        }
        
        .reorder-mode .drag-handle {
            opacity: 1;
        }
        
        .reorder-mode .select-col,
        .reorder-mode #bulkActionsBar {
            display: none !important;
        }
        
        .reorder-mode .reorder-col {
            display: table-cell !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            let selectedTestimonials = [];
            let isReorderMode = false;
            
            // Select all checkboxes
            $('#selectAll, #tableSelectAll').change(function() {
                const isChecked = $(this).prop('checked');
                $('.testimonial-checkbox').prop('checked', isChecked);
                updateSelectedCount();
            });
            
            // Individual checkbox
            $(document).on('change', '.testimonial-checkbox', function() {
                if (!this.checked) {
                    $('#selectAll, #tableSelectAll').prop('checked', false);
                }
                updateSelectedCount();
            });
            
            // Update selected count
            function updateSelectedCount() {
                selectedTestimonials = [];
                $('.testimonial-checkbox:checked').each(function() {
                    selectedTestimonials.push($(this).val());
                });
                
                const count = selectedTestimonials.length;
                $('#selectedCount').text(count);
                $('#bulkTestimonials').val(JSON.stringify(selectedTestimonials));
                
                // Enable/disable bulk actions button
                $('#bulkActionsBtn').prop('disabled', count === 0);
            }
            
            // Bulk actions
            $('[data-action]').click(function(e) {
                e.preventDefault();
                
                if (selectedTestimonials.length === 0) {
                    showToast('Please select at least one testimonial.', 'warning');
                    return;
                }
                
                const action = $(this).data('action');
                let confirmMessage = '';
                let actionText = '';
                
                switch (action) {
                    case 'delete':
                        confirmMessage = 'Are you sure you want to delete selected testimonials? This action cannot be undone.';
                        actionText = 'deleting';
                        break;
                    case 'approve':
                        confirmMessage = 'Are you sure you want to approve selected testimonials?';
                        actionText = 'approving';
                        break;
                    case 'feature':
                        confirmMessage = 'Are you sure you want to mark selected testimonials as featured?';
                        actionText = 'featuring';
                        break;
                    case 'unfeature':
                        confirmMessage = 'Are you sure you want to remove featured status from selected testimonials?';
                        actionText = 'unfeaturing';
                        break;
                }
                
                if (confirm(confirmMessage)) {
                    $('#bulkAction').val(action);
                    
                    // Show loading state
                    const btn = $('#bulkActionsBtn');
                    const originalText = btn.html();
                    btn.prop('disabled', true).html('<i class="ri-loader-4-line me-2"></i> ' + actionText + '...');
                    
                    // Submit form
                    $('#bulkActionForm').submit();
                }
            });
            
            // Toggle reorder mode
            function toggleReorderMode() {
                isReorderMode = !isReorderMode;
                const table = $('#testimonialsTable');
                const reorderBtn = $('#reorderBtn');
                
                if (isReorderMode) {
                    table.addClass('reorder-mode');
                    reorderBtn.html('<i class="ri-save-line me-2"></i> Save Order');
                    reorderBtn.removeClass('btn-admin-secondary').addClass('btn-admin-primary');
                    initSortable();
                } else {
                    table.removeClass('reorder-mode');
                    reorderBtn.html('<i class="ri-sort-desc me-2"></i> Reorder');
                    reorderBtn.removeClass('btn-admin-primary').addClass('btn-admin-secondary');
                    saveOrder();
                }
            }
            
            // Initialize sortable
            function initSortable() {
                const sortable = Sortable.create(document.getElementById('sortable'), {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onUpdate: function() {
                        // Visual feedback
                        $('#testimonialsTable tbody tr').each(function(index) {
                            $(this).find('.drag-handle').css('opacity', 0.7);
                        });
                    }
                });
            }
            
            // Save order to server
            function saveOrder() {
                const order = [];
                $('#testimonialsTable tbody tr').each(function(index) {
                    const id = $(this).data('id');
                    if (id) {
                        order.push(id);
                    }
                });
                
                $.ajax({
                    url: '{{ route("admin.testimonials.reorder") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Display order updated successfully.', 'success');
                            // Reset reorder button visibility
                            setTimeout(() => {
                                $('#reorderBtn').addClass('d-none');
                            }, 2000);
                        }
                    },
                    error: function() {
                        showToast('Failed to update display order.', 'error');
                    }
                });
            }
            
            // Show reorder button only when viewing by display order
            @if(request('sort_by') == 'order')
                $('#reorderBtn').removeClass('d-none');
            @endif
            
            // Helper function for toast notifications
            function showToast(message, type = 'info') {
                // You can implement your toast notification here
                alert(message); // Temporary fallback
            }
            
            // Initialize
            updateSelectedCount();
        });
    </script>
@endpush