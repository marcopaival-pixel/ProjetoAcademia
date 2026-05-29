@extends('layouts.onboarding-premium')

@section('title', 'Administrador')
@section('step_title', 'Conta de Administrador')
@section('step_description', 'Crie as credenciais de acesso para o gestor principal da conta.')

@section('content')
<form action="{{ route('onboarding-premium.step.save', 5) }}" method="POST" class="space-y-12" x-data="{
    password: '',
    confirm: '',
    get strength() {
        if (this.password.length === 0) return 0;
        let s = 0;
        if (this.password.length > 8) s += 25;
        if (/[A-Z]/.test(this.password)) s += 25;
        if (/[0-9]/.test(this.password)) s += 25;
        if (/[^A-Za-z0-9]/.test(this.password)) s += 25;
        return s;
    },
    get strengthColor() {
        if (this.strength < 50) return 'bg-red-500';
        if (this.strength < 75) return 'bg-yellow-500';
        return 'bg-emerald-500';
    }
}">
    @csrf
    
    <div class="space-y-8">
        <!-- Nome do Admin -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Nome Completo</label>
            <input type="text" name="name" required
                placeholder="Ex: Dr. João Silva"
                class="w-full input-premium">
        </div>

        <!-- E-mail do Admin -->
        <div class="space-y-3">
            <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">E-mail de Acesso</label>
            <input type="email" name="email" required
                placeholder="admin@suaempresa.com"
                class="w-full input-premium">
            <p class="text-[10px] text-zinc-600 px-1">Este será o e-mail de login definitivo.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Senha -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Senha</label>
                <input type="password" name="password" x-model="password" required
                    placeholder="••••••••"
                    class="w-full input-premium">
                
                <!-- Força da Senha -->
                <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden mt-2">
                    <div class="h-full transition-all duration-500" :class="strengthColor" :style="`width: ${strength}%`"></div>
                </div>
                <p class="text-[10px] text-zinc-600 px-1">Mínimo 8 caracteres, letras, números e símbolos.</p>
            </div>

            <!-- Confirmação -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-zinc-500 uppercase tracking-widest ml-1">Confirmar Senha</label>
                <input type="password" name="password_confirmation" x-model="confirm" required
                    placeholder="••••••••"
                    class="w-full input-premium"
                    :class="password !== confirm && confirm !== '' ? 'border-red-500/50' : ''">
                <p x-show="password !== confirm && confirm !== ''" class="text-[10px] text-red-400 px-1 mt-2">As senhas não coincidem.</p>
            </div>
        </div>
    </div>

    <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <a href="{{ route('onboarding-premium.step', 4) }}" class="text-zinc-500 hover:text-white font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
        <button type="submit" class="btn-premium w-full sm:w-auto flex items-center justify-center gap-3" :disabled="strength < 50 || password !== confirm">
            Continuar para Ajustes <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
