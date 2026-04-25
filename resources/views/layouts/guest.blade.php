<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Controle de Horas PJ</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : 1 }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if(config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
        @endif

        <script>
            if (localStorage.getItem('theme') !== 'light') {
                document.documentElement.classList.add('dark');
            }
        </script>
        <style>
            /* O badge do reCAPTCHA é ocultado — aviso exibido nos formulários conforme política do Google */
            .grecaptcha-badge { visibility: hidden !important; }
        </style>
        <style>
            body { font-family: 'Inter', sans-serif; }

            .auth-gradient-text {
                background: linear-gradient(135deg, #22d3ee, #10b981);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .auth-glow {
                position: absolute;
                width: 560px;
                height: 560px;
                background: radial-gradient(circle, rgba(34,211,238,0.10) 0%, transparent 70%);
                top: 40%;
                left: 50%;
                transform: translate(-50%, -50%);
                pointer-events: none;
            }

            html:not(.dark) .auth-left-panel { background: #f1f5f9; }
            html:not(.dark) .auth-gradient-text {
                background: linear-gradient(135deg, #0e7490, #047857);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            html:not(.dark) .auth-glow { display: none; }
            html:not(.dark) .auth-feature-icon { background: rgba(8,145,178,0.1); border-color: rgba(8,145,178,0.2); }
            html:not(.dark) .auth-feature-icon svg { color: #0e7490; }
            html:not(.dark) .auth-feature-text { color: #334155; }
            html:not(.dark) .auth-tagline { color: #475569; }
            html:not(.dark) .auth-headline { color: #0f172a; }
            html:not(.dark) .auth-back-link { color: #64748b; }
            html:not(.dark) .auth-back-link:hover { color: #0f172a; }
            html:not(.dark) .auth-copyright { color: #94a3b8; }
            html:not(.dark) .auth-right-panel { background: #f8fafc; }
            html:not(.dark) .auth-panel-fade { background: linear-gradient(to right, transparent, #f8fafc); }
            html.dark .auth-panel-fade { background: linear-gradient(to right, transparent, #05091a); }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex dark:bg-[#020817]">

            {{-- ── LEFT PANEL (desktop only) ─────────────────────────────── --}}
            <div class="auth-left-panel hidden lg:flex lg:w-[58%] xl:w-[46%] flex-col relative overflow-hidden dark:bg-[#020817]">
                <div class="auth-glow"></div>

                {{-- Back link --}}
                <div class="relative z-10 p-8">
                    <a href="{{ route('landing') }}" class="auth-back-link inline-flex items-center gap-2 dark:text-slate-400 dark:hover:text-white transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Página inicial
                    </a>
                </div>

                {{-- Center content --}}
                <div class="flex-1 flex flex-col items-center justify-center px-12 xl:px-16 relative z-10 text-center">
                    <img src="{{ asset('images/logo.png?v=' . filemtime(public_path('images/logo.png'))) }}"
                         alt="Horas PJ" class="h-16 w-auto mb-10">

                    <h1 class="auth-headline text-4xl xl:text-[2.75rem] font-black leading-tight tracking-tight dark:text-white mb-4">
                        Controle suas horas<br>
                        <span class="auth-gradient-text">como um profissional</span>
                    </h1>

                    <p class="auth-tagline dark:text-slate-400 text-base mb-10 leading-relaxed max-w-sm">
                        Para profissionais PJ e autônomos que precisam de controle real sobre horas e receita.
                    </p>

                    <ul class="space-y-4 text-left">
                        <li class="flex items-center gap-3">
                            <div class="auth-feature-icon w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="auth-feature-text dark:text-slate-300 text-sm">Tracking automático de horas</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="auth-feature-icon w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span class="auth-feature-text dark:text-slate-300 text-sm">Relatórios completos e exportação PDF/Excel</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="auth-feature-icon w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="auth-feature-text dark:text-slate-300 text-sm">7 dias grátis de Premium — sem cartão de crédito</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="auth-feature-icon w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <span class="auth-feature-text dark:text-slate-300 text-sm">Autenticação segura com verificação em 2 etapas</span>
                        </li>
                    </ul>
                </div>

                {{-- Gradient fade toward right panel --}}
                <div class="auth-panel-fade absolute right-0 top-0 h-full w-28 pointer-events-none z-20"></div>

                {{-- Footer --}}
                <div class="relative z-10 p-8 text-center">
                    <p class="auth-copyright dark:text-slate-700 text-xs">&copy; {{ date('Y') }} Controle de Horas PJ</p>
                </div>
            </div>

            {{-- ── RIGHT PANEL (form) ───────────────────────────────────── --}}
            <div class="auth-right-panel flex-1 flex flex-col items-center justify-center px-6 py-10 dark:bg-[#05091a]">

                {{-- Mobile logo --}}
                <div class="lg:hidden mb-8">
                    <a href="{{ route('landing') }}">
                        <img src="{{ asset('images/logo.png?v=' . filemtime(public_path('images/logo.png'))) }}"
                             alt="Horas PJ" class="h-14 w-auto">
                    </a>
                </div>

                <div class="w-full max-w-md">
                    <div class="dark:bg-white/[0.03] dark:border-white/[0.08] bg-white border border-slate-200 rounded-2xl px-8 py-8 shadow-2xl dark:shadow-black/40">
                        {{ $slot }}
                    </div>
                </div>
            </div>

        </div>

        @stack('scripts')
    </body>
</html>
