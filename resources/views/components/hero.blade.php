<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="badge-custom mb-4">
                    <i class="ri-award-line"></i>
                    <span class="fw-medium">JAMB-Style CBT Platform</span>
                </div>
                <h1 class="display-4 fw-bold mb-4">Master Your Exams with Professional CBT Practice</h1>
                <p class="lead text-muted mb-4">Experience authentic JAMB-style computer-based testing with our comprehensive platform. Practice with real exam conditions, track your progress, and boost your confidence before the big day.</p>
                <div class="d-flex flex-wrap gap-3 mb-5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-4 shadow">
                            <i class="ri-dashboard-line me-2"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 shadow">
                            <i class="ri-user-add-line me-2"></i>Start Practicing Now
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-4">
                            <i class="ri-login-box-line me-2"></i>Login to Dashboard
                        </a>
                    @endauth
                </div>
                <div class="row g-4 pt-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-user-line text-primary fs-3"></i>
                            <div>
                                <h3 class="h4 mb-0">5,000+</h3>
                                <p class="text-muted small mb-0">Active Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-file-list-line text-primary fs-3"></i>
                            <div>
                                <h3 class="h4 mb-0">10,000+</h3>
                                <p class="text-muted small mb-0">Practice Questions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <img src="{{ asset('assets/images/hero-image.jpg') }}" alt="Students taking exam" class="img-fluid">
                    <div class="hero-badge d-none d-md-block">
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon icon-teal mb-0">
                                <i class="ri-timer-line"></i>
                            </div>
                            <div>
                                <p class="text-muted small mb-0">Real-time Timer</p>
                                <h6 class="fw-bold mb-0">Exam Simulation</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>