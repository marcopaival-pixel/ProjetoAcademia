<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta Comercial - {{ $proposal->lead->nome }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #09090b; color: #fafafa; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen selection:bg-blue-500/30">
    <!-- Header -->
    <header class="max-w-5xl mx-auto py-12 px-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
            <div class="space-y-2">
                <span class="px-4 py-1.5 bg-blue-600/10 border border-blue-500/20 text-blue-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-full">Proposta Comercial</span>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mt-4">Olá, {{ explode(' ', $proposal->lead->nome)[0] }}!</h1>
                <p class="text-zinc-500 font-medium">Preparamos uma oferta exclusiva para a <span class="text-blue-400">{{ $proposal->lead->empresa ?? 'sua empresa' }}</span>.</p>
            </div>
            <div class="glass p-6 rounded-[2.5rem] w-full md:w-auto text-center md:text-left">
                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-1">Válido até</p>
                <p class="text-lg font-black text-white">{{ $proposal->validade->format('d/m/Y') }}</p>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 pb-24 grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Detalhes do Plano -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass rounded-[3rem] p-10 relative overflow-hidden group">
                <div class="absolute top-[-50px] right-[-50px] w-64 h-64 bg-blue-600/5 blur-[100px] rounded-full transition-all group-hover:bg-blue-600/10"></div>
                
                <h2 class="text-zinc-500 text-xs font-black uppercase tracking-widest mb-10 flex items-center gap-3">
                    <span class="w-8 h-px bg-zinc-800"></span> O Investigimento
                </h2>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                    <div>
                        <h3 class="text-3xl font-black text-white mb-2">{{ $proposal->plan->name }}</h3>
                        <p class="text-sm text-zinc-400">Solução completa para gestão e escala da sua academia.</p>
                    </div>
                    <div class="text-right">
                        @if($proposal->desconto > 0)
                            <p class="text-sm text-zinc-500 line-through">R$ {{ number_format($proposal->valor, 2, ',', '.') }}</p>
                        @endif
                        <p class="text-5xl font-black text-white tracking-tighter">R$ {{ number_format($proposal->valor - $proposal->desconto, 2, ',', '.') }}</p>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-2">Valor Total</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-12">
                    @php
                        $features = [
                            'student' => ['Gestão de Alunos', 'Treinos Inteligentes', 'App para Aluno'],
                            'professional' => ['Gestão Financeira', 'Avaliação Física', 'Relatórios Avançados'],
                            'full' => ['Tudo do Pro', 'Site Personalizado', 'Automação Marketing']
                        ];
                        $planFeatures = $features[$proposal->plan->type] ?? ['Recursos do plano selecionado'];
                    @endphp
                    @foreach($planFeatures as $feature)
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/[0.02] border border-white/5">
                        <div class="w-8 h-8 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500 text-xs shadow-inner">
                            <i class="fas fa-check"></i>
                        </div>
                        <span class="text-sm font-semibold text-zinc-300">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>

                @if($proposal->observacoes)
                <div class="pt-10 border-t border-white/5">
                    <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Condições Adicionais</h4>
                    <p class="text-sm text-zinc-400 italic leading-relaxed">{{ $proposal->observacoes }}</p>
                </div>
                @endif
            </div>

            <!-- Por que nós? -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass p-8 rounded-[2.5rem] border border-blue-500/10 hover:border-blue-500/30 transition-all">
                    <i class="fas fa-chart-line text-blue-500 text-2xl mb-6"></i>
                    <h4 class="text-white font-black text-sm uppercase tracking-widest mb-3">Aumento de Retenção</h4>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">A IA do NexShape mantém seus alunos engajados, aumentando o LTV (Lifetime Value) médio em 25%.</p>
                </div>
                <div class="glass p-8 rounded-[2.5rem] border border-emerald-500/10 hover:border-emerald-500/30 transition-all">
                    <i class="fas fa-wallet text-emerald-500 text-2xl mb-6"></i>
                    <h4 class="text-white font-black text-sm uppercase tracking-widest mb-3">Previsibilidade Financeira</h4>
                    <p class="text-xs text-zinc-500 leading-relaxed font-medium">Sistema de cobrança automatizado que reduz a inadimplência e garante caixa todos os meses.</p>
                </div>
            </div>

            <!-- Próximos Passos -->
            <div class="glass rounded-[3rem] p-10 mt-8 relative overflow-hidden hidden md:block">
                <h3 class="text-zinc-500 text-xs font-black uppercase tracking-widest mb-8 flex items-center gap-3">
                    <span class="w-8 h-px bg-zinc-800"></span> Próximos Passos
                </h3>
                
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-4 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-zinc-800 before:to-transparent">
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-blue-500 bg-zinc-950 text-blue-500 shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 shadow-[0_0_15px_rgba(59,130,246,0.3)] z-10 font-bold text-xs">1</div>
                        <div class="w-[calc(100%-3rem)] md:w-[calc(50%-2rem)] p-5 rounded-2xl glass border border-blue-500/10">
                            <h4 class="font-bold text-white text-sm mb-1">Aceite & Contrato</h4>
                            <p class="text-[10px] text-zinc-500 leading-relaxed">Você aceita a proposta online e o contrato digital é gerado automaticamente para você.</p>
                        </div>
                    </div>
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-zinc-700 bg-zinc-950 text-zinc-500 shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 font-bold text-xs">2</div>
                        <div class="w-[calc(100%-3rem)] md:w-[calc(50%-2rem)] p-5 rounded-2xl glass border border-white/5">
                            <h4 class="font-bold text-white text-sm mb-1">Onboarding</h4>
                            <p class="text-[10px] text-zinc-500 leading-relaxed">Nossa equipe agenda com você para importar alunos e configurar a plataforma.</p>
                        </div>
                    </div>
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 border-zinc-700 bg-zinc-950 text-zinc-500 shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 font-bold text-xs">3</div>
                        <div class="w-[calc(100%-3rem)] md:w-[calc(50%-2rem)] p-5 rounded-2xl glass border border-white/5">
                            <h4 class="font-bold text-white text-sm mb-1">Lançamento (Go-Live)</h4>
                            <p class="text-[10px] text-zinc-500 leading-relaxed">Sua clínica operando com inteligência artificial de elite desde o primeiro dia.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna de Ação -->
        <div class="space-y-8 relative w-full pb-32 md:pb-0">
            <div class="glass rounded-t-[3rem] md:rounded-[2rem] p-8 border-t md:border border-blue-500/20 fixed md:sticky bottom-0 md:top-12 left-0 w-full md:w-auto z-50 bg-[#09090b]/95 md:bg-transparent backdrop-blur-2xl shadow-[0_-20px_50px_rgba(0,0,0,0.8)] md:shadow-none">
                <h3 class="text-xl font-black text-white mb-6 hidden md:block">Tudo certo?</h3>
                
                @if($proposal->status == 'Aprovada')
                <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-center">
                    <i class="fas fa-check-circle text-3xl text-emerald-500 mb-4"></i>
                    <p class="text-xs text-emerald-400 font-black uppercase tracking-widest leading-loose">Esta proposta já foi aprovada!</p>
                </div>
                @elseif($proposal->status == 'Rejeitada')
                <div class="p-6 bg-red-500/10 border border-red-500/20 rounded-2xl text-center">
                    <i class="fas fa-times-circle text-3xl text-red-500 mb-4"></i>
                    <p class="text-xs text-red-400 font-black uppercase tracking-widest leading-loose">Esta proposta foi rejeitada em seu formato atual.</p>
                </div>
                @else
                <div class="space-y-4">
                    <form action="{{ route('public.proposal.accept', $proposal->token) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-5 bg-blue-600 rounded-2xl text-xs text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-[0_0_30px_rgba(37,99,235,0.4)] animate-pulse hover:animate-none">
                            Aceitar Proposta
                        </button>
                    </form>
                    
                    <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="w-full py-4 bg-zinc-900 border border-white/5 rounded-2xl text-[10px] text-zinc-500 font-black uppercase tracking-widest hover:bg-zinc-800 hover:text-white transition-all">
                        Solicitar Ajustes
                    </button>
                    
                    <p class="text-[9px] text-zinc-600 text-center font-bold px-4 leading-loose uppercase mt-4 italic">Ao aceitar, você concorda com nossos termos de serviço e política de privacidade.</p>
                </div>
                @endif

                <div class="mt-12 pt-8 border-t border-white/5">
                    <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-4">Dúvidas? Fale conosco</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-500 border border-white/5">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-white">Equipe Comercial</p>
                            <p class="text-[10px] text-zinc-500 font-medium">(00) 00000-0000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-6">
        <div class="glass w-full max-w-md rounded-[2.5rem] p-10 animate-fade-in">
            <h3 class="text-xl font-black text-white mb-4 tracking-tight">Solicitar Ajustes</h3>
            <p class="text-xs text-zinc-500 font-medium mb-6">Conte-nos o que falta para fecharmos a parceria. Analisaremos seu feedback agora mesmo.</p>
            <form action="{{ route('public.proposal.reject', $proposal->token) }}" method="POST">
                @csrf
                <textarea name="motivo" required rows="4" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-5 py-4 text-sm text-zinc-300 outline-none focus:border-red-500/50 resize-none mb-6" placeholder="Descreva o motivo ou melhoria sugerida..."></textarea>
                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full py-4 bg-red-600 rounded-2xl text-xs text-white font-black uppercase tracking-widest hover:bg-red-500 transition-all">
                        Enviar Feedback
                    </button>
                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="w-full py-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
