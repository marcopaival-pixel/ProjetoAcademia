@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-file-contract text-blue-500"></i>
            Atestados
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-certificate')" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Novo Atestado
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($certificates as $certificate)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 group-hover:text-blue-500 transition-colors">
                            <i class="fas fa-file-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-black text-lg">{{ $certificate->reason }}</h4>
                            <p class="text-zinc-500 text-xs font-bold">{{ $certificate->date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-zinc-800 text-zinc-400 rounded-lg text-[10px] font-black uppercase">
                        {{ $certificate->period ?: 'Período Indefinido' }}
                    </span>
                </div>
                
                <div class="p-4 bg-zinc-800/50 rounded-2xl border border-zinc-800 mb-6">
                    <div class="flex justify-between items-center text-xs text-zinc-400">
                        <span>Início: <strong class="text-white">{{ $certificate->start_date->format('d/m/Y') }}</strong></span>
                        <i class="fas fa-arrow-right mx-2"></i>
                        <span>Fim: <strong class="text-white">{{ $certificate->end_date->format('d/m/Y') }}</strong></span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('professional.patients.medical-records.certificates.download', [$patient->id, $certificate->id]) }}" class="flex-1 py-3 bg-blue-600/10 text-blue-500 rounded-xl text-sm font-black hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i> Baixar PDF
                    </a>
                    <button class="p-3 bg-zinc-800 text-zinc-400 rounded-xl hover:text-white transition-all">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-file-contract text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhum atestado emitido</h4>
                <p class="text-zinc-500 text-sm">Atestados de afastamento ou aptidão física aparecerão aqui.</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $certificates->links() }}
    </div>
</div>

<x-modal name="add-certificate" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.certificates.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Novo Atestado</h2>
        
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Motivo / Finalidade</label>
                <input type="text" name="reason" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: Afastamento por lesão muscular">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Data de Início</label>
                    <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Data de Término</label>
                    <input type="date" name="end_date" value="{{ now()->addDays(7)->format('Y-m-d') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Período (Ex: 7 dias)</label>
                    <input type="text" name="period" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: 10 dias">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Data de Emissão</label>
                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Observações Adicionais</label>
                <textarea name="observations" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Informações que devem constar no atestado..."></textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-500 transition-all">Gerar Atestado</button>
        </div>
    </form>
</x-modal>
@endsection
