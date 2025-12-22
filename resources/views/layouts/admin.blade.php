<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
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
    
    {{-- Dynamic Title --}}
    <title>@yield('title', 'Admin Dashboard') - {{ $siteName }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Admin CSS -->
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <!-- Admin Wrapper -->
    <div class="admin-wrapper">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Navigation -->
            @include('admin.partials.topnav')
            
            <!-- Main Content Area -->
            <main class="admin-content">
                <div class="container-fluid py-4">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-0">@yield('page-title')</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    @yield('breadcrumbs')
                                </ol>
                            </nav>
                        </div>
                        <div>
                            @yield('page-actions')
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
                    
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
            
            <!-- Footer -->
            @include('admin.partials.footer')
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

     <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    
    @stack('scripts')

    <!-- Floating Home Button to Public Site -->
    <a href="{{ url('/') }}" class="floating-home-btn" title="Back to Home Page">
        <i class="ri-home-line"></i>
    </a>
</body>
</html>