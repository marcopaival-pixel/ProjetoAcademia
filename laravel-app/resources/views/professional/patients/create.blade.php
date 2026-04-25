@extends('layouts.app')

@section('title', 'Novo Paciente — NexShape Pro')

@section('content')
<div class="py-12 space-y-12 animate-fade-in max-w-[1200px] mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest">
                <i class="fas fa-user-plus text-[8px]"></i>
                Gestão de Pacientes
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter leading-none">Novo <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Paciente</span></h1>
            <p class="text-zinc-500 text-lg font-medium">Cadastre um novo aluno ou paciente para iniciar o acompanhamento profissional.</p>
        </div>
        
        <a href="{{ route('professional.patients.index') }}" class="px-6 py-3 bg-zinc-900 text-zinc-400 font-bold rounded-xl hover:bg-zinc-800 transition-all flex items-center gap-2 border border-white/5">
            <i class="fas fa-arrow-left text-xs"></i>
            Cancelar
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
        <form action="{{ route('professional.patients.store') }}" method="POST" class="space-y-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Informações Básicas -->
                <div class="space-y-6">
                    <h3 class="text-white font-black text-xl tracking-tight flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center text-blue-400">
                            <i class="fas fa-id-card text-xs"></i>
                        </div>
                        Informações Básicas
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Nome Completo</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" placeholder="Ex: Carlos Silva">
                            @error('name')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">CPF (Identificador Único)</label>
                            <input type="text" name="cpf" id="cpf_input" value="{{ old('cpf') }}" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" placeholder="000.000.000-00">
                            @error('cpf')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">E-mail</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" placeholder="Ex: carlos@exemplo.com">
                            @error('email')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Telefone / WhatsApp</label>
                            <input type="text" name="phone" id="phone_input" value="{{ old('phone') }}" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white placeholder-zinc-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all" placeholder="(00) 00000-0000">
                            @error('phone')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Configuração Inicial -->
                <div class="space-y-6">
                    <h3 class="text-white font-black text-xl tracking-tight flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600/20 flex items-center justify-center text-indigo-400">
                            <i class="fas fa-bullseye text-xs"></i>
                        </div>
                        Objetivo & Fisiologia
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Objetivo</label>
                            <select name="goal" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all appearance-none">
                                <option value="gain" {{ old('goal') == 'gain' ? 'selected' : '' }}>Hipertrofia</option>
                                <option value="lose" {{ old('goal') == 'lose' ? 'selected' : '' }}>Emagrecimento</option>
                                <option value="recomp" {{ old('goal') == 'recomp' ? 'selected' : '' }}>Recomposição Corporal</option>
                                <option value="performance" {{ old('goal') == 'performance' ? 'selected' : '' }}>Performance</option>
                                <option value="maintain" {{ old('goal') == 'maintain' ? 'selected' : '' }}>Manutenção</option>
                            </select>
                            @error('goal')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Sexo</label>
                            <select name="sex" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all appearance-none">
                                <option value="M" {{ old('sex') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sex') == 'F' ? 'selected' : '' }}>Feminino</option>
                            </select>
                            @error('sex')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2 px-1">Data de Nascimento</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required class="w-full bg-black/40 border border-white/5 rounded-2xl px-5 py-4 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all [color-scheme:dark]">
                            @error('birth_date')
                                <p class="text-red-500 text-[10px] mt-2 px-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-10 border-t border-white/5 flex items-center justify-between">
                <p class="text-zinc-500 text-xs">O paciente receberá um convite por e-mail para acessar o portal.</p>
                
                <button type="submit" class="px-10 py-5 bg-blue-600 text-white font-black rounded-3xl hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px] flex items-center gap-3">
                    Cadastrar Paciente
                    <i class="fas fa-rocket text-[10px]"></i>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('cpf_input').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        let maskedValue = '';
        if (value.length > 0) {
            maskedValue = value.substring(0, 3);
            if (value.length > 3) {
                maskedValue += '.' + value.substring(3, 6);
            }
            if (value.length > 6) {
                maskedValue += '.' + value.substring(6, 9);
            }
            if (value.length > 9) {
                maskedValue += '-' + value.substring(9, 11);
            }
        }
        e.target.value = maskedValue;
    });

    document.getElementById('phone_input').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        let maskedValue = '';
        if (value.length > 0) {
            maskedValue = '(' + value.substring(0, 2);
            if (value.length > 2) {
                maskedValue += ') ' + value.substring(2, 7);
            }
            if (value.length > 7) {
                maskedValue += '-' + value.substring(7, 11);
            }
        }
        e.target.value = maskedValue;
    });
</script>
@endpush

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
