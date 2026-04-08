@extends('layouts.app')

@section('title', 'Diário Alimentar — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1600px] mx-auto px-6">
    <!-- Header Strategy: Glassmorphic Date Navigation -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-2">
            <h1 class="text-4xl font-black tracking-tight text-white leading-tight">
                Diário <span class="text-blue-500">Alimentar</span>
            </h1>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Nutrição Inteligente</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ date('d M, Y', strtotime($date)) }}</span>
            </div>
        </div>
        
        <!-- Futuristic Date Picker -->
        <div class="flex items-center bg-zinc-900/50 backdrop-blur-xl p-2 rounded-[2rem] border border-white/5 shadow-2xl group hover:border-blue-500/30 transition-all">
            <a href="{{ route('diary', ['date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}" class="w-12 h-12 flex items-center justify-center text-zinc-400 hover:bg-white/5 hover:text-white rounded-full transition-all active:scale-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div class="px-8 text-center min-w-[180px]">
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest leading-none mb-1">Selecionado</p>
                <p class="text-white font-black text-lg leading-none">{{ date('d/m/Y', strtotime($date)) }}</p>
            </div>
            <a href="{{ route('diary', ['date' => date('Y-m-d', strtotime($date . ' +1 day'))]) }}" class="w-12 h-12 flex items-center justify-center text-zinc-400 hover:bg-white/5 hover:text-white rounded-full transition-all active:scale-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <!-- Macro Master HUD (Dashboard Cohesion) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Calorie Ring Widget -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl p-8 rounded-[3rem] border border-white/5 relative overflow-hidden group shadow-xl">
             <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Calorias Restantes</p>
                    <h4 class="text-4xl font-black {{ ($calorieTarget - $sumCal) < 0 ? 'text-rose-500' : 'text-white' }} tabular-nums">{{ $calorieTarget - $sumCal }}</h4>
                </div>
                <div class="relative w-20 h-20">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="4" fill="transparent" class="text-zinc-800" />
                        <circle cx="40" cy="40" r="34" stroke="url(#blue_gradient_diary)" stroke-width="6" fill="transparent" 
                            stroke-dasharray="213" 
                            stroke-dashoffset="{{ 213 - (213 * min($sumCal / ($calorieTarget ?: 2000), 1)) }}" 
                            stroke-linecap="round" class="transition-all duration-1000" />
                        <defs><linearGradient id="blue_gradient_diary" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#3b82f6" /><stop offset="100%" stop-color="#10b981" /></linearGradient></defs>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center text-[10px] font-black text-white">
                        {{ number_format(($sumCal / ($calorieTarget ?: 2000)) * 100, 0) }}%
                    </div>
                </div>
             </div>
             <div class="mt-6 flex items-center gap-2">
                 <div class="h-1.5 flex-1 bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 transition-all duration-1000" style="width: {{ min(($sumCal / ($calorieTarget ?: 2000)) * 100, 100) }}%"></div>
                 </div>
                 <span class="text-[10px] text-zinc-500 font-bold">{{ $sumCal }} / {{ $calorieTarget }} kcal</span>
             </div>
        </div>

        @foreach([['label' => 'Proteína', 'val' => $sumP, 'target' => $macroTargets['p'], 'color' => 'blue'], ['label' => 'Carbo', 'val' => $sumC, 'target' => $macroTargets['c'], 'color' => 'purple'], ['label' => 'Gordura', 'val' => $sumF, 'target' => $macroTargets['f'], 'color' => 'amber']] as $m)
        <div class="bg-zinc-900/20 backdrop-blur-xl p-8 rounded-[3rem] border border-white/5 group hover:bg-zinc-900/40 transition-all shadow-xl">
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-4">{{ $m['label'] }}</p>
            <div class="flex items-end justify-between">
                <div>
                    <h4 class="text-3xl font-black text-white leading-none">{{ number_format($m['val'], 0) }}g</h4>
                    <p class="text-[10px] text-zinc-500 font-bold mt-2">Meta: {{ $m['target'] }}g</p>
                </div>
                <div class="w-12 h-16 bg-zinc-950 rounded-2xl p-1.5 flex items-end shadow-inner border border-white/5">
                    <div class="w-full bg-{{ $m['color'] }}-500 rounded-xl transition-all duration-1000 shadow-[0_0_10px_rgba(var(--tw-color-{{ $m['color'] }}-500),0.3)]" style="height: {{ min(($m['val'] / ($m['target'] ?: 1)) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start pb-20">
        <!-- Meal Timeline (Left - 8 cols) -->
        <div class="lg:col-span-12 xl:col-span-8 space-y-8">
            @foreach(['breakfast', 'lunch', 'dinner', 'snack', 'other'] as $mtype)
            @php
                $mealRows = $rows->where('meal_type', $mtype);
                $mealCal = $mealRows->sum('calories');
                $icons = ['breakfast' => '☀️', 'lunch' => '🍲', 'dinner' => '🌙', 'snack' => '🍎', 'other' => '☕'];
            @endphp
            <div class="group relative bg-zinc-900/40 backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden transition-all hover:bg-zinc-900/60 hover:translate-y-[-4px] shadow-2xl">
                <!-- Meal Header -->
                <div class="p-8 flex items-center justify-between border-b border-white/5 relative bg-gradient-to-r from-blue-900/10 to-transparent">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-3xl bg-zinc-950 flex items-center justify-center text-3xl shadow-xl transition-transform group-hover:scale-110 border border-white/10">
                            {{ $icons[$mtype] }}
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight">{{ $mealLabels[$mtype] }}</h3>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest">{{ $mealRows->count() }} Itens</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                                <span class="text-[11px] text-blue-400 font-bold uppercase tracking-widest">{{ $mealCal }} Kcal Consumidas</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Food Items -->
                <div class="divide-y divide-white/5">
                    @forelse($mealRows as $row)
                    <div class="p-8 flex items-center justify-between group/item hover:bg-white/5 transition-all">
                        <div class="flex-1 space-y-1">
                            <h4 class="text-lg font-bold text-white group-hover/item:text-blue-400 transition-colors">{{ $row->food_name }}</h4>
                            <div class="flex items-center gap-4 text-xs font-medium text-zinc-500">
                                <span>{{ $row->amount }} {{ $row->unit }}</span>
                                <span class="h-1 w-1 bg-zinc-700 rounded-full"></span>
                                <span class="px-2 py-0.5 bg-zinc-800 rounded-lg text-zinc-300 font-bold">{{ $row->calories }} kcal</span>
                                <div class="hidden sm:flex items-center gap-3 ml-4 bg-zinc-950 px-3 py-1 rounded-full border border-white/5">
                                    <span class="text-[10px]"><span class="text-blue-400">P</span> {{ $row->protein_g }}g</span>
                                    <span class="text-[10px]"><span class="text-purple-400">C</span> {{ $row->carbs_g }}g</span>
                                    <span class="text-[10px]"><span class="text-amber-400">G</span> {{ $row->fat_g }}g</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 opacity-0 group-hover/item:opacity-100 transition-all">
                            <a href="{{ route('diary', ['date' => $date, 'edit' => $row->id]) }}" class="w-10 h-10 bg-zinc-800 text-zinc-400 hover:text-blue-400 hover:bg-blue-400/10 rounded-xl flex items-center justify-center transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </a>
                            <form method="POST" onsubmit="return confirm('Excluir este alimento?')">
                                @csrf
                                <input type="hidden" name="action" value="delete_food">
                                <input type="hidden" name="entry_date" value="{{ $date }}">
                                <input type="hidden" name="food_id" value="{{ $row->id }}">
                                <button type="submit" class="w-10 h-10 bg-zinc-800 text-zinc-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl flex items-center justify-center transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center group-hover:bg-zinc-950/20 transition-colors">
                        <p class="text-zinc-600 text-sm font-bold uppercase tracking-widest italic opacity-50">Nenhum registro</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>

        <!-- Float Form (Right - 4 cols) -->
        <div class="lg:col-span-12 xl:col-span-4 space-y-10 sticky top-10">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group/form">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-600/5 blur-3xl rounded-full"></div>
                
                <header class="mb-10">
                    <h3 class="text-2xl font-black text-white">{{ $editRow ? 'Editar Registro' : 'Quick Add' }}</h3>
                    <p class="text-zinc-500 text-xs font-bold mt-1 uppercase tracking-widest">Alimente seu objetivo</p>
                </header>

                <form method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if($editRow) <input type="hidden" name="food_edit_id" value="{{ $editRow->id }}"> @endif

                    <div class="space-y-4">
                        <div class="relative group/field">
                            <label class="block text-[10px] text-zinc-500 font-black uppercase mb-2 tracking-widest pl-2 transition-colors group-focus-within/field:text-blue-400">Alimento</label>
                            <input type="text" name="food_name" value="{{ old('food_name', $editRow->food_name ?? '') }}" 
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                                placeholder="O que você comeu?" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase mb-2 tracking-widest pl-2">Quantidade</label>
                                <input type="text" name="amount" value="{{ old('amount', $editRow->amount ?? '100') }}" 
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 transition-all font-mono" placeholder="100">
                            </div>
                            <div>
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase mb-2 tracking-widest pl-2">Unidade</label>
                                <select name="unit" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl p-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 transition-all appearance-none cursor-pointer">
                                    <option value="g">Grama (g)</option>
                                    <option value="ml">Militro (ml)</option>
                                    <option value="un">Unidade</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                             <div class="relative group/field">
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase mb-2 tracking-widest pl-2">Calorias</label>
                                <input type="number" name="calories" value="{{ old('calories', $editRow->calories ?? '') }}" 
                                    class="w-full bg-zinc-950 text-white font-black text-lg border border-white/5 rounded-2xl p-4 outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                                <span class="absolute right-4 bottom-4 text-[10px] text-zinc-600 font-bold uppercase">Kcal</span>
                            </div>
                            <div>
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase mb-2 tracking-widest pl-2">Proteína</label>
                                <input type="number" step="0.1" name="protein_g" value="{{ old('protein_g', $editRow->protein_g ?? '') }}" 
                                    class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-black text-lg outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-bold uppercase mb-2 tracking-widest pl-2">Tipo de Refeição</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($mealLabels as $val => $txt)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="meal_type" value="{{ $val }}" class="peer hidden" {{ $formMeal === $val ? 'checked' : '' }}>
                                    <div class="p-3 text-center rounded-xl bg-zinc-950 border border-white/5 text-[10px] font-black uppercase text-zinc-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-500 transition-all">
                                        {{ $txt }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 space-y-4">
                        <button type="submit" class="w-full relative py-5 bg-white text-zinc-900 font-black rounded-3xl overflow-hidden hover:bg-blue-400 hover:text-white transition-all active:scale-95 shadow-2xl shadow-white/5 group/submit">
                            <span class="relative z-10 transition-transform group-hover/submit:scale-105 block">
                                {{ $editRow ? 'ATUALIZAR REGISTRO' : 'ADICIONAR AO DIÁRIO' }}
                            </span>
                        </button>
                        
                        @if($editRow)
                            <a href="{{ route('diary', ['date' => $date]) }}" class="block text-center text-zinc-500 text-[10px] font-black uppercase tracking-widest hover:text-white transition-colors">Cancelar Edição</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes dashboard-entry {
        from { opacity: 0; transform: translateY(40px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-dashboard-entry {
        animation: dashboard-entry 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    body {
        background-color: #0c0f16;
        background-image: 
            radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.08) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.08) 0, transparent 40%),
            radial-gradient(at 50% 100%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }
</style>
@endsection
