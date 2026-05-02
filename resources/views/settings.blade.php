<x-app-layout>
    <!-- Modal de Projeto -->
    <div id="project-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeProjectModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
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

    <!-- Modal de Empresa (removido — gerenciado em /companies) -->
    <div id="company-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeCompanyModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
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

                <!-- Seção 1: Dados da Empresa -->
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Dados da Empresa</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Nome Fantasia <span class="text-red-400">*</span></label>
                            <input type="text" id="company-name" placeholder="Ex: Empresa Alpha"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Razão Social</label>
                            <input type="text" id="company-razao-social" placeholder="Ex: Empresa Alpha Tecnologia LTDA"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">CNPJ <span class="text-red-400">*</span></label>
                                <input type="text" id="company-cnpj" placeholder="00.000.000/0000-00" maxlength="18"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                    oninput="formatCnpj(this)"/>
                            </div>
                            <div class="flex-shrink-0 flex flex-col justify-end">
                                <button type="button" id="btn-lookup-cnpj" onclick="lookupCnpj()"
                                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium whitespace-nowrap">
                                    Buscar
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" id="company-active" checked
                                    class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900"/>
                                <span class="text-gray-300">Ativa</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Seção 2: Contato da Empresa -->
                <div class="mb-5 pt-4 border-t border-gray-700">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Contato da Empresa</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Telefone</label>
                            <input type="text" id="company-telefone" placeholder="(00) 00000-0000" maxlength="15"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                oninput="formatTelefone(this)"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">E-mail</label>
                            <input type="email" id="company-email" placeholder="contato@empresa.com.br"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                    </div>
                </div>

                <!-- Seção 3: Endereço -->
                <div class="mb-5 pt-4 border-t border-gray-700">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Endereço</h4>
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <div class="w-40">
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">CEP</label>
                                <input type="text" id="company-cep" placeholder="00000-000" maxlength="9"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono"
                                    oninput="formatCep(this)"/>
                            </div>
                            <div class="flex-shrink-0 flex flex-col justify-end">
                                <button type="button" id="btn-lookup-cep" onclick="lookupCep()"
                                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium whitespace-nowrap">
                                    Buscar
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Logradouro</label>
                            <input type="text" id="company-logradouro" placeholder="Rua, Avenida..."
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Número</label>
                                <input type="text" id="company-numero" placeholder="123"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Complemento</label>
                                <input type="text" id="company-complemento" placeholder="Sala 10, Andar 2..."
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Bairro</label>
                            <input type="text" id="company-bairro" placeholder="Centro"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Cidade</label>
                                <input type="text" id="company-cidade" placeholder="São Paulo"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">UF</label>
                                <input type="text" id="company-uf" placeholder="SP" maxlength="2"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Inscrição Municipal</label>
                                <input type="text" id="company-inscricao-municipal" placeholder="000000-0"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Inscrição Estadual</label>
                                <input type="text" id="company-inscricao-estadual" placeholder="000.000.000.000"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção 4: Responsável -->
                <div class="mb-5 pt-4 border-t border-gray-700">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Responsável</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Nome do Responsável</label>
                            <input type="text" id="company-responsavel-nome" placeholder="João da Silva"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">E-mail do Responsável</label>
                                <input type="email" id="company-responsavel-email" placeholder="joao@empresa.com.br"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Telefone do Responsável</label>
                                <input type="text" id="company-responsavel-telefone" placeholder="(00) 00000-0000" maxlength="15"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    oninput="formatTelefone(this)"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 justify-end pt-4 border-t border-gray-700">
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
    <div id="link-company-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeLinkCompanyModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
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

                <!-- Ciclo de Faturamento -->
                <div class="mt-6 p-4 bg-gray-800/50 rounded-lg border border-gray-700"
                     x-data="{ saved: {{ $settings->billing_cycle_day ?? 1 }}, current: {{ $settings->billing_cycle_day ?? 1 }} }"
                     @cycle-day-saved.window="saved = $event.detail.day">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-cyan-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="flex-1">
                            <span class="text-white font-medium block mb-1">Ciclo de Faturamento</span>
                            <p class="text-xs text-gray-500 mb-3">
                                Dia do mês em que começa seu ciclo. Ex: dia 5 = período de 05/mar até 04/abr.
                                Use 1 para ciclo mensal padrão (01 ao último dia do mês).
                            </p>
                            <div class="flex items-center gap-3">
                                <label class="text-sm text-gray-400 shrink-0">Inicia no dia:</label>
                                <input type="number" id="billing-cycle-day"
                                    x-model.number="current"
                                    value="{{ $settings->billing_cycle_day ?? 1 }}"
                                    min="1" max="28"
                                    class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent text-center"/>
                                <span class="text-sm text-gray-500">de cada mês</span>
                            </div>

                            <!-- Aviso ao alterar o ciclo -->
                            <div x-show="current !== saved" x-cloak x-transition
                                class="mt-3 flex items-start gap-2 p-3 bg-yellow-900/30 border border-yellow-500/40 rounded-lg">
                                <svg class="w-4 h-4 text-yellow-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                <p class="text-xs text-yellow-300 leading-relaxed">
                                    Ao salvar, lançamentos existentes passarão a ser exibidos no período correto conforme o novo ciclo.
                                    Lançamentos de datas próximas à virada podem mudar de período no dashboard.
                                </p>
                            </div>
                        </div>
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

        <!-- Segurança -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6"
             x-data="{ twoFactorEnabled: {{ auth()->user()->two_factor_enabled ? 'true' : 'false' }}, loading: false }">
            <h2 class="text-xl font-semibold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Segurança
            </h2>

            <!-- Toggle 2FA -->
            <div class="p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-white font-medium">Autenticação de dois fatores (2FA)</span>
                            <span x-show="twoFactorEnabled"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-900/40 text-emerald-400 border border-emerald-700/50">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Ativo
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">
                            Ao fazer login, um código de 6 dígitos será enviado ao seu e-mail para confirmar o acesso.
                            O código expira em 10 minutos. Após 3 tentativas incorretas, o acesso fica bloqueado por 10 minutos.
                        </p>
                    </div>
                    <button type="button"
                        :disabled="loading"
                        @click="
                            loading = true;
                            fetch('{{ route('settings.two-factor-toggle') }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                            })
                            .then(r => r.json())
                            .then(d => { twoFactorEnabled = d.enabled; showToast(d.message, d.enabled ? TOAST_TYPES.SUCCESS : TOAST_TYPES.INFO); })
                            .catch(() => showToast('Erro ao alterar configuração.', TOAST_TYPES.ERROR))
                            .finally(() => loading = false);
                        "
                        class="shrink-0 relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-gray-900 disabled:opacity-50"
                        :class="twoFactorEnabled ? 'bg-cyan-600' : 'bg-gray-700'">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                              :class="twoFactorEnabled ? 'translate-x-6' : 'translate-x-1'">
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empresas → link para o CRM -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Empresas</h2>
                        <p class="text-gray-400 text-sm">
                            {{ $companies->count() }} empresa(s) cadastrada(s)
                            @if($companyLimit !== null)
                                · limite: {{ $companyLimit }}
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('companies.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-blue-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Gerenciar Empresas
                </a>
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
            <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2 min-w-0">
                    <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Avisos e Lembretes
                </h2>
                <button onclick="openNoticeModal()"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-amber-500/30 shrink-0">
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

        <!-- Contas Bancárias -->
        @if($isPremium)
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6" x-data="bankAccountsSection()">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Contas Bancárias
                </h2>
                <button @click="openAdd()"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Conta
                </button>
            </div>

            <!-- Lista -->
            <div id="bank-accounts-list" class="space-y-3">
                @forelse($bankAccounts as $ba)
                <div class="flex items-center justify-between p-4 bg-gray-800 rounded-lg" data-ba-uuid="{{ $ba->uuid }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-white">{{ $ba->bank_name }}</p>
                            @if(!$ba->active)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-700 text-gray-400">Inativa</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5">
                            Ag. {{ $ba->branch }} · Conta {{ $ba->account_number }} ({{ $ba->account_type }})
                            @if($ba->pix_key) · PIX: {{ $ba->pix_key }}@endif
                        </p>
                        <p class="text-xs text-gray-500">{{ $ba->holder_name }}</p>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <button @click="openEdit({{ $ba->toJson() }})"
                                class="text-gray-400 hover:text-white transition-colors p-1.5 rounded hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="toggleAccount('{{ $ba->uuid }}', {{ $ba->active ? 'true' : 'false' }})"
                                class="text-gray-400 hover:text-yellow-400 transition-colors p-1.5 rounded hover:bg-gray-700"
                                title="{{ $ba->active ? 'Desativar' : 'Ativar' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ba->active ? 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21' : 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' }}"/>
                            </svg>
                        </button>
                        <button @click="deleteAccount('{{ $ba->uuid }}')"
                                class="text-gray-400 hover:text-red-400 transition-colors p-1.5 rounded hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div id="no-bank-accounts" class="text-center py-8 text-gray-500">
                    Nenhuma conta bancária cadastrada. Contas bancárias ficam vinculadas às faturas.
                </div>
                @endforelse
            </div>

            <!-- Modal Conta Bancária -->
            <div x-show="showModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
                 @click.self="showModal = false"
                 style="display:none">
                <div class="bg-gray-900 border border-gray-700 rounded-xl w-full max-w-lg shadow-2xl" @click.stop>
                    <div class="flex items-center justify-between p-5 border-b border-gray-800">
                        <h3 class="text-lg font-semibold text-white" x-text="editingUuid ? 'Editar Conta' : 'Nova Conta Bancária'"></h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitAccount()" class="p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm text-gray-300 mb-1">Banco <span class="text-red-400">*</span></label>
                                <input type="text" x-model="form.bank_name" placeholder="Ex: Nubank, Itaú, Bradesco"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                                <p x-show="errors.bank_name" x-text="errors.bank_name" class="text-red-400 text-xs mt-1"></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Agência <span class="text-red-400">*</span></label>
                                <input type="text" x-model="form.branch" placeholder="0001-0"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                                <p x-show="errors.branch" x-text="errors.branch" class="text-red-400 text-xs mt-1"></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Conta <span class="text-red-400">*</span></label>
                                <input type="text" x-model="form.account_number" placeholder="12345-6"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                                <p x-show="errors.account_number" x-text="errors.account_number" class="text-red-400 text-xs mt-1"></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Tipo <span class="text-red-400">*</span></label>
                                <select x-model="form.account_type"
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                                    <option value="corrente">Corrente</option>
                                    <option value="poupança">Poupança</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Titular <span class="text-red-400">*</span></label>
                                <input type="text" x-model="form.holder_name" placeholder="Nome do titular"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                                <p x-show="errors.holder_name" x-text="errors.holder_name" class="text-red-400 text-xs mt-1"></p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm text-gray-300 mb-1">Chave PIX</label>
                                <input type="text" x-model="form.pix_key" placeholder="CPF, e-mail, telefone ou chave aleatória"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                            </div>
                            <div class="col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="ba_active" x-model="form.active"
                                       class="rounded border-gray-600 bg-gray-800 text-emerald-500">
                                <label for="ba_active" class="text-sm text-gray-300">Conta ativa</label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showModal = false"
                                    class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="loading"
                                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                                <span x-show="!loading" x-text="editingUuid ? 'Salvar' : 'Criar Conta'"></span>
                                <span x-show="loading">Salvando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        function bankAccountsSection() {
            return {
                showModal: false,
                loading: false,
                editingUuid: null,
                errors: {},
                form: { bank_name: '', branch: '', account_number: '', account_type: 'corrente', holder_name: '', pix_key: '', active: true },

                openAdd() {
                    this.editingUuid = null;
                    this.form = { bank_name: '', branch: '', account_number: '', account_type: 'corrente', holder_name: '', pix_key: '', active: true };
                    this.errors = {};
                    this.showModal = true;
                },

                openEdit(account) {
                    this.editingUuid = account.uuid;
                    this.form = {
                        bank_name:      account.bank_name,
                        branch:         account.branch,
                        account_number: account.account_number,
                        account_type:   account.account_type,
                        holder_name:    account.holder_name,
                        pix_key:        account.pix_key || '',
                        active:         account.active,
                    };
                    this.errors = {};
                    this.showModal = true;
                },

                async submitAccount() {
                    this.loading = true;
                    this.errors = {};
                    const url    = this.editingUuid ? `/bank-accounts/${this.editingUuid}` : '/bank-accounts';
                    const method = this.editingUuid ? 'PUT' : 'POST';
                    try {
                        const res  = await fetch(url, {
                            method,
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                            body: JSON.stringify(this.form),
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.showModal = false;
                            showToast(data.message, TOAST_TYPES.SUCCESS);
                            window.location.reload();
                        } else if (data.errors) {
                            this.errors = data.errors;
                        } else {
                            showToast(data.message || 'Erro ao salvar.', TOAST_TYPES.ERROR);
                        }
                    } catch (e) {
                        showToast('Erro ao salvar conta bancária.', TOAST_TYPES.ERROR);
                    } finally {
                        this.loading = false;
                    }
                },

                async toggleAccount(uuid, currentActive) {
                    const res  = await fetch(`/bank-accounts/${uuid}/toggle`, {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, TOAST_TYPES.SUCCESS);
                        window.location.reload();
                    } else {
                        showToast(data.message, TOAST_TYPES.ERROR);
                    }
                },

                async deleteAccount(uuid) {
                    if (!confirm('Excluir esta conta bancária?')) return;
                    const res  = await fetch(`/bank-accounts/${uuid}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
                    });
                    const data = await res.json();
                    if (data.success) {
                        showToast(data.message, TOAST_TYPES.SUCCESS);
                        const el = document.querySelector(`[data-ba-uuid="${uuid}"]`);
                        if (el) el.remove();
                        if (!document.querySelector('[data-ba-uuid]')) {
                            document.getElementById('bank-accounts-list').innerHTML =
                                '<div class="text-center py-8 text-gray-500">Nenhuma conta bancária cadastrada.</div>';
                        }
                    } else {
                        showToast(data.message, TOAST_TYPES.ERROR);
                    }
                },
            };
        }
        </script>
        @endif

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
    <div id="notice-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeNoticeModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
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
                        <div class="flex flex-wrap gap-x-4 gap-y-2">
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Data de Início</label>
                            <input type="text" id="notice-start-date" placeholder="Selecione..." readonly
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent cursor-pointer"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Encerramento <span class="text-gray-500">(opcional)</span></label>
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
            const billingCycleDay = parseInt(document.getElementById('billing-cycle-day').value) || 1;

            if (hourlyRate < 0 || extraValue < 0 || discountValue < 0 || onCallHourlyRate < 0) {
                showToast('Os valores não podem ser negativos!', TOAST_TYPES.WARNING);
                return;
            }

            if (billingCycleDay < 1 || billingCycleDay > 28) {
                showToast('O dia do ciclo de faturamento deve estar entre 1 e 28.', TOAST_TYPES.WARNING);
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
                        auto_save_tracking: autoSaveTracking,
                        billing_cycle_day: billingCycleDay
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Erro ao salvar');
                showToast(data.message, TOAST_TYPES.SUCCESS);

                // Sincroniza o valor salvo do ciclo para sumir o aviso
                window.dispatchEvent(new CustomEvent('cycle-day-saved', { detail: { day: billingCycleDay } }));
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
        function openCompanyModal(data = null) {
            const isEdit = data && data.id;
            document.getElementById('company-modal-title').textContent = isEdit ? 'Editar Empresa' : 'Nova Empresa';
            document.getElementById('company-id').value = isEdit ? data.id : '';

            const fields = [
                ['company-name', 'name'], ['company-razao-social', 'razao_social'],
                ['company-cnpj', 'cnpj'], ['company-telefone', 'telefone'],
                ['company-email', 'email'], ['company-cep', 'cep'],
                ['company-logradouro', 'logradouro'], ['company-numero', 'numero'],
                ['company-complemento', 'complemento'], ['company-bairro', 'bairro'],
                ['company-cidade', 'cidade'], ['company-uf', 'uf'],
                ['company-inscricao-municipal', 'inscricao_municipal'],
                ['company-inscricao-estadual', 'inscricao_estadual'],
                ['company-responsavel-nome', 'responsavel_nome'],
                ['company-responsavel-email', 'responsavel_email'],
                ['company-responsavel-telefone', 'responsavel_telefone'],
            ];
            fields.forEach(([elId, key]) => {
                const el = document.getElementById(elId);
                if (el) el.value = (data && data[key]) ? data[key] : '';
            });
            document.getElementById('company-active').checked = data ? !!data.active : true;
            document.getElementById('company-modal').classList.remove('hidden');
        }

        function closeCompanyModal() {
            document.getElementById('company-modal').classList.add('hidden');
        }

        function editCompany(data) {
            openCompanyModal(data);
        }

        async function saveCompany() {
            const id = document.getElementById('company-id').value;
            const name = document.getElementById('company-name').value;
            const cnpj = document.getElementById('company-cnpj').value;

            if (!name.trim()) {
                showToast('Por favor, informe o nome da empresa!', TOAST_TYPES.WARNING);
                return;
            }

            if (!/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/.test(cnpj)) {
                showToast('CNPJ inválido! Use o formato XX.XXX.XXX/XXXX-XX', TOAST_TYPES.WARNING);
                return;
            }

            const payload = {
                name,
                cnpj,
                active: document.getElementById('company-active').checked,
                razao_social: document.getElementById('company-razao-social').value.trim() || null,
                email: document.getElementById('company-email').value.trim() || null,
                telefone: document.getElementById('company-telefone').value.trim() || null,
                cep: document.getElementById('company-cep').value.trim() || null,
                logradouro: document.getElementById('company-logradouro').value.trim() || null,
                numero: document.getElementById('company-numero').value.trim() || null,
                complemento: document.getElementById('company-complemento').value.trim() || null,
                bairro: document.getElementById('company-bairro').value.trim() || null,
                cidade: document.getElementById('company-cidade').value.trim() || null,
                uf: document.getElementById('company-uf').value.trim().toUpperCase() || null,
                inscricao_municipal: document.getElementById('company-inscricao-municipal').value.trim() || null,
                inscricao_estadual: document.getElementById('company-inscricao-estadual').value.trim() || null,
                responsavel_nome: document.getElementById('company-responsavel-nome').value.trim() || null,
                responsavel_email: document.getElementById('company-responsavel-email').value.trim() || null,
                responsavel_telefone: document.getElementById('company-responsavel-telefone').value.trim() || null,
            };

            try {
                const url = id ? `/companies/${id}` : '/companies';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
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

        // Busca CNPJ via backend (BrasilAPI)
        async function lookupCnpj() {
            const cnpj = document.getElementById('company-cnpj').value.replace(/\D/g, '');
            if (cnpj.length !== 14) {
                showToast('Digite o CNPJ completo antes de buscar.', TOAST_TYPES.WARNING);
                return;
            }
            const btn = document.getElementById('btn-lookup-cnpj');
            btn.textContent = '...';
            btn.disabled = true;
            try {
                const res = await fetch(`/cnpj/${cnpj}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.error || 'CNPJ não encontrado.', TOAST_TYPES.ERROR);
                    return;
                }
                fillIfEmpty('company-name', data.nome_fantasia);
                fillIfEmpty('company-razao-social', data.razao_social);
                fillIfEmpty('company-email', data.email);
                fillIfEmpty('company-telefone', data.telefone);
                fillIfEmpty('company-cep', data.cep);
                fillIfEmpty('company-logradouro', data.logradouro);
                fillIfEmpty('company-numero', data.numero);
                fillIfEmpty('company-complemento', data.complemento);
                fillIfEmpty('company-bairro', data.bairro);
                fillIfEmpty('company-cidade', data.cidade);
                fillIfEmpty('company-uf', data.uf);
                showToast('Dados encontrados e preenchidos!', TOAST_TYPES.SUCCESS);
            } catch (e) {
                showToast('Erro ao buscar CNPJ.', TOAST_TYPES.ERROR);
            } finally {
                btn.textContent = 'Buscar';
                btn.disabled = false;
            }
        }

        // Busca CEP via proxy backend → ViaCEP
        async function lookupCep() {
            const cep = document.getElementById('company-cep').value.replace(/\D/g, '');
            if (cep.length !== 8) {
                showToast('Digite o CEP completo antes de buscar.', TOAST_TYPES.WARNING);
                return;
            }
            const btn = document.getElementById('btn-lookup-cep');
            btn.textContent = '...';
            btn.disabled = true;
            try {
                const res = await fetch(`/cep/${cep}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.error || 'CEP não encontrado.', TOAST_TYPES.ERROR);
                    return;
                }
                fillIfEmpty('company-logradouro', data.logradouro);
                fillIfEmpty('company-bairro', data.bairro);
                fillIfEmpty('company-cidade', data.cidade);
                fillIfEmpty('company-uf', data.uf);
                document.getElementById('company-numero').focus();
                showToast('Endereço preenchido!', TOAST_TYPES.SUCCESS);
            } catch (e) {
                showToast('Erro ao buscar CEP.', TOAST_TYPES.ERROR);
            } finally {
                btn.textContent = 'Buscar';
                btn.disabled = false;
            }
        }

        function fillIfEmpty(id, value) {
            const el = document.getElementById(id);
            if (el && !el.value && value) el.value = value;
        }

        function formatTelefone(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) {
                v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            } else if (v.length > 6) {
                v = v.replace(/^(\d{2})(\d{4})(\d*)$/, '($1) $2-$3');
            } else if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d*)$/, '($1) $2');
            }
            input.value = v;
        }

        function formatCep(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 5) v = v.replace(/^(\d{5})(\d*)$/, '$1-$2');
            input.value = v;
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
