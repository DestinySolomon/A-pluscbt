{{-- resources/views/user/materials/index.blade.php --}}
@extends('layouts.user-dashboard')

@section('title', 'Study Materials')
@section('page-title', 'Study Materials & Resources')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Study Materials</li>
@endsection

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h4 class="mb-2 text-primary"><i class="ri-book-open-line me-2"></i> JAMB Study Materials</h4>
                        <p class="text-muted mb-0">
                            Access curated study resources, past questions, and educational materials to help you prepare effectively for your exams. 
                            All links open in new tabs for your convenience.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            <i class="ri-lightbulb-flash-line me-1"></i> Updated Regularly
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Official JAMB Resources -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0 text-primary">
                    <i class="ri-government-line me-2"></i> Official JAMB Resources
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Official resources from JAMB for accurate information.</p>
                
                <div class="list-group list-group-flush">
                    <a href="https://www.jamb.gov.ng" target="_blank" class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                <i class="ri-global-line text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">JAMB Official Website</h6>
                                <p class="text-muted mb-0 small">Official portal for news, registration, and updates</p>
                            </div>
                        </div>
                    </a>
                    
                  
                </div>
            </div>
        </div>
    </div>
    
    <!-- Past Questions & Textbooks -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0 text-primary">
                    <i class="ri-archive-line me-2"></i> Past Questions & Textbooks
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Download past questions and recommended textbooks.</p>
                
                <div class="list-group list-group-flush">
                    <a href="https://myschoolgist.com/ng/free-jamb-past-questions-available/" target="_blank" class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                <i class="ri-file-download-line text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">MySchoolGist JAMB Past Questions</h6>
                                <p class="text-muted mb-0 small">Free downloadable past questions</p>
                            </div>
                        </div>
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
    
    <!-- Online Learning Platforms -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0 text-primary">
                    <i class="ri-video-line me-2"></i> Online Learning Platforms
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">Video lessons and interactive learning platforms.</p>
                
                <div class="list-group list-group-flush">
                    <a href="https://www.khanacademy.org" target="_blank" class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                <i class="ri-video-chat-line text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Khan Academy</h6>
                                <p class="text-muted mb-0 small">Free video lessons in Mathematics, Sciences, etc.</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="https://www.coursera.org" target="_blank" class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                <i class="ri-graduation-cap-line text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Coursera</h6>
                                <p class="text-muted mb-0 small">University-level courses (some free)</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="https://www.udemy.com/courses/search/?q=jamb" target="_blank" class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                <i class="ri-video-line text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Udemy JAMB Courses</h6>
                                <p class="text-muted mb-0 small">Paid but affordable comprehensive courses</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="https://www.youtube.com/results?search_query=jamb+tutorial" target="_blank" class="list-group-item list-group-item-action border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                <i class="ri-youtube-line text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">YouTube JAMB Tutorials</h6>
                                <p class="text-muted mb-0 small">Free video tutorials on various subjects</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subject-Specific Resources -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0 text-primary">
                    <i class="ri-book-2-line me-2"></i> Subject-Specific Resources
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Mathematics -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                    <i class="ri-calculator-line text-danger"></i>
                                </div>
                                <h6 class="mb-0">Mathematics</h6>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="https://www.mathsisfun.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Maths is Fun
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="https://www.purplemath.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Purplemath
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.wolframalpha.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Wolfram Alpha
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- English -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success  p-2 rounded me-2">
                                    <i class="ri-english-input text-primary"></i>
                                </div>
                                <h6 class="mb-0">English Language</h6>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="https://www.grammarly.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Grammarly
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="https://www.merriam-webster.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Merriam-Webster
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.cambridgeenglish.org" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Cambridge English
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Sciences -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                    <i class="ri-flask-line text-success"></i>
                                </div>
                                <h6 class="mb-0">Sciences</h6>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="https://phet.colorado.edu" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> PhET Simulations
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="https://www.ck12.org" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> CK-12 Foundation
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.sciencedaily.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Science Daily
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Social Sciences -->
                    <div class="col-md-6 col-lg-3">
                        <div class="border rounded p-3 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 p-2 rounded me-2">
                                    <i class="ri-globe-line text-info"></i>
                                </div>
                                <h6 class="mb-0">Social Sciences</h6>
                            </div>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <a href="https://www.britannica.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> Britannica
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <a href="https://www.history.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> History Channel
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.nationalgeographic.com" target="_blank" class="text-decoration-none">
                                        <i class="ri-external-link-line me-1 small"></i> National Geographic
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tips & Recommendations -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0 text-primary">
                    <i class="ri-lightbulb-line me-2"></i> Study Tips & Recommendations
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                                            <i class="ri-timer-line text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6>Time Management</h6>
                                        <p class="text-muted small mb-0">
                                            Create a study schedule. Allocate specific time for each subject and stick to it.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-success bg-opacity-10 p-2 rounded">
                                            <i class="ri-checkbox-multiple-line text-success"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6>Practice Regularly</h6>
                                        <p class="text-muted small mb-0">
                                            Solve past questions daily. Regular practice builds speed and accuracy.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                                            <i class="ri-rest-time-line text-warning"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6>Take Breaks</h6>
                                        <p class="text-muted small mb-0">
                                            Study in 45-60 minute intervals with 10-15 minute breaks to maintain focus.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="bg-info bg-opacity-10 p-2 rounded">
                                            <i class="ri-team-line text-info"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6>Group Study</h6>
                                        <p class="text-muted small mb-0">
                                            Join study groups to discuss difficult topics and learn from peers.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="bg-light rounded p-4 h-100">
                            <h6 class="mb-3">Quick Links</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('user.exams.index') }}" class="btn btn-primary">
                                    <i class="ri-play-line me-2"></i> Practice Exams
                                </a>
                                <a href="{{ route('instructions') }}" class="btn btn-outline-primary">
                                    <i class="ri-information-line me-2"></i> Exam Instructions
                                </a>
                                <a href="{{ route('user.results.index') }}" class="btn btn-outline-primary">
                                    <i class="ri-bar-chart-line me-2"></i> View Results
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disclaimer -->
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="ri-information-line display-6"></i>
                </div>
                <div>
                    <h6 class="alert-heading">Important Notice</h6>
                    <p class="mb-0">
                        These are external resources not affiliated with this platform. We provide these links for your convenience only. 
                        Always verify information from official sources. External sites may have their own terms and privacy policies.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item {
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(67, 97, 238, 0.05);
        transform: translateX(5px);
    }
    
    .subject-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .subject-card:hover {
        border-color: var(--user-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .study-tip {
        border-left: 3px solid var(--user-primary);
        padding-left: 1rem;
    }
    
    .resource-link {
        color: var(--user-primary);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .resource-link:hover {
        color: var(--user-primary-dark);
        text-decoration: underline;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click tracking for external links (optional)
    const externalLinks = document.querySelectorAll('a[target="_blank"]');
    
    externalLinks.forEach(link => {
        link.addEventListener('click', function() {
            // You can add analytics tracking here if needed
            console.log('External link clicked:', this.href);
        });
    });
    
    // Add subtle animation to cards
    const cards = document.querySelectorAll('.card, .border.rounded');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>
@endpush