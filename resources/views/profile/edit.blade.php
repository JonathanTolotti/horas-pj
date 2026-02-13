<x-app-layout>
    <div class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">Meu Perfil</h1>
            <p class="text-gray-400 text-sm sm:text-base">Gerencie suas informações pessoais e segurança</p>
        </div>

        <!-- Informacoes do Perfil -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Alterar Senha -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Excluir Conta -->
        <div class="bg-gray-800 rounded-xl border border-red-900/50 overflow-hidden">
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-app-layout>
