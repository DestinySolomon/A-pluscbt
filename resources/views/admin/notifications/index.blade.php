@extends('layouts.admin')

@section('title', 'All Notifications')

@section('page-title', 'Notifications')

@section('breadcrumbs')
    <li class="breadcrumb-item active">All Notifications</li>
@endsection

@section('page-actions')
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-admin btn-admin-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="ri-more-2-fill me-2"></i>Actions
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" onclick="markAllAsRead()"><i class="ri-check-double-line me-2"></i>Mark all as read</a></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="clearReadNotifications()"><i class="ri-delete-bin-line me-2"></i>Clear read notifications</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" onclick="refreshNotifications()"><i class="ri-refresh-line me-2"></i>Refresh</a></li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <!-- Notification Stats -->
            <div class="admin-card">
                <h6 class="card-title mb-3">Notification Statistics</h6>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="ri-notification-3-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalNotifications">{{ $notifications->total() }}</h3>
                            <p>Total</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="ri-notification-off-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="unreadNotifications">{{ Auth::user()->unread_notifications_count }}</h3>
                            <p>Unread</p>
                        </div>
                    </div>
                </div>
                
                <!-- Notification Filters -->
                <div class="mt-4">
                    <h6 class="mb-3">Filter by Type</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.notifications.index') }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request('type') ? 'active' : '' }}">
                            All Types
                            <span class="badge bg-primary rounded-pill">{{ Auth::user()->notifications()->count() }}</span>
                        </a>
                        @php
                            $types = [
                                'exam' => ['icon' => 'ri-file-text-line', 'color' => 'text-primary', 'bg' => 'bg-primary'],
                                'result' => ['icon' => 'ri-award-line', 'color' => 'text-success', 'bg' => 'bg-success'],
                                'system' => ['icon' => 'ri-settings-3-line', 'color' => 'text-info', 'bg' => 'bg-info'],
                                'user' => ['icon' => 'ri-user-add-line', 'color' => 'text-warning', 'bg' => 'bg-warning'],
                                'question' => ['icon' => 'ri-question-line', 'color' => 'text-danger', 'bg' => 'bg-danger'],
                            ];
                            
                            $typeCounts = Auth::user()->notifications()
                                ->selectRaw('type, count(*) as count')
                                ->groupBy('type')
                                ->pluck('count', 'type')
                                ->toArray();
                        @endphp
                        
                        @foreach($types as $typeKey => $typeInfo)
                            <a href="{{ route('admin.notifications.index', ['type' => $typeKey]) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('type') == $typeKey ? 'active' : '' }}">
                                <div>
                                    <i class="{{ $typeInfo['icon'] }} me-2 {{ $typeInfo['color'] }}"></i>
                                    {{ ucfirst($typeKey) }}
                                </div>
                                <span class="badge {{ $typeInfo['bg'] }} rounded-pill">{{ $typeCounts[$typeKey] ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-4">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-admin btn-admin-primary" onclick="markAllAsRead()">
                            <i class="ri-check-double-line me-2"></i>Mark all as read
                        </button>
                        <button type="button" class="btn btn-admin btn-admin-danger" onclick="clearReadNotifications()">
                            <i class="ri-delete-bin-line me-2"></i>Clear read notifications
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- Notifications List -->
            <div class="admin-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="mb-0">All Notifications</h6>
                        <small class="text-muted">Showing {{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} notifications</small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri-filter-line me-2"></i>
                                {{ request('status', 'All') == 'all' ? 'All Status' : ucfirst(request('status')) }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('status'), ['status' => 'all'])) }}">All Status</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('status'), ['status' => 'unread'])) }}">Unread Only</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('status'), ['status' => 'read'])) }}">Read Only</a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-admin btn-admin-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ri-calendar-line me-2"></i>
                                {{ request('period', 'all') == 'all' ? 'All Time' : ucfirst(request('period')) }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('period'), ['period' => 'all'])) }}">All Time</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('period'), ['period' => 'today'])) }}">Today</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('period'), ['period' => 'week'])) }}">This Week</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.notifications.index', array_merge(request()->except('period'), ['period' => 'month'])) }}">This Month</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                @if($notifications->count() > 0)
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th width="50"></th>
                                    <th>Notification</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="notificationsTableBody">
                                @foreach($notifications as $notification)
                                    <tr class="{{ $notification->is_read ? '' : 'bg-light' }}" id="notification-row-{{ $notification->id }}">
                                        <td>
                                            <div class="notification-list-icon" style="background: rgba({{ $notification->color_rgb }}, 0.1); color: {{ $notification->color_hex }}">
                                                <i class="{{ $notification->icon }}"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <strong class="mb-1">{{ $notification->title }}</strong>
                                                <small class="text-muted">{{ Str::limit($notification->message, 80) }}</small>
                                                @if($notification->link)
                                                    <small><a href="{{ $notification->link }}" class="text-primary">View details</a></small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: {{ $notification->color_hex }}; color: white;">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <small>{{ $notification->created_at->format('M d, Y') }}</small>
                                                <small class="text-muted">{{ $notification->created_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="badge bg-success">Read</span>
                                                <small class="d-block text-muted">{{ $notification->read_at->diffForHumans() }}</small>
                                            @else
                                                <span class="badge bg-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                @if(!$notification->is_read)
                                                    <button class="btn btn-sm btn-admin btn-admin-success" 
                                                            onclick="markSingleAsRead({{ $notification->id }})"
                                                            title="Mark as read">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-admin btn-admin-danger" 
                                                        onclick="deleteNotification({{ $notification->id }})"
                                                        title="Delete">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">
                                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} entries
                            </small>
                        </div>
                        <nav aria-label="Page navigation">
                            {{ $notifications->links() }}
                        </nav>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="ri-notification-off-line" style="font-size: 4rem; color: #9ca3af;"></i>
                            <h5 class="mt-3">No notifications found</h5>
                            <p class="text-muted">You don't have any notifications matching your filters.</p>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-admin btn-admin-primary mt-2">
                                <i class="ri-refresh-line me-2"></i>Clear Filters
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .notification-list-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .list-group-item.active {
        background-color: #f0fdfa;
        border-color: #14b8a6;
        color: #0d9488;
    }
    
    .empty-state {
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        opacity: 0.5;
    }
    
    .bg-light {
        background-color: #f9fafb !important;
    }
</style>
@endpush

@push('scripts')
<script>
// Function to mark all notifications as read
function markAllAsRead() {
    if (!confirm('Are you sure you want to mark all notifications as read?')) return;
    
    fetch('{{ route("admin.notifications.mark-all-read") }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all rows to show as read
            document.querySelectorAll('tr.bg-light').forEach(row => {
                row.classList.remove('bg-light');
                const statusCell = row.querySelector('td:nth-child(5)');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="badge bg-success">Read</span>
                        <small class="d-block text-muted">Just now</small>
                    `;
                }
                // Hide mark as read buttons
                const markBtn = row.querySelector('button[onclick^="markSingleAsRead"]');
                if (markBtn) markBtn.style.display = 'none';
            });
            
            // Update unread count
            document.getElementById('unreadNotifications').textContent = '0';
            
            // Update topnav badge if exists
            const topnavBadge = document.getElementById('notificationBadge');
            if (topnavBadge) {
                topnavBadge.style.display = 'none';
            }
            
            showToast('All notifications marked as read successfully!', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error marking notifications as read', 'error');
    });
}

// Function to mark single notification as read
function markSingleAsRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/read`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the row
            const row = document.getElementById(`notification-row-${notificationId}`);
            if (row) {
                row.classList.remove('bg-light');
                const statusCell = row.querySelector('td:nth-child(5)');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="badge bg-success">Read</span>
                        <small class="d-block text-muted">Just now</small>
                    `;
                }
                // Hide mark as read button
                const markBtn = row.querySelector('button[onclick^="markSingleAsRead"]');
                if (markBtn) markBtn.style.display = 'none';
            }
            
            // Update unread count
            const currentUnread = parseInt(document.getElementById('unreadNotifications').textContent);
            document.getElementById('unreadNotifications').textContent = Math.max(0, currentUnread - 1);
            
            // Update topnav badge if exists
            updateTopnavBadge(Math.max(0, currentUnread - 1));
            
            showToast('Notification marked as read', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error marking notification as read', 'error');
    });
}

// Function to delete a notification
function deleteNotification(notificationId) {
    if (!confirm('Are you sure you want to delete this notification?')) return;
    
    fetch(`/admin/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the row
            const row = document.getElementById(`notification-row-${notificationId}`);
            if (row) {
                row.remove();
                
                // Update total count
                const currentTotal = parseInt(document.getElementById('totalNotifications').textContent);
                document.getElementById('totalNotifications').textContent = Math.max(0, currentTotal - 1);
                
                // Update unread count if notification was unread
                if (row.classList.contains('bg-light')) {
                    const currentUnread = parseInt(document.getElementById('unreadNotifications').textContent);
                    document.getElementById('unreadNotifications').textContent = Math.max(0, currentUnread - 1);
                    updateTopnavBadge(Math.max(0, currentUnread - 1));
                }
                
                // Check if table is now empty
                if (document.querySelectorAll('#notificationsTableBody tr').length === 0) {
                    showEmptyState();
                }
            }
            
            showToast('Notification deleted successfully', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting notification', 'error');
    });
}

// Function to clear read notifications
function clearReadNotifications() {
    if (!confirm('Are you sure you want to clear all read notifications? This action cannot be undone.')) return;
    
    fetch('{{ route("admin.notifications.clear-read") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove all read rows
            document.querySelectorAll('tr:not(.bg-light)').forEach(row => {
                if (!row.id.includes('notification-row-')) return;
                row.remove();
            });
            
            // Update total count
            const remainingCount = document.querySelectorAll('#notificationsTableBody tr').length;
            document.getElementById('totalNotifications').textContent = remainingCount;
            
            // Check if table is now empty
            if (remainingCount === 0) {
                showEmptyState();
            }
            
            showToast(`Cleared ${data.count} read notifications`, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error clearing notifications', 'error');
    });
}

// Function to refresh notifications
function refreshNotifications() {
    window.location.reload();
}

// Function to update topnav badge
function updateTopnavBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Function to show empty state
function showEmptyState() {
    const tableBody = document.getElementById('notificationsTableBody');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="empty-state">
                        <i class="ri-notification-off-line" style="font-size: 4rem; color: #9ca3af;"></i>
                        <h5 class="mt-3">No notifications found</h5>
                        <p class="text-muted">You don't have any notifications.</p>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-admin btn-admin-primary mt-2">
                            <i class="ri-refresh-line me-2"></i>Refresh
                        </a>
                    </div>
                </td>
            </tr>
        `;
    }
}

// Function to show toast messages
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());
    
    const toast = document.createElement('div');
    toast.className = `custom-toast alert alert-${type} alert-dismissible fade show`;
    toast.innerHTML = `
        <i class="ri-${type === 'success' ? 'checkbox-circle-fill' : 'error-warning-fill'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    // Style the toast
    Object.assign(toast.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        zIndex: '9999',
        minWidth: '300px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)'
    });
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add row hover effects
    const rows = document.querySelectorAll('#notificationsTableBody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8fafc';
        });
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('bg-light')) {
                this.style.backgroundColor = '';
            }
        });
    });
    
    // Make notification titles clickable
    document.querySelectorAll('td:nth-child(2) strong').forEach(title => {
        title.style.cursor = 'pointer';
        title.addEventListener('click', function() {
            const link = this.parentElement.querySelector('a');
            if (link) {
                window.location.href = link.href;
            }
        });
    });
});
</script>
@endpush