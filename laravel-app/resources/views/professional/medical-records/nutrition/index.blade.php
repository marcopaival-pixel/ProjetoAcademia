@extends('professional.medical-records.layout')

@section('medical-content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h3 class="text-xl font-black text-white flex items-center gap-3">
            <i class="fas fa-apple-alt text-green-400"></i>
            Plano alimentar
        </h3>
        <button x-data @click="$dispatch('open-modal', 'add-meal-template')" class="px-6 py-3 bg-green-600 text-white rounded-2xl font-black hover:bg-green-500 transition-all shadow-lg shadow-green-600/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Modelo de Refeição
        </button>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <form method="POST" action="{{ route('professional.patients.medical-records.nutrition.treatment-plan.store', $patient->id) }}" class="xl:col-span-1 bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-5">
            @csrf
            <div>
                <h4 class="text-white font-black text-lg">Orientações nutricionais</h4>
                <p class="text-zinc-500 text-xs mt-1">Use este bloco para anamnese alimentar, objetivos e conduta principal.</p>
            </div>

            <label class="block space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Diagnóstico / anamnese</span>
                <textarea name="diagnosis" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-green-500">{{ old('diagnosis', $treatmentPlan?->diagnosis) }}</textarea>
            </label>
            <label class="block space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Objetivos</span>
                <textarea name="objectives" rows="3" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-green-500">{{ old('objectives', $treatmentPlan?->objectives) }}</textarea>
            </label>
            <label class="block space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Plano alimentar</span>
                <textarea name="care_plan" rows="5" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-green-500">{{ old('care_plan', $treatmentPlan?->care_plan) }}</textarea>
            </label>
            <label class="block space-y-2">
                <span class="text-xs font-bold text-zinc-400 uppercase">Orientações</span>
                <textarea name="orientations" rows="4" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-green-500">{{ old('orientations', $treatmentPlan?->orientations) }}</textarea>
            </label>
            <label class="flex items-center gap-3 text-sm text-zinc-300 font-bold">
                <input type="checkbox" name="is_active" value="1" class="rounded bg-zinc-800 border-zinc-700 text-green-500" @checked(old('is_active', $treatmentPlan?->is_active ?? true))>
                Plano ativo
            </label>
            <button class="w-full py-4 bg-green-600 hover:bg-green-500 text-white font-black uppercase text-[10px] tracking-widest rounded-2xl transition-all">
                Salvar plano alimentar
            </button>
        </form>

        <div class="xl:col-span-2 space-y-4">
            @forelse($mealTemplates as $template)
                @php
                    $totals = [
                        'calories' => $template->items->sum('calories'),
                        'protein' => $template->items->sum('protein_g'),
                        'carbs' => $template->items->sum('carbs_g'),
                        'fat' => $template->items->sum('fat_g'),
                    ];
                @endphp
                <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-5">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div>
                            <h4 class="text-white font-black text-lg">{{ $template->name }}</h4>
                            <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">{{ $template->updated_at->format('d/m/Y') }}</p>
                            <div class="mt-3 flex items-center gap-4">
                                <button x-data @click="$dispatch('open-modal', 'edit-meal-template-{{ $template->id }}')" class="text-[10px] font-black uppercase tracking-widest text-green-300 hover:text-green-200 transition-colors">
                                    <i class="fas fa-pen mr-2"></i> Editar
                                </button>
                                <form method="POST" action="{{ route('professional.patients.medical-records.nutrition.meal-template.destroy', [$patient->id, $template->id]) }}" onsubmit="return confirm('Remover este modelo alimentar?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-300 transition-colors">
                                        <i class="fas fa-trash-alt mr-2"></i> Remover
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="px-3 py-2 bg-zinc-950 rounded-xl border border-zinc-800"><p class="text-white font-black">{{ $totals['calories'] }}</p><p class="text-[8px] text-zinc-500 font-black uppercase">kcal</p></div>
                            <div class="px-3 py-2 bg-zinc-950 rounded-xl border border-zinc-800"><p class="text-white font-black">{{ number_format($totals['protein'], 1) }}</p><p class="text-[8px] text-zinc-500 font-black uppercase">prot</p></div>
                            <div class="px-3 py-2 bg-zinc-950 rounded-xl border border-zinc-800"><p class="text-white font-black">{{ number_format($totals['carbs'], 1) }}</p><p class="text-[8px] text-zinc-500 font-black uppercase">carb</p></div>
                            <div class="px-3 py-2 bg-zinc-950 rounded-xl border border-zinc-800"><p class="text-white font-black">{{ number_format($totals['fat'], 1) }}</p><p class="text-[8px] text-zinc-500 font-black uppercase">gord</p></div>
                        </div>
                    </div>

                    <div class="divide-y divide-zinc-800/60">
                        @foreach($template->items as $item)
                            <div class="py-3 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-white text-sm font-bold">{{ $item->food_name }}</p>
                                    <p class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">{{ $item->meal_type }}</p>
                                </div>
                                <p class="text-zinc-400 text-xs">{{ $item->calories }} kcal · P {{ $item->protein_g }}g · C {{ $item->carbs_g }}g · G {{ $item->fat_g }}g</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <x-modal name="edit-meal-template-{{ $template->id }}" focusable>
                    <form method="POST" action="{{ route('professional.patients.medical-records.nutrition.meal-template.update', [$patient->id, $template->id]) }}" class="p-8 space-y-6">
                        @csrf
                        @method('PUT')
                        <h2 class="text-2xl font-black text-white">Editar Modelo Alimentar</h2>

                        <input type="text" name="name" value="{{ $template->name }}" required class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-green-500">

                        <div class="space-y-4">
                            @foreach($template->items as $i => $item)
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-3 p-4 bg-zinc-950/50 rounded-2xl border border-zinc-800">
                                    <select name="items[{{ $i }}][meal_type]" class="md:col-span-1 bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                                        @foreach(['breakfast' => 'Café', 'lunch' => 'Almoço', 'snack' => 'Lanche', 'dinner' => 'Jantar', 'other' => 'Outro'] as $value => $label)
                                            <option value="{{ $value }}" @selected($item->meal_type === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="items[{{ $i }}][food_name]" value="{{ $item->food_name }}" required class="md:col-span-2 bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                                    <input type="number" name="items[{{ $i }}][calories]" value="{{ $item->calories }}" class="bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                                    <input type="number" step="0.01" name="items[{{ $i }}][protein_g]" value="{{ $item->protein_g }}" class="bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                                    <input type="number" step="0.01" name="items[{{ $i }}][carbs_g]" value="{{ $item->carbs_g }}" class="bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                                    <input type="hidden" name="items[{{ $i }}][fat_g]" value="{{ $item->fat_g }}">
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end gap-4">
                            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
                            <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-xl font-black hover:bg-green-500 transition-all">Atualizar Modelo</button>
                        </div>
                    </form>
                </x-modal>
            @empty
                <div class="bg-zinc-900 border border-dashed border-zinc-800 rounded-[2rem] p-12 text-center">
                    <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                        <i class="fas fa-utensils text-2xl"></i>
                    </div>
                    <h4 class="text-white font-bold mb-2">Nenhum modelo alimentar</h4>
                    <p class="text-zinc-500 text-sm">Crie modelos de refeições para orientar o acompanhamento nutricional.</p>
                </div>
            @endforelse

            {{ $mealTemplates->links() }}
        </div>
    </div>
</div>

<x-modal name="add-meal-template" focusable>
    <form method="POST" action="{{ route('professional.patients.medical-records.nutrition.meal-template.store', $patient->id) }}" class="p-8 space-y-6">
        @csrf
        <h2 class="text-2xl font-black text-white">Novo Modelo Alimentar</h2>

        <label class="block space-y-2">
            <span class="text-xs font-bold text-zinc-400 uppercase">Nome</span>
            <input type="text" name="name" required placeholder="Ex: Dia base - hipertrofia" class="w-full bg-zinc-800 border-none rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-green-500">
        </label>

        <div class="space-y-4">
            @for($i = 0; $i < 3; $i++)
                <div class="grid grid-cols-1 md:grid-cols-6 gap-3 p-4 bg-zinc-950/50 rounded-2xl border border-zinc-800">
                    <select name="items[{{ $i }}][meal_type]" class="md:col-span-1 bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                        <option value="breakfast">Café</option>
                        <option value="lunch">Almoço</option>
                        <option value="snack">Lanche</option>
                        <option value="dinner">Jantar</option>
                        <option value="other">Outro</option>
                    </select>
                    <input type="text" name="items[{{ $i }}][food_name]" {{ $i === 0 ? 'required' : '' }} placeholder="Alimento / refeição" class="md:col-span-2 bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                    <input type="number" name="items[{{ $i }}][calories]" placeholder="kcal" class="bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                    <input type="number" step="0.01" name="items[{{ $i }}][protein_g]" placeholder="P" class="bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                    <input type="number" step="0.01" name="items[{{ $i }}][carbs_g]" placeholder="C" class="bg-zinc-800 border-none rounded-xl py-3 px-3 text-white text-sm">
                    <input type="hidden" name="items[{{ $i }}][fat_g]" value="0">
                </div>
            @endfor
        </div>

        <div class="flex justify-end gap-4">
            <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-zinc-800 text-zinc-400 rounded-xl font-bold hover:bg-zinc-700 transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-xl font-black hover:bg-green-500 transition-all">Salvar Modelo</button>
        </div>
    </form>
</x-modal>
@endsection
