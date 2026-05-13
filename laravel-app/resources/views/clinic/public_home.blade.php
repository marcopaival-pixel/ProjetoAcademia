@extends('layouts.app')

@section('title', $clinic->name)

@section('content')
<div class="min-h-screen bg-black text-white" style="--clinic-primary: {{ $clinic->primary_color }}">
    <!-- Hero Section -->
    <div class="relative py-20 px-6 overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[var(--clinic-primary)] rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-[var(--clinic-primary)] rounded-full blur-[120px]"></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10 text-center">
            <div class="flex justify-center mb-8">
                @if($clinic->logo_path)
                    <img src="{{ asset('storage/' . $clinic->logo_path) }}" alt="{{ $clinic->name }}" class="h-24 w-auto drop-shadow-2xl">
                @else
                    <div class="h-24 w-24 rounded-2xl bg-gradient-to-br from-[var(--clinic-primary)] to-black flex items-center justify-center text-4xl font-bold border border-white/10">
                        {{ substr($clinic->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter mb-4 bg-clip-text text-transparent bg-gradient-to-b from-white to-white/50">
                {{ $clinic->name }}
            </h1>
            <p class="text-xl text-zinc-400 max-w-2xl mx-auto mb-10">
                Bem-vindo à nossa clínica. Explore nossos serviços e comece sua jornada para uma vida mais saudável hoje mesmo.
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register', ['clinic' => $clinic->slug]) }}" 
                   class="px-8 py-4 bg-[var(--clinic-primary)] text-black font-bold rounded-full hover:scale-105 transition-all shadow-[0_0_20px_rgba(0,0,0,0.3)] shadow-[var(--clinic-primary)]/20">
                    Cadastre-se Agora
                </a>
                <a href="#servicos" 
                   class="px-8 py-4 bg-white/5 border border-white/10 text-white font-medium rounded-full hover:bg-white/10 transition-all backdrop-blur-md">
                    Ver Serviços
                </a>
            </div>
        </div>
    </div>

    <!-- Stats / Info Section -->
    <div class="py-16 border-y border-white/5 bg-zinc-900/30 backdrop-blur-sm" id="servicos">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
            <div class="group">
                <div class="w-12 h-12 bg-[var(--clinic-primary)]/10 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="text-[var(--clinic-primary)]"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Atendimento de Elite</h3>
                <p class="text-zinc-500">Profissionais altamente qualificados focados no seu resultado.</p>
            </div>
            <div class="group">
                <div class="w-12 h-12 bg-[var(--clinic-primary)]/10 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="zap" class="text-[var(--clinic-primary)]"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Tecnologia Avançada</h3>
                <p class="text-zinc-500">Uso de IA e bioimpedância de última geração para monitoramento.</p>
            </div>
            <div class="group">
                <div class="w-12 h-12 bg-[var(--clinic-primary)]/10 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                    <i data-lucide="shield-check" class="text-[var(--clinic-primary)]"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Segurança de Dados</h3>
                <p class="text-zinc-500">Seus dados protegidos com os mais altos padrões de segurança.</p>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --clinic-primary: {{ $clinic->primary_color }};
    }
</style>
@endsection
