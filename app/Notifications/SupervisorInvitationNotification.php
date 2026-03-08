<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupervisorInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly \App\Models\User $inviter,
        public readonly \App\Models\SupervisorInvitation $invitation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
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

        $permList = implode(', ', $permissions);

        $validadeText = $this->invitation->expires_at
            ? 'O acesso é válido até ' . $this->invitation->expires_at->format('d/m/Y \à\s H:i') . '.'
            : 'O acesso não tem prazo definido — fica ativo até ser revogado.';

        return (new MailMessage)
            ->subject($this->inviter->name . ' quer compartilhar dados com você — Horas PJ')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line($this->inviter->name . ' enviou um convite para você acompanhar os dados de horas e faturamento dele no Horas PJ.')
            ->line('Com este acesso você poderá visualizar: ' . $permList . '.')
            ->line($validadeText)
            ->action('Ver convite', route('supervisor.invitations'))
            ->line('Caso não conheça ' . $this->inviter->name . ' ou não esperava este convite, pode ignorar este e-mail.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
