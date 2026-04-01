<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
            // Apply theme immediately to prevent flash (dark is default)
            if (localStorage.getItem('theme') !== 'light') {
                document.documentElement.classList.add('dark');
            }
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
            /* Flatpickr dark theme customization (only in dark mode) */
            html.dark .flatpickr-calendar {
                background: #1f2937 !important;
                border-color: #374151 !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
            }
            html.dark .flatpickr-months .flatpickr-month,
            html.dark .flatpickr-current-month .flatpickr-monthDropdown-months,
            html.dark .flatpickr-weekdays,
            html.dark span.flatpickr-weekday {
                background: #1f2937 !important;
                color: #9ca3af !important;
            }
            html.dark .flatpickr-current-month input.cur-year,
            html.dark .flatpickr-current-month .numInputWrapper span {
                color: #fff !important;
            }
            html.dark .flatpickr-day {
                color: #e5e7eb !important;
            }
            html.dark .flatpickr-day:hover {
                background: #374151 !important;
                border-color: #374151 !important;
            }
            html.dark .flatpickr-day.selected {
                background: #06b6d4 !important;
                border-color: #06b6d4 !important;
                color: #fff !important;
            }
            html.dark .flatpickr-day.today {
                border-color: #06b6d4 !important;
            }
            html.dark .flatpickr-months .flatpickr-prev-month,
            html.dark .flatpickr-months .flatpickr-next-month {
                color: #9ca3af !important;
                fill: #9ca3af !important;
            }
            html.dark .flatpickr-months .flatpickr-prev-month:hover svg,
            html.dark .flatpickr-months .flatpickr-next-month:hover svg {
                fill: #06b6d4 !important;
            }
            html.dark .flatpickr-day.flatpickr-disabled {
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

        <!-- Modal de Confirmação Global -->
        <div id="confirm-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
            <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-red-500/20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" id="confirm-title">Confirmar</h3>
                </div>
                <p class="text-gray-400 mb-6" id="confirm-message">Deseja realmente realizar esta ação?</p>
                <div class="flex gap-3 justify-end">
                    <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button id="confirm-btn" onclick="executeConfirm()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>

        <!-- Global JS Variables -->
        <script>
            const CSRF_TOKEN = '{{ csrf_token() }}';

            // ==================== CONFIRM MODAL (global) ====================
            let confirmCallback = null;

            function showConfirm(message, callback, title = 'Confirmar', btnLabel = 'Confirmar') {
                const modal = document.getElementById('confirm-modal');
                if (!modal) { if (window.confirm(message)) callback(); return; }
                document.getElementById('confirm-title').textContent = title;
                document.getElementById('confirm-message').textContent = message;
                document.getElementById('confirm-btn').textContent = btnLabel;
                confirmCallback = callback;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function executeConfirm() {
                const cb = confirmCallback;
                closeConfirmModal();
                if (cb) cb();
            }

            function closeConfirmModal() {
                const modal = document.getElementById('confirm-modal');
                if (modal) { modal.classList.add('hidden'); document.body.style.overflow = ''; }
                confirmCallback = null;
            }

            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeConfirmModal(); });

            function toggleTheme() {
                const html = document.documentElement;
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }

            // ==================== TOAST SYSTEM (global) ====================
            const TOAST_TYPES = { SUCCESS: 'success', ERROR: 'error', WARNING: 'warning', INFO: 'info' };

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function showToast(message, type = TOAST_TYPES.INFO, duration = 4000) {
                const container = document.getElementById('toast-container');
                if (!container) return;
                const toast = document.createElement('div');
                toast.className = 'toast-item';
                toast.style.cssText = 'transform:translateX(100%);opacity:0;transition:all 0.3s ease-out;';
                const isDark = document.documentElement.classList.contains('dark');
                const styles = isDark ? {
                    success: { bg: '#064e3b', border: '#10b981', text: '#a7f3d0', icon: '#34d399' },
                    error:   { bg: '#7f1d1d', border: '#ef4444', text: '#fecaca', icon: '#f87171' },
                    warning: { bg: '#78350f', border: '#f59e0b', text: '#fde68a', icon: '#fbbf24' },
                    info:    { bg: '#164e63', border: '#06b6d4', text: '#a5f3fc', icon: '#22d3ee' }
                } : {
                    success: { bg: '#d1fae5', border: '#059669', text: '#064e3b', icon: '#059669' },
                    error:   { bg: '#fee2e2', border: '#dc2626', text: '#7f1d1d', icon: '#dc2626' },
                    warning: { bg: '#fef3c7', border: '#d97706', text: '#78350f', icon: '#d97706' },
                    info:    { bg: '#cffafe', border: '#0891b2', text: '#164e63', icon: '#0891b2' }
                };
                const style = styles[type] || styles.info;
                const icons = {
                    success: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
                    error:   `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
                    warning: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
                    info:    `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
                };
                toast.innerHTML = `<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:10px;border:2px solid ${style.border};background:${style.bg};color:${style.text};box-shadow:0 10px 25px rgba(0,0,0,${isDark ? '0.4' : '0.12'}),0 0 20px ${style.border}40;font-size:14px;font-weight:500;min-width:280px;max-width:400px;">${icons[type] || icons.info}<span style="flex:1">${escapeHtml(message)}</span><button onclick="this.closest('.toast-item').remove()" style="background:none;border:none;cursor:pointer;opacity:0.7;transition:opacity 0.2s;padding:4px;color:${style.text}" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>`;
                container.appendChild(toast);
                requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; });
                setTimeout(() => { toast.style.transform = 'translateX(100%)'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, duration);
            }
        </script>

        <!-- Global Import Functions -->
        <script src="{{ asset('js/import.js') }}?v={{ @filemtime(public_path('js/import.js')) ?: time() }}"></script>

        @stack('scripts')
    </body>
</html>
