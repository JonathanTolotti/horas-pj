<x-mail::message>
# Nova mensagem do cliente

O cliente **{{ $ticket->user->name }}** respondeu no chamado **#{{ $ticket->id }} — {{ $ticket->title }}**.

**Categoria:** {{ $ticket->category->label() }}
**Status atual:** {{ $ticket->status->label() }}

<x-mail::panel>
{{ $message->body }}
</x-mail::panel>

<x-mail::button :url="$url">
Ver no Painel Admin
</x-mail::button>

Atenciosamente,<br>
{{ config('app.name') }}
</x-mail::message>
