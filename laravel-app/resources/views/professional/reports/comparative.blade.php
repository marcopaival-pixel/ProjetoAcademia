@extends('layouts.professional')

@section('title', 'Análise Comparativa — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-indigo-500 hover:border-indigo-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-600/20">
                 <i class="fas fa-balance-scale text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Análise <span class="text-indigo-500">Comparativa</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Crescimento Mensal • Este Mês vs Mês Anterior
                </p>
            </div>
        </div>
    </div>

    <!-- Comparison Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Students Growth -->
        <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/10 blur-[60px] rounded-full"></div>
            <div class="flex justify-between items-start mb-10">
                <div>
                    <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] block mb-2">Base de {{ $patientsLabel }}</span>
                    <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Crescimento da Carteira</h3>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-4xl font-black {{ $data['delta']['students'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} italic">
                        {{ $data['delta']['students'] >= 0 ? '+' : '' }}{{ $data['delta']['students'] }}%
                    </span>
                    <span class="text-[9px] text-zinc-700 font-black uppercase tracking-widest mt-1">Var. Mensal</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Este Mês</span>
                    <span class="text-3xl font-black text-white italic tracking-tighter">{{ $data['current']['students'] }}</span>
                    <p class="text-[8px] text-zinc-700 font-bold uppercase mt-2">{{ $patientsLabel }} Ativos</p>
                </div>
                <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Mês Anterior</span>
                    <span class="text-3xl font-black text-zinc-500 italic tracking-tighter">{{ $data['previous']['students'] }}</span>
                    <p class="text-[8px] text-zinc-700 font-bold uppercase mt-2">Fechamento</p>
                </div>
            </div>
        </div>

        <!-- Training Volume -->
        <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/10 blur-[60px] rounded-full"></div>
            <div class="flex justify-between items-start mb-10">
                <div>
                    <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] block mb-2">Volume de Treinos</span>
                    <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Engajamento Coletivo</h3>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-4xl font-black {{ $data['delta']['workouts'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }} italic">
                        {{ $data['delta']['workouts'] >= 0 ? '+' : '' }}{{ $data['delta']['workouts'] }}%
                    </span>
                    <span class="text-[9px] text-zinc-700 font-black uppercase tracking-widest mt-1">Var. Mensal</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Este Mês</span>
                    <span class="text-3xl font-black text-white italic tracking-tighter">{{ $data['current']['workouts'] }}</span>
                    <p class="text-[8px] text-zinc-700 font-bold uppercase mt-2">Sessões Totais</p>
                </div>
                <div class="bg-zinc-950/50 p-6 rounded-3xl border border-white/5">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Mês Anterior</span>
                    <span class="text-3xl font-black text-zinc-500 italic tracking-tighter">{{ $data['previous']['workouts'] }}</span>
                    <p class="text-[8px] text-zinc-700 font-bold uppercase mt-2">Sessões Totais</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights Card -->
    <div class="bg-gradient-to-br from-indigo-900/20 to-purple-900/20 border border-indigo-500/20 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden">
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/10 blur-[120px] rounded-full"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-12">
            <div class="w-32 h-32 rounded-[2.5rem] bg-indigo-600 flex items-center justify-center text-white shadow-2xl shadow-indigo-600/40 shrink-0">
                <i class="fas fa-lightbulb text-5xl"></i>
            </div>
            <div>
                <h4 class="text-3xl font-black text-white italic uppercase tracking-tighter mb-4">Insights da <span class="text-indigo-400">NexShape AI</span></h4>
                <p class="text-zinc-400 text-lg leading-relaxed max-w-3xl font-medium">
                    Seu volume de treinos teve uma variação de <span class="text-white font-black">{{ $data['delta']['workouts'] }}%</span> em relação ao mês passado. 
                    @if($data['delta']['workouts'] > 0)
                        O engajamento está em curva ascendente, sugerindo alta retenção e satisfação dos {{ mb_strtolower($patientsLabel) }} com as prescrições atuais.
                    @else
                        Houve uma leve queda no volume total. Recomendamos revisar os planos de treino dos {{ mb_strtolower($patientsLabel) }} menos ativos para evitar possíveis churns.
                    @endif
                </p>
                <div class="mt-8 flex gap-4">
                    <a href="{{ route('professional.reports.show', ['type' => 'complete_analytics']) }}" class="px-8 py-3 bg-white text-zinc-950 text-[11px] font-black rounded-2xl uppercase tracking-widest hover:bg-zinc-200 transition-all shadow-xl">Ver Detalhes por {{ $patientLabel }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection



