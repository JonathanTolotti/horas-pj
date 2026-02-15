@props(['feature' => 'esta funcionalidade'])

<div x-data="{ open: false }"
     x-on:open-premium-modal.window="open = true"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="premium-modal-title"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/75 transition-opacity"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-gray-800 rounded-xl border border-gray-700 shadow-xl max-w-md w-full p-6">

            <!-- Close Button -->
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Content -->
            <div class="text-center">
                <div class="w-12 h-12 bg-cyan-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <h3 id="premium-modal-title" class="text-lg font-semibold text-white mb-2">
                    Funcionalidade Premium
                </h3>

                <p class="text-gray-400 mb-4">
                    A {{ $feature }} esta disponivel apenas para assinantes Premium.
                </p>

                <p class="text-gray-300 text-sm mb-6">
                    Por apenas <span class="text-cyan-400 font-semibold">R$ 9,90/mes</span> voce desbloqueia:
                </p>

                <ul class="text-left text-sm text-gray-300 space-y-2 mb-6">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Projetos e empresas ilimitados
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Visualizacao por dia
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Exportacao de relatorios
                    </li>
                </ul>

                <div class="flex gap-3">
                    <a href="{{ route('subscription.plans') }}"
                       class="flex-1 py-2 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors text-center">
                        Ver Planos
                    </a>
                    <button @click="open = false"
                            class="flex-1 py-2 px-4 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
