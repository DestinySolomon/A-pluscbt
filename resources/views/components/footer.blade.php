@php
    $logo = App\Models\Setting::get('logo');
    $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
    $siteTagline = App\Models\Setting::get('site_tagline', 'Professional JAMB-style computer-based testing platform for secondary school students.');
    $contactEmail = App\Models\Setting::get('contact_email');
    $contactPhone = App\Models\Setting::get('contact_phone');
    $contactAddress = App\Models\Setting::get('contact_address');
@endphp

<footer class="py-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    @if($logo && Storage::disk('public')->exists($logo))
                        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }} Logo" style="height: 40px; max-width: 40px; object-fit: contain;">
                    @else
                        <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $siteName }} Logo" style="height: 40px; width: 40px;">
                    @endif
                    <span class="text-white fw-bold fs-5">{{ $siteName }}</span>
                </div>
                <p class="small">{{ $siteTagline }}</p>
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
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-decoration-none text-white">Register</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-decoration-none text-white">Login</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">Take Exam</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-white">View Results</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Contact Info</h6>
                <ul class="list-unstyled small">
                    @if($contactEmail)
                        <li class="mb-2">
                            <i class="ri-mail-line me-2"></i>
                            <a href="mailto:{{ $contactEmail }}" class="text-decoration-none text-white">{{ $contactEmail }}</a>
                        </li>
                    @endif
                    @if($contactPhone)
                        <li class="mb-2">
                            <i class="ri-phone-line me-2"></i>
                            <a href="tel:{{ $contactPhone }}" class="text-decoration-none text-white">{{ $contactPhone }}</a>
                        </li>
                    @endif
                    @if($contactAddress)
                        <li class="mb-2">
                            <i class="ri-map-pin-line me-2"></i>
                            <span class="text-white">{{ $contactAddress }}</span>
                        </li>
                    @endif
                    <li class="mb-2"><a href="{{ route('home') }}#contact" class="text-decoration-none text-white">
                        <i class="ri-customer-service-2-line me-2"></i>Contact Form
                    </a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main copyright line -->
        <div class="border-top border-secondary pt-4 mt-4 text-center">
            <p class="small mb-0">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
        </div>
        
        <!-- Site by Dee - styled nicely -->
        <div class="site-by-dee" title="Designed & Developed with ❤️">
            <span class="text-white-50 small">Site by</span>
            <a href="#" class="text-primary text-white-50 fw-semibold small ms-1" style="text-decoration: none;">Dee</a>
            <i class="ri-heart-fill text-danger ms-1" style="font-size: 12px;"></i>
        </div>
    </div>
</footer>