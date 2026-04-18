<x-mail::message>
# Status do seu chamado foi atualizado

Olá, **{{ $ticket->user->name }}**!

O status do chamado **#{{ $ticket->id }} — {{ $ticket->title }}** foi atualizado para:

**{{ $ticket->status->label() }}**

<x-mail::button :url="$url">
Ver Chamado
</x-mail::button>

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
