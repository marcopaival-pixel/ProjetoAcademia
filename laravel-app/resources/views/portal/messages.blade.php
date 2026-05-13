@extends('layouts.app')

@section('title', 'Mensagens — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
    }

    .message-bubble {
        position: relative;
        padding: 1.5rem;
        border-radius: 2rem 2rem 2rem 0.5rem;
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
        border: 1px solid rgba(255,255,255,0.05);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-32">
    <div class="py-10 px-6 max-w-lg mx-auto space-y-10">
        <!-- Header -->
        <header class="flex items-center gap-4">
            <a href="{{ route('patient.portal') }}" class="w-10 h-10 rounded-xl glass-card flex items-center justify-center text-zinc-400">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black tracking-tighter uppercase italic">Mensagens e Avisos</h1>
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Comunicados do seu profissional</p>
            </div>
        </header>

        <!-- Avisos Importantes -->
        <div class="space-y-6">
            @if($alerts)
            <div class="message-bubble relative group">
                <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-1 h-12 bg-[var(--brand-primary)] rounded-full shadow-[0_0_15px_var(--brand-primary)]"></div>
                
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-zinc-900 border border-white/10 flex items-center justify-center overflow-hidden">
                        @if($professional->avatar)
                            <img src="{{ asset('storage/' . $professional->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user-md text-zinc-700"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-white uppercase tracking-widest">{{ $professional->name }}</h4>
                        <span class="text-[8px] font-bold text-zinc-600 uppercase">Profissional Responsável</span>
                    </div>
                </div>

                <p class="text-xs text-zinc-300 leading-relaxed font-medium italic">
                    "{{ $alerts }}"
                </p>
            </div>
            @else
            <div class="glass-card p-12 rounded-[3.5rem] text-center border-dashed border-white/5 bg-transparent">
                <div class="w-16 h-16 bg-zinc-900/50 rounded-full mx-auto flex items-center justify-center text-zinc-700 mb-6">
                    <i class="fas fa-comment-slash text-2xl"></i>
                </div>
                <h5 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Sem avisos novos</h5>
                <p class="text-zinc-700 text-[9px] font-bold px-10 leading-relaxed uppercase tracking-widest">Quando seu profissional tiver um aviso geral ou orientação importante, ela aparecerá aqui.</p>
            </div>
            @endif
        </div>

        <!-- Support Access (Call to action) -->
        <div class="pt-10">
            <div class="glass-card rounded-[2.5rem] p-8 text-center space-y-5">
                <h4 class="text-white text-sm font-black uppercase tracking-widest italic">Dúvidas sobre o tratamento?</h4>
                <p class="text-[10px] text-zinc-500 font-bold px-4 leading-relaxed uppercase tracking-widest">Entre em contato via chat oficial para suporte direto com a equipe.</p>
                <a href="{{ route('messages.index') }}" class="inline-block px-8 py-3 bg-[var(--brand-primary)] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-[var(--brand-primary-glow)] hover:scale-105 transition-transform">
                    Abrir Chat Oficial
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
