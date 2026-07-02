@extends('layouts.app')

@section('title', 'Mapeamento de Dor & EVA — NexShape')

@section('content')
<div class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-zinc-800 pb-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase">Pain <span class="text-rose-500">Mapping</span> & EVA</h1>
            <p class="text-zinc-400 text-sm font-medium">Acompanhe a dor e o progresso da reabilitação física de forma detalhada.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pain Registry Form -->
        <div class="lg:col-span-1 bg-zinc-900/50 border border-zinc-800 rounded-3xl p-6 space-y-6">
            <h2 class="text-xl font-bold text-white tracking-tight">Novo Registro de Dor</h2>

            <form action="{{ route('pain-mapping.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Pain point coordinates simulator (using interactive checkboxes) -->
                <div>
                    <label class="block text-zinc-400 text-xs font-bold uppercase tracking-wider mb-2">Zonas com Dor</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Ombro Esquerdo', 'Ombro Direito', 'Lombar', 'Cervical', 'Joelho Esquerdo', 'Joelho Direito', 'Punho Esquerdo', 'Punho Direito'] as $zone)
                            <label class="flex items-center space-x-3 p-3 rounded-xl bg-zinc-800/40 border border-zinc-800 cursor-pointer hover:border-zinc-700 transition-all">
                                <input type="checkbox" name="pain_points[]" value="{{ $zone }}" class="rounded text-rose-500 focus:ring-rose-500 bg-zinc-900 border-zinc-700">
                                <span class="text-zinc-300 text-xs font-semibold">{{ $zone }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- EVA Level -->
                <div>
                    <label for="eva_level" class="block text-zinc-400 text-xs font-bold uppercase tracking-wider mb-2">Intensidade (Escala EVA: 0 a 10)</label>
                    <input type="range" name="eva_level" id="eva_level" min="0" max="10" value="5" class="w-full h-2 bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-rose-500">
                    <div class="flex justify-between text-[10px] text-zinc-500 font-bold mt-1">
                        <span>0 - Sem Dor</span>
                        <span>5 - Moderada</span>
                        <span>10 - Pior Dor Possível</span>
                    </div>
                </div>

                <!-- Assessment Date -->
                <div>
                    <label for="assessment_date" class="block text-zinc-400 text-xs font-bold uppercase tracking-wider mb-2">Data do Registro</label>
                    <input type="datetime-local" name="assessment_date" id="assessment_date" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-rose-500 text-sm">
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-zinc-400 text-xs font-bold uppercase tracking-wider mb-2">Notas Clínicas</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Descreva os sintomas, limitações ou observações..." class="w-full bg-zinc-900 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-rose-500 text-sm"></textarea>
                </div>

                <button type="submit" class="w-full py-4 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                    Salvar Registro
                </button>
            </form>
        </div>

        <!-- Pain Records List & Visual Evolution -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-zinc-900/50 border border-zinc-800 rounded-3xl p-6">
                <h2 class="text-xl font-bold text-white tracking-tight mb-4">Histórico & Evolução EVA</h2>
                
                @if($painRecords->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <i data-lucide="activity" class="w-12 h-12 text-zinc-700 mb-4"></i>
                        <h3 class="text-white font-bold mb-1">Nenhum registro encontrado</h3>
                        <p class="text-zinc-500 text-xs max-w-xs">Todos os registros de reabilitação e mapeamento de dor serão mostrados aqui.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($painRecords as $record)
                            <div class="p-5 bg-zinc-800/20 border border-zinc-800/80 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-zinc-700 transition-all">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $record->eva_level >= 7 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : ($record->eva_level >= 4 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20') }}">
                                            EVA: {{ $record->eva_level }}/10
                                        </span>
                                        <span class="text-zinc-500 text-[11px] font-semibold">{{ $record->assessment_date->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach((array)$record->pain_points as $point)
                                            <span class="px-2 py-0.5 bg-zinc-900 text-rose-400 border border-zinc-800 text-[10px] font-semibold rounded-md">
                                                {{ $point }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @if($record->notes)
                                        <p class="text-zinc-400 text-xs italic">"{{ $record->notes }}"</p>
                                    @endif
                                </div>
                                <form action="{{ route('pain-mapping.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Deseja excluir este registro?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-zinc-500 hover:text-red-400 rounded-lg hover:bg-zinc-800/50 transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
