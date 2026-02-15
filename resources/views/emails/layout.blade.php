<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Controle de Horas PJ' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            padding: 30px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 5px 0 0;
            font-size: 14px;
        }

        .email-body {
            padding: 30px;
        }

        .email-body h2 {
            color: #1f2937;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .email-body p {
            color: #4b5563;
            margin-bottom: 15px;
        }

        .highlight-box {
            background-color: #f0fdfa;
            border-left: 4px solid #0891b2;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .highlight-box p {
            margin: 0;
            color: #0f766e;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .info-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table td {
            padding: 12px 0;
        }

        .info-table td:first-child {
            color: #6b7280;
            width: 40%;
        }

        .info-table td:last-child {
            color: #1f2937;
            font-weight: 500;
            text-align: right;
        }

        .total-row td {
            padding-top: 15px;
            font-size: 18px;
        }

        .total-row td:last-child {
            color: #059669;
            font-weight: 700;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }

        .btn:hover {
            background: linear-gradient(135deg, #0e7490, #0891b2);
        }

        .btn-secondary {
            background: #6b7280;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #9ca3af;
            font-size: 14px;
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 5px 0;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 25px 0;
        }

        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 10px;
            }

            .email-body {
                padding: 20px;
            }

            .email-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1>Controle de Horas PJ</h1>
                <p>Gerencie suas horas de trabalho</p>
            </div>

            <div class="email-body">
                @yield('content')
            </div>

            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Controle de Horas PJ. Todos os direitos reservados.</p>
                <p>Este é um e-mail automático, por favor não responda.</p>
            </div>
        </div>
    </div>
</body>
</html>
