@extends('emails.layout')

@section('content')
    <h2>Olá, {{ $userName }}! 👋</h2>

    <p>Seja muito bem-vindo(a) ao <strong>Controle de Horas PJ</strong>!</p>

    <p>Sua conta foi criada com sucesso e você já pode começar a registrar suas horas de trabalho de forma simples e organizada.</p>

    <div class="highlight-box">
        <p>🎉 <strong>Presente de boas-vindas:</strong> Você ganhou <strong>{{ $trialDays }} dias grátis</strong> de acesso Premium para testar todas as funcionalidades!</p>
    </div>

    <p>Com o plano Premium você tem acesso a:</p>

    <table class="info-table">
        <tr>
            <td>✅ Projetos ilimitados</td>
            <td></td>
        </tr>
        <tr>
            <td>✅ Empresas ilimitadas</td>
            <td></td>
        </tr>
        <tr>
            <td>✅ Visualização por dia</td>
            <td></td>
        </tr>
        <tr>
            <td>✅ Exportação PDF e Excel</td>
            <td></td>
        </tr>
        <tr>
            <td>✅ Relatório para Nota Fiscal</td>
            <td></td>
        </tr>
        <tr>
            <td>✅ Relatório Anual</td>
            <td></td>
        </tr>
    </table>

    <div class="text-center">
        <a href="{{ url('/dashboard') }}" class="btn">Acessar Meu Dashboard</a>
    </div>

    <div class="divider"></div>

    <p class="text-muted">Dica: Comece configurando seu valor por hora e cadastrando sua primeira empresa em <strong>Configurações</strong>.</p>

    <p>Qualquer dúvida, estamos aqui para ajudar!</p>

    <p>
        Abraços,<br>
        <strong>Equipe Controle de Horas PJ</strong>
    </p>
@endsection
