@extends('layouts.app')

@section('title', 'Documentos e Exames')

@section('style')
<style>
    :root {
        --bg-dark: #07090c;
        --card-bg: #0c0e12;
        --card-border: rgba(255, 255, 255, 0.05);
        --brand-accent: #0ea5e9;
    }
    body {
        background-color: var(--bg-dark);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen pb-32 px-5 pt-8 font-sans max-w-3xl mx-auto flex flex-col relative z-0">

    <!-- Efeito de Luz de Fundo (Glow Superior) -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-[#0ea5e9]/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <!-- Cabeçalho Principal -->
    <div class="flex justify-between items-start mb-12 animate-fade-in-up">
        <div class="flex items-center gap-6">
            <a href="{{ route('patient.unified.dashboard') }}" class="w-16 h-16 rounded-[2rem] bg-[var(--card-bg)] border border-[var(--card-border)] flex items-center justify-center text-zinc-500 hover:text-white transition-colors shadow-2xl">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
            <div>
                <h1 class="text-white font-black text-3xl leading-none tracking-tight uppercase max-w-[280px]">
                    Documentos
                </h1>
                <div class="flex items-center gap-2 mt-2.5">
                    <span class="text-xs text-zinc-500 font-bold tracking-[0.15em] uppercase">Arquivo Clínico</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Documentos -->
    <div class="space-y-6">
        @forelse($documents as $doc)
        <div class="bg-[var(--card-bg)] p-8 rounded-[3rem] border border-[var(--card-border)] flex items-center gap-6 group hover:border-[#0ea5e9]/30 transition-all duration-300 shadow-2xl relative overflow-hidden">
            
            <!-- Glow hover -->
            <div class="absolute inset-0 bg-gradient-to-r from-[#0ea5e9]/0 via-[#0ea5e9]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
            
            <div class="w-20 h-20 rounded-[2rem] bg-[#0a0c10] border-2 border-[var(--card-border)] flex items-center justify-center text-[#0ea5e9] shrink-0 shadow-inner group-hover:scale-110 transition-transform duration-500">
                @if($doc->category == 'Receita')
                    <i class="fas fa-prescription-bottle-alt text-3xl"></i>
                @elseif($doc->category == 'Exame')
                    <i class="fas fa-microscope text-3xl"></i>
                @else
                    <i class="fas fa-file-alt text-3xl"></i>
                @endif
            </div>
            
            <div class="flex-1">
                <span class="text-[10px] font-black text-[#0ea5e9] uppercase tracking-[0.2em]">{{ $doc->category }}</span>
                <h4 class="text-xl font-black text-white uppercase tracking-tight mt-1 mb-1">{{ $doc->title }}</h4>
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest"><i class="far fa-calendar-alt mr-1"></i> {{ $doc->created_at->format('d/m/Y') }}</p>
            </div>
            
            <div class="flex gap-4 relative z-10">
                <a href="{{ route('secure-files.show', ['type' => 'patient-document', 'id' => $doc->id]) }}" target="_blank" class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-white/10 transition-colors shadow-lg" title="Visualizar">
                    <i class="fas fa-eye text-xl"></i>
                </a>
                <a href="{{ route('secure-files.show', ['type' => 'patient-document', 'id' => $doc->id]) }}" download class="w-14 h-14 rounded-2xl bg-[#0ea5e9]/10 border border-[#0ea5e9]/20 flex items-center justify-center text-[#0ea5e9] hover:bg-[#0ea5e9]/20 transition-colors shadow-lg" title="Baixar">
                    <i class="fas fa-download text-xl"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="bg-[var(--card-bg)] p-12 rounded-[3rem] border border-[var(--card-border)] flex flex-col items-center justify-center text-center shadow-2xl">
            <div class="w-24 h-24 rounded-full bg-zinc-900 border-4 border-zinc-800 flex items-center justify-center text-zinc-700 mb-6">
                <i class="fas fa-folder-open text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-white uppercase tracking-tighter mb-3">Nenhum Documento</h3>
            <p class="text-sm font-bold text-zinc-500 leading-relaxed max-w-sm mx-auto">
                Seus exames, receitas e laudos serão listados aqui assim que forem anexados ao seu prontuário pelo profissional responsável.
            </p>
        </div>
        @endforelse
    </div>

    <!-- Nota de Segurança -->
    <div class="mt-12 p-8 bg-[#0ea5e9]/5 border border-[#0ea5e9]/10 rounded-[2.5rem] flex items-center gap-6 shadow-xl">
        <div class="w-14 h-14 bg-[#0ea5e9]/10 rounded-[1.5rem] flex items-center justify-center text-[#0ea5e9] shrink-0">
            <i class="fas fa-shield-alt text-2xl"></i>
        </div>
        <div>
            <h4 class="text-white font-black text-sm uppercase tracking-widest mb-1">Criptografia Ativa</h4>
            <p class="text-xs text-zinc-500 font-bold leading-relaxed">
                Seus documentos estão protegidos pela LGPD. Somente você e seu profissional têm acesso.
            </p>
        </div>
    </div>

</div>

<!-- Navegação Flutuante Inferior (mesma do dashboard) -->
<div class="fixed bottom-0 left-0 w-full z-50 pointer-events-none">
    <div class="max-w-3xl mx-auto relative pointer-events-auto">
        <!-- Curvas Decorativas (Linhas Finas) -->
        <div class="absolute -top-12 left-1/2 -translate-x-1/2 w-full h-12 pointer-events-none flex justify-center overflow-hidden z-[-1]">
            <div class="w-[100px] h-[100px] border border-[var(--card-border)] rounded-full mt-2"></div>
        </div>
        
        <nav class="nav-bar-shape px-8 pb-8 pt-6 flex items-center justify-between" style="background: linear-gradient(to top, #07090c 40%, rgba(7,9,12,0.9)); -webkit-mask-image: radial-gradient(circle at center -40px, transparent 50px, black 51px);">
            <a href="{{ route('patient.unified.dashboard') }}" class="flex flex-col items-center gap-1.5 text-zinc-500 hover:text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-home text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Home</span>
            </a>
            
            <div class="relative flex-1 flex justify-center -mt-20">
                <a href="{{ route('patient.agenda') }}" class="w-20 h-20 rounded-full bg-[#0a0c10] border-2 border-[var(--card-border)] flex items-center justify-center text-white shadow-2xl relative z-10 transition-transform hover:scale-105">
                    <i class="far fa-calendar-alt text-3xl"></i>
                </a>
            </div>

            <a href="{{ route('patient.evolution') }}" class="flex flex-col items-center gap-1.5 text-zinc-500 hover:text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-dumbbell text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Treino</span>
            </a>

            <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1.5 text-zinc-500 hover:text-white flex-1 transition-transform hover:scale-110">
                <i class="fas fa-user-circle text-3xl mb-1"></i>
                <span class="text-[9px] font-black tracking-[0.2em] uppercase">Perfil</span>
            </a>
        </nav>
    </div>
</div>
@endsection
