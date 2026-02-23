<x-app-layout>
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Gerenciar Changelog</h1>
                <p class="text-sm text-gray-400 mt-1">Publique novidades e atualizações do sistema para todos os usuários</p>
            </div>
            <button
                onclick="window.dispatchEvent(new CustomEvent('open-create-changelog'))"
                class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-medium rounded-lg transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Novidade
            </button>
        </div>

        <!-- Table -->
        <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
            @if($changelogs->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p>Nenhum changelog cadastrado ainda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Título</th>
                                <th class="px-4 py-3 text-left">Versão</th>
                                <th class="px-4 py-3 text-left">Itens</th>
                                <th class="px-4 py-3 text-left">Notificação</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Publicado em</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @foreach($changelogs as $changelog)
                                @php
                                    $categoryColors = [
                                        'feature'     => 'bg-cyan-500/20 text-cyan-300',
                                        'improvement' => 'bg-green-500/20 text-green-300',
                                        'bugfix'      => 'bg-yellow-500/20 text-yellow-300',
                                        'hotfix'      => 'bg-red-500/20 text-red-300',
                                    ];
                                    $categoryLabels = [
                                        'feature'     => 'Nova Funcionalidade',
                                        'improvement' => 'Melhoria',
                                        'bugfix'      => 'Correção de Bug',
                                        'hotfix'      => 'Correção Urgente',
                                    ];
                                    $styleLabels = [
                                        'badge' => 'Badge',
                                        'modal' => 'Modal',
                                        'both'  => 'Badge + Modal',
                                    ];
                                    $editData = [
                                        'id' => $changelog->id,
                                        'title' => $changelog->title,
                                        'version' => $changelog->version,
                                        'notification_style' => $changelog->notification_style,
                                        'is_published' => $changelog->is_published,
                                        'items' => $changelog->items->map(fn($i) => [
                                            'category' => $i->category,
                                            'description' => $i->description,
                                            'sort_order' => $i->sort_order,
                                        ])->values()->toArray(),
                                    ];
                                @endphp
                                <tr class="text-gray-300 hover:bg-gray-800/30 transition-colors"
                                    x-data="{}"
                                >
                                    <td class="px-4 py-3 font-medium text-white">{{ $changelog->title }}</td>
                                    <td class="px-4 py-3">
                                        @if($changelog->version)
                                            <span class="font-mono text-xs bg-gray-700 text-gray-300 px-2 py-0.5 rounded">
                                                v{{ $changelog->version }}
                                            </span>
                                        @else
                                            <span class="text-gray-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($changelog->items as $item)
                                                <span class="text-xs px-1.5 py-0.5 rounded {{ $categoryColors[$item->category] ?? 'bg-gray-500/20 text-gray-300' }}">
                                                    {{ $categoryLabels[$item->category] ?? $item->category }}
                                                </span>
                                            @endforeach
                                            @if($changelog->items->isEmpty())
                                                <span class="text-gray-600 text-xs">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-400">
                                        {{ $styleLabels[$changelog->notification_style] ?? $changelog->notification_style }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($changelog->is_published)
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-500/20 text-green-300">Publicado</span>
                                        @else
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-500/20 text-gray-400">Rascunho</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">
                                        {{ $changelog->published_at ? $changelog->published_at->format('d/m/Y H:i') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="$dispatch('open-edit-changelog', {{ json_encode($editData) }})"
                                                class="p-1.5 text-gray-400 hover:text-cyan-400 hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Editar"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button
                                                @click="deleteChangelog({{ $changelog->id }})"
                                                class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Excluir"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
        x-data="changelogAdmin()"
        @open-create-changelog.window="openCreate()"
        @open-edit-changelog.window="openEdit($event.detail)"
    >
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="open = false"></div>
            <div
                class="relative bg-gray-900 border border-gray-700 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-5 border-b border-gray-700 shrink-0">
                    <h3 class="text-lg font-semibold text-white" x-text="editId ? 'Editar Novidade' : 'Nova Novidade'"></h3>
                    <button @click="open = false" class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto flex-1 p-5 space-y-4">
                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Título <span class="text-red-400">*</span></label>
                        <input
                            type="text"
                            x-model="form.title"
                            placeholder="Ex: Novidades de Fevereiro 2026"
                            class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm"
                        >
                    </div>

                    <!-- Versão e Notificação -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Versão <span class="text-gray-500">(opcional)</span></label>
                            <input
                                type="text"
                                x-model="form.version"
                                placeholder="Ex: 1.5.0"
                                class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Notificação <span class="text-red-400">*</span></label>
                            <select
                                x-model="form.notification_style"
                                class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm"
                            >
                                <option value="badge">Apenas Badge</option>
                                <option value="modal">Apenas Modal</option>
                                <option value="both">Badge + Modal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Itens -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-300">Itens <span class="text-red-400">*</span></label>
                            <button
                                type="button"
                                @click="addItem()"
                                class="inline-flex items-center gap-1 text-xs text-cyan-400 hover:text-cyan-300 transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Adicionar item
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(item, index) in form.items" :key="index">
                                <div class="flex items-start gap-2">
                                    <select
                                        x-model="item.category"
                                        class="bg-gray-800 border border-gray-600 rounded-lg px-2 py-2 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 text-xs shrink-0"
                                    >
                                        <option value="feature">Nova Funcionalidade</option>
                                        <option value="improvement">Melhoria</option>
                                        <option value="bugfix">Correção de Bug</option>
                                        <option value="hotfix">Correção Urgente</option>
                                    </select>
                                    <input
                                        type="text"
                                        x-model="item.description"
                                        placeholder="Descrição do item..."
                                        class="flex-1 bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm"
                                    >
                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        x-show="form.items.length > 1"
                                        class="p-2 text-gray-500 hover:text-red-400 hover:bg-gray-700 rounded-lg transition-colors shrink-0"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Publicar -->
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.is_published" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:ring-2 peer-focus:ring-cyan-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                        </label>
                        <span class="text-sm text-gray-300">Publicar imediatamente</span>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-gray-700 shrink-0 flex justify-end gap-3">
                    <button
                        @click="open = false"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded-lg transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="save()"
                        :disabled="saving"
                        class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <span x-show="!saving" x-text="editId ? 'Salvar Alterações' : 'Criar'"></span>
                        <span x-show="saving">Salvando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    const TOAST_TYPES = { SUCCESS: 'success', ERROR: 'error', WARNING: 'warning', INFO: 'info' };

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function showToast(message, type = TOAST_TYPES.INFO, duration = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const styles = {
            success: { bg: '#064e3b', border: '#10b981', text: '#a7f3d0', icon: '#34d399' },
            error:   { bg: '#7f1d1d', border: '#ef4444', text: '#fecaca', icon: '#f87171' },
            warning: { bg: '#78350f', border: '#f59e0b', text: '#fde68a', icon: '#fbbf24' },
            info:    { bg: '#164e63', border: '#06b6d4', text: '#a5f3fc', icon: '#22d3ee' },
        };
        const s = styles[type] || styles.info;
        const toast = document.createElement('div');
        toast.style.cssText = 'transform:translateX(100%);opacity:0;transition:all 0.3s ease-out;';
        toast.innerHTML = `<div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:10px;border:2px solid ${s.border};background:${s.bg};color:${s.text};box-shadow:0 10px 25px rgba(0,0,0,0.4);font-size:14px;font-weight:500;min-width:280px;max-width:400px;">
            <span style="flex:1">${escapeHtml(message)}</span>
            <button onclick="this.closest('div').parentElement.remove()" style="background:none;border:none;cursor:pointer;opacity:0.7;color:${s.text};padding:4px;">✕</button>
        </div>`;
        container.appendChild(toast);
        requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; });
        setTimeout(() => { toast.style.transform = 'translateX(100%)'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, duration);
    }

    function changelogAdmin() {
        return {
            open: false,
            saving: false,
            editId: null,
            form: {
                title: '',
                version: '',
                notification_style: 'badge',
                is_published: false,
                items: [{ category: 'feature', description: '', sort_order: 0 }],
            },

            openCreate() {
                this.editId = null;
                this.form = {
                    title: '',
                    version: '',
                    notification_style: 'badge',
                    is_published: false,
                    items: [{ category: 'feature', description: '', sort_order: 0 }],
                };
                this.open = true;
            },

            openEdit(changelog) {
                this.editId = changelog.id;
                this.form = {
                    title: changelog.title,
                    version: changelog.version || '',
                    notification_style: changelog.notification_style,
                    is_published: changelog.is_published,
                    items: changelog.items && changelog.items.length > 0
                        ? changelog.items.map((item, i) => ({
                            category: item.category,
                            description: item.description,
                            sort_order: item.sort_order ?? i,
                        }))
                        : [{ category: 'feature', description: '', sort_order: 0 }],
                };
                this.open = true;
            },

            addItem() {
                this.form.items.push({
                    category: 'feature',
                    description: '',
                    sort_order: this.form.items.length,
                });
            },

            removeItem(index) {
                if (this.form.items.length > 1) {
                    this.form.items.splice(index, 1);
                }
            },

            async save() {
                if (this.saving) return;

                if (!this.form.title.trim()) {
                    showToast('Preencha o título.', TOAST_TYPES.ERROR);
                    return;
                }

                const hasEmptyItem = this.form.items.some(i => !i.description.trim());
                if (hasEmptyItem) {
                    showToast('Preencha a descrição de todos os itens.', TOAST_TYPES.ERROR);
                    return;
                }

                this.saving = true;
                const url = this.editId
                    ? `/admin/changelogs/${this.editId}`
                    : '/admin/changelogs';
                const method = this.editId ? 'PUT' : 'POST';

                const payload = {
                    title: this.form.title,
                    version: this.form.version,
                    notification_style: this.form.notification_style,
                    is_published: this.form.is_published,
                    items: this.form.items.map((item, i) => ({
                        category: item.category,
                        description: item.description,
                        sort_order: i,
                    })),
                };

                try {
                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast(this.editId ? 'Changelog atualizado!' : 'Changelog criado!', TOAST_TYPES.SUCCESS);
                        this.open = false;
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        showToast('Erro ao salvar changelog.', TOAST_TYPES.ERROR);
                    }
                } catch (e) {
                    showToast('Erro ao salvar changelog.', TOAST_TYPES.ERROR);
                } finally {
                    this.saving = false;
                }
            },
        };
    }

    async function deleteChangelog(id) {
        if (!confirm('Deseja realmente excluir este changelog?')) return;

        try {
            const response = await fetch(`/admin/changelogs/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            });
            const data = await response.json();
            if (data.success) {
                showToast('Changelog excluído!', TOAST_TYPES.SUCCESS);
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (e) {
            showToast('Erro ao excluir changelog.', TOAST_TYPES.ERROR);
        }
    }
    </script>
    @endpush
</x-app-layout>
