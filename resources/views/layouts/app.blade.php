<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Raven Travel - Perjalanan Elegan & Nyaman')</title>
        @hasSection('meta')
            @yield('meta')
        @endif
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&family=Cinzel:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
        @stack('styles')

        {{-- Always load asset files directly (bypass Vite during development) --}}
        <link rel="stylesheet" href="{{ asset('assets/raven-travel.css') }}">
    </head>
    <body>
        @yield('body')

        {{-- Load JS from assets directly --}}
        <script src="{{ asset('assets/raven-travel.js') }}" defer></script>
        @stack('scripts')
    </body>
</html>
