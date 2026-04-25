<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ValidRecaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Se a chave secreta não estiver configurada (ambiente local/testes), passa direto
        $secret = config('services.recaptcha.secret_key');
        if (!$secret) {
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $data = $response->json();

        $minScore = app()->isProduction() ? 0.5 : 0.1;

        if (empty($data['success']) || ($data['score'] ?? 0) < $minScore) {
            $fail('Verificação de segurança falhou. Por favor, tente novamente.');
        }
    }
}
