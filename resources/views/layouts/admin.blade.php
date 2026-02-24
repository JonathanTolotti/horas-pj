<!DOCTYPE html>
<html lang="pt-BR" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin — Controle de Horas PJ</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
            .hidden { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-950 text-gray-100">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

            <!-- Overlay mobile -->
            <div
                x-show="sidebarOpen"
                x-cloak
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black/60 lg:hidden"
            ></div>

            <!-- Sidebar -->
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 border-r border-gray-800 flex flex-col transition-transform duration-200 lg:relative lg:translate-x-0 lg:flex"
            >
                <!-- Logo / Brand -->
                <div class="flex items-center gap-3 px-5 h-16 border-b border-gray-800 shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Horas PJ" class="h-9 w-auto">
                    </a>
                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-cyan-600 text-white tracking-wide">
                        Admin
                    </span>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-600/20 text-cyan-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.users*') ? 'bg-cyan-600/20 text-cyan-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Usuários
                    </a>
                    <a href="{{ route('admin.changelogs.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('admin.changelogs*') ? 'bg-cyan-600/20 text-cyan-400' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Changelog
                    </a>
                </nav>

                <!-- Footer Sidebar -->
                <div class="p-4 border-t border-gray-800 shrink-0">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Voltar ao App
                    </a>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Bar -->
                <header class="h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-4 lg:px-6 shrink-0">
                    <!-- Hamburger mobile -->
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="hidden lg:block">
                        <h2 class="text-sm text-gray-400">
                            Painel Administrativo
                        </h2>
                    </div>

                    <div class="flex items-center gap-3 ml-auto">
                        <span class="text-sm text-gray-400">{{ Auth::user()->name }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded bg-cyan-600 text-white">
                            Admin
                        </span>
                    </div>
                </header>

                <!-- Page -->
                <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Toast Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

        <!-- Global JS -->
        <script>
            const CSRF_TOKEN = '{{ csrf_token() }}';

            const TOAST_TYPES = { SUCCESS: 'success', ERROR: 'error', WARNING: 'warning', INFO: 'info' };

            function escapeHtml(text) {
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }

            function showToast(message, type = TOAST_TYPES.INFO, duration = 4000) {
                const container = document.getElementById('toast-container');
                if (!container) return;
                const styles = {
                    success: { bg: '#064e3b', border: '#10b981', text: '#a7f3d0' },
                    error:   { bg: '#7f1d1d', border: '#ef4444', text: '#fecaca' },
                    warning: { bg: '#78350f', border: '#f59e0b', text: '#fde68a' },
                    info:    { bg: '#164e63', border: '#06b6d4', text: '#a5f3fc' },
                };
                const s = styles[type] || styles.info;
                const toast = document.createElement('div');
                toast.style.cssText = 'pointer-events:auto;transform:translateX(100%);opacity:0;transition:all 0.3s ease-out;';
                toast.innerHTML = `<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:10px;border:2px solid ${s.border};background:${s.bg};color:${s.text};box-shadow:0 10px 25px rgba(0,0,0,0.4);font-size:14px;font-weight:500;min-width:280px;max-width:400px;">
                    <span style="flex:1">${escapeHtml(message)}</span>
                    <button onclick="this.closest('div').parentElement.remove()" style="background:none;border:none;cursor:pointer;opacity:0.7;color:${s.text};padding:4px;">✕</button>
                </div>`;
                container.appendChild(toast);
                requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; });
                setTimeout(() => { toast.style.transform = 'translateX(100%)'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, duration);
            }
        </script>

        @stack('scripts')
    </body>
</html>
