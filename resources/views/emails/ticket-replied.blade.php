<x-mail::message>
# Você recebeu uma nova resposta

Olá, **{{ $ticket->user->name }}**!

O seu chamado **#{{ $ticket->id }} — {{ $ticket->title }}** recebeu uma nova resposta da nossa equipe.

<x-mail::panel>
{{ $message->body }}
</x-mail::panel>

<x-mail::button :url="$url">
Ver Chamado
</x-mail::button>

Se tiver mais dúvidas, responda diretamente pelo sistema.

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
