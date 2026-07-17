<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #1e293b;">
    <h2 style="margin-bottom: 8px;">Documento gerado</h2>
    @if($historico->numero_oficial)
        <p><strong>Número:</strong> {{ $historico->numero_oficial }}</p>
    @endif
    @if($urlValidacao)
        <p><strong>Validação:</strong> <a href="{{ $urlValidacao }}">{{ $urlValidacao }}</a></p>
    @endif
    <p>O PDF encontra-se em anexo.</p>
    <p style="color: #64748b; font-size: 12px;">{{ config('app.name') }}</p>
</body>
</html>
