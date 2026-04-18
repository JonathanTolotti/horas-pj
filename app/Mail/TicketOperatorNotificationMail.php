<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketOperatorNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketMessage $message,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chamado #{$this->ticket->id} — Nova mensagem do cliente",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-operator-notification',
            with: [
                'ticket'  => $this->ticket,
                'message' => $this->message,
                'url'     => route('admin.tickets.show', $this->ticket),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
