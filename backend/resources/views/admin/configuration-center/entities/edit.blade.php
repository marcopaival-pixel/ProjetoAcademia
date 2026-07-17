@extends('layouts.admin')

@section('title', 'Editar Entidade: ' . $entity->display_name)

@section('content')
<div class="space-y-8">
    <!-- Configurações Gerais -->
    <div class="glass-card p-8">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-white tracking-tight">Configurações da Entidade</h2>
            <p class="text-xs text-zinc-500 mt-1">Atualize os parâmetros básicos e visibilidade.</p>
        </div>

        <form action="{{ route('admin.configuration-center.entities.update', $entity->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome de Exibição</label>
                    <input type="text" name="display_name" value="{{ old('display_name', $entity->display_name) }}" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Ícone (Lucide)</label>
                    <input type="text" name="icon" value="{{ old('icon', $entity->icon) }}" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Categoria</label>
                    <input type="text" name="category" value="{{ old('category', $entity->category) }}" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ $entity->is_active ? 'checked' : '' }} id="is_active" class="w-5 h-5 rounded-lg bg-zinc-950 border-white/5 text-emerald-500 focus:ring-offset-zinc-950 focus:ring-emerald-500 transition-all">
                <label for="is_active" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest cursor-pointer">Entidade Ativa</label>
            </div>

            <div class="pt-6 border-t border-white/5 flex justify-end gap-4">
                <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">Salvar Alterações</button>
            </div>
        </form>
    </div>

    <!-- Gestão de Campos -->
    <div class="glass-card p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-xl font-bold text-white tracking-tight">Definição de Campos</h2>
                <p class="text-xs text-zinc-500 mt-1">Configure como cada coluna da tabela deve ser exibida e validada.</p>
            </div>
            <button onclick="document.getElementById('modal-new-field').style.display='flex'" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-950 border border-emerald-500/20 text-emerald-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-lg shadow-emerald-500/10">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Novo Campo
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
                        <th class="px-6 py-4">Campo</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4 text-center">Lista</th>
                        <th class="px-6 py-4 text-center">Form</th>
                        <th class="px-6 py-4 text-center">Req.</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($entity->fields->sortBy('sort_order') as $field)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-[11px] font-black text-white uppercase tracking-wider">{{ $field->label }}</div>
                            <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-1">{{ $field->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg bg-zinc-950 border border-white/5 text-[8px] font-black text-zinc-400 uppercase tracking-widest">
                                {{ $field->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <i data-lucide="{{ $field->is_visible_list ? 'check-circle-2' : 'x-circle' }}" class="w-4 h-4 mx-auto {{ $field->is_visible_list ? 'text-emerald-500' : 'text-zinc-800' }}"></i>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <i data-lucide="{{ $field->is_visible_form ? 'check-circle-2' : 'x-circle' }}" class="w-4 h-4 mx-auto {{ $field->is_visible_form ? 'text-emerald-500' : 'text-zinc-800' }}"></i>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <i data-lucide="{{ $field->is_required ? 'check-circle-2' : 'x-circle' }}" class="w-4 h-4 mx-auto {{ $field->is_required ? 'text-amber-500' : 'text-zinc-800' }}"></i>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.configuration-center.fields.destroy', $field->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-zinc-700 hover:text-rose-500 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Novo Campo -->
<div id="modal-new-field" class="fixed inset-0 z-[3000] hidden items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4">
    <div class="bg-zinc-950 border border-white/10 rounded-3xl w-full max-w-lg shadow-3xl animate-fade-in-up">
        <div class="p-8 border-b border-white/5 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white uppercase tracking-tight">Adicionar Novo Campo</h3>
            <button onclick="document.getElementById('modal-new-field').style.display='none'" class="text-zinc-600 hover:text-white transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.configuration-center.entities.fields.store', $entity->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Nome na Tabela (SQL Name)</label>
                    <input type="text" name="name" class="w-full bg-zinc-900 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" placeholder="ex: description" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Label de Exibição</label>
                    <input type="text" name="label" class="w-full bg-zinc-900 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all" placeholder="ex: Descrição do Exercício" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Tipo de Input</label>
                    <select name="type" class="w-full bg-zinc-900 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                        <option value="text">Texto Simples</option>
                        <option value="textarea">Área de Texto</option>
                        <option value="number">Número</option>
                        <option value="select">Seleção (Select)</option>
                        <option value="boolean">Booleano (Toggle)</option>
                        <option value="date">Data</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Ordem</label>
                    <input type="number" name="sort_order" value="1" class="w-full bg-zinc-900 border border-white/5 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500/50 transition-all">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_required" value="1" id="is_required_new" class="w-5 h-5 rounded-lg bg-zinc-900 border-white/5 text-emerald-500 focus:ring-emerald-500 transition-all">
                <label for="is_required_new" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest cursor-pointer">Campo Obrigatório</label>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-new-field').style.display='none'" class="px-6 py-3 rounded-xl bg-zinc-900 text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white transition-all">Cancelar</button>
                <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20">Adicionar Campo</button>
            </div>
        </form>
    </div>
</div>
@endsection
