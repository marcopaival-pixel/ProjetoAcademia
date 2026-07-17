<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teste de E-mail</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { background: #3d9cf5; color: white; padding: 15px; border-radius: 10px 10px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #999; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Configuração Bem-sucedida!</h1>
        </div>
        <div class="content">
            <p>Olá, <strong>{{ $userName }}</strong>,</p>
            <p>Este é um e-mail de teste enviado pelo sistema <strong>{{ config('app.name') }}</strong> para confirmar que suas configurações de SMTP estão funcionando corretamente.</p>
            <p>Se você recebeu esta mensagem, significa que o sistema já pode enviar notificações, lembretes e outros comunicados aos utilizadores.</p>
            <p>Data do teste: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
        <div class="footer">
            Este e-mail foi gerado automaticamente. Por favor, não responda.
        </div>
    </div>
</body>
</html>
