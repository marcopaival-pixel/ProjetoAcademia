@extends('layouts.clinic-onboarding')

@section('title', 'Cadastro de Usuários')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-white font-bold text-xl">Equipe Administrativa</h3>
            <p class="text-zinc-500 text-sm">Adicione gestores e secretários para esta clínica.</p>
        </div>
        <a href="{{ route('admin.users.create', ['academy_company_id' => $company->id]) }}" target="_blank"
            class="bg-white/5 hover:bg-white/10 text-white text-xs font-black py-3 px-6 rounded-xl border border-white/10 transition-all flex items-center uppercase tracking-widest">
            <i class="fas fa-plus mr-2 text-blue-500"></i> Novo Usuário
        </a>
    </div>

    <div class="space-y-4">
        @forelse($users as $user)
            <div class="bg-white/5 border border-white/5 rounded-2xl p-6 flex items-center justify-between group hover:border-white/10 transition-all">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-500 font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-white font-bold">{{ $user->name }}</h4>
                        <p class="text-zinc-500 text-xs">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @foreach($user->roles as $role)
                        <span class="text-[10px] uppercase font-black tracking-widest px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full border border-blue-500/20">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 text-center border-2 border-dashed border-white/5 rounded-3xl">
                <i class="fas fa-users text-4xl text-zinc-800 mb-4"></i>
                <p class="text-zinc-600 font-medium">Nenhum administrador cadastrado ainda.</p>
            </div>
        @endforelse
    </div>

    <!-- Componente de Seleção de Músculos (Solicitado) -->
    <div class="mt-12 bg-white/5 border border-white/10 rounded-3xl p-8" 
         x-data="muscleSelector()"
         x-init="init()">
        
        <div class="flex items-center justify-between mb-6">
            <h4 class="text-white font-bold text-lg flex items-center">
                <i class="fas fa-dumbbell mr-3 text-blue-500"></i>
                Músculos Selecionados 
                <span class="ml-2 px-2 py-0.5 bg-blue-500/20 text-blue-400 rounded-lg text-sm" x-text="selectedMuscles.length">0</span>
            </h4>
        </div>

        <!-- Área de Tags -->
        <div class="min-h-[100px] p-6 bg-zinc-950/50 border border-white/5 rounded-2xl mb-4 flex flex-wrap gap-2 items-start transition-all"
             :class="selectedMuscles.length === 0 ? 'border-dashed' : ''">
            
            <template x-if="selectedMuscles.length === 0">
                <p class="text-zinc-600 italic text-sm">Nenhuma área selecionada...</p>
            </template>

            <template x-for="muscle in selectedMuscles" :key="muscle.id">
                <div class="flex items-center bg-blue-500/10 border border-blue-500/30 text-blue-300 px-4 py-2 rounded-xl text-xs font-bold animate-fade-up">
                    <span x-text="muscle.name"></span>
                    <span class="ml-2 text-[10px] text-zinc-500 uppercase font-medium" x-text="muscle.group"></span>
                    <button @click="removeMuscle(muscle.id)" class="ml-3 hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
        </div>

        <!-- Input Autocomplete -->
        <div class="relative">
            <div class="flex items-center bg-zinc-900 border border-white/10 rounded-2xl focus-within:border-blue-500/50 transition-all">
                <i class="fas fa-search ml-5 text-zinc-600"></i>
                <input type="text" 
                       x-model="search" 
                       @input.debounce.300ms="fetchMuscles()"
                       @keydown.enter.prevent="selectFirstResult()"
                       placeholder="Adicionar outro músculo... (Enter)"
                       class="w-full bg-transparent border-none text-white py-5 px-4 focus:ring-0 placeholder:text-zinc-600 font-medium">
                
                <div x-show="loading" class="mr-5">
                    <i class="fas fa-circle-notch fa-spin text-blue-500"></i>
                </div>
            </div>

            <!-- Lista de Resultados -->
            <div x-show="results.length > 0 && search.length > 0" 
                 class="absolute z-50 w-full mt-2 bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden max-h-64 overflow-y-auto"
                 @click.away="results = []">
                <template x-for="result in results" :key="result.id">
                    <button @click="addMuscle(result)" 
                            class="w-full text-left px-6 py-4 hover:bg-white/5 flex items-center justify-between group transition-colors">
                        <div>
                            <span class="text-white font-bold group-hover:text-blue-400 transition-colors" x-text="result.name"></span>
                            <span class="ml-2 text-xs text-zinc-500" x-text="result.group"></span>
                        </div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-zinc-600 bg-zinc-800 px-2 py-1 rounded" x-text="result.type"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Hidden input to save data -->
        <input type="hidden" name="selected_muscles" :value="JSON.stringify(selectedMuscles.map(m => m.id))">
    </div>

    <script>
        function muscleSelector() {
            return {
                search: '',
                loading: false,
                results: [],
                selectedMuscles: @json($selectedMuscles ?? []),

                init() {
                    // Pre-load if needed
                },

                async fetchMuscles() {
                    if (this.search.length < 2) {
                        this.results = [];
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('admin.muscles.search') }}?q=${this.search}`);
                        this.results = await response.json();
                    } catch (e) {
                        console.error('Error fetching muscles:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                addMuscle(muscle) {
                    if (!this.selectedMuscles.find(m => m.id === muscle.id)) {
                        this.selectedMuscles.push(muscle);
                    }
                    this.search = '';
                    this.results = [];
                },

                removeMuscle(id) {
                    this.selectedMuscles = this.selectedMuscles.filter(m => m.id !== id);
                },

                selectFirstResult() {
                    if (this.results.length > 0) {
                        this.addMuscle(this.results[0]);
                    }
                }
            }
        }
    </script>

    <form action="{{ route('admin.clinic-onboarding.step.save', [$company, 4]) }}" method="POST" class="pt-8 border-t border-white/5 flex justify-between">
        @csrf
        <a href="{{ route('admin.clinic-onboarding.step', [$company, 3]) }}" class="text-zinc-500 hover:text-white font-bold py-4 px-8 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-3"></i> Voltar
        </a>
        <button type="submit" class="group bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center shadow-lg shadow-blue-600/20">
            Avançar
            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>
</div>
@endsection
