@extends('emails.layout')

@section('content')
    <h2>Seu código de verificação</h2>

    <p>Olá, {{ $userName }}! Recebemos uma tentativa de acesso à sua conta.</p>

    <p>Use o código abaixo para confirmar seu login. Ele é válido por <strong>10 minutos</strong>.</p>

    <div style="text-align: center; margin: 32px 0;">
        <div style="display: inline-block; background-color: #f0fdfa; border: 2px solid #0891b2; border-radius: 12px; padding: 20px 40px;">
            <span style="font-size: 36px; font-weight: 700; letter-spacing: 10px; color: #0e7490; font-family: monospace;">{{ $code }}</span>
        </div>
    </div>

    <div class="highlight-box">
        <p>⚠️ <strong>Nunca compartilhe este código.</strong> Nossa equipe jamais solicitará seu código de verificação.</p>
    </div>

    <div class="divider"></div>

    <p class="text-muted">Se você não tentou fazer login, ignore este e-mail. Considere alterar sua senha se suspeitar de acesso não autorizado.</p>

    <p class="text-muted">Após 3 tentativas incorretas, o acesso ficará bloqueado por 10 minutos.</p>
@endsection
