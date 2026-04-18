<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Question   = 'question';
    case Bug        = 'bug';
    case Financial  = 'financial';
    case Suggestion = 'suggestion';
    case Other      = 'other';

    public function label(): string
    {
        return match($this) {
            self::Question   => 'Dúvida',
            self::Bug        => 'Bug',
            self::Financial  => 'Financeiro',
            self::Suggestion => 'Sugestão',
            self::Other      => 'Outro',
        };
    }

    public static function options(): array
    {
        return array_map(fn($c) => ['value' => $c->value, 'label' => $c->label()], self::cases());
    }
}
