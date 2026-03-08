<x-app-layout>
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <div class="max-w-3xl mx-auto p-4 sm:p-6 space-y-6">

        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('supervisor.index') }}" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Convites recebidos</h1>
                <p class="text-gray-400 text-sm">Alguém quer que você acompanhe os dados de horas dele</p>
            </div>
        </div>

        @if($invitations->isEmpty())
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
                <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-400">Nenhum convite pendente no momento.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($invitations as $invitation)
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5" id="invitation-{{ $invitation->id }}">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium">{{ $invitation->user->name }}</p>
                                <p class="text-gray-400 text-sm">{{ $invitation->user->email }}</p>
                                <p class="text-xs text-gray-500 mt-1">Recebido {{ $invitation->created_at->diffForHumans() }}</p>

                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 mb-1.5">Você poderá ver:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="text-xs px-2 py-1 bg-cyan-900/40 text-cyan-300 rounded-full">Lançamentos de horas</span>
                                        @if($invitation->can_view_financials)
                                            <span class="text-xs px-2 py-1 bg-emerald-900/40 text-emerald-300 rounded-full">Valores financeiros</span>
                                        @endif
                                        @if($invitation->can_view_analytics)
                                            <span class="text-xs px-2 py-1 bg-purple-900/40 text-purple-300 rounded-full">Analytics</span>
                                        @endif
                                        @if($invitation->can_export)
                                            <span class="text-xs px-2 py-1 bg-indigo-900/40 text-indigo-300 rounded-full">Exportar relatórios</span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">
                                    @if($invitation->expires_at === null)
                                        Sem prazo definido
                                    @else
                                        Válido até {{ $invitation->expires_at->format('d/m/Y \à\s H:i') }}
                                    @endif
                                </p>
                            </div>

                            <div class="flex gap-2 shrink-0">
                                <button onclick="rejectInvitation('{{ $invitation->uuid }}', '{{ $invitation->user->name }}')"
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm transition-colors">
                                    Recusar
                                </button>
                                <button onclick="acceptInvitation('{{ $invitation->uuid }}')"
                                    class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-sm transition-colors">
                                    Aceitar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-black/60" onclick="closeConfirmModal()"></div>
        <div class="relative bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-sm mx-4 shadow-2xl">
            <h3 id="confirm-title" class="text-lg font-semibold text-white mb-2">Confirmar</h3>
            <p id="confirm-message" class="text-gray-400 text-sm mb-6"></p>
            <div class="flex gap-3 justify-end">
                <button onclick="closeConfirmModal()"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm transition-colors">
                    Cancelar
                </button>
                <button id="confirm-btn"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function showConfirm(message, callback, title = 'Confirmar') {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        const btn = document.getElementById('confirm-btn');
        btn.onclick = () => { closeConfirmModal(); callback(); };
        document.getElementById('confirm-modal').classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirm-modal').classList.add('hidden');
    }

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

    function acceptInvitation(uuid) {
        fetch(`/supervisor/invitations/${uuid}/accept`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toast(data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                toast(data.message || 'Não foi possível aceitar o convite.', 'error');
            }
        })
        .catch(() => toast('Erro de conexão. Tente novamente.', 'error'));
    }

    function rejectInvitation(uuid, name) {
        showConfirm(`Recusar o convite de ${name}?`, () => {
            fetch(`/supervisor/invitations/${uuid}/reject`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    toast(data.message);
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toast(data.message || 'Não foi possível recusar.', 'error');
                }
            })
            .catch(() => toast('Erro de conexão. Tente novamente.', 'error'));
        }, 'Recusar convite');
    }
    </script>
</x-app-layout>
