@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-clipboard-list text-fuchsia-400"></i>
            Protocolos
        </h3>
        <form method="GET" class="flex gap-2">
            <select name="type" class="bg-zinc-900 border border-zinc-800 rounded-2xl px-4 py-3 text-white text-sm font-bold">
                <option value="">Todos</option>
                <option value="medical" @selected($type === 'medical')>Clínico</option>
                <option value="training" @selected($type === 'training')>Treino / Terapêutico</option>
                <option value="nutrition" @selected($type === 'nutrition')>Nutrição</option>
            </select>
            <button class="px-5 py-3 bg-zinc-800 text-white rounded-2xl font-black text-xs uppercase tracking-widest">Filtrar</button>
        </form>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        @forelse($protocols as $protocol)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="px-3 py-1 bg-fuchsia-500/10 text-fuchsia-300 rounded-full text-[9px] font-black uppercase tracking-widest">{{ $protocol->type }}</span>
                        <h4 class="text-white font-black text-xl mt-3">{{ $protocol->name }}</h4>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-1">{{ $protocol->specialty?->nome ?? 'Especialidade geral' }}</p>
                    </div>
                </div>

                @if($protocol->objective)
                    <p class="text-zinc-300 text-sm leading-relaxed">{{ $protocol->objective }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-zinc-950/60 rounded-2xl border border-zinc-800">
                        <p class="text-zinc-500 font-black uppercase">Frequência</p>
                        <p class="text-white font-bold">{{ $protocol->frequency ?? '--' }}</p>
                    </div>
                    <div class="p-3 bg-zinc-950/60 rounded-2xl border border-zinc-800">
                        <p class="text-zinc-500 font-black uppercase">Duração</p>
                        <p class="text-white font-bold">{{ $protocol->duration ?? '--' }}</p>
                    </div>
                </div>

                @if($protocol->protocol)
                    <div class="p-4 bg-zinc-950/60 rounded-2xl border border-zinc-800">
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Protocolo</p>
                        <p class="text-zinc-300 text-sm leading-relaxed whitespace-pre-line">{{ $protocol->protocol }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('professional.patients.medical-records.protocols.apply', [$patient->id, $protocol->id]) }}">
                    @csrf
                    <button class="w-full py-4 bg-fuchsia-600 hover:bg-fuchsia-500 text-white font-black uppercase text-[10px] tracking-widest rounded-2xl transition-all">
                        Aplicar e registrar na evolução
                    </button>
                </form>
            </div>
        @empty
            <div class="xl:col-span-2 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhum protocolo cadastrado</h4>
                <p class="text-zinc-500 text-sm">Cadastre protocolos padrão da clínica para aplicá-los ao prontuário.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $protocols->links() }}
    </div>
</div>
@endsection
