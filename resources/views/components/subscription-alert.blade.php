@props(['alert'])

@if($alert)
    @php
        $colors = match($alert['type']) {
            'trial_expiring' => [
                'bg' => 'bg-yellow-500/10 border-yellow-500/30',
                'icon' => 'text-yellow-400',
                'text' => 'text-yellow-200',
                'button' => 'bg-yellow-500 hover:bg-yellow-600 text-gray-900',
            ],
            'premium_expiring' => [
                'bg' => 'bg-orange-500/10 border-orange-500/30',
                'icon' => 'text-orange-400',
                'text' => 'text-orange-200',
                'button' => 'bg-orange-500 hover:bg-orange-600 text-white',
            ],
            'expired' => [
                'bg' => 'bg-red-500/10 border-red-500/30',
                'icon' => 'text-red-400',
                'text' => 'text-red-200',
                'button' => 'bg-red-500 hover:bg-red-600 text-white',
            ],
            default => [
                'bg' => 'bg-gray-500/10 border-gray-500/30',
                'icon' => 'text-gray-400',
                'text' => 'text-gray-200',
                'button' => 'bg-gray-500 hover:bg-gray-600 text-white',
            ],
        };

        $icon = match($alert['type']) {
            'expired' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        };

        $buttonText = match($alert['type']) {
            'trial_expiring' => 'Assinar agora',
            'premium_expiring' => 'Renovar',
            'expired' => 'Reativar Premium',
            default => 'Ver planos',
        };
    @endphp

    <div class="rounded-lg border {{ $colors['bg'] }} p-4" x-data="{ show: true }" x-show="show" x-transition>
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="shrink-0">
                    <svg class="w-5 h-5 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <p class="text-sm {{ $colors['text'] }}">
                    {{ $alert['message'] }}
                    @if($alert['type'] === 'trial_expiring')
                        <span class="text-gray-400">Assine para manter acesso aos recursos Premium.</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('subscription.plans') }}"
                   class="px-4 py-1.5 text-sm font-medium rounded-lg {{ $colors['button'] }} transition-colors">
                    {{ $buttonText }}
                </a>
                @if($alert['type'] !== 'expired')
                    <button @click="show = false" class="p-1 text-gray-500 hover:text-gray-400 transition-colors" title="Fechar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
