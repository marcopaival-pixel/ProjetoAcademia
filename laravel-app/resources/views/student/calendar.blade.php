@extends('layouts.app')

@section('title', 'Calendário de Evolução — NexShape')

@section('content')
<div class="py-10 space-y-10 animate-fade-in mx-auto px-4 md:px-0" x-data="{ 
    modalOpen: false, 
    selectedDateStr: '', 
    selectedDateFormatted: '',
    selectedExercises: [], 
    selectedNutrition: [],
    entriesData: {{ \Illuminate\Support\Js::from($entries) }},
    nutritionData: {{ \Illuminate\Support\Js::from($nutritionEntries) }},
    openModal(dateStr, dateFormatted) {
        this.selectedDateStr = dateStr;
        this.selectedDateFormatted = dateFormatted;
        this.selectedExercises = this.entriesData[dateStr] ? this.entriesData[dateStr].items : [];
        this.selectedNutrition = this.nutritionData[dateStr] ? this.nutritionData[dateStr].items : [];
        this.modalOpen = true;
    }
}">
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
                        <button type="button" @click="openModal('{{ $dateStr }}', '{{ $day->translatedFormat('d \d\e F \d\e Y') }}')" 
                           class="w-full absolute inset-0 flex flex-col items-center justify-center rounded-3xl border transition-all duration-300 outline-none
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
                        </button>
                        
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

    <!-- Daily Details Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <!-- Modal Container -->
         <div class="bg-zinc-900 border border-white/10 rounded-[2rem] w-full max-w-2xl shadow-2xl overflow-hidden relative"
              @click.outside="modalOpen = false"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 translate-y-4 scale-95"
              x-transition:enter-end="opacity-100 translate-y-0 scale-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 translate-y-0 scale-100"
              x-transition:leave-end="opacity-0 translate-y-4 scale-95">
              
              <!-- Header -->
              <div class="flex items-center justify-between p-6 border-b border-white/5 bg-zinc-900/50">
                  <div>
                      <h3 class="text-2xl font-black text-white italic tracking-tighter uppercase" x-text="selectedDateFormatted"></h3>
                      <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Resumo do dia</p>
                  </div>
                  <button @click="modalOpen = false" class="w-10 h-10 rounded-xl bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-colors">
                      <i class="fas fa-times"></i>
                  </button>
              </div>

              <!-- Content -->
              <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar space-y-8">
                  
                  <!-- Treinos -->
                  <div>
                      <h4 class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                          <i class="fas fa-dumbbell"></i> Atividades Físicas (<span x-text="selectedExercises.length"></span>)
                      </h4>
                      
                      <template x-if="selectedExercises.length > 0">
                          <div class="space-y-3">
                              <template x-for="exercise in selectedExercises" :key="exercise.id">
                                  <div class="bg-zinc-950/50 border border-white/5 rounded-2xl p-4 flex flex-col gap-2">
                                      <div class="flex justify-between items-start">
                                          <strong class="text-white text-sm font-bold uppercase tracking-wide" x-text="exercise.activity_type || 'Treino'"></strong>
                                          <span class="text-xs text-zinc-500 font-medium" x-text="exercise.duration_min + ' min'"></span>
                                      </div>
                                      <div class="flex items-center gap-4 mt-2">
                                          <div class="text-[10px] text-emerald-400 font-black tracking-widest bg-emerald-400/10 px-2 py-1 rounded-lg" x-show="exercise.calories_burned">
                                              <i class="fas fa-fire mr-1"></i> <span x-text="exercise.calories_burned + ' kcal'"></span>
                                          </div>
                                          <div class="text-[10px] text-zinc-400 font-medium" x-show="exercise.notes" x-text="exercise.notes"></div>
                                      </div>
                                  </div>
                              </template>
                          </div>
                      </template>
                      
                      <template x-if="selectedExercises.length === 0">
                          <div class="text-center p-6 bg-zinc-950/30 rounded-2xl border border-white/5 border-dashed">
                              <i class="fas fa-bed text-zinc-600 text-2xl mb-2"></i>
                              <p class="text-xs text-zinc-500 font-medium">Nenhum treino registrado neste dia.</p>
                          </div>
                      </template>
                  </div>

                  <!-- Nutrição -->
                  <div>
                      <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                          <i class="fas fa-utensils"></i> Registros de Nutrição (<span x-text="selectedNutrition.length"></span>)
                      </h4>
                      
                      <template x-if="selectedNutrition.length > 0">
                          <div class="space-y-3">
                              <template x-for="food in selectedNutrition" :key="food.id">
                                  <div class="bg-zinc-950/50 border border-white/5 rounded-2xl p-4 flex flex-col gap-2">
                                      <div class="flex justify-between items-start">
                                          <strong class="text-white text-sm font-bold capitalize" x-text="food.food_name || food.meal_type || 'Refeição'"></strong>
                                          <span class="text-xs text-blue-400 font-medium bg-blue-500/10 px-2 py-1 rounded-lg" x-text="food.calories + ' kcal'"></span>
                                      </div>
                                      <div class="grid grid-cols-3 gap-2 mt-2">
                                          <div class="text-[9px] text-zinc-400 font-bold tracking-widest bg-zinc-900 p-2 rounded-lg text-center">
                                              <span class="text-red-400 block mb-1">PROT</span> <span x-text="(food.protein_g || 0) + 'g'"></span>
                                          </div>
                                          <div class="text-[9px] text-zinc-400 font-bold tracking-widest bg-zinc-900 p-2 rounded-lg text-center">
                                              <span class="text-yellow-400 block mb-1">CARB</span> <span x-text="(food.carbs_g || 0) + 'g'"></span>
                                          </div>
                                          <div class="text-[9px] text-zinc-400 font-bold tracking-widest bg-zinc-900 p-2 rounded-lg text-center">
                                              <span class="text-orange-400 block mb-1">GORD</span> <span x-text="(food.fat_g || 0) + 'g'"></span>
                                          </div>
                                      </div>
                                  </div>
                              </template>
                          </div>
                      </template>
                      
                      <template x-if="selectedNutrition.length === 0">
                          <div class="text-center p-6 bg-zinc-950/30 rounded-2xl border border-white/5 border-dashed">
                              <i class="fas fa-leaf text-zinc-600 text-2xl mb-2"></i>
                              <p class="text-xs text-zinc-500 font-medium">Nenhum registro alimentar neste dia.</p>
                          </div>
                      </template>
                  </div>

              </div>
              
              <!-- Footer Actions -->
              <div class="p-6 border-t border-white/5 bg-zinc-900/80 flex items-center justify-between">
                  <a :href="'{{ route('dashboard') }}?date=' + selectedDateStr" class="text-[10px] text-zinc-400 font-black uppercase tracking-widest hover:text-white transition-colors">
                      Ir para o Dashboard
                  </a>
                  <button @click="modalOpen = false" class="px-6 py-2 bg-emerald-500 text-zinc-900 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-500/20">
                      Fechar
                  </button>
              </div>
         </div>
    </div>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fade-in 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>
@endsection
