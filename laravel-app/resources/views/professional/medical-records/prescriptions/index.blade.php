@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-prescription-bottle-alt text-blue-500"></i>
            Receitas
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-prescription')" class="px-6 py-3 bg-blue-600 text-white rounded-2xl font-black hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Nova Receita
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($prescriptions as $prescription)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 hover:border-blue-500/30 transition-all group">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black leading-tight">{{ $prescription->medicine }}</h4>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">{{ $prescription->date->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-500">Dosagem:</span>
                        <span class="text-white font-bold">{{ $prescription->dosage ?: '--' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-500">Frequência:</span>
                        <span class="text-white font-bold">{{ $prescription->frequency ?: '--' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-500">Duração:</span>
                        <span class="text-white font-bold">{{ $prescription->duration ?: '--' }}</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button x-data @click="$dispatch('open-modal', 'edit-prescription-{{ $prescription->id }}')" class="flex-1 py-2 bg-zinc-800 text-zinc-400 rounded-xl text-xs font-bold hover:bg-zinc-700 hover:text-white transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-pen"></i> Editar
                    </button>
                    <form method="POST" action="{{ route('professional.patients.medical-records.prescriptions.destroy', [$patient->id, $prescription->id]) }}" onsubmit="return confirm('Remover esta receita?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-2 bg-zinc-800 text-red-400 rounded-xl hover:text-red-300 transition-all">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <x-modal name="edit-prescription-{{ $prescription->id }}" focusable>
                <form method="POST" action="{{ route('professional.patients.medical-records.prescriptions.update', [$patient->id, $prescription->id]) }}" class="p-8">
                    @csrf
                    @method('PUT')
                    <h2 class="text-2xl font-black text-white mb-6">Editar Receita</h2>
                    <div class="space-y-4">
                        <input type="text" name="medicine" value="{{ $prescription->medicine }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="dosage" value="{{ $prescription->dosage }}" placeholder="Dosagem" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                            <input type="text" name="frequency" value="{{ $prescription->frequency }}" placeholder="Frequência" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="duration" value="{{ $prescription->duration }}" placeholder="Duração" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                            <input type="date" name="date" value="{{ $prescription->date?->format('Y-m-d') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <textarea name="observations" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">{{ $prescription->observations }}</textarea>
                    </div>
                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-500 transition-all">Atualizar</button>
                    </div>
                </form>
            </x-modal>
        @empty
            <div class="md:col-span-2 lg:col-span-3 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-prescription-bottle-alt text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhuma receita registrada</h4>
                <p class="text-zinc-500 text-sm">Registre medicamentos, suplementos ou orientações terapêuticas aqui.</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $prescriptions->links() }}
    </div>
</div>

<x-modal name="add-prescription" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.prescriptions.store', $patient->id) }}" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Nova Receita</h2>
        
        <div class="space-y-4">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Medicamento / Suplemento</label>
                <input type="text" name="medicine" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: Creatina Monohidratada">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Dosagem</label>
                    <input type="text" name="dosage" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: 5g">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Frequência</label>
                    <input type="text" name="frequency" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: 1x ao dia">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Duração</label>
                    <input type="text" name="duration" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Ex: Uso contínuo">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase">Data da Prescrição</label>
                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase">Observações</label>
                <textarea name="observations" rows="2" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-blue-500" placeholder="Instruções adicionais..."></textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-500 transition-all">Salvar Receita</button>
        </div>
    </form>
</x-modal>
@endsection

