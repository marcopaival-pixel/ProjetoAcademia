<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirme seu e-mail</title>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; line-height: 1.6; color: #f8fafc; background-color: #0f172a; margin: 0; padding: 0; }
        .wrapper { background-color: #0f172a; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #1e293b; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3); }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 40px 20px; text-align: center; }
        .header h1 { margin: 0; color: white; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 40px; text-align: center; }
        .content p { margin-bottom: 24px; font-size: 16px; color: #94a3b8; }
        .btn { display: inline-block; padding: 16px 32px; background: #3b82f6; color: white !important; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5); }
        .expire-hint { margin-top: 32px; font-size: 13px; color: #64748b; }
        .footer { padding: 24px; text-align: center; font-size: 12px; color: #475569; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
            </div>
            <div class="content">
                <p>Olá <strong>{{ $user->name }}</strong>,</p>
                <p>Obrigado por se cadastrar.</p>
                <p>Clique no link abaixo para confirmar seu email:</p>

                <a href="{{ $verificationUrl }}" class="btn">Confirmar e-mail</a>

                <p class="expire-hint">
                    Este link expira em {{ $ttlHours }} horas.<br>
                    Se você não realizou este cadastro, ignore este email.
                </p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </div>
        </div>
    </div>
</body>
</html>
