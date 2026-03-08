<?php

namespace App\Mail;

use App\Models\SupervisorInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupervisorInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $inviter,
        public readonly SupervisorInvitation $invitation,
        public readonly User $recipient,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->inviter->name . ' quer compartilhar dados com você — Horas PJ',
        );
    }

    public function content(): Content
    {
        $permissions = ['lançamentos de horas'];
        if ($this->invitation->can_view_financials) {
            $permissions[] = 'valores financeiros';
        }
        if ($this->invitation->can_view_analytics) {
            $permissions[] = 'analytics';
        }
        if ($this->invitation->can_export) {
            $permissions[] = 'exportação de relatórios';
        }

        return new Content(
            view: 'emails.supervisor-invitation',
            with: [
                'inviterName' => $this->inviter->name,
                'recipientName' => $this->recipient->name,
                'permissions' => $permissions,
                'expiresAt' => $this->invitation->expires_at,
                'invitationsUrl' => route('supervisor.invitations'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
