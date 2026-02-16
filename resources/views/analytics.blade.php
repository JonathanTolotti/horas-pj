<x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1 flex items-center gap-3">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </h1>
            <p class="text-gray-400 text-sm sm:text-base">Visualize seus dados e identifique padrões</p>
        </div>

        @if(!$isPremium)
        <!-- Banner Premium -->
        <div class="bg-gradient-to-r from-purple-900/50 to-indigo-900/50 border border-purple-500/30 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="bg-purple-500/20 p-3 rounded-lg">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white">Recurso Premium</h3>
                    <p class="text-purple-200 text-sm">Os gráficos e analytics avançados estão disponíveis apenas para assinantes Premium.</p>
                </div>
                <a href="{{ route('subscription.plans') }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all hover:shadow-lg hover:shadow-purple-500/30">
                    Ver Planos
                </a>
            </div>
        </div>
        @endif

        <!-- Summary Cards -->
        <div id="summary-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 {{ !$isPremium ? 'opacity-50 pointer-events-none blur-sm' : '' }}">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5" x-data="{ showTip: false }">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-400 text-sm">Horas Este Mês</span>
                    <div class="flex items-center gap-2">
                        <div id="variation-badge" class="hidden px-2 py-0.5 text-xs font-medium rounded-full"></div>
                        <button @mouseenter="showTip = true" @mouseleave="showTip = false" class="text-gray-500 hover:text-gray-300 transition-colors relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div x-show="showTip" x-cloak class="absolute right-0 top-full mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-3 text-xs text-gray-300">
                                Total de horas trabalhadas no mês atual. O badge mostra a variação em relação ao mês anterior.
                            </div>
                        </button>
                    </div>
                </div>
                <div class="text-2xl font-bold text-white" id="current-month-hours">--:--</div>
                <div class="text-sm text-gray-500 mt-1" id="current-month-revenue">R$ 0,00</div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5" x-data="{ showTip: false }">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-400 text-sm">Média Diária</span>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <button @mouseenter="showTip = true" @mouseleave="showTip = false" class="text-gray-500 hover:text-gray-300 transition-colors relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div x-show="showTip" x-cloak class="absolute right-0 top-full mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-3 text-xs text-gray-300">
                                Horas totais do mês divididas pelo número de dias em que houve lançamentos.
                            </div>
                        </button>
                    </div>
                </div>
                <div class="text-2xl font-bold text-white" id="daily-average">--:--</div>
                <div class="text-sm text-gray-500 mt-1"><span id="days-worked">0</span> dias trabalhados</div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5" x-data="{ showTip: false }">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-400 text-sm">Total do Ano</span>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <button @mouseenter="showTip = true" @mouseleave="showTip = false" class="text-gray-500 hover:text-gray-300 transition-colors relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div x-show="showTip" x-cloak class="absolute right-0 top-full mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-3 text-xs text-gray-300">
                                Soma de todas as horas trabalhadas desde janeiro do ano atual até o mês corrente.
                            </div>
                        </button>
                    </div>
                </div>
                <div class="text-2xl font-bold text-white" id="year-hours">--:--</div>
                <div class="text-sm text-gray-500 mt-1" id="year-revenue">R$ 0,00</div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5" x-data="{ showTip: false }">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-400 text-sm">Projeto Destaque</span>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <button @mouseenter="showTip = true" @mouseleave="showTip = false" class="text-gray-500 hover:text-gray-300 transition-colors relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div x-show="showTip" x-cloak class="absolute right-0 top-full mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-3 text-xs text-gray-300">
                                O projeto com mais horas trabalhadas no mês atual.
                            </div>
                        </button>
                    </div>
                </div>
                <div class="text-lg font-bold text-white truncate" id="top-project-name">-</div>
                <div class="text-sm text-gray-500 mt-1"><span id="top-project-hours">0</span> horas este mês</div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 {{ !$isPremium ? 'opacity-50 pointer-events-none blur-sm' : '' }}">
            <!-- Comparativo Mensal -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Comparativo Mensal
                    </h3>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-full hover:bg-gray-800 text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-cloak
                             class="absolute right-0 top-full mt-2 w-72 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-blue-500/20 p-2 rounded-lg shrink-0">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm mb-1">Como funciona?</h4>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        Mostra o <strong class="text-white">total de horas trabalhadas</strong> em cada um dos últimos 12 meses.
                                        Ao passar o mouse sobre uma barra, você vê também o faturamento calculado (horas × valor/hora).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Horas por Dia da Semana -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Horas por Dia da Semana
                    </h3>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-full hover:bg-gray-800 text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-cloak
                             class="absolute right-0 top-full mt-2 w-72 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-purple-500/20 p-2 rounded-lg shrink-0">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm mb-1">Como funciona?</h4>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        Analisa seus lançamentos dos <strong class="text-white">últimos 3 meses</strong> e mostra em quais dias da semana você mais trabalha.
                                        Útil para identificar padrões e planejar sua rotina.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="weekdayChart"></canvas>
                </div>
            </div>

            <!-- Horas por Projeto -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Horas por Projeto (Mês Atual)
                    </h3>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-full hover:bg-gray-800 text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-cloak
                             class="absolute right-0 top-full mt-2 w-72 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-cyan-500/20 p-2 rounded-lg shrink-0">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm mb-1">Como funciona?</h4>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        Mostra a <strong class="text-white">distribuição de horas por projeto</strong> no mês atual.
                                        Cada fatia representa um projeto, e o tamanho indica a proporção de horas dedicadas a ele.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="projectChart"></canvas>
                </div>
            </div>

            <!-- Tendência de Faturamento -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Tendência de Faturamento
                    </h3>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false" class="p-1.5 rounded-full hover:bg-gray-800 text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-cloak
                             class="absolute right-0 top-full mt-2 w-80 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="bg-emerald-500/20 p-2 rounded-lg shrink-0">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm mb-1">Como funciona?</h4>
                                    <p class="text-gray-400 text-xs leading-relaxed mb-2">
                                        Exibe seu faturamento dos <strong class="text-white">últimos 6 meses</strong> (linha sólida) e uma <strong class="text-white">previsão para os próximos 2 meses</strong> (linha tracejada).
                                    </p>
                                    <p class="text-gray-400 text-xs leading-relaxed">
                                        A previsão é calculada com base na <strong class="text-white">média</strong> dos meses que tiveram lançamentos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="mt-3 flex items-center justify-center gap-6 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                        <span class="text-gray-400">Realizado</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-emerald-500/30 rounded-full border border-emerald-500 border-dashed"></span>
                        <span class="text-gray-400">Previsão</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @if($isPremium)
    @push('scripts')
    <script>
        // Cores do tema
        const colors = {
            blue: 'rgb(59, 130, 246)',
            purple: 'rgb(168, 85, 247)',
            cyan: 'rgb(6, 182, 212)',
            emerald: 'rgb(16, 185, 129)',
            orange: 'rgb(249, 115, 22)',
            pink: 'rgb(236, 72, 153)',
            yellow: 'rgb(234, 179, 8)',
            gray: 'rgb(107, 114, 128)',
        };

        // Configuração global do Chart.js
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.borderColor = 'rgba(55, 65, 81, 0.5)';
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';

        // Formatadores
        function formatHours(hours) {
            const h = Math.floor(hours);
            const m = Math.round((hours - h) * 60);
            return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        }

        function formatCurrency(value) {
            return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Carregar resumo
        async function loadSummary() {
            try {
                const response = await fetch('/analytics/summary');
                const data = await response.json();

                // Mês atual
                document.getElementById('current-month-hours').textContent = formatHours(data.current_month.hours);
                document.getElementById('current-month-revenue').textContent = formatCurrency(data.current_month.revenue);
                document.getElementById('daily-average').textContent = formatHours(data.current_month.daily_average);
                document.getElementById('days-worked').textContent = data.current_month.days_worked;

                // Ano
                document.getElementById('year-hours').textContent = formatHours(data.year.hours);
                document.getElementById('year-revenue').textContent = formatCurrency(data.year.revenue);

                // Variação
                const badge = document.getElementById('variation-badge');
                if (data.variation !== 0) {
                    badge.classList.remove('hidden');
                    if (data.variation > 0) {
                        badge.classList.add('bg-emerald-500/20', 'text-emerald-400');
                        badge.textContent = '+' + data.variation + '%';
                    } else {
                        badge.classList.add('bg-red-500/20', 'text-red-400');
                        badge.textContent = data.variation + '%';
                    }
                }

                // Projeto destaque
                if (data.top_project) {
                    document.getElementById('top-project-name').textContent = data.top_project.name;
                    document.getElementById('top-project-hours').textContent = formatHours(data.top_project.hours);
                }
            } catch (error) {
                console.error('Erro ao carregar resumo:', error);
            }
        }

        // Gráfico de Comparativo Mensal
        async function loadMonthlyChart() {
            try {
                const response = await fetch('/analytics/monthly-comparison');
                const data = await response.json();

                const ctx = document.getElementById('monthlyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.month),
                        datasets: [{
                            label: 'Horas',
                            data: data.map(d => d.hours),
                            backgroundColor: colors.blue,
                            borderRadius: 4,
                            barPercentage: 0.7,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const item = data[ctx.dataIndex];
                                        return [
                                            'Horas: ' + formatHours(item.hours),
                                            'Faturamento: ' + formatCurrency(item.revenue)
                                        ];
                                    },
                                    title: (ctx) => data[ctx[0].dataIndex].month_full
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(55, 65, 81, 0.3)' },
                                ticks: {
                                    callback: (value) => formatHours(value)
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao carregar gráfico mensal:', error);
            }
        }

        // Gráfico de Horas por Dia da Semana
        async function loadWeekdayChart() {
            try {
                const response = await fetch('/analytics/hours-by-weekday');
                const data = await response.json();

                const ctx = document.getElementById('weekdayChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.short),
                        datasets: [{
                            label: 'Total de Horas',
                            data: data.map(d => d.hours),
                            backgroundColor: colors.purple,
                            borderRadius: 4,
                            barPercentage: 0.6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const item = data[ctx.dataIndex];
                                        return [
                                            'Total: ' + formatHours(item.hours),
                                            'Média: ' + formatHours(item.average) + '/dia',
                                            'Lançamentos: ' + item.count
                                        ];
                                    },
                                    title: (ctx) => data[ctx[0].dataIndex].name
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(55, 65, 81, 0.3)' },
                                ticks: {
                                    callback: (value) => formatHours(value)
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao carregar gráfico de dias da semana:', error);
            }
        }

        // Gráfico de Horas por Projeto (Donut)
        async function loadProjectChart() {
            try {
                const response = await fetch('/analytics/hours-by-project');
                const data = await response.json();

                if (data.length === 0) {
                    document.getElementById('projectChart').parentElement.innerHTML = `
                        <div class="h-64 flex items-center justify-center text-gray-500">
                            Nenhum lançamento este mês
                        </div>
                    `;
                    return;
                }

                const projectColors = [colors.cyan, colors.purple, colors.orange, colors.pink, colors.yellow, colors.emerald, colors.blue];

                const ctx = document.getElementById('projectChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(d => d.project),
                        datasets: [{
                            data: data.map(d => d.hours),
                            backgroundColor: data.map((_, i) => projectColors[i % projectColors.length]),
                            borderWidth: 0,
                            hoverOffset: 10,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15,
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((ctx.raw / total) * 100).toFixed(1);
                                        return ctx.label + ': ' + formatHours(ctx.raw) + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao carregar gráfico de projetos:', error);
            }
        }

        // Gráfico de Tendência de Faturamento
        async function loadTrendChart() {
            try {
                const response = await fetch('/analytics/revenue-trend');
                const result = await response.json();
                const data = result.data;

                const ctx = document.getElementById('trendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.month),
                        datasets: [{
                            label: 'Faturamento',
                            data: data.map(d => d.revenue),
                            borderColor: colors.emerald,
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: data.map(d => d.type === 'actual' ? colors.emerald : 'transparent'),
                            pointBorderColor: colors.emerald,
                            pointBorderWidth: 2,
                            segment: {
                                borderDash: ctx => {
                                    const idx = ctx.p0DataIndex;
                                    return data[idx].type === 'forecast' || data[idx + 1]?.type === 'forecast' ? [5, 5] : [];
                                },
                                borderColor: ctx => {
                                    const idx = ctx.p0DataIndex;
                                    return data[idx].type === 'forecast' || data[idx + 1]?.type === 'forecast'
                                        ? 'rgba(16, 185, 129, 0.5)'
                                        : colors.emerald;
                                }
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const item = data[ctx.dataIndex];
                                        const prefix = item.type === 'forecast' ? '(Previsão) ' : '';
                                        return prefix + formatCurrency(item.revenue);
                                    },
                                    afterLabel: (ctx) => {
                                        const item = data[ctx.dataIndex];
                                        return 'Horas: ' + formatHours(item.hours);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(55, 65, 81, 0.3)' },
                                ticks: {
                                    callback: (value) => 'R$ ' + value.toLocaleString('pt-BR')
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao carregar gráfico de tendência:', error);
            }
        }

        // Inicializar todos os gráficos
        document.addEventListener('DOMContentLoaded', function() {
            loadSummary();
            loadMonthlyChart();
            loadWeekdayChart();
            loadProjectChart();
            loadTrendChart();
        });
    </script>
    @endpush
    @endif
</x-app-layout>
