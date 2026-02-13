<section>
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-white">
            Informações Pessoais
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            Atualize seus dados pessoais e informações de contato.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Nome -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-yellow-400">
                            Seu email ainda não foi verificado.
                            <button form="send-verification" class="underline text-yellow-300 hover:text-yellow-200">
                                Clique aqui para reenviar o email de verificação.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm text-green-400">
                                Um novo link de verificação foi enviado para seu email.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Telefone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Telefone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required
                    placeholder="(11) 99999-9999"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- CPF/CNPJ -->
            <div>
                <label for="tax_id" class="block text-sm font-medium text-gray-300 mb-1">CPF ou CNPJ</label>
                <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id', $user->tax_id) }}" required
                    placeholder="000.000.000-00"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500">Usado para pagamentos via Pix</p>
                <x-input-error class="mt-2" :messages="$errors->get('tax_id')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors">
                Salvar Alterações
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400">
                    Salvo com sucesso!
                </p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script>
    // Mascara de telefone
    document.getElementById('phone')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        e.target.value = value;
    });

    // Mascara de CPF/CNPJ
    document.getElementById('tax_id')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }
        e.target.value = value;
    });
</script>
@endpush
