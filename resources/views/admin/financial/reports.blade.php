@extends('layouts.admin')

@section('title', 'Relatórios Financeiros')

@section('content')
<div class="animate-fade-in space-y-6">
    
    <!-- Header -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-purple-600/10 border border-purple-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                    <span class="text-purple-400 text-[9px] font-black uppercase tracking-widest">Inteligência de Dados e Exportação</span>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter">
                Relatórios <span class="text-purple-500">Consolidados</span>
            </h1>
        </div>

        <!-- Seletor de Tipo de Relatório -->
        <div class="flex bg-[#11141b]/80 p-1 rounded-xl border border-white/5">
            <a href="?type=revenue" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $type == 'revenue' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Receita</a>
            <a href="?type=delinquency" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $type == 'delinquency' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Inadimplência</a>
            <a href="?type=ai_credits" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $type == 'ai_credits' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Créditos IA</a>
            <a href="?type=subscriptions" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $type == 'subscriptions' ? 'bg-purple-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Assinaturas</a>
        </div>
    </div>

    <!-- Filtros de Data -->
    <div class="glass-card p-6 rounded-2xl mb-6">
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <input type="hidden" name="type" value="{{ $type }}">
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Data Inicial</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Data Final</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Empresa/Clínica</label>
                <select name="company_id" class="w-full bg-zinc-950 border border-white/5 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-purple-500 transition-all">
                    <option value="">Todas</option>
                    @foreach(\App\Models\AcademyCompany::all() as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-purple-500 transition-all shadow-xl shadow-purple-600/20">
                Gerar Relatório
            </button>
        </form>
    </div>

    <!-- Resultados -->
    <div class="glass-card rounded-2xl overflow-hidden min-h-[400px]">
        @if($type == 'revenue')
            @include('admin.financial.reports._revenue', ['data' => $data])
        @elseif($type == 'delinquency')
            @include('admin.financial.reports._delinquency', ['data' => $data])
        @elseif($type == 'ai_credits')
            @include('admin.financial.reports._ai_credits', ['data' => $data])
        @elseif($type == 'subscriptions')
            @include('admin.financial.reports._subscriptions', ['data' => $data])
        @elseif($type == 'blocked')
            @include('admin.financial.reports._blocked', ['data' => $data])
        @else
            <div class="p-20 text-center text-zinc-600 italic">
                Selecione um tipo de relatório acima para visualizar os dados.
            </div>
        @endif
    </div>

</div>
@endsection
