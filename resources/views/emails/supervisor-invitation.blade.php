@extends('emails.layout')

@section('content')
    <h2>Olá, {{ $recipientName }}!</h2>

    <p><strong>{{ $inviterName }}</strong> enviou um convite para você acompanhar os dados de horas e faturamento dele no <strong>Horas PJ</strong>.</p>

    <div class="highlight-box">
        <p>Com este acesso você poderá visualizar: <strong>{{ implode(', ', $permissions) }}</strong>.</p>
    </div>

    <p>
        @if($expiresAt)
            O acesso é válido até <strong>{{ $expiresAt->format('d/m/Y \à\s H:i') }}</strong>.
        @else
            O acesso não tem prazo definido — fica ativo até ser revogado.
        @endif
    </p>

    <div class="text-center">
        <a href="{{ $invitationsUrl }}" class="btn">Ver convite</a>
    </div>

    <div class="divider"></div>

    <p class="text-muted">Caso não conheça {{ $inviterName }} ou não esperava este convite, pode ignorar este e-mail.</p>

    <p>
        Abraços,<br>
        <strong>Equipe Horas PJ</strong>
    </p>
@endsection
