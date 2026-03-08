<x-app-layout>
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Modal de Confirmação (padrão do sistema) -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-red-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white" id="confirm-title">Confirmar</h3>
            </div>
            <p class="text-gray-400 mb-6" id="confirm-message">Deseja realmente realizar esta ação?</p>
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

    <div class="max-w-4xl mx-auto p-4 sm:p-6 space-y-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Supervisores</h1>
                <p class="text-gray-400 text-sm mt-1">Defina quem pode acompanhar seus dados de horas e faturamento</p>
            </div>
            <button onclick="openInviteModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar supervisor
            </button>
        </div>

        @if(session('error'))
            <div class="bg-red-900/40 border border-red-500/50 text-red-200 rounded-lg px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Supervisores Ativos -->
        <div>
            <h2 class="text-base font-semibold text-gray-300 mb-3">Com acesso agora</h2>

            @if($accesses->isEmpty())
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                    <svg class="w-10 h-10 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-gray-500">Nenhum supervisor ativo ainda.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($accesses as $access)
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4" id="access-{{ $access->id }}">
                            <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-white">{{ $access->supervisor->name }}</span>
                                        @if($access->isExpired())
                                            <span class="text-xs px-2 py-0.5 bg-red-900/50 text-red-300 rounded-full">Expirado</span>
                                        @elseif($access->expires_at && $access->expires_at->diffInDays(now()) <= 3)
                                            <span class="text-xs px-2 py-0.5 bg-orange-900/50 text-orange-300 rounded-full">Expira em breve</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-400 text-sm">{{ $access->supervisor->email }}</p>

                                    <!-- Permissões -->
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="text-xs px-2 py-1 bg-cyan-900/40 text-cyan-300 rounded-full">Lançamentos</span>
                                        @if($access->can_view_financials)
                                            <span class="text-xs px-2 py-1 bg-emerald-900/40 text-emerald-300 rounded-full">Valores financeiros</span>
                                        @endif
                                        @if($access->can_view_analytics)
                                            <span class="text-xs px-2 py-1 bg-purple-900/40 text-purple-300 rounded-full">Analytics</span>
                                        @endif
                                        @if($access->can_export)
                                            <span class="text-xs px-2 py-1 bg-indigo-900/40 text-indigo-300 rounded-full">Exportação</span>
                                        @endif
                                    </div>

                                    <!-- Validade -->
                                    <p class="text-xs text-gray-500 mt-2">
                                        @if($access->expires_at === null)
                                            Acesso permanente
                                        @else
                                            <span class="{{ $access->isExpired() ? 'text-red-400' : ($access->expires_at->diffInDays(now()) <= 3 ? 'text-orange-400' : 'text-gray-400') }}">
                                                Válido até {{ $access->expires_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <div class="flex gap-2 shrink-0">
                                    <button onclick="openEditModal('{{ $access->uuid }}', {{ (int)$access->can_view_financials }}, {{ (int)$access->can_view_analytics }}, {{ (int)$access->can_export }}, '{{ $access->expires_at?->format('Y-m-d\TH:i') ?? '' }}')"
                                        class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white rounded-lg text-sm transition-colors">
                                        Editar
                                    </button>
                                    <button onclick="confirmRevoke('{{ $access->uuid }}', '{{ $access->supervisor->name }}')"
                                        class="px-3 py-1.5 bg-red-900/40 hover:bg-red-900/70 text-red-400 hover:text-red-300 rounded-lg text-sm transition-colors">
                                        Revogar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Convites Enviados -->
        @if($invitations->isNotEmpty())
        <div>
            <h2 class="text-base font-semibold text-gray-300 mb-3">Convites enviados</h2>
            <div class="space-y-3">
                @foreach($invitations as $invitation)
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4" id="invitation-{{ $invitation->id }}">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-white">{{ $invitation->supervisor->name }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $invitation->status === 'pending' ? 'bg-yellow-900/50 text-yellow-300' : 'bg-red-900/50 text-red-300' }}">
                                        {{ $invitation->status === 'pending' ? 'Aguardando resposta' : 'Recusado' }}
                                    </span>
                                </div>
                                <p class="text-gray-400 text-sm">{{ $invitation->supervisor->email }}</p>
                                <p class="text-xs text-gray-500 mt-1">Enviado em {{ $invitation->created_at->format('d/m/Y \à\s H:i') }}</p>
                            </div>
                            @if($invitation->status === 'pending')
                            <button onclick="confirmCancelInvite('{{ $invitation->uuid }}', '{{ $invitation->supervisor->name }}')"
                                class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm transition-colors shrink-0">
                                Cancelar convite
                            </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Link para painel de supervisão -->
        <div class="border-t border-gray-800 pt-6 flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Você também pode acompanhar dados de outros usuários que te convidaram.</p>
            </div>
            <a href="{{ route('supervisor.index') }}" class="text-sm text-cyan-400 hover:text-cyan-300 transition-colors whitespace-nowrap ml-4">
                Ver painel →
            </a>
        </div>

    </div>

    <!-- Modal: Convidar Supervisor -->
    <div id="invite-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeInviteModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-cyan-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Convidar supervisor</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">E-mail do supervisor</label>
                    <input type="email" id="invite-email" placeholder="email@exemplo.com"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"/>
                    <p class="text-xs text-gray-500 mt-1">A pessoa precisa ter uma conta no Horas PJ.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">O que ele poderá ver</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="perm-financials" class="w-4 h-4 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Valores financeiros (receita, valor/hora)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="perm-analytics" class="w-4 h-4 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Analytics</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="perm-export" class="w-4 h-4 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Exportar relatórios</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Por quanto tempo?</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="invite-expiry" id="expiry-permanent" value="permanent" checked
                                onchange="toggleExpiryField(false)"
                                class="text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Sem prazo — fica até eu revogar</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="invite-expiry" id="expiry-date" value="date"
                                onchange="toggleExpiryField(true)"
                                class="text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Válido até uma data específica</span>
                        </label>
                        <div id="expiry-date-field" class="hidden pl-7">
                            <input type="text" id="invite-expires-at" placeholder="dd/mm/aaaa hh:mm"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end mt-6">
                <button onclick="closeInviteModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancelar
                </button>
                <button id="invite-submit-btn" onclick="sendInvite()" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    Enviar convite
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Permissões -->
    <div id="edit-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-md">
            <input type="hidden" id="edit-access-uuid"/>
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-gray-700 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Editar permissões</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">O que ele pode ver</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="edit-perm-financials" class="w-4 h-4 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Valores financeiros</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="edit-perm-analytics" class="w-4 h-4 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Analytics</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="edit-perm-export" class="w-4 h-4 rounded bg-gray-800 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Exportar relatórios</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Prazo do acesso</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="edit-expiry" id="edit-expiry-permanent" value="permanent"
                                onchange="toggleEditExpiryField(false)"
                                class="text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Sem prazo</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="edit-expiry" id="edit-expiry-date" value="date"
                                onchange="toggleEditExpiryField(true)"
                                class="text-cyan-500 focus:ring-cyan-500 focus:ring-offset-gray-900"/>
                            <span class="text-gray-300 text-sm">Válido até data específica</span>
                        </label>
                        <div id="edit-expiry-date-field" class="hidden pl-7">
                            <input type="text" id="edit-expires-at" placeholder="dd/mm/aaaa hh:mm"
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 justify-end mt-6">
                <button onclick="closeEditModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancelar
                </button>
                <button onclick="saveEdit()" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors">
                    Salvar
                </button>
            </div>
        </div>
    </div>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    // Confirm Modal (padrão do sistema)
    let confirmCallback = null;
    function showConfirm(message, callback, title = 'Confirmar') {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        confirmCallback = callback;
        document.getElementById('confirm-btn').onclick = function() {
            const cb = confirmCallback;
            closeConfirmModal();
            if (cb) cb();
        };
        document.getElementById('confirm-modal').classList.remove('hidden');
    }
    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
        confirmCallback = null;
    }

    // Toast
    function toast(msg, type = 'success') {
        const isDark = document.documentElement.classList.contains('dark');
        const colors = type === 'success'
            ? (isDark ? 'bg-emerald-900 border-emerald-500 text-emerald-200' : 'bg-emerald-100 border-emerald-600 text-emerald-900')
            : (isDark ? 'bg-red-900 border-red-500 text-red-200' : 'bg-red-100 border-red-600 text-red-900');
        const el = document.createElement('div');
        el.className = `border rounded-lg px-4 py-3 text-sm shadow-lg ${colors}`;
        el.textContent = msg;
        document.getElementById('toast-container').appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }

    // Invite Modal
    function openInviteModal() {
        document.getElementById('invite-email').value = '';
        document.getElementById('perm-financials').checked = false;
        document.getElementById('perm-analytics').checked = false;
        document.getElementById('perm-export').checked = false;
        document.getElementById('expiry-permanent').checked = true;
        toggleExpiryField(false);
        document.getElementById('invite-modal').classList.remove('hidden');
    }
    function closeInviteModal() {
        document.getElementById('invite-modal').classList.add('hidden');
    }
    function toggleExpiryField(show) {
        document.getElementById('expiry-date-field').classList.toggle('hidden', !show);
    }

    function sendInvite() {
        const email = document.getElementById('invite-email').value.trim();
        if (!email) { toast('Informe o e-mail do supervisor.', 'error'); return; }

        const isPermanent = document.getElementById('expiry-permanent').checked;
        const expiresRaw = document.getElementById('invite-expires-at').value;

        let expiresAt = null;
        if (!isPermanent) {
            if (!expiresRaw) { toast('Informe a data de validade.', 'error'); return; }
            const parts = expiresRaw.match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/);
            if (!parts) { toast('Formato inválido. Use dd/mm/aaaa hh:mm.', 'error'); return; }
            expiresAt = `${parts[3]}-${parts[2]}-${parts[1]} ${parts[4]}:${parts[5]}`;
        }

        const btn = document.getElementById('invite-submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Enviando...';

        fetch('{{ route("supervisors.invite") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                email,
                can_view_financials: document.getElementById('perm-financials').checked,
                can_view_analytics: document.getElementById('perm-analytics').checked,
                can_export: document.getElementById('perm-export').checked,
                expires_at: expiresAt,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toast(data.message);
                closeInviteModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                toast(data.message || 'Não foi possível enviar o convite.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Enviar convite';
            }
        })
        .catch(() => {
            toast('Erro de conexão. Tente novamente.', 'error');
            btn.disabled = false;
            btn.innerHTML = 'Enviar convite';
        });
    }

    // Edit Modal
    function openEditModal(uuid, financials, analytics, canExport, expiresAt) {
        document.getElementById('edit-access-uuid').value = uuid;
        document.getElementById('edit-perm-financials').checked = !!financials;
        document.getElementById('edit-perm-analytics').checked = !!analytics;
        document.getElementById('edit-perm-export').checked = !!canExport;

        if (expiresAt) {
            document.getElementById('edit-expiry-date').checked = true;
            toggleEditExpiryField(true);
            const d = new Date(expiresAt);
            const pad = n => String(n).padStart(2, '0');
            document.getElementById('edit-expires-at').value =
                `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
        } else {
            document.getElementById('edit-expiry-permanent').checked = true;
            toggleEditExpiryField(false);
        }

        document.getElementById('edit-modal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }
    function toggleEditExpiryField(show) {
        document.getElementById('edit-expiry-date-field').classList.toggle('hidden', !show);
    }

    function saveEdit() {
        const uuid = document.getElementById('edit-access-uuid').value;
        const isPermanent = document.getElementById('edit-expiry-permanent').checked;
        const expiresRaw = document.getElementById('edit-expires-at').value;

        let expiresAt = null;
        let permanent = false;

        if (isPermanent) {
            permanent = true;
        } else {
            if (!expiresRaw) { toast('Informe a data de validade.', 'error'); return; }
            const parts = expiresRaw.match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/);
            if (!parts) { toast('Formato inválido. Use dd/mm/aaaa hh:mm.', 'error'); return; }
            expiresAt = `${parts[3]}-${parts[2]}-${parts[1]} ${parts[4]}:${parts[5]}`;
        }

        fetch(`/settings/supervisors/${uuid}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                can_view_financials: document.getElementById('edit-perm-financials').checked,
                can_view_analytics: document.getElementById('edit-perm-analytics').checked,
                can_export: document.getElementById('edit-perm-export').checked,
                expires_at: expiresAt,
                permanent,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toast(data.message);
                closeEditModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                toast(data.message || 'Não foi possível salvar.', 'error');
            }
        })
        .catch(() => toast('Erro de conexão. Tente novamente.', 'error'));
    }

    function confirmRevoke(uuid, name) {
        showConfirm(
            `Revogar o acesso de ${name}? Ele perderá a visibilidade dos seus dados imediatamente.`,
            () => revokeAccess(uuid),
            'Revogar acesso'
        );
    }

    function revokeAccess(uuid) {
        fetch(`/settings/supervisors/${uuid}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toast(data.message);
                // Recarrega para atualizar a lista
                setTimeout(() => location.reload(), 1200);
            } else {
                toast(data.message || 'Não foi possível revogar.', 'error');
            }
        })
        .catch(() => toast('Erro de conexão. Tente novamente.', 'error'));
    }

    function confirmCancelInvite(uuid, name) {
        showConfirm(
            `Cancelar o convite para ${name}?`,
            () => cancelInvite(uuid),
            'Cancelar convite'
        );
    }

    function cancelInvite(uuid) {
        fetch(`/settings/supervisors/invitations/${uuid}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toast(data.message);
                setTimeout(() => location.reload(), 1200);
            } else {
                toast(data.message || 'Não foi possível cancelar.', 'error');
            }
        })
        .catch(() => toast('Erro de conexão. Tente novamente.', 'error'));
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fpConfig = {
            enableTime: true,
            dateFormat: 'd/m/Y H:i',
            time_24hr: true,
            minDate: 'today',
        };
        flatpickr('#invite-expires-at', fpConfig);
        flatpickr('#edit-expires-at', fpConfig);
    });
    </script>
</x-app-layout>
