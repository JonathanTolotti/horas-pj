<x-guest-layout>
    <div class="mb-7">
        <h2 class="text-xl font-bold dark:text-white text-gray-900">Bem-vindo de volta</h2>
        <p class="text-sm dark:text-slate-400 text-slate-500 mt-1">Entre na sua conta para continuar</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors"
                placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium dark:text-slate-300 text-slate-600">Senha</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs dark:text-cyan-400 text-cyan-600 hover:underline">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember"
                class="w-4 h-4 rounded dark:bg-white/[0.05] dark:border-white/20 bg-slate-100 border-slate-300 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0" />
            <label for="remember_me" class="text-sm dark:text-slate-400 text-slate-500">Lembrar de mim</label>
        </div>

        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-login">
        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mb-1" />

        <button type="submit"
            class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm text-white transition-all focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-transparent"
            style="background: linear-gradient(135deg, #0891b2, #059669);">
            Entrar
        </button>

        <p class="text-center text-sm dark:text-slate-500 text-slate-400">
            Não tem conta?
            <a href="{{ route('register') }}" class="dark:text-cyan-400 text-cyan-600 font-medium hover:underline">Criar conta grátis</a>
        </p>
    </form>

    @if(config('services.recaptcha.site_key'))
    <p class="mt-4 text-center text-xs dark:text-slate-600 text-slate-400">
        Protegido pelo reCAPTCHA —
        <a href="https://policies.google.com/privacy" target="_blank" class="underline">Privacidade</a> e
        <a href="https://policies.google.com/terms" target="_blank" class="underline">Termos</a> do Google.
    </p>
    @endif

    @push('scripts')
    <script>
        @if(config('services.recaptcha.site_key'))
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'}).then(function(token) {
                    document.getElementById('g-recaptcha-response-login').value = token;
                    form.submit();
                });
            });
        });
        @endif
    </script>
    @endpush
</x-guest-layout>
