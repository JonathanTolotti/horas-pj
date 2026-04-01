<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public User $sender,
        public string $recipientEmail,
        public string $customMessage = '',
    ) {}

    public function envelope(): Envelope
    {
        $month = \Carbon\Carbon::parse($this->invoice->reference_month . '-01')->translatedFormat('F Y');
        return new Envelope(
            subject: 'Fatura: ' . $this->invoice->title . ' – ' . $month,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    public function attachments(): array
    {
        $invoice = $this->invoice->load(['company', 'bankAccount', 'entries', 'xmls']);
        $user = $this->sender;

        $pdf = Pdf::loadView('exports.pdf.invoice', compact('invoice', 'user'))
            ->setPaper('a4', 'portrait');

        $filename = 'fatura-' . $this->invoice->reference_month . '-' . str_replace(' ', '-', strtolower($this->invoice->title)) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
