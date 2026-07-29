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
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
            <i class="fas fa-folder-open text-8xl text-blue-500"></i>
        </div>
        
        <div class="relative z-10 flex items-center gap-6">
            <a href="{{ route('patient.portal') }}" class="w-12 h-12 bg-zinc-800 hover:bg-zinc-700 rounded-2xl flex items-center justify-center text-white transition-colors">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight mb-2">Central de <span class="text-blue-500">Documentos</span></h1>
                <p class="text-zinc-400 font-medium max-w-2xl">Seus exames, receitas e laudos anexados pelo seu profissional responsável.</p>
            </div>
        </div>
    </div>

    <!-- Security Note -->
    <div class="p-6 bg-blue-500/5 border border-blue-500/10 rounded-[2rem] flex gap-4 items-start">
        <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500 shrink-0">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div>
            <h4 class="text-white font-bold mb-1">Proteção e Privacidade</h4>
            <p class="text-sm text-zinc-400 font-medium">Seus documentos estão criptografados e protegidos. Somente você e seu profissional responsável têm acesso a estes arquivos.</p>
        </div>
    </div>

    <!-- Document List -->
    <div class="space-y-4">
        @forelse($documents as $doc)
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-[2rem] flex flex-col md:flex-row md:items-center gap-5 hover:border-blue-500/50 transition-all group">
            <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 shrink-0 group-hover:scale-110 transition-transform">
                @if($doc->category == 'Receita')
                    <i class="fas fa-prescription-bottle-alt text-2xl"></i>
                @elseif($doc->category == 'Exame')
                    <i class="fas fa-microscope text-2xl"></i>
                @else
                    <i class="fas fa-file-alt text-2xl"></i>
                @endif
            </div>
            
            <div class="flex-1">
                <span class="inline-block px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-lg mb-2">{{ $doc->category }}</span>
                <h4 class="text-lg font-black text-white mb-1">{{ $doc->title }}</h4>
                <p class="text-sm font-medium text-zinc-500"><i class="far fa-calendar-alt mr-2"></i> Anexado em {{ $doc->created_at->format('d/m/Y') }}</p>
            </div>
            
            <div class="flex gap-3 mt-4 md:mt-0">
                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex-1 md:flex-none px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-bold rounded-xl text-center transition-colors">
                    <i class="fas fa-eye mr-2"></i> Visualizar
                </a>
                <a href="{{ asset('storage/' . $doc->file_path) }}" download class="flex-1 md:flex-none px-6 py-3 bg-[var(--brand-primary)] hover:brightness-110 text-white font-bold rounded-xl text-center transition-all">
                    <i class="fas fa-download mr-2"></i> Baixar
                </a>
            </div>
        </div>
        @empty
        <x-patient.empty-state 
            icon="fas fa-folder-open" 
            title="Nenhum Documento" 
            description="Seus exames, receitas e laudos serão listados aqui assim que forem anexados ao seu prontuário pelo profissional responsável."
        />
        @endforelse
    </div>
</div>
@endsection
