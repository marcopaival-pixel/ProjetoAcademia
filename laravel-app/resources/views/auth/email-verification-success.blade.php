@extends('layouts.app')

@section('title', 'E-mail confirmado — ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10 text-center space-y-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-emerald-500/30 mb-2 shadow-2xl backdrop-blur-xl">
            <i class="fas fa-circle-check text-3xl text-emerald-400"></i>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">E-mail confirmado</h1>
        <p class="text-zinc-400 text-sm leading-relaxed">
            Email confirmado com sucesso.<br>
            Sua conta foi ativada.
        </p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-emerald-600/20 uppercase tracking-[0.2em] text-[10px]">
            Entrar no sistema
        </a>
    </div>
</div>
<style>body { background-color: #0b0e14; }</style>
@endsection
