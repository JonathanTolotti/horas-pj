<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fatura – {{ $invoice->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.5;
        }

        .container { padding: 30px; }

        .header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #059669;
        }

        .header h1 {
            font-size: 16pt;
            color: #059669;
            margin-bottom: 4px;
        }

        .header .subtitle {
            font-size: 10pt;
            color: #6b7280;
        }

        .meta-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .meta-grid .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .meta-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .meta-value {
            font-size: 10pt;
            color: #111827;
            font-weight: bold;
        }

        .meta-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.entries {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }

        table.entries th {
            background: #f3f4f6;
            color: #374151;
            text-align: left;
            padding: 7px 10px;
            font-weight: bold;
            border-bottom: 2px solid #d1d5db;
        }

        table.entries td {
            padding: 6px 10px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        table.entries tr:nth-child(even) td {
            background: #f9fafb;
        }

        .credit { color: #059669; }
        .debit  { color: #dc2626; }

        .totals-table {
            width: 280px;
            margin-left: auto;
            margin-bottom: 24px;
            font-size: 9pt;
        }

        .totals-table td {
            padding: 4px 8px;
        }

        .totals-table .total-row td {
            font-weight: bold;
            font-size: 11pt;
            border-top: 2px solid #d1d5db;
            padding-top: 8px;
        }

        table.xmls {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }

        table.xmls th {
            background: #f3f4f6;
            color: #374151;
            text-align: left;
            padding: 7px 10px;
            font-weight: bold;
            border-bottom: 2px solid #d1d5db;
        }

        table.xmls td {
            padding: 6px 10px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }

        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 8pt;
            color: #9ca3af;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-draft      { background: #f3f4f6; color: #6b7280; }
        .badge-open       { background: #dbeafe; color: #1d4ed8; }
        .badge-reconciled { background: #d1fae5; color: #065f46; }
        .badge-closed     { background: #e5e7eb; color: #4b5563; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <h1>{{ $invoice->title }}</h1>
        <div class="subtitle">
            Fatura · {{ \Carbon\Carbon::parse($invoice->reference_month . '-01')->translatedFormat('F Y') }}
            &nbsp;&nbsp;·&nbsp;&nbsp;
            @php
                $statusLabels = ['rascunho' => 'Rascunho','aberta' => 'Aberta','conciliada' => 'Conciliada','encerrada' => 'Encerrada'];
                $statusClasses = ['rascunho' => 'badge-draft','aberta' => 'badge-open','conciliada' => 'badge-reconciled','encerrada' => 'badge-closed'];
            @endphp
            <span class="badge {{ $statusClasses[$invoice->status] }}">{{ $statusLabels[$invoice->status] }}</span>
        </div>
    </div>

    {{-- Informações --}}
    <div class="meta-grid">
        @if($invoice->company)
        <div class="col" style="padding-right: 10px;">
            <div class="meta-box">
                <div class="meta-label">Empresa</div>
                <div class="meta-value">{{ $invoice->company->name }}</div>
                @if($invoice->company->cnpj)
                    <div style="font-size:9pt;color:#6b7280;">CNPJ: {{ $invoice->company->cnpj }}</div>
                @endif
            </div>
        </div>
        @endif
        @if($invoice->bankAccount)
        <div class="col" style="padding-left: 10px;">
            <div class="meta-box">
                <div class="meta-label">Conta Bancária</div>
                <div class="meta-value">{{ $invoice->bankAccount->bank_name }}</div>
                <div style="font-size:9pt;color:#6b7280;">
                    Ag. {{ $invoice->bankAccount->branch }} · Conta {{ $invoice->bankAccount->account_number }}
                    ({{ $invoice->bankAccount->account_type }})
                </div>
                @if($invoice->bankAccount->pix_key)
                    <div style="font-size:9pt;color:#6b7280;">PIX: {{ $invoice->bankAccount->pix_key }}</div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Lançamentos --}}
    <div class="section-title">Lançamentos</div>
    @if($invoice->entries->isNotEmpty())
    <table class="entries">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th style="text-align:right;">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->entries as $entry)
            <tr>
                <td>{{ $entry->date->format('d/m/Y') }}</td>
                <td>{{ $entry->description }}</td>
                <td class="{{ $entry->type === 'credit' ? 'credit' : 'debit' }}">
                    {{ $entry->type === 'credit' ? 'Crédito' : 'Débito' }}
                </td>
                <td style="text-align:right;" class="{{ $entry->type === 'credit' ? 'credit' : 'debit' }}">
                    {{ $entry->type === 'debit' ? '-' : '' }}R$ {{ number_format($entry->amount, 2, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td style="color:#6b7280;">Créditos:</td>
            <td style="text-align:right;" class="credit">R$ {{ number_format($invoice->getTotalCredits(), 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="color:#6b7280;">Débitos:</td>
            <td style="text-align:right;" class="debit">R$ {{ number_format($invoice->getTotalDebits(), 2, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Total Líquido:</td>
            <td style="text-align:right;">R$ {{ number_format($invoice->getNetTotal(), 2, ',', '.') }}</td>
        </tr>
    </table>
    @else
    <p style="color:#9ca3af;font-size:9pt;margin-bottom:20px;">Nenhum lançamento.</p>
    @endif

    {{-- XMLs / NFs --}}
    @if($invoice->xmls->isNotEmpty())
    <div class="section-title">Notas Fiscais</div>
    <table class="xmls">
        <thead>
            <tr>
                <th>NF</th>
                <th>Emissão</th>
                <th>Prestador</th>
                <th style="text-align:right;">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->xmls as $xml)
            <tr>
                <td>{{ $xml->invoice_number ?? '—' }}</td>
                <td>{{ $xml->issued_at ? $xml->issued_at->format('d/m/Y') : '—' }}</td>
                <td>{{ $xml->provider_name ?? '—' }}</td>
                <td style="text-align:right;">
                    {{ $xml->amount ? 'R$ ' . number_format($xml->amount, 2, ',', '.') : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Observações --}}
    @if($invoice->notes)
    <div class="section-title">Observações</div>
    <p style="font-size:9pt;color:#374151;">{{ $invoice->notes }}</p>
    @endif

    {{-- Rodapé --}}
    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }} por {{ $user->name }} · Horas PJ
    </div>

</div>
</body>
</html>
