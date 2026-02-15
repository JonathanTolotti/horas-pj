<section>
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-red-400">
            Excluir Conta
        </h2>
        <p class="mt-1 text-sm text-gray-400">
            Ao excluir sua conta, todos os dados serão permanentemente removidos. Esta ação não pode ser desfeita.
        </p>
    </header>

    <button type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white font-medium rounded-lg transition-colors">
        Excluir Minha Conta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-gray-800">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-4">
                <div class="bg-red-500/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-white">
                    Confirmar Exclusão da Conta
                </h2>
            </div>

            <p class="text-gray-400 mb-6">
                Tem certeza que deseja excluir sua conta? Todos os seus dados, incluindo lançamentos de horas,
                projetos e configurações serão permanentemente removidos. Digite sua senha para confirmar.
            </p>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition-colors">
                    Sim, Excluir Conta
                </button>
            </div>
        </form>
    </x-modal>
</section>
