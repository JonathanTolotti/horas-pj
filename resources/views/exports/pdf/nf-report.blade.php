<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório para NF - {{ $company->name }} - {{ $month_label }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.5;
        }

        .container {
            padding: 30px;
        }

        /* Header */
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e40af;
        }

        .header h1 {
            font-size: 16pt;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 11pt;
            color: #4b5563;
            font-weight: bold;
        }

        /* Company Info */
        .company-info {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .company-info-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #d1d5db;
        }

        .company-info-grid {
            width: 100%;
        }

        .company-info-grid td {
            padding: 5px 0;
            vertical-align: top;
        }

        .company-info-grid .label {
            font-weight: bold;
            color: #6b7280;
            width: 120px;
        }

        .company-info-grid .value {
            color: #1f2937;
        }

        /* Service Description */
        .service-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .service-box-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .service-box-content {
            font-size: 10pt;
            color: #1f2937;
        }

        /* Summary */
        .summary-box {
            background-color: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .summary-box-title {
            font-size: 11pt;
            font-weight: bold;
            color: #166534;
            margin-bottom: 15px;
        }

        .summary-table {
            width: 100%;
        }

        .summary-table td {
            padding: 8px 0;
            border-bottom: 1px solid #bbf7d0;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
            padding-top: 15px;
        }

        .summary-table .label {
            color: #166534;
            font-weight: bold;
        }

        .summary-table .value {
            text-align: right;
            color: #1f2937;
        }

        .summary-table .total .label,
        .summary-table .total .value {
            font-size: 14pt;
            color: #166534;
        }

        /* Entries Detail */
        .entries-section {
            margin-bottom: 25px;
        }

        .entries-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .entries-table {
            width: 100%;
            border-collapse: collapse;
        }

        .entries-table th,
        .entries-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 9pt;
        }

        .entries-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #6b7280;
        }

        .entries-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .entries-table .text-right {
            text-align: right;
        }

        .entries-table .text-center {
            text-align: center;
        }

        .entries-table tfoot td {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .signature-area {
            margin-top: 50px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            width: 250px;
            margin: 0 auto;
            padding-top: 5px;
            font-size: 9pt;
            color: #6b7280;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Relatório de Serviços Prestados</h1>
            <div class="subtitle">Referência: {{ $month_label }}</div>
        </div>

        <!-- Company Info -->
        <div class="company-info">
            <div class="company-info-title">Dados do Tomador de Serviço</div>
            <table class="company-info-grid">
                <tr>
                    <td class="label">Razão Social:</td>
                    <td class="value">{{ $company->name }}</td>
                </tr>
                <tr>
                    <td class="label">CNPJ:</td>
                    <td class="value">{{ $company->cnpj }}</td>
                </tr>
            </table>
        </div>

        <!-- Service Description -->
        <div class="service-box">
            <div class="service-box-title">Descrição do Serviço</div>
            <div class="service-box-content">
                Serviços de desenvolvimento de software e consultoria em tecnologia da informação,
                conforme detalhamento de horas trabalhadas abaixo.
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-box-title">Resumo para Faturamento</div>
            <table class="summary-table">
                <tr>
                    <td class="label">Total de Horas Trabalhadas:</td>
                    <td class="value">{{ number_format($stats['total_hours'], 1, ',', '.') }} horas</td>
                </tr>
                <tr>
                    <td class="label">Valor por Hora:</td>
                    <td class="value">R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</td>
                </tr>
                @php
                    $companyData = collect($stats['company_revenues'])->firstWhere('id', $company->id);
                    $companyValue = $companyData['revenue'] ?? $stats['total_with_extra'];
                @endphp
                <tr class="total">
                    <td class="label">VALOR TOTAL:</td>
                    <td class="value">R$ {{ number_format($companyValue, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Entries Detail -->
        <div class="entries-section">
            <div class="entries-title">Detalhamento das Horas ({{ $entries->count() }} lançamentos)</div>
            @if($entries->count() > 0)
            <table class="entries-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="text-center">Início</th>
                        <th class="text-center">Fim</th>
                        <th class="text-right">Horas</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry->date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ substr($entry->start_time, 0, 5) }}</td>
                        <td class="text-center">{{ substr($entry->end_time, 0, 5) }}</td>
                        <td class="text-right">{{ number_format($entry->hours, 2, ',', '.') }}</td>
                        <td>{{ Str::limit($entry->description, 60) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">Total:</td>
                        <td class="text-right">{{ number_format($entries->sum('hours'), 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
            <p style="color: #6b7280; text-align: center; padding: 20px;">Nenhum lançamento encontrado no período.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="signature-area">
                <div class="signature-line">
                    {{ $user->name }}<br>
                    Prestador de Serviços
                </div>
            </div>

            <div class="footer-note">
                <p>Documento gerado em {{ $generated_at->format('d/m/Y') }} às {{ $generated_at->format('H:i') }}</p>
                <p>Este documento serve como comprovante das horas trabalhadas para fins de emissão de Nota Fiscal.</p>
            </div>
        </div>
    </div>
</body>
</html>
