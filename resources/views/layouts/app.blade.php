<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Dynamic Settings --}}
        @php
            $favicon = App\Models\Setting::get('favicon');
            $siteName = App\Models\Setting::get('site_name', config('app.name', 'A-plus CBT'));
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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Remixicon Icons -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">

        @stack('styles')
    </head>
    <body>
        @include('components.navbar')

        <main>
            @yield('content')
        </main>

        @include('components.footer')

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom JS -->
        <script src="{{ asset('assets/js/app.js') }}"></script>

        @stack('scripts')
    </body>
</html>