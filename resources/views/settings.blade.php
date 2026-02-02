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
                        <label class="block text-sm font-medium text-gray-400 mb-2">Valor Extra Mensal</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">R$</span>
                            <input type="text" id="extra-value" inputmode="decimal"
                                value="{{ number_format($settings->extra_value, 2, ',', '.') }}"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg pl-12 pr-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                                onkeyup="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)"/>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Ex: Home Office, ajuda de custo, etc.</p>
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

        <!-- Projetos -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Projetos
                </h2>
                <button onclick="openProjectModal()"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center gap-2 text-sm hover:shadow-lg hover:shadow-cyan-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Projeto
                </button>
            </div>

            <div id="projects-list" class="space-y-3">
                @forelse($projects as $project)
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-gray-600 transition-colors" data-project-id="{{ $project->id }}">
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
                            <button onclick="editProject({{ $project->id }}, '{{ $project->name }}', {{ $project->active ? 'true' : 'false' }}, {{ $project->is_default ? 'true' : 'false' }})"
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
                @empty
                    <div id="no-projects" class="text-center py-8 text-gray-500">
                        Nenhum projeto cadastrado. Crie um novo projeto para comecar.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

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

            if (hourlyRate < 0 || extraValue < 0) {
                showToast('Os valores não podem ser negativos!', TOAST_TYPES.WARNING);
                return;
            }

            try {
                const response = await fetch('/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ hourly_rate: hourlyRate, extra_value: extraValue })
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
            }
        });
    </script>
    @endpush
</x-app-layout>
