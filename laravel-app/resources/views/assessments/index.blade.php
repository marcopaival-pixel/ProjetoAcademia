@extends('layouts.app')

@section('title', 'Avaliações Físicas — NexShape')

@section('content')
<div class="py-10 space-y-10 animate-fade-in max-w-[1400px] mx-auto px-6">
    <!-- Header Hero -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white tracking-tighter">Histórico de <span class="text-blue-500">Avaliações</span></h1>
            <p class="text-zinc-500 font-medium">Monitore suas medidas, composição corporal e progresso estético.</p>
        </div>
        
        <a href="{{ route('assessments.create') }}" class="group relative px-8 py-4 bg-blue-600 text-white font-black rounded-[1.5rem] overflow-hidden transition-all hover:scale-[1.02] active:scale-[0.98] shadow-2xl shadow-blue-600/20">
            <span class="relative z-10 flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i>
                Nova Avaliação
            </span>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-white/20 transform translate-y-full group-hover:translate-y-0 transition-transform"></div>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($assessments as $index => $assessment)
            <div class="group relative bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] overflow-hidden transition-all hover:bg-zinc-900/60 hover:border-blue-500/20 shadow-xl">
                <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-100 transition-opacity">
                    <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" onsubmit="return confirm('Excluir esta avaliação?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 flex items-center justify-center text-blue-500 border border-blue-500/20 transition-all group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <div>
                        <h5 class="text-white font-black text-xl leading-none">{{ $assessment->assessment_date->translatedFormat('d \d\e F') }}</h5>
                        <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest mt-1">{{ $assessment->assessment_date->format('Y') }} • {{ $assessment->assessment_date->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                        <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">Peso</span>
                        <span class="text-lg font-black text-white">{{ $assessment->weight_kg ?? '--' }}<small class="text-zinc-500 text-[10px] ml-0.5">kg</small></span>
                    </div>
                    <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                        <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">BF</span>
                        <span class="text-lg font-black text-blue-400">{{ $assessment->bf_percent ?? '--' }}<small class="text-[10px] ml-0.5">%</small></span>
                    </div>
                    <div class="bg-zinc-950/60 p-4 rounded-2xl border border-white/5 text-center">
                        <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">Massa</span>
                        <span class="text-lg font-black text-emerald-400">{{ $assessment->muscle_percent ?? '--' }}<small class="text-[10px] ml-0.5">%</small></span>
                    </div>
                </div>

                <a href="{{ route('assessments.show', $assessment) }}" class="flex items-center justify-center w-full py-4 bg-zinc-950 border border-white/10 text-zinc-400 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all group-hover:bg-zinc-900 group-hover:text-white group-hover:border-blue-500/30">
                    Detalhes Completos &rarr;
                </a>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 py-20 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-zinc-900/50 border border-white/5 mb-6 text-zinc-700">
                    <i class="fas fa-ruler-combined text-4xl"></i>
                </div>
                <h3 class="text-white font-black text-xl">Nenhuma avaliação ainda</h3>
                <p class="text-zinc-500 max-w-sm mx-auto mt-2">Comece registrando suas medidas hoje para visualizar seu progresso em gráficos e métricas.</p>
                <a href="{{ route('assessments.create') }}" class="inline-block mt-8 px-8 py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-500 transition-all">Criar Primeira Avaliação</a>
            </div>
        @endforelse
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
