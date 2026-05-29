<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falha na Validação — NexShape</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #06080c; color: white; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md glass rounded-[2.5rem] p-10 text-center space-y-8">
        <div class="w-20 h-20 bg-rose-500/20 border border-rose-500/30 rounded-full flex items-center justify-center mx-auto text-rose-500 text-3xl">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <div class="space-y-3">
            <h1 class="text-2xl font-black italic tracking-tighter uppercase text-rose-500">Documento Inválido</h1>
            <p class="text-zinc-400 text-xs font-medium">{{ $message }}</p>
        </div>

        <div class="p-6 bg-rose-500/5 rounded-3xl border border-rose-500/10">
            <p class="text-[11px] text-zinc-500 leading-relaxed">
                A assinatura digital deste documento não pôde ser verificada. Isso pode ocorrer se o documento foi alterado, se a versão é obsoleta ou se o token de segurança expirou.
            </p>
        </div>

        <div class="space-y-4">
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Ações Recomendadas</p>
            <div class="grid grid-cols-1 gap-2">
                <div class="p-3 bg-white/5 rounded-xl text-[10px] text-zinc-400 text-left flex items-center gap-3">
                    <i class="fas fa-sync text-zinc-600"></i>
                    Solicite uma nova via atualizada ao profissional.
                </div>
                <div class="p-3 bg-white/5 rounded-xl text-[10px] text-zinc-400 text-left flex items-center gap-3">
                    <i class="fas fa-shield-alt text-zinc-600"></i>
                    Verifique se o QR Code está íntegro e legível.
                </div>
            </div>
        </div>

        <div class="pt-4">
            <img src="{{ asset('images/logo_Rodape.png') }}" class="h-6 mx-auto opacity-30 grayscale" alt="NexShape">
        </div>
    </div>
</body>
</html>
