<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup do Banco de Dados</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f5f9;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;width:100%;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#0f172a;border-radius:12px 12px 0 0;padding:28px 32px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <p style="margin:0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#64748b;">Horas PJ</p>
                                        <p style="margin:6px 0 0;font-size:20px;font-weight:700;color:#f8fafc;">Backup do Banco de Dados</p>
                                    </td>
                                    <td align="right" style="vertical-align:middle;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="background-color:#166534;border-radius:99px;padding:5px 14px;">
                                                    <p style="margin:0;font-size:12px;font-weight:700;color:#dcfce7;">&#10003; SUCESSO</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color:#ffffff;padding:32px;">

                            <p style="margin:0 0 24px;font-size:14px;color:#475569;line-height:1.6;">
                                O backup automático foi executado com sucesso. O arquivo ZIP está em anexo a este e-mail.
                            </p>

                            <!-- Info table -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">

                                <tr style="background-color:#f8fafc;">
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;width:40%;">
                                        <p style="margin:0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Arquivo</p>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;">
                                        <p style="margin:0;font-size:13px;color:#1e293b;font-family:monospace;">{{ $fileName }}</p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;background-color:#f8fafc;">
                                        <p style="margin:0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Tamanho</p>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;">
                                        <p style="margin:0;font-size:13px;color:#1e293b;">{{ $sizeKb }} KB</p>
                                    </td>
                                </tr>

                                <tr style="background-color:#f8fafc;">
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;">
                                        <p style="margin:0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Banco</p>
                                    </td>
                                    <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;">
                                        <p style="margin:0;font-size:13px;color:#1e293b;">{{ strtoupper($connection) }}</p>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:12px 16px;background-color:#f8fafc;">
                                        <p style="margin:0;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;">Executado em</p>
                                    </td>
                                    <td style="padding:12px 16px;">
                                        <p style="margin:0;font-size:13px;color:#1e293b;">{{ $executedAt }}</p>
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8fafc;border-radius:0 0 12px 12px;border-top:1px solid #e2e8f0;padding:20px 32px;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;text-align:center;">
                                Este e-mail é gerado automaticamente pelo sistema Horas PJ todos os dias às 02:00.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
