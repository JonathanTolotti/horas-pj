<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\AbacatePayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(
        protected AbacatePayService $abacatePay
    ) {}

    public function plans()
    {
        return view('subscription.plans', [
            'prices' => config('plans.prices'),
            'currentPlan' => auth()->user()->subscription,
        ]);
    }

    public function checkout(int $months)
    {
        $prices = config('plans.prices');

        if (!isset($prices[$months])) {
            abort(404);
        }

        $price = $prices[$months];
        $user = auth()->user();

        // Criar registro do pagamento (expira em 5 minutos)
        $payment = Payment::create([
            'user_id' => $user->id,
            'abacatepay_id' => null,
            'amount' => $price['price'],
            'months' => $months,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);

        // Criar cobrança no AbacatePay
        $response = $this->abacatePay->createPixPayment(
            $price['price'],
            "Premium {$price['label']} - Controle de Horas PJ",
            [
                'payment_id' => $payment->id,
                'user' => $user,
            ]
        );

        // Verificar se a resposta foi bem sucedida
        if (!isset($response['data']['id']) || !isset($response['data']['brCode'])) {
            $payment->delete();
            $errorMsg = $response['error'] ?? 'Erro ao criar pagamento. Tente novamente.';
            return back()->with('error', $errorMsg);
        }

        // Atualizar com ID do AbacatePay e data de expiração da API
        $expiresAt = isset($response['data']['expiresAt'])
            ? \Carbon\Carbon::parse($response['data']['expiresAt'])
            : now()->addMinutes(5);

        $payment->update([
            'abacatepay_id' => $response['data']['id'],
            'expires_at' => $expiresAt,
        ]);

        // Refresh para pegar o expires_at atualizado
        $payment->refresh();

        return view('subscription.checkout', [
            'payment' => $payment,
            'pixData' => [
                'id' => $response['data']['id'],
                'brCode' => $response['data']['brCode'],
                'brCodeBase64' => $response['data']['brCodeBase64'],
                'amount' => $response['data']['amount'],
                'expiresAt' => $expiresAt->toISOString(),
            ],
            'price' => $price,
            'months' => $months,
        ]);
    }

    public function success()
    {
        return view('subscription.success');
    }

    /**
     * Webhook do AbacatePay
     *
     * SEGURANÇA:
     * 1. Token secreto na URL (só AbacatePay conhece)
     * 2. Verificação do pagamento direto na API (nunca confiar no payload)
     */
    public function webhook(Request $request, string $token)
    {
        // 1. Verificar token secreto da URL
        $expectedToken = config('services.abacatepay.webhook_token');
        if (!$expectedToken || !hash_equals($expectedToken, $token)) {
            Log::warning('Webhook AbacatePay: Token inválido', [
                'ip' => $request->ip(),
            ]);
            abort(403, 'Invalid token');
        }

        $payload = $request->all();

        // Log de auditoria - Webhook recebido (antes de processar)
        $this->abacatePay->logWebhook($payload, $request->ip());

        // Verificar se é evento de pagamento PIX
        $event = $payload['event'] ?? '';
        if (!in_array($event, ['pixQrCode.paid', 'billing.paid'])) {
            return response()->json(['ok' => true]);
        }

        $pixId = $payload['data']['id'] ?? null;

        if (!$pixId) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Buscar pagamento no nosso banco
        $payment = Payment::where('abacatepay_id', $pixId)->first();

        if (!$payment) {
            Log::warning('Webhook AbacatePay: Pagamento não encontrado', [
                'pix_id' => $pixId,
            ]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Log de auditoria - Webhook com payment encontrado
        $this->abacatePay->logWebhook($payload, $request->ip(), $payment->user_id, $payment->id);

        // Já processado
        if ($payment->status === 'paid') {
            return response()->json(['ok' => true]);
        }

        // 2. IMPORTANTE: Verificar status DIRETAMENTE na API do AbacatePay
        // Nunca confiar apenas no payload do webhook (pode ser forjado)
        if (!$this->abacatePay->isPaymentConfirmed($pixId, $payment->user_id, $payment->id)) {
            Log::warning('Webhook AbacatePay: Pagamento não confirmado na API', [
                'pix_id' => $pixId,
                'payment_id' => $payment->id,
            ]);
            return response()->json(['error' => 'Payment not confirmed'], 400);
        }

        // Pagamento confirmado - ativar assinatura
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $payment->user->activatePremium($payment->months);

        Log::info('Assinatura ativada via webhook', [
            'user_id' => $payment->user_id,
            'months' => $payment->months,
            'amount' => $payment->amount,
        ]);

        return response()->json(['ok' => true]);
    }

    public function manage()
    {
        $user = auth()->user();

        return view('subscription.manage', [
            'subscription' => $user->subscription,
            'payments' => $user->payments()->where('status', 'paid')->latest()->get(),
        ]);
    }

    /**
     * Endpoint para polling - verifica se pagamento foi confirmado
     * Chamado pelo JavaScript do checkout a cada 5 segundos
     */
    public function checkPaymentStatus(Payment $payment)
    {
        // Verificar se o pagamento pertence ao usuário
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Verificar se expirou
        if ($payment->isExpired()) {
            $payment->markAsExpired();

            return response()->json([
                'status' => 'expired',
                'paid' => false,
            ]);
        }

        return response()->json([
            'status' => $payment->status,
            'paid' => $payment->status === 'paid',
        ]);
    }
}
