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
            
            <!-- Company Info -->
            <div class="lg:col-span-2 space-y-8">
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-building text-blue-500"></i> Informações da Unidade
                    </h3>

                    <form action="{{ route('clinic.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Nome da Clínica / Unidade</label>
                                <input type="text" name="name" value="{{ $company->name }}" class="w-full bg-zinc-900 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 ring-blue-500">
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Identificador (Slug)</label>
                                <input type="text" value="{{ $company->slug }}" disabled class="w-full bg-zinc-800 border-none rounded-2xl p-4 text-sm font-bold opacity-50 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Cor Primária</label>
                                <div class="flex gap-4 items-center bg-zinc-900 rounded-2xl p-2">
                                    <input type="color" name="primary_color" value="{{ $company->primary_color ?? '#3b82f6' }}" class="h-12 w-20 bg-transparent border-none cursor-pointer">
                                    <span class="text-xs font-mono font-bold text-zinc-400">{{ $company->primary_color ?? '#3b82f6' }}</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Cor de Destaque</label>
                                <div class="flex gap-4 items-center bg-zinc-900 rounded-2xl p-2">
                                    <input type="color" name="accent_color" value="{{ $company->accent_color ?? '#10b981' }}" class="h-12 w-20 bg-transparent border-none cursor-pointer">
                                    <span class="text-xs font-mono font-bold text-zinc-400">{{ $company->accent_color ?? '#10b981' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[9px] font-black uppercase text-zinc-500 tracking-widest mb-2 block">Logo da Clínica</label>
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 rounded-2xl bg-zinc-900 flex items-center justify-center border border-white/5 overflow-hidden">
                                    @if($company->logo_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($company->logo_path) }}" class="w-full h-full object-contain">
                                    @else
                                        <i class="fas fa-image text-zinc-700"></i>
                                    @endif
                                </div>
                                <input type="file" name="logo" class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700">
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-6 bg-zinc-900/50 rounded-2xl border border-white/5">
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-widest">Compartilhar Prontuários entre Profissionais</h4>
                                <p class="text-[9px] text-zinc-500 uppercase font-black">Permite que todos os profissionais da clínica visualizem o histórico completo dos pacientes da unidade.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="shared_medical_records" value="1" {{ ($company->shared_medical_records ?? false) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-400 after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="p-6 bg-blue-500/5 border border-blue-500/10 rounded-[2rem]">
                            <h4 class="text-[10px] font-black uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-user-plus text-blue-400"></i> Link de Convite para Profissionais
                            </h4>
                            <p class="text-[10px] text-zinc-400 mb-4 uppercase font-bold">Use este link para que novos profissionais se cadastrem diretamente na sua clínica.</p>
                            
                            <div class="flex gap-2">
                                <input type="text" id="inviteUrl" value="{{ $inviteUrl }}" readonly class="flex-1 bg-black/40 border-none rounded-xl p-3 text-[10px] font-mono text-blue-400">
                                <button onclick="copyInviteUrl()" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 rounded-xl text-[10px] font-black uppercase transition-all">
                                    Copiar
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Team List -->
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-users text-blue-500"></i> Equipe da Clínica
                    </h3>

                    <div class="space-y-4">
                        @foreach($team as $member)
                        <div class="flex items-center gap-4 p-4 rounded-3xl bg-white/5 border border-white/5">
                            <div class="w-12 h-12 rounded-2xl bg-zinc-900 flex items-center justify-center font-black text-xs text-zinc-500">
                                {{ substr($member->name, 0, 2) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold">{{ $member->name }}</h4>
                                <p class="text-[9px] text-zinc-500 uppercase font-black">{{ $member->roles->pluck('name')->join(', ') }}</p>
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
