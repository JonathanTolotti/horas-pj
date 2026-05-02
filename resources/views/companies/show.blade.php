<x-app-layout>

    <!-- Modal Editar Empresa -->
    <div id="company-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeCompanyModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-blue-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Editar Empresa</h3>
            </div>
            <form id="company-form" onsubmit="return false;">
                <input type="hidden" id="company-id" value="{{ $company->id }}">
                <div class="mb-5">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Dados da Empresa</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Nome Fantasia <span class="text-red-400">*</span></label>
                            <input type="text" id="company-name" value="{{ $company->name }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Razão Social</label>
                            <input type="text" id="company-razao-social" value="{{ $company->razao_social }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">CNPJ <span class="text-red-400">*</span></label>
                                <input type="text" id="company-cnpj" value="{{ $company->cnpj }}" maxlength="18"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    oninput="formatCnpj(this)"/>
                            </div>
                            <div class="flex-shrink-0 flex flex-col justify-end">
                                <button type="button" id="btn-lookup-cnpj" onclick="lookupCnpj()"
                                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">Buscar</button>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="company-active" {{ $company->active ? 'checked' : '' }}
                                class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300">Ativa</span>
                        </label>
                    </div>
                </div>
                <div class="mb-5 pt-4 border-t border-gray-700">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Contato</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Telefone</label>
                            <input type="text" id="company-telefone" value="{{ $company->telefone }}" maxlength="15"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                oninput="formatTelefone(this)"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">E-mail</label>
                            <input type="email" id="company-email" value="{{ $company->email }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                    </div>
                </div>
                <div class="mb-5 pt-4 border-t border-gray-700">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Endereço</h4>
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <div class="w-40">
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">CEP</label>
                                <input type="text" id="company-cep" value="{{ $company->cep }}" maxlength="9"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    oninput="formatCep(this)"/>
                            </div>
                            <div class="flex-shrink-0 flex flex-col justify-end">
                                <button type="button" id="btn-lookup-cep" onclick="lookupCep()"
                                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">Buscar</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Logradouro</label>
                            <input type="text" id="company-logradouro" value="{{ $company->logradouro }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Número</label>
                                <input type="text" id="company-numero" value="{{ $company->numero }}"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Complemento</label>
                                <input type="text" id="company-complemento" value="{{ $company->complemento }}"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Bairro</label>
                            <input type="text" id="company-bairro" value="{{ $company->bairro }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Cidade</label>
                                <input type="text" id="company-cidade" value="{{ $company->cidade }}"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">UF</label>
                                <input type="text" id="company-uf" value="{{ $company->uf }}" maxlength="2"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white uppercase focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Inscrição Municipal</label>
                                <input type="text" id="company-inscricao-municipal" value="{{ $company->inscricao_municipal }}"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Inscrição Estadual</label>
                                <input type="text" id="company-inscricao-estadual" value="{{ $company->inscricao_estadual }}"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-5 pt-4 border-t border-gray-700">
                    <h4 class="text-sm font-semibold text-blue-400 uppercase tracking-wide mb-3">Responsável</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1.5">Nome</label>
                            <input type="text" id="company-responsavel-nome" value="{{ $company->responsavel_nome }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">E-mail</label>
                                <input type="email" id="company-responsavel-email" value="{{ $company->responsavel_email }}"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1.5">Telefone</label>
                                <input type="text" id="company-responsavel-telefone" value="{{ $company->responsavel_telefone }}" maxlength="15"
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    oninput="formatTelefone(this)"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 justify-end pt-4 border-t border-gray-700">
                    <button type="button" onclick="closeCompanyModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Cancelar</button>
                    <button type="button" onclick="saveCompany()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Vincular Projeto -->
    <div id="link-project-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeLinkProjectModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-purple-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="link-project-modal-title">Vincular Projeto</h3>
            </div>
            <input type="hidden" id="link-project-id" value="">
            <input type="hidden" id="link-edit-mode" value="false">
            <div class="space-y-4">
                <div id="link-project-select-wrapper">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Projeto</label>
                    <select id="link-project-select"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Selecione um projeto</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="link-project-name-display" class="hidden">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Projeto</label>
                    <p class="text-white font-medium" id="link-project-name-text"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Porcentagem (%)</label>
                    <input type="number" id="link-percentage" placeholder="100" min="0.01" max="100" step="0.01"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"/>
                    <p class="text-xs text-gray-500 mt-1">Porcentagem do faturamento deste projeto destinada a esta empresa</p>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button type="button" onclick="closeLinkProjectModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Cancelar</button>
                <button type="button" onclick="saveLinkProject()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">Salvar</button>
            </div>
        </div>
    </div>

    <!-- Modal Documento -->
    <div id="document-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeDocumentModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-emerald-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Enviar Documento</h3>
            </div>
            <form id="document-form" onsubmit="return false;">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Arquivo <span class="text-red-400">*</span></label>
                        <input type="file" id="doc-file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-600 file:text-white hover:file:bg-blue-700"/>
                        <p class="text-xs text-gray-500 mt-1">PDF, Word, Excel, imagens — máx. 10 MB</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Nome do documento</label>
                        <input type="text" id="doc-name" placeholder="Ex: Contrato de Prestação de Serviços"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"/>
                        <p class="text-xs text-gray-500 mt-1">Se vazio, usa o nome do arquivo</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Descrição</label>
                        <textarea id="doc-description" rows="2" placeholder="Observações sobre o documento..."
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" onclick="closeDocumentModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Cancelar</button>
                    <button type="button" id="btn-upload-doc" onclick="uploadDocument()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Registro/Nota -->
    <div id="note-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeNoteModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-amber-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="note-modal-title">Novo Registro</h3>
            </div>
            <input type="hidden" id="note-id" value="">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Tipo <span class="text-red-400">*</span></label>
                        <select id="note-type"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="meeting">Reunião</option>
                            <option value="negotiation">Negociação</option>
                            <option value="call">Ligação</option>
                            <option value="email">E-mail</option>
                            <option value="visit">Visita</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1.5">Data <span class="text-red-400">*</span></label>
                        <input type="text" id="note-date" placeholder="Selecione..." readonly
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer"/>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Título <span class="text-red-400">*</span></label>
                    <input type="text" id="note-title" placeholder="Ex: Reunião de alinhamento de escopo"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">Conteúdo <span class="text-red-400">*</span></label>
                    <textarea id="note-content" rows="5" placeholder="Descreva o que foi discutido, decidido ou acordado..."
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-6">
                <button type="button" onclick="closeNoteModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">Cancelar</button>
                <button type="button" onclick="saveNote()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Salvar</button>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Breadcrumb + Header -->
        <div>
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                <a href="{{ route('companies.index') }}" class="hover:text-gray-300 transition-colors">Empresas</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-300">{{ $company->name }}</span>
            </nav>

            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-500/20 p-3 rounded-xl">
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-white">{{ $company->name }}</h1>
                            @if($company->active)
                                <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full">Ativa</span>
                            @else
                                <span class="text-xs bg-gray-700/50 text-gray-400 border border-gray-600 px-2 py-0.5 rounded-full">Inativa</span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-sm font-mono">{{ $company->cnpj }}</p>
                    </div>
                </div>
                <button onclick="openCompanyModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </button>
            </div>
        </div>

        <!-- Abas -->
        <div x-data="{ tab: 'overview' }">
            <div class="flex gap-1 bg-gray-900 border border-gray-800 rounded-xl p-1 overflow-x-auto">
                <button @click="tab = 'overview'" :class="tab === 'overview' ? 'bg-gray-700 text-white' : 'text-gray-400 hover:text-gray-300'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Visão Geral
                </button>
                <button @click="tab = 'notes'" :class="tab === 'notes' ? 'bg-gray-700 text-white' : 'text-gray-400 hover:text-gray-300'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Registros
                    @if($company->notes->count() > 0)
                        <span class="bg-amber-500/20 text-amber-400 text-xs px-1.5 py-0.5 rounded-full">{{ $company->notes->count() }}</span>
                    @endif
                </button>
                <button @click="tab = 'documents'" :class="tab === 'documents' ? 'bg-gray-700 text-white' : 'text-gray-400 hover:text-gray-300'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Documentos
                    @if($company->documents->count() > 0)
                        <span class="bg-emerald-500/20 text-emerald-400 text-xs px-1.5 py-0.5 rounded-full">{{ $company->documents->count() }}</span>
                    @endif
                </button>
                @if($isPremium && $company->invoices->count() > 0)
                <button @click="tab = 'invoices'" :class="tab === 'invoices' ? 'bg-gray-700 text-white' : 'text-gray-400 hover:text-gray-300'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Faturas
                    <span class="bg-gray-600 text-gray-300 text-xs px-1.5 py-0.5 rounded-full">{{ $company->invoices->count() }}</span>
                </button>
                @endif
            </div>

            <!-- Aba: Visão Geral -->
            <div x-show="tab === 'overview'" class="space-y-5 mt-5">

                <!-- Dados completos da empresa -->
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Coluna 1 -->
                    <div class="space-y-4">
                        @if($company->razao_social)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Razão Social</p>
                            <p class="text-white">{{ $company->razao_social }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Contato</p>
                            @if($company->email)
                                <p class="text-gray-300 text-sm">{{ $company->email }}</p>
                            @endif
                            @if($company->telefone)
                                <p class="text-gray-300 text-sm">{{ $company->telefone }}</p>
                            @endif
                            @if(!$company->email && !$company->telefone)
                                <p class="text-gray-600 text-sm italic">Não informado</p>
                            @endif
                        </div>

                        @if($company->logradouro)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Endereço</p>
                            <p class="text-gray-300 text-sm">
                                {{ $company->logradouro }}{{ $company->numero ? ', ' . $company->numero : '' }}
                                {{ $company->complemento ? ' — ' . $company->complemento : '' }}
                            </p>
                            <p class="text-gray-300 text-sm">
                                {{ $company->bairro ? $company->bairro . ' · ' : '' }}
                                {{ $company->cidade }}{{ $company->uf ? '/' . $company->uf : '' }}
                                {{ $company->cep ? ' · CEP ' . $company->cep : '' }}
                            </p>
                        </div>
                        @endif

                        @if($company->inscricao_municipal || $company->inscricao_estadual)
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Inscrições</p>
                            @if($company->inscricao_municipal)
                                <p class="text-gray-300 text-sm">Municipal: {{ $company->inscricao_municipal }}</p>
                            @endif
                            @if($company->inscricao_estadual)
                                <p class="text-gray-300 text-sm">Estadual: {{ $company->inscricao_estadual }}</p>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Coluna 2 -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Responsável</p>
                            @if($company->responsavel_nome)
                                <p class="text-gray-300 font-medium">{{ $company->responsavel_nome }}</p>
                            @endif
                            @if($company->responsavel_email)
                                <p class="text-gray-400 text-sm">{{ $company->responsavel_email }}</p>
                            @endif
                            @if($company->responsavel_telefone)
                                <p class="text-gray-400 text-sm">{{ $company->responsavel_telefone }}</p>
                            @endif
                            @if(!$company->responsavel_nome && !$company->responsavel_email && !$company->responsavel_telefone)
                                <p class="text-gray-600 text-sm italic">Não informado</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Projetos Vinculados</p>
                            @forelse($company->projects as $project)
                                <div class="flex items-center justify-between bg-gray-800/50 rounded-lg px-3 py-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 {{ $project->active ? 'bg-emerald-400' : 'bg-gray-500' }} rounded-full"></span>
                                        <span class="text-gray-300 text-sm">{{ $project->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-purple-300 text-sm font-medium">{{ number_format($project->pivot->percentage, 2) }}%</span>
                                        <button onclick="editLinkProject({{ $project->id }}, '{{ addslashes($project->name) }}', {{ $project->pivot->percentage }})"
                                            class="text-gray-500 hover:text-purple-400 transition-colors p-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button onclick="detachProject({{ $project->id }})"
                                            class="text-gray-500 hover:text-red-400 transition-colors p-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Nenhum projeto vinculado.</p>
                            @endforelse
                            <button onclick="openLinkProjectModal()" class="mt-2 text-sm text-purple-400 hover:text-purple-300 transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Vincular projeto
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba: Registros -->
            <div x-show="tab === 'notes'" x-cloak class="mt-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Registros</h2>
                    <button onclick="openNoteModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Registro
                    </button>
                </div>

                <div id="notes-list" class="space-y-3">
                    @forelse($company->notes as $note)
                    @php
                        $colorMap = [
                            'meeting'     => ['bg' => 'bg-blue-500/10',   'border' => 'border-blue-500/20',   'badge' => 'bg-blue-500/20 text-blue-300'],
                            'negotiation' => ['bg' => 'bg-purple-500/10', 'border' => 'border-purple-500/20', 'badge' => 'bg-purple-500/20 text-purple-300'],
                            'call'        => ['bg' => 'bg-green-500/10',  'border' => 'border-green-500/20',  'badge' => 'bg-green-500/20 text-green-300'],
                            'email'       => ['bg' => 'bg-cyan-500/10',   'border' => 'border-cyan-500/20',   'badge' => 'bg-cyan-500/20 text-cyan-300'],
                            'visit'       => ['bg' => 'bg-orange-500/10', 'border' => 'border-orange-500/20', 'badge' => 'bg-orange-500/20 text-orange-300'],
                            'other'       => ['bg' => 'bg-gray-700/30',   'border' => 'border-gray-600/50',   'badge' => 'bg-gray-700 text-gray-300'],
                        ];
                        $c = $colorMap[$note->type] ?? $colorMap['other'];
                    @endphp
                    <div class="{{ $c['bg'] }} border {{ $c['border'] }} rounded-xl p-4" data-note-id="{{ $note->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <span class="text-xs {{ $c['badge'] }} px-2 py-1 rounded-full whitespace-nowrap flex-shrink-0 mt-0.5">
                                    {{ $note->type_label }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-white font-medium">{{ $note->title }}</p>
                                    <p class="text-gray-500 text-xs mt-0.5">{{ $note->note_date->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button onclick='editNote({{ $note->id }}, "{{ addslashes($note->type) }}", "{{ addslashes($note->title) }}", {{ json_encode($note->content) }}, "{{ $note->note_date->format('Y-m-d') }}")'
                                    class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteNote({{ $note->id }})"
                                    class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm mt-3 leading-relaxed whitespace-pre-wrap">{{ $note->content }}</p>
                    </div>
                    @empty
                    <div id="no-notes" class="text-center py-12 text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <p>Nenhum registro ainda. Clique em "Novo Registro" para começar.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Aba: Documentos -->
            <div x-show="tab === 'documents'" x-cloak class="mt-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Documentos</h2>
                    <button onclick="openDocumentModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Enviar Documento
                    </button>
                </div>

                <div id="documents-list" class="space-y-2">
                    @forelse($company->documents as $doc)
                    <div class="flex items-center gap-4 bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-gray-700 transition-colors" data-doc-id="{{ $doc->id }}">
                        <div class="bg-gray-800 p-2.5 rounded-lg flex-shrink-0">
                            @if(str_contains($doc->mime_type, 'pdf'))
                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @elseif(str_contains($doc->mime_type, 'image'))
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium truncate">{{ $doc->name }}</p>
                            @if($doc->description)
                                <p class="text-gray-500 text-xs truncate">{{ $doc->description }}</p>
                            @endif
                            <p class="text-gray-600 text-xs mt-0.5">{{ $doc->formatted_size }} · {{ $doc->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <a href="{{ route('companies.documents.view', [$company, $doc]) }}" target="_blank"
                                class="p-1.5 text-gray-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-lg transition-colors" title="Visualizar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('companies.documents.download', [$company, $doc]) }}"
                                class="p-1.5 text-gray-400 hover:text-emerald-400 hover:bg-emerald-400/10 rounded-lg transition-colors" title="Baixar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            <button onclick="deleteDocument({{ $doc->id }})"
                                class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-colors" title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div id="no-documents" class="text-center py-12 text-gray-500">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Nenhum documento ainda. Clique em "Enviar Documento" para anexar arquivos.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Aba: Faturas -->
            @if($isPremium)
            <div x-show="tab === 'invoices'" x-cloak class="mt-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-white">Faturas</h2>
                    <a href="{{ route('invoices.index') }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">
                        Ver todas as faturas →
                    </a>
                </div>
                <div class="space-y-2">
                    @forelse($company->invoices->sortByDesc('created_at') as $invoice)
                    <a href="{{ route('invoices.show', $invoice->uuid) }}"
                        class="flex items-center justify-between bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-gray-700 transition-colors">
                        <div>
                            <p class="text-white font-medium">{{ $invoice->description ?? 'Fatura #' . $invoice->id }}</p>
                            <p class="text-gray-500 text-xs">{{ $invoice->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">R$ {{ number_format($invoice->total ?? 0, 2, ',', '.') }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ match($invoice->status ?? '') { 'paid' => 'bg-emerald-500/20 text-emerald-300', 'cancelled' => 'bg-red-500/20 text-red-300', default => 'bg-gray-700 text-gray-400' } }}">
                                {{ match($invoice->status ?? '') { 'paid' => 'Paga', 'cancelled' => 'Cancelada', default => 'Pendente' } }}
                            </span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-12 text-gray-500">
                        <p>Nenhuma fatura para esta empresa.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const COMPANY_ID = {{ $company->id }};

        // ---- Edit Company ----
        function openCompanyModal() {
            document.getElementById('company-modal').classList.remove('hidden');
        }
        function closeCompanyModal() {
            document.getElementById('company-modal').classList.add('hidden');
        }
        async function saveCompany() {
            const id   = document.getElementById('company-id').value;
            const name = document.getElementById('company-name').value;
            const cnpj = document.getElementById('company-cnpj').value;
            if (!name.trim()) { showToast('Por favor, informe o nome da empresa!', TOAST_TYPES.WARNING); return; }
            if (!/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/.test(cnpj)) { showToast('CNPJ inválido!', TOAST_TYPES.WARNING); return; }
            const payload = {
                name, cnpj,
                active:               document.getElementById('company-active').checked,
                razao_social:         document.getElementById('company-razao-social').value.trim() || null,
                email:                document.getElementById('company-email').value.trim() || null,
                telefone:             document.getElementById('company-telefone').value.trim() || null,
                cep:                  document.getElementById('company-cep').value.trim() || null,
                logradouro:           document.getElementById('company-logradouro').value.trim() || null,
                numero:               document.getElementById('company-numero').value.trim() || null,
                complemento:          document.getElementById('company-complemento').value.trim() || null,
                bairro:               document.getElementById('company-bairro').value.trim() || null,
                cidade:               document.getElementById('company-cidade').value.trim() || null,
                uf:                   document.getElementById('company-uf').value.trim().toUpperCase() || null,
                inscricao_municipal:  document.getElementById('company-inscricao-municipal').value.trim() || null,
                inscricao_estadual:   document.getElementById('company-inscricao-estadual').value.trim() || null,
                responsavel_nome:     document.getElementById('company-responsavel-nome').value.trim() || null,
                responsavel_email:    document.getElementById('company-responsavel-email').value.trim() || null,
                responsavel_telefone: document.getElementById('company-responsavel-telefone').value.trim() || null,
            };
            try {
                const res  = await fetch(`/companies/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro ao salvar');
                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeCompanyModal();
                location.reload();
            } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
        }

        // ---- Link Project ----
        function openLinkProjectModal() {
            document.getElementById('link-project-modal-title').textContent = 'Vincular Projeto';
            document.getElementById('link-project-id').value = '';
            document.getElementById('link-edit-mode').value = 'false';
            document.getElementById('link-percentage').value = '';
            document.getElementById('link-project-select').value = '';
            document.getElementById('link-project-select-wrapper').classList.remove('hidden');
            document.getElementById('link-project-name-display').classList.add('hidden');
            document.getElementById('link-project-modal').classList.remove('hidden');
        }
        function editLinkProject(projectId, projectName, percentage) {
            document.getElementById('link-project-modal-title').textContent = 'Editar Porcentagem';
            document.getElementById('link-project-id').value = projectId;
            document.getElementById('link-edit-mode').value = 'true';
            document.getElementById('link-percentage').value = percentage;
            document.getElementById('link-project-name-text').textContent = projectName;
            document.getElementById('link-project-select-wrapper').classList.add('hidden');
            document.getElementById('link-project-name-display').classList.remove('hidden');
            document.getElementById('link-project-modal').classList.remove('hidden');
        }
        function closeLinkProjectModal() {
            document.getElementById('link-project-modal').classList.add('hidden');
        }
        async function saveLinkProject() {
            const projectId  = document.getElementById('link-project-id').value || document.getElementById('link-project-select').value;
            const percentage = parseFloat(document.getElementById('link-percentage').value);
            const isEdit     = document.getElementById('link-edit-mode').value === 'true';
            if (!projectId) { showToast('Selecione um projeto!', TOAST_TYPES.WARNING); return; }
            if (!percentage || percentage <= 0 || percentage > 100) { showToast('Porcentagem inválida!', TOAST_TYPES.WARNING); return; }
            const url    = isEdit ? `/companies/${COMPANY_ID}/projects/${projectId}` : `/companies/${COMPANY_ID}/projects`;
            const method = isEdit ? 'PUT' : 'POST';
            const body   = isEdit ? { percentage } : { project_id: projectId, percentage };
            try {
                const res  = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, body: JSON.stringify(body) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro ao salvar');
                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeLinkProjectModal();
                location.reload();
            } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
        }
        function detachProject(projectId) {
            showConfirm('Deseja remover o vínculo deste projeto?', async () => {
                try {
                    const res  = await fetch(`/companies/${COMPANY_ID}/projects/${projectId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message);
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    location.reload();
                } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
            }, 'Remover Vínculo');
        }

        // ---- Documents ----
        function openDocumentModal() { document.getElementById('document-modal').classList.remove('hidden'); }
        function closeDocumentModal() { document.getElementById('document-modal').classList.add('hidden'); }
        async function uploadDocument() {
            const file = document.getElementById('doc-file').files[0];
            if (!file) { showToast('Selecione um arquivo!', TOAST_TYPES.WARNING); return; }
            const btn = document.getElementById('btn-upload-doc');
            btn.textContent = 'Enviando...'; btn.disabled = true;
            const fd = new FormData();
            fd.append('file', file);
            fd.append('name', document.getElementById('doc-name').value.trim());
            fd.append('description', document.getElementById('doc-description').value.trim());
            try {
                const res  = await fetch(`/companies/${COMPANY_ID}/documents`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, body: fd });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro ao enviar');
                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeDocumentModal();
                location.reload();
            } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
            finally { btn.textContent = 'Enviar'; btn.disabled = false; }
        }
        function deleteDocument(id) {
            showConfirm('Deseja realmente excluir este documento?', async () => {
                try {
                    const res  = await fetch(`/companies/${COMPANY_ID}/documents/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message);
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    document.querySelector(`[data-doc-id="${id}"]`).remove();
                    if (!document.querySelector('[data-doc-id]')) {
                        document.getElementById('documents-list').innerHTML = '<div id="no-documents" class="text-center py-12 text-gray-500"><p>Nenhum documento ainda.</p></div>';
                    }
                } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
            }, 'Excluir Documento');
        }

        // ---- Notes ----
        let noteDatePicker;
        document.addEventListener('DOMContentLoaded', () => {
            noteDatePicker = flatpickr('#note-date', {
                locale: 'pt', dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y',
                disableMobile: true, defaultDate: new Date(),
            });
        });

        function openNoteModal() {
            document.getElementById('note-modal-title').textContent = 'Novo Registro';
            document.getElementById('note-id').value = '';
            document.getElementById('note-type').value = 'meeting';
            document.getElementById('note-title').value = '';
            document.getElementById('note-content').value = '';
            noteDatePicker?.setDate(new Date());
            document.getElementById('note-modal').classList.remove('hidden');
        }
        function closeNoteModal() { document.getElementById('note-modal').classList.add('hidden'); }
        function editNote(id, type, title, content, date) {
            document.getElementById('note-modal-title').textContent = 'Editar Registro';
            document.getElementById('note-id').value = id;
            document.getElementById('note-type').value = type;
            document.getElementById('note-title').value = title;
            document.getElementById('note-content').value = content;
            noteDatePicker?.setDate(date);
            document.getElementById('note-modal').classList.remove('hidden');
        }
        async function saveNote() {
            const id      = document.getElementById('note-id').value;
            const type    = document.getElementById('note-type').value;
            const title   = document.getElementById('note-title').value.trim();
            const content = document.getElementById('note-content').value.trim();
            const date    = document.getElementById('note-date').value;
            if (!title)   { showToast('Informe o título!', TOAST_TYPES.WARNING); return; }
            if (!content) { showToast('Informe o conteúdo!', TOAST_TYPES.WARNING); return; }
            if (!date)    { showToast('Informe a data!', TOAST_TYPES.WARNING); return; }
            const url    = id ? `/companies/${COMPANY_ID}/notes/${id}` : `/companies/${COMPANY_ID}/notes`;
            const method = id ? 'PUT' : 'POST';
            try {
                const res  = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, body: JSON.stringify({ type, title, content, note_date: date }) });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erro ao salvar');
                showToast(data.message, TOAST_TYPES.SUCCESS);
                closeNoteModal();
                location.reload();
            } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
        }
        function deleteNote(id) {
            showConfirm('Deseja realmente excluir este registro?', async () => {
                try {
                    const res  = await fetch(`/companies/${COMPANY_ID}/notes/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message);
                    showToast(data.message, TOAST_TYPES.SUCCESS);
                    document.querySelector(`[data-note-id="${id}"]`).remove();
                    if (!document.querySelector('[data-note-id]')) {
                        document.getElementById('notes-list').innerHTML = '<div id="no-notes" class="text-center py-12 text-gray-500"><p>Nenhum registro ainda.</p></div>';
                    }
                } catch (e) { showToast(e.message, TOAST_TYPES.ERROR); }
            }, 'Excluir Registro');
        }

        // ---- CNPJ/CEP lookup ----
        async function lookupCnpj() {
            const cnpj = document.getElementById('company-cnpj').value.replace(/\D/g, '');
            if (cnpj.length !== 14) { showToast('Digite o CNPJ completo antes de buscar.', TOAST_TYPES.WARNING); return; }
            const btn = document.getElementById('btn-lookup-cnpj');
            btn.textContent = '...'; btn.disabled = true;
            try {
                const res  = await fetch(`/cnpj/${cnpj}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'CNPJ não encontrado.', TOAST_TYPES.ERROR); return; }
                fillIfEmpty('company-name', data.nome_fantasia); fillIfEmpty('company-razao-social', data.razao_social);
                fillIfEmpty('company-email', data.email); fillIfEmpty('company-telefone', data.telefone);
                fillIfEmpty('company-cep', data.cep); fillIfEmpty('company-logradouro', data.logradouro);
                fillIfEmpty('company-numero', data.numero); fillIfEmpty('company-complemento', data.complemento);
                fillIfEmpty('company-bairro', data.bairro); fillIfEmpty('company-cidade', data.cidade); fillIfEmpty('company-uf', data.uf);
                showToast('Dados preenchidos!', TOAST_TYPES.SUCCESS);
            } catch (e) { showToast('Erro ao buscar CNPJ.', TOAST_TYPES.ERROR); }
            finally { btn.textContent = 'Buscar'; btn.disabled = false; }
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
                fillIfEmpty('company-logradouro', data.logradouro); fillIfEmpty('company-bairro', data.bairro);
                fillIfEmpty('company-cidade', data.cidade); fillIfEmpty('company-uf', data.uf);
                showToast('Endereço preenchido!', TOAST_TYPES.SUCCESS);
            } catch (e) { showToast('Erro ao buscar CEP.', TOAST_TYPES.ERROR); }
            finally { btn.textContent = 'Buscar'; btn.disabled = false; }
        }
        function fillIfEmpty(id, value) { const el = document.getElementById(id); if (el && !el.value && value) el.value = value; }
        function formatCnpj(input) {
            let v = input.value.replace(/\D/g, ''); if (v.length > 14) v = v.substring(0, 14);
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
            if (e.key === 'Escape') { closeCompanyModal(); closeLinkProjectModal(); closeDocumentModal(); closeNoteModal(); }
        });
    </script>
    @endpush
</x-app-layout>
