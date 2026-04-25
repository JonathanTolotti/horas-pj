<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidRecaptcha;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'               => ['required', 'string', 'max:20'],
            'tax_id'              => ['required', 'string', 'max:18'],
            'password'            => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => [config('services.recaptcha.site_key') ? 'required' : 'nullable', new ValidRecaptcha],
        ], [
            'g-recaptcha-response.required' => 'Verificação de segurança não concluída. Recarregue a página e tente novamente.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'tax_id' => $request->tax_id,
            'password' => Hash::make($request->password),
        ]);

        // Criar trial de 7 dias para novos usuários
        $user->createTrialSubscription();

        // Dispara evento Registered → envia e-mail de verificação automaticamente
        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice'));
    }
}
