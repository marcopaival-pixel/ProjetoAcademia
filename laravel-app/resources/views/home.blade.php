@extends('layouts.app')

@section('title', 'NEX SHAPE — Ecossistema de Elite para Treino e Nutrição')

@section('content')
<div class="space-y-32 py-10 px-6 max-w-[1400px] mx-auto overflow-hidden font-['Outfit']">
    
    <!-- Ultra Impact Hero -->
    <section class="relative flex flex-col lg:flex-row items-center gap-16 py-10 animate-fade-in">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-emerald-600/5 blur-[150px] rounded-full pointer-events-none"></div>
        
        <div class="flex-1 space-y-10 text-center lg:text-left relative z-20">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest animate-bounce-slow">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Próxima Geração de Inteligência Fitness
            </div>

            <h1 class="text-6xl md:text-8xl font-black text-white tracking-tighter leading-[0.85]">
                O Futuro da <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-300">Performance.</span>
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

    <!-- Value Prop: Bento Grid Section -->
    <section id="features" class="space-y-16 py-20 relative">
        <div class="absolute top-1/2 left-0 w-96 h-96 bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none"></div>
        
        <div class="text-center space-y-6 max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-zinc-900 border border-zinc-800 shadow-xl">
                <i data-lucide="layers" class="w-4 h-4 text-emerald-500"></i>
                <span class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Arquitetura de Alta Performance</span>
            </div>
            <h2 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-none italic uppercase">
                O Ecossistema <br> <span class="text-emerald-500 italic">Definitivo.</span>
            </h2>
            <p class="text-zinc-500 font-medium text-lg leading-relaxed italic">
                Integramos todas as camadas do treinamento e gestão clínica em uma única plataforma neural.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-6 grid-rows-2 gap-6 h-auto md:h-[800px]">
            <!-- IA Neural: Large Main Card -->
            <div class="md:col-span-3 md:row-span-2 bg-zinc-900/30 backdrop-blur-3xl p-10 rounded-[3.5rem] border border-white/5 flex flex-col justify-between group hover:border-emerald-500/30 transition-all duration-700 overflow-hidden relative">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
                
                <div class="space-y-6 relative z-10">
                    <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 shadow-inner">
                        <i data-lucide="brain-circuit" class="w-8 h-8"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-4xl font-black text-white uppercase italic tracking-tighter">NexNeural <span class="text-emerald-500">IA</span></h3>
                        <p class="text-zinc-400 font-medium leading-relaxed italic">Nossa inteligência proprietária analisa o seu RPE (Esforço Percebido) em tempo real para sugerir a carga exata da sua próxima série, otimizando o volume de treinamento e prevenindo o overtraining.</p>
                    </div>
                </div>

                <div class="mt-12 relative z-10 bg-zinc-950/50 border border-zinc-800 p-6 rounded-[2.5rem] backdrop-blur-md">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest italic">Otimização de Carga</span>
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-2 bg-zinc-900 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 w-3/4 animate-pulse"></div>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-tighter italic">Sugestão: Aumentar +2kg na próxima série (RPE 8.5 detectado)</p>
                    </div>
                </div>
            </div>

            <!-- CRM & Patients -->
            <div class="md:col-span-3 bg-zinc-900/30 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 flex flex-col justify-between group hover:border-blue-500/20 transition-all duration-700">
                <div class="flex items-start justify-between">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                            <i data-lucide="users"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase italic tracking-tighter">Gestão <span class="text-blue-500">Clinical</span></h3>
                        <p class="text-xs text-zinc-500 font-medium italic max-w-[200px]">CRM completo para clínicas, com prontuário digital e histórico evolutivo.</p>
                    </div>
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-zinc-900 bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-white">JD</div>
                        <div class="w-10 h-10 rounded-full border-2 border-zinc-900 bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-white">MA</div>
                        <div class="w-10 h-10 rounded-full border-2 border-zinc-900 bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-white">+12</div>
                    </div>
                </div>
            </div>

            <!-- Bio-Tracking -->
            <div class="md:col-span-1.5 bg-zinc-900/30 backdrop-blur-3xl p-8 rounded-[3rem] border border-white/5 flex flex-col gap-6 group hover:border-emerald-500/20 transition-all">
                <div class="w-12 h-12 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-lg font-black text-white uppercase italic tracking-tighter leading-none mb-2">Bio-Tracking</h4>
                    <p class="text-[10px] text-zinc-500 font-medium italic">Análise de bioimpedância e medidas antropométricas automatizadas.</p>
                </div>
            </div>

            <!-- Precision Nutrition -->
            <div class="md:col-span-1.5 bg-zinc-900/30 backdrop-blur-3xl p-8 rounded-[3rem] border border-white/5 flex flex-col gap-6 group hover:border-amber-500/20 transition-all">
                <div class="w-12 h-12 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-amber-500">
                    <i data-lucide="utensils" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-lg font-black text-white uppercase italic tracking-tighter leading-none mb-2">Nutrição</h4>
                    <p class="text-[10px] text-zinc-500 font-medium italic">Prescrição de dietas flexíveis e rígidas com cálculo de macronutrientes dinâmico.</p>
                </div>
            </div>

            <!-- PDF Reports -->
            <div class="md:col-span-3 bg-zinc-900/30 backdrop-blur-3xl p-8 rounded-[3rem] border border-white/5 flex items-center gap-8 group hover:border-indigo-500/20 transition-all">
                <div class="w-20 h-20 bg-indigo-500/10 rounded-[2rem] flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform">
                    <i data-lucide="file-text" class="w-10 h-10"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="text-xl font-black text-white uppercase italic tracking-tighter">Laudos de Elite</h4>
                    <p class="text-[10px] text-zinc-500 font-medium italic leading-relaxed">Gere relatórios PDF profissionais de evolução para seus alunos em segundos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Summary Section -->
    <section id="pricing" class="space-y-20 relative" x-data="{ 
        activeCategory: '{{ $preferredType ?? 'student' }}',
        categories: {
            student: 'Aluno',
            professional: 'Profissional',
            clinic: 'Clínica'
        }
    }">
        <div class="absolute -bottom-40 right-0 w-[600px] h-[600px] bg-emerald-600/5 blur-[150px] rounded-full pointer-events-none"></div>

        <div class="text-center space-y-6 max-w-3xl mx-auto">
            <h2 class="text-5xl md:text-6xl font-black text-white tracking-tighter uppercase italic">Escolha sua <span class="text-emerald-500">Jornada</span></h2>
            <p class="text-zinc-500 font-medium text-lg">Selecione seu perfil e descubra o plano ideal para sua evolução.</p>
            
            @if(!$preferredType)
            <!-- Category Switcher -->
            <div class="flex flex-wrap justify-center gap-4 mt-8 p-1.5 bg-zinc-900 border border-zinc-800 rounded-3xl max-w-lg mx-auto shadow-2xl">
                <template x-for="(label, key) in categories">
                    <button @click="activeCategory = key" 
                            :class="activeCategory === key ? 'bg-emerald-500 text-zinc-950' : 'text-zinc-500 hover:text-white'"
                            class="px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all"
                            x-text="label">
                    </button>
                </template>
            </div>
            @else
            <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                Recomendado para seu perfil: {{ $preferredType === 'student' ? 'Aluno' : ($preferredType === 'professional' ? 'Profissional' : 'Clínica') }}
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($summaryPlans as $type => $plan)
                @if($plan)
                <div x-show="activeCategory === '{{ $type }}'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="col-span-1 md:col-start-2 bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3rem] border border-white/5 space-y-8 flex flex-col hover:bg-zinc-900/60 transition-all group relative overflow-hidden"
                     :class="'{{ $plan->name }}'.includes('Premium') || '{{ $plan->name }}'.includes('Pro') ? 'border-emerald-500/20 shadow-[0_0_50px_rgba(16,185,129,0.05)]' : ''">
                    
                    @if(str_contains($plan->name, 'Premium') || str_contains($plan->name, 'Pro') || $plan->price > 100)
                        <div class="absolute top-0 right-0 bg-emerald-500 text-zinc-950 text-[9px] font-black px-6 py-2 rounded-bl-3xl uppercase tracking-widest shadow-xl">Mais Popular</div>
                    @endif

                    <div class="space-y-3">
                        <span class="text-emerald-500 font-black text-[10px] uppercase tracking-[0.3em] italic">{{ $plan->type === 'student' ? 'NEX ALUNO' : ($plan->type === 'professional' ? 'NEX PRO' : 'NEX BUSINESS') }}</span>
                        <h3 class="text-4xl font-black text-white italic tracking-tighter">{{ $plan->name }}</h3>
                        <p class="text-zinc-500 text-sm font-medium leading-relaxed italic">{{ Str::limit($plan->description, 100) }}</p>
                    </div>

                    <div class="flex items-baseline gap-2">
                        <span class="text-zinc-700 text-xs font-black uppercase">R$</span>
                        <span class="text-5xl font-black text-white tracking-tighter">{{ number_format($plan->price, 2, ',', '.') }}</span>
                        <span class="text-zinc-700 text-[10px] font-black uppercase tracking-widest">/mês</span>
                    </div>

                    <ul class="space-y-4 flex-1">
                        @foreach($plan->planFeatures->take(4) as $feature)
                        <li class="flex items-center gap-3 text-zinc-400 text-sm font-medium italic">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            {{ str_replace('_', ' ', ucfirst($feature->feature_key)) }}
                        </li>
                        @endforeach
                        @if($plan->planFeatures->count() > 4)
                        <li class="text-zinc-600 text-[10px] font-black uppercase tracking-widest pl-7 italic">+ {{ $plan->planFeatures->count() - 4 }} recursos integrados</li>
                        @endif
                    </ul>

                    <div class="flex flex-col gap-4">
                        <a href="{{ route('checkout.index', $plan->id) }}" class="block w-full py-5 text-center bg-white text-zinc-950 font-black rounded-3xl hover:bg-emerald-500 transition-all active:scale-95 shadow-2xl text-xs tracking-widest uppercase">
                            COMEÇAR AGORA
                        </a>
                        <a href="{{ route('plano') }}?type={{ $type }}" class="block w-full py-5 text-center bg-zinc-950 border border-zinc-800 text-zinc-500 hover:text-white transition-all rounded-3xl text-xs font-black tracking-widest uppercase italic">
                            VER DETALHES COMPLETOS
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        @if(!$preferredType)
        <div class="text-center pt-10">
            <p class="text-zinc-600 text-xs font-black uppercase tracking-[0.2em]">Não encontrou o que procurava? <a href="{{ route('plano') }}" class="text-emerald-500 hover:underline">Ver tabela comparativa completa</a></p>
        </div>
        @endif
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
