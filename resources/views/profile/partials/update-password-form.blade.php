<section>
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-white">
            Alterar Senha
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            Use uma senha forte e única para proteger sua conta.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Senha Atual -->
            <div>
                <label for="update_password_current_password" class="block text-sm font-medium text-gray-300 mb-1">Senha Atual</label>
                <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <!-- Nova Senha -->
            <div>
                <label for="update_password_password" class="block text-sm font-medium text-gray-300 mb-1">Nova Senha</label>
                <input type="password" id="update_password_password" name="password" autocomplete="new-password"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar Nova Senha -->
            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmar Nova Senha</label>
                <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-medium rounded-lg transition-colors">
                Atualizar Senha
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400">
                    Senha atualizada!
                </p>
            @endif
        </div>
    </form>
</section>
