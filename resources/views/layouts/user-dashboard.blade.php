<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Dynamic Favicon --}}
    @php
        $favicon = App\Models\Setting::get('favicon');
        $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
    @endphp
    
    @if($favicon && Storage::disk('public')->exists($favicon))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <title>@yield('title', 'Student Dashboard') - {{ $siteName }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
    <!-- Custom User CSS -->
    <link href="{{ asset('assets/css/user-dashboard.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <!-- User Dashboard Wrapper -->
    <div class="user-dashboard-wrapper">
        <!-- Sidebar -->
        @include('partials.user-sidebar')
        
        <!-- Main Content -->
        <main class="user-dashboard-main">
            <!-- Top Navigation -->
            @include('partials.user-topnav')
            
            <!-- Content Area -->
            <div class="user-content-area">
                <div class="container-fluid py-4">
                    <!-- Page Header -->
                    <div class="page-header mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title mb-1">@yield('page-title', 'Dashboard')</h1>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                        @yield('breadcrumbs')
                                    </ol>
                                </nav>
                            </div>
                            <div class="page-actions">
                                @yield('page-actions')
                            </div>
                        </div>
                    </div>
                    
                    <!-- JAMB Timer (Only during exams) -->
                    <div class="jamb-timer-container d-none" id="examTimerContainer">
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="ri-time-line me-2"></i>
                                    <strong>Exam in Progress</strong>
                                    <span class="ms-2">Time Remaining:</span>
                                </div>
                                <div class="jamb-timer">
                                    <span class="timer-hours">00</span>:<span class="timer-minutes">00</span>:<span class="timer-seconds">00</span>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="endExamBtn">
                                        <i class="ri-stop-circle-line me-1"></i> End Exam
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri-checkbox-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri-error-warning-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Main Content -->
                    <div class="main-content">
                        @yield('content')
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="user-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center py-3">
                            <p class="mb-0 text-muted">
                                &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
    
    <!-- CRITICAL: jQuery MUST be loaded FIRST -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- THEN Bootstrap JS (depends on jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- FINALLY Custom User JS -->
    <script src="{{ asset('assets/js/user-dashboard.js') }}"></script>
    
    @stack('scripts')
</body>
</html>