@extends('layouts.admin')

@section('title', 'Nova Entidade')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="glass-card p-8">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-white tracking-tight">Configurar Nova Entidade</h2>
            <p class="text-xs text-zinc-500 mt-1">Defina os parâmetros técnicos para a nova tabela administrável.</p>
        </div>

        <form action="{{ route('admin.configuration-center.entities.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Identificador (Name)</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="ex: exercises" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" required>
                    @error('name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome de Exibição</label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}" placeholder="ex: Catálogo de Exercícios" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome da Tabela SQL</label>
                    <input type="text" name="table_name" value="{{ old('table_name') }}" placeholder="ex: exercises_catalog" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Classe do Model (Namespace)</label>
                    <input type="text" name="model_class" value="{{ old('model_class') }}" placeholder="ex: App\Models\ExerciseCatalog" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Ícone (Lucide)</label>
                    <input type="text" name="icon" value="{{ old('icon', 'box') }}" placeholder="ex: zap, users, settings" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Categoria</label>
                    <input type="text" name="category" value="{{ old('category', 'Geral') }}" placeholder="ex: Cadastros, Sistema, IA" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
            </div>

            <div class="pt-6 border-t border-white/5 flex justify-end gap-4">
                <a href="{{ route('admin.configuration-center.entities.index') }}" class="px-6 py-3 rounded-xl bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:text-white transition-all">Cancelar</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">Salvar Entidade</button>
            </div>
        </form>
    </div>
</div>
@endsection
