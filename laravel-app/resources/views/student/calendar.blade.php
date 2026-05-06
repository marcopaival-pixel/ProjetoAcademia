@extends('layouts.app')

@section('title', 'Calendário de Evolução — NexShape')

@section('content')
<div class="py-10 space-y-10 animate-fade-in mx-auto px-4 md:px-0">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-white/5">
        <div class="space-y-4">
            <div class="flex items-center gap-2 text-emerald-500 font-black text-[10px] uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20 w-fit">
                <i class="fas fa-calendar-alt"></i> Portal de Evolução
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-white leading-tight">
                Seu <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-500">Calendário</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Acompanhe sua consistência e visualize todos os seus registros de treino e alimentação em um só lugar.</p>
        </div>

        <div class="flex items-center gap-3 p-2 bg-zinc-900/60 backdrop-blur-xl rounded-[2rem] border border-white/5 shadow-2xl">
            <a href="{{ route('calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="w-12 h-12 flex items-center justify-center bg-zinc-800 text-white rounded-2xl hover:bg-zinc-700 transition-all border border-white/5">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="px-6 text-center">
                <span class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest">{{ $year }}</span>
                <span class="text-xl font-black text-white italic uppercase tracking-tighter">{{ $currentDate->translatedFormat('F') }}</span>
            </div>
            <a href="{{ route('calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="w-12 h-12 flex items-center justify-center bg-zinc-800 text-white rounded-2xl hover:bg-zinc-700 transition-all border border-white/5">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Calendar -->
        <div class="lg:col-span-8 bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-8 shadow-2xl overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="grid grid-cols-7 mb-6">
                @foreach(['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'] as $dayName)
                    <div class="text-center text-[10px] font-black text-zinc-600 uppercase tracking-widest">{{ $dayName }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-3">
                @php
                    $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $endOfCalendar = $currentDate->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    $day = $startOfCalendar->copy();
                @endphp

                @while($day <= $endOfCalendar)
                    @php
                        $dateStr = $day->format('Y-m-d');
                        $isCurrentMonth = $day->month === (int)$month;
                        $isToday = $day->isToday();
                        $hasWorkout = isset($entries[$dateStr]);
                        $hasNutrition = isset($nutritionEntries[$dateStr]);
                    @endphp

                    <div class="relative aspect-square group">
                        <a href="{{ route('dashboard', ['date' => $dateStr]) }}" 
                           class="absolute inset-0 flex flex-col items-center justify-center rounded-3xl border transition-all duration-300 
                           {{ !$isCurrentMonth ? 'opacity-20 pointer-events-none' : 'opacity-100' }}
                           {{ $isToday ? 'bg-emerald-500 border-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.3)] z-10 scale-105' : 'bg-zinc-900/60 border-white/5 hover:border-emerald-500/30 hover:bg-zinc-800' }}">
                            
                            <span class="text-lg font-black {{ $isToday ? 'text-white' : 'text-zinc-400 group-hover:text-white' }} transition-colors">
                                {{ $day->day }}
                            </span>

                            <div class="flex gap-1 mt-2">
                                @if($hasWorkout)
                                    <div class="w-1.5 h-1.5 rounded-full {{ $isToday ? 'bg-white' : 'bg-emerald-500' }}"></div>
                                @endif
                                @if($hasNutrition)
                                    <div class="w-1.5 h-1.5 rounded-full {{ $isToday ? 'bg-zinc-200' : 'bg-blue-500' }}"></div>
                                @endif
                            </div>
                        </a>
                        
                        @if($hasWorkout || $hasNutrition)
                            <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                                <div class="bg-zinc-950 border border-white/10 p-2 rounded-xl shadow-2xl text-[8px] font-black uppercase text-zinc-400 tracking-widest whitespace-nowrap">
                                    @if($hasWorkout) <span class="block text-emerald-400">Treino OK</span> @endif
                                    @if($hasNutrition) <span class="block text-blue-400">Nutrição OK</span> @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @php $day->addDay(); @endphp
                @endwhile
            </div>
        </div>

        <!-- Sidebar: Stats & Legend -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Legend -->
            <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-[3rem] p-10 shadow-2xl">
                <h3 class="text-xl font-black text-white italic uppercase tracking-tighter mb-8">Legenda</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500 shadow-lg shadow-emerald-500/5">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white leading-none">Atividade Física</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Registros de treino</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-500/10 border border-blue-500/20 rounded-2xl flex items-center justify-center text-blue-500 shadow-lg shadow-blue-500/5">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white leading-none">Nutrição</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Diário alimentar</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-white/5">
                        <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white leading-none">Hoje</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Dia atual</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Motivational Card -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-10 rounded-[3.5rem] shadow-2xl text-white relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all"></div>
                
                <div class="relative z-10 text-center space-y-6">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-xl rounded-[1.75rem] flex items-center justify-center mx-auto border border-white/20 shadow-2xl">
                        <i class="fas fa-fire-alt text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black italic tracking-tighter leading-none">FOCO NO PROCESSO</h3>
                        <p class="text-[9px] font-black uppercase tracking-[0.3em] opacity-60 mt-2">Sua jornada NexShape</p>
                    </div>
                    <p class="text-sm font-medium leading-relaxed opacity-80 italic">"Consistência é o que transforma o comum em extraordinário. Cada registro é uma vitória."</p>
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between w-full p-2 pr-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-zinc-900 hover:text-white transition-all shadow-2xl group/btn">
                        <div class="h-10 w-10 bg-zinc-900 text-white rounded-2xl flex items-center justify-center group-hover/btn:bg-white group-hover/btn:text-zinc-900 transition-colors">
                            <i class="fa-solid fa-house text-xs"></i>
                        </div>
                        <span class="text-[10px]">VOLTAR AO DASHBOARD</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fade-in 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>
@endsection
