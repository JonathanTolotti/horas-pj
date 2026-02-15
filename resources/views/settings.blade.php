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
                <button id="confirm-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Projeto -->
    <div id="project-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeProjectModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-cyan-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="project-modal-title">Novo Projeto</h3>
            </div>
            <form id="project-form" onsubmit="return false;">
                <input type="hidden" id="project-id" value="">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nome do Projeto</label>
                        <input type="text" id="project-name" placeholder="Ex: Projeto Alpha"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="project-active" checked
                                class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300">Ativo</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="project-default"
                                class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300">Padrao</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="closeProjectModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveProject()" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Empresa -->
    <div id="company-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeCompanyModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-blue-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="company-modal-title">Nova Empresa</h3>
            </div>
            <form id="company-form" onsubmit="return false;">
                <input type="hidden" id="company-id" value="">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nome da Empresa</label>
                        <input type="text" id="company-name" placeholder="Ex: Empresa Alpha LTDA"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">CNPJ</label>
                        <input type="text" id="company-cnpj" placeholder="00.000.000/0000-00" maxlength="18"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                            oninput="formatCnpj(this)"/>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="company-active" checked
                                class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300">Ativa</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="closeCompanyModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveCompany()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Vinculo Empresa-Projeto -->
    <div id="link-company-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeLinkCompanyModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-purple-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="link-company-modal-title">Vincular Empresa</h3>
            </div>
            <form id="link-company-form" onsubmit="return false;">
                <input type="hidden" id="link-project-id" value="">
                <input type="hidden" id="link-company-id" value="">
                <input type="hidden" id="link-edit-mode" value="false">
                <div class="space-y-4">
                    <div id="link-company-select-wrapper">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Empresa</label>
                        <select id="link-company-select"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Selecione uma empresa</option>
                            @foreach($companies as $company)
                                @if($company->active)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div id="link-company-name-display" class="hidden">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Empresa</label>
                        <p class="text-white font-medium" id="link-company-name-text"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Porcentagem (%)</label>
                        <input type="number" id="link-percentage" placeholder="100" min="0.01" max="100" step="0.01"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"/>
                        <p class="text-xs text-gray-500 mt-1">Porcentagem do faturamento deste projeto para esta empresa</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="closeLinkCompanyModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveLinkCompany()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Configuracoes</h1>
            <p class="text-gray-400 text-sm sm:text-base">Gerencie valores e projetos</p>
        </div>

        <!-- Valores -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Valores
            </h2>

            <form id="settings-form" onsubmit="return false;">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Valor por Hora</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                            <input type="text" id="hourly-rate" inputmode="decimal"
                                value="{{ number_format($settings->hourly_rate, 2, ',', '.') }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-12 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                onkeyup="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Acréscimo Mensal</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400">+R$</span>
                            <input type="text" id="extra-value" inputmode="decimal"
                                value="{{ number_format($settings->extra_value, 2, ',', '.') }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-14 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                onkeyup="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)"/>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ex: Home Office, ajuda de custo</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Desconto Mensal</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-red-400">-R$</span>
                            <input type="text" id="discount-value" inputmode="decimal"
                                value="{{ number_format($settings->discount_value ?? 0, 2, ',', '.') }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-14 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                onkeyup="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)"/>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ex: Impostos, taxas, deduções</p>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="button" onclick="saveSettings()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2 hover:shadow-lg hover:shadow-emerald-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar Valores
                    </button>
                </div>
            </form>
        </div>

        <!-- Empresas (CNPJs) -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Empresas (CNPJs)
                </h2>
                @if($canAddCompany)
                    <button onclick="openCompanyModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-blue-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nova Empresa
                    </button>
                @else
                    <button onclick="window.dispatchEvent(new CustomEvent('open-premium-modal'))"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm"
                        title="Limite de {{ $companyLimit }} empresa(s) atingido">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nova Empresa
                        <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </button>
                @endif
            </div>

            <div id="companies-list" class="space-y-3">
                @forelse($companies as $company)
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors" data-company-id="{{ $company->id }}">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                            <div class="flex items-center gap-2">
                                @if($company->active)
                                    <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                                @else
                                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                @endif
                                <span class="text-white font-medium">{{ $company->name }}</span>
                            </div>
                            <span class="text-gray-400 text-sm font-mono">{{ $company->cnpj }}</span>
                            @if(!$company->active)
                                <span class="text-xs bg-gray-500/20 text-gray-400 px-2 py-1 rounded-full">Inativa</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="editCompany({{ $company->id }}, '{{ addslashes($company->name) }}', '{{ $company->cnpj }}', {{ $company->active ? 'true' : 'false' }})"
                                class="p-2 text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="deleteCompany({{ $company->id }})"
                                class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div id="no-companies" class="text-center py-8 text-gray-500">
                        Nenhuma empresa cadastrada. Cadastre empresas para distribuir o faturamento no dashboard.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Projetos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Projetos
                </h2>
                @if($canAddProject)
                    <button onclick="openProjectModal()"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-cyan-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Projeto
                    </button>
                @else
                    <button onclick="window.dispatchEvent(new CustomEvent('open-premium-modal'))"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm"
                        title="Limite de {{ $projectLimit }} projeto(s) atingido">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Projeto
                        <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </button>
                @endif
            </div>

            <div id="projects-list" class="space-y-4">
                @forelse($projects as $project)
                    <div class="p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors" data-project-id="{{ $project->id }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    @if($project->active)
                                        <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                                    @else
                                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                    @endif
                                    <span class="text-white font-medium">{{ $project->name }}</span>
                                </div>
                                @if($project->is_default)
                                    <span class="text-xs bg-cyan-500/20 text-cyan-300 px-2 py-1 rounded-full">Padrao</span>
                                @endif
                                @if(!$project->active)
                                    <span class="text-xs bg-gray-500/20 text-gray-400 px-2 py-1 rounded-full">Inativo</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="openLinkCompanyModal({{ $project->id }})"
                                    class="p-2 text-gray-400 hover:text-purple-400 transition-colors" title="Vincular empresa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </button>
                                <button onclick="editProject({{ $project->id }}, '{{ addslashes($project->name) }}', {{ $project->active ? 'true' : 'false' }}, {{ $project->is_default ? 'true' : 'false' }})"
                                    class="p-2 text-gray-400 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteProject({{ $project->id }})"
                                    class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- Empresas vinculadas -->
                        <div class="mt-3 pt-3 border-t border-gray-700" id="project-companies-{{ $project->id }}">
                            @if($project->companies->count() > 0)
                                <p class="text-xs text-gray-500 mb-2">Empresas vinculadas:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($project->companies as $company)
                                        <div class="flex items-center gap-2 bg-purple-500/10 border border-purple-500/30 rounded-lg px-3 py-1.5">
                                            <span class="text-purple-300 text-sm">{{ $company->name }}</span>
                                            <span class="text-purple-400 text-xs font-medium">({{ number_format($company->pivot->percentage, 2) }}%)</span>
                                            <button onclick="editLinkCompany({{ $project->id }}, {{ $company->id }}, '{{ addslashes($company->name) }}', {{ $company->pivot->percentage }})"
                                                class="text-purple-400 hover:text-purple-300 transition-colors p-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button onclick="unlinkCompany({{ $project->id }}, {{ $company->id }})"
                                                class="text-red-400 hover:text-red-300 transition-colors p-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-500">Nenhuma empresa vinculada. O faturamento ira para "Nao Atribuido".</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div id="no-projects" class="text-center py-8 text-gray-500">
                        Nenhum projeto cadastrado. Crie um novo projeto para comecar.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Premium Modal -->
    <x-premium-modal feature="criacao de multiplos projetos e empresas" />

    @push('scripts')
    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Formatacao de moeda
        function formatCurrencyInput(input) {
            let value = input.value.replace(/\D/g, '');
            if (value === '') {
                input.value = '0,00';
                return;
            }
            value = (parseInt(value) / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            input.value = value;
        }

        function parseCurrencyValue(value) {
            return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
        }

        // Toast System
        const TOAST_TYPES = { SUCCESS: 'success', ERROR: 'error', WARNING: 'warning', INFO: 'info' };

        function showToast(message, type = TOAST_TYPES.INFO, duration = 4000) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'toast-item';
            toast.style.cssText = 'transform: translateX(100%); opacity: 0; transition: all 0.3s ease-out;';

            const styles = {
                success: { bg: '#064e3b', border: '#10b981', text: '#a7f3d0', icon: '#34d399' },
                error: { bg: '#7f1d1d', border: '#ef4444', text: '#fecaca', icon: '#f87171' },
                warning: { bg: '#78350f', border: '#f59e0b', text: '#fde68a', icon: '#fbbf24' },
                info: { bg: '#164e63', border: '#06b6d4', text: '#a5f3fc', icon: '#22d3ee' }
            };

            const style = styles[type] || styles.info;
            const icons = {
                success: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
                error: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
                warning: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
                info: `<svg style="width:20px;height:20px;color:${style.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            };

            toast.innerHTML = `<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:10px;border:2px solid ${style.border};background:${style.bg};color:${style.text};box-shadow:0 10px 25px rgba(0,0,0,0.4),0 0 20px ${style.border}40;font-size:14px;font-weight:500;min-width:280px;max-width:400px;">${icons[type] || icons.info}<span style="flex:1">${message}</span><button onclick="this.closest('.toast-item').remove()" style="background:none;border:none;cursor:pointer;opacity:0.7;padding:4px;color:${style.text}"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>`;

            container.appendChild(toast);
            requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; });
            setTimeout(() => { toast.style.transform = 'translateX(100%)'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, duration);
        }

        // Confirm Modal
        let confirmCallback = null;

        function showConfirm(message, callback, title = 'Confirmar') {
            const modal = document.getElementById('confirm-modal');
            document.getElementById('confirm-title').textContent = title;
            document.getElementById('confirm-message').textContent = message;
            confirmCallback = callback;
            document.getElementById('confirm-btn').onclick = function() { closeConfirmModal(); if (confirmCallback) confirmCallback(); };
            modal.classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirm-modal').classList.add('hidden');
            confirmCallback = null;
        }

        // Project Modal
        function openProjectModal(id = null, name = '', active = true, isDefault = false) {
            document.getElementById('project-modal-title').textContent = id ? 'Editar Projeto' : 'Novo Projeto';
            document.getElementById('project-id').value = id || '';
            document.getElementById('project-name').value = name;
            document.getElementById('project-active').checked = active;
            document.getElementById('project-default').checked = isDefault;
            document.getElementById('project-modal').classList.remove('hidden');
        }

        function closeProjectModal() {
            document.getElementById('project-modal').classList.add('hidden');
        }

        function editProject(id, name, active, isDefault) {
            openProjectModal(id, name, active, isDefault);
        }

        // Save Settings
        async function saveSettings() {
            const hourlyRate = parseCurrencyValue(document.getElementById('hourly-rate').value);
            const extraValue = parseCurrencyValue(document.getElementById('extra-value').value);
            const discountValue = parseCurrencyValue(document.getElementById('discount-value').value);

            if (hourlyRate < 0 || extraValue < 0 || discountValue < 0) {
                showToast('Os valores não podem ser negativos!', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const response = await fetch('/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ hourly_rate: hourlyRate, extra_value: extraValue, discount_value: discountValue })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao salvar');
                showToast(data.message, TOAST_TYPES.SUCCESS);
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        // Save Project
        async function saveProject() {
            const id = document.getElementById('project-id').value;
            const name = document.getElementById('project-name').value;
            const active = document.getElementById('project-active').checked;
            const isDefault = document.getElementById('project-default').checked;

            if (!name.trim()) {
                showToast('Por favor, informe o nome do projeto!', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const url = id ? `/projects/${id}` : '/projects';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ name, active, is_default: isDefault })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao salvar');

                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeProjectModal();
                location.reload();
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        // Delete Project
        function deleteProject(id) {
            showConfirm('Deseja realmente excluir este projeto?', async () => {
                try {
                    const response = await fetch(`/projects/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erro ao excluir');

                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    document.querySelector(`[data-project-id="${id}"]`).remove();

                    if (document.querySelectorAll('[data-project-id]').length === 0) {
                        document.getElementById('projects-list').innerHTML = '<div id="no-projects" class="text-center py-8 text-gray-500">Nenhum projeto cadastrado. Crie um novo projeto para comecar.</div>';
                    }
                } catch (error) {
                    showToast(error.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir Projeto');
        }

        // Close modals on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeConfirmModal();
                closeProjectModal();
                closeCompanyModal();
                closeLinkCompanyModal();
            }
        });

        // CNPJ Mask
        function formatCnpj(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 14) value = value.substring(0, 14);

            if (value.length > 12) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
            } else if (value.length > 8) {
                value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d*).*/, '$1.$2.$3/$4');
            } else if (value.length > 5) {
                value = value.replace(/^(\d{2})(\d{3})(\d*).*/, '$1.$2.$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d*).*/, '$1.$2');
            }

            input.value = value;
        }

        // Company Modal
        function openCompanyModal(id = null, name = '', cnpj = '', active = true) {
            document.getElementById('company-modal-title').textContent = id ? 'Editar Empresa' : 'Nova Empresa';
            document.getElementById('company-id').value = id || '';
            document.getElementById('company-name').value = name;
            document.getElementById('company-cnpj').value = cnpj;
            document.getElementById('company-active').checked = active;
            document.getElementById('company-modal').classList.remove('hidden');
        }

        function closeCompanyModal() {
            document.getElementById('company-modal').classList.add('hidden');
        }

        function editCompany(id, name, cnpj, active) {
            openCompanyModal(id, name, cnpj, active);
        }

        async function saveCompany() {
            const id = document.getElementById('company-id').value;
            const name = document.getElementById('company-name').value;
            const cnpj = document.getElementById('company-cnpj').value;
            const active = document.getElementById('company-active').checked;

            if (!name.trim()) {
                showToast('Por favor, informe o nome da empresa!', TOAST_TYPES.WARNING);
                return;
            }

            if (!/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/.test(cnpj)) {
                showToast('CNPJ invalido! Use o formato XX.XXX.XXX/XXXX-XX', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const url = id ? `/companies/${id}` : '/companies';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ name, cnpj, active })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao salvar');

                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeCompanyModal();
                location.reload();
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        function deleteCompany(id) {
            showConfirm('Deseja realmente excluir esta empresa?', async () => {
                try {
                    const response = await fetch(`/companies/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erro ao excluir');

                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    document.querySelector(`[data-company-id="${id}"]`).remove();

                    if (document.querySelectorAll('[data-company-id]').length === 0) {
                        document.getElementById('companies-list').innerHTML = '<div id="no-companies" class="text-center py-8 text-gray-500">Nenhuma empresa cadastrada. Cadastre empresas para distribuir o faturamento no dashboard.</div>';
                    }
                } catch (error) {
                    showToast(error.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir Empresa');
        }

        // Link Company Modal
        function openLinkCompanyModal(projectId) {
            document.getElementById('link-company-modal-title').textContent = 'Vincular Empresa';
            document.getElementById('link-project-id').value = projectId;
            document.getElementById('link-company-id').value = '';
            document.getElementById('link-edit-mode').value = 'false';
            document.getElementById('link-company-select').value = '';
            document.getElementById('link-percentage').value = '';
            document.getElementById('link-company-select-wrapper').classList.remove('hidden');
            document.getElementById('link-company-name-display').classList.add('hidden');
            document.getElementById('link-company-modal').classList.remove('hidden');
        }

        function editLinkCompany(projectId, companyId, companyName, percentage) {
            document.getElementById('link-company-modal-title').textContent = 'Editar Porcentagem';
            document.getElementById('link-project-id').value = projectId;
            document.getElementById('link-company-id').value = companyId;
            document.getElementById('link-edit-mode').value = 'true';
            document.getElementById('link-percentage').value = percentage;
            document.getElementById('link-company-name-text').textContent = companyName;
            document.getElementById('link-company-select-wrapper').classList.add('hidden');
            document.getElementById('link-company-name-display').classList.remove('hidden');
            document.getElementById('link-company-modal').classList.remove('hidden');
        }

        function closeLinkCompanyModal() {
            document.getElementById('link-company-modal').classList.add('hidden');
        }

        async function saveLinkCompany() {
            const projectId = document.getElementById('link-project-id').value;
            const companyId = document.getElementById('link-company-id').value || document.getElementById('link-company-select').value;
            const percentage = parseFloat(document.getElementById('link-percentage').value);
            const isEditMode = document.getElementById('link-edit-mode').value === 'true';

            if (!companyId) {
                showToast('Por favor, selecione uma empresa!', TOAST_TYPES.WARNING);
                return;
            }

            if (!percentage || percentage <= 0 || percentage > 100) {
                showToast('Por favor, informe uma porcentagem valida (0.01 a 100)!', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const url = isEditMode
                    ? `/projects/${projectId}/companies/${companyId}`
                    : `/projects/${projectId}/companies`;
                const method = isEditMode ? 'PUT' : 'POST';

                const body = isEditMode
                    ? { percentage }
                    : { company_id: companyId, percentage };

                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao salvar');

                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeLinkCompanyModal();
                location.reload();
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        function unlinkCompany(projectId, companyId) {
            showConfirm('Deseja remover o vinculo desta empresa com o projeto?', async () => {
                try {
                    const response = await fetch(`/projects/${projectId}/companies/${companyId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erro ao remover');

                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    location.reload();
                } catch (error) {
                    showToast(error.message, TOAST_TYPES.ERROR);
                }
            }, 'Remover Vinculo');
        }
    </script>
    @endpush
</x-app-layout>
