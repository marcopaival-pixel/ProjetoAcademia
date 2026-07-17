@extends('layouts.app')

@section('title', 'Abrir Chamado')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('support.tickets.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all shadow-xl">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Novo Protocolo</h2>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Nossa equipe responderá em breve</p>
        </div>
    </div>

    <form action="{{ route('support.tickets.store') }}" method="POST" class="space-y-8 pb-20">
        @csrf
        <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] p-10 space-y-8 shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                     <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-1 mb-2 block">Assunto resumido</label>
                     <input type="text" name="subject" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold outline-none focus:border-blue-500/50 transition-all shadow-inner" placeholder="Ex: Problema no login do aluno">
                </div>
                <div>
                     <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-1 mb-2 block">Urgência do Caso</label>
                     <select name="priority" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold outline-none focus:border-blue-500/50 transition-all appearance-none">
                        <option value="Low">Baixa - Dúvida Geral</option>
                        <option value="Medium">Média - Funcionamento Impróprio</option>
                        <option value="High">Alta - Bloqueio Parcial</option>
                        <option value="Critical">Crítica - Sistema Indisponível</option>
                     </select>
                </div>
            </div>

            <div>
                 <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-1 mb-2 block">Categoria</label>
                 <select name="category" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white font-bold outline-none focus:border-blue-500/50 transition-all appearance-none">
                    <option value="Técnico">Problema Técnico / Bug</option>
                    <option value="Financeiro">Dúvida Financeira / Faturamento</option>
                    <option value="Onboarding">Dúvida de Uso / Configuração</option>
                    <option value="Sugestão">Sugestão de Funcionalidade</option>
                    <option value="Outros">Outros</option>
                 </select>
            </div>

            <div>
                 <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-1 mb-2 block">Descrição Detalhada</label>
                 <textarea name="message" required rows="6" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-5 text-zinc-300 text-sm outline-none focus:border-blue-500/50 resize-none transition-all shadow-inner" placeholder="Descreva o que está acontecendo. Testou em outro navegador? Algum erro específico?"></textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <button type="submit" class="px-12 py-5 bg-blue-600 rounded-3xl text-xs text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/30 flex items-center gap-3">
                <i class="fas fa-paper-plane text-[10px]"></i> Enviar Chamado Agora
            </button>
        </div>
    </form>
</div>
@endsection
