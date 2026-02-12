<x-app-layout>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-white mb-6">Gerenciar Assinatura</h1>

            <!-- Current Subscription -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 mb-6">
                <h2 class="text-lg font-semibold text-white mb-4">Plano Atual</h2>

                @if($subscription && $subscription->status === 'trial' && $subscription->trial_ends_at?->isFuture())
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-white font-medium">Trial Premium</span>
                                <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs rounded-full">Trial</span>
                            </div>
                            <p class="text-gray-400 text-sm">
                                Expira em {{ $subscription->trial_ends_at->format('d/m/Y') }}
                                ({{ $subscription->daysRemaining() }} dias restantes)
                            </p>
                        </div>
                        <a href="{{ route('subscription.plans') }}"
                           class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors">
                            Assinar
                        </a>
                    </div>
                @elseif($subscription && $subscription->status === 'active' && $subscription->ends_at?->isFuture())
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-white font-medium">Premium</span>
                                <span class="px-2 py-0.5 bg-green-500/20 text-green-400 text-xs rounded-full">Ativo</span>
                            </div>
                            <p class="text-gray-400 text-sm">
                                Expira em {{ $subscription->ends_at->format('d/m/Y') }}
                                ({{ $subscription->daysRemaining() }} dias restantes)
                            </p>
                        </div>
                        <a href="{{ route('subscription.plans') }}"
                           class="text-cyan-400 hover:text-cyan-300 text-sm">
                            Renovar
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-white font-medium">Free</span>
                                <span class="px-2 py-0.5 bg-gray-500/20 text-gray-400 text-xs rounded-full">Gratuito</span>
                            </div>
                            <p class="text-gray-400 text-sm">
                                Funcionalidades limitadas
                            </p>
                        </div>
                        <a href="{{ route('subscription.plans') }}"
                           class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors">
                            Fazer Upgrade
                        </a>
                    </div>
                @endif
            </div>

            <!-- Payment History -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-white mb-4">Historico de Pagamentos</h2>

                @if($payments->count() > 0)
                    <div class="space-y-3">
                        @foreach($payments as $payment)
                            <div class="flex items-center justify-between py-3 border-b border-gray-700 last:border-0">
                                <div>
                                    <p class="text-white">Premium {{ config("plans.prices.{$payment->months}.label") }}</p>
                                    <p class="text-gray-400 text-sm">{{ $payment->paid_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-white font-medium">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                                    <span class="text-green-400 text-xs">Pago</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 text-center py-4">Nenhum pagamento realizado</p>
                @endif
            </div>

            <!-- Back Link -->
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-300 text-sm">
                    Voltar para o Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
