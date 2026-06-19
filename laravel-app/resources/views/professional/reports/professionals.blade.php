@extends('layouts.professional')

@section('title', 'Performance da Equipe — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 text-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/10">
                 <i class="fas fa-user-tie text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Performance da <span class="text-emerald-500">Equipe</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Análise por Profissional • Unidade Consolidada
                </p>
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-500/5 blur-[60px] rounded-full"></div>
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] block mb-4">Total de {{ $patientsLabel }} (Unidade)</span>
            <div class="flex items-baseline gap-2">
                <span class="text-6xl font-black text-white italic tracking-tighter tabular-nums">{{ $data['total_company_patients'] }}</span>
                <span class="text-sm font-black text-emerald-500 uppercase tracking-widest italic">{{ $patientsLabel }} Ativos</span>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-500/5 blur-[60px] rounded-full"></div>
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] block mb-4">Profissionais Ativos</span>
            <div class="flex items-baseline gap-2">
                <span class="text-6xl font-black text-white italic tracking-tighter tabular-nums">{{ count($data['professionals']) }}</span>
                <span class="text-sm font-black text-blue-500 uppercase tracking-widest italic">Corpo Clínico</span>
            </div>
        </div>
    </div>

    <!-- Professionals List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Ranking de <span class="text-emerald-500">Engajamento</span></h3>
            <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Métricas por Profissional</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-950/50">
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">Profissional</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-center">Base de {{ $patientsLabel }}</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-center">Score de Atividade (7d)</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($data['professionals'] as $prof)
                    <tr class="hover:bg-emerald-500/[0.02] transition-all group">
                        <td class="py-6 px-10">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-700 overflow-hidden shadow-inner group-hover:border-emerald-500/30 transition-all">
                                    @if($prof['avatar'])
                                        <img src="{{ asset('storage/' . $prof['avatar']) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-user-tie text-lg"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-black text-white">{{ $prof['name'] }}</div>
                                    <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-tighter">{{ $prof['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-10 text-center">
                            <span class="text-2xl font-black text-white italic tabular-nums">{{ $prof['patients_count'] }}</span>
                        </td>
                        <td class="py-6 px-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-24 h-1.5 bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $prof['recent_activity_score'] }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-emerald-500 italic">{{ $prof['recent_activity_score'] }}%</span>
                            </div>
                        </td>
                        <td class="py-6 px-10 text-right">
                             <span class="px-3 py-1 rounded-full bg-zinc-950 border border-emerald-500/20 text-emerald-500 text-[9px] font-black uppercase tracking-widest">
                                Ativo
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-zinc-600 italic">Nenhum outro profissional vinculado a esta unidade encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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



