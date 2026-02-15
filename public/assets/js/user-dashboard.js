// User Dashboard JavaScript - Enhanced Debug Version
console.log("Dashboard JS loaded");

// Function to toggle sidebar
function toggleSidebar(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    console.log("Toggle clicked!");
    const sidebar = $(".user-sidebar");
    const isActive = sidebar.hasClass("active");

    sidebar.toggleClass("active");
    console.log("Sidebar active:", sidebar.hasClass("active"));

    // Toggle hamburger icon
    const icon = $("#sidebarToggle i, .menu-toggle i");
    if (!isActive) {
        icon.removeClass("ri-menu-line").addClass("ri-close-line");
    } else {
        icon.removeClass("ri-close-line").addClass("ri-menu-line");
    }
}

$(document).ready(function () {
    console.log("jQuery ready");
    console.log("Sidebar toggle button exists:", $("#sidebarToggle").length);
    console.log("Sidebar exists:", $(".user-sidebar").length);

    // Direct click handler on the button element
    $("#sidebarToggle, .menu-toggle").on("click", function (e) {
        toggleSidebar(e);
    });

    // Backup: Delegated click handler
    $(document).on("click", "#sidebarToggle, .menu-toggle", function (e) {
        toggleSidebar(e);
    });

    // Close sidebar when clicking outside on mobile
    $(document).on("click", function (event) {
        if ($(window).width() <= 992) {
            const sidebar = $(".user-sidebar");
            if (
                !$(event.target).closest(".user-sidebar").length &&
                !$(event.target).closest("#sidebarToggle, .menu-toggle")
                    .length &&
                sidebar.hasClass("active")
            ) {
                sidebar.removeClass("active");
                $("#sidebarToggle i, .menu-toggle i")
                    .removeClass("ri-close-line")
                    .addClass("ri-menu-line");
            }
        }
    });

    // Close sidebar when clicking on sidebar items (for mobile)
    $(document).on("click", ".sidebar-item", function () {
        if ($(window).width() <= 992) {
            $(".user-sidebar").removeClass("active");
            $("#sidebarToggle i, .menu-toggle i")
                .removeClass("ri-close-line")
                .addClass("ri-menu-line");
        }
    });

    // Window resize handler
    $(window).on("resize", function () {
        if ($(window).width() > 992) {
            $(".user-sidebar").removeClass("active");
            $("#sidebarToggle i, .menu-toggle i")
                .removeClass("ri-close-line")
                .addClass("ri-menu-line");
        }
    });

    // Initialize sidebar on page load (for mobile)
    if ($(window).width() <= 992) {
        $(".user-sidebar").removeClass("active");
        $("#sidebarToggle i, .menu-toggle i")
            .removeClass("ri-close-line")
            .addClass("ri-menu-line");
    }

    // Auto-dismiss alerts after 5 seconds
    $(".alert").not(".alert-danger, .alert-warning").delay(5000).fadeOut(300);

    // Exam Timer Functionality
    let examTimer = null;
    let examTimeRemaining = 0;

    // Start exam timer
    window.startExamTimer = function (minutes) {
        examTimeRemaining = minutes * 60;
        updateTimerDisplay();

        examTimer = setInterval(function () {
            examTimeRemaining--;
            updateTimerDisplay();

            if (examTimeRemaining <= 0) {
                clearInterval(examTimer);
                autoSubmitExam();
            }

            // Warning at 5 minutes
            if (examTimeRemaining === 300) {
                showTimeWarning();
            }
        }, 1000);

        $("#examTimerContainer").removeClass("d-none");
    };

    // Update timer display
    function updateTimerDisplay() {
        const hours = Math.floor(examTimeRemaining / 3600);
        const minutes = Math.floor((examTimeRemaining % 3600) / 60);
        const seconds = examTimeRemaining % 60;

        $(".timer-hours").text(hours.toString().padStart(2, "0"));
        $(".timer-minutes").text(minutes.toString().padStart(2, "0"));
        $(".timer-seconds").text(seconds.toString().padStart(2, "0"));
    }

    // Show time warning
    function showTimeWarning() {
        const warningAlert = `
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="ri-alarm-warning-line me-2"></i>
                <strong>Time Warning!</strong> Only 5 minutes remaining in your exam.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $(".main-content").prepend(warningAlert);
    }

    // Auto submit exam
    function autoSubmitExam() {
        if (confirm("Time is up! Your exam will be automatically submitted.")) {
            submitExam();
        }
    }

    // End exam button
    $("#endExamBtn").on("click", function () {
        if (
            confirm(
                "Are you sure you want to end the exam? This action cannot be undone."
            )
        ) {
            clearInterval(examTimer);
            submitExam();
        }
    });

    // Submit exam function
    function submitExam() {
        console.log("Exam submitted");
        $("#examTimerContainer").addClass("d-none");
        showAlert("Exam submitted successfully!", "success");
    }

    // Show alert function
    function showAlert(message, type = "info") {
        const alertClass =
            type === "success"
                ? "alert-success"
                : type === "error"
                ? "alert-danger"
                : type === "warning"
                ? "alert-warning"
                : "alert-info";

        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show">
                <i class="ri-information-line me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        $(".main-content").prepend(alert);
        $(".alert").delay(5000).fadeOut(300);
    }

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
});
