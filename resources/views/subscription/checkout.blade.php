<x-app-layout>
    <div class="py-8">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-700">
                    <h1 class="text-xl font-bold text-white text-center">Finalizar Pagamento</h1>
                </div>

                <!-- Order Summary -->
                <div class="p-6 border-b border-gray-700 bg-gray-850">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-400 text-sm">Plano</p>
                            <p class="text-white font-semibold">Premium {{ $price['label'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-400 text-sm">Valor</p>
                            <p class="text-white font-bold text-xl">R$ {{ number_format($price['price'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="p-6" id="waiting-payment">
                    <input type="hidden" id="payment-id" value="{{ $payment->id }}">

                    <!-- QR Code -->
                    <div class="flex justify-center mb-6">
                        @if(isset($pixData['pixQrCode']))
                            <div class="bg-white p-4 rounded-lg">
                                <img src="data:image/png;base64,{{ $pixData['pixQrCode'] }}"
                                     alt="QR Code Pix"
                                     class="w-48 h-48">
                            </div>
                        @else
                            <div class="bg-gray-700 p-4 rounded-lg w-48 h-48 flex items-center justify-center">
                                <p class="text-gray-400 text-sm text-center">QR Code indisponivel</p>
                            </div>
                        @endif
                    </div>

                    <!-- Copy Code -->
                    @if(isset($pixData['brCode']))
                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm mb-2">Ou copie o codigo Pix:</label>
                            <div class="flex gap-2">
                                <input type="text"
                                       id="pix-code"
                                       value="{{ $pixData['brCode'] }}"
                                       readonly
                                       class="flex-1 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-300 text-sm font-mono truncate">
                                <button onclick="copyPixCode()"
                                        id="copy-btn"
                                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    <span id="copy-text">Copiar</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Status -->
                    <div class="text-center">
                        <div class="flex items-center justify-center gap-2 text-yellow-400 mb-2">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Aguardando pagamento...</span>
                        </div>
                        <p class="text-gray-500 text-sm">
                            Expira em: <span id="countdown" class="font-mono text-gray-400">05:00</span>
                        </p>
                    </div>
                </div>

                <!-- Success Message (hidden by default) -->
                <div class="p-6 hidden" id="payment-success">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">Pagamento Confirmado!</h2>
                        <p class="text-gray-400 mb-4">Sua assinatura Premium foi ativada.</p>
                        <a href="{{ route('dashboard') }}"
                           class="inline-block px-6 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors">
                            Ir para o Dashboard
                        </a>
                    </div>
                </div>

                <!-- Expired Message (hidden by default) -->
                <div class="p-6 hidden" id="payment-expired">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-2">Tempo Expirado</h2>
                        <p class="text-gray-400 mb-4">O tempo para pagamento esgotou.</p>
                        <a href="{{ route('subscription.plans') }}"
                           class="inline-block px-6 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors">
                            Tentar Novamente
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="mt-4 text-center">
                <a href="{{ route('subscription.plans') }}" class="text-gray-400 hover:text-gray-300 text-sm">
                    Voltar para planos
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const paymentId = document.getElementById('payment-id').value;
        const checkUrl = `/subscription/check-payment/${paymentId}`;
        const POLL_INTERVAL_SECONDS = 5;

        let pollInterval = null;
        let countdownInterval = null;
        let expiresAt = new Date('{{ $payment->expires_at->toISOString() }}');

        function init() {
            startPolling();
            startCountdown();
        }

        function startPolling() {
            checkPaymentStatus();
            pollInterval = setInterval(checkPaymentStatus, POLL_INTERVAL_SECONDS * 1000);
        }

        function startCountdown() {
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
        }

        function updateCountdown() {
            const now = new Date();
            const diff = expiresAt - now;

            if (diff <= 0) {
                handleExpired();
                return;
            }

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);

            document.getElementById('countdown').textContent =
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        async function checkPaymentStatus() {
            try {
                const response = await fetch(checkUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.paid) {
                    stopTimers();
                    showSuccessMessage();
                } else if (data.status === 'expired') {
                    handleExpired();
                }
            } catch (error) {
                console.error('Erro ao verificar pagamento:', error);
            }
        }

        function stopTimers() {
            if (pollInterval) clearInterval(pollInterval);
            if (countdownInterval) clearInterval(countdownInterval);
        }

        function showSuccessMessage() {
            document.getElementById('waiting-payment').classList.add('hidden');
            document.getElementById('payment-success').classList.remove('hidden');

            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 3000);
        }

        function handleExpired() {
            stopTimers();
            document.getElementById('waiting-payment').classList.add('hidden');
            document.getElementById('payment-expired').classList.remove('hidden');
        }

        function copyPixCode() {
            const code = document.getElementById('pix-code').value;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copy-text');
                btn.textContent = 'Copiado!';
                setTimeout(() => {
                    btn.textContent = 'Copiar';
                }, 2000);
            });
        }

        document.addEventListener('DOMContentLoaded', init);
        window.addEventListener('beforeunload', stopTimers);
    </script>
    @endpush
</x-app-layout>
