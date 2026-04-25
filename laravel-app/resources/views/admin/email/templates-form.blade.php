@extends('layouts.admin')

@section('title', $template->exists ? 'Editar template' : 'Novo template')

@section('content')
<div class="space-y-10 animate-fade-in max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black text-white tracking-tight">{{ $template->exists ? 'Editar template' : 'Novo template' }}</h2>
        <a href="{{ route('admin.settings.email.templates.index') }}" class="text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-white">← Voltar</a>
    </div>

    <form method="POST" action="{{ $template->exists ? route('admin.settings.email.templates.update', $template) : route('admin.settings.email.templates.store') }}" class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] space-y-8">
        @csrf
        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Empresa (vazio = global)</label>
            <select name="empresa_id" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                <option value="">— Global —</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected(old('empresa_id', $template->empresa_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo</label>
            <select name="tipo" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all" required>
                @foreach($tipos as $key => $label)
                    <option value="{{ $key }}" @selected(old('tipo', $template->tipo) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do template</label>
            <input type="text" name="nome_template" value="{{ old('nome_template', $template->nome_template) }}" required
                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Assunto</label>
            <input type="text" name="assunto" value="{{ old('assunto', $template->assunto) }}" required
                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Mensagem (HTML permitido)</label>
            <textarea name="mensagem" rows="12" required
                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all font-mono text-xs">{{ old('mensagem', $template->mensagem) }}</textarea>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Variáveis (descrição)</label>
            <input type="text" name="variaveis" value="{{ old('variaveis', $template->variaveis) }}"
                class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all"
                placeholder="Ex.: name, email, verification_url">
        </div>

        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="ativo" value="1" class="rounded border-white/10 bg-zinc-950" @checked(old('ativo', $template->ativo ?? true))>
            <span class="text-sm text-white font-bold">Ativo</span>
        </label>

        <button type="submit" class="w-full md:w-auto px-12 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all">Guardar</button>
    </form>
</div>
@endsection
