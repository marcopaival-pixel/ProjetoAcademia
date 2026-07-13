@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-images text-indigo-400"></i>
            Fotos de evolução
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-evolution-photo')" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
            <i class="fas fa-upload"></i> Nova Foto
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        @forelse($photos as $photo)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] overflow-hidden">
                <div class="aspect-[3/4] bg-zinc-950">
                    <img src="{{ route('secure-files.show', ['type' => 'evolution', 'id' => $photo->id]) }}" alt="Foto de evolução" class="w-full h-full object-cover">
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-indigo-500/10 text-indigo-300 rounded-full text-[9px] font-black uppercase tracking-widest">{{ $photo->type }}</span>
                        <span class="text-zinc-500 text-xs font-bold">{{ \Carbon\Carbon::parse($photo->registered_date)->format('d/m/Y') }}</span>
                    </div>
                    @if($photo->weight_kg)
                        <p class="text-white text-sm font-bold">{{ $photo->weight_kg }} kg</p>
                    @endif
                    @if($photo->notes)
                        <p class="text-zinc-500 text-xs leading-relaxed">{{ $photo->notes }}</p>
                    @endif
                    <div class="flex items-center gap-4">
                        <button x-data @click="$dispatch('open-modal', 'edit-evolution-photo-{{ $photo->id }}')" class="text-[10px] font-black uppercase tracking-widest text-indigo-300 hover:text-indigo-200 transition-colors">
                            <i class="fas fa-pen mr-2"></i> Editar
                        </button>
                        <form method="POST" action="{{ route('professional.patients.medical-records.photos.destroy', [$patient->id, $photo->id]) }}" onsubmit="return confirm('Remover esta foto de evolução?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-300 transition-colors">
                                <i class="fas fa-trash-alt mr-2"></i> Remover
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <x-modal name="edit-evolution-photo-{{ $photo->id }}" focusable>
                <form method="POST" action="{{ route('professional.patients.medical-records.photos.update', [$patient->id, $photo->id]) }}" class="p-8">
                    @csrf
                    @method('PUT')
                    <h2 class="text-2xl font-black text-white mb-6">Editar Foto de Evolução</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <select name="type" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
                            @foreach(['front' => 'Frente', 'side' => 'Lateral', 'back' => 'Costas', 'custom' => 'Outro'] as $value => $label)
                                <option value="{{ $value }}" @selected($photo->type === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="registered_date" value="{{ \Carbon\Carbon::parse($photo->registered_date)->toDateString() }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
                        <input type="number" step="0.01" name="weight_kg" value="{{ $photo->weight_kg }}" placeholder="Peso no dia" class="md:col-span-2 w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <textarea name="notes" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500 mt-5">{{ $photo->notes }}</textarea>

                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black hover:bg-indigo-500 transition-all">Atualizar</button>
                    </div>
                </form>
            </x-modal>
        @empty
            <div class="xl:col-span-4 bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                    <i class="fas fa-images text-2xl"></i>
                </div>
                <h4 class="text-white font-bold mb-2">Nenhuma foto registrada</h4>
                <p class="text-zinc-500 text-sm">Adicione fotos padronizadas para comparar a evolução do {{ mb_strtolower($patientLabel) }}.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $photos->links() }}
    </div>
</div>

<x-modal name="add-evolution-photo" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.photos.store', $patient->id) }}" enctype="multipart/form-data" class="p-8">
        @csrf
        <h2 class="text-2xl font-black text-white mb-6">Nova Foto de Evolução</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <label class="space-y-2 md:col-span-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Foto</span>
                <input type="file" name="photo" accept="image/*" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Tipo</span>
                <select name="type" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="front">Frente</option>
                    <option value="side">Lateral</option>
                    <option value="back">Costas</option>
                    <option value="custom">Outro</option>
                </select>
            </label>
            <label class="space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Data</span>
                <input type="date" name="registered_date" value="{{ now()->toDateString() }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
            </label>
            <label class="space-y-2 md:col-span-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Peso no dia</span>
                <input type="number" step="0.01" name="weight_kg" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500">
            </label>
        </div>

        <label class="block space-y-2 mt-5">
            <span class="text-xs font-bold text-zinc-400 uppercase">Observações</span>
            <textarea name="notes" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-indigo-500"></textarea>
        </label>

        <div class="mt-8 flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black hover:bg-indigo-500 transition-all">Salvar Foto</button>
        </div>
    </form>
</x-modal>
@endsection
