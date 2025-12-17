<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold text-white mb-4">Ready to Start Your Exam Preparation?</h2>
                <p class="lead text-white mb-4" style="opacity: 0.9;">Join thousands of students who are already improving their scores with our professional CBT platform</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-5 text-primary fw-semibold">
                            <i class="ri-dashboard-line me-2"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 text-primary fw-semibold">
                            <i class="ri-user-add-line me-2"></i>Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5">
                            <i class="ri-login-box-line me-2"></i>Login Now
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>