<x-app-layout>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Relatórios</h1>
            <p class="text-gray-400 text-sm sm:text-base">Exporte seus dados em diferentes formatos</p>
        </div>

        <!-- Filtros -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtros
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Período</label>
                    <select id="month-filter"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        @foreach($months as $month)
                            <option value="{{ $month['value'] }}" {{ $currentMonth === $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Projeto (opcional)</label>
                    <select id="project-filter"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <option value="">Todos os projetos</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Relatórios Disponíveis -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Relatório PDF -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-red-500/50 transition-all group">
                <div class="flex items-start gap-4">
                    <div class="bg-red-500/20 p-3 rounded-lg group-hover:bg-red-500/30 transition-colors">
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-1">Relatório Completo</h3>
                        <p class="text-gray-400 text-sm mb-4">Relatório mensal em PDF com resumo de horas, valores e todos os lançamentos detalhados.</p>
                        @if($isPremium)
                            <button onclick="downloadPdf()"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Baixar PDF
                            </button>
                        @else
                            <button onclick="showPremiumModal('exportação de PDF')"
                                class="w-full bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Premium
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Planilha CSV/Excel -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-emerald-500/50 transition-all group">
                <div class="flex items-start gap-4">
                    <div class="bg-emerald-500/20 p-3 rounded-lg group-hover:bg-emerald-500/30 transition-colors">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-1">Planilha CSV</h3>
                        <p class="text-gray-400 text-sm mb-4">Exporte seus dados em formato CSV para abrir no Excel, Google Sheets ou qualquer planilha.</p>
                        @if($isPremium)
                            <button onclick="downloadExcel()"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Baixar CSV
                            </button>
                        @else
                            <button onclick="showPremiumModal('exportação de planilhas')"
                                class="w-full bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Premium
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Relatório para NF -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-amber-500/50 transition-all group md:col-span-2">
                <div class="flex items-start gap-4">
                    <div class="bg-amber-500/20 p-3 rounded-lg group-hover:bg-amber-500/30 transition-colors">
                        <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-1">Relatório para Nota Fiscal</h3>
                        <p class="text-gray-400 text-sm mb-4">Gere um relatório formatado para emissão de Nota Fiscal, com detalhamento de horas por empresa.</p>

                        @if($isPremium)
                            @if($companies->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($companies as $company)
                                        <button onclick="downloadNf({{ $company->id }})"
                                            class="bg-amber-600/20 hover:bg-amber-600 border border-amber-500/30 hover:border-amber-500 text-amber-300 hover:text-white px-4 py-3 rounded-lg font-medium transition-all text-left">
                                            <div class="font-semibold">{{ $company->name }}</div>
                                            <div class="text-xs opacity-75 font-mono">{{ $company->cnpj }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-500">
                                    <p>Nenhuma empresa cadastrada.</p>
                                    <a href="{{ route('settings') }}" class="text-cyan-400 hover:text-cyan-300 text-sm">Cadastrar empresa</a>
                                </div>
                            @endif
                        @else
                            <button onclick="showPremiumModal('relatório para NF')"
                                class="w-full sm:w-auto bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Premium
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Relatório Anual para IR -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-purple-500/50 transition-all group md:col-span-2">
                <div class="flex items-start gap-4">
                    <div class="bg-purple-500/20 p-3 rounded-lg group-hover:bg-purple-500/30 transition-colors">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-4 mb-1">
                            <h3 class="text-lg font-semibold text-white">Relatório Anual para IR</h3>
                            <span class="bg-purple-500/20 text-purple-300 text-xs px-2 py-1 rounded-full font-medium">Novo</span>
                        </div>
                        <p class="text-gray-400 text-sm mb-4">Relatório completo do ano para declaração de Imposto de Renda. Inclui faturamento mensal, total por empresa/CNPJ, média mensal e comparativos.</p>

                        @if($isPremium)
                            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                                <div class="flex-1 sm:flex-none">
                                    <select id="year-filter"
                                        class="w-full sm:w-auto bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <button onclick="downloadAnnualReport()"
                                    class="flex-1 sm:flex-none bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Gerar Relatório Anual
                                </button>
                            </div>
                            <div class="mt-3 text-xs text-gray-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Inclui: faturamento mês a mês, total por CNPJ, média mensal, melhor/pior mês
                                </span>
                            </div>
                        @else
                            <button onclick="showPremiumModal('relatório anual para IR')"
                                class="w-full sm:w-auto bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Premium
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Info Premium -->
        @if(!$isPremium)
        <div class="bg-gradient-to-r from-purple-900/30 to-cyan-900/30 border border-purple-500/30 rounded-xl p-6 text-center">
            <div class="inline-flex items-center justify-center bg-purple-500/20 p-3 rounded-full mb-4">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Desbloqueie todos os relatórios</h3>
            <p class="text-gray-400 mb-4">Com o plano Premium você pode exportar seus dados em PDF, CSV e gerar relatórios formatados para Nota Fiscal.</p>
            <a href="{{ route('subscription.plans') }}"
                class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Ver planos
            </a>
        </div>
        @endif

    </div>

    @push('scripts')
    <script>
        function getFilters() {
            const month = document.getElementById('month-filter').value;
            const projectId = document.getElementById('project-filter').value;
            let params = `month=${month}`;
            if (projectId) {
                params += `&project_id=${projectId}`;
            }
            return params;
        }

        function downloadPdf() {
            window.location.href = `{{ route('export.pdf') }}?${getFilters()}`;
        }

        function downloadExcel() {
            window.location.href = `{{ route('export.excel') }}?${getFilters()}`;
        }

        function downloadNf(companyId) {
            const month = document.getElementById('month-filter').value;
            window.location.href = `{{ route('export.nf') }}?month=${month}&company_id=${companyId}`;
        }

        function downloadAnnualReport() {
            const year = document.getElementById('year-filter').value;
            window.location.href = `{{ route('export.annual') }}?year=${year}`;
        }
    </script>
    @endpush

    <!-- Premium Modal -->
    <x-premium-modal feature="exportação de relatórios" />
</x-app-layout>
