<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de documento — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="max-w-lg mx-auto px-4 py-16">
        <h1 class="text-2xl font-black tracking-tight mb-2">Validação de documento</h1>
        <p class="text-sm text-slate-500 mb-8">{{ config('app.name') }}</p>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-8 space-y-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Estado</p>
                <p class="text-xl font-bold mt-1 @if($isValid) text-emerald-400 @elseif($status === \App\Enums\PdfValidationStatus::Cancelled) text-red-400 @else text-amber-400 @endif">
                    {{ $statusLabel }}
                </p>
            </div>
            @if($historico->numero_oficial)
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Número oficial</p>
                <p class="font-mono text-sm mt-1">{{ $historico->numero_oficial }}</p>
            </div>
            @endif
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Código</p>
                <p class="font-mono text-xs break-all mt-1 text-slate-400">{{ $historico->codigo_validacao }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Emitido em</p>
                <p class="text-sm mt-1">{{ $historico->issued_at?->format('d/m/Y H:i') }}</p>
            </div>
            @if($historico->company)
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Empresa</p>
                <p class="text-sm mt-1">{{ $historico->company->name }}</p>
            </div>
            @endif
        </div>
        <p class="text-[11px] text-slate-600 mt-8 text-center">Em caso de dúvida, contacte a academia emitente.</p>
    </div>
</body>
</html>
