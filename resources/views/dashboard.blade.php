<x-app-layout>
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Modal de Confirmação -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md mx-4">
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

    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Subscription Alert Banner -->
        <x-subscription-alert :alert="$subscriptionAlert" />

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Controle de Horas</h1>
                <p class="text-gray-400 text-sm sm:text-base">Gestão de tempo e faturamento PJ</p>
            </div>
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Month Filter -->
                <div>
                    <select id="month-filter" onchange="changeMonth(this.value)"
                        class="bg-gray-800 border border-gray-700 rounded-lg pl-3 pr-8 sm:pl-4 sm:pr-10 py-2 text-white text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent appearance-none bg-no-repeat bg-right"
                        style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%239ca3af%22%3E%3Cpath fill-rule=%22evenodd%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22 clip-rule=%22evenodd%22/%3E%3C/svg%3E'); background-position: right 0.5rem center; background-size: 1.25rem;">
                        @foreach($months as $month)
                            <option value="{{ $month['value'] }}" {{ $currentMonth === $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Export Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                        class="bg-gray-800 border border-gray-700 hover:border-gray-600 rounded-lg px-3 py-2 text-white text-sm flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="hidden sm:inline">Exportar</span>
                        <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition
                        class="absolute right-0 mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50 overflow-hidden">
                        <div class="py-1">
                            @if($isPremium)
                                <a href="{{ route('export.pdf', ['month' => $currentMonth]) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Relatório PDF</div>
                                        <div class="text-xs text-gray-500">Relatório completo do mês</div>
                                    </div>
                                </a>
                                <a href="{{ route('export.excel', ['month' => $currentMonth]) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Planilha CSV</div>
                                        <div class="text-xs text-gray-500">Exportar para Excel</div>
                                    </div>
                                </a>
                                <div class="border-t border-gray-700 my-1"></div>
                                <button onclick="openNfExportModal()"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Relatório para NF</div>
                                        <div class="text-xs text-gray-500">Selecione uma empresa</div>
                                    </div>
                                </button>
                                <div class="border-t border-gray-700 my-1"></div>
                                <button onclick="openImportModal()"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium">Importar CSV</div>
                                        <div class="text-xs text-gray-500">Lançar horas em lote</div>
                                    </div>
                                </button>
                            @else
                                <button onclick="showPremiumModal('exportacao de relatorios')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">Relatório PDF</div>
                                        <div class="text-xs text-gray-500">Relatório completo do mês</div>
                                    </div>
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </button>
                                <button onclick="showPremiumModal('exportacao de relatorios')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">Planilha CSV</div>
                                        <div class="text-xs text-gray-500">Exportar para Excel</div>
                                    </div>
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </button>
                                <div class="border-t border-gray-700 my-1"></div>
                                <button onclick="showPremiumModal('exportacao de relatorios')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">Relatório para NF</div>
                                        <div class="text-xs text-gray-500">Selecione uma empresa</div>
                                    </div>
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </button>
                                <div class="border-t border-gray-700 my-1"></div>
                                <button onclick="showPremiumModal('importacao de CSV')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-400 hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div class="flex-1 text-left">
                                        <div class="font-medium">Importar CSV</div>
                                        <div class="text-xs text-gray-500">Lançar horas em lote</div>
                                    </div>
                                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-xs sm:text-sm text-gray-400">Hoje</p>
                    <p class="text-base sm:text-2xl font-semibold text-white" id="current-date"></p>
                    <p class="text-sm sm:text-lg text-cyan-400 font-mono" id="current-time"></p>
                </div>
            </div>
        </div>

        <!-- Cards de Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de Horas -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-cyan-500/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-cyan-500/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-white sensitive-value" id="total-hours">{{ sprintf('%02d:%02d', floor($stats['total_hours']), round(($stats['total_hours'] - floor($stats['total_hours'])) * 60)) }}</span>
                </div>
                <p class="text-gray-400 text-sm">Total de Horas</p>
                <p class="text-xs text-gray-500 mt-1">No período atual</p>
            </div>

            <!-- Valor Hora -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-emerald-500/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-emerald-500/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-bold text-white sensitive-value" id="hourly-rate">R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-gray-400 text-sm">Valor/Hora</p>
                <p class="text-xs text-gray-500 mt-1">Configurado no sistema</p>
            </div>

            <!-- Total Faturado (horas) -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-purple-500/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-500/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-white sensitive-value" id="total-revenue">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</span>
                </div>
                <p class="text-gray-400 text-sm">Faturado (Horas)</p>
                <p class="text-xs text-gray-500 mt-1">{{ sprintf('%02d:%02d', floor($stats['total_hours']), round(($stats['total_hours'] - floor($stats['total_hours'])) * 60)) }} x R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</p>
            </div>

            <!-- Valor Extra (Acréscimo) -->
{{--            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-emerald-500/50 transition-all">--}}
{{--                <div class="flex items-center justify-between mb-4">--}}
{{--                    <div class="bg-emerald-500/10 p-3 rounded-lg">--}}
{{--                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>--}}
{{--                        </svg>--}}
{{--                    </div>--}}
{{--                    <span class="text-3xl font-bold text-emerald-400 sensitive-value" id="extra-value">+R$ {{ number_format($stats['extra_value'], 2, ',', '.') }}</span>--}}
{{--                </div>--}}
{{--                <p class="text-gray-400 text-sm">Acréscimo</p>--}}
{{--                <p class="text-xs text-gray-500 mt-1">Valor fixo mensal</p>--}}
{{--            </div>--}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-indigo-500/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-indigo-500/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold text-emerald-400 sensitive-value" id="extra-value">+R$ {{ number_format($stats['extra_value'], 2, ',', '.') }}</div>
                        <div class="text-xl font-bold text-red-400 sensitive-value" id="discount-value">-R$ {{ number_format($stats['discount_value'] ?? 0, 2, ',', '.') }}</div>
                    </div>
                </div>
                <p class="text-gray-400 text-sm">Ajustes Mensais</p>
                <p class="text-xs text-gray-500 mt-1">Acréscimo e desconto fixos</p>
            </div>

        </div>

        <!-- Card de Sobreaviso (se houver) -->
        @if($stats['on_call_hours'] > 0 || $onCallPeriods->count() > 0)
        <div class="bg-gradient-to-r from-orange-900/30 to-amber-900/30 border border-orange-500/30 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-orange-500/20 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Sobreaviso do Mês</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $onCallPeriods->count() }} {{ $onCallPeriods->count() == 1 ? 'período' : 'períodos' }} • {{ sprintf('%02d:%02d', floor($stats['on_call_hours']), round(($stats['on_call_hours'] - floor($stats['on_call_hours'])) * 60)) }} horas</p>
                    </div>
                </div>
                <span class="text-3xl font-bold text-orange-400 sensitive-value" id="on-call-revenue">R$ {{ number_format($stats['on_call_revenue'], 2, ',', '.') }}</span>
            </div>
        </div>
        @endif

        <!-- Total Geral -->
        <div class="bg-gradient-to-r from-cyan-900/30 to-purple-900/30 border border-cyan-500/30 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Final do Mês</p>
                    <p class="text-xs text-gray-500 mt-1">Horas + acréscimo - desconto{{ $stats['on_call_revenue'] > 0 ? ' + sobreaviso' : '' }}</p>
                </div>
                <span class="text-4xl font-bold text-white sensitive-value" id="total-final">R$ {{ number_format($stats['total_final_with_on_call'] ?? $stats['total_final'] ?? $stats['total_with_extra'], 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Bater Ponto -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-900/50 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Novo Lançamento
            </h2>

            <form id="entry-form" onsubmit="return false;">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Data</label>
                        <input type="date" id="entry-date" name="date"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Início</label>
                        <input type="text" id="entry-start" name="start_time" placeholder="00:00" maxlength="5"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent font-mono"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Fim</label>
                        <input type="text" id="entry-end" name="end_time" placeholder="00:00" maxlength="5"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent font-mono"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Projeto</label>
                        <select id="entry-project" name="project_id"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            <option value="">Sem projeto</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $defaultProjectId == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Descrição</label>
                        <input type="text" id="entry-description" name="description" placeholder="Ex: Desenvolvimento de features"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button" onclick="addEntry()"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-cyan-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Adicionar
                    </button>

                    <button type="button" onclick="toggleTracking()" id="track-btn"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-emerald-500/30">
                        <!-- Play Icon -->
                        <svg id="track-icon-play" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <!-- Pause Icon (hidden by default) -->
                        <svg id="track-icon-pause" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="track-btn-text">Iniciar Tracking</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabela de Lançamentos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span id="entries-title">Últimos lançamentos</span>
                    </h2>
                    <!-- Toggle Visualização -->
                    <div class="flex items-center gap-1 bg-gray-800 rounded-lg p-1">
                        <button onclick="setViewMode('entries')" id="view-entries-btn"
                            class="view-toggle-btn px-3 py-1.5 rounded-md text-sm font-medium transition-all flex items-center gap-1.5 bg-cyan-600 text-white"
                            title="Visualizar por batidas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            <span class="hidden sm:inline">Batidas</span>
                        </button>
                        <button onclick="setViewMode('daily')" id="view-daily-btn"
                            class="view-toggle-btn px-3 py-1.5 rounded-md text-sm font-medium transition-all flex items-center gap-1.5 text-gray-400 hover:text-white"
                            title="Visualizar por dia">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="hidden sm:inline">Por Dia</span>
                            @if(!$canViewByDay)
                                <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========== VISUALIZAÇÃO POR BATIDAS ========== -->
            <div id="view-entries" class="view-container">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Horário</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Horas</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Projeto</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Descrição</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="entries-table" class="divide-y divide-gray-800">
                            @forelse($entries as $entry)
                                <tr class="hover:bg-gray-800/50 transition-colors" data-entry-id="{{ $entry->id }}">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ $entry->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">{{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full font-medium">{{ sprintf('%02d:%02d', floor($entry->hours), round(($entry->hours - floor($entry->hours)) * 60)) }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        @if($entry->project)
                                            <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">{{ $entry->project->name }}</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-300 max-w-xs truncate">{{ $entry->description }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-emerald-400 sensitive-value">R$ {{ number_format($entry->hours * $stats['hourly_rate'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <button onclick="removeEntry({{ $entry->id }})"
                                            class="text-red-400 hover:text-red-300 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-row">
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Nenhum lançamento encontrado para este mês.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-gray-800" id="entries-cards">
                    @forelse($entries as $entry)
                        <div class="p-4 hover:bg-gray-800/50 transition-colors" data-entry-id="{{ $entry->id }}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-white font-medium">{{ $entry->date->format('d/m/Y') }}</span>
                                    <span class="text-gray-400 font-mono text-sm">{{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}</span>
                                </div>
                                <button onclick="removeEntry({{ $entry->id }})"
                                    class="text-red-400 hover:text-red-300 transition-colors p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            @if($entry->project)
                                <span class="inline-block bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded text-xs mb-2">{{ $entry->project->name }}</span>
                            @endif
                            <p class="text-gray-300 text-sm mb-3">{{ $entry->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-sm font-medium">{{ sprintf('%02d:%02d', floor($entry->hours), round(($entry->hours - floor($entry->hours)) * 60)) }}</span>
                                <span class="text-emerald-400 font-semibold sensitive-value">R$ {{ number_format($entry->hours * $stats['hourly_rate'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div id="empty-card" class="p-8 text-center text-gray-500">
                            Nenhum lançamento encontrado para este mês.
                        </div>
                    @endforelse
                </div>

                <!-- Paginação (apenas na visualização por batidas) -->
                @if($entries->hasPages())
                    <div class="p-4 border-t border-gray-800">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-400">
                                Mostrando {{ $entries->firstItem() }} a {{ $entries->lastItem() }} de {{ $entries->total() }} lançamentos
                            </p>
                            <div class="flex gap-2">
                                @if($entries->onFirstPage())
                                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-lg text-sm cursor-not-allowed">Anterior</span>
                                @else
                                    <a href="{{ $entries->previousPageUrl() }}&month={{ $currentMonth }}" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm transition-colors">Anterior</a>
                                @endif

                                @if($entries->hasMorePages())
                                    <a href="{{ $entries->nextPageUrl() }}&month={{ $currentMonth }}" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm transition-colors">Próximo</a>
                                @else
                                    <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-lg text-sm cursor-not-allowed">Próximo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ========== VISUALIZAÇÃO POR DIA ========== -->
            <div id="view-daily" class="view-container hidden">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Data</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Dia</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Batidas</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Horas</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody id="daily-table" class="divide-y divide-gray-800">
                            @forelse($entriesByDay as $dateKey => $dayData)
                                <tr class="hover:bg-gray-800/50 transition-colors group">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300 font-medium">{{ $dayData['date']->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">{{ ucfirst($dayData['date']->isoFormat('dddd')) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs font-medium">{{ $dayData['entries_count'] }} {{ $dayData['entries_count'] == 1 ? 'batida' : 'batidas' }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full font-medium">{{ sprintf('%02d:%02d', floor($dayData['total_hours']), round(($dayData['total_hours'] - floor($dayData['total_hours'])) * 60)) }}</span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-emerald-400 sensitive-value">R$ {{ number_format($dayData['total_value'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <button onclick="toggleDayDetails('{{ $dateKey }}')" class="text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                                            <svg class="w-4 h-4 transform transition-transform" id="chevron-{{ $dateKey }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <span class="text-xs">Ver horários</span>
                                        </button>
                                    </td>
                                </tr>
                                <tr id="details-{{ $dateKey }}" class="hidden bg-gray-800/30">
                                    <td colspan="6" class="px-4 py-3">
                                        <div class="pl-4 border-l-2 border-cyan-500/30 space-y-2">
                                            @foreach($dayData['entries'] as $entry)
                                                <div class="flex items-center justify-between text-sm py-1">
                                                    <div class="flex items-center gap-4">
                                                        <span class="text-gray-400 font-mono">{{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}</span>
                                                        <span class="bg-cyan-500/10 text-cyan-300 px-2 py-0.5 rounded text-xs">{{ sprintf('%02d:%02d', floor($entry->hours), round(($entry->hours - floor($entry->hours)) * 60)) }}</span>
                                                        @if($entry->project)
                                                            <span class="bg-purple-500/10 text-purple-300 px-2 py-0.5 rounded text-xs">{{ $entry->project->name }}</span>
                                                        @endif
                                                        <span class="text-gray-500 truncate max-w-xs">{{ $entry->description }}</span>
                                                    </div>
                                                    <span class="text-emerald-400 font-medium sensitive-value">R$ {{ number_format($entry->hours * $stats['hourly_rate'], 2, ',', '.') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-daily-row">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Nenhum lançamento encontrado para este mês.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-gray-800" id="daily-cards">
                    @forelse($entriesByDay as $dateKey => $dayData)
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="text-white font-medium">{{ $dayData['date']->format('d/m/Y') }}</span>
                                    <span class="text-gray-500 text-sm ml-2">{{ ucfirst($dayData['date']->isoFormat('dddd')) }}</span>
                                </div>
                                <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">{{ $dayData['entries_count'] }} {{ $dayData['entries_count'] == 1 ? 'batida' : 'batidas' }}</span>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full text-sm font-medium">{{ sprintf('%02d:%02d', floor($dayData['total_hours']), round(($dayData['total_hours'] - floor($dayData['total_hours'])) * 60)) }}</span>
                                <span class="text-emerald-400 font-semibold sensitive-value">R$ {{ number_format($dayData['total_value'], 2, ',', '.') }}</span>
                            </div>
                            <button onclick="toggleDayDetails('{{ $dateKey }}-mobile')" class="w-full text-center text-cyan-400 hover:text-cyan-300 transition-colors text-sm py-2 border-t border-gray-700 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 transform transition-transform" id="chevron-{{ $dateKey }}-mobile" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                Ver horários
                            </button>
                            <div id="details-{{ $dateKey }}-mobile" class="hidden mt-3 space-y-2 border-t border-gray-700 pt-3">
                                @foreach($dayData['entries'] as $entry)
                                    <div class="bg-gray-800/50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-gray-300 font-mono text-sm">{{ substr($entry->start_time, 0, 5) }} - {{ substr($entry->end_time, 0, 5) }}</span>
                                            <span class="bg-cyan-500/10 text-cyan-300 px-2 py-0.5 rounded text-xs">{{ sprintf('%02d:%02d', floor($entry->hours), round(($entry->hours - floor($entry->hours)) * 60)) }}</span>
                                        </div>
                                        @if($entry->project)
                                            <span class="inline-block bg-purple-500/10 text-purple-300 px-2 py-0.5 rounded text-xs mb-1">{{ $entry->project->name }}</span>
                                        @endif
                                        <p class="text-gray-500 text-xs">{{ $entry->description }}</p>
                                        <p class="text-emerald-400 text-sm font-medium mt-1 sensitive-value">R$ {{ number_format($entry->hours * $stats['hourly_rate'], 2, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            Nenhum lançamento encontrado para este mês.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Seção de Sobreaviso -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Períodos de Sobreaviso
                    @if(!$canUseOnCall)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-amber-500/20 text-amber-400 rounded-full border border-amber-500/30">Premium</span>
                    @endif
                </h2>
                <div class="flex items-center gap-2">
                    @if($canUseOnCall)
                        @if($onCallPeriods->count() > 0)
                        <button onclick="recalculateOnCall()"
                            class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm"
                            title="Recalcular vínculos com lançamentos">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="hidden sm:inline">Recalcular</span>
                        </button>
                        @endif
                        <button onclick="openOnCallModal()"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-orange-500/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Novo Período
                        </button>
                    @else
                        <button onclick="showPremiumModal('sobreaviso')"
                            class="bg-gray-700 text-gray-400 px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm cursor-pointer hover:bg-gray-600">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Novo Período
                        </button>
                    @endif
                </div>
            </div>

            @if($canUseOnCall)
            <div id="on-call-list">
                @forelse($onCallPeriods as $period)
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-orange-500/30 transition-colors mb-3" data-on-call-id="{{ $period->id }}">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-medium">
                                        {{ $period->start_datetime->format('d/m/Y H:i') }} - {{ $period->end_datetime->format('d/m/Y H:i') }}
                                    </p>
                                    @if($period->project)
                                        <span class="text-xs bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded">{{ $period->project->name }}</span>
                                    @endif
                                    @if($period->description)
                                        <p class="text-gray-500 text-sm mt-1">{{ $period->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4 text-sm">
                                <div class="text-center">
                                    <p class="text-gray-500 text-xs">Total</p>
                                    <p class="text-white font-mono">{{ sprintf('%02d:%02d', floor($period->total_hours), round(($period->total_hours - floor($period->total_hours)) * 60)) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-500 text-xs">Trabalhado</p>
                                    <p class="text-cyan-400 font-mono">{{ sprintf('%02d:%02d', floor($period->worked_hours), round(($period->worked_hours - floor($period->worked_hours)) * 60)) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-500 text-xs">Sobreaviso</p>
                                    <p class="text-orange-400 font-mono font-semibold">{{ sprintf('%02d:%02d', floor($period->on_call_hours), round(($period->on_call_hours - floor($period->on_call_hours)) * 60)) }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-500 text-xs">Valor/h</p>
                                    <p class="text-gray-300 sensitive-value">R$ {{ number_format($period->hourly_rate, 2, ',', '.') }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-gray-500 text-xs">Total</p>
                                    <p class="text-emerald-400 font-semibold sensitive-value">R$ {{ number_format($period->on_call_hours * $period->hourly_rate, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="editOnCall({{ $period->id }}, '{{ $period->start_datetime->format('Y-m-d\TH:i') }}', '{{ $period->end_datetime->format('Y-m-d\TH:i') }}', {{ $period->project_id ?? 'null' }}, {{ $period->hourly_rate }}, '{{ addslashes($period->description ?? '') }}')"
                                class="p-2 text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="deleteOnCall({{ $period->id }})"
                                class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div id="no-on-call" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="mb-2">Nenhum período de sobreaviso cadastrado.</p>
                        <p class="text-sm">Clique em "Novo Período" para adicionar um período de sobreaviso.</p>
                    </div>
                @endforelse
            </div>
            @else
            <!-- Bloqueio Premium - Sobreaviso -->
            <div class="text-center py-8">
                <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/10 border border-amber-500/30 rounded-xl p-8 max-w-md mx-auto">
                    <div class="bg-amber-500/20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Funcionalidade Premium</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        O controle de sobreaviso está disponível apenas para assinantes Premium.
                        Registre períodos de sobreaviso e calcule automaticamente as horas trabalhadas versus horas de disponibilidade.
                    </p>
                    <button onclick="showPremiumModal('sobreaviso')"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-all hover:shadow-lg hover:shadow-amber-500/30">
                        Desbloquear Sobreaviso
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Divisão por Empresa -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Faturamento por Empresa (Total: <span class="sensitive-value">R$ {{ number_format($stats['total_final_with_on_call'] ?? $stats['total_final'] ?? $stats['total_with_extra'], 2, ',', '.') }}</span>)
            </h2>

            @if(count($stats['company_revenues']) > 0 || $stats['unassigned_revenue'] > 0)
                @php
                    $colors = ['blue', 'emerald', 'purple', 'cyan', 'amber', 'rose'];
                    $colorIndex = 0;
                    $totalRevenue = $stats['total_final'] ?? $stats['total_with_extra'];
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($stats['company_revenues'] as $company)
                        @php
                            $color = $colors[$colorIndex % count($colors)];
                            $percentage = $totalRevenue > 0 ? ($company['revenue'] / $totalRevenue) * 100 : 0;
                            $colorIndex++;
                        @endphp
                        <div class="bg-gradient-to-br from-{{ $color }}-500/10 to-{{ $color }}-600/5 border border-{{ $color }}-500/30 rounded-lg p-5 hover:border-{{ $color }}-500/50 transition-all" data-company-id="{{ $company['id'] }}">
                            <div class="flex items-start justify-between mb-3">
                                <div class="bg-{{ $color }}-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="text-xs bg-{{ $color }}-500/20 text-{{ $color }}-300 px-2 py-1 rounded-full font-medium">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            <h3 class="text-white font-semibold mb-1">{{ $company['name'] }}</h3>
                            <p class="text-gray-400 text-sm mb-3 font-mono">{{ $company['cnpj'] }}</p>
                            <p class="text-2xl font-bold text-{{ $color }}-400 sensitive-value company-revenue">R$ {{ number_format($company['revenue'], 2, ',', '.') }}</p>
                        </div>
                    @endforeach

                    @if($stats['unassigned_revenue'] > 0)
                        @php $unassignedPercentage = $totalRevenue > 0 ? ($stats['unassigned_revenue'] / $totalRevenue) * 100 : 0; @endphp
                        <div class="bg-gradient-to-br from-gray-500/10 to-gray-600/5 border border-gray-500/30 rounded-lg p-5 hover:border-gray-500/50 transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div class="bg-gray-500/20 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-xs bg-gray-500/20 text-gray-300 px-2 py-1 rounded-full font-medium">{{ number_format($unassignedPercentage, 1) }}%</span>
                            </div>
                            <h3 class="text-white font-semibold mb-1">Não Atribuído</h3>
                            <p class="text-gray-400 text-sm mb-3">Projetos sem empresa ou com porcentagem &lt; 100%</p>
                            <p class="text-2xl font-bold text-gray-400 sensitive-value unassigned-revenue">R$ {{ number_format($stats['unassigned_revenue'], 2, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="mb-2">Nenhuma empresa cadastrada.</p>
                    <p class="text-sm">Acesse <a href="{{ route('settings') }}" class="text-cyan-400 hover:text-cyan-300">Configurações</a> para cadastrar empresas e vincular aos projetos.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const HOURLY_RATE = {{ $stats['hourly_rate'] }};
        const EXTRA_VALUE = {{ $stats['extra_value'] }};
        const DISCOUNT_VALUE = {{ $stats['discount_value'] ?? 0 }};
        const CURRENT_MONTH = '{{ $currentMonth }}';
        const CAN_VIEW_BY_DAY = {{ $canViewByDay ? 'true' : 'false' }};
        const CAN_USE_ON_CALL = {{ $canUseOnCall ? 'true' : 'false' }};
        const IS_PREMIUM = {{ $isPremium ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('js/tracking.js') }}?v={{ filemtime(public_path('js/tracking.js')) }}"></script>
    @endpush

    <!-- Import CSV Modal -->
    @include('imports.modal')

    <!-- Modal de Exportação NF -->
    <div id="nf-export-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeNfExportModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-amber-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Relatório para Nota Fiscal</h3>
            </div>
            <p class="text-gray-400 mb-4">Selecione a empresa para gerar o relatório:</p>

            <form action="{{ route('export.nf') }}" method="GET">
                <input type="hidden" name="month" value="{{ $currentMonth }}">

                <div class="space-y-2 mb-6">
                    @forelse($companies as $company)
                        <label class="flex items-center gap-3 p-3 bg-gray-800 hover:bg-gray-700 rounded-lg cursor-pointer transition-colors border border-transparent has-[:checked]:border-amber-500/50">
                            <input type="radio" name="company_id" value="{{ $company->id }}" class="text-amber-500 focus:ring-amber-500 bg-gray-700 border-gray-600">
                            <div class="flex-1">
                                <div class="text-white font-medium">{{ $company->name }}</div>
                                <div class="text-gray-500 text-sm font-mono">{{ $company->cnpj }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            <p>Nenhuma empresa cadastrada.</p>
                            <a href="{{ route('settings') }}" class="text-cyan-400 hover:text-cyan-300 text-sm">Cadastrar empresa</a>
                        </div>
                    @endforelse
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeNfExportModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </button>
                    @if($companies->count() > 0)
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Gerar PDF
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openNfExportModal() {
            document.getElementById('nf-export-modal').classList.remove('hidden');
        }

        function closeNfExportModal() {
            document.getElementById('nf-export-modal').classList.add('hidden');
        }
    </script>
    @endpush

    <!-- Modal de Sobreaviso -->
    <div id="on-call-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" onclick="closeOnCallModal()"></div>
            <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg my-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-500/20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" id="on-call-modal-title">Novo Período de Sobreaviso</h3>
                </div>
                <form id="on-call-form" onsubmit="return false;">
                    <input type="hidden" id="on-call-id" value="">
                    <div class="space-y-4">
                        <!-- Inicio -->
                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <label class="block text-sm font-medium text-orange-400 mb-3">Início do Sobreaviso</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Data</label>
                                    <input type="text" id="on-call-start-date" placeholder="Selecione..."
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer" readonly/>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Hora</label>
                                    <input type="text" id="on-call-start-time" placeholder="00:00" maxlength="5"
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent font-mono"/>
                                </div>
                            </div>
                        </div>
                        <!-- Fim -->
                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <label class="block text-sm font-medium text-orange-400 mb-3">Fim do Sobreaviso</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Data</label>
                                    <input type="text" id="on-call-end-date" placeholder="Selecione..."
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer" readonly/>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Hora</label>
                                    <input type="text" id="on-call-end-time" placeholder="00:00" maxlength="5"
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent font-mono"/>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Projeto (opcional)</label>
                            <select id="on-call-project"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">Geral (todos os projetos)</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Valor por Hora (Sobreaviso)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-orange-400">R$</span>
                                <input type="text" id="on-call-rate" inputmode="decimal" placeholder="Deixe vazio para usar padrão"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-12 pr-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    onkeyup="formatCurrencyInputOnCall(this)" onblur="formatCurrencyInputOnCall(this)"/>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Se vazio, usa o valor configurado ou 1/3 do valor/hora normal</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Descrição (opcional)</label>
                            <input type="text" id="on-call-description" placeholder="Ex: Plantão de fim de semana"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"/>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="closeOnCallModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button type="button" onclick="saveOnCall()" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Flatpickr para sobreaviso (apenas data)
        let onCallStartDatePicker, onCallEndDatePicker;

        document.addEventListener('DOMContentLoaded', function() {
            const flatpickrConfig = {
                locale: 'pt',
                dateFormat: 'd/m/Y',
                disableMobile: true,
                allowInput: false
            };

            onCallStartDatePicker = flatpickr('#on-call-start-date', flatpickrConfig);
            onCallEndDatePicker = flatpickr('#on-call-end-date', flatpickrConfig);

            // Inicializar máscaras de hora para sobreaviso
            initializeOnCallTimeMasks();
        });

        // Máscara de hora para sobreaviso (igual ao dashboard)
        function initializeOnCallTimeMasks() {
            const timeInputs = document.querySelectorAll('#on-call-start-time, #on-call-end-time');
            timeInputs.forEach(input => {
                input.addEventListener('input', handleOnCallTimeInput);
                input.addEventListener('blur', validateOnCallTimeInput);
                input.addEventListener('keydown', handleOnCallTimeKeydown);
            });
        }

        function handleOnCallTimeInput(e) {
            const input = e.target;
            let cursorPos = input.selectionStart;
            const oldLen = input.value.length;

            let digits = input.value.replace(/\D/g, '');
            digits = digits.substring(0, 4);

            let newValue = '';
            if (digits.length === 0) {
                newValue = '';
            } else if (digits.length <= 2) {
                newValue = digits;
            } else {
                newValue = digits.substring(0, 2) + ':' + digits.substring(2);
            }

            if (input.value !== newValue) {
                input.value = newValue;
                const newLen = newValue.length;
                const newPos = cursorPos + (newLen - oldLen);
                input.setSelectionRange(Math.max(0, newPos), Math.max(0, newPos));
            }
        }

        function validateOnCallTimeInput(e) {
            const input = e.target;
            const value = input.value;

            if (!value) return;

            const match = value.match(/^(\d{1,2}):?(\d{0,2})$/);
            if (match) {
                let hours = parseInt(match[1]) || 0;
                let minutes = parseInt(match[2]) || 0;

                hours = Math.min(23, Math.max(0, hours));
                minutes = Math.min(59, Math.max(0, minutes));

                input.value = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
            } else {
                input.value = '';
            }
        }

        function handleOnCallTimeKeydown(e) {
            const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
            if (allowedKeys.includes(e.key)) return;
            if (e.ctrlKey || e.metaKey) return;
            if (!/^\d$/.test(e.key)) {
                e.preventDefault();
            }
        }

        // Formatação de moeda para sobreaviso
        function formatCurrencyInputOnCall(input) {
            let value = input.value.replace(/\D/g, '');
            if (value === '') {
                input.value = '';
                return;
            }
            value = (parseInt(value) / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = value;
        }

        function parseCurrencyValueOnCall(value) {
            if (!value) return null;
            return parseFloat(value.replace(/\./g, '').replace(',', '.')) || null;
        }

        // Converte data BR + hora para formato ISO
        function buildDatetime(dateStr, timeStr) {
            if (!dateStr || !timeStr) return null;
            // dateStr: dd/mm/yyyy, timeStr: HH:mm
            const [day, month, year] = dateStr.split('/');
            return `${year}-${month}-${day}T${timeStr}`;
        }

        // Extrai data e hora de formato ISO
        function parseDatetime(isoStr) {
            if (!isoStr) return { date: '', time: '' };
            // isoStr: yyyy-mm-ddTHH:mm ou yyyy-mm-dd HH:mm
            const normalized = isoStr.replace(' ', 'T');
            const [datePart, timePart] = normalized.split('T');
            const [year, month, day] = datePart.split('-');
            return {
                date: `${day}/${month}/${year}`,
                time: timePart ? timePart.substring(0, 5) : '00:00'
            };
        }

        // Modal de Sobreaviso
        function openOnCallModal(id = null, start = '', end = '', projectId = null, rate = '', description = '') {
            if (!CAN_USE_ON_CALL) {
                showPremiumModal('sobreaviso');
                return;
            }

            document.getElementById('on-call-modal-title').textContent = id ? 'Editar Período de Sobreaviso' : 'Novo Período de Sobreaviso';
            document.getElementById('on-call-id').value = id || '';

            // Parse e set das datas/horas
            const startParsed = parseDatetime(start);
            const endParsed = parseDatetime(end);

            if (startParsed.date) {
                onCallStartDatePicker.setDate(startParsed.date, false, 'd/m/Y');
                document.getElementById('on-call-start-time').value = startParsed.time;
            } else {
                onCallStartDatePicker.clear();
                document.getElementById('on-call-start-time').value = '';
            }

            if (endParsed.date) {
                onCallEndDatePicker.setDate(endParsed.date, false, 'd/m/Y');
                document.getElementById('on-call-end-time').value = endParsed.time;
            } else {
                onCallEndDatePicker.clear();
                document.getElementById('on-call-end-time').value = '';
            }

            document.getElementById('on-call-project').value = projectId || '';
            document.getElementById('on-call-rate').value = rate ? parseFloat(rate).toFixed(2).replace('.', ',') : '';
            document.getElementById('on-call-description').value = description;
            document.getElementById('on-call-modal').classList.remove('hidden');
        }

        function closeOnCallModal() {
            document.getElementById('on-call-modal').classList.add('hidden');
        }

        function editOnCall(id, start, end, projectId, rate, description) {
            openOnCallModal(id, start, end, projectId, rate, description);
        }

        async function saveOnCall() {
            if (!CAN_USE_ON_CALL) {
                showPremiumModal('sobreaviso');
                return;
            }

            const id = document.getElementById('on-call-id').value;
            const startDate = document.getElementById('on-call-start-date').value;
            const startTime = document.getElementById('on-call-start-time').value;
            const endDate = document.getElementById('on-call-end-date').value;
            const endTime = document.getElementById('on-call-end-time').value;
            const projectId = document.getElementById('on-call-project').value || null;
            const hourlyRate = parseCurrencyValueOnCall(document.getElementById('on-call-rate').value);
            const description = document.getElementById('on-call-description').value;

            if (!startDate || !startTime || !endDate || !endTime) {
                showToast('Por favor, informe data e hora de início e fim!', TOAST_TYPES.WARNING);
                return;
            }

            const startDatetime = buildDatetime(startDate, startTime);
            const endDatetime = buildDatetime(endDate, endTime);

            if (new Date(endDatetime) <= new Date(startDatetime)) {
                showToast('A data/hora de fim deve ser posterior à de início!', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const url = id ? `/on-call/${id}` : '/on-call';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        start_datetime: startDatetime,
                        end_datetime: endDatetime,
                        project_id: projectId,
                        hourly_rate: hourlyRate,
                        description: description || null
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao salvar');

                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeOnCallModal();
                location.reload();
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        function deleteOnCall(id) {
            if (!CAN_USE_ON_CALL) {
                showPremiumModal('sobreaviso');
                return;
            }
            showConfirm('Deseja realmente excluir este período de sobreaviso?', async () => {
                try {
                    const response = await fetch(`/on-call/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erro ao excluir');

                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    location.reload();
                } catch (error) {
                    showToast(error.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir Sobreaviso');
        }

        async function recalculateOnCall() {
            if (!CAN_USE_ON_CALL) {
                showPremiumModal('sobreaviso');
                return;
            }
            try {
                const response = await fetch('/on-call/recalculate?month=' + CURRENT_MONTH, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao recalcular');

                showToast(data.message, TOAST_TYPES.SUCCESS);
                location.reload();
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        // Fechar modal de sobreaviso com Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeOnCallModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
