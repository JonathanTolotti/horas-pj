<x-app-layout>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabeçalho -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Consolidação de Período</h1>
            <p class="text-gray-400 text-sm mt-1">Selecione os lançamentos e sobreavisos que deseja incluir no PDF.</p>
        </div>

        @if(!$isPremium)
        <div class="bg-yellow-900/30 border border-yellow-700 rounded-xl p-6 text-center">
            <svg class="w-10 h-10 mx-auto text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <p class="text-yellow-300 font-medium">Funcionalidade Premium</p>
            <p class="text-yellow-400/70 text-sm mt-1">Faça upgrade para consolidar e exportar períodos customizados.</p>
            <a href="{{ route('subscription.plans') }}" class="mt-4 inline-block bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-semibold px-6 py-2 rounded-lg text-sm transition-colors">
                Ver planos
            </a>
        </div>
        @else

        <!-- Formulário de filtros -->
        @php
            $allCompanyIds = $allCompanies->pluck('id')->map(fn($id) => (int)$id)->values()->toArray();
            $allProjectIds = $allProjects->pluck('id')->map(fn($id) => (int)$id)->values()->toArray();
            $selectedCompanyIds = !empty($filterCompanyIds) ? array_map('intval', $filterCompanyIds) : $allCompanyIds;
            $selectedProjectIds = !empty($filterProjectIds) ? array_map('intval', $filterProjectIds) : $allProjectIds;
            $hasActiveFilters = !empty($filterCompanyIds) || !empty($filterProjectIds);
        @endphp
        <form method="POST" action="{{ route('consolidation.filter') }}"
              x-data="consolidationFilters(
                  {{ json_encode($allCompanies->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()) }},
                  {{ json_encode($allProjects->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()) }},
                  {{ json_encode($selectedCompanyIds) }},
                  {{ json_encode($selectedProjectIds) }}
              )"
              class="bg-gray-900 border border-gray-700 rounded-xl p-5 mb-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

                <!-- Data início -->
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wide">Data início</label>
                    <div class="relative">
                        <input type="text" id="filter-start-date" name="start_date"
                            value="{{ $startDate ?? '' }}"
                            placeholder="dd/mm/aaaa" readonly
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-9 pr-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent cursor-pointer">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Data fim -->
                <div>
                    <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wide">Data fim</label>
                    <div class="relative">
                        <input type="text" id="filter-end-date" name="end_date"
                            value="{{ $endDate ?? '' }}"
                            placeholder="dd/mm/aaaa" readonly
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-9 pr-3 py-2.5 text-sm text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent cursor-pointer">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Empresas multi-select -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wide">Empresas</label>
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between bg-gray-800 border border-gray-700 rounded-lg pl-9 pr-3 py-2.5 text-sm text-left focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent relative"
                        :class="selectedCompanyIds.length > 0 ? 'text-white' : 'text-gray-500'">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span x-text="selectedCompanyIds.length === companies.length ? 'Todas' : selectedCompanyIds.length + ' selecionada(s)'"></span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <!-- Hidden inputs para envio -->
                    <template x-for="id in selectedCompanyIds" :key="id">
                        <input type="hidden" name="filter_company_ids[]" :value="id">
                    </template>
                    <!-- Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute z-30 left-0 top-full mt-1 w-full bg-gray-800 border border-gray-700 rounded-lg shadow-xl py-1 max-h-48 overflow-y-auto" style="display:none">
                        @forelse($allCompanies as $company)
                        <label class="flex items-center gap-2.5 px-3 py-2 hover:bg-gray-700 cursor-pointer text-sm text-gray-300 hover:text-white">
                            <input type="checkbox" :value="{{ $company->id }}"
                                :checked="selectedCompanyIds.includes({{ $company->id }})"
                                @change="toggleCompany({{ $company->id }})"
                                class="rounded border-gray-600 bg-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-800">
                            {{ $company->name }}
                        </label>
                        @empty
                        <p class="px-3 py-2 text-xs text-gray-500">Nenhuma empresa cadastrada</p>
                        @endforelse
                    </div>
                </div>

                <!-- Projetos multi-select -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wide">Projetos</label>
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between bg-gray-800 border border-gray-700 rounded-lg pl-9 pr-3 py-2.5 text-sm text-left focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent relative"
                        :class="selectedProjectIds.length > 0 ? 'text-white' : 'text-gray-500'">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span x-text="selectedProjectIds.length === projects.length ? 'Todos' : selectedProjectIds.length + ' selecionado(s)'"></span>
                        <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <!-- Hidden inputs para envio -->
                    <template x-for="id in selectedProjectIds" :key="id">
                        <input type="hidden" name="filter_project_ids[]" :value="id">
                    </template>
                    <!-- Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute z-30 left-0 top-full mt-1 w-full bg-gray-800 border border-gray-700 rounded-lg shadow-xl py-1 max-h-48 overflow-y-auto" style="display:none">
                        @forelse($allProjects as $project)
                        <label class="flex items-center gap-2.5 px-3 py-2 hover:bg-gray-700 cursor-pointer text-sm text-gray-300 hover:text-white">
                            <input type="checkbox" :value="{{ $project->id }}"
                                :checked="selectedProjectIds.includes({{ $project->id }})"
                                @change="toggleProject({{ $project->id }})"
                                class="rounded border-gray-600 bg-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-800">
                            {{ $project->name }}
                        </label>
                        @empty
                        <p class="px-3 py-2 text-xs text-gray-500">Nenhum projeto cadastrado</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="flex items-center justify-between gap-3 mt-4 pt-4 border-t border-gray-800">
                @if(isset($hasData) && $hasData)
                <span class="text-xs text-gray-500">
                    {{ $entries->count() }} lançamento(s) · {{ $onCallPeriods->count() }} sobreaviso(s)
                </span>
                @else
                <span></span>
                @endif
                <div class="flex gap-2">
                    @if($hasActiveFilters)
                    <form method="POST" action="{{ route('consolidation.clear') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm text-gray-400 hover:text-white border border-gray-700 hover:border-gray-500 rounded-lg transition-colors">
                            Limpar filtros
                        </button>
                    </form>
                    @endif
                    <button type="submit"
                        class="flex items-center gap-2 bg-cyan-600 hover:bg-cyan-500 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Buscar
                    </button>
                </div>
            </div>
        </form>

        @if($hasData)
        @php
            // Montar JSON para Alpine.js
            $entriesJson = $entries->map(fn($e) => [
                'id' => $e->id,
                'date' => $e->date->format('d/m/Y'),
                'weekday' => mb_substr(ucfirst($e->date->isoFormat('ddd')), 0, 3),
                'start_time' => $e->start_time ? substr($e->start_time, 0, 5) : '-',
                'end_time' => $e->end_time ? substr($e->end_time, 0, 5) : '-',
                'hours' => (float) $e->hours,
                'revenue' => (float) $e->computed_revenue,
                'project' => $e->project?->name ?? 'Sem projeto',
                'description' => $e->description,
                'selected' => true,
            ])->values()->toArray();

            $onCallJson = $onCallPeriods->map(fn($p) => [
                'id' => $p->id,
                'period' => $p->start_datetime->format('d/m H:i') . ' – ' . $p->end_datetime->format('d/m H:i'),
                'on_call_hours' => (float) $p->on_call_hours,
                'worked_hours' => (float) $p->worked_hours,
                'hourly_rate' => (float) $p->hourly_rate,
                'revenue' => (float) $p->computed_on_call_revenue,
                'project' => $p->project?->name ?? 'Geral',
                'description' => $p->description,
                'selected' => true,
            ])->values()->toArray();

            $companiesJson = $companies->map(function ($c) {
                $pct = 0;
                if ($c->projects->count() > 0) {
                    foreach ($c->projects as $proj) { $pct += $proj->pivot->percentage; }
                    $pct /= $c->projects->count();
                }
                return ['id' => $c->id, 'name' => $c->name, 'cnpj' => $c->cnpj, 'percentage' => $pct];
            })->values()->toArray();
        @endphp

        <div
            x-data="consolidation(
                {{ json_encode($entriesJson) }},
                {{ json_encode($onCallJson) }},
                {{ $extraValue }},
                {{ $discountValue }},
                {{ json_encode($companiesJson) }}
            )"
        >
            <form id="pdf-form" method="POST" action="{{ route('consolidation.pdf') }}">
                @csrf
            </form>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- Coluna esquerda: lançamentos + sobreavisos -->
                <div class="xl:col-span-2 space-y-5">

                    <!-- Lançamentos -->
                    <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-700">
                            <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Lançamentos
                                <span class="text-gray-500 font-normal" x-text="'(' + entries.length + ' registros)'"></span>
                            </h2>
                            <div class="flex gap-3 text-xs">
                                <button type="button" @click="selectAllEntries(true)" class="text-cyan-400 hover:text-cyan-300 transition-colors">Todos</button>
                                <button type="button" @click="selectAllEntries(false)" class="text-gray-500 hover:text-gray-400 transition-colors">Nenhum</button>
                            </div>
                        </div>

                        @if($entries->isEmpty())
                        <p class="text-gray-500 text-sm text-center py-8">Nenhum lançamento no período.</p>
                        @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-500 border-b border-gray-800">
                                        <th class="px-4 py-2 text-left w-8"></th>
                                        <th class="px-4 py-2 text-left">Data</th>
                                        <th class="px-4 py-2 text-center">Início</th>
                                        <th class="px-4 py-2 text-center">Fim</th>
                                        <th class="px-4 py-2 text-right">Horas</th>
                                        <th class="px-4 py-2 text-left">Projeto</th>
                                        <th class="px-4 py-2 text-left hidden lg:table-cell">Descrição</th>
                                        <th class="px-4 py-2 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(entry, index) in entries" :key="entry.id">
                                        <tr class="border-b border-gray-800/50 transition-colors"
                                            :class="entry.selected ? 'bg-transparent' : 'opacity-40'">
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox" x-model="entry.selected"
                                                    class="rounded border-gray-600 bg-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900 cursor-pointer">
                                            </td>
                                            <td class="px-4 py-2.5 text-gray-300 whitespace-nowrap">
                                                <span x-text="entry.date"></span>
                                                <span class="text-gray-600 text-xs ml-1" x-text="entry.weekday"></span>
                                            </td>
                                            <td class="px-4 py-2.5 text-center text-gray-400" x-text="entry.start_time"></td>
                                            <td class="px-4 py-2.5 text-center text-gray-400" x-text="entry.end_time"></td>
                                            <td class="px-4 py-2.5 text-right text-gray-300 font-mono" x-text="formatHours(entry.hours)"></td>
                                            <td class="px-4 py-2.5 text-gray-400 max-w-[120px] truncate" x-text="entry.project"></td>
                                            <td class="px-4 py-2.5 text-gray-500 hidden lg:table-cell max-w-[180px] truncate" x-text="entry.description"></td>
                                            <td class="px-4 py-2.5 text-right text-emerald-400 font-medium" x-text="formatMoney(entry.revenue)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>

                    <!-- Sobreavisos -->
                    @if($onCallPeriods->isNotEmpty())
                    <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-700">
                            <h2 class="text-sm font-semibold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Sobreavisos
                                <span class="text-gray-500 font-normal" x-text="'(' + onCallPeriods.length + ' períodos)'"></span>
                            </h2>
                            <div class="flex gap-3 text-xs">
                                <button type="button" @click="selectAllOnCall(true)" class="text-orange-400 hover:text-orange-300 transition-colors">Todos</button>
                                <button type="button" @click="selectAllOnCall(false)" class="text-gray-500 hover:text-gray-400 transition-colors">Nenhum</button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-500 border-b border-gray-800">
                                        <th class="px-4 py-2 text-left w-8"></th>
                                        <th class="px-4 py-2 text-left">Período</th>
                                        <th class="px-4 py-2 text-left">Projeto</th>
                                        <th class="px-4 py-2 text-center">Sobreaviso</th>
                                        <th class="px-4 py-2 text-center">Trabalhado</th>
                                        <th class="px-4 py-2 text-right">Valor/h</th>
                                        <th class="px-4 py-2 text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="period in onCallPeriods" :key="period.id">
                                        <tr class="border-b border-gray-800/50 transition-colors"
                                            :class="period.selected ? 'bg-transparent' : 'opacity-40'">
                                            <td class="px-4 py-2.5 text-center">
                                                <input type="checkbox" x-model="period.selected"
                                                    class="rounded border-gray-600 bg-gray-700 text-orange-500 focus:ring-orange-500 focus:ring-offset-gray-900 cursor-pointer">
                                            </td>
                                            <td class="px-4 py-2.5 text-gray-300 whitespace-nowrap text-xs" x-text="period.period"></td>
                                            <td class="px-4 py-2.5 text-gray-400" x-text="period.project"></td>
                                            <td class="px-4 py-2.5 text-center text-orange-400 font-mono font-medium" x-text="formatHours(period.on_call_hours)"></td>
                                            <td class="px-4 py-2.5 text-center text-gray-400 font-mono" x-text="formatHours(period.worked_hours)"></td>
                                            <td class="px-4 py-2.5 text-right text-gray-400" x-text="formatMoney(period.hourly_rate)"></td>
                                            <td class="px-4 py-2.5 text-right text-orange-400 font-medium" x-text="formatMoney(period.revenue)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Coluna direita: resumo -->
                <div class="space-y-4">
                    <div class="bg-gray-900 border border-gray-700 rounded-xl p-5 sticky top-4">
                        <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M15 7h.01M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
                            </svg>
                            Resumo
                        </h2>

                        <!-- Período -->
                        <div class="text-xs text-gray-500 mb-4">
                            {{ $startDateFormatted }} &rarr; {{ $endDateFormatted }}
                        </div>

                        <!-- Horas e receita normais -->
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-400">
                                <span>Horas selecionadas</span>
                                <span class="font-mono text-gray-300" x-text="formatHours(selectedHours)"></span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>Receita (horas)</span>
                                <span class="text-emerald-400" x-text="formatMoney(selectedRevenue)"></span>
                            </div>

                            <!-- Sobreaviso -->
                            <template x-if="selectedOnCallHours > 0">
                                <div>
                                    <div class="flex justify-between text-gray-400 mt-1">
                                        <span>Sobreaviso</span>
                                        <span class="font-mono text-orange-400" x-text="formatHours(selectedOnCallHours)"></span>
                                    </div>
                                    <div class="flex justify-between text-gray-400">
                                        <span>Receita (sobreaviso)</span>
                                        <span class="text-orange-400" x-text="formatMoney(selectedOnCallRevenue)"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- Extra / desconto -->
                            @if($extraValue > 0 || $discountValue > 0)
                            <div class="border-t border-gray-800 pt-2 mt-2 space-y-1">
                                @if($extraValue > 0)
                                <div class="flex justify-between text-gray-400 text-xs">
                                    <span>Acréscimo</span>
                                    <span class="text-emerald-400">+{{ number_format($extraValue, 2, ',', '.') }}</span>
                                </div>
                                @endif
                                @if($discountValue > 0)
                                <div class="flex justify-between text-gray-400 text-xs">
                                    <span>Desconto</span>
                                    <span class="text-red-400">-{{ number_format($discountValue, 2, ',', '.') }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Total -->
                            <div class="border-t border-gray-700 pt-3 mt-1">
                                <div class="flex justify-between text-white font-semibold">
                                    <span>Total</span>
                                    <span class="text-cyan-400 text-base" x-text="formatMoney(totalFinal)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Por empresa -->
                        <template x-if="companyRevenues.length > 0">
                            <div class="mt-4 border-t border-gray-800 pt-4">
                                <div class="text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">Por empresa</div>
                                <div class="space-y-1.5">
                                    <template x-for="company in companyRevenues" :key="company.id">
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-400 truncate mr-2" x-text="company.name"></span>
                                            <span class="text-gray-300 font-medium whitespace-nowrap" x-text="formatMoney(company.revenue)"></span>
                                        </div>
                                    </template>
                                    <template x-if="unassignedRevenue > 0">
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-600">Não atribuído</span>
                                            <span class="text-gray-500" x-text="formatMoney(unassignedRevenue)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Botão PDF -->
                        <div class="mt-5 space-y-2">
                            <button type="button" @click="submitPdf()"
                                x-bind:disabled="selectedHours === 0 && selectedOnCallHours === 0"
                                class="w-full flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-500 disabled:bg-gray-700 disabled:text-gray-500 text-white font-medium px-4 py-2.5 rounded-lg text-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Gerar PDF
                            </button>
                            <p class="text-xs text-gray-600 text-center" x-show="selectedHours === 0 && selectedOnCallHours === 0">Selecione ao menos um item</p>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- fim x-data -->

        @else
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400">Selecione o período acima para visualizar os lançamentos.</p>
        </div>
        @endif

        @endif {{-- isPremium --}}
    </div>
</div>

<script>
// Flatpickr — respeita dark/light mode via CSS do app
document.addEventListener('DOMContentLoaded', function () {
    const ptLocale = flatpickr.l10ns.pt || {};
    const fpBase = {
        locale: ptLocale,
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: false,
        disableMobile: true,
    };

    let endPicker;
    const startPicker = flatpickr('#filter-start-date', {
        ...fpBase,
        onChange: function(dates) {
            if (endPicker && dates[0]) endPicker.set('minDate', dates[0]);
        }
    });

    endPicker = flatpickr('#filter-end-date', {
        ...fpBase,
        onChange: function(dates) {
            if (startPicker && dates[0]) startPicker.set('maxDate', dates[0]);
        }
    });
});

function consolidationFilters(companies, projects, selectedCompanyIds, selectedProjectIds) {
    return {
        companies,
        projects,
        selectedCompanyIds,
        selectedProjectIds,
        toggleCompany(id) {
            const idx = this.selectedCompanyIds.indexOf(id);
            if (idx === -1) this.selectedCompanyIds.push(id);
            else this.selectedCompanyIds.splice(idx, 1);
        },
        toggleProject(id) {
            const idx = this.selectedProjectIds.indexOf(id);
            if (idx === -1) this.selectedProjectIds.push(id);
            else this.selectedProjectIds.splice(idx, 1);
        },
    };
}

function consolidation(entries, onCallPeriods, extraValue, discountValue, companies) {
    return {
        entries: entries,
        onCallPeriods: onCallPeriods,
        extraValue: extraValue,
        discountValue: discountValue,
        companies: companies,

        get selectedHours() {
            return this.entries
                .filter(e => e.selected)
                .reduce((s, e) => s + e.hours, 0);
        },
        get selectedRevenue() {
            return this.entries
                .filter(e => e.selected)
                .reduce((s, e) => s + e.revenue, 0);
        },
        get selectedOnCallHours() {
            return this.onCallPeriods
                .filter(p => p.selected)
                .reduce((s, p) => s + p.on_call_hours, 0);
        },
        get selectedOnCallRevenue() {
            return this.onCallPeriods
                .filter(p => p.selected)
                .reduce((s, p) => s + p.revenue, 0);
        },
        get totalFinal() {
            return Math.round((this.selectedRevenue + this.selectedOnCallRevenue + this.extraValue - this.discountValue) * 100) / 100;
        },
        get companyRevenues() {
            const total = this.totalFinal;
            if (this.companies.length === 0 || total <= 0) return [];

            const totalPct = this.companies.reduce((s, c) => s + c.percentage, 0);
            if (totalPct <= 0) return [];

            let distributed = 0;
            const result = this.companies.map((c, idx) => {
                const rev = Math.round(total * (c.percentage / totalPct) * 100) / 100;
                distributed += rev;
                return { id: c.id, name: c.name, revenue: rev };
            });

            // Ajuste de arredondamento na última empresa
            if (result.length > 0) {
                const diff = Math.round((total - distributed) * 100) / 100;
                if (Math.abs(diff) > 0 && Math.abs(diff) <= 0.03) {
                    result[result.length - 1].revenue += diff;
                }
            }

            return result;
        },
        get unassignedRevenue() {
            const assigned = this.companyRevenues.reduce((s, c) => s + c.revenue, 0);
            const diff = Math.round((this.totalFinal - assigned) * 100) / 100;
            return Math.abs(diff) <= 0.05 ? 0 : diff;
        },

        selectAllEntries(val) {
            this.entries = this.entries.map(e => ({ ...e, selected: val }));
        },
        selectAllOnCall(val) {
            this.onCallPeriods = this.onCallPeriods.map(p => ({ ...p, selected: val }));
        },

        submitPdf() {
            const form = document.getElementById('pdf-form');
            form.querySelectorAll('input.dynamic').forEach(el => el.remove());

            const add = (name, value) => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = name;
                inp.value = value;
                inp.className = 'dynamic';
                form.appendChild(inp);
            };

            this.entries.filter(e => e.selected).forEach(e => add('entry_ids[]', e.id));
            this.onCallPeriods.filter(p => p.selected).forEach(p => add('on_call_ids[]', p.id));

            form.submit();
        },

        formatHours(hours) {
            const h = Math.floor(hours);
            const m = Math.round((hours - h) * 60);
            return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        },
        formatMoney(value) {
            return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    };
}
</script>
</x-app-layout>
