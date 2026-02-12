<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AbacatePayService
{
    protected string $baseUrl = 'https://api.abacatepay.com/v1';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.abacatepay.api_key', '');
    }

    public function createPixPayment(float $amount, string $description, array $metadata = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/billing/create", [
            'frequency' => 'ONE_TIME',
            'methods' => ['PIX'],
            'products' => [
                [
                    'externalId' => $metadata['payment_id'] ?? uniqid(),
                    'name' => $description,
                    'quantity' => 1,
                    'price' => (int) ($amount * 100), // centavos
                ],
            ],
            'returnUrl' => route('subscription.success'),
            'completionUrl' => route('subscription.success'),
            'expiresIn' => 300, // 5 minutos em segundos
        ]);

        return $response->json();
    }

    /**
     * Busca o pagamento diretamente na API para verificar status real
     * IMPORTANTE: Sempre usar este método para validar antes de ativar assinatura
     */
    public function getPayment(string $id): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->get("{$this->baseUrl}/billing/show/{$id}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('AbacatePay: Falha ao buscar pagamento', [
                'id' => $id,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AbacatePay: Erro ao buscar pagamento', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verifica se o pagamento está realmente pago consultando a API
     * Nunca confiar apenas no payload do webhook
     */
    public function isPaymentConfirmed(string $billingId): bool
    {
        $billing = $this->getPayment($billingId);

        if (!$billing) {
            return false;
        }

        // Verificar se o status é PAID na resposta da API
        return ($billing['data']['status'] ?? '') === 'PAID';
    }
}
