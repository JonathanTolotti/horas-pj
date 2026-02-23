<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Controle de Horas PJ</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Flatpickr for date/time pickers with pt-BR locale -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

        <script>
            // Apply privacy mode immediately to prevent flash of sensitive content
            if (localStorage.getItem('privacy_mode') === 'true') {
                document.documentElement.classList.add('privacy-mode');
            }
            // Apply view mode immediately to prevent flash
            if (localStorage.getItem('view_mode') === 'daily') {
                document.documentElement.classList.add('view-mode-daily');
            }
            // Mark desktop viewport so nav is shown immediately (before Tailwind loads)
            if (window.innerWidth >= 640) {
                document.documentElement.classList.add('is-sm');
            }
        </script>
        <style>
            /* Hide elements with x-cloak until Alpine.js initializes */
            [x-cloak] {
                display: none !important;
            }

            body {
                font-family: 'Inter', sans-serif;
            }
            /* Flatpickr dark theme customization */
            .flatpickr-calendar {
                background: #1f2937 !important;
                border-color: #374151 !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
            }
            .flatpickr-months .flatpickr-month,
            .flatpickr-current-month .flatpickr-monthDropdown-months,
            .flatpickr-weekdays,
            span.flatpickr-weekday {
                background: #1f2937 !important;
                color: #9ca3af !important;
            }
            .flatpickr-current-month input.cur-year,
            .flatpickr-current-month .numInputWrapper span {
                color: #fff !important;
            }
            .flatpickr-day {
                color: #e5e7eb !important;
            }
            .flatpickr-day:hover {
                background: #374151 !important;
                border-color: #374151 !important;
            }
            .flatpickr-day.selected {
                background: #06b6d4 !important;
                border-color: #06b6d4 !important;
                color: #fff !important;
            }
            .flatpickr-day.today {
                border-color: #06b6d4 !important;
            }
            .flatpickr-months .flatpickr-prev-month,
            .flatpickr-months .flatpickr-next-month {
                color: #9ca3af !important;
                fill: #9ca3af !important;
            }
            .flatpickr-months .flatpickr-prev-month:hover svg,
            .flatpickr-months .flatpickr-next-month:hover svg {
                fill: #06b6d4 !important;
            }
            .flatpickr-day.flatpickr-disabled {
                color: #4b5563 !important;
            }
            /* Date picker input styling */
            .flatpickr-input {
                cursor: pointer !important;
            }
            /* Privacy mode - blur sensitive values */
            html.privacy-mode .sensitive-value {
                filter: blur(8px);
                user-select: none;
                transition: filter 0.2s ease;
            }
            html.privacy-mode .sensitive-value:hover {
                filter: blur(4px);
            }
            /* View mode - prevent flash on page load */
            html.view-mode-daily #view-entries {
                display: none !important;
            }
            html.view-mode-daily #view-daily {
                display: block !important;
            }
            /* View mode buttons - sync with view state */
            html.view-mode-daily #view-entries-btn {
                background-color: transparent !important;
                color: #9ca3af !important;
            }
            html.view-mode-daily #view-daily-btn {
                background-color: #0891b2 !important;
                color: white !important;
            }
            /* Critical: prevent FOUC caused by Vite dev mode injecting CSS via JS.
               Before the Tailwind bundle loads, class="hidden" has no effect,
               so hidden elements (like dropdowns) briefly flash. */
            .hidden { display: none !important; }
            /* Restore Tailwind responsive display utilities overridden by .hidden above */
            @media (min-width: 640px) {
                .sm\:block { display: block !important; }
                .sm\:flex { display: flex !important; }
                .sm\:inline { display: inline !important; }
                .sm\:inline-block { display: inline-block !important; }
                .sm\:grid { display: grid !important; }
                .sm\:hidden { display: none !important; }
            }
            @media (min-width: 768px) {
                .md\:block { display: block !important; }
                .md\:flex { display: flex !important; }
                .md\:inline { display: inline !important; }
                .md\:inline-block { display: inline-block !important; }
                .md\:grid { display: grid !important; }
                .md\:hidden { display: none !important; }
            }
            @media (min-width: 1024px) {
                .lg\:block { display: block !important; }
                .lg\:flex { display: flex !important; }
                .lg\:inline { display: inline !important; }
                .lg\:inline-block { display: inline-block !important; }
                .lg\:grid { display: grid !important; }
                .lg\:hidden { display: none !important; }
            }
            @media (min-width: 1280px) {
                .xl\:block { display: block !important; }
                .xl\:flex { display: flex !important; }
                .xl\:inline { display: inline !important; }
                .xl\:inline-block { display: inline-block !important; }
                .xl\:grid { display: grid !important; }
                .xl\:hidden { display: none !important; }
            }
            /* Nav visibility controlled by JS-set class (same pattern as privacy-mode).
               The script in <head> runs before body renders, so is-sm is set
               before any nav element is painted. No dependency on Tailwind loading. */
            .nav-desktop { display: none; }
            .nav-right    { display: none; }
            html.is-sm .nav-desktop { display: flex; }
            html.is-sm .nav-right   { display: flex; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-950 text-gray-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global Modals -->
        @auth
            @include('imports.modal')
            <x-premium-modal feature="esta funcionalidade" />
            @include('partials.changelog-modal')
        @endauth

        <!-- Global JS Variables -->
        <script>
            const CSRF_TOKEN = '{{ csrf_token() }}';
        </script>

        <!-- Global Import Functions -->
        <script src="{{ asset('js/import.js') }}?v={{ @filemtime(public_path('js/import.js')) ?: time() }}"></script>

        @stack('scripts')
    </body>
</html>
