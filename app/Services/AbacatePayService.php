<?php

namespace App\Services;

use App\Models\PaymentLog;
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
        $user = $metadata['user'] ?? auth()->user();
        $paymentId = $metadata['payment_id'] ?? null;
        $endpoint = '/pixQrCode/create';

        // Usar dados do usuário ou fallback
        $phone = $user->phone ?? '11999999999';
        $taxId = $user->tax_id ?? '00000000000';

        // Formatar telefone (remover caracteres especiais)
        $phone = preg_replace('/\D/', '', $phone);

        // Formatar CPF/CNPJ (remover caracteres especiais)
        $taxId = preg_replace('/\D/', '', $taxId);

        $payload = [
            'amount' => (int) ($amount * 100), // centavos
            'expiresIn' => 300, // 5 minutos
            'description' => $description,
            'customer' => [
                'name' => $user->name,
                'email' => $user->email,
                'cellphone' => $phone,
                'taxId' => $taxId,
            ],
            'metadata' => [
                'externalId' => (string) ($paymentId ?? uniqid()),
            ],
        ];

        // Log de auditoria - Request
        PaymentLog::logRequest(
            $endpoint,
            'POST',
            $this->sanitizePayload($payload),
            $user->id,
            $paymentId
        );

        Log::info('AbacatePay: Criando PIX QR Code', [
            'amount' => $amount,
            'user_id' => $user->id,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}{$endpoint}", $payload);

            $data = $response->json() ?? [];

            // Log de auditoria - Response
            PaymentLog::logResponse(
                $endpoint,
                $response->status(),
                $this->sanitizeResponse($data),
                $user->id,
                $paymentId
            );

            if (!$response->successful()) {
                Log::error('AbacatePay: Erro na API', [
                    'status' => $response->status(),
                    'error' => $data['error'] ?? 'Unknown error',
                ]);
            }

            return $data;
        } catch (\Exception $e) {
            // Log de auditoria - Error
            PaymentLog::logError(
                $endpoint,
                $e->getMessage(),
                $this->sanitizePayload($payload),
                $user->id,
                $paymentId
            );

            Log::error('AbacatePay: Excecao ao criar pagamento', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Remove dados sensíveis do payload para log
     */
    protected function sanitizePayload(array $payload): array
    {
        $sanitized = $payload;

        // Mascarar CPF/CNPJ
        if (isset($sanitized['customer']['taxId'])) {
            $taxId = $sanitized['customer']['taxId'];
            $sanitized['customer']['taxId'] = substr($taxId, 0, 3) . '.***.***-' . substr($taxId, -2);
        }

        return $sanitized;
    }

    /**
     * Remove dados sensíveis da resposta para log
     */
    protected function sanitizeResponse(array $response): array
    {
        $sanitized = $response;

        // Remover QR code base64 (muito grande)
        if (isset($sanitized['data']['brCodeBase64'])) {
            $sanitized['data']['brCodeBase64'] = '[BASE64_IMAGE_REMOVED]';
        }

        return $sanitized;
    }

    /**
     * Busca o pagamento diretamente na API para verificar status real
     * IMPORTANTE: Sempre usar este método para validar antes de ativar assinatura
     */
    public function getPayment(string $id, ?int $userId = null, ?int $paymentId = null): ?array
    {
        $endpoint = '/pixQrCode/check';

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->get("{$this->baseUrl}{$endpoint}", [
                'id' => $id,
            ]);

            $data = $response->json();

            // Log de auditoria
            PaymentLog::logResponse(
                $endpoint,
                $response->status(),
                $data ?? [],
                $userId,
                $paymentId
            );

            if ($response->successful()) {
                return $data;
            }

            Log::warning('AbacatePay: Falha ao buscar pagamento', [
                'id' => $id,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            PaymentLog::logError(
                $endpoint,
                $e->getMessage(),
                ['pix_id' => $id],
                $userId,
                $paymentId
            );

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
    public function isPaymentConfirmed(string $pixId, ?int $userId = null, ?int $paymentId = null): bool
    {
        $pix = $this->getPayment($pixId, $userId, $paymentId);

        if (!$pix) {
            return false;
        }

        // Verificar se o status é PAID na resposta da API
        return ($pix['data']['status'] ?? '') === 'PAID';
    }

    /**
     * Loga um webhook recebido
     */
    public function logWebhook(array $payload, ?string $ipAddress = null, ?int $userId = null, ?int $paymentId = null): void
    {
        PaymentLog::logWebhook($payload, $ipAddress, $userId, $paymentId);
    }
}
