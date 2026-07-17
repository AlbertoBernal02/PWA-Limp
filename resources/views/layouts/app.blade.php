<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <script>
            // Aplicado antes de pintar para evitar parpadeo del tema incorrecto.
            if (localStorage.getItem('tema') === 'oscuro') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- PWA -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#158c78">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        <link rel="icon" href="{{ asset('icons/favicon-32.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash messages -->
            <x-flash-messages />

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
            }
        </script>
    </body>
</html>
