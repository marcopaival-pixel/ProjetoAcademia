@extends('layouts.app')

@section('title', 'Histórico de Acessos — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --card-bg: rgba(255, 255, 255, 0.6);
        --glass-border: rgba(0, 0, 0, 0.05);
        --card-hover-bg: rgba(255, 255, 255, 0.9);
        --glass-hover-border: rgba(0, 0, 0, 0.1);
        --shadow-color: rgba(0,0,0,0.05);
    }
    
    .dark {
        --card-bg: rgba(20, 22, 28, 0.6);
        --glass-border: rgba(255, 255, 255, 0.05);
        --card-hover-bg: rgba(30, 32, 40, 0.8);
        --glass-hover-border: rgba(255, 255, 255, 0.1);
        --shadow-color: rgba(0,0,0,0.4);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .glass-card:hover {
        transform: translateY(-2px);
        background: var(--card-hover-bg);
        border-color: var(--glass-hover-border);
        box-shadow: 0 20px 40px var(--shadow-color);
    }

    .status-badge {
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-12 animate-fade-in pb-20">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-10 border-b border-zinc-200 dark:border-white/5">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-4">
                 <a href="{{ route('patient.portal') }}" class="hover:text-zinc-900 dark:hover:text-white transition-colors">Painel</a>
                 <i class="fas fa-chevron-right text-[7px] text-zinc-300 dark:text-zinc-800"></i>
                 <span class="text-zinc-900 dark:text-zinc-400">Privacidade</span>
            </nav>
            <h1 class="text-5xl font-black text-zinc-900 dark:text-white tracking-tighter leading-none">Logs de Acesso</h1>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Transparência e Conformidade LGPD</p>
        </div>
        
        <div class="flex items-center gap-4 bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-white/5 px-6 py-4 rounded-3xl">
             <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <i class="fas fa-shield-halved text-lg"></i>
             </div>
             <div>
                <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Proteção Ativa</p>
                <p class="text-[11px] text-zinc-900 dark:text-white font-bold">Seus dados estão seguros</p>
             </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div class="bg-blue-50 dark:bg-blue-600/5 border border-blue-100 dark:border-blue-500/10 p-6 rounded-3xl flex items-start gap-5">
            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
            <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Este relatório detalha todas as interações e visualizações realizadas sobre seus dados pessoais e clínicos dentro da plataforma NexShape. Mantemos este registro por <span class="text-zinc-900 dark:text-white font-bold">5 anos</span> para garantir a rastreabilidade total conforme exigido pela LGPD.
            </p>
        </div>

        <!-- Logs Timeline -->
        <div class="space-y-4 mt-6">
            @forelse($logs as $log)
                <div class="glass-card p-6 rounded-[2rem] flex items-center gap-6 group">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center text-xl text-zinc-400 dark:text-zinc-700 border border-zinc-200/50 dark:border-white/5 shadow-inner">
                        @php
                            $icon = match($log->action) {
                                'view_medical_record' => 'fa-file-medical',
                                'download_exam' => 'fa-file-arrow-down',
                                'patient_portal_login' => 'fa-right-to-bracket',
                                default => 'fa-user-shield'
                            };
                        @endphp
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-sm font-black text-zinc-900 dark:text-white group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors uppercase tracking-tight">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </h3>
                                <p class="text-[10px] text-zinc-500 font-medium">Acesso por: <span class="text-zinc-700 dark:text-zinc-300 font-bold">{{ $log->user->name ?? 'Sistema' }}</span></p>
                            </div>
                            <span class="text-[9px] text-zinc-500 dark:text-zinc-600 font-black uppercase tracking-widest bg-zinc-100 dark:bg-white/5 px-3 py-1 rounded-lg">
                                {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-network-wired text-[9px] text-zinc-400 dark:text-zinc-800"></i>
                                <span class="text-[10px] text-zinc-600 dark:text-zinc-700 font-mono">{{ $log->ip_address }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="far fa-clock text-[9px] text-zinc-400 dark:text-zinc-800"></i>
                                <span class="text-[10px] text-zinc-600 dark:text-zinc-700 font-bold uppercase tracking-tighter">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-32 text-center bg-zinc-50 dark:bg-zinc-950/30 border border-dashed border-zinc-200 dark:border-white/5 rounded-[3rem]">
                    <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-6 text-zinc-400 dark:text-zinc-800">
                        <i class="fas fa-fingerprint text-4xl"></i>
                    </div>
                    <p class="text-xs text-zinc-500 font-black uppercase tracking-widest">Nenhum registro de transparência disponível</p>
                </div>
            @endforelse
        </div>

        <div class="pt-10">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
