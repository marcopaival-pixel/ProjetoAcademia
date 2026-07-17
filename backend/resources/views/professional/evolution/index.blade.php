@extends('layouts.app')

@section('title', 'Hub de Evolução IA — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up mx-auto px-4 md:px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-8 border-b border-white/5">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">Análise de Base Ativa</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">NexSense Evolution Engine</span>
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-white leading-none">
                Hub de <span class="text-emerald-500 italic">Evolução Inteligente</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-2xl">Acompanhe a saúde global da sua base de pacientes através de algoritmos preditivos e análise de risco em tempo real.</p>
        </div>
    </div>

    <!-- Global Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Health Index -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 blur-3xl rounded-full transition-all group-hover:scale-125"></div>
            <div class="relative z-10">
                <h3 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-6">Índice de Saúde Global</h3>
                <div class="flex items-baseline gap-4">
                    <span class="text-7xl font-black text-white italic tracking-tighter">{{ round($avgHealthScore) }}%</span>
                    <span class="text-sm font-bold text-emerald-400 uppercase tracking-widest">Excelente</span>
                </div>
                <div class="h-2 w-full bg-zinc-950 rounded-full mt-6 border border-white/5 overflow-hidden">
                    <div class="h-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)] transition-all duration-1000" style="width: {{ $avgHealthScore }}%"></div>
                </div>
            </div>
        </div>

        <!-- Risk Counter -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-rose-500/10 blur-3xl rounded-full transition-all group-hover:scale-125"></div>
            <div class="relative z-10">
                <h3 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-6">Pacientes em Risco</h3>
                <div class="flex items-baseline gap-4">
                    <span class="text-7xl font-black text-white italic tracking-tighter">{{ $riskPatients->count() }}</span>
                    <span class="text-sm font-bold text-rose-400 uppercase tracking-widest">Ações Pendentes</span>
                </div>
                <p class="text-xs text-zinc-500 mt-4 font-medium italic">Baseado em platôs e scores críticos detectados pelo NexBot.</p>
            </div>
        </div>

        <!-- Total Base -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 blur-3xl rounded-full transition-all group-hover:scale-125"></div>
            <div class="relative z-10">
                <h3 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-6">Total de Alunos</h3>
                <div class="flex items-baseline gap-4">
                    <span class="text-7xl font-black text-white italic tracking-tighter">{{ $patients->count() }}</span>
                    <span class="text-sm font-bold text-blue-400 uppercase tracking-widest">Vínculos Ativos</span>
                </div>
                <a href="{{ route('professional.patients.index') }}" class="inline-block mt-4 text-[10px] font-black text-blue-400 uppercase tracking-widest hover:underline">Gerenciar Base &rarr;</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Mapa de Risco & Alertas -->
        <div class="lg:col-span-8 space-y-10">
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[4rem] p-10 shadow-2xl">
                <h3 class="text-2xl font-black text-white mb-10 flex items-center gap-3 uppercase tracking-tighter italic">
                    <i data-lucide="shield-alert" class="text-rose-500 w-8 h-8"></i>
                    Fila de Atenção Crítica
                </h3>
                
                <div class="space-y-6">
                    @forelse($riskPatients as $rp)
                        <div class="flex items-center gap-6 p-6 bg-rose-500/5 rounded-3xl border border-rose-500/10 hover:bg-rose-500/10 transition-all group">
                            <div class="w-16 h-16 rounded-2xl bg-zinc-950 flex items-center justify-center text-rose-500 font-black text-xl border border-rose-500/20 shadow-2xl">
                                {{ mb_substr($rp->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-white font-black text-xl">{{ $rp->name }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Health Score: {{ $rp->health_score }}%</span>
                                    <span class="w-1 h-1 rounded-full bg-zinc-700"></span>
                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Alvo: {{ $rp->profile?->goal }}</span>
                                </div>
                            </div>
                            <a href="{{ route('professional.patients.show', $rp->id) }}" class="px-6 py-3 bg-zinc-950 text-white font-black rounded-xl hover:bg-rose-600 transition-all text-[10px] uppercase tracking-widest shadow-xl">Intervir</a>
                        </div>
                    @empty
                        <div class="py-20 text-center border-2 border-dashed border-zinc-800 rounded-[3rem]">
                            <i data-lucide="check-circle" class="w-12 h-12 text-zinc-800 mx-auto mb-4"></i>
                            <h4 class="text-zinc-600 font-black text-lg">Toda a base está operando em níveis seguros.</h4>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Últimas Análises de IA -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[4rem] p-10 shadow-2xl">
                <h3 class="text-2xl font-black text-white mb-10 flex items-center gap-3 uppercase tracking-tighter italic">
                    <i data-lucide="brain-circuit" class="text-emerald-500 w-8 h-8"></i>
                    Recém Processados pelo NexBot
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($recentAssessments as $ra)
                        <div class="p-6 bg-zinc-950 rounded-[2.5rem] border border-zinc-800 hover:border-emerald-500/20 transition-all group">
                            <div class="flex justify-between items-start mb-6">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                                    <i data-lucide="zap" class="w-5 h-5"></i>
                                </div>
                                <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">{{ $ra->assessment_date->diffForHumans() }}</span>
                            </div>
                            <h4 class="text-white font-black text-lg mb-2 leading-tight">{{ $ra->user->name }}</h4>
                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-6">Novo Plano Gerado</p>
                            
                            <div class="flex items-center gap-4 pt-6 border-t border-zinc-900">
                                <div class="text-center">
                                    <p class="text-[8px] text-zinc-700 font-black uppercase tracking-tighter">Gordura</p>
                                    <p class="text-sm font-black text-white">{{ $ra->bf_percent }}%</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[8px] text-zinc-700 font-black uppercase tracking-tighter">Peso</p>
                                    <p class="text-sm font-black text-white">{{ $ra->weight_kg }}kg</p>
                                </div>
                                <div class="ml-auto">
                                    <a href="{{ route('professional.patients.show', $ra->user_id) }}" class="text-[9px] font-black text-emerald-500 uppercase tracking-widest hover:underline">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="lg:col-span-4 space-y-10">
            <!-- Goal Breakdown -->
            <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                <h3 class="text-xl font-black text-white mb-8 uppercase tracking-tighter italic">Arquitetura de Objetivos</h3>
                <div class="space-y-6">
                    @foreach($goalDistribution as $goal => $count)
                        @php
                            $percent = ($count / max(1, $patients->count())) * 100;
                        @endphp
                        <div class="space-y-2">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $goal }}</span>
                                <span class="text-xs font-black text-white italic">{{ $count }} Alunos</span>
                            </div>
                            <div class="h-2 bg-zinc-950 rounded-full border border-white/5 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-400 shadow-xl transition-all duration-1000" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- High Performance List (Top Health) -->
            <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl">
                <h3 class="text-xl font-black text-white mb-8 uppercase tracking-tighter italic">Elite Performance</h3>
                <div class="space-y-8">
                    @foreach($patients->sortByDesc('health_score')->take(5) as $top)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 font-black">
                                {{ $loop->iteration }}º
                            </div>
                            <div class="flex-1">
                                <p class="text-white font-black text-sm">{{ $top->name }}</p>
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Consistency Streak: 12d</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black text-white italic tracking-tighter">{{ $top->health_score }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection
