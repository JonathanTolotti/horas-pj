<x-app-layout>
    <!-- Modal de Empresa -->
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

    <div class="max-w-5xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Empresas</h1>
                <p class="text-gray-400 text-sm">Gerencie as empresas com as quais você trabalha</p>
            </div>
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

        <!-- Busca e Filtro -->
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="search-input" placeholder="Buscar por nome ou CNPJ..."
                    oninput="filterCompanies()"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg pl-10 pr-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-2">
                <button onclick="setFilter('all')" id="filter-all"
                    class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-700 text-white">
                    Todas
                </button>
                <button onclick="setFilter('active')" id="filter-active"
                    class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:text-white">
                    Ativas
                </button>
                <button onclick="setFilter('inactive')" id="filter-inactive"
                    class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:text-white">
                    Inativas
                </button>
            </div>
        </div>

        <!-- Lista de Empresas -->
        @if($companies->isEmpty())
            <div class="text-center py-16 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-lg font-medium text-gray-600 mb-1">Nenhuma empresa cadastrada</p>
                <p class="text-sm">Clique em "Nova Empresa" para começar.</p>
            </div>
        @else
        <div id="companies-list" class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden divide-y divide-gray-800">
            @foreach($companies as $company)
            @php
                $companyJson = json_encode([
                    'id'                   => $company->uuid,
                    'name'                 => $company->name,
                    'cnpj'                 => $company->cnpj,
                    'active'               => (bool) $company->active,
                    'razao_social'         => $company->razao_social,
                    'email'                => $company->email,
                    'telefone'             => $company->telefone,
                    'cep'                  => $company->cep,
                    'logradouro'           => $company->logradouro,
                    'numero'               => $company->numero,
                    'complemento'          => $company->complemento,
                    'bairro'               => $company->bairro,
                    'cidade'               => $company->cidade,
                    'uf'                   => $company->uf,
                    'inscricao_municipal'  => $company->inscricao_municipal,
                    'inscricao_estadual'   => $company->inscricao_estadual,
                    'responsavel_nome'     => $company->responsavel_nome,
                    'responsavel_email'    => $company->responsavel_email,
                    'responsavel_telefone' => $company->responsavel_telefone,
                ]);
            @endphp
            <div class="company-card flex items-center gap-4 px-5 py-4 hover:bg-gray-800/40 transition-colors"
                 data-name="{{ strtolower($company->name) }}"
                 data-cnpj="{{ $company->cnpj }}"
                 data-active="{{ $company->active ? 'true' : 'false' }}"
                 data-company-id="{{ $company->uuid }}">

                <!-- Status dot -->
                <span class="w-2 h-2 {{ $company->active ? 'bg-emerald-400' : 'bg-gray-500' }} rounded-full flex-shrink-0"></span>

                <!-- Nome + CNPJ -->
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-white font-medium">{{ $company->name }}</h3>
                        @if($company->razao_social)
                            <span class="text-gray-500 text-xs truncate hidden sm:inline">{{ $company->razao_social }}</span>
                        @endif
                        @if(!$company->active)
                            <span class="text-xs bg-gray-700/60 text-gray-400 px-1.5 py-0.5 rounded-full">Inativa</span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-xs font-mono mt-0.5">{{ $company->cnpj }}</p>
                </div>

                <!-- Localização / Contato -->
                <div class="hidden md:flex flex-col gap-0.5 text-sm text-gray-400 min-w-0 w-44">
                    @if($company->cidade)
                        <span class="truncate">{{ $company->cidade }}{{ $company->uf ? '/' . $company->uf : '' }}</span>
                    @endif
                    @if($company->email)
                        <span class="truncate text-xs">{{ $company->email }}</span>
                    @elseif($company->telefone)
                        <span class="text-xs">{{ $company->telefone }}</span>
                    @elseif(!$company->cidade)
                        <span class="text-xs italic text-gray-600">Não informado</span>
                    @endif
                </div>

                <!-- Projetos -->
                @if($company->projects->count() > 0)
                <div class="hidden lg:flex items-center gap-1.5 flex-wrap">
                    @foreach($company->projects->take(2) as $project)
                        <span class="text-xs bg-purple-500/10 text-purple-300 border border-purple-500/20 px-2 py-0.5 rounded-full whitespace-nowrap">
                            {{ $project->name }}
                        </span>
                    @endforeach
                    @if($company->projects->count() > 2)
                        <span class="text-xs text-gray-500">+{{ $company->projects->count() - 2 }}</span>
                    @endif
                </div>
                @endif

                <!-- Ações -->
                <div class="flex items-center gap-1 flex-shrink-0">
                    <a href="{{ route('companies.show', $company) }}"
                        class="p-1.5 text-gray-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Ver detalhes">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <button onclick='editCompany({{ $companyJson }})'
                        class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button onclick="deleteCompany('{{ $company->uuid }}')"
                        class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors" title="Excluir">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        <div id="no-results" class="hidden text-center py-10 text-gray-500">
            Nenhuma empresa encontrada para o filtro aplicado.
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        let currentFilter = 'all';

        function setFilter(filter) {
            currentFilter = filter;
            document.getElementById('filter-all').className    = filter === 'all'      ? 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-700 text-white'    : 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:text-white';
            document.getElementById('filter-active').className = filter === 'active'   ? 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-emerald-700 text-white' : 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:text-white';
            document.getElementById('filter-inactive').className = filter === 'inactive' ? 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-600 text-white'   : 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-800 text-gray-400 hover:text-white';
            filterCompanies();
        }

        function filterCompanies() {
            const search = (document.getElementById('search-input')?.value || '').toLowerCase();
            const cards  = document.querySelectorAll('.company-card');
            let visible  = 0;

            cards.forEach(card => {
                const name   = card.dataset.name || '';
                const cnpj   = card.dataset.cnpj || '';
                const active = card.dataset.active;

                const matchSearch = !search || name.includes(search) || cnpj.includes(search);
                const matchFilter = currentFilter === 'all'
                    || (currentFilter === 'active'   && active === 'true')
                    || (currentFilter === 'inactive' && active === 'false');

                const show = matchSearch && matchFilter;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            const noResults = document.getElementById('no-results');
            if (noResults) noResults.classList.toggle('hidden', visible > 0);
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
            const id   = document.getElementById('company-id').value;
            const name = document.getElementById('company-name').value;
            const cnpj = document.getElementById('company-cnpj').value;

            if (!name.trim()) { showToast('Por favor, informe o nome da empresa!', TOAST_TYPES.WARNING); return; }
            if (!/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/.test(cnpj)) {
                showToast('CNPJ inválido! Use o formato XX.XXX.XXX/XXXX-XX', TOAST_TYPES.WARNING); return;
            }

            const payload = {
                name, cnpj,
                active:                document.getElementById('company-active').checked,
                razao_social:          document.getElementById('company-razao-social').value.trim() || null,
                email:                 document.getElementById('company-email').value.trim() || null,
                telefone:              document.getElementById('company-telefone').value.trim() || null,
                cep:                   document.getElementById('company-cep').value.trim() || null,
                logradouro:            document.getElementById('company-logradouro').value.trim() || null,
                numero:                document.getElementById('company-numero').value.trim() || null,
                complemento:           document.getElementById('company-complemento').value.trim() || null,
                bairro:                document.getElementById('company-bairro').value.trim() || null,
                cidade:                document.getElementById('company-cidade').value.trim() || null,
                uf:                    document.getElementById('company-uf').value.trim().toUpperCase() || null,
                inscricao_municipal:   document.getElementById('company-inscricao-municipal').value.trim() || null,
                inscricao_estadual:    document.getElementById('company-inscricao-estadual').value.trim() || null,
                responsavel_nome:      document.getElementById('company-responsavel-nome').value.trim() || null,
                responsavel_email:     document.getElementById('company-responsavel-email').value.trim() || null,
                responsavel_telefone:  document.getElementById('company-responsavel-telefone').value.trim() || null,
            };

            try {
                const url    = id ? `/companies/${id}` : '/companies';
                const method = id ? 'PUT' : 'POST';
                const res    = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro ao salvar');
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
                    const res  = await fetch(`/companies/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Erro ao excluir');
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    document.querySelector(`[data-company-id="${id}"]`).remove();
                } catch (error) {
                    showToast(error.message, TOAST_TYPES.ERROR);
                }
            }, 'Excluir Empresa');
        }

        async function lookupCnpj() {
            const cnpj = document.getElementById('company-cnpj').value.replace(/\D/g, '');
            if (cnpj.length !== 14) { showToast('Digite o CNPJ completo antes de buscar.', TOAST_TYPES.WARNING); return; }
            const btn = document.getElementById('btn-lookup-cnpj');
            btn.textContent = '...'; btn.disabled = true;
            try {
                const res  = await fetch(`/cnpj/${cnpj}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'CNPJ não encontrado.', TOAST_TYPES.ERROR); return; }
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
                btn.textContent = 'Buscar'; btn.disabled = false;
            }
        }

        async function lookupCep() {
            const cep = document.getElementById('company-cep').value.replace(/\D/g, '');
            if (cep.length !== 8) { showToast('Digite o CEP completo antes de buscar.', TOAST_TYPES.WARNING); return; }
            const btn = document.getElementById('btn-lookup-cep');
            btn.textContent = '...'; btn.disabled = true;
            try {
                const res  = await fetch(`/cep/${cep}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'CEP não encontrado.', TOAST_TYPES.ERROR); return; }
                fillIfEmpty('company-logradouro', data.logradouro);
                fillIfEmpty('company-bairro', data.bairro);
                fillIfEmpty('company-cidade', data.cidade);
                fillIfEmpty('company-uf', data.uf);
                document.getElementById('company-numero').focus();
                showToast('Endereço preenchido!', TOAST_TYPES.SUCCESS);
            } catch (e) {
                showToast('Erro ao buscar CEP.', TOAST_TYPES.ERROR);
            } finally {
                btn.textContent = 'Buscar'; btn.disabled = false;
            }
        }

        function fillIfEmpty(id, value) {
            const el = document.getElementById(id);
            if (el && !el.value && value) el.value = value;
        }

        function formatCnpj(input) {
            let v = input.value.replace(/\D/g, '');
            if (v.length > 14) v = v.substring(0, 14);
            if (v.length > 12) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
            else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d*).*/, '$1.$2.$3/$4');
            else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d*).*/, '$1.$2.$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d*).*/, '$1.$2');
            input.value = v;
        }

        function formatTelefone(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d*)$/, '($1) $2-$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d*)$/, '($1) $2');
            input.value = v;
        }

        function formatCep(input) {
            let v = input.value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 5) v = v.replace(/^(\d{5})(\d*)$/, '$1-$2');
            input.value = v;
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeCompanyModal();
        });
    </script>
    @endpush
</x-app-layout>
