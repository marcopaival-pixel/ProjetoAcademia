@extends('layouts.admin')

@section('title', 'Gestão de Grupos')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Gestão de <span class="text-blue-500">Grupos e Comunidades</span></h1>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Controle total administrativo sobre canais de comunicação</p>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.groups.requests') }}" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 rounded-2xl text-[10px] text-zinc-300 font-black uppercase tracking-widest transition-all flex items-center gap-2">
                <i class="fas fa-user-clock text-amber-500"></i>
                Solicitações
            </a>
            <button onclick="document.getElementById('newGroupModal').classList.remove('hidden')" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20">
                Novo Grupo
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="text-xs font-bold uppercase tracking-wide">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($groups as $group)
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 hover:bg-zinc-900/60 transition-all group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 flex gap-3">
                <a href="{{ route('admin.groups.members', $group) }}" class="flex flex-col items-center gap-1 group/act">
                    <div class="p-3 bg-zinc-800/80 backdrop-blur-md border border-white/5 hover:bg-blue-600 text-zinc-400 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-users text-xs"></i>
                    </div>
                    <span class="text-[7px] font-black uppercase tracking-widest text-zinc-600 group-hover/act:text-blue-400 transition-colors">Membros</span>
                </a>
                
                <a href="{{ route('admin.groups.edit', $group) }}" class="flex flex-col items-center gap-1 group/act">
                    <div class="p-3 bg-zinc-800/80 backdrop-blur-md border border-white/5 hover:bg-zinc-700 text-zinc-400 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </div>
                    <span class="text-[7px] font-black uppercase tracking-widest text-zinc-600 group-hover/act:text-zinc-300 transition-colors">Config</span>
                </a>

                <form action="{{ route('admin.groups.delete', $group) }}" method="POST" class="flex flex-col items-center gap-1 group/act"
                    data-confirm-delete
                    data-confirm-title="Excluir grupo"
                    data-confirm-message="Excluir este grupo? Esta ação não pode ser desfeita.">
                    @csrf
                    @method('DELETE')
                    <button class="p-3 bg-zinc-800/80 backdrop-blur-md border border-white/5 hover:bg-red-600 text-zinc-400 hover:text-white rounded-xl transition-all">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                    <span class="text-[7px] font-black uppercase tracking-widest text-zinc-600 group-hover/act:text-red-400 transition-colors">Excluir</span>
                </form>
            </div>

            <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center text-xl text-blue-500 mb-6 font-black border border-white/5">
                <i class="fas fa-layer-group"></i>
            </div>
            
            <h3 class="text-xl font-black text-white tracking-tight mb-2">{{ $group->name }}</h3>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-6 min-h-[30px] line-clamp-2">{{ $group->description ?: 'Sem descrição' }}</p>
            
            <div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/5 mb-6">
                <div class="flex flex-col">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Tipo</span>
                    <span class="text-[10px] font-black uppercase tracking-widest {{ $group->is_private ? 'text-amber-500' : 'text-emerald-500' }}">
                        {{ $group->is_private ? 'Privado' : 'Público' }}
                    </span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Status</span>
                    <span class="text-[10px] font-black uppercase tracking-widest {{ $group->is_active ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $group->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between text-[9px] text-zinc-600 font-black uppercase tracking-widest">
                <div class="flex flex-col">
                    <span>Membros</span>
                    <span class="text-lg font-black text-white">{{ $group->members_count }}</span>
                </div>
                <div class="flex flex-col items-end">
                    <span>Criado em</span>
                    <span class="text-white">{{ $group->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-zinc-900/20 rounded-[2.5rem] border border-dashed border-white/5">
            <i class="fas fa-layer-group text-4xl text-zinc-800 mb-4"></i>
            <p class="text-zinc-500 font-medium">Nenhum grupo de comunicação cadastrado.</p>
        </div>
        @endforelse
    </div>
</div>

<!-- New Group Modal -->
<div id="newGroupModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm animate-fade-in">
    <div class="bg-zinc-900 border border-white/10 w-full max-w-xl rounded-[2.5rem] p-8 shadow-2xl animate-dashboard-entry text-left space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-black text-white tracking-tight">Criar <span class="text-blue-500">Novo Grupo</span></h3>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Configurações avançadas da comunidade</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-600/10 flex items-center justify-center text-blue-500 border border-blue-500/20">
                <i class="fas fa-plus text-xl"></i>
            </div>
        </div>

        <form action="{{ route('admin.groups.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-1">
                <label class="text-[10px] text-zinc-400 font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-2">
                    <i class="fas fa-signature text-blue-500"></i> Nome do Grupo
                </label>
                <input type="text" name="name" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white focus:border-blue-500 transition-all outline-none text-sm placeholder:text-zinc-700" placeholder="Ex: Elite Fitness Club">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] text-zinc-400 font-black uppercase tracking-widest ml-1 mb-2 flex items-center gap-2">
                    <i class="fas fa-align-left text-blue-500"></i> Descrição
                </label>
                <textarea name="description" rows="3" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white focus:border-blue-500 transition-all outline-none text-sm placeholder:text-zinc-700" placeholder="Qual o propósito deste grupo?"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="bg-zinc-950 p-4 rounded-3xl border border-white/5 flex items-center justify-between group/opt hover:border-blue-500/30 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fas fa-lock text-[10px] text-amber-500"></i> Privado
                            </span>
                            <span class="text-[8px] text-zinc-600 uppercase font-black tracking-widest mt-0.5 whitespace-nowrap">Requer aprovação</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_private" value="1" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="bg-zinc-950 p-4 rounded-3xl border border-white/5 flex items-center justify-between group/opt hover:border-blue-500/30 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fas fa-door-open text-[10px] text-blue-500"></i> Solicitável
                            </span>
                            <span class="text-[8px] text-zinc-600 uppercase font-black tracking-widest mt-0.5 whitespace-nowrap">Auto-entrada</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_self_join" value="1" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="bg-zinc-950 p-4 rounded-3xl border border-white/5 flex items-center justify-between group/opt hover:border-emerald-500/30 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fas fa-check-circle text-[10px] text-emerald-500"></i> Ativo
                            </span>
                            <span class="text-[8px] text-zinc-600 uppercase font-black tracking-widest mt-0.5 whitespace-nowrap">Status Global</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="bg-zinc-950 p-4 rounded-3xl border border-white/5 flex items-center justify-between group/opt hover:border-blue-500/30 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fas fa-paper-plane text-[10px] text-blue-500"></i> Mensagens
                            </span>
                            <span class="text-[8px] text-zinc-600 uppercase font-black tracking-widest mt-0.5 whitespace-nowrap">Chat aberto</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="can_members_send_messages" value="1" class="sr-only peer" checked>
                            <div class="w-10 h-5 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4">
                <button type="button" onclick="document.getElementById('newGroupModal').classList.add('hidden')" class="w-full py-4 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-all text-xs uppercase tracking-widest">
                    Cancelar
                </button>
                <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all text-xs uppercase tracking-widest shadow-lg shadow-blue-500/20">
                    Criar Grupo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
