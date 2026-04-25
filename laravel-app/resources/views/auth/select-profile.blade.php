@extends('layouts.app')

@section('title', 'Selecionar Perfil')

@section('content')
<div class="min-h-screen bg-[#06080c] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Efeitos de Fundo -->
    <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-blue-600 opacity-[0.05] blur-[150px] rounded-full"></div>
    <div class="absolute bottom-[0%] -right-[10%] w-[40%] h-[40%] bg-emerald-500 opacity-[0.03] blur-[120px] rounded-full"></div>

    <div class="max-w-4xl w-full space-y-12 relative z-10 animate-dashboard-entry">
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4">
                <i class="fas fa-user-shield"></i>
                Múltiplos Perfis Detectados
            </div>
            <h2 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                Como deseja <span class="text-blue-500">acessar</span> hoje?
            </h2>
            <p class="text-zinc-500 text-lg font-medium max-w-xl mx-auto">
                Identificamos mais de um perfil vinculado à sua conta. Escolha a experiência que deseja iniciar agora.
            </p>
        </div>

        <form action="{{ route('profile.select') }}" method="POST" id="profileForm" class="max-w-2xl mx-auto">
            @csrf
            
            <div class="space-y-6">
                @foreach($roles as $role)
                    @php
                        $icon = 'fa-user';
                        if($role->name === 'admin') $icon = 'fa-user-crown';
                        elseif($role->name === 'professional') $icon = 'fa-stethoscope';
                        elseif($role->name === 'paciente') $icon = 'fa-user-injured';
                        elseif($role->name === 'aluno') $icon = 'fa-running';
                    @endphp
                    
                    <label class="relative block group cursor-pointer">
                        <input type="radio" name="role" value="{{ $role->name }}" class="peer hidden" required>
                        <div class="glass-card rounded-3xl p-6 border border-white/5 peer-checked:border-blue-500/50 peer-checked:bg-blue-500/5 transition-all duration-300 flex items-center gap-6 group-hover:bg-white/[0.03]">
                            <div class="w-16 h-16 rounded-2xl bg-zinc-900 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-500">
                                <i class="fas {{ $icon }} text-blue-500"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-black text-white uppercase italic tracking-tight">Somente Painel do {{ $role->label }}</h3>
                                <p class="text-zinc-500 text-sm font-medium">Acessar apenas as funcionalidades exclusivas deste perfil.</p>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-zinc-700 peer-checked:border-blue-500 peer-checked:bg-blue-500 flex items-center justify-center transition-all">
                                <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                    </label>
                @endforeach

                <!-- Opção Mostrar Todos -->
                <label class="relative block group cursor-pointer">
                    <input type="radio" name="role" value="all" class="peer hidden">
                    <div class="glass-card rounded-3xl p-6 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 transition-all duration-300 flex items-center gap-6 group-hover:bg-white/[0.03]">
                        <div class="w-16 h-16 rounded-2xl bg-zinc-900 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-500">
                            <i class="fas fa-th-large text-emerald-500"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-white uppercase italic tracking-tight">Mostrar todos os painéis disponíveis</h3>
                            <p class="text-zinc-500 text-sm font-medium">Combinar todas as funcionalidades em uma visão unificada.</p>
                        </div>
                        <div class="w-6 h-6 rounded-full border-2 border-zinc-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 flex items-center justify-center transition-all">
                            <div class="w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                        </div>
                    </div>
                </label>

                <!-- Lembrar escolha -->
                <div class="pt-6 flex flex-col items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative w-6 h-6">
                            <input type="checkbox" name="remember" value="1" class="peer hidden">
                            <div class="absolute inset-0 rounded-lg bg-zinc-800 border border-white/10 peer-checked:bg-blue-600 peer-checked:border-blue-500 transition-all duration-300"></div>
                            <i class="fas fa-check absolute inset-0 flex items-center justify-center text-[10px] text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300"></i>
                        </div>
                        <span class="text-zinc-400 font-bold uppercase tracking-widest text-[10px] group-hover:text-white transition-colors">Lembrar minha escolha</span>
                    </label>

                    <button type="submit" class="w-full py-5 rounded-[2rem] bg-blue-600 text-white font-black uppercase tracking-[0.2em] italic text-sm hover:bg-blue-500 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-2xl shadow-blue-500/20">
                        Confirmar Acesso <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function selectProfile(role) {
        document.getElementById('selectedRole').value = role;
        document.getElementById('profileForm').submit();
    }
</script>

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
