<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:bg-forest focus:p-4 focus:text-white">Skip to content</a>

    @include('partials.nav')

    <main id="main-content" class="page-shell flex-1 py-10 sm:py-14">
        @include('partials.flash')
        @yield('content')
    </main>

    <footer class="border-t border-line py-6 text-sm text-muted">
        <div class="page-shell flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <p>A local guide to Davao’s everyday tables.</p>
            <p>Ej F. Liu · {{ now()->year }}</p>
        </div>
    </footer>
</body>
</html>
