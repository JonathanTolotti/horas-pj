<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Seja Premium</h1>
                <p class="text-gray-400">Desbloqueie todas as funcionalidades</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-900/50 border border-red-700 rounded-lg text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Current Plan Status -->
            @if($currentPlan)
                <div class="mb-8 p-4 bg-gray-800 rounded-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Seu plano atual</p>
                            <p class="text-white font-semibold">
                                @if($currentPlan->status === 'trial')
                                    Trial Premium
                                    <span class="text-yellow-400 text-sm">({{ $currentPlan->daysRemaining() }} dias restantes)</span>
                                @elseif($currentPlan->isPremium())
                                    Premium
                                    <span class="text-green-400 text-sm">(expira em {{ $currentPlan->ends_at->format('d/m/Y') }})</span>
                                @else
                                    Free
                                @endif
                            </p>
                        </div>
                        @if($currentPlan->isPremium())
                            <a href="{{ route('subscription.manage') }}" class="text-cyan-400 hover:text-cyan-300 text-sm">
                                Gerenciar assinatura
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Pricing Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                @foreach($prices as $months => $price)
                    <div class="relative bg-gray-800 rounded-xl border border-gray-700 p-6 hover:border-cyan-500 transition-colors group">
                        @if(isset($price['discount']))
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="bg-cyan-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $price['discount'] }}% OFF
                                </span>
                            </div>
                        @endif

                        <div class="text-center">
                            <h3 class="text-white font-semibold mb-2">{{ $price['label'] }}</h3>
                            <p class="text-3xl font-bold text-white mb-1">
                                R$ {{ number_format($price['price'], 2, ',', '.') }}
                            </p>
                            @if($months > 1)
                                <p class="text-gray-400 text-sm mb-4">
                                    R$ {{ number_format($price['price'] / $months, 2, ',', '.') }}/mês
                                </p>
                            @else
                                <p class="text-gray-400 text-sm mb-4">&nbsp;</p>
                            @endif

                            <a href="{{ route('subscription.checkout', $months) }}"
                               class="block w-full py-2 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors">
                                Assinar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Comparison Table -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <div class="grid grid-cols-3">
                    <!-- Header -->
                    <div class="p-4 bg-gray-900 border-b border-gray-700">
                        <span class="text-gray-400 font-medium">Funcionalidade</span>
                    </div>
                    <div class="p-4 bg-gray-900 border-b border-gray-700 text-center">
                        <span class="text-gray-400 font-medium">Free</span>
                    </div>
                    <div class="p-4 bg-gray-900 border-b border-gray-700 text-center">
                        <span class="text-cyan-400 font-medium">Premium</span>
                    </div>

                    <!-- Projetos -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Projetos
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center text-gray-400">
                        1 projeto
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center text-green-400 font-medium">
                        Ilimitados
                    </div>

                    <!-- Empresas -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Empresas/CNPJs
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center text-gray-400">
                        1 empresa
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center text-green-400 font-medium">
                        Ilimitadas
                    </div>

                    <!-- Histórico -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Histórico de lançamentos
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center text-gray-400">
                        2 meses
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center text-green-400 font-medium">
                        Completo
                    </div>

                    <!-- Tracking -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Tracking de horas
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <!-- Dashboard -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Dashboard com totais
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <!-- Visualização por dia -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Visualização por dia
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <!-- Exportação PDF -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Exportação PDF
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <!-- Exportação Excel -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Exportação Excel
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <!-- Relatório NF -->
                    <div class="p-4 border-b border-gray-700 text-gray-300">
                        Relatório para Nota Fiscal
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="p-4 border-b border-gray-700 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <!-- Relatório Anual -->
                    <div class="p-4 text-gray-300">
                        Relatório Anual
                    </div>
                    <div class="p-4 text-center">
                        <svg class="w-5 h-5 text-red-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="p-4 text-center">
                        <svg class="w-5 h-5 text-green-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Trial Info -->
            @if(!$currentPlan || (!$currentPlan->isPremium() && $currentPlan->status !== 'trial'))
                <div class="mt-6 text-center">
                    <p class="text-gray-400 text-sm">
                        Ainda não está convencido? Novos usuários ganham
                        <span class="text-cyan-400 font-medium">{{ config('plans.trial_days') }} dias de trial grátis</span>
                        para testar todas as funcionalidades Premium.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
