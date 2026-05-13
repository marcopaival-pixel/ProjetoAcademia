@extends('layouts.onboarding-premium')

@section('title', 'Contato')
@section('step_title', 'Canais de Atendimento')
@section('step_description', 'Como seus clientes e nossa equipe podem entrar em contato com você?')

@section('content')
@if(isset($isLegacy) && $isLegacy)
    <form action="{{ route('onboarding.step3.save') }}" method="POST" class="space-y-12">
        @csrf
        <div class="grid grid-cols-1 gap-4" x-data="{ level: '{{ old('activity_level', $data['activity_level'] ?? '') }}' }">
            @foreach([
                'sedentary' => ['title' => 'Sedentário', 'desc' => 'Trabalho de escritório, pouco ou nenhum exercício.'],
                'lightly_active' => ['title' => 'Levemente Ativo', 'desc' => 'Exercício leve 1-3 dias por semana.'],
                'moderately_active' => ['title' => 'Moderadamente Ativo', 'desc' => 'Exercício moderado 3-5 dias por semana.'],
                'very_active' => ['title' => 'Muito Ativo', 'desc' => 'Exercício pesado 6-7 dias por semana.'],
                'extra_active' => ['title' => 'Atleta / Elite', 'desc' => 'Exercício muito pesado, trabalho físico ou treino 2x/dia.'],
            ] as $key => $info)
                <label class="group relative p-6 rounded-3xl glass glass-hover transition-all cursor-pointer flex items-center justify-between border-2 border-transparent"
                       :class="level === '{{ $key }}' ? 'border-emerald-500 bg-emerald-500/5' : ''"
                       @click="level = '{{ $key }}'">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-500 transition-colors"
                             :class="level === '{{ $key }}' ? 'text-emerald-500' : ''">
                            <input type="radio" name="activity_level" value="{{ $key }}" class="hidden" required :checked="level === '{{ $key }}'">
                            <i class="fas fa-running text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-white font-bold">{{ $info['title'] }}</h4>
                            <p class="text-zinc-500 text-xs">{{ $info['desc'] }}</p>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-full border-2 border-zinc-800 flex items-center justify-center"
                         :class="level === '{{ $key }}' ? 'border-emerald-500 bg-emerald-500' : ''">
                        <i class="fas fa-check text-[10px] text-zinc-950" x-show="level === '{{ $key }}'"></i>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <a href="{{ route('onboarding.step2') }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3" :disabled="!level">
                Continuar <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
@else
    <form action="{{ route('onboarding-premium.step.save', 3) }}" method="POST" class="space-y-12">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Telefone Fixo -->
            <div class="space-y-3" x-data="{ 
                phone: '{{ old('phone', $company->phone ?? '') }}',
                mask() {
                    this.phone = window.formatPhone(this.phone);
                }
            }">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Telefone Principal</label>
                <div class="relative">
                    <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                    <input type="text" name="phone" x-model="phone" @input="mask()"
                        placeholder="(00) 0000-0000"
                        class="w-full input-premium pl-14">
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="space-y-3" x-data="{ 
                whatsapp: '{{ old('whatsapp', $company->whatsapp ?? '') }}',
                mask() {
                    this.whatsapp = window.formatPhone(this.whatsapp);
                }
            }">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">WhatsApp</label>
                <div class="relative">
                    <i class="fab fa-whatsapp absolute left-5 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                    <input type="text" name="whatsapp" x-model="whatsapp" @input="mask()" required
                        placeholder="(00) 90000-0000"
                        class="w-full input-premium pl-14">
                </div>
            </div>

            <!-- E-mail Comercial -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">E-mail Comercial</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                    <input type="email" name="email" value="{{ old('email', $company->responsible_email ?? '') }}" required
                        placeholder="contato@suaempresa.com"
                        class="w-full input-premium pl-14">
                </div>
            </div>

            <!-- Site / Landing Page -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Site / Landing Page</label>
                <div class="relative">
                    <i class="fas fa-globe absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600"></i>
                    <input type="url" name="website" value="{{ old('website', $company->website ?? '') }}"
                        placeholder="https://www.seusite.com"
                        class="w-full input-premium pl-14">
                </div>
            </div>

            <!-- Instagram -->
            <div class="md:col-span-2 space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Instagram</label>
                <div class="relative">
                    <i class="fab fa-instagram absolute left-5 top-1/2 -translate-y-1/2 text-pink-500"></i>
                    <input type="text" name="instagram" value="{{ old('instagram', $company->instagram ?? '') }}"
                        placeholder="@seuusuario"
                        class="w-full input-premium pl-14">
                </div>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <a href="{{ route('onboarding-premium.step', 2) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Voltar
            </a>
            <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3">
                Continuar para Endereço <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
@endif
@endsection
