@auth
@php
    $categoryLabels = [
        'feature'     => 'Nova Funcionalidade',
        'improvement' => 'Melhoria',
        'bugfix'      => 'Correção de Bug',
        'hotfix'      => 'Correção Urgente',
    ];
    $categoryBorder = [
        'feature'     => 'border-cyan-500',
        'improvement' => 'border-green-500',
        'bugfix'      => 'border-yellow-500',
        'hotfix'      => 'border-red-500',
    ];
    $categoryLabelColor = [
        'feature'     => 'text-cyan-400',
        'improvement' => 'text-green-400',
        'bugfix'      => 'text-yellow-400',
        'hotfix'      => 'text-red-400',
    ];
@endphp

<div
    x-data="changelogModal()"
    x-init="init()"
    @open-changelog-modal.window="open = true"
>
    <!-- Modal Overlay -->
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
            @click="closeModal()"
        ></div>

        <div
            class="relative bg-gray-900 border border-gray-700 rounded-xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-cyan-500/20 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white">Novidades do Sistema</h2>
                        <p class="text-xs text-gray-400" x-text="`${changelogs.length} novidade${changelogs.length !== 1 ? 's' : ''} não lida${changelogs.length !== 1 ? 's' : ''}`"></p>
                    </div>
                </div>
                <button @click="closeModal()" class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="overflow-y-auto flex-1 p-5 space-y-4">
                @forelse($unreadChangelogs ?? [] as $changelog)
                    <div class="bg-gray-800/60 border border-gray-700/50 rounded-lg p-4 space-y-3"
                         data-changelog-id="{{ $changelog->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                @if($changelog->version)
                                    <span class="text-xs font-mono text-gray-400 bg-gray-700 px-2 py-0.5 rounded">
                                        v{{ $changelog->version }}
                                    </span>
                                @endif
                            </div>
                            @if($changelog->published_at)
                                <span class="text-xs text-gray-500 shrink-0">
                                    {{ $changelog->published_at->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-white">{{ $changelog->title }}</h3>
                        @if($changelog->items->isNotEmpty())
                            @php
                                $categoryOrder = ['feature', 'improvement', 'bugfix', 'hotfix'];
                                $grouped = $changelog->items->groupBy('category');
                            @endphp
                            <div class="space-y-3">
                                @foreach($categoryOrder as $cat)
                                    @if($grouped->has($cat))
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wide mb-1.5 {{ $categoryLabelColor[$cat] ?? 'text-gray-400' }}">
                                                {{ $categoryLabels[$cat] ?? $cat }}
                                            </p>
                                            <ul class="space-y-1.5 border-l-2 pl-3 {{ $categoryBorder[$cat] ?? 'border-gray-500' }}">
                                                @foreach($grouped[$cat] as $item)
                                                    <li class="text-sm text-gray-300 leading-relaxed">{{ $item->description }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Nenhuma novidade não lida.</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            @if(($unreadChangelogs ?? collect())->count() > 0)
            <div class="p-4 border-t border-gray-700 shrink-0">
                <button
                    @click="markAllRead()"
                    :disabled="marking"
                    class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <span x-show="!marking">Marcar tudo como lido e fechar</span>
                    <span x-show="marking">Salvando...</span>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function changelogModal() {
    return {
        open: false,
        marking: false,
        changelogs: @json(($unreadChangelogs ?? collect())->pluck('id')),

        init() {
            // Auto-open is triggered from dashboard.blade.php to avoid opening on every page
        },

        closeModal() {
            this.open = false;
        },

        async markAllRead() {
            if (this.marking) return;
            this.marking = true;

            try {
                await fetch('{{ route("changelogs.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ ids: this.changelogs }),
                });

                this.open = false;

                // Update badge in nav
                const badge = document.getElementById('changelog-badge');
                if (badge) {
                    badge.style.display = 'none';
                }
            } catch (e) {
                console.error('Erro ao marcar changelogs como lidos', e);
            } finally {
                this.marking = false;
            }
        },
    };
}
</script>
@endauth
