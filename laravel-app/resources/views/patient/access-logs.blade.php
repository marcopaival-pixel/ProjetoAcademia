@extends('layouts.app')

@section('title', 'Histórico de Acessos — ' . $branding['clinic_name'])

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
        border: 1px solid var(--glass-border);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] relative overflow-hidden pb-40 px-6 pt-10">
    <div class="max-w-lg mx-auto space-y-8">
        <header class="flex items-center gap-4">
            <a href="{{ route('patient.portal') }}" class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-white">
                <i class="fas fa-chevron-left"></i>
            </a>
            <h1 class="text-2xl font-black text-white tracking-tighter">Histórico de Transparência</h1>
        </header>

        <p class="text-zinc-500 text-sm font-medium">Confira quem acessou seus dados clínicos e quando. No NexShape, sua privacidade é prioridade.</p>

        <div class="space-y-4">
            @forelse($logs as $log)
                <div class="glass-card p-5 rounded-2xl flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-zinc-800 flex items-center justify-center text-blue-500">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <span class="text-white text-xs font-bold">{{ $log->action }}</span>
                            <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[10px] text-zinc-500 mt-1">Realizado por: {{ $log->user->name }}</p>
                        <p class="text-[9px] text-zinc-700 font-mono mt-1">IP: {{ $log->ip_address }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <i class="fas fa-history text-4xl text-zinc-800 mb-4"></i>
                    <p class="text-zinc-500 font-bold">Nenhum registro de acesso encontrado.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-6">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
