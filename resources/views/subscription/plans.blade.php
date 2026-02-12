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
                                    R$ {{ number_format($price['price'] / $months, 2, ',', '.') }}/mes
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

            <!-- Features List -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h2 class="text-white font-semibold mb-4">O que esta incluso no Premium</h2>
                <div class="grid md:grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Projetos ilimitados
                    </div>
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Empresas ilimitadas
                    </div>
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Visualizacao por dia
                    </div>
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Exportacao PDF e Excel
                    </div>
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Historico completo
                    </div>
                    <div class="flex items-center gap-2 text-gray-300">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Relatorio para NF
                    </div>
                </div>
            </div>

            <!-- Free Plan Comparison -->
            <div class="mt-6 bg-gray-900 rounded-xl border border-gray-800 p-6">
                <h3 class="text-gray-400 font-medium mb-3">Plano Free</h3>
                <div class="grid md:grid-cols-2 gap-2 text-sm">
                    <div class="flex items-center gap-2 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        1 projeto ativo
                    </div>
                    <div class="flex items-center gap-2 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        1 empresa cadastrada
                    </div>
                    <div class="flex items-center gap-2 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Historico de 2 meses
                    </div>
                    <div class="flex items-center gap-2 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="line-through">Visualizacao por dia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
