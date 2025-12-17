// A-plus CBT - Homepage JavaScript
class APlusHomepage {
    constructor() {
        this.init();
    }

    init() {
        console.log("A-plus CBT Homepage initialized");

        // Initialize features in order
        this.initNavbarBehavior();
        this.initBackToTop();
        this.initContactForm();
        this.initAnimatedCounters();
        this.initSmoothScroll();
    }

    // 1. Navbar scroll behavior
    initNavbarBehavior() {
        const navbar = document.querySelector(".navbar");
        if (!navbar) return;

        window.addEventListener("scroll", () => {
            const currentScroll = window.pageYOffset;

            // Add/remove shadow based on scroll position
            if (currentScroll > 50) {
                navbar.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.1)";
                navbar.style.backgroundColor = "rgba(255, 255, 255, 0.98)";
            } else {
                navbar.style.boxShadow = "0 1px 3px rgba(0, 0, 0, 0.1)";
                navbar.style.backgroundColor = "rgba(255, 255, 255, 0.95)";
            }
        });

        // Add transition for smooth effects
        navbar.style.transition = "all 0.3s ease";
    }

    // 2. Back to top button
    initBackToTop() {
        // Create button
        const button = document.createElement("button");
        button.innerHTML = `
            <i class="ri-arrow-up-line"></i>
            <span class="back-to-top-text">Top</span>
        `;
        button.className = "back-to-top-btn";
        document.body.appendChild(button);

        // Show/hide based on scroll
        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 500) {
                button.classList.add("visible");
            } else {
                button.classList.remove("visible");
            }
        });

        // Scroll to top when clicked
        button.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth",
            });
        });
    }

    // 3. Contact form validation
    initContactForm() {
        const form = document.getElementById("contactForm");
        if (!form) return;

        form.addEventListener("submit", (e) => {
            e.preventDefault();

            if (this.validateContactForm(form)) {
                this.submitContactForm(form);
            }
        });

        // Add real-time validation
        const inputs = form.querySelectorAll("input, textarea, select");
        inputs.forEach((input) => {
            input.addEventListener("blur", () => {
                this.validateField(input);
            });
        });
    }

    validateContactForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll("[required]");

        requiredFields.forEach((field) => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const errorElement = field.nextElementSibling?.classList?.contains(
            "error-message"
        )
            ? field.nextElementSibling
            : this.createErrorElement(field);

        // Clear previous error
        field.classList.remove("is-invalid");
        if (errorElement) errorElement.textContent = "";

        // Validation rules
        if (field.hasAttribute("required") && !value) {
            this.showError(field, errorElement, "This field is required");
            return false;
        }

        if (field.type === "email" && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showError(
                    field,
                    errorElement,
                    "Please enter a valid email address"
                );
                return false;
            }
        }

        return true;
    }

    showError(field, errorElement, message) {
        field.classList.add("is-invalid");
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    createErrorElement(field) {
        const errorElement = document.createElement("div");
        errorElement.className = "error-message";
        errorElement.style.cssText = `
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        `;
        field.parentNode.appendChild(errorElement);
        return errorElement;
    }

    submitContactForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = `
            <span class="spinner"></span>
            Sending...
        `;
        submitBtn.disabled = true;

        // Simulate API call (replace with actual fetch later)
        setTimeout(() => {
            // Show success message
            this.showNotification(
                "Message sent successfully! We'll get back to you soon.",
                "success"
            );

            // Reset form
            form.reset();

            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;

            // Remove validation errors
            form.querySelectorAll(".is-invalid").forEach((el) => {
                el.classList.remove("is-invalid");
            });
            form.querySelectorAll(".error-message").forEach((el) => {
                el.style.display = "none";
            });
        }, 1500);
    }

    // 4. Animated statistics counters
    initAnimatedCounters() {
        const counters = document.querySelectorAll(".stat-card h3");
        if (counters.length === 0) return;

        // Check if element is in viewport
        const isInViewport = (element) => {
            const rect = element.getBoundingClientRect();
            return (
                rect.top <=
                (window.innerHeight || document.documentElement.clientHeight) *
                    0.8
            );
        };

        let animated = false;

        const animateCounters = () => {
            if (!animated && isInViewport(counters[0])) {
                animated = true;

                counters.forEach((counter) => {
                    const target = parseInt(
                        counter.textContent.replace(/,/g, "").replace("+", "")
                    );
                    const suffix = counter.textContent.includes("+") ? "+" : "";
                    const duration = 2000; // 2 seconds
                    const increment = target / (duration / 16); // 60fps
                    let current = 0;

                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            counter.textContent =
                                Math.floor(current).toLocaleString() + suffix;
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent =
                                target.toLocaleString() + suffix;
                        }
                    };

                    updateCounter();
                });
            }
        };

        // Check on scroll and load
        window.addEventListener("scroll", animateCounters);
        window.addEventListener("load", animateCounters);
    }

    // 5. Smooth scroll for anchor links
    initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener("click", (e) => {
                const href = anchor.getAttribute("href");

                if (href === "#") return;

                const targetElement = document.querySelector(href);
                if (targetElement) {
                    e.preventDefault();

                    const navbarHeight =
                        document.querySelector(".navbar").offsetHeight;
                    const targetPosition =
                        targetElement.offsetTop - navbarHeight - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: "smooth",
                    });
                }
            });
        });
    }

    // Utility: Show notification
    showNotification(message, type = "success") {
        // Create notification element
        const notification = document.createElement("div");
        notification.className = `homepage-notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="ri-${
                    type === "success" ? "check" : "error"
                }-circle-fill"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="ri-close-line"></i>
            </button>
        `;

        document.body.appendChild(notification);

        // Show with animation
        setTimeout(() => {
            notification.classList.add("show");
        }, 10);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.classList.remove("show");
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);

        // Close button
        notification
            .querySelector(".notification-close")
            .addEventListener("click", () => {
                notification.classList.remove("show");
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            });
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    new APlusHomepage();
});
