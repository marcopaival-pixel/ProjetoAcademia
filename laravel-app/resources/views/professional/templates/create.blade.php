@extends('layouts.app')

@section('title', 'Novo Template — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1000px] mx-auto px-6">
    <div class="flex items-center justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-white">Novo <span class="text-blue-400">Template</span></h1>
            <p class="text-zinc-500 font-medium">Crie modelos de orientações para facilitar suas prescrições.</p>
        </div>
        <a href="{{ route('professional.templates.index') }}" class="text-zinc-500 hover:text-white font-bold transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Voltar
        </a>
    </div>

    <form action="{{ route('professional.templates.store') }}" method="POST" class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-12 rounded-[3rem] shadow-2xl space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Título do Template</label>
                <input type="text" name="title" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" placeholder="E.g. Orientações Pré-Treino Hipertrofia">
            </div>
            
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 ml-4">Especialidade Relacionada</label>
                <select name="especialidade_id" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all appearance-none cursor-pointer">
                    <option value="">Selecione...</option>
                    @foreach($specialties as $s)
                        <option value="{{ $s->id }}">{{ $s->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex justify-between items-center ml-4">
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Conteúdo do Template</label>
                <span class="text-[9px] text-zinc-600 font-bold italic">Este texto será usado como base no prompt da IA.</span>
            </div>
            <textarea name="content" rows="12" required class="w-full bg-zinc-950/50 border border-white/5 rounded-[2.5rem] p-8 text-white font-medium focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none shadow-inner" placeholder="E.g. Focar em exercícios multiarticulares, carga progressiva, descanso de 90s..."></textarea>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-3xl shadow-2xl shadow-blue-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all uppercase text-xs tracking-widest">
                SALVAR TEMPLATE
            </button>
        </div>
    </form>
</div>
@endsection



