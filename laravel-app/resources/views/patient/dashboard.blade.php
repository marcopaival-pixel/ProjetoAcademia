@extends('layouts.app')

@section('title', 'Meu Plano — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --brand-primary-glow: {{ $branding['primary_color'] }}40;
    }
    
    .btn-brand {
        background-color: var(--brand-primary);
        box-shadow: 0 10px 15px -3px var(--brand-primary-glow);
    }
    
    .border-brand {
        border-color: var(--brand-primary);
    }

    .text-brand {
        color: var(--brand-primary);
    }

    .bg-brand-soft {
        background-color: var(--brand-primary-glow);
    }
</style>
@endsection

@section('content')
<div class="py-6 max-w-md mx-auto space-y-8 animate-fade-in">
    <!-- Header Branding -->
    <div class="flex flex-col items-center text-center space-y-4">
        @if($branding['logo_url'])
            <img src="{{ $branding['logo_url'] }}" alt="Logo" class="h-12 w-auto">
        @else
            <div class="w-12 h-12 rounded-2xl bg-brand-soft border border-brand flex items-center justify-center text-brand font-black">
                {{ substr($branding['clinic_name'], 0, 1) }}
            </div>
        @endif
        <div>
            <p class="text-[10px] text-zinc-500 uppercase font-bold tracking-widest">Acompanhamento Profissional</p>
            <h1 class="text-xl font-bold text-white">{{ $branding['clinic_name'] }}</h1>
        </div>
    </div>

    <!-- Greeting & Day Summary -->
    <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 p-6 rounded-3xl relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-brand-soft blur-3xl rounded-full"></div>
        
        <div class="relative z-10 flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-bold text-white">Olá, Carlos! 👋</h2>
                <p class="text-zinc-400 text-sm">Faltam 2 tarefas para fechar o dia.</p>
            </div>
            <div class="w-16 h-16 rounded-full border-4 border-zinc-800 flex items-center justify-center relative">
                <svg class="w-full h-full -rotate-90">
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" class="text-zinc-800" />
                    <circle cx="32" cy="32" r="28" stroke="var(--brand-primary)" stroke-width="4" fill="transparent" stroke-dasharray="175" stroke-dashoffset="50" class="transition-all duration-700" />
                </svg>
                <span class="absolute text-xs font-bold text-white">71%</span>
            </div>
        </div>
    </div>

    <!-- Checklist Diário -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-widest px-2">Agenda de Hoje</h3>
        
        <div class="space-y-3">
            @foreach($dailyTasks as $task)
            <div class="group bg-zinc-900/40 border {{ $task['done'] ? 'border-emerald-500/20' : 'border-white/5' }} p-4 rounded-2xl flex items-center justify-between hover:bg-zinc-800/50 transition-all cursor-pointer">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $task['done'] ? 'bg-emerald-500/10 text-emerald-400' : 'bg-zinc-800 text-zinc-500 group-hover:bg-brand-soft group-hover:text-brand' }} transition-colors">
                        @if($task['type'] == 'meal')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        @elseif($task['type'] == 'workout')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">{{ $task['title'] }}</p>
                        <p class="text-zinc-500 text-[10px] uppercase font-bold">{{ $task['time'] }}</p>
                    </div>
                </div>
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all {{ $task['done'] ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-zinc-800 group-hover:border-brand' }}">
                    @if($task['done'])
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Action / Message -->
    <div class="bg-brand-soft border border-brand/20 p-6 rounded-3xl flex items-center gap-4">
        <div class="flex-1">
            <h4 class="text-brand font-bold text-sm">Dúvida sobre o plano?</h4>
            <p class="text-zinc-400 text-xs">Fale agora com seu profissional.</p>
        </div>
        <button class="p-3 btn-brand text-white rounded-2xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </button>
    </div>

    <!-- Navigation Bar Mobile-Style -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-md bg-zinc-900/80 backdrop-blur-2xl border border-white/10 p-2 rounded-[2rem] flex items-center justify-around shadow-2xl z-50">
        <a href="#" class="p-4 text-brand">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a2 2 0 002 2h2a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h2a2 2 0 002-2v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
        </a>
        <a href="#" class="p-4 text-zinc-500 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </a>
        <div class="w-14 h-14 btn-brand text-white rounded-full flex items-center justify-center -mt-12 shadow-2xl border-4 border-[#0b0e14]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </div>
        <a href="#" class="p-4 text-zinc-500 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </a>
        <a href="#" class="p-4 text-zinc-500 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </a>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    body {
        background-color: #0b0e14;
        padding-bottom: 100px;
    }
</style>
@endsection
