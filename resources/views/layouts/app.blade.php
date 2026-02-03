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

        <style>
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
            .flatpickr-time {
                background: #1f2937 !important;
                border-color: #374151 !important;
            }
            .flatpickr-time input,
            .flatpickr-time .flatpickr-am-pm {
                color: #fff !important;
            }
            .flatpickr-time input:hover,
            .flatpickr-time .flatpickr-am-pm:hover {
                background: #374151 !important;
            }
            .numInputWrapper:hover {
                background: #374151 !important;
            }
            .numInputWrapper span {
                border-color: #4b5563 !important;
            }
            .numInputWrapper span:hover {
                background: #4b5563 !important;
            }
            .flatpickr-day.flatpickr-disabled {
                color: #4b5563 !important;
            }
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

        @stack('scripts')
    </body>
</html>
