@extends('layouts.admin')

@section('title', $template->exists ? 'Editar Template' : 'Novo Template')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-20">
    
    <!-- Navigation -->
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.settings.email.templates.index') }}" class="inline-flex items-center gap-3 px-5 py-2.5 rounded-xl bg-zinc-950 border border-white/5 text-zinc-500 text-[10px] font-black uppercase tracking-widest hover:text-white transition-all group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Voltar aos Templates
        </a>
        <div class="flex items-center gap-3">
            @if($template->exists)
                <span class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 text-blue-500 text-[10px] font-black uppercase tracking-widest rounded-xl">ID: #{{ $template->id }}</span>
            @endif
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="glass-card p-10 border-emerald-500/10 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/5 rounded-full blur-[100px] pointer-events-none"></div>

        <header class="mb-12">
            <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic leading-none">Editor de <span class="text-emerald-500">Comunicação</span></h3>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.3em] mt-3 italic">Defina a estrutura e o conteúdo dinâmico das mensagens</p>
        </header>

        <form method="POST" action="{{ $template->exists ? route('admin.settings.email.templates.update', $template) : route('admin.settings.email.templates.store') }}" class="space-y-10">
            @csrf
            
            <!-- Grupo 1: Escopo e Hook -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Contexto / Unidade</label>
                    <div class="relative">
                        <select name="empresa_id" class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                            <option value="">— Escopo Global (Fallback) —</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}" @selected(old('empresa_id', $template->empresa_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo de Gatilho (Hook)</label>
                    <div class="relative">
                        <select name="tipo" class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer" required>
                            @foreach($tipos as $key => $label)
                                <option value="{{ $key }}" @selected(old('tipo', $template->tipo) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-600">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 md:col-span-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Identificador</label>
                    <input type="text" name="nome_template" value="{{ old('nome_template', $template->nome_template) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                        placeholder="Ex.: Boas-vindas Aluno Premium">
                </div>
            </div>

            <!-- Grupo 2: Conteúdo -->
            <div class="pt-10 border-t border-white/5 space-y-8">
                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Assunto do E-mail (Subject)</label>
                    <input type="text" name="assunto" value="{{ old('assunto', $template->assunto) }}" required
                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                        placeholder="Ex.: Bem-vindo à @{{app_name}}">
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between ml-1">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Corpo da Mensagem (HTML)</label>
                        <div class="flex items-center gap-4">
                             <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest">Variáveis Sugeridas:</span>
                             <div class="flex gap-2">
                                 <code class="text-[9px] bg-zinc-950 px-2 py-0.5 rounded border border-white/5 text-emerald-500">@{{name}}</code>
                                 <code class="text-[9px] bg-zinc-950 px-2 py-0.5 rounded border border-white/5 text-emerald-500">@{{app_name}}</code>
                             </div>
                        </div>
                    </div>
                    <textarea name="mensagem" rows="15" required
                        class="w-full bg-zinc-950 border border-white/5 p-6 rounded-3xl text-white text-xs outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all font-mono leading-relaxed">{{ old('mensagem', $template->mensagem) }}</textarea>
                </div>
            </div>

            <!-- Grupo 3: Metadados e Status -->
            <div class="pt-10 border-t border-white/5 grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                <div class="space-y-3">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tags / Metadados (opcional)</label>
                    <input type="text" name="variaveis" value="{{ old('variaveis', $template->variaveis) }}"
                        class="w-full bg-zinc-950 border border-white/5 p-5 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-800"
                        placeholder="Ex.: name, email, link">
                </div>

                <div class="flex items-center justify-between p-5 bg-zinc-950/50 rounded-2xl border border-white/5 h-[62px]">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-600">Publicar Template</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ativo" value="1" class="sr-only peer" @checked(old('ativo', $template->ativo ?? true))>
                        <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 peer-checked:after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-8">
                <button type="submit" class="w-full md:w-auto px-16 py-6 bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-[0.2em] rounded-[2rem] hover:bg-emerald-400 transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98]">
                    {{ $template->exists ? 'Atualizar Template' : 'Criar Template' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
