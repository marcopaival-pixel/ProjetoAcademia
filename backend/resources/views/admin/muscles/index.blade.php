@extends('layouts.admin')

@section('title', 'Gestão Anatômica — Músculos')

@section('content')
<div class="space-y-8 animate-fade-in py-8 px-4 sm:px-6 lg:px-8 max-w-[1200px] mx-auto">
    
    <header class="pb-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Gerencie os grupos e músculos para o catálogo de exercícios.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-zinc-900 border border-white/5 text-zinc-400 hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button onclick="document.getElementById('modalGroup').classList.remove('hidden')" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-white/5">
                + Novo Grupo
            </button>
            <button onclick="document.getElementById('modalMuscle').classList.remove('hidden')" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20">
                + Adicionar Músculo
            </button>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Tabela de Músculos -->
        <div class="lg:col-span-12">
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-white/5 bg-white/5 flex items-center justify-between">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Registros Ativos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-zinc-950/50">
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Músculo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Grupo Muscular</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Tipo</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($muscles as $muscle)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-white">{{ $muscle->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-zinc-800 text-zinc-400 text-[10px] font-black uppercase rounded-lg border border-white/5">
                                        {{ $muscle->group->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-black uppercase tracking-widest {{ $muscle->type === 'principal' ? 'text-blue-400' : 'text-zinc-500' }}">
                                        {{ $muscle->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="editMuscle(@js($muscle))" class="p-2 bg-zinc-800 text-zinc-400 hover:text-white hover:bg-blue-600 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form action="{{ route('admin.muscles.destroy', $muscle) }}" method="POST"
                                              data-confirm-delete="true"
                                              data-confirm-title="Excluir Músculo"
                                              data-confirm-message="Deseja remover '{{ $muscle->name }}'? Verifique se existem exercícios vinculados a este músculo.">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-zinc-800 text-zinc-400 hover:text-white hover:bg-red-600 rounded-lg transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
        </div>
    </div>
</div>

<!-- Modal Novo Músculo -->
<div id="modalMuscle" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-zinc-900 border border-white/10 rounded-[2.5rem] shadow-2xl w-full max-w-md p-8 animate-fade-in">
        <h3 class="text-xl font-black text-white mb-6" id="modalMuscleTitle">Adicionar Músculo</h3>
        <form action="{{ route('admin.muscles.store') }}" method="POST" id="muscleForm" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="muscleMethod" value="POST">
            
            <div class="space-y-1">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do Músculo</label>
                <input type="text" name="name" id="muscleName" required class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Grupo Muscular</label>
                <select name="group_id" id="muscleGroup" required class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Tipo de Atuação</label>
                <select name="type" id="muscleType" required class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                    <option value="principal">Principal (Agonista)</option>
                    <option value="sinergista">Sinergista</option>
                    <option value="estabilizador">Estabilizador</option>
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modalMuscle').classList.add('hidden')" class="flex-1 py-3 bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-zinc-700 transition-all">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-500/20">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Novo Grupo -->
<div id="modalGroup" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-md" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-zinc-900 border border-white/10 rounded-[2.5rem] shadow-2xl w-full max-w-md p-8 animate-fade-in">
        <h3 class="text-xl font-black text-white mb-6">Criar Grupo Muscular</h3>
        <form action="{{ route('admin.muscles.group.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do Grupo</label>
                <input type="text" name="name" required placeholder="Ex: Peitorais" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Região Corporal</label>
                <select name="region" required class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all appearance-none">
                    <option value="superior">Membros Superiores</option>
                    <option value="inferior">Membros Inferiores</option>
                    <option value="core">Core / Tronco</option>
                    <option value="fullbody">Corpo Inteiro</option>
                </select>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="button" onclick="document.getElementById('modalGroup').classList.add('hidden')" class="flex-1 py-3 bg-zinc-800 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-zinc-700 transition-all">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-500/20">Criar Grupo</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editMuscle(muscle) {
        const modal = document.getElementById('modalMuscle');
        const form = document.getElementById('muscleForm');
        const title = document.getElementById('modalMuscleTitle');
        const method = document.getElementById('muscleMethod');
        
        title.innerText = 'Editar Músculo';
        form.action = `/admin/muscles/${muscle.id}`;
        method.value = 'PUT';
        
        document.getElementById('muscleName').value = muscle.name;
        document.getElementById('muscleGroup').value = muscle.group_id;
        document.getElementById('muscleType').value = muscle.type;
        
        modal.classList.remove('hidden');
    }
</script>
@endsection
