@extends('layouts.admin')

@section('title', 'Gestão de Entidades')

@section('content')
<div class="glass-card p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Catálogo de Entidades</h2>
            <p class="text-xs text-zinc-500 mt-1">Configure quais tabelas do sistema podem ser administradas dinamicamente.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.configuration-center.entities.discovery') }}" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-950 border border-white/5 text-zinc-400 rounded-xl font-black text-[10px] uppercase tracking-widest hover:text-white hover:border-white/20 transition-all">
                <i data-lucide="search" class="w-4 h-4"></i>
                Explorar Banco
            </a>
            <a href="{{ route('admin.configuration-center.entities.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-emerald-500 text-zinc-950 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nova Entidade
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em]">
                    <th class="px-6 py-4">Entidade</th>
                    <th class="px-6 py-4">Tabela / Model</th>
                    <th class="px-6 py-4">Categoria</th>
                    <th class="px-6 py-4 text-center">Campos</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entities as $entity)
                <tr class="group">
                    <td class="px-6 py-4 bg-zinc-900/30 rounded-l-2xl border-y border-l border-white/5 group-hover:bg-zinc-900/50 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-white/5 flex items-center justify-center text-emerald-500 shadow-xl">
                                <i data-lucide="{{ $entity->icon ?? 'box' }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-[11px] font-black text-white uppercase tracking-wider">{{ $entity->display_name }}</div>
                                <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-1">{{ $entity->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 bg-zinc-900/30 border-y border-white/5 group-hover:bg-zinc-900/50 transition-all">
                        <div class="text-[10px] text-zinc-400 font-mono">{{ $entity->table_name }}</div>
                        <div class="text-[8px] text-zinc-600 truncate max-w-[200px] mt-1">{{ $entity->model_class }}</div>
                    </td>
                    <td class="px-6 py-4 bg-zinc-900/30 border-y border-white/5 group-hover:bg-zinc-900/50 transition-all">
                        <span class="px-2 py-1 rounded-lg bg-zinc-950 border border-white/5 text-[8px] font-black text-zinc-500 uppercase tracking-widest">
                            {{ $entity->category ?? 'Geral' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 bg-zinc-900/30 border-y border-white/5 group-hover:bg-zinc-900/50 transition-all text-center">
                        <span class="text-[10px] font-black text-white">{{ $entity->fields_count ?? $entity->fields()->count() }}</span>
                    </td>
                    <td class="px-6 py-4 bg-zinc-900/30 border-y border-white/5 group-hover:bg-zinc-900/50 transition-all text-center">
                        @if($entity->is_active)
                            <span class="flex items-center justify-center gap-1.5 text-emerald-500 text-[9px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Ativo
                            </span>
                        @else
                            <span class="flex items-center justify-center gap-1.5 text-zinc-600 text-[9px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span> Inativo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 bg-zinc-900/30 rounded-r-2xl border-y border-r border-white/5 group-hover:bg-zinc-900/50 transition-all text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.configuration-center.crud.index', $entity->name) }}" class="p-2 rounded-lg bg-zinc-950 border border-white/5 text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/20 transition-all" title="Ver Dados">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.configuration-center.entities.edit', $entity->id) }}" class="p-2 rounded-lg bg-zinc-950 border border-white/5 text-zinc-500 hover:text-blue-500 hover:border-blue-500/20 transition-all" title="Editar Configuração">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.configuration-center.entities.destroy', $entity->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja remover esta entidade? Isso não apagará os dados da tabela, apenas a configuração de administração.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-zinc-950 border border-white/5 text-zinc-500 hover:text-rose-500 hover:border-rose-500/20 transition-all" title="Remover">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
