@extends('layouts.app')

@section('title', 'Escolha seu Plano — NexShape')

@section('content')
<div class="py-20 px-6 max-w-7xl mx-auto space-y-16 animate-fade-in">
    <!-- Header Strategico -->
    <div class="text-center space-y-4 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-widest">
            <i class="fas fa-crown text-[10px]"></i>
            Upgrade Premium
        </div>
        <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-tight">
            Transforme seus resultados com a <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400">NexShape Premium</span>
        </h1>
        <p class="text-zinc-500 text-xl font-medium">
            Escolha o plano que melhor se adapta aos seus objetivos e desbloqueie o poder da nossa IA para acelerar sua evolução.
        </p>
    </div>

    <!-- Cards de Planos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($allPlans as $plan)
            @php
                $isRecommended = str_contains(strtolower($plan->name), 'anual') || str_contains(strtolower($plan->name), 'premium');
                $priceLabel = str_contains(strtolower($plan->name), 'anual') ? 'por mês' : 'por mês';
                $isAnnual = str_contains(strtolower($plan->name), 'anual');
            @endphp
            
            <div class="relative group">
                @if($isRecommended)
                    <div class="absolute -top-5 left-1/2 -translate-x-1/2 z-20">
                        <span class="bg-emerald-500 text-black text-[10px] font-black uppercase tracking-[0.2em] px-4 py-2 rounded-full shadow-xl shadow-emerald-500/20">
                            Mais Popular
                        </span>
                    </div>
                @endif

                <div class="h-full bg-zinc-900/40 backdrop-blur-3xl border {{ $isRecommended ? 'border-emerald-500/50 shadow-emerald-500/10' : 'border-white/5' }} rounded-[2.5rem] p-10 flex flex-col justify-between transition-all duration-500 hover:scale-[1.02] hover:bg-zinc-900/60 shadow-2xl relative overflow-hidden">
                    @if($isRecommended)
                        <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    @endif

                    <div class="relative z-10 space-y-8">
                        <div class="space-y-2">
                            <h3 class="text-2xl font-black text-white tracking-tight">{{ $plan->name }}</h3>
                            <p class="text-zinc-500 text-sm">Ideal para {{ $isAnnual ? 'quem busca máxima economia e consistência' : 'quem quer começar sua jornada' }}.</p>
                        </div>

                        <div class="flex items-baseline gap-1">
                            <span class="text-white text-lg font-bold">R$</span>
                            <span class="text-5xl font-black text-white tracking-tighter">{{ number_format($plan->price, 2, ',', '.') }}</span>
                            <span class="text-zinc-500 text-sm font-bold">/mês</span>
                        </div>

                        @if($isAnnual)
                            <div class="bg-emerald-500/10 border border-emerald-500/20 py-2 px-4 rounded-xl text-emerald-400 text-[10px] font-black uppercase tracking-widest text-center">
                                Economize 20% ao ano
                            </div>
                        @endif

                        <ul class="space-y-4">
                            @php
                                $benefits = [
                                    'Acesso ao Performance HUB',
                                    'NexIntelligence AI Reports',
                                    'Planos de Treino Ilimitados',
                                    'Suporte Prioritário 24/7',
                                    'Análise Corporal por Foto',
                                    'Sem Taxas de Matrícula'
                                ];
                            @endphp
                            @foreach($benefits as $benefit)
                                <li class="flex items-center gap-3 text-zinc-400 text-sm">
                                    <i class="fas fa-check-circle text-emerald-500/50 group-hover:text-emerald-500 transition-colors"></i>
                                    {{ $benefit }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="relative z-10 pt-10">
                        <a href="{{ route('patient.subscription.checkout', $plan->id) }}" class="block w-full py-5 {{ $isRecommended ? 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/20' : 'bg-zinc-800 hover:bg-zinc-700' }} text-white font-black rounded-2xl text-center transition-all shadow-xl uppercase tracking-widest text-xs">
                            Assinar Agora
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Trust Badges -->
    <div class="pt-12 border-t border-white/5 flex flex-wrap justify-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all duration-700">
        <div class="flex items-center gap-3">
            <i class="fas fa-shield-alt text-2xl text-white"></i>
            <span class="text-white font-black text-[10px] uppercase tracking-widest">Pagamento 100% Seguro</span>
        </div>
        <div class="flex items-center gap-3">
            <i class="fas fa-calendar-check text-2xl text-white"></i>
            <span class="text-white font-black text-[10px] uppercase tracking-widest">7 Dias de Garantia</span>
        </div>
        <div class="flex items-center gap-3">
            <i class="fas fa-headset text-2xl text-white"></i>
            <span class="text-white font-black text-[10px] uppercase tracking-widest">Suporte Especializado</span>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
