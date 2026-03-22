<x-app-layout>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Banner: Modo Supervisor -->
        <div class="bg-indigo-900/40 border border-indigo-500/50 rounded-lg px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex items-center gap-3 flex-1">
                <svg class="w-5 h-5 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <div class="text-indigo-200 text-sm">
                    <span class="font-semibold">Modo Supervisor</span> — Visualizando dados de <span class="font-semibold">{{ $supervisedUser->name }}</span>
                    <span class="text-indigo-400 ml-2">·</span>
                    <span class="text-indigo-400 ml-2">
                        @if($access->expires_at === null)
                            Acesso permanente
                        @else
                            Válido até {{ $access->expires_at->format('d/m/Y H:i') }}
                        @endif
                    </span>
                </div>
            </div>
            <a href="{{ route('supervisor.index') }}"
               class="shrink-0 text-sm text-indigo-300 hover:text-indigo-100 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
        </div>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Controle de Horas</h1>
                <p class="text-gray-400 text-sm sm:text-base">{{ $supervisedUser->name }} · {{ $supervisedUser->email }}</p>
            </div>
            <div class="flex items-center gap-4">
                <!-- Filtro de Mês -->
                <select onchange="changeMonth(this.value)"
                    class="bg-gray-800 border border-gray-700 rounded-lg pl-3 pr-8 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 appearance-none bg-no-repeat"
                    style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%239ca3af%22%3E%3Cpath fill-rule=%22evenodd%22 d=%22M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z%22 clip-rule=%22evenodd%22/%3E%3C/svg%3E'); background-position: right 0.5rem center; background-size: 1.25rem;">
                    @foreach($months as $month)
                        <option value="{{ $month['value'] }}" {{ $currentMonth === $month['value'] ? 'selected' : '' }}>
                            {{ $month['label'] }}
                        </option>
                    @endforeach
                </select>

                <!-- Exportar (se tiver permissão) -->
                @if($access->can_export)
                <div class="relative" x-data="{ open: false, showValues: true }">
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
                        <label class="flex items-center gap-2 px-4 py-2.5 border-b border-gray-700 cursor-pointer hover:bg-gray-700/50 transition-colors">
                            <input type="checkbox" x-model="showValues" class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-800"/>
                            <span class="text-xs text-gray-300">Mostrar valores financeiros</span>
                        </label>
                        <a :href="`{{ route('supervisor.export.pdf', $access->uuid) }}?month={{ $currentMonth }}&show_values=${showValues ? 1 : 0}`"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Relatório PDF
                        </a>
                        <a href="{{ route('supervisor.export.excel', [$access->uuid, 'month' => $currentMonth]) }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Planilha CSV
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cards de Resumo -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Horas Trabalhadas -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-cyan-500/20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400">Horas</span>
                </div>
                @php
                    $h = floor($stats['total_hours']);
                    $m = round(($stats['total_hours'] - $h) * 60);
                @endphp
                <p class="text-2xl font-bold text-white font-mono">{{ sprintf('%02d:%02d', $h, $m) }}</p>
                <p class="text-xs text-gray-500 mt-1">no mês</p>
            </div>

            <!-- Receita (condicional) -->
            @if($access->can_view_financials)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-emerald-500/20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400">Receita</span>
                </div>
                <p class="text-2xl font-bold text-white">R$ {{ number_format($stats['total_final_with_on_call'] ?? $stats['total_final'], 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">total do mês</p>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-yellow-500/20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400">Valor/Hora</span>
                </div>
                <p class="text-2xl font-bold text-white">R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">por hora</p>
            </div>
            @else
            <div class="col-span-2 bg-gray-900/50 border border-gray-800 border-dashed rounded-xl p-4 flex items-center justify-center">
                <p class="text-gray-600 text-sm text-center">Valores financeiros não visíveis</p>
            </div>
            @endif

            <!-- Lançamentos -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="bg-purple-500/20 p-1.5 rounded-lg">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400">Lançamentos</span>
                </div>
                <p class="text-2xl font-bold text-white">{{ $entries->total() }}</p>
                <p class="text-xs text-gray-500 mt-1">no mês</p>
            </div>
        </div>

        <!-- Tabela de Lançamentos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-800">
                <h2 class="text-base font-semibold text-white">Lançamentos de Horas</h2>
            </div>

            @if($entries->isEmpty())
                <div class="p-10 text-center">
                    <svg class="w-10 h-10 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500">Nenhum lançamento neste mês.</p>
                </div>
            @else
                <div class="divide-y divide-gray-800">
                    @foreach($entries as $entry)
                        @php
                            $eh = floor($entry->hours);
                            $em = round(($entry->hours - $eh) * 60);
                        @endphp
                        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                            <div class="sm:w-24 shrink-0">
                                <span class="text-gray-400 text-sm">{{ $entry->date->format('d/m/Y') }}</span>
                            </div>
                            <div class="sm:w-28 shrink-0">
                                <span class="text-gray-300 text-sm font-mono">
                                    {{ substr($entry->start_time, 0, 5) }} – {{ substr($entry->end_time, 0, 5) }}
                                </span>
                            </div>
                            <div class="sm:w-20 shrink-0">
                                <span class="text-cyan-400 text-sm font-mono font-medium">
                                    {{ sprintf('%02d:%02d', $eh, $em) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-gray-300 text-sm truncate block">{{ $entry->description }}</span>
                                @if($entry->project)
                                    <span class="text-xs text-gray-500">{{ $entry->project->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($entries->hasPages())
                    <div class="px-5 py-4 border-t border-gray-800">
                        {{ $entries->appends(['month' => $currentMonth])->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Sobreaviso (se houver) -->
        @if($onCallPeriods->isNotEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-800">
                <h2 class="text-base font-semibold text-white">Períodos de Sobreaviso</h2>
            </div>
            <div class="divide-y divide-gray-800">
                @foreach($onCallPeriods as $period)
                    @php
                        $och = floor($period->on_call_hours);
                        $ocm = round(($period->on_call_hours - $och) * 60);
                    @endphp
                    <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-300 text-sm">
                                {{ $period->start_datetime->format('d/m/Y H:i') }} até {{ $period->end_datetime->format('d/m/Y H:i') }}
                            </p>
                            @if($period->project)
                                <p class="text-xs text-gray-500">{{ $period->project->name }}</p>
                            @endif
                        </div>
                        <div class="text-right sm:text-left shrink-0">
                            <p class="text-yellow-400 text-sm font-mono font-medium">{{ sprintf('%02d:%02d', $och, $ocm) }}</p>
                            <p class="text-xs text-gray-500">sobreaviso</p>
                        </div>
                        @if($access->can_view_financials)
                        <div class="text-right shrink-0">
                            <p class="text-gray-300 text-sm">R$ {{ number_format($period->on_call_hours * $period->hourly_rate, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">R$ {{ number_format($period->hourly_rate, 2, ',', '.') }}/h</p>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    <script>
    function changeMonth(month) {
        const url = new URL(window.location.href);
        url.searchParams.set('month', month);
        window.location.href = url.toString();
    }
    </script>
</x-app-layout>
