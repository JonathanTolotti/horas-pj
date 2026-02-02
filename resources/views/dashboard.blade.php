<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-1">Controle de Horas</h1>
                <p class="text-gray-400">Gestao de tempo e faturamento PJ</p>
            </div>
            <div class="flex items-center gap-6">
                <!-- Month Filter -->
                <div>
                    <select id="month-filter" onchange="changeMonth(this.value)"
                        class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        @foreach($months as $month)
                            <option value="{{ $month['value'] }}" {{ $currentMonth === $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Hoje</p>
                    <p class="text-2xl font-semibold text-white" id="current-date"></p>
                    <p class="text-lg text-cyan-400 font-mono" id="current-time"></p>
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
                    <span class="text-3xl font-bold text-white" id="total-hours">{{ number_format($stats['total_hours'], 2, ',', '.') }}h</span>
                </div>
                <p class="text-gray-400 text-sm">Total de Horas</p>
                <p class="text-xs text-gray-500 mt-1">No periodo atual</p>
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
                        <span class="text-3xl font-bold text-white" id="hourly-rate">R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</span>
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
                    <span class="text-3xl font-bold text-white" id="total-revenue">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</span>
                </div>
                <p class="text-gray-400 text-sm">Faturado (Horas)</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['total_hours'], 2, ',', '.') }}h x R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</p>
            </div>

            <!-- Extra Home Office -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-orange-500/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-500/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-white" id="extra-home-office">R$ {{ number_format($stats['extra_home_office'], 2, ',', '.') }}</span>
                </div>
                <p class="text-gray-400 text-sm">Extra Home Office</p>
                <p class="text-xs text-gray-500 mt-1">Valor fixo mensal</p>
            </div>
        </div>

        <!-- Total Geral -->
        <div class="bg-gradient-to-r from-cyan-900/30 to-purple-900/30 border border-cyan-500/30 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Geral do Mes</p>
                    <p class="text-xs text-gray-500 mt-1">Horas + Extra Home Office</p>
                </div>
                <span class="text-4xl font-bold text-white" id="total-with-extra">R$ {{ number_format($stats['total_with_extra'], 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Bater Ponto -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-900/50 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Novo Lancamento
            </h2>

            <form id="entry-form" onsubmit="return false;">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Data</label>
                        <input type="date" id="entry-date" name="date"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Inicio</label>
                        <input type="time" id="entry-start" name="start_time"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Fim</label>
                        <input type="time" id="entry-end" name="end_time"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Descricao</label>
                        <input type="text" id="entry-description" name="description" placeholder="Ex: Desenvolvimento de features"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button type="button" onclick="addEntry()"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-cyan-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Adicionar Lancamento
                    </button>

                    <button type="button" onclick="toggleTracking()" id="track-btn"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-emerald-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="track-btn-text">Iniciar Tracking</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabela de Lancamentos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-gray-800">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lancamentos do Mes
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Inicio</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Fim</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Horas</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Descricao</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Acoes</th>
                        </tr>
                    </thead>
                    <tbody id="entries-table" class="divide-y divide-gray-800">
                        @forelse($entries as $entry)
                            <tr class="hover:bg-gray-800/50 transition-colors" data-entry-id="{{ $entry->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $entry->date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">{{ substr($entry->start_time, 0, 5) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">{{ substr($entry->end_time, 0, 5) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="bg-cyan-500/20 text-cyan-300 px-3 py-1 rounded-full font-medium">{{ number_format($entry->hours, 2, ',', '.') }}h</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">{{ $entry->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-400">R$ {{ number_format($entry->hours * $stats['hourly_rate'], 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                    Nenhum lancamento encontrado para este mes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Divisao por CNPJ -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Divisao por CNPJ (Total: R$ {{ number_format($stats['total_with_extra'], 2, ',', '.') }})
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php $colors = ['blue', 'emerald', 'purple']; @endphp
                @foreach($stats['cnpjs'] as $index => $cnpj)
                    @php $color = $colors[$index - 1] ?? 'blue'; @endphp
                    <div class="bg-gradient-to-br from-{{ $color }}-500/10 to-{{ $color }}-600/5 border border-{{ $color }}-500/30 rounded-lg p-5 hover:border-{{ $color }}-500/50 transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <div class="bg-{{ $color }}-500/20 p-2 rounded-lg">
                                <svg class="w-5 h-5 text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="text-xs bg-{{ $color }}-500/20 text-{{ $color }}-300 px-2 py-1 rounded-full font-medium">33,33%</span>
                        </div>
                        <h3 class="text-white font-semibold mb-1">{{ $cnpj['name'] }}</h3>
                        <p class="text-gray-400 text-sm mb-3 font-mono">{{ $cnpj['number'] }}</p>
                        <p class="text-2xl font-bold text-{{ $color }}-400 cnpj-value">R$ {{ number_format($stats['revenue_per_cnpj'], 2, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const HOURLY_RATE = {{ $stats['hourly_rate'] }};
        const EXTRA_HOME_OFFICE = {{ $stats['extra_home_office'] }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const CURRENT_MONTH = '{{ $currentMonth }}';
    </script>
    <script src="{{ asset('js/tracking.js') }}"></script>
    @endpush
</x-app-layout>
