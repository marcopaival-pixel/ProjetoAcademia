@extends('layouts.app')

@section('title', 'Nova Avaliação Física')

@section('content')
<div class="py-10 max-w-4xl mx-auto px-4 animate-fade-in-up">
    <!-- Header Strategy -->
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('assessments.index') }}" class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:border-emerald-500/50 transition-all">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-white tracking-tighter uppercase">Nova <span class="text-emerald-500">Avaliação</span></h1>
            <p class="text-zinc-500 text-sm font-medium">Registre suas métricas e acompanhe sua evolução real.</p>
        </div>
    </div>

    <form action="{{ route('assessments.store') }}" method="POST">
        @csrf
        
        <div class="space-y-12">
            <!-- Seção: Info Básica -->
            <x-premium-card title="Informações Gerais" icon="info" iconColor="emerald">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <x-premium-input label="Data" name="assessment_date" type="date" value="{{ date('Y-m-d') }}" required />
                    <x-premium-input label="Peso (kg)" name="weight_kg" type="number" step="0.1" placeholder="0.0" icon="weight" />
                    <x-premium-input label="Gordura (BF %)" name="bf_percent" type="number" step="0.1" placeholder="0" icon="percent" />
                    <x-premium-input label="Músculo (%)" name="muscle_percent" type="number" step="0.1" placeholder="0" icon="activity" />
                    
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 px-1">Enviar para Profissional</label>
                        <select name="professional_id" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white text-sm font-medium focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all appearance-none">
                            <option value="">Apenas registro pessoal</option>
                            @foreach($professionals as $pro)
                                <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-premium-input label="Pressão Arterial" name="blood_pressure" placeholder="120/80" icon="heart" />
                    <x-premium-input label="Freq. Cardíaca (bpm)" name="heart_rate" type="number" placeholder="70" icon="zap" />
                </div>
            </x-premium-card>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Seção: Tronco -->
                <x-premium-card title="Tronco" icon="user" iconColor="emerald">
                    <div class="grid grid-cols-2 gap-6">
                        <x-premium-input label="Pescoço (cm)" name="neck" type="number" step="0.1" />
                        <x-premium-input label="Tórax (cm)" name="chest" type="number" step="0.1" />
                        <x-premium-input label="Cintura (cm)" name="waist" type="number" step="0.1" />
                        <x-premium-input label="Abdômen (cm)" name="abdomen" type="number" step="0.1" />
                        <div class="col-span-2">
                            <x-premium-input label="Quadril (cm)" name="hips" type="number" step="0.1" />
                        </div>
                    </div>
                </x-premium-card>

                <!-- Seção: Membros Superiores -->
                <x-premium-card title="Membros Superiores" icon="armchair" iconColor="emerald">
                    <div class="grid grid-cols-2 gap-6">
                        <x-premium-input label="Braço Esq. (cm)" name="bicep_l" type="number" step="0.1" />
                        <x-premium-input label="Braço Dir. (cm)" name="bicep_r" type="number" step="0.1" />
                        <x-premium-input label="Antebraço Esq. (cm)" name="forearm_l" type="number" step="0.1" />
                        <x-premium-input label="Antebraço Dir. (cm)" name="forearm_r" type="number" step="0.1" />
                    </div>
                </x-premium-card>
            </div>

            <!-- Seção: Membros Inferiores -->
            <x-premium-card title="Membros Inferiores" icon="leg" iconColor="emerald">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <x-premium-input label="Coxa Esq. (cm)" name="thigh_l" type="number" step="0.1" />
                    <x-premium-input label="Coxa Dir. (cm)" name="thigh_r" type="number" step="0.1" />
                    <x-premium-input label="Panturrilha Esq. (cm)" name="calf_l" type="number" step="0.1" />
                    <x-premium-input label="Panturrilha Dir. (cm)" name="calf_r" type="number" step="0.1" />
                </div>
            </x-premium-card>

            <x-premium-card title="Objetivos e Rotina" icon="target" iconColor="emerald">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-premium-input label="Peso Desejado (kg)" name="target_weight_kg" type="number" step="0.1" value="{{ auth()->user()->profile->target_weight_kg ?? '' }}" icon="target" />
                    
                    <div>
                        <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 px-1">Nível Físico</label>
                        <select name="physical_level" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white text-sm font-medium focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all appearance-none">
                            <option value="beginner" {{ (auth()->user()->profile->physical_level ?? '') == 'beginner' ? 'selected' : '' }}>Iniciante</option>
                            <option value="intermediate" {{ (auth()->user()->profile->physical_level ?? '') == 'intermediate' ? 'selected' : '' }}>Intermediário</option>
                            <option value="advanced" {{ (auth()->user()->profile->physical_level ?? '') == 'advanced' ? 'selected' : '' }}>Avançado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 px-1">Local de Treino</label>
                        <select name="training_location" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white text-sm font-medium focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all appearance-none">
                            <option value="gym" {{ (auth()->user()->profile->training_location ?? '') == 'gym' ? 'selected' : '' }}>Academia</option>
                            <option value="home" {{ (auth()->user()->profile->training_location ?? '') == 'home' ? 'selected' : '' }}>Casa</option>
                            <option value="outdoor" {{ (auth()->user()->profile->training_location ?? '') == 'outdoor' ? 'selected' : '' }}>Ar Livre</option>
                        </select>
                    </div>

                    <x-premium-input label="Horas de Sono" name="sleep_hours" type="number" value="{{ auth()->user()->profile->sleep_hours ?? '' }}" icon="moon" />
                    <x-premium-input label="Qualidade Alimentação (1-10)" name="nutrition_quality" type="number" min="1" max="10" value="{{ auth()->user()->profile->nutrition_quality ?? '' }}" icon="utensils" />
                    <x-premium-input label="Tempo Disponível (min/dia)" name="available_daily_time_mins" type="number" value="{{ auth()->user()->profile->available_daily_time_mins ?? '' }}" icon="clock" />
                </div>
            </x-premium-card>

            <x-premium-card title="Anotações e Restrições" icon="message-square" iconColor="emerald">
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 px-1">Restrições Médicas / Lesões</label>
                        <textarea name="fitness_notes" rows="3" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white text-sm font-medium focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all" placeholder="Ex: Hérnia de disco, Labirintite, etc.">{{ auth()->user()->profile->fitness_notes ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 px-1">Observações da Avaliação</label>
                        <textarea name="notes" rows="3" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white text-sm font-medium focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all" placeholder="Sentiu alguma diferença? Novo protocolo?"></textarea>
                    </div>
                </div>
            </x-premium-card>

            <x-premium-card title="Inteligência NexShape" icon="sparkles" iconColor="emerald">
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10">
                    <div class="flex-shrink-0">
                        <input type="checkbox" name="generate_ai_training" id="generate_ai_training" class="w-6 h-6 rounded-lg bg-zinc-950 border-zinc-800 text-emerald-500 focus:ring-emerald-500/20 transition-all">
                    </div>
                    <div class="flex-1">
                        <label for="generate_ai_training" class="text-sm font-bold text-white block">Gerar Plano de Treino via IA</label>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest">O NexBot criará um treino personalizado baseado nesta avaliação.</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10 mt-4">
                    <div class="flex-shrink-0">
                        <input type="checkbox" name="generate_ai_meal_plan" id="generate_ai_meal_plan" class="w-6 h-6 rounded-lg bg-zinc-950 border-zinc-800 text-emerald-500 focus:ring-emerald-500/20 transition-all">
                    </div>
                    <div class="flex-1">
                        <label for="generate_ai_meal_plan" class="text-sm font-bold text-white block">Gerar Sugestões Alimentares via IA</label>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest">Receba sugestões de refeições ajustadas às suas novas metas.</p>
                    </div>
                </div>
            </x-premium-card>

            <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
                <x-premium-button type="submit" variant="primary" size="lg" class="w-full sm:w-auto px-12">
                    SALVAR AVALIAÇÃO
                </x-premium-button>
                <x-premium-button variant="secondary" size="lg" class="w-full sm:w-auto px-12" href="{{ route('assessments.index') }}">
                    CANCELAR
                </x-premium-button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
