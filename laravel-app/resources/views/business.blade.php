@extends('layouts.app')

@section('title', 'NexShape Business — Gestão Clínica de Elite')

@section('content')
<div class="space-y-32 py-10 px-6 max-w-[1400px] mx-auto overflow-hidden font-['Outfit']">
    
    <!-- Hero Section -->
    <section class="relative flex flex-col lg:flex-row items-center gap-20 py-20 animate-fade-in">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1000px] h-[600px] bg-blue-600/5 blur-[150px] rounded-full pointer-events-none"></div>
        
        <div class="flex-1 space-y-10 text-center lg:text-left relative z-20">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest">
                <i data-lucide="building-2" class="w-4 h-4"></i>
                Solução B2B de Alta Performance
            </div>

            <h1 class="text-6xl md:text-8xl font-black text-white tracking-tighter leading-[0.85]">
                A Inteligência que sua <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-blue-500 to-emerald-400">Clínica Merece.</span>
            </h1>
            
            <p class="text-zinc-500 text-xl font-medium max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                Centralize o histórico de seus pacientes, gerencie sua equipe técnica e gere laudos automatizados com a tecnologia que está revolucionando o mercado de saúde e fitness.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-6 pt-6">
                <a href="#pricing" class="group relative px-10 py-5 bg-white text-zinc-950 font-black rounded-[2rem] hover:bg-blue-600 hover:text-white transition-all shadow-2xl active:scale-95">
                    <span class="relative z-10">ESCOLHER PLANO BUSINESS</span>
                </a>
                <a href="{{ route('demo.start') }}" class="px-10 py-5 bg-zinc-900/50 backdrop-blur-xl border border-white/5 text-white font-black rounded-[2rem] hover:bg-zinc-800 transition-all active:scale-95 flex items-center gap-2">
                    <i data-lucide="play-circle" class="w-5 h-5 text-blue-400"></i>
                    VER DEMONSTRAÇÃO
                </a>
            </div>
        </div>

        <div class="flex-1 relative">
            <div class="absolute -inset-10 bg-blue-500/10 blur-[100px] rounded-full opacity-50"></div>
            <div class="bg-zinc-900 p-3 rounded-[3rem] border border-white/10 shadow-3xl transform lg:rotate-3 hover:rotate-0 transition-transform duration-700">
                <img src="{{ asset('images/b2b-preview.png') }}" alt="NexShape Business Dashboard" class="rounded-[2.5rem] w-full shadow-2xl">
            </div>
        </div>
    </section>

    <!-- Key Metrics -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="p-10 bg-zinc-900/40 backdrop-blur-3xl rounded-[3rem] border border-white/5 space-y-6 group hover:border-blue-500/30 transition-all">
            <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                <i data-lucide="trending-up" class="w-8 h-8"></i>
            </div>
            <h3 class="text-3xl font-black text-white italic tracking-tighter uppercase">+40% Produtividade</h3>
            <p class="text-zinc-500 font-medium leading-relaxed">Otimize o tempo de sua equipe técnica com ferramentas de prescrição inteligente e automação de laudos.</p>
        </div>
        <div class="p-10 bg-zinc-900/40 backdrop-blur-3xl rounded-[3rem] border border-white/5 space-y-6 group hover:border-emerald-500/30 transition-all">
            <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500">
                <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            <h3 class="text-3xl font-black text-white italic tracking-tighter uppercase">100% Seguro (LGPD)</h3>
            <p class="text-zinc-500 font-medium leading-relaxed">Dados de pacientes protegidos com criptografia de ponta a ponta e conformidade total com a legislação.</p>
        </div>
        <div class="p-10 bg-zinc-900/40 backdrop-blur-3xl rounded-[3rem] border border-white/5 space-y-6 group hover:border-indigo-500/30 transition-all">
            <div class="w-16 h-16 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500">
                <i data-lucide="users" class="w-8 h-8"></i>
            </div>
            <h3 class="text-3xl font-black text-white italic tracking-tighter uppercaseEscalabilidade Geriátrica</h3>
            <p class="text-zinc-500 font-medium leading-relaxed">Gerencie desde pequenas clínicas até grandes redes com gestão multi-unidade e controle de acesso.</p>
        </div>
    </section>

    <!-- Features Deep Dive -->
    <section class="space-y-16">
        <div class="text-center space-y-4">
            <h2 class="text-4xl md:text-6xl font-black text-white tracking-tighter uppercase italic">O Ecossistema <span class="text-blue-500">Completo.</span></h2>
            <p class="text-zinc-500 font-medium text-lg">Tudo o que sua clínica precisa em uma única plataforma neural.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-zinc-900/20 p-10 rounded-[3rem] border border-white/5 flex items-start gap-8">
                <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center text-blue-500 border border-white/5 shrink-0">
                    <i data-lucide="database" class="w-8 h-8"></i>
                </div>
                <div class="space-y-2">
                    <h4 class="text-xl font-black text-white uppercase italic">Prontuário Digital Unificado</h4>
                    <p class="text-zinc-500 text-sm leading-relaxed">Histórico completo de avaliações, treinos e nutrição de cada paciente acessível em segundos.</p>
                </div>
            </div>
            <div class="bg-zinc-900/20 p-10 rounded-[3rem] border border-white/5 flex items-start gap-8">
                <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center text-emerald-500 border border-white/5 shrink-0">
                    <i data-lucide="file-text" class="w-8 h-8"></i>
                </div>
                <div class="space-y-2">
                    <h4 class="text-xl font-black text-white uppercase italic">Laudos Automatizados</h4>
                    <p class="text-zinc-500 text-sm leading-relaxed">Gere relatórios profissionais em PDF com gráficos de evolução automática para seus clientes.</p>
                </div>
            </div>
            <div class="bg-zinc-900/20 p-10 rounded-[3rem] border border-white/5 flex items-start gap-8">
                <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center text-amber-500 border border-white/5 shrink-0">
                    <i data-lucide="users-2" class="w-8 h-8"></i>
                </div>
                <div class="space-y-2">
                    <h4 class="text-xl font-black text-white uppercase italic">Gestão de Equipe</h4>
                    <p class="text-zinc-500 text-sm leading-relaxed">Atribua pacientes a profissionais específicos e monitore o engajamento da base em tempo real.</p>
                </div>
            </div>
            <div class="bg-zinc-900/20 p-10 rounded-[3rem] border border-white/5 flex items-start gap-8">
                <div class="w-16 h-16 bg-zinc-950 rounded-2xl flex items-center justify-center text-purple-500 border border-white/5 shrink-0">
                    <i data-lucide="palette" class="w-8 h-8"></i>
                </div>
                <div class="space-y-2">
                    <h4 class="text-xl font-black text-white uppercase italic">White Label (Branding)</h4>
                    <p class="text-zinc-500 text-sm leading-relaxed">Personalize o portal com a identidade visual de sua clínica para uma experiência de marca única.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="space-y-20 py-20 relative">
        <div class="absolute -bottom-40 right-1/2 translate-x-1/2 w-[800px] h-[800px] bg-blue-600/5 blur-[150px] rounded-full pointer-events-none"></div>

        <div class="text-center space-y-6 max-w-3xl mx-auto">
            <h2 class="text-5xl md:text-6xl font-black text-white tracking-tighter uppercase italic">Planos <span class="text-blue-500">Business</span></h2>
            <p class="text-zinc-500 font-medium text-lg">Selecione a escala ideal para o seu negócio.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($plans as $plan)
            <div class="bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[3.5rem] border border-white/5 space-y-8 flex flex-col hover:bg-zinc-900/60 transition-all group relative overflow-hidden">
                <div class="space-y-3">
                    <span class="text-blue-500 font-black text-[10px] uppercase tracking-[0.3em] italic">NEX BUSINESS</span>
                    <h3 class="text-4xl font-black text-white italic tracking-tighter">{{ $plan->name }}</h3>
                    <p class="text-zinc-500 text-sm font-medium leading-relaxed italic">{{ Str::limit($plan->description, 120) }}</p>
                </div>

                <div class="flex items-baseline gap-2">
                    <span class="text-zinc-700 text-xs font-black uppercase">R$</span>
                    <span class="text-6xl font-black text-white tracking-tighter">{{ number_format($plan->price, 2, ',', '.') }}</span>
                    <span class="text-zinc-700 text-[10px] font-black uppercase tracking-widest">/mês</span>
                </div>

                <ul class="space-y-4 flex-1">
                    @foreach($plan->planFeatures->take(6) as $feature)
                    <li class="flex items-center gap-3 text-zinc-400 text-sm font-medium italic">
                        <i data-lucide="check" class="w-4 h-4 text-blue-500"></i>
                        {{ str_replace('_', ' ', ucfirst($feature->feature_key)) }}
                    </li>
                    @endforeach
                </ul>

                <a href="{{ route('checkout.index', $plan->id) }}" class="block w-full py-6 text-center bg-white text-zinc-950 font-black rounded-3xl hover:bg-blue-600 hover:text-white transition-all active:scale-95 shadow-2xl text-xs tracking-widest uppercase">
                    CRIAR CONTA BUSINESS
                </a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Final CTA -->
    <section class="text-center py-24 bg-gradient-to-br from-blue-700 via-blue-600 to-emerald-600 rounded-[4rem] shadow-3xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="relative z-10 space-y-10 px-10">
            <h2 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-[0.9]">Sua clínica, agora <br> em outro patamar.</h2>
            <p class="text-white/80 text-xl font-medium max-w-2xl mx-auto">Junte-se às clínicas de elite que já utilizam a NexShape para escalar seus resultados.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="px-12 py-6 bg-zinc-950 text-white font-black rounded-3xl hover:bg-white hover:text-zinc-950 transition-all active:scale-95 shadow-2xl text-lg">
                    AGENDAR CONSULTORIA
                </a>
                <a href="#pricing" class="px-12 py-6 bg-white/10 backdrop-blur-md text-white font-black rounded-3xl hover:bg-white/20 transition-all active:scale-95 text-lg">
                    VER TABELA DE PREÇOS
                </a>
            </div>
        </div>
    </section>
</div>

<style>
    body { background-color: #0b0e14; scroll-behavior: smooth; }
    .animate-fade-in { animation: fadeIn 1.2s cubic-bezier(0.16,1,0.3,1) forwards; }
    @keyframes fadeIn {
        from { opacity:0; transform:translateY(30px); }
        to   { opacity:1; transform:translateY(0); }
    }
</style>
@endsection
