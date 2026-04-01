<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #1e293b; line-height: 1.6; margin: 0; padding: 0; background: #f8fafc; }
        .container { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: #0f172a; padding: 28px 32px; }
        .header h1 { color: #ffffff; font-size: 20px; margin: 0 0 4px; }
        .header p { color: #94a3b8; font-size: 13px; margin: 0; }
        .body { padding: 28px 32px; }
        .info-grid { display: table; width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 6px 16px 6px 0; white-space: nowrap; }
        .info-value { display: table-cell; color: #0f172a; font-size: 14px; padding: 6px 0; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
        .message-box { background: #f8fafc; border-left: 3px solid #3b82f6; padding: 14px 16px; border-radius: 0 6px 6px 0; margin: 16px 0; font-size: 14px; color: #334155; white-space: pre-wrap; }
        .footer { padding: 20px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0369a1; }
        .amount { font-size: 22px; font-weight: 700; color: #059669; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $invoice->title }}</h1>
        <p>Fatura · {{ \Carbon\Carbon::parse($invoice->reference_month . '-01')->translatedFormat('F Y') }}</p>
    </div>
    <div class="body">
        <p>Olá,</p>
        <p>Segue em anexo a fatura referente a <strong>{{ \Carbon\Carbon::parse($invoice->reference_month . '-01')->translatedFormat('F Y') }}</strong>.</p>

        @if($customMessage)
        <div class="message-box">{{ $customMessage }}</div>
        @endif

        <hr class="divider">

        <div class="info-grid">
            @if($invoice->company)
            <div class="info-row">
                <div class="info-label">Empresa</div>
                <div class="info-value">{{ $invoice->company->name }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Competência</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($invoice->reference_month . '-01')->translatedFormat('F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge">{{ ['rascunho'=>'Rascunho','aberta'=>'Aberta','conciliada'=>'Conciliada','encerrada'=>'Encerrada','cancelada'=>'Cancelada'][$invoice->status] ?? $invoice->status }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Valor Líquido</div>
                <div class="info-value">
                    <span class="amount">R$ {{ number_format($invoice->getNetTotal(), 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
        <hr class="divider">
        <p style="color:#64748b;font-size:12px;margin-bottom:4px;font-weight:600;text-transform:uppercase;">Observações</p>
        <p style="color:#334155;font-size:14px;">{{ $invoice->notes }}</p>
        @endif

        <hr class="divider">
        <p style="color:#64748b;font-size:13px;">O PDF completo da fatura está em anexo neste e-mail.</p>
    </div>
    <div class="footer">
        Enviado por {{ $sender->name }} &mdash; Sistema de Controle de Horas
    </div>
</div>
</body>
</html>
