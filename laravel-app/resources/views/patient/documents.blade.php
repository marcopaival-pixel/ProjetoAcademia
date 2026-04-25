@extends('layouts.app')

@section('title', 'Documentos — ' . $branding['clinic_name'])

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
                <h1 class="text-xl font-black tracking-tighter uppercase italic">Central de Documentos</h1>
                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Exames, Receitas e Laudos</p>
            </div>
        </header>

        <!-- Document List -->
        <div class="space-y-4">
            @forelse($documents as $doc)
            <div class="glass-card p-6 rounded-[2.5rem] flex items-center gap-5 group hover:bg-white/[0.03] transition-all">
                <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center text-blue-400">
                    @if($doc->category == 'Receita')
                        <i class="fas fa-prescription-bottle-alt text-lg"></i>
                    @elseif($doc->category == 'Exame')
                        <i class="fas fa-microscope text-lg"></i>
                    @else
                        <i class="fas fa-file-alt text-lg"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <span class="text-[8px] font-black text-blue-400/70 uppercase tracking-widest">{{ $doc->category }}</span>
                    <h4 class="text-xs font-black text-white uppercase tracking-wider mb-0.5">{{ $doc->title }}</h4>
                    <p class="text-[9px] font-bold text-zinc-600 uppercase tracking-widest">Data: {{ $doc->created_at->format('d/m/Y') }}</p>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-zinc-400 hover:text-white" title="Visualizar">
                        <i class="fas fa-eye text-xs"></i>
                    </a>
                    <a href="{{ asset('storage/' . $doc->file_path) }}" download class="w-10 h-10 rounded-xl bg-[var(--brand-primary)]/10 flex items-center justify-center text-[var(--brand-primary)] hover:scale-105 transition-transform" title="Baixar">
                        <i class="fas fa-download text-xs"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="glass-card p-12 rounded-[3.5rem] text-center border-dashed border-white/5 bg-transparent">
                <div class="w-16 h-16 bg-zinc-900/50 rounded-full mx-auto flex items-center justify-center text-zinc-700 mb-6">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <h5 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Nada por aqui</h5>
                <p class="text-zinc-700 text-[9px] font-bold px-10 leading-relaxed uppercase tracking-widest">Seus documentos e laudos aparecerão aqui assim que forem enviados pelo profissional.</p>
            </div>
            @endforelse
        </div>

        <!-- Security Note -->
        <div class="px-6 py-4 bg-blue-500/5 border border-blue-500/10 rounded-2xl flex gap-4 items-start">
            <i class="fas fa-shield-alt text-blue-400 mt-0.5"></i>
            <p class="text-[9px] text-zinc-500 font-bold leading-relaxed">
                Seus documentos estão protegidos. Somente você e seu profissional responsável têm acesso a estes arquivos.
            </p>
        </div>
    </div>
</div>
@endsection
