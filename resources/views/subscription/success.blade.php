<x-app-layout>
    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <h1 class="text-2xl font-bold text-white mb-2">Pagamento Confirmado!</h1>
                    <p class="text-gray-400 mb-6">Sua assinatura Premium foi ativada com sucesso.</p>

                    <div class="space-y-3">
                        <a href="{{ route('dashboard') }}"
                           class="block w-full py-3 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors">
                            Ir para o Dashboard
                        </a>
                        <a href="{{ route('subscription.manage') }}"
                           class="block w-full py-3 px-4 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                            Gerenciar Assinatura
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
