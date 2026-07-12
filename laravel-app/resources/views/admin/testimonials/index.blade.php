@extends('layouts.admin')

@section('title', 'Depoimentos (Prova Social)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Marketing & Vendas</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Depoimentos</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.testimonials.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black uppercase transition-all shadow-lg">Novo Depoimento</a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-[#11141b] border border-white/5 p-4 rounded-2xl">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input name="search" value="{{ request('search') }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white uppercase font-bold tracking-wider placeholder:text-zinc-600 focus:outline-none focus:border-emerald-500/50" placeholder="Buscar por nome, profissão ou conteúdo">
            </div>

            <div>
                <select name="status" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs font-bold text-white focus:outline-none focus:border-emerald-500/50">
                    <option value="">Todos os status</option>
                    <option value="approved" @selected(request('status') === 'approved')>Aprovados</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pendentes de Aprovação</option>
                </select>
            </div>

            <div>
                <select name="featured" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2.5 text-xs font-bold text-white focus:outline-none focus:border-emerald-500/50">
                    <option value="">Destaque (Qualquer)</option>
                    <option value="1" @selected(request('featured') === '1')>Destacados</option>
                    <option value="0" @selected(request('featured') === '0')>Não destacados</option>
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-zinc-900 border border-white/10 hover:bg-zinc-800 text-zinc-300 text-xs font-black uppercase tracking-wider transition-all">Filtrar</button>
            
            @if(request()->anyFilled(['search', 'status', 'featured']))
                <a href="{{ route('admin.testimonials.index') }}" class="text-[10px] font-black uppercase text-zinc-500 hover:text-zinc-300 transition-colors">Limpar Filtros</a>
            @endif
        </form>
    </div>

    <!-- Testimonials Grid/Table -->
    <div class="overflow-x-auto bg-[#11141b] border border-white/5 rounded-2xl">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-white/5 bg-zinc-950/20">
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Cliente</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Avaliação (1-5)</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Depoimento</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Status / Moderação</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Destaque</th>
                    <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($testimonials as $item)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <!-- Client Column -->
                        <td class="p-4 align-top">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->avatar_url }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-xl object-cover border border-white/10">
                                <div>
                                    <div class="text-xs font-black text-white uppercase tracking-wider">{{ $item->name }}</div>
                                    <div class="text-[10px] text-zinc-500 font-medium">{{ $item->profession }}</div>
                                    @if($item->city || $item->state)
                                        <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-wider mt-0.5">{{ $item->city }} - {{ $item->state }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Rating Column -->
                        <td class="p-4 align-top">
                            <div class="flex items-center gap-1 text-amber-400 mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $item->rating)
                                        <i class="fas fa-star text-xs"></i>
                                    @else
                                        <i class="far fa-star text-xs text-zinc-700"></i>
                                    @endif
                                @endfor
                            </div>
                        </td>

                        <!-- Testimonial Column -->
                        <td class="p-4 align-top max-w-sm">
                            <p class="text-xs text-zinc-400 font-medium line-clamp-3 italic" title="{{ $item->testimonial }}">
                                "{{ $item->testimonial }}"
                            </p>
                        </td>

                        <!-- Status/Moderation Column -->
                        <td class="p-4 align-top space-y-2">
                            <!-- Approved Status -->
                            <form method="POST" action="{{ route('admin.testimonials.toggle-approve', $item) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $item->approved_at ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->approved_at ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                                    {{ $item->approved_at ? 'Aprovado' : 'Pendente' }}
                                </button>
                            </form>

                            <!-- Public Visibility -->
                            <form method="POST" action="{{ route('admin.testimonials.toggle-visibility', $item) }}" class="block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider {{ $item->is_public ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-zinc-400' }}">
                                    <i class="fas {{ $item->is_public ? 'fa-eye' : 'fa-eye-slash' }} mr-1"></i>
                                    {{ $item->is_public ? 'Público' : 'Oculto' }}
                                </button>
                            </form>
                        </td>

                        <!-- Featured Column -->
                        <td class="p-4 align-top">
                            <form method="POST" action="{{ route('admin.testimonials.toggle-feature', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm transition-transform hover:scale-110">
                                    <i class="fas fa-bookmark {{ $item->featured ? 'text-emerald-500' : 'text-zinc-800 hover:text-zinc-600' }}"></i>
                                </button>
                            </form>
                        </td>

                        <!-- Actions Column -->
                        <td class="p-4 align-top text-right space-y-1.5">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.testimonials.edit', $item) }}" class="px-2.5 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-300 text-[10px] font-black uppercase tracking-wider transition-colors inline-block">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('admin.testimonials.destroy', $item) }}" onsubmit="return confirm('Tem certeza que deseja excluir este depoimento?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-650/10 border border-rose-500/20 text-rose-400 hover:bg-rose-600 hover:text-white text-[10px] font-black uppercase tracking-wider transition-all">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-500">
                                    <i data-lucide="quote" class="w-6 h-6"></i>
                                </div>
                                <p class="text-sm text-zinc-500 font-bold uppercase tracking-wider">Nenhum depoimento cadastrado.</p>
                                <p class="text-xs text-zinc-650 max-w-sm">Cadastre avaliações de clientes para gerar prova social e SEO para a landing page.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $testimonials->links() }}
    </div>
</div>
@endsection
