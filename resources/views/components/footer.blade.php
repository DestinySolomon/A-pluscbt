<footer class="py-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="height: 40px; width: 40px;">
                    <span class="text-white fw-bold fs-5">A-plus CBT</span>
                </div>
                <p class="small">Professional JAMB-style computer-based testing platform for secondary school students.</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('home') }}#features" class="text-decoration-none text-white">Features</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#how-it-works" class="text-decoration-none text-white">How It Works</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#subjects" class="text-decoration-none text-white">Subjects</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#testimonials" class="text-decoration-none text-white">Testimonials</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-semibold mb-3">For Students</h6>
                <ul class="list-unstyled small ">
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-decoration-none text-white">Register</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-decoration-none text-white">Login</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">Take Exam</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">View Results</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Legal</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">Terms of Service</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">Cookie Policy</a></li>
                    <li class="mb-2"><a href="{{ route('home') }}#contact" class="text-decoration-none text-white">Contact Us</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main copyright line -->
        <div class="border-top border-secondary pt-4 mt-4 text-center">
            <p class="small mb-0">© {{ date('Y') }} A-plus CBT. All rights reserved.</p>
        </div>
        
        <!-- Site by Dee - styled nicely -->
        <div class="site-by-dee" title="Designed & Developed with ❤️">
    <span class="text-white-50 small">Site by</span>
    <a href="#" class="text-primary text-white-50 fw-semibold small ms-1" style="text-decoration: none;">Dee</a>
    <i class="ri-heart-fill text-danger ms-1" style="font-size: 12px;"></i>
</div>
    </div>
</footer>