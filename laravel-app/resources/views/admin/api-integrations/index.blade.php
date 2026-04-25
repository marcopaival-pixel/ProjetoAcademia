@extends('layouts.admin')

@section('title', 'Configuração de APIs Externas')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex justify-between items-center bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 rounded-[2rem] shadow-2xl">
        <div>
            <h3 class="text-xl font-black text-white tracking-tight italic">Integrações Disponíveis</h3>
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Gerencie chaves e parâmetros de serviços externos</p>
        </div>
        <a href="{{ route('admin.api-integrations.create') }}" class="px-6 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Adicionar API
        </a>
    </div>

    @if($integrations->isEmpty())
        <div class="bg-zinc-900/40 border border-white/5 p-20 rounded-[3rem] text-center">
            <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-plug text-zinc-600 text-3xl"></i>
            </div>
            <h4 class="text-white font-black uppercase tracking-widest text-sm">Nenhuma API Cadastrada</h4>
            <p class="text-zinc-500 text-xs mt-2">Clique no botão acima para começar a integrar serviços.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($integrations as $item)
                <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] shadow-xl hover:border-blue-500/30 transition-all group relative overflow-hidden">
                    <!-- Status Indicator -->
                    <div class="absolute top-0 right-0 p-6">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full {{ $item->status === 'active' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }} border border-current/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest">{{ $item->status === 'active' ? 'Ativa' : 'Inativa' }}</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="w-12 h-12 bg-blue-600/10 rounded-2xl flex items-center justify-center mb-4 border border-blue-500/20">
                            @switch($item->type)
                                @case('exercise') <i class="fas fa-dumbbell text-blue-500 text-lg"></i> @break
                                @case('food') <i class="fas fa-apple-alt text-blue-500 text-lg"></i> @break
                                @case('equipment') <i class="fas fa-cog text-blue-500 text-lg"></i> @break
                                @case('ai') <i class="fas fa-robot text-blue-500 text-lg"></i> @break
                                @case('health') <i class="fas fa-heartbeat text-blue-500 text-lg"></i> @break
                                @case('nutrition') <i class="fas fa-utensils text-blue-500 text-lg"></i> @break
                                @default <i class="fas fa-external-link-alt text-blue-500 text-lg"></i>
                            @endswitch
                        </div>
                        <h4 class="text-white font-black tracking-tight text-lg mb-1">{{ $item->name }}</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $item->typeName }}</p>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="bg-zinc-950/50 p-3 rounded-xl border border-white/5 overflow-hidden">
                            <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mb-1">Base URL</p>
                            <p class="text-xs text-zinc-400 truncate font-mono">{{ $item->base_url }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 bg-zinc-950/50 p-3 rounded-xl border border-white/5">
                                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mb-1">Timeout</p>
                                <p class="text-xs text-white font-bold">{{ $item->timeout }}s</p>
                            </div>
                            <div class="flex-1 bg-zinc-950/50 p-3 rounded-xl border border-white/5">
                                <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mb-1">Auth</p>
                                <p class="text-xs text-white font-bold">{{ $item->api_key ? 'Configurado' : 'Público' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.api-integrations.edit', $item) }}" class="flex-1 px-4 py-3 bg-zinc-800 text-white text-[9px] font-black uppercase tracking-widest rounded-xl border border-white/5 hover:bg-zinc-700 transition-all text-center">
                            Editar
                        </a>
                        <form action="{{ route('admin.api-integrations.toggle-status', $item) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-3 bg-zinc-800 text-zinc-400 rounded-xl border border-white/5 hover:bg-zinc-700 transition-all" title="{{ $item->status === 'active' ? 'Desativar' : 'Ativar' }}">
                                <i class="fas fa-power-off {{ $item->status === 'active' ? 'text-green-500' : 'text-red-500' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.api-integrations.test', $item) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-3 bg-blue-600/10 text-blue-500 rounded-xl border border-blue-500/20 hover:bg-blue-600 hover:text-white transition-all" title="Testar Conexão">
                                <i class="fas fa-play"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.api-integrations.destroy', $item) }}" method="POST" class="inline"
                        data-confirm-delete
                        data-confirm-title="Excluir integração"
                        data-confirm-message="Tem certeza de que deseja excluir esta API? Esta ação não pode ser desfeita.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-red-600/10 text-red-500 rounded-xl border border-red-500/20 hover:bg-red-600 hover:text-white transition-all" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
