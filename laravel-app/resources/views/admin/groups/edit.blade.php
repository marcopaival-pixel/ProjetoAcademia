@extends('layouts.admin')

@section('title', 'Editar Grupo')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 animate-fade-in">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.groups.index') }}" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-black text-white tracking-tight">Editar <span class="text-blue-500">Grupo</span></h1>
        </div>
        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-7">Ajuste as configurações finas da comunidade</p>
    </div>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
        <form action="{{ route('admin.groups.update', $group) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="text-[10px] text-zinc-400 font-black uppercase tracking-widest ml-1 mb-2 block">Nome do Grupo</label>
                <input type="text" name="name" value="{{ $group->name }}" required class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white focus:border-blue-500 transition-all outline-none" placeholder="Ex: Musculação Avançada">
            </div>

            <div>
                <label class="text-[10px] text-zinc-400 font-black uppercase tracking-widest ml-1 mb-2 block">Descrição</label>
                <textarea name="description" rows="4" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white focus:border-blue-500 transition-all outline-none" placeholder="O que se fala neste grupo?">{{ $group->description }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-zinc-950 p-5 rounded-3xl border border-white/5 flex items-center justify-between group hover:border-blue-500/30 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white">Grupo Privado</span>
                            <span class="text-[9px] text-zinc-500 uppercase tracking-widest">Requer aprovação</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_private" value="1" class="sr-only peer" {{ $group->is_private ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="bg-zinc-950 p-5 rounded-3xl border border-white/5 flex items-center justify-between group hover:border-blue-500/30 transition-all">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white">Solicitável</span>
                            <span class="text-[9px] text-zinc-500 uppercase tracking-widest">Permitir entrada</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_self_join" value="1" class="sr-only peer" {{ $group->allow_self_join ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-zinc-950 p-5 rounded-3xl border border-white/5 flex items-center justify-between group hover:border-emerald-500/30 transition-all">
                        <div class="flex flex-col text-left">
                            <span class="text-xs font-bold text-white">Status Ativo</span>
                            <span class="text-[9px] text-zinc-500 uppercase tracking-widest">Ativar/Desativar</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $group->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div class="bg-zinc-950 p-5 rounded-3xl border border-white/5 flex items-center justify-between group hover:border-blue-500/30 transition-all">
                        <div class="flex flex-col text-left">
                            <span class="text-xs font-bold text-white">Enviar Mensagens</span>
                            <span class="text-[9px] text-zinc-500 uppercase tracking-widest">Permissão membros</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="can_members_send_messages" value="1" class="sr-only peer" {{ $group->can_members_send_messages ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-6">
                <a href="{{ route('admin.groups.index') }}" class="flex-1 py-4 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-all text-center text-xs uppercase tracking-widest">
                    Cancelar
                </a>
                <button type="submit" class="flex-[2] py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all text-xs uppercase tracking-widest shadow-lg shadow-blue-500/20">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
