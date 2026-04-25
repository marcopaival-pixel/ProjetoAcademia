@extends('layouts.admin')

@section('title', 'Empresas (PDF)')

@section('content')
<div class="space-y-8 animate-fade-in max-w-4xl">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-black text-white">Empresas / <span class="text-purple-400">Academias</span></h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.pdf-suite.index') }}" class="text-[10px] font-black uppercase text-zinc-500">Hub</a>
            <a href="{{ route('admin.pdf-companies.create') }}" class="px-4 py-2 rounded-xl bg-purple-600 text-white text-[10px] font-black uppercase">Nova empresa</a>
        </div>
    </div>
    <div class="bg-zinc-900/40 border border-white/5 rounded-2xl divide-y divide-white/5">
        @forelse($companies as $c)
            <div class="px-6 py-4 flex justify-between items-center">
                <div>
                    <p class="font-bold text-white">{{ $c->name }}</p>
                    <p class="text-[10px] text-zinc-500 mt-1">{{ $c->units_count }} unidade(s) · {{ $c->slug }}</p>
                </div>
                <div class="flex items-center gap-4">
                    @if($c->onboarding_status !== 'completed')
                        <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase border border-blue-500/20">
                            Em Implantação ({{ $c->current_onboarding_step }}/11)
                        </span>
                        <a href="{{ route('admin.clinic-onboarding.index', $c) }}" class="text-[10px] font-black uppercase text-blue-500">Continuar</a>
                    @else
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase border border-emerald-500/20">
                            Ativa
                        </span>
                    @endif
                    <a href="{{ route('admin.impersonate-clinic.start', $c) }}" class="px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase transition-colors">
                        Acessar
                    </a>
                    <a href="{{ route('admin.pdf-companies.edit', $c) }}" class="text-[10px] font-black uppercase text-amber-500">Configurações</a>
                </div>
            </div>
        @empty
            <p class="px-6 py-12 text-center text-zinc-500">Nenhuma empresa. Crie a primeira para multi-tenant.</p>
        @endforelse
    </div>
</div>
@endsection
