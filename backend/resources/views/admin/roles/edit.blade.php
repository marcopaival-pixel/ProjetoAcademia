@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent leading-tight">
                Editar Perfil: {{ $role->label }}
            </h1>
            <p class="text-gray-400 mt-2 uppercase text-xs font-black tracking-widest leading-none">Atualize o nome e as permissões de acesso</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="group flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl transition-all duration-300 shadow-xl">
            <i class="fas fa-arrow-left text-blue-400 group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-gray-300 font-bold text-sm leading-none">Voltar</span>
        </a>
    </div>

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10 space-y-8">
                <h3 class="text-xl font-bold text-white flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 text-sm border border-blue-500/20">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Informações Básicas
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Identificador Único (Slug)</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all placeholder:text-gray-700 opacity-60 cursor-not-allowed"
                            readonly>
                        @error('name') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Rótulo Exibível (Label)</label>
                        <input type="text" name="label" value="{{ old('label', $role->label) }}" required
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all placeholder:text-gray-700"
                            placeholder="ex: Gerente Comercial">
                        @error('label') <p class="text-red-400 text-xs mt-2 ml-2 italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 mb-3 uppercase tracking-widest ml-1">Descrição Breve</label>
                        <textarea name="description" rows="3"
                            class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/40 transition-all placeholder:text-gray-700 resize-none">{{ old('description', $role->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] p-10">
                <h3 class="text-xl font-bold text-white flex items-center gap-3 mb-8">
                    <div class="w-8 h-8 bg-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-400 text-sm border border-indigo-500/20">
                        <i class="fas fa-lock-open"></i>
                    </div>
                    Permissões Atribuídas
                </h3>

                <div class="max-h-[380px] overflow-y-auto pr-4 space-y-10 custom-scrollbar">
                    @foreach($permissions as $group => $items)
                    <div>
                        <h4 class="text-indigo-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 flex items-center gap-3">
                            <span class="w-8 h-px bg-indigo-500/20"></span>
                            {{ strtoupper($group) }}
                        </h4>
                        <div class="space-y-3">
                            @foreach($items as $permission)
                            <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-2xl border border-white/[0.03] hover:bg-white/[0.03] hover:border-white/10 transition-all">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                    class="hidden peer" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                <div class="w-6 h-6 rounded-lg border-2 border-white/10 flex items-center justify-center peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all shadow-lg group-hover:scale-105">
                                    <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                                <div>
                                    <span class="block text-gray-300 font-bold text-sm group-hover:text-white transition-colors">{{ $permission->label }}</span>
                                    <span class="block text-gray-500 text-[10px]">{{ $permission->name }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-white/5 gap-6">
            <button type="reset" class="px-8 py-4 text-gray-400 hover:text-white transition-all uppercase text-xs font-black tracking-widest shadow-xl">Limpar Alterações</button>
            <button type="submit" class="px-12 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black uppercase text-xs tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/20 transform hover:-translate-y-1 transition-all duration-300">
                Salvar Atualizações
            </button>
        </div>
    </form>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59, 130, 246, 0.4); }
</style>
@endsection
