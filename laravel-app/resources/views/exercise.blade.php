@extends('layouts.app')

@section('title', 'Registro de Treino — NexShape')

@section('content')
<div class="py-10 space-y-10 animate-fade-in">
    <!-- Header com Navegação de Data -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <h1 class="text-3xl font-black text-white tracking-tight">Registro de Treino</h1>
            <span class="px-3 py-1 bg-emerald-600/10 text-emerald-400 text-[10px] font-black uppercase rounded-lg border border-emerald-500/20 tracking-widest">Performance HUD</span>
        </div>
        
        <div class="flex items-center bg-zinc-900/50 p-2 rounded-2xl border border-white/5 space-x-2">
            <a href="{{ route('exercise', ['date' => date('Y-m-d', strtotime($date . ' -1 day'))]) }}" class="p-2 text-zinc-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="text-white font-bold text-sm px-4">{{ date('d/m/Y', strtotime($date)) }}</span>
            <a href="{{ route('exercise', ['date' => date('Y-m-d', strtotime($date . ' +1 day'))]) }}" class="p-2 text-zinc-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <!-- Intensity HUD (Resumo do Dia) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-1 bg-zinc-900/20 rounded-[2.5rem] border border-white/5">
        <div class="bg-zinc-900/40 backdrop-blur-xl p-8 rounded-[2rem] flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Tempo de Atividade</p>
                <p class="text-3xl font-black text-white">{{ $sumMin }} <span class="text-lg font-bold text-zinc-500">min</span></p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-600/10 flex items-center justify-center text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-emerald-600/10 backdrop-blur-xl p-8 rounded-[2rem] border border-emerald-500/10 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-[10px] text-emerald-600/60 font-bold uppercase tracking-widest">Total Queimado</p>
                <p class="text-3xl font-black text-emerald-400">{{ $sumBurn }} <span class="text-lg font-bold text-emerald-600/60">kcal</span></p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-600/20 flex items-center justify-center text-emerald-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.99 7.99 0 0120 13a7.99 7.99 0 01-2.343 5.657z"></path></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        <!-- Lista de Exercícios (Col 12) -->
        <div class="lg:col-span-12 xl:col-span-7 space-y-6">
            @forelse($rows as $row)
            <div class="group bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-6 rounded-3xl flex items-center justify-between transition-all hover:bg-zinc-800/60 hover:border-emerald-500/30">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg leading-tight">{{ $row->activity_type }}</h3>
                        <p class="text-zinc-500 text-xs mt-1">{{ $row->duration_min }} min • {{ $row->calories_burned ?? 0 }} kcal</p>
                        @if($row->notes)
                            <span class="inline-block mt-2 px-2 py-0.5 bg-zinc-950 text-zinc-600 text-[10px] rounded border border-white/5">{{ $row->notes }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('exercise', ['date' => $date, 'edit' => $row->id]) }}" class="p-3 text-zinc-500 hover:text-white hover:bg-zinc-800 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </a>
                    <form method="POST" onsubmit="return confirm('Excluir este exercício?')">
                        @csrf
                        <input type="hidden" name="action" value="delete_exercise">
                        <input type="hidden" name="entry_date" value="{{ $date }}">
                        <input type="hidden" name="exercise_id" value="{{ $row->id }}">
                        <button type="submit" class="p-3 text-zinc-500 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="p-20 text-center bg-zinc-900/20 border border-white/5 border-dashed rounded-[2.5rem]">
                <div class="w-16 h-16 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-white font-bold opacity-40">Nenhum treino hoje?</h3>
                <p class="text-zinc-600 text-sm mt-1 italic">Cada movimento aproxima você da sua meta.</p>
            </div>
            @endforelse
        </div>

        <!-- Formulário (Col 4) -->
        <div class="lg:col-span-12 xl:col-span-5 space-y-8 sticky top-32">
            <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl">
                <header class="mb-10">
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ $editRow ? 'Editar Treino' : 'Novo Registro' }}</h3>
                    <p class="text-zinc-500 text-sm mt-1">Insira os detalhes técnicos da atividade.</p>
                </header>

                <form method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if($editRow) <input type="hidden" name="exercise_edit_id" value="{{ $editRow->id }}"> @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mb-3">Atividade / Exercício</label>
                            <input type="text" name="activity_type" value="{{ old('activity_type', $editRow->activity_type ?? '') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-medium outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Ex: Crossfit, Corrida, Supino" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mb-3">Duração (min)</label>
                                <input type="number" name="duration_min" value="{{ old('duration_min', $editRow->duration_min ?? '') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-medium outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-mono" placeholder="45" required>
                            </div>
                            <div>
                                <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mb-3">Calorias Queimadas</label>
                                <input type="number" name="calories_burned" value="{{ old('calories_burned', $editRow->calories_burned ?? '') }}" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-medium outline-none focus:ring-2 focus:ring-emerald-500 transition-all font-mono" placeholder="Opcional">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mb-3">Observações / Notas</label>
                            <textarea name="notes" rows="3" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition-all" placeholder="Ex: Intensidade alta, foco em pernas...">{{ old('notes', $editRow->notes ?? '') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-emerald-600 text-white font-black rounded-3xl hover:bg-emerald-500 transition-all active:scale-[0.98] shadow-2xl shadow-emerald-500/20">
                        {{ $editRow ? 'Atualizar Treino' : 'Registrar Atividade' }}
                    </button>

                    @if($editRow)
                        <a href="{{ route('exercise', ['date' => $date]) }}" class="block text-center text-zinc-500 text-[10px] font-bold uppercase hover:text-white transition-colors tracking-widest">Descartar Edição</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
