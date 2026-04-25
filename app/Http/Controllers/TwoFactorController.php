<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorCodeMail;
use App\Models\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (!session('two_factor_pending')) {
            return redirect()->route('dashboard');
        }

        $record = TwoFactorCode::where('user_id', Auth::id())->first();
        $lockedUntil = ($record && $record->isLocked()) ? $record->locked_until : null;

        return view('auth.two-factor', compact('lockedUntil'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Informe o código de verificação.',
            'code.size'     => 'O código deve ter 6 dígitos.',
        ]);

        $user = Auth::user();
        $record = TwoFactorCode::where('user_id', $user->id)->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Código não encontrado. Solicite um novo código.']);
        }

        if ($record->isLocked()) {
            $remaining = (int) ceil(now()->diffInSeconds($record->locked_until) / 60);
            return back()->withErrors(['code' => "Muitas tentativas incorretas. Tente novamente em {$remaining} minuto(s)."]);
        }

        if ($record->isExpired()) {
            return back()->withErrors(['code' => 'Código expirado. Clique em "Reenviar código" para receber um novo.']);
        }

        if (!Hash::check($request->code, $record->code)) {
            $record->increment('attempts');

            if ($record->attempts >= 3) {
                $record->update(['locked_until' => now()->addMinutes(10)]);
                return back()->withErrors(['code' => 'Muitas tentativas incorretas. Acesso bloqueado por 10 minutos.']);
            }

            $remaining = 3 - $record->attempts;
            return back()->withErrors(['code' => "Código incorreto. {$remaining} tentativa(s) restante(s)."]);
        }

        $record->delete();
        session()->forget('two_factor_pending');

        return redirect()->intended(route('dashboard'));
    }

    public function resend(): RedirectResponse
    {
        $user = Auth::user();
        $record = TwoFactorCode::where('user_id', $user->id)->first();

        if ($record && $record->isLocked()) {
            $remaining = (int) ceil(now()->diffInSeconds($record->locked_until) / 60);
            return back()->withErrors(['code' => "Conta bloqueada. Aguarde {$remaining} minuto(s) para solicitar novo código."]);
        }

        $this->sendCode($user);

        return back()->with('status', 'Novo código enviado para o seu e-mail.');
    }

    public static function sendCode(\App\Models\User $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        TwoFactorCode::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code'         => Hash::make($code),
                'expires_at'   => now()->addMinutes(10),
                'attempts'     => 0,
                'locked_until' => null,
            ]
        );

        Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));
    }
}
