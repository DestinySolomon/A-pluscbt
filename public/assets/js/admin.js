// Admin Dashboard JavaScript
$(document).ready(function() {
    console.log('Admin dashboard loaded');
    
    // Toggle sidebar on mobile
    $('.menu-toggle').click(function() {
        $('.admin-sidebar').toggleClass('active');
    });
    
    // Close sidebar when clicking outside on mobile
    $(document).click(function(event) {
        if ($(window).width() <= 992) {
            if (!$(event.target).closest('.admin-sidebar, .menu-toggle').length) {
                $('.admin-sidebar').removeClass('active');
            }
        }
    });
    
    // Initialize DataTables
    $('.data-table').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "_MENU_ records per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
    
    // Confirm before delete
    $('.confirm-delete').click(function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const itemName = $(this).data('name') || 'this item';
        
        if (confirm(`Are you sure you want to delete ${itemName}? This action cannot be undone.`)) {
            window.location.href = url;
        }
    });
    
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const input = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('ri-eye-line').addClass('ri-eye-off-line');
        } else {
            input.attr('type', 'password');
            icon.removeClass('ri-eye-off-line').addClass('ri-eye-line');
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Update active menu item based on current URL
    const currentPath = window.location.pathname;
    $('.sidebar-item').each(function() {
        const linkPath = $(this).attr('href');
        if (linkPath && currentPath.includes(linkPath.replace('/admin', ''))) {
            $(this).addClass('active');
        }
    });
});


// Notification functionality
// $('.btn-icon[data-bs-toggle="dropdown"]').click(function(e) {
//     e.stopPropagation();
    
//     // Close other dropdowns
//     $('.dropdown-menu').not($(this).next('.dropdown-menu')).removeClass('show');
    
//     // Toggle this dropdown
//     $(this).next('.dropdown-menu').toggleClass('show');
    
//     // Mark notifications as read
//     $('.notification-badge').fadeOut();
// });

// Close notifications when clicking outside
$(document).click(function(event) {
    if (!$(event.target).closest('.notification-dropdown, .btn-icon').length) {
        $('.notification-dropdown').removeClass('show');
    }
});

// Notification items click
$('.notification-item').click(function(e) {
    e.preventDefault();
    $(this).addClass('read');
    updateNotificationCount();
});

// Update notification count
function updateNotificationCount() {
    const unreadCount = $('.notification-item:not(.read)').length;
    const badge = $('.notification-badge');
    
    if (unreadCount > 0) {
        badge.text(unreadCount).fadeIn();
    } else {
        badge.fadeOut();
    }
}

// Mark all as read
$('.notification-header a').click(function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    $('.notification-item').addClass('read');
    updateNotificationCount();
    
    // Show confirmation
    const originalText = $(this).text();
    $(this).text('Marked all as read!');
    
    setTimeout(() => {
        $(this).text(originalText);
    }, 2000);
});

// Initialize notification count
updateNotificationCount();