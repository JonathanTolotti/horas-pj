<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #111827;
            color: #f3f4f6;
            min-height: 100vh;
            padding: 2rem;
        }

        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: #1f2937;
            border-radius: 1rem;
            border: 1px solid #374151;
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            padding: 2rem;
            text-align: center;
        }

        .receipt-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .receipt-header .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .receipt-number {
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            font-weight: 600;
        }

        .receipt-body {
            padding: 2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .status-badge svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .section {
            margin-bottom: 1.5rem;
        }

        .section-title {
            color: #9ca3af;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .section-content {
            color: #f3f4f6;
        }

        .divider {
            height: 1px;
            background: #374151;
            margin: 1.5rem 0;
        }

        .details-grid {
            display: grid;
            gap: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-label {
            color: #9ca3af;
        }

        .detail-value {
            font-weight: 500;
        }

        .total-row {
            background: #111827;
            margin: 1.5rem -2rem -2rem;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .total-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #22c55e;
        }

        .receipt-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            background: #111827;
            border-top: 1px solid #374151;
        }

        .footer-text {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: #0891b2;
            color: white;
        }

        .btn-primary:hover {
            background: #0e7490;
        }

        .btn-secondary {
            background: #374151;
            color: #f3f4f6;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media print {
            body {
                background: white;
                color: #111827;
                padding: 0;
            }

            .receipt-container {
                background: white;
                border: none;
                box-shadow: none;
            }

            .receipt-header {
                background: #0891b2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status-badge {
                background: #dcfce7 !important;
                color: #166534 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total-row, .receipt-footer {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .total-value {
                color: #166534 !important;
            }

            .section-content, .detail-value, .total-label {
                color: #111827;
            }

            .actions {
                display: none;
            }

            .divider {
                background: #e5e7eb;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Controle de Horas PJ</h1>
            <p class="subtitle">Recibo de Pagamento</p>
            <div class="receipt-number">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>

        <div class="receipt-body">
            <div class="status-badge">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Pagamento Aprovado
            </div>

            <div class="section">
                <div class="section-title">Cliente</div>
                <div class="section-content">
                    <strong>{{ $user->name }}</strong><br>
                    {{ $user->email }}
                    @if($user->tax_id)
                        <br>CPF/CNPJ: {{ $user->tax_id }}
                    @endif
                </div>
            </div>

            <div class="divider"></div>

            <div class="details-grid">
                <div class="detail-row">
                    <span class="detail-label">Produto</span>
                    <span class="detail-value">Premium {{ $planLabel }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Forma de Pagamento</span>
                    <span class="detail-value">PIX</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Data do Pagamento</span>
                    <span class="detail-value">{{ $payment->paid_at->format('d/m/Y \a\s H:i') }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">ID da Transação</span>
                    <span class="detail-value" style="font-family: monospace; font-size: 0.8rem;">{{ $payment->abacatepay_id }}</span>
                </div>
            </div>

            <div class="total-row">
                <span class="total-label">Valor Total</span>
                <span class="total-value">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-footer">
            <p class="footer-text">
                Este recibo foi gerado automaticamente e comprova o pagamento realizado.<br>
                Em caso de dúvidas, entre em contato pelo e-mail de suporte.
            </p>
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-primary" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimir / Salvar PDF
        </button>

        <a href="{{ route('subscription.manage') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar
        </a>
    </div>
</body>
</html>
