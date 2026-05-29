@extends('layouts.onboarding')

@section('title', 'Passo 8: Conclusão — NexShape')
@section('step_number', '08/08')
@section('back_route', route('onboarding.step7'))

@section('onboarding_content')
<div class="space-y-10 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Último Passo</h2>
        <p class="text-zinc-400 text-base font-medium">Sua privacidade e segurança são nossa prioridade.</p>
    </header>

    <div class="p-8 bg-white/5 rounded-3xl border border-white/10 backdrop-blur-md space-y-6">
        <h3 class="text-xl font-bold text-white">Quase lá!</h3>
        <p class="text-zinc-400 leading-relaxed">
            Ao clicar em concluir, você confirma que leu e concorda com nossos termos. Estamos prontos para calcular seu plano final.
        </p>
    </div>

    <form action="{{ route('onboarding.step8.save') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="space-y-8">
            <!-- Username -->
            <div class="space-y-3">
                <label for="username" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest pl-1">Escolha um nome de usuário</label>
                <div class="relative group">
                    <input type="text" name="username" id="username" required 
                        value="{{ old('username', strtolower(str_replace(' ', '', Session::get('onboarding_data.name', '')))) }}"
                        class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-xl font-bold text-white focus:outline-none focus:border-blue-500 transition-all">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-focus-within:w-full transition-all duration-500"></div>
                </div>
                <p class="text-[9px] text-zinc-500 pl-1">Como você será identificado na plataforma.</p>
            </div>

            <div class="space-y-4 pt-4">
                <label class="group relative cursor-pointer block">
                    <input type="checkbox" name="terms" required class="peer sr-only">
                    <div class="p-5 bg-white/5 border-2 border-white/10 rounded-2xl transition-all duration-300 group-hover:bg-white/10 peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded border-2 border-white/20 peer-checked:bg-blue-500 peer-checked:border-blue-500 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-zinc-300">Eu concordo com os Termos e Políticas de Privacidade.</span>
                        </div>
                    </div>
                </label>

                <label class="group relative cursor-pointer block">
                    <input type="checkbox" name="marketing" class="peer sr-only">
                    <div class="p-5 bg-white/5 border-2 border-white/10 rounded-2xl transition-all duration-300 group-hover:bg-white/10 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10">
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded border-2 border-white/20 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-zinc-300">Quero receber dicas exclusivas de saúde por e-mail.</span>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Gerar Meu Plano Agora
            </button>
        </div>
    </form>
</div>
@endsection
