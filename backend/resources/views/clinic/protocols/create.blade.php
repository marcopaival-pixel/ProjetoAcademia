@extends('layouts.app')

@section('title', 'Novo Protocolo — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1000px] mx-auto px-6">
    <div class="flex items-center justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white">Novo <span class="text-blue-400">Protocolo</span></h1>
            <p class="text-zinc-500 font-medium">Defina os parâmetros padrão para este protocolo clínico.</p>
        </div>
        <a href="{{ route('admin.clinic.protocols.index') }}" class="text-zinc-500 hover:text-white font-bold transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Voltar
        </a>
    </div>

    <form action="{{ route('admin.clinic.protocols.store') }}" method="POST" class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[3rem] shadow-2xl space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Nome do Protocolo</label>
                <input type="text" name="name" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. Tratamento Anti-Inflamatório Padrão">
            </div>
            
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Tipo de Protocolo</label>
                <select name="type" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer">
                    <option value="training">Treino</option>
                    <option value="nutrition">Nutrição</option>
                    <option value="medical" selected>Clínico</option>
                </select>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Especialidade</label>
                <select name="especialidade_id" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">Selecione...</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->id }}">{{ $s->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Objetivo Principal</label>
                <input type="text" name="objective" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. Redução de edema e dor">
            </div>
            
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Protocolo Técnico</label>
                <input type="text" name="protocol" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. Protocolo PRICE + Mobilidade">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Frequência</label>
                <input type="text" name="frequency" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. 1x ao dia / a cada 8h">
            </div>
            
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Duração</label>
                <input type="text" name="duration" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. 7 dias / 12 semanas">
            </div>
        </div>

        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Descrição / Notas Adicionais</label>
            <textarea name="description" rows="4" class="w-full bg-zinc-950/50 border border-white/5 rounded-3xl p-6 text-white font-medium focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none" placeholder="Detalhes técnicos ou contra-indicações..."></textarea>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-3xl shadow-2xl shadow-blue-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all uppercase text-xs tracking-widest">
                SALVAR PROTOCOLO
            </button>
        </div>
    </form>
</div>
@endsection
