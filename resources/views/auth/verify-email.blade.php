<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-cyan-900/40 mb-4">
            <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-100">Confirme seu e-mail</h2>
    </div>

    <p class="text-sm text-gray-400 text-center mb-4">
        Enviamos um link de confirmação para o endereço de e-mail cadastrado. Clique no link para ativar sua conta e começar a usar o sistema.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-900/40 border border-emerald-700 text-sm text-emerald-300 text-center">
            Um novo link de verificação foi enviado para o seu e-mail.
        </div>
    @endif

    @error('resend')
        <div class="mb-4 px-4 py-3 rounded-lg bg-yellow-900/40 border border-yellow-700 text-sm text-yellow-300 text-center">
            {{ $message }}
        </div>
    @enderror

    <div class="mt-2 p-3 rounded-lg bg-gray-800 border border-gray-700 text-sm text-gray-400 text-center">
        Não recebeu o e-mail? Verifique a pasta de spam ou solicite o reenvio.
    </div>

    <div class="mt-5 flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                Reenviar e-mail de verificação
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm text-gray-400 hover:text-gray-300 transition-colors rounded-lg focus:outline-none">
                Sair da conta
            </button>
        </form>
    </div>
</x-guest-layout>
