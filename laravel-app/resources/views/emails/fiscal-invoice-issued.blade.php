<!doctype html>
<html lang="pt-BR">
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5;">
    <h2>Nota fiscal emitida</h2>
    <p>Olá, {{ $invoice->user?->name ?? 'cliente' }}.</p>
    <p>A nota fiscal do pagamento #{{ $invoice->payment?->gateway_id ?? $invoice->payment_id }} foi emitida.</p>
    <p>
        <strong>Número:</strong> {{ $invoice->invoice_number ?? '-' }}<br>
        <strong>Código de verificação:</strong> {{ $invoice->verification_code ?? '-' }}<br>
        <strong>Valor:</strong> R$ {{ number_format((float) $invoice->net_amount, 2, ',', '.') }}
    </p>
    @if($invoice->official_url)
        <p><a href="{{ $invoice->official_url }}">Abrir nota fiscal</a></p>
    @endif
    <p>NexShape</p>
</body>
</html>
