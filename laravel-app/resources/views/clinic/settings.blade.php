@extends('layouts.app')

@section('title', 'Gestão da Clínica')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-10">
            <h1 class="text-3xl font-black tracking-tighter italic uppercase">Gestão da Clínica</h1>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Configurações, Equipe e Branding</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Clinics List -->
            <div class="lg:col-span-2 space-y-8">
                @foreach($clinics as $clinic)
                <div class="glass-card rounded-[2.5rem] p-8 {{ $clinic->id === auth()->user()->clinic_id ? 'border-emerald-500/30' : '' }}">
                    <div class="flex justify-between items-start mb-8">
                        <h3 class="text-sm font-black uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-clinic-medical text-blue-500"></i> {{ $clinic->name }}
                            @if($clinic->id === auth()->user()->clinic_id)
                                <span class="bg-emerald-500/10 text-emerald-500 text-[8px] px-2 py-0.5 rounded-full">Ativa</span>
                            @endif
                        </h3>
                        <a href="{{ route('clinic.home', $clinic->slug) }}" target="_blank" class="text-[9px] font-black uppercase text-blue-400 hover:text-blue-300 transition-colors">
                            Ver Home Pública <i class="fas fa-external-link-alt ml-1"></i>
                        </a>
                    </div>

                    <form action="{{ route('clinic.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Nome da Clínica</label>
                                <input type="text" name="name" value="{{ $clinic->name }}" class="w-full bg-zinc-900 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 ring-blue-500">
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">URL Exclusiva</label>
                                <div class="relative">
                                    <input type="text" value="{{ $clinic->slug }}" disabled class="w-full bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold opacity-50 cursor-not-allowed">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[8px] text-zinc-600 font-bold">nexshape.com/{{ $clinic->slug }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Cor Primária</label>
                                <div class="flex gap-4 items-center bg-zinc-900 rounded-2xl p-2">
                                    <input type="color" name="primary_color" value="{{ $clinic->primary_color ?? '#10b981' }}" class="h-12 w-20 bg-transparent border-none cursor-pointer">
                                    <span class="text-xs font-mono font-bold text-zinc-400 uppercase">{{ $clinic->primary_color ?? '#10b981' }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Logo da Clínica</label>
                                <div class="flex items-center gap-6">
                                    <div class="w-12 h-12 rounded-xl bg-zinc-900 flex items-center justify-center border border-white/5 overflow-hidden">
                                        @if($clinic->logo_path)
                                            <img src="{{ asset('storage/' . $clinic->logo_path) }}" class="w-full h-full object-contain">
                                        @else
                                            <i class="fas fa-image text-zinc-700"></i>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" class="text-[8px] text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-zinc-800 file:text-zinc-300">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Atualizar {{ $clinic->name }}
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach

                <!-- Add New Clinic Button -->
                @if($clinics->count() < 5) {{-- Exemplo de limite --}}
                <div class="glass-card rounded-[2.5rem] p-8 border-dashed border-zinc-800 bg-transparent hover:border-blue-500/50 transition-colors group">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-6 flex items-center gap-2 group-hover:text-blue-500 transition-colors">
                        <i class="fas fa-plus-circle"></i> Adicionar Nova Clínica
                    </h3>
                    <form action="{{ route('clinic.settings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <input type="text" name="name" placeholder="Nome da Clínica" class="bg-zinc-900 border-none rounded-xl p-3 text-xs font-bold" required>
                        <input type="text" name="slug" placeholder="slug-exclusivo" class="bg-zinc-900 border-none rounded-xl p-3 text-xs font-bold" required>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Criar Clínica
                        </button>
                    </form>
                </div>
                @endif

                <!-- Team List -->
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-users text-blue-500"></i> Equipe da Empresa
                    </h3>

                    <div class="space-y-4">
                        @foreach($team as $member)
                        <div class="flex items-center gap-4 p-4 rounded-3xl bg-white/5 border border-white/5 hover:border-white/10 transition-all">
                            <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center font-black text-xs text-zinc-500">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold">{{ $member->name }}</h4>
                                <div class="flex gap-2">
                                    <span class="text-[8px] text-zinc-500 uppercase font-black">{{ $member->roles->pluck('name')->join(', ') }}</span>
                                    @if($member->clinic)
                                        <span class="text-[8px] text-blue-500/70 uppercase font-black">• {{ $member->clinic->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-zinc-500">{{ $member->email }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="glass-card rounded-[2.5rem] p-8 bg-blue-600">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-4">Plano Enterprise</h3>
                    <p class="text-[11px] text-white/80 leading-relaxed font-bold uppercase mb-6">Sua clínica possui acesso a todos os recursos avançados do NexShape.</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase">
                            <i class="fas fa-check-circle"></i> Agenda Multi-Profissional
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase">
                            <i class="fas fa-check-circle"></i> Branding Personalizado
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-black uppercase">
                            <i class="fas fa-check-circle"></i> Suporte Prioritário
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-[2.5rem] p-8 border-dashed border-zinc-800 bg-transparent">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-4">Dica de Gestão</h3>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-bold uppercase tracking-wider">
                        Configure o Branding da sua clínica para que todos os documentos (laudos e treinos) saiam com sua identidade visual.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function copyInviteUrl() {
    var copyText = document.getElementById("inviteUrl");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    
    alert("Link de convite copiado!");
}
</script>
@endsection
