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
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Descricao Padrao</label>
                        <input type="text" id="project-default-description" placeholder="Ex: Desenvolvimento de features"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"/>
                        <p class="text-xs text-gray-500 mt-1">Usada ao salvar tracking automaticamente</p>
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
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Configurações</h1>
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Valor por Hora (Sobreaviso)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-orange-400">R$</span>
                            <input type="text" id="on-call-hourly-rate" inputmode="decimal"
                                value="{{ number_format($settings->on_call_hourly_rate ?? 0, 2, ',', '.') }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-12 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                onkeyup="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)"/>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ex: 1/3 do valor normal (deixe 0 para calcular automaticamente)</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
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

                <!-- Nota sobre ajustes mensais -->
                <div class="mt-4 p-3 bg-indigo-900/20 border border-indigo-700/40 rounded-lg flex items-start gap-2">
                    <svg class="w-4 h-4 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-indigo-300">
                        Estes são os valores <strong>padrão</strong> aplicados a meses sem ajuste específico.
                        Para definir valores diferentes em um mês individual, use o botão de edição no card
                        "Ajustes Mensais" do dashboard.
                    </p>
                </div>

                <!-- Opcao de Auto-Save Tracking -->
                <div class="mt-6 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="auto-save-tracking" {{ $settings->auto_save_tracking ? 'checked' : '' }}
                            class="w-5 h-5 mt-0.5 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                        <div>
                            <span class="text-white font-medium">Salvar automaticamente ao parar tracking</span>
                            <p class="text-xs text-gray-500 mt-1">
                                Quando ativado, ao parar o tracking o lançamento sera salvo automaticamente
                                usando o projeto padrão e sua descrição padrão.
                            </p>
                        </div>
                    </label>
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
                                <button onclick="editProject({{ $project->id }}, '{{ addslashes($project->name) }}', {{ $project->active ? 'true' : 'false' }}, {{ $project->is_default ? 'true' : 'false' }}, '{{ addslashes($project->default_description ?? '') }}')"
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

        <!-- Avisos e Lembretes -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Avisos e Lembretes
                </h2>
                <button onclick="openNoticeModal()"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-amber-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Aviso
                </button>
            </div>

            <div id="notices-list" class="space-y-3">
                @forelse($notices as $notice)
                    @php
                        $colorDot = match($notice->color) {
                            'yellow' => 'bg-yellow-400',
                            'red'    => 'bg-red-400',
                            'green'  => 'bg-green-400',
                            default  => 'bg-blue-400',
                        };
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors" data-notice-id="{{ $notice->id }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-3 h-3 rounded-full shrink-0 {{ $colorDot }}"></span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-white font-medium">{{ $notice->title }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $notice->type === 'persistent' ? 'bg-purple-500/20 text-purple-300' : 'bg-cyan-500/20 text-cyan-300' }}">
                                        {{ $notice->type === 'persistent' ? 'Persistente' : 'Uma vez' }}
                                    </span>
                                    @if(!$notice->is_active)
                                        <span class="text-xs bg-gray-500/20 text-gray-400 px-2 py-0.5 rounded-full">Inativo</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $notice->start_date->format('d/m/Y') }}
                                    @if($notice->end_date)
                                        até {{ $notice->end_date->format('d/m/Y') }}
                                    @else
                                        · sem data de encerramento
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <button onclick='editNotice({{ $notice->id }}, {{ json_encode($notice->title) }}, {{ json_encode($notice->message) }}, "{{ $notice->type }}", "{{ $notice->color }}", "{{ $notice->start_date->format('Y-m-d') }}", "{{ $notice->end_date?->format('Y-m-d') ?? '' }}", {{ $notice->is_active ? 'true' : 'false' }})'
                                class="p-2 text-gray-400 hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="deleteNotice({{ $notice->id }})"
                                class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div id="no-notices" class="text-center py-8 text-gray-500">
                        Nenhum aviso cadastrado. Crie avisos para exibir lembretes no dashboard.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Histórico de Alterações -->
        <details class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden" id="audit-details">
            <summary class="flex items-center justify-between p-6 cursor-pointer list-none select-none hover:bg-gray-800/50 transition-colors">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Histórico de Alterações
                    <span class="text-sm font-normal text-gray-500">(últimas 30)</span>
                </h2>
                <svg id="audit-chevron" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>

            <div class="px-6 pb-6">
                <!-- Filtros por módulo -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <button data-audit-filter="all"
                        data-active-class="bg-gray-600 text-white"
                        data-inactive-class="bg-gray-800 text-gray-400 hover:text-white"
                        onclick="loadAuditLogs('all')"
                        class="text-xs px-3 py-1 rounded-full transition-colors bg-gray-600 text-white">
                        Todos
                    </button>
                    <button data-audit-filter="setting"
                        data-active-class="bg-emerald-600 text-white"
                        data-inactive-class="bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20"
                        onclick="loadAuditLogs('setting')"
                        class="text-xs px-3 py-1 rounded-full transition-colors bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20">
                        Configurações
                    </button>
                    <button data-audit-filter="project"
                        data-active-class="bg-cyan-600 text-white"
                        data-inactive-class="bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20"
                        onclick="loadAuditLogs('project')"
                        class="text-xs px-3 py-1 rounded-full transition-colors bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20">
                        Projetos
                    </button>
                    <button data-audit-filter="company"
                        data-active-class="bg-blue-600 text-white"
                        data-inactive-class="bg-blue-500/10 text-blue-400 hover:bg-blue-500/20"
                        onclick="loadAuditLogs('company')"
                        class="text-xs px-3 py-1 rounded-full transition-colors bg-blue-500/10 text-blue-400 hover:bg-blue-500/20">
                        Empresas
                    </button>
                    <button data-audit-filter="company_project"
                        data-active-class="bg-purple-600 text-white"
                        data-inactive-class="bg-purple-500/10 text-purple-400 hover:bg-purple-500/20"
                        onclick="loadAuditLogs('company_project')"
                        class="text-xs px-3 py-1 rounded-full transition-colors bg-purple-500/10 text-purple-400 hover:bg-purple-500/20">
                        Vínculos
                    </button>
                </div>

                <div id="audit-logs-container" class="space-y-2">
                    @include('partials.audit-logs', ['auditLogs' => $auditLogs])
                </div>
            </div>
        </details>
    </div>

    <!-- Modal de Aviso -->
    <div id="notice-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeNoticeModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-amber-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="notice-modal-title">Novo Aviso</h3>
            </div>
            <form id="notice-form" onsubmit="return false;">
                <input type="hidden" id="notice-id" value="">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Título</label>
                        <input type="text" id="notice-title" placeholder="Ex: Reunião de alinhamento"
                            oninput="updateNoticePreview()"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Mensagem</label>
                        <textarea id="notice-message" rows="3" placeholder="Detalhes do aviso..."
                            oninput="updateNoticePreview()"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Tipo</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="notice-type" id="notice-type-one-time" value="one_time" checked
                                    onchange="updateNoticePreview()"
                                    class="w-4 h-4 text-amber-500 bg-gray-800 border-gray-700 focus:ring-amber-500 focus:ring-offset-gray-900"/>
                                <span class="text-gray-300">Uma vez</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="notice-type" id="notice-type-persistent" value="persistent"
                                    onchange="updateNoticePreview()"
                                    class="w-4 h-4 text-amber-500 bg-gray-800 border-gray-700 focus:ring-amber-500 focus:ring-offset-gray-900"/>
                                <span class="text-gray-300">Persistente</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">"Uma vez": o usuário pode fechar. "Persistente": não pode ser fechado.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Cor</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="notice-color" id="notice-color-blue" value="blue" checked
                                    onchange="updateNoticePreview()"
                                    class="w-4 h-4 text-blue-500 bg-gray-800 border-gray-700 focus:ring-blue-500"/>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-400"></span><span class="text-gray-300 text-sm">Azul</span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="notice-color" id="notice-color-yellow" value="yellow"
                                    onchange="updateNoticePreview()"
                                    class="w-4 h-4 text-yellow-500 bg-gray-800 border-gray-700 focus:ring-yellow-500"/>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400"></span><span class="text-gray-300 text-sm">Amarelo</span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="notice-color" id="notice-color-red" value="red"
                                    onchange="updateNoticePreview()"
                                    class="w-4 h-4 text-red-500 bg-gray-800 border-gray-700 focus:ring-red-500"/>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400"></span><span class="text-gray-300 text-sm">Vermelho</span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="notice-color" id="notice-color-green" value="green"
                                    onchange="updateNoticePreview()"
                                    class="w-4 h-4 text-green-500 bg-gray-800 border-gray-700 focus:ring-green-500"/>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-400"></span><span class="text-gray-300 text-sm">Verde</span></span>
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Data de Início</label>
                            <input type="text" id="notice-start-date" placeholder="Selecione..." readonly
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent cursor-pointer"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Data de Encerramento <span class="text-gray-500">(opcional)</span></label>
                            <input type="text" id="notice-end-date" placeholder="Selecione..." readonly
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent cursor-pointer"/>
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="notice-active" checked
                                class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-amber-500 focus:ring-amber-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300">Ativo</span>
                        </label>
                    </div>

                    <!-- Pré-visualização -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Pré-visualização</label>
                        <div id="notice-preview" class="border rounded-lg px-4 py-3 flex items-start gap-3 bg-blue-900/40 border-blue-500/50 text-blue-200 transition-all">
                            <svg id="notice-preview-icon" class="w-5 h-5 mt-0.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p id="notice-preview-title" class="font-semibold text-sm">Título do aviso</p>
                                <p id="notice-preview-message" class="text-sm mt-0.5 opacity-90">Mensagem do aviso...</p>
                            </div>
                            <button id="notice-preview-close" class="shrink-0 opacity-70 pointer-events-none" title="Botão de fechar (apenas no tipo &quot;Uma vez&quot;)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Aparência real no dashboard.</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="closeNoticeModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveNotice()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Rotacionar chevron do historico de alteracoes
        document.getElementById('audit-details').addEventListener('toggle', function () {
            const chevron = document.getElementById('audit-chevron');
            chevron.style.transform = this.open ? 'rotate(180deg)' : '';
        });

        // Filtro de auditoria via AJAX
        let currentAuditFilter = 'all';

        async function loadAuditLogs(filter) {
            if (filter === currentAuditFilter) return;
            currentAuditFilter = filter;

            // Atualizar estilos dos botoes
            document.querySelectorAll('[data-audit-filter]').forEach(btn => {
                const isActive = btn.dataset.auditFilter === filter;
                const activeClasses = btn.dataset.activeClass.split(' ');
                const inactiveClasses = btn.dataset.inactiveClass.split(' ');
                btn.classList.remove(...activeClasses, ...inactiveClasses);
                btn.classList.add(...(isActive ? activeClasses : inactiveClasses));
            });

            // Mostrar loading
            const container = document.getElementById('audit-logs-container');
            container.innerHTML = '<p class="text-center py-8 text-gray-500">Carregando...</p>';

            try {
                const response = await fetch(`/settings/audit-logs?filter=${filter}`, {
                    headers: { 'Accept': 'text/html', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                if (!response.ok) throw new Error();
                container.innerHTML = await response.text();
            } catch {
                container.innerHTML = '<p class="text-center py-8 text-red-400">Erro ao carregar.</p>';
            }
        }

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
            document.getElementById('confirm-btn').onclick = function() {
                const cb = confirmCallback;
                closeConfirmModal();
                if (cb) cb();
            };
            modal.classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirm-modal').classList.add('hidden');
            confirmCallback = null;
        }

        // Project Modal
        function openProjectModal(id = null, name = '', active = true, isDefault = false, defaultDescription = '') {
            document.getElementById('project-modal-title').textContent = id ? 'Editar Projeto' : 'Novo Projeto';
            document.getElementById('project-id').value = id || '';
            document.getElementById('project-name').value = name;
            document.getElementById('project-default-description').value = defaultDescription || '';
            document.getElementById('project-active').checked = active;
            document.getElementById('project-default').checked = isDefault;
            document.getElementById('project-modal').classList.remove('hidden');
        }

        function closeProjectModal() {
            document.getElementById('project-modal').classList.add('hidden');
        }

        function editProject(id, name, active, isDefault, defaultDescription = '') {
            openProjectModal(id, name, active, isDefault, defaultDescription);
        }

        // Save Settings
        async function saveSettings() {
            const hourlyRate = parseCurrencyValue(document.getElementById('hourly-rate').value);
            const onCallHourlyRate = parseCurrencyValue(document.getElementById('on-call-hourly-rate').value);
            const extraValue = parseCurrencyValue(document.getElementById('extra-value').value);
            const discountValue = parseCurrencyValue(document.getElementById('discount-value').value);
            const autoSaveTracking = document.getElementById('auto-save-tracking').checked;

            if (hourlyRate < 0 || extraValue < 0 || discountValue < 0 || onCallHourlyRate < 0) {
                showToast('Os valores não podem ser negativos!', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const response = await fetch('/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        hourly_rate: hourlyRate,
                        on_call_hourly_rate: onCallHourlyRate > 0 ? onCallHourlyRate : null,
                        extra_value: extraValue,
                        discount_value: discountValue,
                        auto_save_tracking: autoSaveTracking
                    })
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
            const defaultDescription = document.getElementById('project-default-description').value;
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
                    body: JSON.stringify({
                        name,
                        active,
                        is_default: isDefault,
                        default_description: defaultDescription || null
                    })
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
                closeNoticeModal();
            }
        });

        // Flatpickr para avisos
        let noticeStartPicker, noticeEndPicker;

        document.addEventListener('DOMContentLoaded', function () {
            const fpConfig = {
                locale: 'pt',
                dateFormat: 'd/m/Y',
                disableMobile: true,
                allowInput: false,
            };

            noticeStartPicker = flatpickr('#notice-start-date', {
                ...fpConfig,
                onChange: () => updateNoticePreview(),
            });

            noticeEndPicker = flatpickr('#notice-end-date', fpConfig);
        });

        function parseDateBrToIso(dateStr) {
            if (!dateStr) return null;
            const [day, month, year] = dateStr.split('/');
            return `${year}-${month}-${day}`;
        }

        // Notice Preview
        const NOTICE_COLOR_CLASSES = {
            blue:   { wrap: 'bg-blue-900/40 border-blue-500/50 text-blue-200',   icon: 'text-blue-400' },
            yellow: { wrap: 'bg-yellow-900/40 border-yellow-500/50 text-yellow-200', icon: 'text-yellow-400' },
            red:    { wrap: 'bg-red-900/40 border-red-500/50 text-red-200',       icon: 'text-red-400' },
            green:  { wrap: 'bg-green-900/40 border-green-500/50 text-green-200', icon: 'text-green-400' },
        };

        function updateNoticePreview() {
            const title   = document.getElementById('notice-title').value.trim() || 'Título do aviso';
            const message = document.getElementById('notice-message').value.trim() || 'Mensagem do aviso...';
            const color   = document.querySelector('input[name="notice-color"]:checked')?.value || 'blue';
            const type    = document.querySelector('input[name="notice-type"]:checked')?.value || 'one_time';

            const preview = document.getElementById('notice-preview');
            const icon    = document.getElementById('notice-preview-icon');
            const closeBtn = document.getElementById('notice-preview-close');

            // Remover todas as classes de cor anteriores
            const allWrap = Object.values(NOTICE_COLOR_CLASSES).map(c => c.wrap.split(' ')).flat();
            const allIcon = Object.values(NOTICE_COLOR_CLASSES).map(c => c.icon.split(' ')).flat();
            preview.classList.remove(...allWrap);
            icon.classList.remove(...allIcon);

            // Aplicar classes da cor selecionada
            const cls = NOTICE_COLOR_CLASSES[color] || NOTICE_COLOR_CLASSES.blue;
            cls.wrap.split(' ').forEach(c => preview.classList.add(c));
            cls.icon.split(' ').forEach(c => icon.classList.add(c));

            document.getElementById('notice-preview-title').textContent   = title;
            document.getElementById('notice-preview-message').textContent = message;

            // Mostrar/ocultar botão de fechar conforme tipo
            closeBtn.style.display = type === 'one_time' ? '' : 'none';
        }

        // Notice Modal
        function openNoticeModal() {
            document.getElementById('notice-modal-title').textContent = 'Novo Aviso';
            document.getElementById('notice-id').value = '';
            document.getElementById('notice-title').value = '';
            document.getElementById('notice-message').value = '';
            document.getElementById('notice-type-one-time').checked = true;
            document.getElementById('notice-color-blue').checked = true;
            noticeStartPicker.setDate(new Date());
            noticeEndPicker.clear();
            document.getElementById('notice-active').checked = true;
            document.getElementById('notice-modal').classList.remove('hidden');
            updateNoticePreview();
        }

        function closeNoticeModal() {
            document.getElementById('notice-modal').classList.add('hidden');
        }

        function editNotice(id, title, message, type, color, startDate, endDate, isActive) {
            document.getElementById('notice-modal-title').textContent = 'Editar Aviso';
            document.getElementById('notice-id').value = id;
            document.getElementById('notice-title').value = title;
            document.getElementById('notice-message').value = message;
            document.querySelector(`input[name="notice-type"][value="${type}"]`).checked = true;
            document.querySelector(`input[name="notice-color"][value="${color}"]`).checked = true;
            noticeStartPicker.setDate(startDate);
            endDate ? noticeEndPicker.setDate(endDate) : noticeEndPicker.clear();
            document.getElementById('notice-active').checked = isActive;
            document.getElementById('notice-modal').classList.remove('hidden');
            updateNoticePreview();
        }

        async function saveNotice() {
            const id = document.getElementById('notice-id').value;
            const title = document.getElementById('notice-title').value.trim();
            const message = document.getElementById('notice-message').value.trim();
            const type = document.querySelector('input[name="notice-type"]:checked')?.value;
            const color = document.querySelector('input[name="notice-color"]:checked')?.value;
            const startDate = parseDateBrToIso(document.getElementById('notice-start-date').value);
            const endDate = parseDateBrToIso(document.getElementById('notice-end-date').value) || null;
            const isActive = document.getElementById('notice-active').checked;

            if (!title) { showToast('Por favor, informe o título do aviso!', TOAST_TYPES.WARNING); return; }
            if (!message) { showToast('Por favor, informe a mensagem do aviso!', TOAST_TYPES.WARNING); return; }
            if (!startDate) { showToast('Por favor, informe a data de início!', TOAST_TYPES.WARNING); return; }

            try {
                const url = id ? `/notices/${id}` : '/notices';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ title, message, type, color, start_date: startDate, end_date: endDate, is_active: isActive })
                });

                const data = await response.json();
                if (!response.ok) {
                    const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro ao salvar');
                    throw new Error(errors);
                }

                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeNoticeModal();
                location.reload();
            } catch (error) {
                showToast(error.message, TOAST_TYPES.ERROR);
            }
        }

        function deleteNotice(id) {
            showConfirm('Deseja realmente excluir este aviso?', async () => {
                try {
                    const response = await fetch(`/notices/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Erro ao excluir');

                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    document.querySelector(`[data-notice-id="${id}"]`).remove();

                    if (document.querySelectorAll('[data-notice-id]').length === 0) {
                        document.getElementById('notices-list').innerHTML = '<div id="no-notices" class="text-center py-8 text-gray-500">Nenhum aviso cadastrado. Crie avisos para exibir lembretes no dashboard.</div>';
                    }
                } catch (error) {
                    showToast(error.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir Aviso');
        }

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
