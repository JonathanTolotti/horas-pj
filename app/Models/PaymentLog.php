<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    protected $fillable = [
        'user_id',
        'payment_id',
        'type',
        'endpoint',
        'method',
        'payload',
        'status_code',
        'response',
        'ip_address',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Registra uma requisição enviada
     */
    public static function logRequest(
        string $endpoint,
        string $method,
        array $payload,
        ?int $userId = null,
        ?int $paymentId = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'payment_id' => $paymentId,
            'type' => 'request',
            'endpoint' => $endpoint,
            'method' => $method,
            'payload' => $payload,
        ]);
    }

    /**
     * Registra uma resposta recebida
     */
    public static function logResponse(
        string $endpoint,
        int $statusCode,
        array $response,
        ?int $userId = null,
        ?int $paymentId = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'payment_id' => $paymentId,
            'type' => 'response',
            'endpoint' => $endpoint,
            'status_code' => $statusCode,
            'response' => $response,
        ]);
    }

    /**
     * Registra um webhook recebido
     */
    public static function logWebhook(
        array $payload,
        ?string $ipAddress = null,
        ?int $userId = null,
        ?int $paymentId = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'payment_id' => $paymentId,
            'type' => 'webhook',
            'payload' => $payload,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Registra um erro
     */
    public static function logError(
        string $endpoint,
        string $errorMessage,
        ?array $payload = null,
        ?int $userId = null,
        ?int $paymentId = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'payment_id' => $paymentId,
            'type' => 'error',
            'endpoint' => $endpoint,
            'payload' => $payload,
            'error_message' => $errorMessage,
        ]);
    }
}
