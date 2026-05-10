@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
                Perfis e Permissões
            </h1>
            <p class="text-gray-400 mt-1 uppercase text-[10px] font-black tracking-widest">Controle de acesso granular do sistema</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="fas fa-plus"></i> Novo Perfil
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group overflow-hidden relative">
            <!-- Decorative Background Icon -->
            <div class="absolute -right-4 -bottom-4 text-white/5 text-8xl transform -rotate-12 group-hover:scale-110 transition-transform">
                <i class="fas fa-shield-alt"></i>
            </div>

            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-400 text-xl border border-blue-500/20">
                    <i class="fas fa-user-tag"></i>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-blue-600 transition-all">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    @if($role->users_count == 0)
                    <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST" class="inline"
                        data-confirm-delete
                        data-confirm-title="Excluir perfil"
                        data-confirm-message="Tem certeza que deseja excluir este perfil? Esta ação não pode ser desfeita.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-400 hover:text-white hover:bg-red-600 transition-all" title="Excluir perfil">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="relative z-10">
                <h3 class="text-xl font-bold text-white mb-1">{{ $role->label }}</h3>
                <p class="text-xs text-gray-400 mb-6 uppercase tracking-wider font-medium">{{ $role->name }}</p>
                
                <p class="text-gray-300 text-sm mb-6 line-clamp-2 h-10">{{ $role->description ?? 'Sem descrição definida.' }}</p>

                <div class="flex items-center justify-between pt-6 border-t border-white/10">
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-white leading-none">{{ $role->users_count }}</span>
                        <span class="text-[9px] text-gray-500 uppercase font-black mt-1">Usuários</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-black text-blue-400 leading-none">{{ $role->permissions()->count() }}</span>
                        <span class="text-[9px] text-gray-500 uppercase font-black mt-1">Permissões</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
