@extends('layouts.app')

@section('title', 'Minhas Receitas')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('patient.medical-records.index') }}" class="text-zinc-500 hover:text-white flex items-center gap-2 font-bold transition-colors">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Prontuário
        </a>
        <h1 class="text-2xl font-black text-white">Minhas <span class="text-emerald-500">Receitas</span></h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($prescriptions as $prescription)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl hover:border-emerald-500/30 transition-all group">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:rotate-12 transition-transform">
                        <i class="fas fa-capsules text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-white leading-tight">{{ $prescription->medicine }}</h3>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">{{ $prescription->date->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-center p-3 bg-zinc-800/50 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Dosagem</span>
                        <span class="text-white font-black text-sm">{{ $prescription->dosage ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-zinc-800/50 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Frequência</span>
                        <span class="text-white font-black text-sm">{{ $prescription->frequency ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-zinc-800/50 rounded-xl border border-zinc-800">
                        <span class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Duração</span>
                        <span class="text-white font-black text-sm">{{ $prescription->duration ?: 'N/A' }}</span>
                    </div>
                </div>

                @if($prescription->observations)
                    <div class="mb-8">
                        <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2">Orientações</p>
                        <p class="text-zinc-400 text-xs italic">{{ $prescription->observations }}</p>
                    </div>
                @endif

                <button class="w-full py-4 bg-zinc-800 text-white font-black rounded-2xl text-sm hover:bg-zinc-700 transition-all flex items-center justify-center gap-2 border border-zinc-700">
                    <i class="fas fa-print"></i> Imprimir Receita
                </button>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2.5rem] p-16 text-center">
                <p class="text-zinc-500 italic">Nenhuma receita prescrita recentemente.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $prescriptions->links() }}
    </div>
</div>
@endsection
