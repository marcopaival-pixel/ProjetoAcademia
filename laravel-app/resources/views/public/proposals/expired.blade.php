<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta Expirada</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; background-color: #09090b; color: #fafafa; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-8 animate-fade-in">
        <div class="w-24 h-24 bg-red-600/10 border border-red-500/20 rounded-[2rem] flex items-center justify-center text-red-500 text-4xl mx-auto mb-10 shadow-lg shadow-red-600/10">
            <i class="fas fa-history"></i>
        </div>
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-4">Proposta Expirada</h1>
            <p class="text-zinc-500 text-sm leading-relaxed font-medium">Esta proposta comercial não está mais disponível. A validade encerrou em <span class="text-white font-bold">{{ $proposal->validade->format('d/m/Y') }}</span>.</p>
        </div>
        <p class="text-[10px] text-zinc-600 uppercase font-black tracking-widest leading-loose">Pode entrar em contato com nossa equipe para solicitar uma nova oferta atualizada.</p>
    </div>
</body>
</html>
