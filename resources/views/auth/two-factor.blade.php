<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-cyan-900/40 mb-4">
            <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-100">Verificação em duas etapas</h2>
        <p class="text-sm text-gray-400 mt-1">Digite o código enviado para o seu e-mail</p>
    </div>

    @if ($lockedUntil)
        {{-- Estado bloqueado: mostra aviso, esconde formulário --}}
        <div class="px-4 py-4 rounded-lg bg-red-900/40 border border-red-700 text-center">
            <div class="flex items-center justify-center gap-2 mb-1">
                <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-5V9m0 0V7m0 2h2m-2 0H10M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-red-300 font-semibold text-sm">Acesso bloqueado</span>
            </div>
            <p class="text-sm text-red-300">
                Muitas tentativas incorretas. Tente novamente às
                <strong>{{ $lockedUntil->format('H:i') }}</strong>
                (em {{ (int) ceil(now()->diffInSeconds($lockedUntil) / 60) }} minuto(s)).
            </p>
        </div>
    @else
        @if (session('status'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-900/40 border border-emerald-700 text-sm text-emerald-300 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify') }}">
            @csrf

            <div>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    autofocus
                    placeholder="000000"
                    class="w-full bg-gray-800 border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-700' }} rounded-lg px-4 py-3 text-white text-center text-2xl tracking-[0.5em] font-mono focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                    value="{{ old('code') }}"
                />

                @error('code')
                    <p class="mt-2 text-sm text-red-400 text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="mt-5 w-full inline-flex items-center justify-center px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                Verificar código
            </button>
        </form>

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('two-factor.resend') }}">
                @csrf
                <button type="submit" class="text-sm text-cyan-400 hover:text-cyan-300 transition-colors">
                    Reenviar código
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-400 hover:text-gray-300 transition-colors">
                    Sair da conta
                </button>
            </form>
        </div>

        <p class="mt-5 text-xs text-gray-500 text-center">
            O código expira em 10 minutos. Após 3 tentativas incorretas, o acesso fica bloqueado por 10 minutos.
        </p>
    @endif

    @if ($lockedUntil)
        <div class="mt-5">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm text-gray-400 hover:text-gray-300 transition-colors rounded-lg focus:outline-none">
                    Sair da conta
                </button>
            </form>
        </div>
    @endif
</x-guest-layout>
