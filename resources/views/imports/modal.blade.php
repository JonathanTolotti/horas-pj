<!-- Modal de Importacao CSV -->
<div id="import-csv-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeImportModal()"></div>
    <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-cyan-500/20 p-2 rounded-lg">
                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-white">Importar CSV</h3>
        </div>

        <!-- Upload Area -->
        <div id="import-upload-area" class="mb-6">
            <div id="import-dropzone"
                class="border-2 border-dashed border-gray-600 rounded-xl p-8 text-center hover:border-cyan-500 transition-colors cursor-pointer"
                ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)"
                ondrop="handleDrop(event)"
                onclick="document.getElementById('import-file-input').click()">
                <input type="file" id="import-file-input" accept=".csv,.txt" class="hidden" onchange="handleFileSelect(event)">
                <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-gray-400 mb-2">Arraste um arquivo CSV ou clique para selecionar</p>
                <p class="text-gray-500 text-sm">Formato: Data;Inicio;Fim;Projeto;Descricao</p>
            </div>
            <p id="import-file-name" class="text-sm text-gray-400 mt-2 hidden"></p>
        </div>

        <!-- Preview Area -->
        <div id="import-preview-area" class="hidden mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-white font-medium">Preview da Importacao</h4>
                <button onclick="resetImportModal()" class="text-gray-400 hover:text-white text-sm">
                    Escolher outro arquivo
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-4">
                    <p class="text-emerald-400 text-2xl font-bold" id="import-valid-count">0</p>
                    <p class="text-gray-400 text-sm">Lancamentos validos</p>
                </div>
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                    <p class="text-red-400 text-2xl font-bold" id="import-error-count">0</p>
                    <p class="text-gray-400 text-sm">Com erros</p>
                </div>
            </div>

            <!-- Valid Entries Preview -->
            <div id="import-valid-entries" class="mb-4 hidden">
                <h5 class="text-gray-400 text-sm mb-2">Lancamentos a importar:</h5>
                <div class="max-h-40 overflow-y-auto bg-gray-800 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-700 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left text-gray-400">Data</th>
                                <th class="px-3 py-2 text-left text-gray-400">Horario</th>
                                <th class="px-3 py-2 text-left text-gray-400">Horas</th>
                                <th class="px-3 py-2 text-left text-gray-400">Projeto</th>
                                <th class="px-3 py-2 text-left text-gray-400">Descricao</th>
                            </tr>
                        </thead>
                        <tbody id="import-valid-table" class="divide-y divide-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Errors Preview -->
            <div id="import-errors" class="mb-4 hidden">
                <h5 class="text-red-400 text-sm mb-2">Linhas com erro:</h5>
                <div class="max-h-32 overflow-y-auto bg-red-900/20 border border-red-500/30 rounded-lg p-3">
                    <ul id="import-error-list" class="space-y-2 text-sm">
                    </ul>
                </div>
            </div>

            <!-- Ignore overlaps option -->
            <div class="flex items-center gap-2 mb-4">
                <input type="checkbox" id="import-ignore-overlaps" class="rounded bg-gray-700 border-gray-600 text-cyan-500 focus:ring-cyan-500">
                <label for="import-ignore-overlaps" class="text-gray-400 text-sm">Ignorar sobreposicoes de horario</label>
            </div>
        </div>

        <!-- Loading State -->
        <div id="import-loading" class="hidden mb-6">
            <div class="flex items-center justify-center gap-3 py-8">
                <svg class="animate-spin h-8 w-8 text-cyan-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-400" id="import-loading-text">Processando...</span>
            </div>
        </div>

        <!-- Result Area -->
        <div id="import-result-area" class="hidden mb-6">
            <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-emerald-400 font-medium">Importacao concluida!</span>
                </div>
                <p class="text-gray-400 text-sm" id="import-result-text"></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 justify-end">
            <button onclick="closeImportModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                Fechar
            </button>
            <button id="import-preview-btn" onclick="previewImport()" class="hidden px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Visualizar
            </button>
            <button id="import-execute-btn" onclick="executeImport()" class="hidden px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Importar
            </button>
        </div>
    </div>
</div>
