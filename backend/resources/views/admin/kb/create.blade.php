@extends('layouts.admin')

@section('title', 'Novo Artigo — Base de Conhecimento')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.kb.index') }}" class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-white hover:bg-zinc-700 transition-all border border-white/5">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-black text-white">Novo Artigo</h1>
    </div>

    <form action="{{ route('admin.kb.store') }}" method="POST" class="bg-zinc-900/60 border border-white/5 rounded-[2.5rem] p-10 space-y-8 shadow-2xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Título do Artigo</label>
                <input type="text" name="titulo" required value="{{ old('titulo') }}" class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-blue-500 transition-all font-bold text-lg">
            </div>
            
            <div>
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Categoria</label>
                <select name="categoria_id" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-blue-500 transition-all font-bold">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nome }} ({{ $cat->tipo_usuario }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Público Alvo</label>
                <select name="tipo_usuario" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-blue-500 transition-all font-bold">
                    @foreach($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-4">Conteúdo do Artigo (HTML permitido)</label>
            <textarea name="conteudo" required class="w-full bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-blue-500 transition-all min-h-[400px] font-medium leading-relaxed">{{ old('conteudo') }}</textarea>
            <p class="text-[10px] text-zinc-600 mt-2 italic">* Use tags HTML como &lt;p&gt;, &lt;h3&gt;, &lt;b&gt;, &lt;br&gt; para formatação.</p>
        </div>

        <div class="flex items-center gap-3 bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
            <input type="checkbox" name="ativo" value="1" checked class="w-6 h-6 rounded-lg bg-zinc-900 border-white/10 text-blue-600 focus:ring-0">
            <label class="text-xs font-black text-zinc-400 uppercase tracking-widest">Publicar artigo imediatamente</label>
        </div>

        <div class="pt-6 flex gap-4">
            <a href="{{ route('admin.kb.index') }}" class="flex-1 py-5 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all text-xs uppercase tracking-[0.2em] text-center">Cancelar</a>
            <button type="submit" class="flex-[2] py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all text-xs uppercase tracking-[0.2em] shadow-xl shadow-blue-500/20">Criar Artigo</button>
        </div>
    </form>
</div>
@endsection
