@extends('layouts.app')

@section('title', 'Editar Protocolo')

@section('content')
<div class="max-w-7xl mx-auto space-y-12 animate-dashboard-entry">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-5xl font-black text-white tracking-tighter mb-2">Editar <span class="text-blue-500">Protocolo</span></h1>
            <p class="text-zinc-500 font-bold uppercase text-[10px] tracking-[0.3em]">Refine as diretrizes institucionais</p>
        </div>
        <a href="{{ route('admin.clinic.protocols.index') }}" class="px-8 py-4 bg-zinc-900 text-zinc-400 font-black rounded-2xl border border-white/5 hover:bg-zinc-800 transition-all flex items-center gap-3">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form action="{{ route('admin.clinic.protocols.update', $protocol) }}" method="POST" class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[3rem] shadow-2xl space-y-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Nome do Protocolo</label>
                <input type="text" name="name" value="{{ $protocol->name }}" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. Tratamento Anti-Inflamatório Padrão">
            </div>
            
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Tipo de Protocolo</label>
                <select name="type" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer">
                    <option value="training" {{ $protocol->type == 'training' ? 'selected' : '' }}>Treino</option>
                    <option value="nutrition" {{ $protocol->type == 'nutrition' ? 'selected' : '' }}>Nutrição</option>
                    <option value="medical" {{ $protocol->type == 'medical' ? 'selected' : '' }}>Clínico</option>
                </select>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Especialidade</label>
                <select name="especialidade_id" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">Selecione...</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->id }}" {{ $protocol->especialidade_id == $s->id ? 'selected' : '' }}>{{ $s->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Descrição Geral</label>
            <textarea name="description" rows="3" class="w-full bg-zinc-950/50 border border-white/5 rounded-3xl px-6 py-4 text-white font-medium focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none">{{ $protocol->description }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-white/5">
            <div class="space-y-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Objetivo Clínico</label>
                    <input type="text" name="objective" value="{{ $protocol->objective }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. Redução da Inflamação">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Frequência Recomendada</label>
                    <input type="text" name="frequency" value="{{ $protocol->frequency }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. 12 em 12 horas">
                </div>
            </div>
            <div class="space-y-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Protocolo Detalhado</label>
                    <textarea name="protocol" rows="4" class="w-full bg-zinc-950/50 border border-white/5 rounded-3xl px-6 py-4 text-white font-medium focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none">{{ $protocol->protocol }}</textarea>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Duração Prevista</label>
                    <input type="text" name="duration" value="{{ $protocol->duration }}" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. 14 dias">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-8">
            <button type="submit" class="px-12 py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/20 active:scale-95">
                SALVAR ALTERAÇÕES
            </button>
        </div>
    </form>
</div>

<style>
    @keyframes dashboard-entry {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-dashboard-entry {
        animation: dashboard-entry 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
