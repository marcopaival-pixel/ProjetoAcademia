<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Documento — NexShape</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #06080c; color: white; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md glass rounded-[2.5rem] p-8 text-center space-y-8">
        <div class="w-20 h-20 bg-emerald-500/20 border border-emerald-500/30 rounded-full flex items-center justify-center mx-auto text-emerald-400 text-3xl">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <div class="space-y-2">
            <h1 class="text-2xl font-black italic tracking-tighter uppercase">Documento Autêntico</h1>
            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Validado pela rede NexShape Intelligence</p>
        </div>

        <div class="p-6 bg-white/5 rounded-3xl space-y-4 text-left">
            <div class="space-y-1">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Paciente</span>
                <p class="text-sm font-bold">{{ $user->name }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Tipo de Documento</span>
                <p class="text-sm font-bold">Relatório de Performance Mensal</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Versão</span>
                    <p class="text-sm font-bold">v{{ $report->version }}</p>
                </div>
                <div class="space-y-1">
                    <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Data de Emissão</span>
                    <p class="text-sm font-bold">{{ $report->generated_at->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="space-y-1 pt-4 border-t border-white/5">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">ID Único</span>
                <p class="text-[10px] font-mono text-zinc-400 break-all">{{ $report->document_id }}</p>
            </div>
        </div>

        <p class="text-[10px] text-zinc-600 leading-relaxed italic">
            Esta validação confirma que o documento foi emitido pelo sistema NexShape e não sofreu alterações desde sua geração original.
        </p>

        <div class="pt-4">
            <img src="{{ asset('images/logo_Rodape.png') }}" class="h-6 mx-auto opacity-30 grayscale" alt="NexShape">
        </div>
    </div>
</body>
</html>
