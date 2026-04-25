@extends('layouts.admin')

@section('title', 'Novo Lead')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in">
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('admin.leads.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all">
            <i class="fas fa-chevron-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Capturar Novo Lead</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Insira os dados da oportunidade comercial</p>
        </div>
    </div>

    <form action="{{ route('admin.leads.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Dados Básicos -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 space-y-6">
                <h3 class="text-sm font-black text-zinc-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fas fa-user-circle text-blue-500"></i> Informações do Contato
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Completo</label>
                        <input type="text" name="nome" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="Ex: Roberto Silva">
                    </div>

                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Email Corporativo</label>
                        <input type="email" name="email" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="email@empresa.com">
                    </div>

                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">WhatsApp / Telefone</label>
                        <input type="text" name="telefone" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="(00) 00000-0000">
                    </div>
                </div>
            </div>

            <!-- Dados Comerciais -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 space-y-6">
                <h3 class="text-sm font-black text-zinc-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fas fa-briefcase text-purple-500"></i> Contexto do Negócio
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Empresa / Academia</label>
                        <input type="text" name="empresa" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="Nome da instituição">
                    </div>

                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Origem do Lead</label>
                        <select name="origem" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none appearance-none">
                            <option value="Instagram">Instagram</option>
                            <option value="Google Search">Google Search</option>
                            <option value="Indicação">Indicação</option>
                            <option value="Evento">Evento</option>
                            <option value="Direto/Site">Direto/Site</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Responsável</label>
                        <select name="responsavel_id" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none appearance-none">
                            <option value="">Aguardando Atribuição</option>
                            @foreach($responsaveis as $resp)
                                <option value="{{ $resp->id }}">{{ $resp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Previsão e Status -->
            <div class="md:col-span-2 bg-gradient-to-br from-zinc-900/40 to-blue-600/[0.02] border border-white/5 rounded-[2.5rem] p-8">
                <h3 class="text-sm font-black text-zinc-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-emerald-500"></i> Metas da Oportunidade
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Status Inicial</label>
                        <select name="status" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none appearance-none font-bold">
                            <option value="Novo">Novo Lead</option>
                            <option value="Em contato">Em contato</option>
                            <option value="Em negociação">Em negociação</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Valor Estimado (R$)</label>
                        <input type="number" step="0.01" name="valor_estimado" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none" placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Previsão de Fechamento</label>
                        <input type="date" name="previsao_fechamento" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none">
                    </div>
                </div>

                <div class="mt-8">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Observações Internas</label>
                    <textarea name="observacao" rows="4" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-5 py-4 text-white focus:border-blue-500/50 transition-all outline-none resize-none" placeholder="Dores do cliente, necessidades específicas, etc..."></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pb-20">
            <button type="submit" class="px-10 py-5 bg-blue-600 rounded-3xl text-xs text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/30">
                Criar Oportunidade
            </button>
        </div>
    </form>
</div>
@endsection
