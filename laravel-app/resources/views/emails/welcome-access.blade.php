<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .content { margin-bottom: 30px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .link-box { background-color: #f8fafc; padding: 15px; border-radius: 6px; border: 1px dashed #cbd5e1; margin: 20px 0; word-break: break-all; }
        .footer { font-size: 12px; color: #64748b; text-align: center; }
        .qr-code { text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $accessLink->system_name }}</div>
        </div>
        <div class="content">
            <h1>Olá, {{ $user->name }}!</h1>
            <p>Sua conta foi criada com sucesso. Para facilitar seu acesso ao sistema, geramos um link direto para você.</p>
            
            <p><strong>Seu link de acesso permanente:</strong></p>
            <div class="link-box">
                {{ $accessLink->system_url }}
            </div>

            <div style="text-align: center;">
                <a href="{{ $accessLink->system_url }}" class="button">Acessar Sistema Agora</a>
            </div>

            <p><strong>Dicas Importantes:</strong></p>
            <ul>
                <li>Salve este link nos seus <strong>Favoritos</strong> do navegador.</li>
                <li>Se estiver no celular, você pode adicionar o link à sua <strong>Tela de Início</strong> como um aplicativo.</li>
            </ul>

            @if($accessLink->qr_code_path)
            <div class="qr-code">
                <p>Ou aponte a câmera do seu celular para o QR Code abaixo:</p>
                <img src="{{ $message->embed(storage_path('app/public/' . $accessLink->qr_code_path)) }}" alt="QR Code Access" width="150">
            </div>
            @endif

            <p><strong>Dados de Acesso:</strong><br>
            Usuário: {{ $user->email }}</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $accessLink->system_name }}. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
