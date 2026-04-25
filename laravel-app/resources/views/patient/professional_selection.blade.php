@extends('layouts.app')

@section('title', 'Selecionar Profissional — NexShape')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-600/10 rounded-full blur-[150px] pointer-events-none"></div>

    <div class="max-w-2xl w-full space-y-12 relative z-10">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-zinc-900/50 border border-white/10 shadow-2xl backdrop-blur-2xl mb-4">
                <i class="fas fa-user-md text-4xl text-blue-500"></i>
            </div>
            <h2 class="text-4xl font-black text-white tracking-tight">Quem você deseja acessar hoje?</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-[0.2em]">Você possui múltiplos vínculos profissionais ativos.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($professionals as $professional)
                <form action="{{ route('patient.professional.select') }}" method="POST">
                    @csrf
                    <input type="hidden" name="professional_id" value="{{ $professional->id }}">
                    <button type="submit" class="w-full group relative flex flex-col items-center p-8 bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] hover:bg-zinc-800/60 hover:border-blue-500/30 transition-all duration-500 shadow-2xl overflow-hidden">
                        <!-- Card Glow -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full blur-3xl group-hover:bg-blue-600/20 transition-all duration-700"></div>
                        
                        <div class="relative mb-6">
                            @if($professional->avatar)
                                <img src="{{ asset('storage/' . $professional->avatar) }}" alt="{{ $professional->name }}" class="w-24 h-24 rounded-3xl object-cover border-2 border-white/10 group-hover:border-blue-500/50 transition-all shadow-2xl">
                            @else
                                <div class="w-24 h-24 rounded-3xl bg-zinc-950 flex items-center justify-center border-2 border-white/5 group-hover:border-blue-500/50 transition-all shadow-2xl">
                                    <span class="text-2xl font-black text-zinc-700 group-hover:text-blue-500 transition-colors">{{ mb_substr($professional->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                <i class="fas fa-check text-[10px] text-white"></i>
                            </div>
                        </div>

                        <h3 class="text-lg font-black text-white mb-1 group-hover:text-blue-400 transition-colors">{{ $professional->name }}</h3>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">
                            {{ $professional->professionalProfile->specialty ?? 'Especialista' }}
                        </p>

                        <div class="w-full h-px bg-white/5 mb-4 group-hover:bg-blue-500/20 transition-all"></div>

                        <div class="flex items-center space-x-2 text-[10px] text-zinc-400 font-bold uppercase tracking-widest">
                            <i class="fas fa-circle text-[6px] text-emerald-500 animate-pulse"></i>
                            <span>Vínculo Ativo</span>
                        </div>
                        
                        <div class="mt-6 py-3 px-6 bg-blue-600/10 text-blue-500 text-[10px] font-black uppercase tracking-widest rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-all shadow-lg">
                            Acessar Portal
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        <div class="flex flex-col items-center gap-6">
            <a href="{{ route('patient.unified.dashboard') }}" class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] hover:text-white transition-colors bg-white/5 px-6 py-3 rounded-2xl border border-white/5 hover:border-blue-500/30">
                <i class="fas fa-layer-group mr-2"></i>Ver Visão Geral da Saúde
            </a>
            
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] hover:text-rose-500 transition-colors">
                <i class="fas fa-sign-out-alt mr-2"></i>Sair da Conta
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection
