@extends('emails.layout')

@section('content')
    <h2>Confirme seu endereço de e-mail</h2>

    <p>Olá! Obrigado por se cadastrar no <strong>Controle de Horas PJ</strong>.</p>

    <p>Para ativar sua conta e começar a registrar suas horas, clique no botão abaixo para confirmar seu endereço de e-mail:</p>

    <div class="text-center">
        <a href="{{ $url }}" class="btn">Confirmar E-mail</a>
    </div>

    <div class="highlight-box">
        <p>⏳ Este link de confirmação expira em <strong>60 minutos</strong>. Caso expire, faça login e solicite um novo link.</p>
    </div>

    <div class="divider"></div>

    <p class="text-muted">Se você não criou uma conta no Controle de Horas PJ, ignore este e-mail com segurança.</p>

    <p class="text-muted">Se o botão acima não funcionar, copie e cole o link abaixo no seu navegador:</p>
    <p class="text-muted" style="word-break: break-all;">{{ $url }}</p>
@endsection
