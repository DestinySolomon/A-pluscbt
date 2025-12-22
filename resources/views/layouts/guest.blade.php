<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Dynamic Settings --}}
    @php
        $favicon = App\Models\Setting::get('favicon');
        $siteName = App\Models\Setting::get('site_name', 'A-plus CBT');
        $logo = App\Models\Setting::get('logo');
    @endphp

    {{-- Dynamic Favicon --}}
    @if($favicon && Storage::disk('public')->exists($favicon))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Dynamic Title --}}
    <title>@yield('title', $siteName)</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 50%, #ecfdf5 100%);
            min-height: 100vh;
        }
        
        .guest-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .guest-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .guest-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .guest-logo h4 {
            color: #14b8a6;
            font-weight: 700;
            margin-top: 1rem;
        }

        .guest-logo img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="guest-container">
        <div class="guest-card">
            <div class="guest-logo">
                {{-- Dynamic Logo --}}
                @if($logo && Storage::disk('public')->exists($logo))
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}">
                @else
                    <div style="width: 60px; height: 60px; background: #14b8a6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px; margin: 0 auto;">
                        A+
                    </div>
                @endif
                <h4 class="mt-3">{{ $siteName }}</h4>
            </div>
            
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>