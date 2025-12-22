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
    // $('.data-table').DataTable({
    //     responsive: true,
    //     language: {
    //         search: "_INPUT_",
    //         searchPlaceholder: "Search...",
    //         lengthMenu: "_MENU_ records per page",
    //         info: "Showing _START_ to _END_ of _TOTAL_ entries",
    //         infoEmpty: "Showing 0 to 0 of 0 entries",
    //         infoFiltered: "(filtered from _MAX_ total entries)",
    //         zeroRecords: "No matching records found",
    //         paginate: {
    //             first: "First",
    //             last: "Last",
    //             next: "Next",
    //             previous: "Previous"
    //         }
    //     }
    // });
    
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
    
    // Initialize Profile Settings Functionality
    initProfileSettings();
    
    // Initialize Tab Persistence
    initTabPersistence();
});

// ========== PROFILE SETTINGS FUNCTIONALITY ==========

function initProfileSettings() {
    // Profile image upload with preview
    $('#profileImageInput').on('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            // Validate file size
            if (file.size > maxSize) {
                alert('File size must be less than 2MB');
                $(this).val('');
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                $(this).val('');
                return;
            }
            
            // Show preview before upload (optional)
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create a preview modal
                const previewModal = `
                    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Preview Profile Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="${e.target.result}" class="img-fluid rounded-circle mb-3" style="max-height: 300px;">
                                    <p>Do you want to upload this image?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="confirmImageUpload">
                                        <i class="ri-upload-line me-2"></i>Upload Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                $('#imagePreviewModal').remove();
                $('body').append(previewModal);
                
                const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
                modal.show();
                
                $('#confirmImageUpload').click(function() {
                    $('#profileImageForm').submit();
                    // Show loading state
                    const submitBtn = $('#profileImageForm').find('button[type="submit"]');
                    if (submitBtn.length) {
                        submitBtn.addClass('btn-loading');
                    }
                });
                
                // Auto-submit after modal closes if confirmed wasn't clicked
                $('#imagePreviewModal').on('hidden.bs.modal', function() {
                    if (!$('#confirmImageUpload').hasClass('confirmed')) {
                        $(this).remove();
                    }
                });
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove profile image confirmation
    $('#removeImageForm').on('submit', function(e) {
        if (!confirm('Are you sure you want to remove your profile image?')) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // Password strength indicator
    const newPasswordInput = $('input[name="new_password"]');
    if (newPasswordInput.length) {
        // Create strength meter
        const strengthMeter = `
            <div class="password-strength mt-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Password strength:</small>
                    <small class="strength-text text-muted">Weak</small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar strength-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        `;
        newPasswordInput.after(strengthMeter);
        
        newPasswordInput.on('input', function() {
            const password = $(this).val();
            const strength = checkPasswordStrength(password);
            const bar = $('.strength-bar');
            const text = $('.strength-text');
            
            let width = 0;
            let color = '#dc3545';
            let strengthText = 'Weak';
            
            if (strength >= 80) {
                width = 100;
                color = '#14a44d';
                strengthText = 'Strong';
            } else if (strength >= 60) {
                width = 75;
                color = '#ffc107';
                strengthText = 'Good';
            } else if (strength >= 40) {
                width = 50;
                color = '#fd7e14';
                strengthText = 'Fair';
            } else if (password.length > 0) {
                width = 25;
                color = '#dc3545';
                strengthText = 'Weak';
            }
            
            bar.css({
                'width': width + '%',
                'background-color': color
            });
            text.text(strengthText).css('color', color);
        });
        
        // Validate password on form submit
        $('form[action*="profile/password"]').on('submit', function(e) {
            const password = newPasswordInput.val();
            if (password && checkPasswordStrength(password) < 40) {
                e.preventDefault();
                alert('Password is too weak. Please use a stronger password.');
                return false;
            }
            return true;
        });
    }
    
    // Delete account confirmation
    const deleteConfirm = $('#deleteConfirm');
    const confirmDeleteBtn = $('#confirmDeleteBtn');
    
    if (deleteConfirm.length && confirmDeleteBtn.length) {
        deleteConfirm.on('input', function() {
            confirmDeleteBtn.prop('disabled', $(this).val().toUpperCase() !== 'DELETE');
        });
        
        confirmDeleteBtn.on('click', function() {
            const password = prompt('For security, please enter your password to confirm account deletion:');
            if (password) {
                // This would typically make an AJAX call to verify password
                alert('Account deletion request submitted. This feature requires backend implementation.');
                $('#deleteAccountModal').modal('hide');
            }
        });
    }
    
    // Form validation enhancements
    $('form[action*="profile/"]').on('submit', function(e) {
        if (!validateForm($(this))) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // Auto-capitalize initials in name field
    $('input[name="name"]').on('blur', function() {
        const name = $(this).val();
        if (name) {
            // Capitalize each word
            const capitalized = name.replace(/\b\w/g, l => l.toUpperCase());
            $(this).val(capitalized);
        }
    });
    
    // Phone number formatting
    $('input[name="phone"]').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 0) {
            phone = phone.match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            $(this).val(!phone[2] ? phone[1] : '(' + phone[1] + ') ' + phone[2] + (phone[3] ? '-' + phone[3] : ''));
        }
    });
}

// ========== TAB PERSISTENCE ==========

function initTabPersistence() {
    // Handle tab clicks
    $('#settingsTab button[data-bs-toggle="tab"]').on('click', function() {
        const target = $(this).data('bs-target');
        if (target) {
            // Store in URL hash
            window.location.hash = target;
            
            // Store in localStorage for page refresh
            localStorage.setItem('admin_active_tab', target);
        }
    });
    
    // Restore active tab on page load
    const hash = window.location.hash;
    const storedTab = localStorage.getItem('admin_active_tab');
    
    let activeTab = '#profile'; // Default tab
    
    if (hash && $(hash).length) {
        activeTab = hash;
    } else if (storedTab && $(storedTab).length) {
        activeTab = storedTab;
    }
    
    // Activate the tab
    if (activeTab && $(activeTab).length) {
        const trigger = $('[data-bs-target="' + activeTab + '"]');
        if (trigger.length) {
            const tab = new bootstrap.Tab(trigger[0]);
            tab.show();
        }
    }
    
    // Clear stored tab when leaving profile page
    $(window).on('beforeunload', function() {
        if (!window.location.pathname.includes('/admin/profile')) {
            localStorage.removeItem('admin_active_tab');
        }
    });
}

// ========== UTILITY FUNCTIONS ==========

// Password strength calculation
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 10;
    
    return Math.min(strength, 100);
}

// Form validation
function validateForm(form) {
    const inputs = form.find('input[required], textarea[required], select[required]');
    let valid = true;
    
    inputs.each(function() {
        const $input = $(this);
        if (!$input.val().trim()) {
            $input.addClass('is-invalid');
            
            // Add error message if not exists
            if (!$input.next('.invalid-feedback').length) {
                $input.after('<div class="invalid-feedback">This field is required.</div>');
            }
            
            valid = false;
        } else {
            $input.removeClass('is-invalid');
            $input.next('.invalid-feedback').remove();
            
            // Additional validations
            if ($input.attr('type') === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test($input.val())) {
                    $input.addClass('is-invalid');
                    $input.after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                    valid = false;
                }
            }
            
            if ($input.attr('name') === 'phone') {
                const phoneRegex = /^[\d\s\-\(\)\+]+$/;
                if ($input.val() && !phoneRegex.test($input.val().replace(/[\s\-\(\)]/g, ''))) {
                    $input.addClass('is-invalid');
                    $input.after('<div class="invalid-feedback">Please enter a valid phone number.</div>');
                    valid = false;
                }
            }
        }
    });
    
    return valid;
}

// ========== NOTIFICATION FUNCTIONALITY ==========

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

// ========== ADDITIONAL ENHANCEMENTS ==========

// Character counter for bio field
$('textarea[name="bio"]').on('input', function() {
    const maxLength = 500;
    const currentLength = $(this).val().length;
    const counter = $(this).next('.char-counter') || $(this).parent().find('.char-counter');
    
    if (!counter.length) {
        $(this).after(`<div class="char-counter text-muted small mt-1"><span class="char-count">${currentLength}</span> / ${maxLength} characters</div>`);
    } else {
        counter.find('.char-count').text(currentLength);
    }
    
    // Update counter color based on length
    const charCount = counter.find('.char-count');
    if (currentLength > maxLength * 0.9) {
        charCount.addClass('text-danger').removeClass('text-warning');
    } else if (currentLength > maxLength * 0.75) {
        charCount.addClass('text-warning').removeClass('text-danger');
    } else {
        charCount.removeClass('text-danger text-warning');
    }
});

// URL validation for social links
$('input[name*="_url"]').on('blur', function() {
    const url = $(this).val();
    if (url && !isValidUrl(url)) {
        $(this).addClass('is-invalid');
        if (!$(this).next('.invalid-feedback').length) {
            $(this).after('<div class="invalid-feedback">Please enter a valid URL (include http:// or https://)</div>');
        }
    } else {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    }
});

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// Smooth scrolling to active tab on page load
$(window).on('load', function() {
    const activeTab = $('.nav-link.active');
    if (activeTab.length && $(window).width() < 768) {
        $('html, body').animate({
            scrollTop: activeTab.offset().top - 20
        }, 500);
    }
});

// Handle form submission loading states
$('form').on('submit', function() {
    const submitBtn = $(this).find('button[type="submit"]');
    if (submitBtn.length && !submitBtn.hasClass('btn-loading')) {
        submitBtn.addClass('btn-loading');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ri-loader-4-line spin me-2"></i>Processing...');
    }
});

// Add spin class for loading icons
$('<style>').text(`
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
    }
    .btn-loading:after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
`).appendTo('head');