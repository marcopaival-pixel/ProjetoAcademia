@extends('layouts.admin')

@section('title', 'Catálogo de Exercícios — Admin')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
        
        <!-- Form Pane (Left 1) -->
        <div class="xl:col-span-1">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-10 rounded-[3rem] shadow-2xl sticky top-32">
                <header class="mb-10">
                    <h3 class="text-base font-bold text-white tracking-tight">Novo Item</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Expansão do Banco de Dados</p>
                </header>

                <form action="{{ route('admin.exercises.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do Exercício</label>
                        <input type="text" name="name" required placeholder="Ex: Supino Reto" 
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Grupo</label>
                            <select name="muscle_group" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                                <option value="Peito">Peito</option>
                                <option value="Costas">Costas</option>
                                <option value="Pernas">Pernas</option>
                                <option value="Ombros">Ombros</option>
                                <option value="Braços">Braços</option>
                                <option value="Abdomen">Abdomen</option>
                                <option value="Cardio">Cardio</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nível</label>
                            <select name="difficulty" required class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-xs outline-none focus:ring-2 focus:ring-blue-600 transition-all appearance-none">
                                <option value="beginner">Iniciante</option>
                                <option value="intermediate">Interm.</option>
                                <option value="advanced">Avançado</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Equipamento</label>
                        <input type="text" name="equipment" placeholder="Halteres, Barra..." 
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                    </div>

                    <x-muscle-selector />

                    <div class="flex items-center gap-3 p-4 bg-zinc-950/50 rounded-2xl border border-white/5">
                        <input type="checkbox" id="is_active" name="is_active" checked class="w-5 h-5 rounded-lg border-white/10 bg-zinc-900 text-blue-600 focus:ring-blue-600">
                        <label for="is_active" class="text-xs text-zinc-400 font-bold cursor-pointer">Visível para usuários</label>
                    </div>

                    <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/10 active:scale-[0.98]">
                        Adicionar ao Catálogo
                    </button>
                </form>
            </div>
        </div>

        <!-- Catalog List (Right 2) -->
        <div class="xl:col-span-2 space-y-8">
            <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden shadow-2xl">
                <header class="p-8 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-zinc-950/20">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-tight">Catálogo de Exercícios — Admin</h2>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">{{ count($exercises) }} Exercícios Ativos</p>
                    </div>
                    
                    <form action="{{ route('admin.exercises.catalog') }}" method="GET" class="flex items-center gap-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." 
                            class="bg-zinc-950 border border-white/5 p-3 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-blue-600 transition-all">
                        <button type="submit" class="w-10 h-10 bg-blue-600/10 text-blue-500 rounded-xl border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </form>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead>
                            <tr class="text-zinc-600 text-[9px] font-black uppercase tracking-[0.2em] border-b border-white/5">
                                <th class="px-8 py-6">Exercício & Intensidade</th>
                                <th class="px-8 py-6">Grupo Muscular</th>
                                <th class="px-8 py-6">Equipamento</th>
                                <th class="px-8 py-6 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($exercises as $ex)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-3 h-3 rounded-full {{ $ex->is_active ? 'bg-emerald-500 shadow-sm shadow-emerald-500/50' : 'bg-zinc-800' }}"></div>
                                            <div>
                                                <p class="text-sm font-black text-white leading-none">{{ $ex->name }}</p>
                                                <span class="text-[8px] font-black uppercase tracking-widest mt-1 inline-block {{ $ex->difficulty == 'beginner' ? 'text-emerald-500' : ($ex->difficulty == 'intermediate' ? 'text-blue-500' : 'text-amber-500') }}">
                                                    {{ ucfirst($ex->difficulty) }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-xs font-bold text-zinc-400">{{ $ex->muscle_group }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-xs text-zinc-600 italic font-medium">{{ $ex->equipment ?: '—' }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.exercises.edit', $ex->id) }}" class="w-9 h-9 border border-white/5 bg-zinc-950 rounded-xl flex items-center justify-center text-zinc-500 hover:text-blue-500 hover:bg-blue-600/10 transition-all">
                                                <i class="fas fa-edit text-[10px]"></i>
                                            </a>
                                            <form action="{{ route('admin.exercises.delete', $ex->id) }}" method="POST"
                                            data-confirm-delete
                                            data-confirm-title="Excluir exercício"
                                            data-confirm-message="Excluir este exercício? Esta ação não pode ser desfeita.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-9 h-9 border border-white/5 bg-zinc-950 rounded-xl flex items-center justify-center text-zinc-500 hover:text-red-500 hover:bg-red-600/10 transition-all">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-20 text-center text-zinc-600 italic text-sm">Nenhum exercício encontrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
