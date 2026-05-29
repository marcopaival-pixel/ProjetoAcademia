@extends('layouts.app')

@section('title', 'Selecionar Unidade')

@section('content')
<div class="min-h-screen bg-[#06080c] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Efeitos de Fundo -->
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-emerald-600 opacity-[0.05] blur-[150px] rounded-full"></div>
    <div class="absolute bottom-[0%] -right-[10%] w-[40%] h-[40%] bg-cyan-500 opacity-[0.03] blur-[120px] rounded-full"></div>

    <div class="max-w-4xl w-full space-y-12 relative z-10 animate-dashboard-entry">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                <i class="fas fa-hospital"></i>
                Múltiplas Unidades Detectadas
            </div>
            <h2 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                Qual <span class="text-emerald-500">clínica</span> deseja acessar?
            </h2>
            <p class="text-zinc-500 text-lg font-medium max-w-xl mx-auto">
                Seu cadastro está vinculado a mais de uma unidade. Selecione a clínica para visualizar seus registros específicos.
            </p>
        </div>

        <form action="{{ route('clinic.select') }}" method="POST" id="clinicForm" class="max-w-2xl mx-auto">
            @csrf
            
            <div class="space-y-6">
                @foreach($clinics as $clinic)
                    <label class="relative block group cursor-pointer">
                        <input type="radio" name="clinic_id" value="{{ $clinic->id }}" class="peer hidden" required>
                        <div class="glass-card rounded-3xl p-6 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 transition-all duration-300 flex items-center gap-6 group-hover:bg-white/[0.03]">
                            <div class="w-16 h-16 rounded-2xl bg-zinc-900 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-clinic-medical text-emerald-500"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-black text-white uppercase italic tracking-tight">{{ $clinic->name }}</h3>
                                <p class="text-zinc-500 text-sm font-medium">Vínculo como: <span class="text-zinc-300 uppercase tracking-widest text-[10px]">{{ $clinic->role }}</span></p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 flex items-center justify-center transition-all">
                                <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                    </label>
                @endforeach

                <div class="pt-6">
                    <button type="submit" class="w-full py-5 rounded-[2rem] bg-emerald-600 text-white font-black uppercase tracking-[0.2em] italic text-sm hover:bg-emerald-500 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-2xl shadow-emerald-500/20">
                        Acessar Unidade <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('profile.selection') }}" class="text-zinc-500 hover:text-white text-[10px] font-bold uppercase tracking-widest transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Voltar para seleção de perfil
            </a>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.7);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    @keyframes dashboard-entry {
        from { opacity: 0; transform: translateY(40px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .animate-dashboard-entry {
        animation: dashboard-entry 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
