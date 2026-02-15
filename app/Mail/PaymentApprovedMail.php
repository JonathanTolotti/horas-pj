<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Payment $payment
    ) {}

    public function envelope(): Envelope
    {
        $receiptNumber = str_pad($this->payment->id, 6, '0', STR_PAD_LEFT);

        return new Envelope(
            subject: "Pagamento Aprovado - Recibo #{$receiptNumber}",
        );
    }

    public function content(): Content
    {
        $planLabel = config("plans.prices.{$this->payment->months}.label", "{$this->payment->months} mês(es)");
        $subscription = $this->user->subscription;

        return new Content(
            view: 'emails.payment-approved',
            with: [
                'userName' => $this->user->name,
                'receiptNumber' => str_pad($this->payment->id, 6, '0', STR_PAD_LEFT),
                'planLabel' => $planLabel,
                'amount' => $this->payment->amount,
                'paidAt' => $this->payment->paid_at,
                'transactionId' => $this->payment->abacatepay_id,
                'subscriptionEndsAt' => $subscription?->ends_at,
                'receiptUrl' => route('subscription.receipt', $this->payment),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
