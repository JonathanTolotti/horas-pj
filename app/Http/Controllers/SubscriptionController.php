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
            ['payment_id' => $payment->id]
        );

        // Verificar se a resposta foi bem sucedida
        if (!isset($response['data']['id'])) {
            $payment->delete();
            return back()->with('error', 'Erro ao criar pagamento. Tente novamente.');
        }

        // Atualizar com ID do AbacatePay
        $payment->update([
            'abacatepay_id' => $response['data']['id'],
        ]);

        return view('subscription.checkout', [
            'payment' => $payment,
            'pixData' => $response['data'],
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

        // Verificar se é evento de pagamento
        if (($payload['event'] ?? '') !== 'billing.paid') {
            return response()->json(['ok' => true]);
        }

        $billingId = $payload['data']['id'] ?? null;

        if (!$billingId) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Buscar pagamento no nosso banco
        $payment = Payment::where('abacatepay_id', $billingId)->first();

        if (!$payment) {
            Log::warning('Webhook AbacatePay: Pagamento não encontrado', [
                'billing_id' => $billingId,
            ]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        // Já processado
        if ($payment->status === 'paid') {
            return response()->json(['ok' => true]);
        }

        // 2. IMPORTANTE: Verificar status DIRETAMENTE na API do AbacatePay
        // Nunca confiar apenas no payload do webhook (pode ser forjado)
        if (!$this->abacatePay->isPaymentConfirmed($billingId)) {
            Log::warning('Webhook AbacatePay: Pagamento não confirmado na API', [
                'billing_id' => $billingId,
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
