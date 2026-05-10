@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Gestão de Banners</h1>
            <p class="text-zinc-500 text-sm mt-1">Configure e monitore banners promocionais em todo o ecossistema NexShape.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketing.banners.create') }}" class="px-6 py-3 bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 active:scale-95 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Novo Banner
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-[2rem] backdrop-blur-xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Banners Ativos</p>
            <h3 class="text-3xl font-black text-white">{{ $stats['active_count'] }}</h3>
        </div>
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-[2rem] backdrop-blur-xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Total Visualizações</p>
            <h3 class="text-3xl font-black text-white">{{ number_format($stats['total_views'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-[2rem] backdrop-blur-xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Total Cliques</p>
            <h3 class="text-3xl font-black text-white">{{ number_format($stats['total_clicks'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-[2rem] backdrop-blur-xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">CTR Médio</p>
            <h3 class="text-3xl font-black text-emerald-400">{{ $stats['avg_ctr'] }}%</h3>
        </div>
    </div>

    <!-- Banners Table -->
    <div class="bg-zinc-900/50 border border-white/5 rounded-[2.5rem] overflow-hidden backdrop-blur-xl shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Banner</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status / Prioridade</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Targets</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Métricas</th>
                        <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($banners as $banner)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                @if($banner->image_desktop)
                                    <img src="{{ $banner->image_desktop }}" class="w-16 h-10 object-cover rounded-lg border border-white/10" alt="">
                                @else
                                    <div class="w-16 h-10 rounded-lg bg-zinc-800 flex items-center justify-center border border-white/10">
                                        <i class="fas fa-image text-zinc-600"></i>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-sm font-bold text-white">{{ $banner->title }}</h4>
                                    <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider">{{ $banner->display_type }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $banner->is_active ? 'bg-emerald-500' : 'bg-zinc-700' }}"></span>
                                    <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">{{ $banner->is_active ? 'Ativo' : 'Inativo' }}</span>
                                </div>
                                <div class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Prioridade: {{ $banner->priority }}</div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1">
                                @foreach($banner->roles as $role)
                                    <span class="px-2 py-0.5 bg-zinc-800 text-zinc-400 text-[8px] font-black uppercase tracking-widest rounded-md border border-white/5">
                                        {{ $role->label ?? $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-6">
                                <div class="text-center">
                                    <p class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">Views</p>
                                    <p class="text-xs font-bold text-white">{{ $banner->views_count }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">Clicks</p>
                                    <p class="text-xs font-bold text-white">{{ $banner->clicks_count }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">CTR</p>
                                    <p class="text-xs font-bold text-emerald-400">
                                        {{ $banner->views_count > 0 ? round(($banner->clicks_count / $banner->views_count) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0">
                                <a href="{{ route('admin.marketing.banners.edit', $banner) }}" class="p-2.5 bg-zinc-800 text-zinc-400 hover:text-white rounded-xl border border-white/5 hover:bg-zinc-700 transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.marketing.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este banner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-2.5 bg-zinc-800 text-zinc-400 hover:text-red-400 rounded-xl border border-white/5 hover:bg-red-400/10 transition-all">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-600">
                                    <i class="fas fa-ad text-xl"></i>
                                </div>
                                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Nenhum banner encontrado</p>
                                <a href="{{ route('admin.marketing.banners.create') }}" class="text-blue-400 text-[10px] font-black uppercase tracking-widest hover:underline">Criar primeiro banner</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($banners->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-zinc-900/30">
            {{ $banners->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
