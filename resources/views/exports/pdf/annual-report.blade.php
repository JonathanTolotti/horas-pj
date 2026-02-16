<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório Anual para IR - {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #7c3aed;
        }

        .header h1 {
            font-size: 18pt;
            color: #7c3aed;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 14pt;
            color: #4b5563;
            font-weight: bold;
        }

        .header .info {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 8px;
        }

        /* Summary Cards */
        .summary-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-grid td {
            width: 25%;
            padding: 8px;
            vertical-align: top;
        }

        .summary-card {
            background-color: #f3f4f6;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }

        .summary-card.highlight {
            background-color: #ede9fe;
            border: 1px solid #c4b5fd;
        }

        .summary-card .label {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-card .value {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
        }

        .summary-card .value.purple {
            color: #7c3aed;
        }

        .summary-card .value.green {
            color: #059669;
        }

        /* Monthly Table */
        .monthly-section {
            margin-bottom: 20px;
        }

        .monthly-table {
            width: 100%;
            border-collapse: collapse;
        }

        .monthly-table th,
        .monthly-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }

        .monthly-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #6b7280;
            font-size: 8pt;
            text-transform: uppercase;
        }

        .monthly-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .monthly-table .text-right {
            text-align: right;
        }

        .monthly-table .text-center {
            text-align: center;
        }

        .monthly-table tfoot td {
            background-color: #ede9fe;
            font-weight: bold;
            border-top: 2px solid #7c3aed;
        }

        .monthly-table .bar-cell {
            width: 100px;
        }

        .bar-container {
            background-color: #e5e7eb;
            border-radius: 3px;
            height: 12px;
            overflow: hidden;
        }

        .bar-fill {
            background-color: #7c3aed;
            height: 100%;
            border-radius: 3px;
        }

        /* Company Section */
        .company-section {
            margin-bottom: 20px;
        }

        .company-table {
            width: 100%;
            border-collapse: collapse;
        }

        .company-table th,
        .company-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .company-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
        }

        .company-table .text-right {
            text-align: right;
        }

        .company-table .percentage {
            color: #7c3aed;
            font-weight: bold;
        }

        /* Highlights Section */
        .highlights-section {
            margin-bottom: 20px;
        }

        .highlights-grid {
            width: 100%;
        }

        .highlights-grid td {
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }

        .highlight-box {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 12px;
        }

        .highlight-box.warning {
            background-color: #fef3c7;
            border-color: #fcd34d;
        }

        .highlight-box .title {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .highlight-box .month {
            font-size: 12pt;
            font-weight: bold;
            color: #1f2937;
        }

        .highlight-box .value {
            font-size: 10pt;
            color: #059669;
            font-weight: bold;
        }

        .highlight-box.warning .value {
            color: #d97706;
        }

        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer .note {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .footer .disclaimer {
            font-size: 7pt;
            color: #9ca3af;
            font-style: italic;
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
            <h1>Relatório Anual para Imposto de Renda</h1>
            <div class="subtitle">Ano-Calendário {{ $year }}</div>
            <div class="info">
                {{ $user->name }} | Gerado em {{ $generated_at->format('d/m/Y') }} às {{ $generated_at->format('H:i') }}
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="section-title">Resumo do Ano</div>
            <table class="summary-grid">
                <tr>
                    <td>
                        <div class="summary-card highlight">
                            <div class="label">Total Faturado</div>
                            <div class="value purple">R$ {{ number_format($total_revenue, 2, ',', '.') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="summary-card">
                            <div class="label">Total de Horas</div>
                            <div class="value">{{ sprintf('%02d:%02d', floor($total_hours), round(($total_hours - floor($total_hours)) * 60)) }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="summary-card">
                            <div class="label">Média Mensal</div>
                            <div class="value green">R$ {{ number_format($average_monthly_revenue, 2, ',', '.') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="summary-card">
                            <div class="label">Meses Trabalhados</div>
                            <div class="value">{{ $months_worked }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Highlights Section -->
        @if($best_month && $worst_month)
        <div class="highlights-section">
            <div class="section-title">Destaques</div>
            <table class="highlights-grid">
                <tr>
                    <td>
                        <div class="highlight-box">
                            <div class="title">Melhor Mês</div>
                            <div class="month">{{ $best_month['month_name'] }}</div>
                            <div class="value">R$ {{ number_format($best_month['revenue'], 2, ',', '.') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="highlight-box warning">
                            <div class="title">Menor Faturamento</div>
                            <div class="month">{{ $worst_month['month_name'] }}</div>
                            <div class="value">R$ {{ number_format($worst_month['revenue'], 2, ',', '.') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Monthly Breakdown -->
        <div class="monthly-section">
            <div class="section-title">Faturamento Mensal</div>
            <table class="monthly-table">
                <thead>
                    <tr>
                        <th>Mês</th>
                        <th class="text-right">Horas</th>
                        <th class="text-right">Faturado</th>
                        <th class="text-right">Ajustes</th>
                        <th class="text-right">Total</th>
                        <th class="bar-cell">% do Ano</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxRevenue = max(array_column($monthly_data, 'revenue')) ?: 1; @endphp
                    @foreach($monthly_data as $month)
                    <tr>
                        <td>{{ $month['month_name'] }}</td>
                        <td class="text-right">{{ sprintf('%02d:%02d', floor($month['hours']), round(($month['hours'] - floor($month['hours'])) * 60)) }}</td>
                        <td class="text-right">R$ {{ number_format($month['hours_revenue'], 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format(($month['extra_value'] ?? 0) - ($month['discount_value'] ?? 0), 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($month['revenue'], 2, ',', '.') }}</td>
                        <td class="bar-cell">
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ ($month['revenue'] / $maxRevenue) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{ sprintf('%02d:%02d', floor($total_hours), round(($total_hours - floor($total_hours)) * 60)) }}</strong></td>
                        <td class="text-right"><strong>R$ {{ number_format($total_hours * $hourly_rate, 2, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>R$ {{ number_format(($extra_value - $discount_value) * count($monthly_data), 2, ',', '.') }}</strong></td>
                        <td class="text-right"><strong>R$ {{ number_format($total_revenue, 2, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Company Breakdown -->
        @if(count($company_revenues) > 0)
        <div class="company-section">
            <div class="section-title">Faturamento por Empresa/CNPJ</div>
            <table class="company-table">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>CNPJ</th>
                        <th class="text-right">Total Faturado</th>
                        <th class="text-right">% do Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($company_revenues as $company)
                    <tr>
                        <td>{{ $company['name'] }}</td>
                        <td>{{ $company['cnpj'] }}</td>
                        <td class="text-right">R$ {{ number_format($company['revenue'], 2, ',', '.') }}</td>
                        <td class="text-right percentage">{{ $company['percentage'] }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p class="note">
                Valor/Hora: R$ {{ number_format($hourly_rate, 2, ',', '.') }}
                @if(($extra_value ?? 0) > 0)
                | Acréscimo Mensal: R$ {{ number_format($extra_value, 2, ',', '.') }}
                @endif
                @if(($discount_value ?? 0) > 0)
                | Desconto Mensal: R$ {{ number_format($discount_value, 2, ',', '.') }}
                @endif
            </p>
            <p class="disclaimer">
                Este relatório é um resumo dos lançamentos registrados no sistema e serve como apoio para a declaração de Imposto de Renda.
                Consulte seu contador para garantir o correto preenchimento da declaração.
            </p>
            <p class="disclaimer" style="margin-top: 10px;">
                Documento gerado por: https://horas.jonathantolotti.com.br
            </p>
        </div>
    </div>
</body>
</html>
