@extends('layouts.app')

@section('title', 'Meus Laudos')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-32">
    <div class="py-10 px-6 max-w-2xl mx-auto">
        <x-patient.page-header 
            title="Meus Laudos" 
            subtitle="Resultados e Análises Clínicas" 
            backUrl="{{ route('patient.medical-records.index') }}"
            icon="fas fa-file-medical-alt"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($reports as $report)
                <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl hover:border-amber-500/30 transition-all group relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-500/5 blur-2xl rounded-full"></div>
                    
                    <div class="flex justify-between items-start mb-6 relative z-10">
                        <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform shadow-inner">
                            <i class="fas fa-file-medical-alt text-2xl"></i>
                        </div>
                        <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">{{ $report->date->format('d/m/Y') }}</span>
                    </div>

                    <h3 class="text-xl font-black text-white mb-2 leading-tight group-hover:text-amber-400 transition-colors tracking-tighter">{{ $report->title }}</h3>
                    <p class="text-zinc-400 text-[11px] font-medium line-clamp-2 mb-8 leading-relaxed italic">"{{ $report->description }}"</p>

                    <div class="flex gap-3 relative z-10">
                        <a href="{{ route('patient.medical-records.reports.download', $report->id) }}" class="flex-1 py-4 bg-amber-500 text-zinc-950 font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-amber-400 transition-all flex items-center justify-center gap-2 shadow-xl shadow-amber-500/20">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('patient.medical-records.reports.download', $report->id) }}" class="px-5 py-4 bg-white/5 text-zinc-300 rounded-2xl border border-white/5 hover:bg-white/10 transition-all">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2">
                    <x-patient.empty-state 
                        icon="fas fa-file-medical-alt" 
                        title="Nenhum Laudo" 
                        description="Seus laudos e resultados de exames serão listados aqui assim que forem liberados pelo seu profissional."
                    />
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
