@extends('layouts.app')

@section('title', 'Clínicas Vendidas')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">Minhas <span class="text-emerald-500">Clínicas</span></h1>
            <p class="text-zinc-500 text-sm mt-1">Acompanhe a implantação, o status e as comissões de seus clientes.</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-4">
        <form action="{{ route('representative.clinics.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="bg-zinc-950 border border-zinc-800 text-white text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                    <option value="">Todos</option>
                    <option value="ativa" {{ request('status') === 'ativa' ? 'selected' : '' }}>Ativas</option>
                    <option value="inativa" {{ request('status') === 'inativa' ? 'selected' : '' }}>Inativas</option>
                    <option value="inadimplente" {{ request('status') === 'inadimplente' ? 'selected' : '' }}>Inadimplentes</option>
                    <option value="novas_vendas" {{ request('status') === 'novas_vendas' ? 'selected' : '' }}>Novas Vendas (30 dias)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Data Início</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-zinc-950 border border-zinc-800 text-white text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            </div>
            <div>
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">Data Fim</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-zinc-950 border border-zinc-800 text-white text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
            </div>
            <div>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition-colors">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    @if($clinics->isEmpty())
        <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden p-10 text-center">
            <i data-lucide="hospital" class="w-12 h-12 text-zinc-700 mx-auto mb-4"></i>
            <h3 class="text-lg font-bold text-white">Nenhuma clínica encontrada</h3>
            <p class="text-zinc-500 text-sm mt-2">Você ainda não vendeu para nenhuma clínica ou nenhuma corresponde aos filtros.</p>
        </div>
    @else
        <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-zinc-400">
                    <thead class="text-xs text-zinc-300 uppercase bg-zinc-900">
                        <tr>
                            <th scope="col" class="px-6 py-4 rounded-tl-3xl">Clínica</th>
                            <th scope="col" class="px-6 py-4">Plano Contratado</th>
                            <th scope="col" class="px-6 py-4">Data da Venda</th>
                            <th scope="col" class="px-6 py-4">Valor Base</th>
                            <th scope="col" class="px-6 py-4">Comissão</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4 rounded-tr-3xl">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @foreach($clinics as $clinic)
                            <tr class="hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-white flex items-center gap-3">
                                    @if($clinic->logo_path)
                                        <img src="{{ $clinic->logo_url }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center font-bold">
                                            {{ substr($clinic->name, 0, 1) }}
                                        </div>
                                    @endif
                                    {{ $clinic->name }}
                                </td>
                                <td class="px-6 py-4">{{ $clinic->plan_name ?: 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $clinic->sale_date ? $clinic->sale_date->format('d/m/Y') : '-' }}</td>
                                <td class="px-6 py-4">R$ {{ number_format($clinic->commission_value, 2, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-emerald-500 font-bold">
                                        {{ $clinic->commission_type === 'percentual' ? $clinic->commission_value . '%' : 'Fixa' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($clinic->sale_status === 'ativa')
                                        <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded text-xs font-bold uppercase tracking-wider">Ativa</span>
                                    @elseif($clinic->sale_status === 'inadimplente')
                                        <span class="px-2 py-1 bg-red-500/10 text-red-500 rounded text-xs font-bold uppercase tracking-wider">Inadimplente</span>
                                    @else
                                        <span class="px-2 py-1 bg-zinc-500/10 text-zinc-500 rounded text-xs font-bold uppercase tracking-wider">{{ $clinic->sale_status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('representative.clinics.show', $clinic) }}" class="text-emerald-500 hover:text-emerald-400 font-bold text-xs uppercase tracking-wider">Detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($clinics->hasPages())
                <div class="p-4 border-t border-zinc-800">
                    {{ $clinics->withQueryString()->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
