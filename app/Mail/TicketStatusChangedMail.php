<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Chamado #{$this->ticket->id} — Status atualizado: {$this->ticket->status->label()}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-status-changed',
            with: [
                'ticket' => $this->ticket,
                'url'    => route('tickets.show', $this->ticket),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
