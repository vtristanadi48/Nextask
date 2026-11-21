<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5f8fc">
        <title>@yield('title', 'Dashboard - Nextask')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="dashboard-page antialiased">
        <div class="dashboard-app" data-dashboard-shell>
            @include('components.dashboard-navbar')

            <div class="dashboard-body">
                @include('components.dashboard-sidebar')
                <button class="dashboard-sidebar-backdrop" type="button" data-sidebar-close aria-label="Tutup menu"></button>

                <main class="dashboard-content" id="dashboard-main" tabindex="-1">
                    @yield('content')
                </main>
            </div>
            <div class="dashboard-feature-toast" role="status" aria-live="polite" data-feature-toast hidden></div>
        </div>
    </body>
</html>
