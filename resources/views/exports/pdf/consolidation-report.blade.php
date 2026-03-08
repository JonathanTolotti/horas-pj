<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Consolidação {{ $start_date_formatted }} a {{ $end_date_formatted }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.4;
        }

        .container { padding: 20px; }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 { font-size: 18pt; color: #1e40af; margin-bottom: 5px; }
        .header .subtitle { font-size: 12pt; color: #6b7280; }
        .header .generated { font-size: 8pt; color: #9ca3af; margin-top: 10px; }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary { margin-bottom: 25px; }
        .summary-grid { width: 100%; }
        .summary-grid td { width: 33.33%; padding: 10px; text-align: center; vertical-align: top; }
        .summary-card { background-color: #f3f4f6; border-radius: 8px; padding: 15px; }
        .summary-card .label { font-size: 9pt; color: #6b7280; margin-bottom: 5px; }
        .summary-card .value { font-size: 16pt; font-weight: bold; color: #1f2937; }
        .summary-card .value.green { color: #059669; }
        .summary-card .value.orange { color: #ea580c; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .data-table th,
        .data-table td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }
        .data-table th { background-color: #f3f4f6; font-weight: bold; color: #6b7280; }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .description { max-width: 180px; word-wrap: break-word; }
        .green { color: #059669; }
        .orange { color: #ea580c; font-weight: bold; }

        .total-row td { font-weight: bold; background-color: #f3f4f6; border-top: 2px solid #e5e7eb; }

        .adjustments {
            margin-bottom: 20px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .adjustments .row { display: block; font-size: 9pt; color: #6b7280; margin-bottom: 3px; }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            page-break-before: avoid;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Cabeçalho -->
    <div class="header">
        <h1>Consolidação de Período</h1>
        <div class="subtitle">{{ $start_date_formatted }} a {{ $end_date_formatted }}</div>
        <div class="generated">Gerado em {{ $generated_at->format('d/m/Y') }} às {{ $generated_at->format('H:i') }}</div>
    </div>

    <!-- Resumo -->
    <div class="summary">
        <div class="section-title">Resumo</div>
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-card">
                        <div class="label">Horas Trabalhadas</div>
                        <div class="value">{{ sprintf('%02d:%02d', floor($total_hours), round(($total_hours - floor($total_hours)) * 60)) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="label">Receita (Horas)</div>
                        <div class="value green">R$ {{ number_format($total_revenue, 2, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="label">Total a Receber</div>
                        <div class="value green">R$ {{ number_format($total_final, 2, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
            @if($total_on_call_hours > 0)
            <tr>
                <td>
                    <div class="summary-card">
                        <div class="label">Horas Sobreaviso</div>
                        <div class="value orange">{{ sprintf('%02d:%02d', floor($total_on_call_hours), round(($total_on_call_hours - floor($total_on_call_hours)) * 60)) }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="label">Receita (Sobreaviso)</div>
                        <div class="value orange">R$ {{ number_format($total_on_call_revenue, 2, ',', '.') }}</div>
                    </div>
                </td>
                <td></td>
            </tr>
            @endif
        </table>

        @if($extra_value > 0 || $discount_value > 0)
        <div class="adjustments" style="margin-top: 10px;">
            @if($extra_value > 0)
            <span class="row">Acréscimo: <strong>+R$ {{ number_format($extra_value, 2, ',', '.') }}</strong></span>
            @endif
            @if($discount_value > 0)
            <span class="row">Desconto: <strong>-R$ {{ number_format($discount_value, 2, ',', '.') }}</strong></span>
            @endif
        </div>
        @endif
    </div>

    <!-- Distribuição por empresa -->
    @if(!empty($company_revenues))
    <div class="summary">
        <div class="section-title">Distribuição por Empresa</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>CNPJ</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($company_revenues as $company)
                <tr>
                    <td>{{ $company['name'] }}</td>
                    <td>{{ $company['cnpj'] ?? '-' }}</td>
                    <td class="text-right green">R$ {{ number_format($company['revenue'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
                @if($unassigned_revenue > 0)
                <tr>
                    <td colspan="2" style="color: #9ca3af;">Não atribuído</td>
                    <td class="text-right" style="color: #9ca3af;">R$ {{ number_format($unassigned_revenue, 2, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    <!-- Sobreavisos -->
    @if($onCallPeriods->isNotEmpty())
    <div class="summary">
        <div class="section-title">Períodos de Sobreaviso ({{ $onCallPeriods->count() }} registros)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Período</th>
                    <th>Projeto</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Trabalhado</th>
                    <th class="text-center">Sobreaviso</th>
                    <th class="text-right">Valor/h</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($onCallPeriods as $period)
                <tr>
                    <td>{{ $period->start_datetime->format('d/m H:i') }} – {{ $period->end_datetime->format('d/m H:i') }}</td>
                    <td>{{ $period->project?->name ?? 'Geral' }}</td>
                    <td class="text-center">{{ sprintf('%02d:%02d', floor($period->total_hours), round(($period->total_hours - floor($period->total_hours)) * 60)) }}</td>
                    <td class="text-center">{{ sprintf('%02d:%02d', floor($period->worked_hours), round(($period->worked_hours - floor($period->worked_hours)) * 60)) }}</td>
                    <td class="text-center orange">{{ sprintf('%02d:%02d', floor($period->on_call_hours), round(($period->on_call_hours - floor($period->on_call_hours)) * 60)) }}</td>
                    <td class="text-right">R$ {{ number_format($period->hourly_rate, 2, ',', '.') }}</td>
                    <td class="text-right orange">R$ {{ number_format($period->computed_on_call_revenue, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Lançamentos -->
    <div>
        <div class="section-title">Lançamentos ({{ $entries->count() }} registros)</div>
        @if($entries->isNotEmpty())
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th class="text-center">Início</th>
                    <th class="text-center">Fim</th>
                    <th class="text-right">Horas</th>
                    <th>Projeto</th>
                    <th class="description">Descrição</th>
                    <th class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr>
                    <td>{{ $entry->date->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $entry->start_time ? substr($entry->start_time, 0, 5) : '-' }}</td>
                    <td class="text-center">{{ $entry->end_time ? substr($entry->end_time, 0, 5) : '-' }}</td>
                    <td class="text-right">{{ sprintf('%02d:%02d', floor($entry->hours), round(($entry->hours - floor($entry->hours)) * 60)) }}</td>
                    <td>{{ $entry->project?->name ?? '-' }}</td>
                    <td class="description">{{ Str::limit($entry->description, 45) }}</td>
                    <td class="text-right green">R$ {{ number_format($entry->computed_revenue, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3">Total</td>
                    <td class="text-right">{{ sprintf('%02d:%02d', floor($total_hours), round(($total_hours - floor($total_hours)) * 60)) }}</td>
                    <td colspan="2"></td>
                    <td class="text-right green">R$ {{ number_format($total_revenue, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @else
        <p style="color: #6b7280; text-align: center; padding: 20px;">Nenhum lançamento selecionado.</p>
        @endif
    </div>

    <!-- Rodapé -->
    <div class="footer">
        <p>Controle de Horas PJ – {{ $user->name }}</p>
        <p style="margin-top: 5px;">Documento gerado por: https://horas.jonathantolotti.com.br</p>
    </div>

</div>
</body>
</html>
