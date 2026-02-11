<x-app-layout>
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Modal de Confirmacao -->
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
            <p class="text-gray-400 mb-6" id="confirm-message">Deseja realmente realizar esta acao?</p>
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

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Controle de Horas</h1>
                <p class="text-gray-400 text-sm sm:text-base">Gestao de tempo e faturamento PJ</p>
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

            <!-- Valor Extra -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-orange-500/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-500/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-white sensitive-value" id="extra-value">R$ {{ number_format($stats['extra_value'], 2, ',', '.') }}</span>
                </div>
                <p class="text-gray-400 text-sm">Valor Extra</p>
                <p class="text-xs text-gray-500 mt-1">Valor fixo mensal</p>
            </div>
        </div>

        <!-- Total Geral -->
        <div class="bg-gradient-to-r from-cyan-900/30 to-purple-900/30 border border-cyan-500/30 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Geral do Mes</p>
                    <p class="text-xs text-gray-500 mt-1">Horas + Valor Extra</p>
                </div>
                <span class="text-4xl font-bold text-white sensitive-value" id="total-with-extra">R$ {{ number_format($stats['total_with_extra'], 2, ',', '.') }}</span>
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Inicio</label>
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Descricao</label>
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
            <div class="p-4 sm:p-6 border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span id="entries-title">Últimos lançamentos</span>
                    </h2>
                    <!-- Toggle Visualizacao -->
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
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Horario</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Horas</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Projeto</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Descricao</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Acoes</th>
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
                                        Nenhum lancamento encontrado para este mes.
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
                            Nenhum lancamento encontrado para este mes.
                        </div>
                    @endforelse
                </div>
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
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">{{ ucfirst($dayData['date']->translatedFormat('l')) }}</td>
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
                                            <span class="text-xs">Ver horarios</span>
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
                                        Nenhum lancamento encontrado para este mes.
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
                                    <span class="text-gray-500 text-sm ml-2">{{ ucfirst($dayData['date']->translatedFormat('l')) }}</span>
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
                                Ver horarios
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
                            Nenhum lancamento encontrado para este mes.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Paginacao -->
            @if($entries->hasPages())
                <div class="p-4 border-t border-gray-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-400">
                            Mostrando {{ $entries->firstItem() }} a {{ $entries->lastItem() }} de {{ $entries->total() }} lancamentos
                        </p>
                        <div class="flex gap-2">
                            @if($entries->onFirstPage())
                                <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-lg text-sm cursor-not-allowed">Anterior</span>
                            @else
                                <a href="{{ $entries->previousPageUrl() }}&month={{ $currentMonth }}" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm transition-colors">Anterior</a>
                            @endif

                            @if($entries->hasMorePages())
                                <a href="{{ $entries->nextPageUrl() }}&month={{ $currentMonth }}" class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm transition-colors">Proximo</a>
                            @else
                                <span class="px-3 py-1 bg-gray-800 text-gray-500 rounded-lg text-sm cursor-not-allowed">Proximo</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Divisao por Empresa -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Faturamento por Empresa (Total: <span class="sensitive-value">R$ {{ number_format($stats['total_with_extra'], 2, ',', '.') }}</span>)
            </h2>

            @if(count($stats['company_revenues']) > 0 || $stats['unassigned_revenue'] > 0)
                @php
                    $colors = ['blue', 'emerald', 'purple', 'cyan', 'amber', 'rose'];
                    $colorIndex = 0;
                    $totalRevenue = $stats['total_with_extra'];
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
                            <h3 class="text-white font-semibold mb-1">Nao Atribuido</h3>
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
                    <p class="text-sm">Acesse <a href="{{ route('settings') }}" class="text-cyan-400 hover:text-cyan-300">Configuracoes</a> para cadastrar empresas e vincular aos projetos.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const HOURLY_RATE = {{ $stats['hourly_rate'] }};
        const EXTRA_VALUE = {{ $stats['extra_value'] }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const CURRENT_MONTH = '{{ $currentMonth }}';
    </script>
        <script src="{{ asset('js/tracking.js') }}?v={{ filemtime(public_path('js/tracking.js')) }}"></script>
    @endpush
</x-app-layout>
