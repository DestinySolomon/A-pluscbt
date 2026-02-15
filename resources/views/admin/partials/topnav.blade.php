<nav class="admin-topnav">
    <div class="topnav-left">
        <button class="menu-toggle">
            <i class="ri-menu-line"></i>
        </button>
    </div>
    
    <div class="topnav-right">
        <!-- Real Notification System -->
        <div class="dropdown">
            <button class="btn btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                    onclick="loadNotifications()" id="notificationDropdown">
                <i class="ri-notification-3-line"></i>
                @auth
                    @if(Auth::user()->unread_notifications_count > 0)
                        <span class="notification-badge" id="notificationBadge">
                            {{ Auth::user()->unread_notifications_count > 99 ? '99+' : Auth::user()->unread_notifications_count }}
                        </span>
                    @endif
                @endauth
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px;">
                <div class="notification-header">
                    <h6 class="mb-0">Notifications</h6>
                    @auth
                        @if(Auth::user()->unread_notifications_count > 0)
                            <a href="#" class="small text-primary" onclick="markAllAsRead(event)" id="markAllReadBtn">Mark all as read</a>
                        @endif
                    @endauth
                </div>
                <div class="notification-list" id="notificationList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Loading state -->
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mb-0 mt-2">Loading notifications...</p>
                    </div>
                </div>
                <div class="notification-footer">
                    <a href="{{ route('admin.notifications.index') }}" class="text-primary">View all notifications</a>
                </div>
            </div>
        </div>
        
        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <div class="admin-user" data-bs-toggle="dropdown">
                <!-- Mobile: Show only icon/image -->
                <div class="user-avatar d-md-none">
                    @auth
                        @if(Auth::user()->profile_image_url)
                            <img src="{{ Auth::user()->profile_image_url }}" 
                                 alt="{{ Auth::user()->name }}"
                                 class="user-avatar-img"
                                 onerror="this.style.display='none'; this.parentNode.innerHTML='{{ Auth::user()->initials }}';">
                        @else
                            {{ Auth::user()->initials }}
                        @endif
                    @endauth
                </div>
                
                <!-- Desktop: Show full info -->
                <div class="d-none d-md-flex align-items-center gap-3">
                    <div class="user-avatar">
                        @auth
                            @if(Auth::user()->profile_image_url)
                                <img src="{{ Auth::user()->profile_image_url }}" 
                                     alt="{{ Auth::user()->name }}"
                                     class="user-avatar-img"
                                     onerror="this.style.display='none'; this.parentNode.innerHTML='{{ Auth::user()->initials }}';">
                            @else
                                {{ Auth::user()->initials }}
                            @endif
                        @endauth
                    </div>
                    <div class="user-info">
                        <div class="fw-medium">{{ Auth::user()->name ?? 'User' }}</div>
                        <small class="text-muted">Administrator</small>
                    </div>
                    <i class="ri-arrow-down-s-line"></i>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="ri-user-line me-2"></i>My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="ri-settings-3-line me-2"></i>System Settings</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.notifications.index') }}"><i class="ri-notification-3-line me-2"></i>All Notifications</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();">
                        <i class="ri-logout-box-r-line me-2"></i>Logout
                    </a>
                    <form id="logout-form-top" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

@push('scripts')
<script>
// ========== REAL NOTIFICATION SYSTEM ==========

let notificationPollingInterval = null;
let isNotificationsLoaded = false;

// Load notifications when dropdown is opened
function loadNotifications() {
    if (isNotificationsLoaded) return;
    
    fetch('/admin/notifications?limit=8')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            updateNotificationList(data.notifications || []);
            updateNotificationBadge(data.unread_count || 0);
            isNotificationsLoaded = true;
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            showNotificationError();
        });
}

// Update notification list HTML
function updateNotificationList(notifications) {
    const notificationList = document.getElementById('notificationList');
    
    if (!notifications || notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="text-center py-4">
                <i class="ri-notification-off-line text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted small mb-0 mt-2">No notifications yet</p>
            </div>
        `;
        
        // Hide "Mark all as read" button
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) markAllBtn.style.display = 'none';
        
        return;
    }
    
    let html = '';
    notifications.forEach(notification => {
        const isRead = notification.is_read ? 'read' : '';
        const timeAgo = formatTimeAgo(new Date(notification.created_at));
        
        html += `
            <a href="${notification.link || '#'}" 
               class="notification-item ${isRead}" 
               data-id="${notification.id}"
               onclick="handleNotificationClick(event, ${notification.id})">
                <div class="notification-icon" style="background: rgba(${hexToRgb(notification.color)}, 0.1); color: ${notification.color}">
                    <i class="${notification.icon}"></i>
                </div>
                <div class="notification-content">
                    <p class="mb-0 fw-medium">${escapeHtml(notification.title)}</p>
                    <p class="text-muted small mb-1">${escapeHtml(notification.message)}</p>
                    <small class="text-muted">${timeAgo}</small>
                </div>
                ${!notification.is_read ? '<span class="notification-unread-dot"></span>' : ''}
            </a>
        `;
    });
    
    notificationList.innerHTML = html;
}

// Handle notification click
function handleNotificationClick(event, notificationId) {
    event.preventDefault();
    event.stopPropagation();
    
    // Mark as read first
    markAsRead(notificationId).then(success => {
        if (success) {
            // Get the link and navigate to it
            const notificationElement = event.currentTarget;
            const link = notificationElement.getAttribute('href');
            
            if (link && link !== '#') {
                window.location.href = link;
            }
        }
    }).catch(error => {
        console.error('Error:', error);
        // Still navigate even if marking as read fails
        const link = event.currentTarget.getAttribute('href');
        if (link && link !== '#') {
            window.location.href = link;
        }
    });
}

// Mark notification as read (returns promise)
function markAsRead(notificationId) {
    return new Promise((resolve, reject) => {
        fetch(`/admin/notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update UI
                const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (notificationItem) {
                    notificationItem.classList.add('read');
                    const unreadDot = notificationItem.querySelector('.notification-unread-dot');
                    if (unreadDot) unreadDot.remove();
                }
                
                // Update badge
                updateNotificationBadge(data.unread_count || 0);
                resolve(true);
            } else {
                resolve(false);
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            reject(error);
        });
    });
}

// Mark all notifications as read
function markAllAsRead(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    fetch('/admin/notifications/mark-all-read', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update all notifications to read
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.add('read');
                const unreadDot = item.querySelector('.notification-unread-dot');
                if (unreadDot) unreadDot.remove();
            });
            
            // Update badge
            updateNotificationBadge(0);
            
            // Show success message
            const markAllBtn = document.getElementById('markAllReadBtn');
            if (markAllBtn) {
                const originalText = markAllBtn.textContent;
                markAllBtn.textContent = 'All marked as read!';
                markAllBtn.style.pointerEvents = 'none';
                
                setTimeout(() => {
                    markAllBtn.textContent = originalText;
                    markAllBtn.style.pointerEvents = 'auto';
                    markAllBtn.style.display = 'none';
                }, 2000);
            }
        }
    })
    .catch(error => {
        console.error('Error marking all as read:', error);
    });
}

// Update notification badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    const markAllBtn = document.getElementById('markAllReadBtn');
    
    if (count > 0) {
        if (!badge) {
            // Create badge if it doesn't exist
            const button = document.querySelector('#notificationDropdown');
            if (button) {
                const badgeHtml = `<span class="notification-badge" id="notificationBadge">${count > 99 ? '99+' : count}</span>`;
                button.insertAdjacentHTML('beforeend', badgeHtml);
            }
        } else {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        }
        
        // Show "Mark all as read" button
        if (markAllBtn) {
            markAllBtn.style.display = 'block';
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
        
        // Hide "Mark all as read" button
        if (markAllBtn) {
            markAllBtn.style.display = 'none';
        }
    }
}

// Poll for new notifications (every 60 seconds)
function startNotificationPolling() {
    // Stop any existing polling
    if (notificationPollingInterval) {
        clearInterval(notificationPollingInterval);
    }
    
    // Start new polling
    notificationPollingInterval = setInterval(() => {
        fetch('/admin/notifications/unread-count')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                updateNotificationBadge(data.count || 0);
            })
            .catch(error => {
                console.error('Error polling notifications:', error);
            });
    }, 60000); // 60 seconds
}

// Show notification error
function showNotificationError() {
    const notificationList = document.getElementById('notificationList');
    if (notificationList) {
        notificationList.innerHTML = `
            <div class="text-center py-4">
                <i class="ri-error-warning-line text-danger" style="font-size: 2rem;"></i>
                <p class="text-danger small mb-0 mt-2">Failed to load notifications</p>
                <button class="btn btn-sm btn-outline-primary mt-2" onclick="retryLoadNotifications()">
                    Try Again
                </button>
            </div>
        `;
    }
}

// Retry loading notifications
function retryLoadNotifications() {
    isNotificationsLoaded = false;
    loadNotifications();
}

// Format time ago
function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

// Convert hex to rgb
function hexToRgb(hex) {
    // Remove # if present
    hex = hex.replace('#', '');
    
    // Parse hex values
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    
    return `${r}, ${g}, ${b}`;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Start polling for new notifications
    startNotificationPolling();
    
    // Initialize badge with current count
    const initialCount = {{ Auth::user()->unread_notifications_count ?? 0 }};
    updateNotificationBadge(initialCount);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.notification-dropdown');
        const button = document.querySelector('#notificationDropdown');
        
        if (dropdown && button && !dropdown.contains(event.target) && !button.contains(event.target)) {
            const bsDropdown = bootstrap.Dropdown.getInstance(button);
            if (bsDropdown) {
                bsDropdown.hide();
            }
        }
    });
    
    // Reset loaded state when dropdown is closed
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('hidden.bs.dropdown', function() {
            // Optionally reset after dropdown is closed
            // isNotificationsLoaded = false;
        });
    }
});

// Clean up polling when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        if (notificationPollingInterval) {
            clearInterval(notificationPollingInterval);
            notificationPollingInterval = null;
        }
    } else {
        startNotificationPolling();
    }
});

// Prevent dropdown from closing when clicking inside
document.addEventListener('click', function(event) {
    if (event.target.closest('.notification-dropdown')) {
        event.stopPropagation();
    }
});
</script>
@endpush