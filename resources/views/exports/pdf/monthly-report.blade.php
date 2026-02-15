<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Horas - {{ $month_label }}</title>
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
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
        }

        .header h1 {
            font-size: 18pt;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 12pt;
            color: #6b7280;
        }

        .header .generated {
            font-size: 8pt;
            color: #9ca3af;
            margin-top: 10px;
        }

        /* Summary Cards */
        .summary {
            margin-bottom: 25px;
        }

        .summary-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-grid {
            width: 100%;
        }

        .summary-grid td {
            width: 33.33%;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }

        .summary-card {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
        }

        .summary-card .label {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .summary-card .value {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
        }

        .summary-card .value.highlight {
            color: #059669;
        }

        /* Companies Section */
        .companies {
            margin-bottom: 25px;
        }

        .companies-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .companies-table {
            width: 100%;
            border-collapse: collapse;
        }

        .companies-table th,
        .companies-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .companies-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 9pt;
            color: #6b7280;
        }

        .companies-table td {
            font-size: 10pt;
        }

        .companies-table .text-right {
            text-align: right;
        }

        .companies-table .text-center {
            text-align: center;
        }

        /* Entries Section */
        .entries {
            margin-bottom: 25px;
        }

        .entries-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .entries-table {
            width: 100%;
            border-collapse: collapse;
        }

        .entries-table th,
        .entries-table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
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

        .entries-table .description {
            max-width: 200px;
            word-wrap: break-word;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Relatório de Horas</h1>
            <div class="subtitle">{{ $month_label }}</div>
            <div class="generated">Gerado em {{ $generated_at->format('d/m/Y') }} às {{ $generated_at->format('H:i') }}</div>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-title">Resumo do Período</div>
            <table class="summary-grid">
                <tr>
                    <td>
                        <div class="summary-card">
                            <div class="label">Total de Horas</div>
                            <div class="value">{{ number_format($stats['total_hours'], 1, ',', '.') }}h</div>
                        </div>
                    </td>
                    <td>
                        <div class="summary-card">
                            <div class="label">Valor/Hora</div>
                            <div class="value">R$ {{ number_format($stats['hourly_rate'], 2, ',', '.') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="summary-card">
                            <div class="label">Total a Receber</div>
                            <div class="value highlight">R$ {{ number_format($stats['total_with_extra'], 2, ',', '.') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Companies -->
        @if(!empty($stats['company_revenues']))
        <div class="companies">
            <div class="companies-title">Distribuição por Empresa</div>
            <table class="companies-table">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>CNPJ</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['company_revenues'] as $company)
                    <tr>
                        <td>{{ $company['name'] }}</td>
                        <td>{{ $company['cnpj'] }}</td>
                        <td class="text-right">R$ {{ number_format($company['revenue'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Entries -->
        <div class="entries">
            <div class="entries-title">Lançamentos ({{ $entries->count() }} registros)</div>
            @if($entries->count() > 0)
            <table class="entries-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="text-center">Início</th>
                        <th class="text-center">Fim</th>
                        <th class="text-right">Horas</th>
                        <th>Projeto</th>
                        <th class="description">Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry->date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ substr($entry->start_time, 0, 5) }}</td>
                        <td class="text-center">{{ substr($entry->end_time, 0, 5) }}</td>
                        <td class="text-right">{{ number_format($entry->hours, 2, ',', '.') }}</td>
                        <td>{{ $entry->project?->name ?? '-' }}</td>
                        <td class="description">{{ Str::limit($entry->description, 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color: #6b7280; text-align: center; padding: 20px;">Nenhum lançamento encontrado no período.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Controle de Horas PJ - {{ $user->name }}</p>
        </div>
    </div>
</body>
</html>
