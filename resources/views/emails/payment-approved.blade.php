@extends('emails.layout')

@section('content')
    <div class="text-center">
        <span class="badge badge-success">PAGAMENTO APROVADO</span>
    </div>

    <h2 style="margin-top: 20px;">Olá, {{ $userName }}!</h2>

    <p>Ótimas notícias! Seu pagamento foi aprovado com sucesso e sua assinatura <strong>Premium</strong> já está ativa.</p>

    <div class="highlight-box">
        <p>🎉 Agora você tem acesso a todas as funcionalidades Premium!</p>
    </div>

    <h3 style="color: #1f2937; margin-top: 25px; margin-bottom: 15px;">Detalhes do Pagamento</h3>

    <table class="info-table">
        <tr>
            <td>Número do Recibo</td>
            <td>#{{ $receiptNumber }}</td>
        </tr>
        <tr>
            <td>Plano</td>
            <td>Premium {{ $planLabel }}</td>
        </tr>
        <tr>
            <td>Forma de Pagamento</td>
            <td>PIX</td>
        </tr>
        <tr>
            <td>Data do Pagamento</td>
            <td>{{ $paidAt->format('d/m/Y \à\s H:i') }}</td>
        </tr>
        <tr>
            <td>ID da Transação</td>
            <td style="font-family: monospace; font-size: 12px;">{{ Str::limit($transactionId, 20) }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Valor Total</strong></td>
            <td>R$ {{ number_format($amount, 2, ',', '.') }}</td>
        </tr>
    </table>

    @if($subscriptionEndsAt)
        <div class="highlight-box" style="background-color: #f0fdf4; border-left-color: #22c55e;">
            <p style="color: #166534;">📅 Sua assinatura Premium é válida até <strong>{{ $subscriptionEndsAt->format('d/m/Y') }}</strong></p>
        </div>
    @endif

    <div class="text-center">
        <a href="{{ $receiptUrl }}" class="btn">Ver Recibo Completo</a>
    </div>

    <div class="divider"></div>

    <p class="text-muted">Guarde este e-mail como comprovante de pagamento. Você também pode acessar o recibo a qualquer momento em <strong>Gerenciar Assinatura</strong>.</p>

    <p>
        Obrigado pela confiança!<br>
        <strong>Equipe Controle de Horas PJ</strong>
    </p>
@endsection
