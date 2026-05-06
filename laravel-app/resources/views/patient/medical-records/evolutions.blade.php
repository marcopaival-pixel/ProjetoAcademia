@extends('layouts.app')

@section('title', 'Histórico de Atendimentos')

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
            title="Meus Atendimentos" 
            subtitle="Registros Clínicos e Evoluções" 
            backUrl="{{ route('patient.medical-records.index') }}"
            icon="fas fa-notes-medical"
        />

        <div class="space-y-6">
            @forelse($evolutions as $evolution)
                <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 blur-3xl rounded-full"></div>
                    
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-500/20">
                                {{ $evolution->type ?: 'Consulta' }}
                            </span>
                            <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">{{ $evolution->date->format('d/m/Y') }}</span>
                        </div>
                        <div class="text-[9px] font-black text-zinc-600 uppercase tracking-widest italic">ID #{{ $evolution->id }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
                        <div class="space-y-3">
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Avaliação Clínica
                            </p>
                            <p class="text-zinc-300 text-xs leading-relaxed font-medium italic">
                                "{{ $evolution->assessment ?: 'Sem detalhes registrados.' }}"
                            </p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Conduta & Plano
                            </p>
                            <p class="text-zinc-300 text-xs leading-relaxed font-medium italic">
                                "{{ $evolution->conduct ?: 'Sem orientações registradas.' }}"
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center text-zinc-500 shadow-inner">
                                <i class="fas fa-user-md text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">Profissional Responsável</p>
                                <p class="text-[10px] font-black text-white uppercase tracking-wider">{{ $evolution->professional->name }}</p>
                            </div>
                        </div>
                        
                        @if($evolution->attachments)
                            <button class="px-5 py-3 bg-white/5 text-zinc-400 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all flex items-center gap-2 border border-white/5">
                                <i class="fas fa-paperclip"></i>
                                Anexos ({{ count(json_decode($evolution->attachments, true)) }})
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <x-patient.empty-state 
                    icon="fas fa-notes-medical" 
                    title="Nenhum Registro" 
                    description="Suas evoluções e registros de atendimentos clínicos aparecerão aqui após cada consulta."
                />
            @endforelse

            <div class="mt-10">
                {{ $evolutions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
