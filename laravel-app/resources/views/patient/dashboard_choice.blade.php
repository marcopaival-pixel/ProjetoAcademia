@extends('layouts.app')

@section('title', 'Escolha seu Painel — NexShape')

@section('style')
<style>
    :root {
        --brand-primary: #3b82f6;
        --brand-accent: #10b981;
        --brand-primary-glow: rgba(59, 130, 246, 0.3);
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .glass-card:hover {
        transform: translateY(-8px);
        border-color: var(--brand-primary);
        box-shadow: 0 30px 60px -12px rgba(0,0,0,0.5);
    }

    .btn-choice {
        position: relative;
        overflow: hidden;
    }

    .btn-choice::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s;
    }

    .btn-choice:hover::after {
        opacity: 1;
    }

    .animate-entry {
        animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] relative overflow-hidden flex flex-col items-center justify-center py-20 px-6">
    <!-- Background Effects -->
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-[#3b82f6] opacity-[0.08] blur-[150px] rounded-full"></div>
    <div class="absolute top-[40%] -right-[10%] w-[40%] h-[40%] bg-[#10b981] opacity-[0.05] blur-[120px] rounded-full"></div>

    <div class="relative z-10 w-full max-w-lg space-y-12 animate-entry">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-blue-600 to-emerald-500 mx-auto flex items-center justify-center text-white text-4xl shadow-2xl mb-8">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter leading-tight">Como deseja<br>prosseguir hoje?</h1>
            <p class="text-zinc-500 text-sm font-medium">Selecione o modo de visualização para sua saúde.</p>
        </div>

        <!-- Choices -->
        <div class="grid gap-6">
            <!-- Unified View -->
            <a href="{{ route('patient.unified.dashboard') }}" class="glass-card p-8 rounded-[2.5rem] flex items-center gap-6 group btn-choice">
                <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-white font-black text-lg tracking-tight">Visão Geral da Saúde</h3>
                    <p class="text-zinc-500 text-xs">Todos os seus profissionais e dados unificados em um só lugar.</p>
                </div>
                <i class="fas fa-chevron-right text-zinc-700 group-hover:text-white transition-colors"></i>
            </a>

            <!-- Specific Professional -->
            <a href="{{ route('patient.professional.selection') }}" class="glass-card p-8 rounded-[2.5rem] flex items-center gap-6 group btn-choice">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                    <i class="fas fa-user-md text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-white font-black text-lg tracking-tight">Acessar Profissional</h3>
                    <p class="text-zinc-500 text-xs">Acesse o portal específico de um dos seus profissionais.</p>
                </div>
                <i class="fas fa-chevron-right text-zinc-700 group-hover:text-white transition-colors"></i>
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-zinc-600 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair da Conta
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
