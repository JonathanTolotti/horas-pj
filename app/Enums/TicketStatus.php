<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InAnalysis = 'in_analysis';
    case WaitingClient = 'waiting_client';
    case Closed = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Open          => 'Aberto',
            self::InAnalysis    => 'Em Análise',
            self::WaitingClient => 'Aguardando Cliente',
            self::Closed        => 'Encerrado',
        };
    }

    public function badgeClasses(): string
    {
        return match($this) {
            self::Open          => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            self::InAnalysis    => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
            self::WaitingClient => 'bg-orange-500/20 text-orange-300 border-orange-500/30',
            self::Closed        => 'bg-gray-700/50 text-gray-400 border-gray-600',
        };
    }

    public static function options(): array
    {
        return array_map(fn($s) => ['value' => $s->value, 'label' => $s->label()], self::cases());
    }
}
