@extends('layouts.app')

@section('title', 'Registro de Treino')

@section('content')
<div class="space-y-8 animate-fade-in max-w-[1400px] mx-auto p-4 md:p-8">
    
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div class="flex items-center gap-4">
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter">Registro de Treino</h1>
            <span class="bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-[0.3em] px-4 py-2 rounded-full border border-emerald-500/20">
                Performance HUD
            </span>
        </div>

        <!-- Date Selector -->
        <div class="flex items-center gap-2 bg-zinc-900/50 backdrop-blur-md border border-white/5 p-2 rounded-2xl">
            <a href="{{ route('exercise', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 rounded-xl transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div class="px-6 py-2 text-sm font-black text-white tracking-widest uppercase">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}
            </div>
            <a href="{{ route('exercise', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}" 
               class="w-10 h-10 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/5 rounded-xl transition-all">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
        </div>
    </header>

    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Activity Time -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3rem] flex items-center justify-between">
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] mb-2">Tempo de Atividade</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black text-white tracking-tighter">{{ $stats['total_minutes'] }}</span>
                    <span class="text-sm font-bold text-zinc-500 uppercase">min</span>
                </div>
            </div>
            <div class="w-16 h-16 bg-emerald-500/10 rounded-3xl flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                <i class="far fa-clock text-2xl"></i>
            </div>
        </div>

        <!-- Calories Burned -->
        <div class="bg-emerald-950/20 backdrop-blur-3xl border border-emerald-500/10 p-10 rounded-[3rem] flex items-center justify-between shadow-2xl shadow-emerald-900/10">
            <div>
                <p class="text-[10px] text-emerald-500/60 font-black uppercase tracking-[0.2em] mb-2">Total Queimado</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black text-white tracking-tighter">{{ $stats['total_calories'] }}</span>
                    <span class="text-sm font-bold text-emerald-500/60 uppercase">kcal</span>
                </div>
            </div>
            <div class="w-16 h-16 bg-emerald-500 text-zinc-950 rounded-3xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="fas fa-fire text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Activity List (Left) -->
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="bg-zinc-950/50 border border-dashed border-white/10 rounded-[4rem] min-h-[500px] flex flex-col items-center justify-center p-12 text-center group">
                @if($entries->isEmpty())
                    <div class="w-24 h-24 bg-zinc-900 rounded-[2rem] flex items-center justify-center mb-8 text-zinc-700 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-bolt text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight italic mb-2">Nenhum treino hoje?</h3>
                    <p class="text-zinc-500 text-sm max-w-xs mx-auto leading-relaxed">
                        Cada movimento aproxima você da sua meta. Registre sua primeira atividade ao lado.
                    </p>
                @else
                    <div class="w-full space-y-6">
                        @foreach($entries as $entry)
                            <div class="flex flex-col p-8 bg-zinc-900/60 backdrop-blur-md border border-white/5 rounded-[2.5rem] hover:border-emerald-500/30 transition-all group/item">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 group-hover/item:scale-110 transition-transform">
                                            <i class="fas fa-dumbbell"></i>
                                        </div>
                                        <div class="text-left">
                                            <h4 class="text-lg font-black text-white tracking-tight uppercase italic">{{ $entry->activity_type }}</h4>
                                            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">
                                                {{ $entry->duration_min }} min • {{ $entry->calories_burned ?? 0 }} kcal
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-1">Status</p>
                                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[8px] font-black uppercase tracking-widest rounded-full border border-emerald-500/30">Concluído</span>
                                    </div>
                                </div>

                                {{-- Detalhes das Séries --}}
                                @if($entry->sets_data)
                                    @php($sets = json_decode($entry->sets_data, true))
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                                        @foreach($sets as $idx => $s)
                                            <div class="bg-zinc-950/50 p-3 rounded-2xl border border-white/5">
                                                <span class="block text-[8px] text-zinc-600 font-black uppercase tracking-widest mb-1">{{ $idx + 1 }}ª Série • {{ $s['type'] ?? 'Normal' }}</span>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-white font-black text-sm">{{ $s['kg'] ?: '--' }}kg</span>
                                                    <span class="text-[10px] text-zinc-500">x</span>
                                                    <span class="text-white font-black text-sm">{{ $s['reps'] ?: '--' }}</span>
                                                </div>
                                                <div class="mt-2 flex items-center gap-1">
                                                    <div class="flex-1 h-1 bg-zinc-900 rounded-full overflow-hidden">
                                                        <div class="h-full bg-emerald-500" style="width: {{ ($s['rpe'] ?? 0) * 10 }}%"></div>
                                                    </div>
                                                    <span class="text-[8px] text-emerald-500 font-bold">RPE {{ $s['rpe'] ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                    <p class="text-xs text-zinc-500 italic">"{{ $entry->notes ?: 'Sem notas registradas' }}"</p>
                                    <div class="flex items-center gap-2">
                                        <button class="w-8 h-8 rounded-lg bg-zinc-950 text-zinc-600 hover:text-white transition-colors"><i class="far fa-edit text-xs"></i></button>
                                        <button class="w-8 h-8 rounded-lg bg-zinc-950 text-zinc-600 hover:text-red-500 transition-colors"><i class="far fa-trash-alt text-xs"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- New Registry Form (Right) -->
        <div class="lg:col-span-5 xl:col-span-4" x-data="{ 
            sets: [{ kg: '', reps: '', rpe: 8, type: 'Trabalho' }],
            addSet() {
                this.sets.push({ kg: '', reps: '', rpe: 8, type: 'Trabalho' });
            },
            removeSet(index) {
                this.sets.splice(index, 1);
            }
        }">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-10 rounded-[3.5rem] shadow-2xl">
                <header class="mb-10">
                    <h3 class="text-3xl font-black text-white tracking-tighter italic">Novo Registro</h3>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-2">SaaS Elite Logging • Performance Hub</p>
                </header>

                <form action="{{ route('exercise.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    <input type="hidden" name="sets_data" :value="JSON.stringify(sets)">

                    <!-- Activity Input -->
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-2">Atividade / Exercício</label>
                        <input type="text" name="activity_type" required placeholder="Ex: Supino Inclinado, Agachamento" list="exercises-catalog"
                            class="w-full bg-zinc-950/80 border border-white/5 p-5 rounded-3xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-700">
                        <datalist id="exercises-catalog">
                            @foreach($catalog as $ex)
                                <option value="{{ $ex->name }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Dynamic Sets Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between ml-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em]">Séries / Sets</label>
                            <button type="button" @click="addSet()" class="text-[10px] text-emerald-500 font-black uppercase tracking-widest hover:text-emerald-400 transition-colors">
                                + Adicionar
                            </button>
                        </div>
                        
                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="(set, index) in sets" :key="index">
                                <div class="grid grid-cols-12 gap-2 items-center">
                                    <div class="col-span-1 text-[10px] font-black text-zinc-700" x-text="index + 1"></div>
                                    <div class="col-span-3">
                                        <input type="number" x-model="set.kg" placeholder="kg" class="w-full bg-zinc-950 border border-white/5 p-3 rounded-xl text-white text-xs outline-none focus:border-emerald-500/50 transition-all">
                                    </div>
                                    <div class="col-span-3">
                                        <input type="number" x-model="set.reps" placeholder="reps" class="w-full bg-zinc-950 border border-white/5 p-3 rounded-xl text-white text-xs outline-none focus:border-emerald-500/50 transition-all">
                                    </div>
                                    <div class="col-span-4">
                                        <select x-model="set.type" class="w-full bg-zinc-950 border border-white/5 p-3 rounded-xl text-white text-[10px] uppercase font-black outline-none focus:border-emerald-500/50 transition-all cursor-pointer">
                                            <option>Trabalho</option>
                                            <option>Aquecimento</option>
                                            <option>Falha</option>
                                            <option>Drop-set</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1 text-right">
                                        <button type="button" @click="removeSet(index)" class="text-zinc-700 hover:text-red-500 transition-colors">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                    <div class="col-span-11 col-start-2 flex items-center gap-3 bg-zinc-950/50 p-2 rounded-lg border border-white/5">
                                        <span class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">RPE</span>
                                        <input type="range" x-model="set.rpe" min="1" max="10" step="0.5" class="flex-1 accent-emerald-500 h-1 bg-zinc-900 rounded-lg appearance-none cursor-pointer">
                                        <span class="text-[10px] font-black text-emerald-500 w-6" x-text="set.rpe"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Duration -->
                        <div class="space-y-3">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-2">Duração (m)</label>
                            <input type="number" name="duration_min" required placeholder="45" value="45"
                                class="w-full bg-zinc-950/80 border border-white/5 p-5 rounded-3xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                        </div>
                        <!-- Calories -->
                        <div class="space-y-3">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-2">Gasto (kcal)</label>
                            <input type="number" name="calories_burned" placeholder="Opcional" 
                                class="w-full bg-zinc-950/80 border border-white/5 p-5 rounded-3xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-700">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] ml-2">Observações / Notas</label>
                        <textarea name="notes" rows="3" placeholder="Ex: Intensidade alta, foco em pernas..."
                            class="w-full bg-zinc-950/80 border border-white/5 p-6 rounded-[2rem] text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder:text-zinc-700 resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-6 bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-[0.3em] rounded-[2rem] hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
                        Registrar Performance
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14 !important; }
</style>
@endsection
