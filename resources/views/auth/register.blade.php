<x-guest-layout>
    <div class="mb-7">
        <h2 class="text-xl font-bold dark:text-white text-gray-900">Crie sua conta</h2>
        <p class="text-sm dark:text-slate-400 text-slate-500 mt-1">Comece grátis, sem cartão de crédito</p>
    </div>

    <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">Nome completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="phone" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">Telefone</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="(11) 99999-9999"
                    class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
            </div>

            <div>
                <label for="tax_id" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">CPF ou CNPJ</label>
                <input id="tax_id" type="text" name="tax_id" value="{{ old('tax_id') }}" required placeholder="000.000.000-00"
                    class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
                <x-input-error :messages="$errors->get('tax_id')" class="mt-1.5" />
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="seu@email.com"
                class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 placeholder-slate-400 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium dark:text-slate-300 text-slate-600 mb-1.5">Confirmar senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full dark:bg-white/[0.05] dark:border-white/[0.10] dark:text-white dark:placeholder-slate-500 dark:focus:border-cyan-500 dark:focus:ring-cyan-500/20 bg-slate-50 border-slate-300 text-slate-900 focus:border-cyan-500 focus:ring-cyan-500/20 border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 transition-colors" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-register">
        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mb-1" />

        <button type="submit"
            class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm text-white mt-2 transition-all focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 dark:focus:ring-offset-transparent"
            style="background: linear-gradient(135deg, #0891b2, #059669);">
            Criar conta grátis
        </button>

        <p class="text-center text-sm dark:text-slate-500 text-slate-400">
            Já tem conta?
            <a href="{{ route('login') }}" class="dark:text-cyan-400 text-cyan-600 font-medium hover:underline">Entrar</a>
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
        document.getElementById('phone')?.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length <= 11) {
                v = v.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = v;
        });

        document.getElementById('tax_id')?.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 14);
            if (v.length <= 11) {
                // CPF: 000.000.000-00
                if      (v.length > 9) v = v.slice(0,3)+'.'+v.slice(3,6)+'.'+v.slice(6,9)+'-'+v.slice(9);
                else if (v.length > 6) v = v.slice(0,3)+'.'+v.slice(3,6)+'.'+v.slice(6);
                else if (v.length > 3) v = v.slice(0,3)+'.'+v.slice(3);
            } else {
                // CNPJ: 00.000.000/0000-00
                if      (v.length > 12) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'/'+v.slice(8,12)+'-'+v.slice(12);
                else if (v.length > 8)  v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'/'+v.slice(8);
                else if (v.length > 5)  v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5);
                else if (v.length > 2)  v = v.slice(0,2)+'.'+v.slice(2);
            }
            e.target.value = v;
        });

        @if(config('services.recaptcha.site_key'))
        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'register'}).then(function(token) {
                    document.getElementById('g-recaptcha-response-register').value = token;
                    form.submit();
                });
            });
        });
        @endif
    </script>
    @endpush
</x-guest-layout>
