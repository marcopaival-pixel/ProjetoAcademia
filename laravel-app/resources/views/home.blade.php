@extends('layouts.app')

@section('title', 'NexShape — Ecossistema de Elite para Treino, Nutrição e Gestão Clínica')

@section('content')
<div class="space-y-32 py-10 px-6 max-w-[1400px] mx-auto overflow-hidden">
    
    <!-- Ultra Impact Hero -->
    <section class="relative flex flex-col lg:flex-row items-center gap-16 py-10 animate-fade-in">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-blue-600/5 blur-[150px] rounded-full pointer-events-none"></div>
        
        <div class="flex-1 space-y-10 text-center lg:text-left relative z-20">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest animate-bounce-slow">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Próxima Geração de Inteligência Fitness
            </div>

            <h1 class="text-6xl md:text-8xl font-black text-white tracking-tighter leading-[0.85]">
                O Futuro do <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-indigo-400 to-emerald-400">Bio-Performance.</span>
            </h1>
            
            <p class="text-zinc-500 text-xl font-medium max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                Do aluno iniciante à clínica de alta performance. O NexShape une IA generativa, monitoramento biométrico e gestão completa em um único ecossistema.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-6 pt-6">
                <a href="{{ route('onboarding.welcome') }}" class="group relative px-10 py-5 bg-white text-zinc-950 font-black rounded-[2rem] hover:bg-blue-500 hover:text-white transition-all shadow-2xl active:scale-95">
                    <span class="relative z-10">COMEÇAR AGORA — É GRÁTIS</span>
                </a>
                <a href="#pricing" class="px-10 py-5 bg-zinc-900/50 backdrop-blur-xl border border-white/5 text-white font-black rounded-[2rem] hover:bg-zinc-800 transition-all active:scale-95">
                    Ver Planos Business
                </a>
            </div>
        </div>

        <!-- CSS 3D Globe -->
        <div class="flex-1 flex items-center justify-center relative" style="min-height:380px;">
            <div class="globe-wrapper">
                <!-- Atmosfera / Glow externo -->
                <div class="globe-atmosphere"></div>

                <!-- Corpo do globo -->
                <div class="globe-sphere">
                    <!-- Sombra interna de profundidade -->
                    <div class="globe-depth"></div>

                    <!-- Grade de meridianos (anéis verticais) -->
                    <div class="globe-ring globe-ring--v1"></div>
                    <div class="globe-ring globe-ring--v2"></div>
                    <div class="globe-ring globe-ring--v3"></div>

                    <!-- Grade de paralelos (anéis horizontais) -->
                    <div class="globe-ring globe-ring--h1"></div>
                    <div class="globe-ring globe-ring--h2"></div>
                    <div class="globe-ring globe-ring--h3"></div>

                    <!-- Pontos de dados pulsantes -->
                    <div class="globe-dot" style="top:28%; left:22%;"></div>
                    <div class="globe-dot globe-dot--emerald" style="top:55%; left:63%;"></div>
                    <div class="globe-dot globe-dot--indigo" style="top:40%; left:75%;"></div>
                    <div class="globe-dot" style="top:70%; left:38%;"></div>
                    <div class="globe-dot globe-dot--emerald" style="top:20%; left:58%;"></div>

                    <!-- Arco de conexão (SVG leve) -->
                    <svg class="globe-arc" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 80 130 Q 200 60 310 200" stroke="rgba(59,130,246,0.35)" stroke-width="1.5" fill="none" stroke-dasharray="6 4"/>
                        <path d="M 140 260 Q 260 180 340 140" stroke="rgba(16,185,129,0.35)" stroke-width="1.5" fill="none" stroke-dasharray="6 4"/>
                        <path d="M 60 200 Q 180 280 300 90"  stroke="rgba(99,102,241,0.3)"  stroke-width="1"   fill="none" stroke-dasharray="4 6"/>
                    </svg>
                </div>

                <!-- Anel orbital externo animado -->
                <div class="globe-orbit">
                    <div class="globe-orbit__dot"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Value Prop Bento Grid -->
    <section id="features" class="space-y-16">
        <div class="text-center space-y-4">
            <h2 class="text-zinc-500 font-black text-[12px] uppercase tracking-[0.3em]">Recursos de Elite</h2>
            <p class="text-4xl md:text-5xl font-black text-white tracking-tight">Potência em cada detalhe.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- IA Card -->
            <div class="bg-zinc-900/30 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 space-y-6 transition-all hover:border-blue-500/30 group">
                <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">🧠</div>
                <h3 class="text-2xl font-black text-white tracking-tight">NexNeural IA</h3>
                <p class="text-zinc-500 font-medium leading-relaxed italic">"Inteligência de Sobrecarga."</p>
                <p class="text-zinc-400 text-sm leading-relaxed">Algoritmos que analisam seu RPE histórico e sugerem cargas precisas para evitar estagnação.</p>
            </div>

            <!-- Clinic Card -->
            <div class="bg-zinc-900/30 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 shadow-2xl md:scale-105 relative z-20 space-y-6 transition-all hover:border-indigo-500/30 group">
                <div class="w-16 h-16 bg-indigo-600/10 rounded-2xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">🏥</div>
                <h3 class="text-2xl font-black text-white tracking-tight italic">Portal Clinical</h3>
                <p class="text-zinc-500 font-medium leading-relaxed italic">"Gestão em Escala."</p>
                <p class="text-zinc-400 text-sm leading-relaxed">Prontuários eletrônicos, compartilhamento de laudos e gestão de múltiplos profissionais em um painel B2B robusto.</p>
            </div>

            <!-- Bio Card -->
            <div class="bg-zinc-900/30 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 space-y-6 transition-all hover:border-emerald-500/30 group">
                <div class="w-16 h-16 bg-emerald-600/10 rounded-2xl flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">🧬</div>
                <h3 class="text-2xl font-black text-white tracking-tight">Bio-Tracking</h3>
                <p class="text-zinc-500 font-medium leading-relaxed italic">"Ciência do Corpo."</p>
                <p class="text-zinc-400 text-sm leading-relaxed">Mapeamento de dobras cutâneas, bioimpedância e integração de exames para uma visão 360º da evolução.</p>
            </div>
        </div>
    </section>

    <!-- Pricing Comparison Section -->
    <section id="pricing" class="space-y-20 relative">
        <div class="absolute -bottom-40 right-0 w-[600px] h-[600px] bg-emerald-600/5 blur-[150px] rounded-full pointer-events-none"></div>

        <div class="text-center space-y-4">
            <h2 class="text-5xl font-black text-white tracking-tight">Escolha seu Nível de Evolução</h2>
            <p class="text-zinc-500 font-medium">Soluções flexíveis para indivíduos, profissionais e organizações de saúde.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Free / Student -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 space-y-8 flex flex-col hover:bg-zinc-900/60 transition-all">
                <div class="space-y-2">
                    <span class="text-zinc-500 font-black text-[10px] uppercase tracking-widest">ALUNO STANDARD</span>
                    <div class="text-4xl font-black text-white">Grátis</div>
                </div>
                <ul class="space-y-4 flex-1">
                    @foreach(['Diário de Treino & Cargas', 'Registro Nutricional Básico', 'Gráficos de Peso e Medidas', 'Comunidade Global'] as $f)
                    <li class="flex items-center gap-3 text-zinc-400 text-sm font-medium">
                        <i class="fas fa-check text-zinc-700"></i> {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('onboarding.welcome') }}" class="block w-full py-4 text-center bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all active:scale-95">COMEÇAR AGORA</a>
            </div>

            <!-- Pro / Professional -->
            <div class="bg-gradient-to-b from-blue-600/10 to-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3rem] border border-blue-500/30 space-y-8 flex flex-col relative overflow-hidden shadow-2xl scale-105 z-10">
                <div class="absolute top-0 right-0 bg-blue-600 text-white text-[9px] font-black px-6 py-2 rounded-bl-3xl uppercase tracking-widest shadow-xl">Mais Popular</div>
                <div class="space-y-2">
                    <span class="text-blue-400 font-black text-[10px] uppercase tracking-widest">NEXELITE PRO</span>
                    <div class="text-4xl font-black text-white">R$ 19,90 <span class="text-zinc-500 text-sm font-medium">/mês</span></div>
                </div>
                <ul class="space-y-4 flex-1">
                    @foreach(['NexNeural IA (Sugestão de Cargas)', 'NexHydra (Curva Metabólica)', 'Relatórios PDF Profissionais', 'Chat com IA Especialista', 'Acesso Prioritário a Recursos'] as $f)
                    <li class="flex items-center gap-3 text-blue-100 text-sm font-bold">
                        <i class="fas fa-crown text-amber-500"></i> {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-4 text-center bg-white text-zinc-950 font-black rounded-2xl hover:bg-blue-400 hover:text-white transition-all shadow-xl active:scale-95">ASSINAR PRO</a>
            </div>

            <!-- Clinic / Business -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3rem] border border-emerald-500/20 space-y-8 flex flex-col hover:bg-zinc-900/60 transition-all">
                <div class="space-y-2">
                    <span class="text-emerald-500 font-black text-[10px] uppercase tracking-widest">CLÍNICAS & STUDIOS</span>
                    <div class="text-4xl font-black text-white">Consulte</div>
                </div>
                <ul class="space-y-4 flex-1">
                    @foreach(['Multi-profissionais ilimitados', 'Gestão de Pacientes Centralizada', 'Prontuário Digital LGPD Ready', 'Branding Personalizado (Logo)', 'Dashboard Financeiro & B2B'] as $f)
                    <li class="flex items-center gap-3 text-emerald-400/80 text-sm font-bold">
                        <i class="fas fa-building text-emerald-600"></i> {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="https://wa.me/seu-numero" target="_blank" class="block w-full py-4 text-center bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all active:scale-95">FALAR COM VENDAS</a>
            </div>
        </div>
    </section>

    <!-- Clinic Proof / B2B Section -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center bg-zinc-900/20 rounded-[4rem] p-12 md:p-20 border border-white/5 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 blur-[100px] rounded-full"></div>
        
        <div class="space-y-8 relative z-10">
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-none">
                Sua Clínica no Próximo Nível Digital.
            </h2>
            <p class="text-zinc-500 text-lg leading-relaxed font-medium">
                O NexShape Business foi desenhado para clínicas que não aceitam o comum. Centralize o histórico de seus pacientes, gere laudos automatizados e tenha controle total sobre sua equipe técnica em uma interface state-of-the-art.
            </p>
            <div class="grid grid-cols-2 gap-6">
                <div class="p-6 bg-zinc-900/50 rounded-3xl border border-white/5">
                    <div class="text-3xl font-black text-white mb-1">+40%</div>
                    <div class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Produtividade</div>
                </div>
                <div class="p-6 bg-zinc-900/50 rounded-3xl border border-white/5">
                    <div class="text-3xl font-black text-white mb-1">100%</div>
                    <div class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Seguro (LGPD)</div>
                </div>
            </div>
        </div>
        
        <div class="relative">
            <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/20 to-emerald-500/20 blur-2xl rounded-full opacity-50"></div>
            <div class="bg-zinc-950 p-4 rounded-[2.5rem] shadow-2xl border border-white/10">
                 <div class="aspect-video bg-zinc-900 rounded-[2rem] overflow-hidden flex items-center justify-center">
                    <span class="text-zinc-700 font-black text-sm tracking-widest uppercase italic">Painel Administrativo B2B</span>
                 </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="text-center py-24 bg-gradient-to-br from-blue-600 via-indigo-600 to-emerald-600 rounded-[4rem] shadow-2xl shadow-blue-500/20 relative overflow-hidden group">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-white/10 blur-[100px] rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
        
        <div class="relative z-10 space-y-10 px-10">
            <h2 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-[0.9]">Sua revolução estética <br> começa por aqui.</h2>
            <p class="text-white/80 text-xl font-medium max-w-2xl mx-auto">Junte-se a milhares de usuários que já utilizam a NexShape para hackear seus resultados.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-12 py-6 bg-zinc-950 text-white font-black rounded-3xl hover:bg-white hover:text-zinc-950 transition-all active:scale-95 shadow-2xl text-lg">
                    CRIAR CONTA AGORA
                </a>
                <a href="#pricing" class="px-12 py-6 bg-white/10 backdrop-blur-md text-white font-black rounded-3xl hover:bg-white/20 transition-all active:scale-95 text-lg">
                    VER TODOS OS PLANOS
                </a>
            </div>
        </div>
    </section>
</div>

{{-- CSS 3D Globe: zero dependências externas --}}

<style>
    body { background-color: #0b0e14; scroll-behavior: smooth; }

    /* ─── Fade-in ─────────────────────────────────────── */
    .animate-fade-in { animation: fadeIn 1.2s cubic-bezier(0.16,1,0.3,1) forwards; }
    @keyframes fadeIn {
        from { opacity:0; transform:translateY(30px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .animate-bounce-slow { animation: bounceSlow 4s ease-in-out infinite; }
    @keyframes bounceSlow {
        0%,100% { transform:translateY(0); }
        50%     { transform:translateY(-8px); }
    }

    /* ─── Custom Scrollbar ────────────────────────────── */
    ::-webkit-scrollbar { width:8px; }
    ::-webkit-scrollbar-track  { background:#0b0e14; }
    ::-webkit-scrollbar-thumb  { background:#1f2937; border-radius:10px; }
    ::-webkit-scrollbar-thumb:hover { background:#374151; }

    /* ════════════════════════════════════════════════════
       CSS 3D GLOBE — zero JS, zero CDN externo
    ════════════════════════════════════════════════════ */
    .globe-wrapper {
        position: relative;
        width:  320px;
        height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 640px) {
        .globe-wrapper { width:220px; height:220px; }
    }

    /* Atmosfera brilhante */
    .globe-atmosphere {
        position: absolute;
        inset: -30px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 35%,
            rgba(59,130,246,0.18) 0%,
            rgba(99,102,241,0.10) 40%,
            transparent 70%);
        filter: blur(18px);
        animation: atmospherePulse 6s ease-in-out infinite;
    }
    @keyframes atmospherePulse {
        0%,100% { opacity:.7; transform:scale(1); }
        50%     { opacity:1;  transform:scale(1.04); }
    }

    /* Corpo principal do globo */
    .globe-sphere {
        position: relative;
        width:  100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        background:
            /* Destaque de luz superior-esquerda */
            radial-gradient(ellipse at 30% 25%, rgba(99,130,255,0.25) 0%, transparent 55%),
            /* Base escura do planeta */
            radial-gradient(circle at 50% 50%, #0d1424 0%, #060c18 70%, #020508 100%);
        border: 1px solid rgba(59,130,246,0.18);
        box-shadow:
            inset -40px -20px 80px rgba(0,0,0,0.8),
            inset  20px  10px 40px rgba(59,130,246,0.08),
            0 0 60px rgba(59,130,246,0.12),
            0 0 120px rgba(99,102,241,0.06);
        animation: globeSpin 22s linear infinite;
    }
    @keyframes globeSpin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    /* Sombra interna de profundidade (lateral direita) */
    .globe-depth {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: radial-gradient(ellipse at 80% 60%,
            rgba(0,0,0,0.6) 0%,
            transparent 60%);
        pointer-events: none;
        z-index: 4;
    }

    /* ─── Anéis de grade ─────────────────────────────── */
    .globe-ring {
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(59,130,246,0.12);
        pointer-events: none;
    }
    /* Meridianos (verticais — perspectiva comprimida horizontalmente) */
    .globe-ring--v1 { inset:0;     transform:scaleX(0.15); }
    .globe-ring--v2 { inset:0;     transform:scaleX(0.45); }
    .globe-ring--v3 { inset:0;     transform:scaleX(0.75); }
    /* Paralelos (horizontais — perspectiva comprimida verticalmente) */
    .globe-ring--h1 { inset:15%;   transform:scaleY(0.25); border-color:rgba(59,130,246,0.10); }
    .globe-ring--h2 { inset:32%;   transform:scaleY(0.22); border-color:rgba(59,130,246,0.08); }
    .globe-ring--h3 { inset:5%;    transform:scaleY(0.15); border-color:rgba(59,130,246,0.07); }

    /* ─── Pontos de dados pulsantes ──────────────────── */
    .globe-dot {
        position: absolute;
        width:  8px;
        height: 8px;
        border-radius: 50%;
        background: #3b82f6;
        box-shadow: 0 0 8px 3px rgba(59,130,246,0.6);
        z-index: 5;
        animation: dotPulse 2.4s ease-in-out infinite;
    }
    .globe-dot--emerald {
        background: #10b981;
        box-shadow: 0 0 8px 3px rgba(16,185,129,0.6);
        animation-delay: .8s;
    }
    .globe-dot--indigo {
        background: #6366f1;
        box-shadow: 0 0 8px 3px rgba(99,102,241,0.6);
        animation-delay: 1.6s;
    }
    @keyframes dotPulse {
        0%,100% { transform:scale(1);   opacity:1; }
        50%     { transform:scale(1.8); opacity:.5; }
    }

    /* ─── SVG de arcos ───────────────────────────────── */
    .globe-arc {
        position: absolute;
        inset: 0;
        width:  100%;
        height: 100%;
        z-index: 3;
        opacity: .7;
    }

    /* ─── Anel orbital externo ───────────────────────── */
    .globe-orbit {
        position: absolute;
        inset: -30px;
        border-radius: 50%;
        border: 1px dashed rgba(59,130,246,0.20);
        animation: orbitSpin 18s linear infinite;
    }
    @keyframes orbitSpin {
        from { transform:rotate(0deg)   scaleY(0.35); }
        to   { transform:rotate(360deg) scaleY(0.35); }
    }
    .globe-orbit__dot {
        position: absolute;
        top: -4px;
        left: 50%;
        transform: translateX(-50%);
        width:  8px;
        height: 8px;
        border-radius: 50%;
        background: #3b82f6;
        box-shadow: 0 0 10px 3px rgba(59,130,246,0.8);
    }
</style>
@endsection
