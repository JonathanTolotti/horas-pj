<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Controle suas horas como PJ com precisão. Tracking automático, relatórios completos e gestão financeira em um só lugar.">
    <title>Horas PJ — Controle de Horas para Profissionais Autônomos</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : 1 }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #020817; color: #e2e8f0; }

        .gradient-text {
            background: linear-gradient(135deg, #22d3ee, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #0e7490 0%, #059669 100%);
        }

        .hero-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(34,211,238,0.12) 0%, transparent 70%);
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none;
        }

        .card-border {
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.03);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 30px rgba(34,211,238,0.25); }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 15px;
            border: 1px solid rgba(255,255,255,0.2);
            color: #e2e8f0;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            background: transparent;
        }

        .btn-outline:hover { border-color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.05); }

        .plan-card { transition: transform 0.2s, box-shadow 0.2s; }
        .plan-card:hover { transform: translateY(-4px); }
        .plan-card.featured { border-color: #22d3ee; }

        .nav-blur {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(2,8,23,0.8);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        .float-anim { animation: float 4s ease-in-out infinite; }

        .stat-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 28px;
            text-align: center;
        }

        input:focus { outline: none; }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="nav-blur fixed top-0 left-0 right-0 z-50">
    <div class="max-w-6xl mx-auto px-6 py-2 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png?v=' . filemtime(public_path('images/logo.png'))) }}" alt="Horas PJ" class="h-12 w-auto">
        </div>
        <div class="hidden md:flex items-center gap-8">
            <a href="#funcionalidades" class="text-sm text-slate-400 hover:text-white transition-colors">Funcionalidades</a>
            <a href="#api" class="text-sm text-slate-400 hover:text-white transition-colors">API</a>
            <a href="#planos" class="text-sm text-slate-400 hover:text-white transition-colors">Planos</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="btn-outline text-sm py-2 px-5">Entrar</a>
            <a href="{{ route('register') }}" class="btn-primary gradient-bg text-white text-sm py-2 px-5">Começar grátis</a>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="relative pt-36 pb-24 px-6 overflow-hidden">
    <div class="hero-glow"></div>
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <div class="inline-flex items-center gap-2 bg-cyan-500/10 border border-cyan-500/20 rounded-full px-4 py-1.5 mb-8">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
            <span class="text-cyan-400 text-sm font-medium">Para profissionais PJ e autônomos</span>
        </div>

        <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight mb-6">
            Controle suas<br>
            <span class="gradient-text">horas e receita</span><br>
            como um profissional
        </h1>

        <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            Tracking automático, relatórios completos, cálculo de sobreaviso e muito mais.
            Feito para quem trabalha como PJ e precisa de controle real.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="btn-primary gradient-bg text-white text-base w-full sm:w-auto">
                Criar conta gratuita
                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="{{ route('login') }}" class="btn-outline text-base w-full sm:w-auto">
                Já tenho conta
            </a>
        </div>

        <p class="text-slate-500 text-sm mt-6">Sem cartão de crédito. Comece grátis hoje mesmo.</p>
    </div>

    {{-- HERO MOCKUP --}}
    <div class="max-w-5xl mx-auto mt-20 relative z-10">
        <div class="float-anim">
            <div class="card-border rounded-2xl overflow-hidden shadow-2xl" style="box-shadow: 0 0 80px rgba(34,211,238,0.08);">
                <div class="bg-slate-800/60 px-5 py-3 flex items-center gap-2 border-b border-white/5">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                    <span class="text-slate-500 text-xs ml-3">app.horaspj.com.br/dashboard</span>
                </div>
                <div class="bg-gray-950/80 p-6 md:p-8">
                    {{-- Simulated Dashboard --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-900/80 rounded-xl p-4 border border-white/5">
                            <p class="text-slate-500 text-xs mb-1">Horas no mês</p>
                            <p class="text-2xl font-bold text-white">168:30</p>
                            <p class="text-emerald-400 text-xs mt-1">+12% vs mês ant.</p>
                        </div>
                        <div class="bg-gray-900/80 rounded-xl p-4 border border-white/5">
                            <p class="text-slate-500 text-xs mb-1">Receita bruta</p>
                            <p class="text-2xl font-bold text-white">R$ 16.850</p>
                            <p class="text-emerald-400 text-xs mt-1">R$ 100,00/h</p>
                        </div>
                        <div class="bg-gray-900/80 rounded-xl p-4 border border-white/5">
                            <p class="text-slate-500 text-xs mb-1">Sobreaviso</p>
                            <p class="text-2xl font-bold text-white">48:00</p>
                            <p class="text-cyan-400 text-xs mt-1">R$ 1.600</p>
                        </div>
                        <div class="bg-gray-900/80 rounded-xl p-4 border border-white/5">
                            <p class="text-slate-500 text-xs mb-1">Total final</p>
                            <p class="text-2xl font-bold text-emerald-400">R$ 18.450</p>
                            <p class="text-slate-500 text-xs mt-1">Após ajustes</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach([
                            ['Desenvolvimento de API', '09:00 — 17:30', '8:30', 'Projeto Alpha'],
                            ['Reunião de planejamento', '10:00 — 11:30', '1:30', 'Projeto Beta'],
                            ['Code review e testes', '14:00 — 18:00', '4:00', 'Projeto Alpha'],
                        ] as $entry)
                        <div class="bg-gray-900/50 rounded-lg px-4 py-3 flex items-center justify-between border border-white/4">
                            <div>
                                <p class="text-sm text-slate-200 font-medium">{{ $entry[0] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $entry[3] }} · {{ $entry[1] }}</p>
                            </div>
                            <span class="text-cyan-400 font-mono font-semibold text-sm">{{ $entry[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="py-16 px-6 border-y border-white/5">
    <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="stat-card">
            <p class="text-3xl font-black gradient-text">100%</p>
            <p class="text-slate-400 text-sm mt-1">Controle do seu tempo</p>
        </div>
        <div class="stat-card">
            <p class="text-3xl font-black gradient-text">0 R$</p>
            <p class="text-slate-400 text-sm mt-1">Para começar</p>
        </div>
        <div class="stat-card">
            <p class="text-3xl font-black gradient-text">+12</p>
            <p class="text-slate-400 text-sm mt-1">Tipos de relatório</p>
        </div>
        <div class="stat-card">
            <p class="text-3xl font-black gradient-text">1 min</p>
            <p class="text-slate-400 text-sm mt-1">Para configurar</p>
        </div>
    </div>
</section>

{{-- FUNCIONALIDADES --}}
<section id="funcionalidades" class="py-24 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4">Tudo que você precisa<br><span class="gradient-text">em um só lugar</span></h2>
            <p class="text-slate-400 text-lg max-w-xl mx-auto">Desenvolvido especificamente para profissionais PJ que precisam de controle real sobre seu tempo e receita.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="card-border rounded-2xl p-6">
                <div class="feature-icon bg-cyan-500/15 mb-4">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Tracking Automático</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Inicie o timer e pare quando terminar. O lançamento é criado automaticamente com as horas calculadas ao segundo.</p>
            </div>

            <div class="card-border rounded-2xl p-6">
                <div class="feature-icon bg-emerald-500/15 mb-4">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Relatórios Completos</h3>
                <p class="text-slate-400 text-sm leading-relaxed">PDFs profissionais mensais, anuais e por empresa. Prontos para enviar ao seu cliente ou contador.</p>
            </div>

            <div class="card-border rounded-2xl p-6">
                <div class="feature-icon bg-cyan-500/15 mb-4">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Cálculo Financeiro</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Configure seu valor por hora, extras e descontos. O sistema calcula automaticamente sua receita do mês.</p>
            </div>

            <div class="card-border rounded-2xl p-6">
                <div class="feature-icon bg-yellow-500/15 mb-4">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Controle de Sobreaviso</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Cadastre períodos de sobreaviso com valor diferenciado. O sistema deduz automaticamente as horas efetivamente trabalhadas.</p>
            </div>

            <div class="card-border rounded-2xl p-6">
                <div class="feature-icon bg-rose-500/15 mb-4">
                    <svg class="w-6 h-6 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Múltiplas Empresas</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Trabalha com mais de um cliente? Gerencie projetos e empresas separadamente, com relatórios individuais por CNPJ.</p>
            </div>

            <div class="card-border rounded-2xl p-6">
                <div class="feature-icon bg-sky-500/15 mb-4">
                    <svg class="w-6 h-6 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Importação CSV</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Já tem lançamentos em planilha? Importe em segundos via CSV com preview antes de confirmar.</p>
            </div>

            <div class="card-border rounded-2xl p-6" style="border-color: rgba(34,211,238,0.15); background: rgba(34,211,238,0.02);">
                <div class="feature-icon bg-cyan-500/15 mb-4">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">API REST</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Integre o Horas PJ com qualquer ferramenta via API REST. Lançamentos, projetos, tracking e mais — totalmente programável.</p>
                <a href="{{ route('api-docs') }}" class="inline-flex items-center gap-1.5 mt-4 text-sm text-cyan-400 hover:text-cyan-300 transition-colors">
                    Ver documentação
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

        </div>
    </div>
</section>

{{-- COMO FUNCIONA --}}
<section class="py-24 px-6 border-y border-white/5" style="background: rgba(255,255,255,0.01)">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4">Como <span class="gradient-text">funciona</span></h2>
            <p class="text-slate-400 text-lg">Três passos para ter controle total do seu tempo</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl gradient-bg flex items-center justify-center mx-auto mb-5 text-white font-black text-xl shadow-lg" style="box-shadow: 0 4px 20px rgba(34,211,238,0.3)">1</div>
                <h3 class="text-lg font-bold text-white mb-2">Configure</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Defina seu valor/hora, projetos e empresas em poucos minutos. Sem burocracia.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl gradient-bg flex items-center justify-center mx-auto mb-5 text-white font-black text-xl shadow-lg" style="box-shadow: 0 4px 20px rgba(34,211,238,0.3)">2</div>
                <h3 class="text-lg font-bold text-white mb-2">Lance suas horas</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Use o tracking automático ou cadastre manualmente. Funciona do jeito que você preferir.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl gradient-bg flex items-center justify-center mx-auto mb-5 text-white font-black text-xl shadow-lg" style="box-shadow: 0 4px 20px rgba(34,211,238,0.3)">3</div>
                <h3 class="text-lg font-bold text-white mb-2">Exporte e fature</h3>
                <p class="text-slate-400 text-sm leading-relaxed">Gere relatórios PDF profissionais prontos para enviar ao cliente ou emitir NF.</p>
            </div>
        </div>
    </div>
</section>

{{-- API --}}
<section id="api" class="py-24 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 bg-cyan-500/10 border border-cyan-500/20 rounded-full px-4 py-1.5 mb-6">
                <svg class="w-4 h-4 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                <span class="text-cyan-400 text-sm font-medium">Para desenvolvedores</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-black mb-4">
                API REST
                <span style="background: linear-gradient(135deg, #22d3ee, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">totalmente aberta</span>
            </h2>
            <p class="text-slate-400 text-lg max-w-2xl mx-auto">Integre o Horas PJ com qualquer ferramenta, automação ou script. Autenticação por Bearer Token, permissões granulares e documentação completa.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Features --}}
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="feature-icon bg-cyan-500/15 shrink-0">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold mb-1">Autenticação por Bearer Token</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Gere tokens de acesso pessoal nas configurações. Revogue a qualquer momento sem afetar o restante da conta.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="feature-icon bg-cyan-500/15 shrink-0">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold mb-1">Permissões Granulares</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Defina exatamente o que cada token pode fazer: leitura, escrita, tracking, sobreaviso — controle total por recurso.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="feature-icon bg-cyan-500/15 shrink-0">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold mb-1">Endpoints Completos</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Lançamentos, projetos, empresas, configurações, tracking e sobreaviso — tudo o que existe no app está disponível via API.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="feature-icon bg-cyan-500/15 shrink-0">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold mb-1">Documentação Interativa</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Exemplos de requisição e resposta para cada endpoint, com parâmetros, códigos de erro e exemplos em cURL.</p>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('api-docs') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm text-white transition-all hover:opacity-90" style="background: linear-gradient(135deg, #0e7490, #059669); box-shadow: 0 4px 20px rgba(34,211,238,0.25);">
                        Ver documentação completa
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>

            {{-- Code preview --}}
            <div class="card-border rounded-2xl overflow-hidden" style="border-color: rgba(34,211,238,0.15);">
                <div class="px-5 py-3 flex items-center gap-2 border-b" style="background: rgba(34,211,238,0.05); border-color: rgba(34,211,238,0.15);">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                    <span class="text-slate-500 text-xs ml-3 font-mono">terminal</span>
                </div>
                <div class="p-6 font-mono text-sm space-y-4" style="background: rgba(10,5,20,0.9);">
                    <div>
                        <p class="text-slate-500 text-xs mb-2"># Listar lançamentos do mês</p>
                        <p><span class="text-cyan-400">curl</span> <span class="text-slate-300">-X GET \</span></p>
                        <p class="pl-4"><span class="text-emerald-400">"https://app.horaspj.com.br/api/v1/time-entries?month=2025-05"</span> <span class="text-slate-300">\</span></p>
                        <p class="pl-4"><span class="text-sky-400">-H</span> <span class="text-amber-300">"Authorization: Bearer SEU_TOKEN"</span></p>
                    </div>
                    <div class="border-t border-white/5 pt-4">
                        <p class="text-slate-500 text-xs mb-2"># Resposta</p>
                        <p><span class="text-slate-400">{</span></p>
                        <p class="pl-4"><span class="text-sky-400">"data"</span><span class="text-slate-400">: [</span></p>
                        <p class="pl-8"><span class="text-slate-400">{</span></p>
                        <p class="pl-12"><span class="text-sky-400">"id"</span><span class="text-slate-400">: </span><span class="text-amber-300">1</span><span class="text-slate-400">,</span></p>
                        <p class="pl-12"><span class="text-sky-400">"date"</span><span class="text-slate-400">: </span><span class="text-emerald-400">"2025-05-01"</span><span class="text-slate-400">,</span></p>
                        <p class="pl-12"><span class="text-sky-400">"hours"</span><span class="text-slate-400">: </span><span class="text-amber-300">8.5</span><span class="text-slate-400">,</span></p>
                        <p class="pl-12"><span class="text-sky-400">"project"</span><span class="text-slate-400">: </span><span class="text-emerald-400">"Projeto Alpha"</span></p>
                        <p class="pl-8"><span class="text-slate-400">}</span></p>
                        <p class="pl-4"><span class="text-slate-400">],</span></p>
                        <p class="pl-4"><span class="text-sky-400">"meta"</span><span class="text-slate-400">: { </span><span class="text-sky-400">"total"</span><span class="text-slate-400">: </span><span class="text-amber-300">22</span><span class="text-slate-400"> }</span></p>
                        <p><span class="text-slate-400">}</span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- PLANOS --}}
<section id="planos" class="py-24 px-6">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black mb-4">Planos <span class="gradient-text">simples</span></h2>
            <p class="text-slate-400 text-lg">Comece grátis. Faça upgrade quando precisar de mais.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">

            {{-- Gratuito --}}
            <div class="plan-card card-border rounded-2xl p-8">
                <div class="mb-6">
                    <p class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">Gratuito</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-white">R$ 0</span>
                        <span class="text-slate-500">/mês</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-3">Para quem está começando</p>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Lançamentos ilimitados',
                        'Tracking automático',
                        '1 projeto',
                        '1 empresa',
                        'Dashboard completo',
                        'Cálculo financeiro mensal',
                    ] as $feature)
                    <li class="flex items-center gap-3 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="block text-center py-3 px-6 rounded-xl border border-white/20 text-white font-semibold hover:bg-white/5 transition-colors">
                    Começar grátis
                </a>
            </div>

            {{-- Premium --}}
            <div class="plan-card rounded-2xl p-8 featured relative" style="border: 1px solid rgba(34,211,238,0.4); background: rgba(34,211,238,0.04);">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="gradient-bg text-white text-xs font-bold px-4 py-1 rounded-full">MAIS POPULAR</span>
                </div>
                <div class="mb-6">
                    <p class="text-cyan-400 text-sm font-medium uppercase tracking-wider mb-2">Premium</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-white">R$ 9,90</span>
                        <span class="text-slate-500">/mês</span>
                        <span class="text-cyan-400 text-lg font-bold">*</span>
                    </div>
                    <p class="text-slate-400 text-sm mt-3">Para quem trabalha a sério como PJ</p>
                    <p class="text-slate-500 text-xs mt-1">* Valor varia conforme o período escolhido</p>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Tudo do plano Gratuito',
                        'Projetos ilimitados',
                        'Empresas ilimitadas',
                        'Relatórios PDF e Excel',
                        'Importação via CSV',
                        'Controle de sobreaviso',
                        'Analytics e gráficos',
                        'Modo supervisor',
                        '7 dias grátis para testar',
                    ] as $feature)
                    <li class="flex items-center gap-3 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="block text-center py-3 px-6 rounded-xl gradient-bg text-white font-semibold hover:opacity-90 transition-opacity" style="box-shadow: 0 4px 20px rgba(34,211,238,0.25)">
                    Começar com 7 dias grátis
                </a>
            </div>

        </div>
    </div>
</section>

{{-- CTA FINAL --}}
<section class="py-24 px-6">
    <div class="max-w-3xl mx-auto text-center">
        <div class="card-border rounded-3xl p-12" style="background: radial-gradient(ellipse at top, rgba(34,211,238,0.06) 0%, transparent 60%);">
            <h2 class="text-4xl md:text-5xl font-black mb-4">
                Pronto para ter<br><span class="gradient-text">controle real?</span>
            </h2>
            <p class="text-slate-400 text-lg mb-8">Crie sua conta agora. É grátis, rápido e sem cartão de crédito.</p>
            <a href="{{ route('register') }}" class="btn-primary gradient-bg text-white text-lg inline-flex">
                Criar minha conta gratuita
                <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <p class="text-slate-600 text-sm mt-6">Já tem uma conta? <a href="{{ route('login') }}" class="text-cyan-500 hover:underline">Entrar</a></p>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="border-t border-white/5 py-10 px-6">
    <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png?v=' . filemtime(public_path('images/logo.png'))) }}" alt="Horas PJ" class="h-10 w-auto opacity-70">
        </div>
        <p class="text-slate-600 text-sm">© {{ date('Y') }} Horas PJ. Todos os direitos reservados.</p>
        <div class="flex items-center gap-6">
            <a href="{{ route('api-docs') }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">API</a>
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">Entrar</a>
            <a href="{{ route('register') }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">Cadastrar</a>
        </div>
    </div>
</footer>

{{-- Smooth scroll --}}
<script>
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>

</body>
</html>
