@php
    $accentColor = '#10b981'; // NexShape Emerald
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Primeiro Acesso — NexShape</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --accent: #10b981;
        }
        [x-cloak] { display: none !important; }
        body { 
            background-color: #080a0f; 
            font-family: 'Outfit', sans-serif; 
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        }
        .animate-fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
        
        .premium-input {
            background: rgba(9, 9, 11, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            color: white;
            font-size: 0.875rem;
            transition: all 0.3s;
            outline: none;
            width: 100%;
        }
        .premium-input:focus {
            border-color: rgba(16, 185, 129, 0.3);
            background: rgba(9, 9, 11, 0.8);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.05);
        }
        .premium-card {
            background: rgba(18, 18, 21, 0.4);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .animate-shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</head>
<body class="min-h-screen text-white">

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-20 relative animate-fade-in">
    <div class="max-w-4xl w-full space-y-12 relative z-10">
        
        <div class="text-center space-y-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-zinc-900 border border-zinc-800 shadow-3xl transform -rotate-6">
                <i data-lucide="user-plus" class="w-10 h-10 text-emerald-500"></i>
            </div>
            <div class="space-y-2">
                <h2 class="text-5xl font-black text-white tracking-tighter uppercase italic">Primeiro <span class="text-emerald-500">Acesso</span></h2>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.4em]">Complete seus dados obrigatórios para liberar o acesso ao painel.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 p-6 rounded-3xl animate-shake">
                <div class="flex items-center gap-3 mb-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500"></i>
                    <span class="text-xs font-black uppercase tracking-widest text-rose-500">Atenção aos campos</span>
                </div>
                <ul class="space-y-1 text-rose-400/80 text-[11px] font-bold">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $token === 'logged-in' ? route('patient.profile.store') : route('patient.activate.process', $token) }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Dados Pessoais -->
            <div class="premium-card p-10 space-y-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20 shadow-lg">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">Dados Pessoais</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-6 gap-8">
                    <!-- Nome -->
                    <div class="md:col-span-3 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Nome Completo</label>
                        <input name="name" type="text" required value="{{ old('name', $patient->name) }}"
                            {{ $patient->name ? 'readonly' : '' }}
                            class="premium-input {{ $patient->name ? 'opacity-50 cursor-not-allowed' : '' }}" 
                            placeholder="Seu nome completo">
                    </div>

                    <!-- CPF -->
                    <div class="md:col-span-1.5 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">CPF (Identificador)</label>
                        <input name="cpf" type="text" required value="{{ old('cpf', $patient->cpf) }}"
                            {{ $patient->cpf ? 'readonly' : '' }}
                            class="premium-input {{ $patient->cpf ? 'opacity-50 cursor-not-allowed' : '' }}" 
                            placeholder="000.000.000-00" oninput="maskCPF(this)">
                    </div>

                    <!-- Telefone -->
                    <div class="md:col-span-1.5 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Telefone / WhatsApp</label>
                        <input name="phone" type="text" required value="{{ old('phone', $patient->phone) }}"
                            class="premium-input" 
                            placeholder="(11) 99999-9999" oninput="maskPhone(this)">
                    </div>

                    <!-- Sexo -->
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Sexo</label>
                        <select name="sex" required class="premium-input appearance-none">
                            <option value="">Selecione...</option>
                            <option value="M" {{ old('sex', $patient->profile->sex ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sex', $patient->profile->sex ?? '') == 'F' ? 'selected' : '' }}>Feminino</option>
                        </select>
                    </div>

                    <!-- Data Nascimento -->
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Data de Nascimento</label>
                        <input name="birth_date" type="date" required value="{{ old('birth_date', $patient->profile->birth_date ?? '') }}"
                            class="premium-input">
                    </div>

                    <!-- Altura -->
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Altura (cm)</label>
                        <input name="height_cm" type="number" required value="{{ old('height_cm', $patient->profile->height_cm ?? '') }}"
                            class="premium-input" placeholder="Ex: 175">
                    </div>

                    <!-- Peso -->
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Peso Atual (kg)</label>
                        <input name="weight_kg" type="number" step="0.1" required value="{{ old('weight_kg') }}"
                            class="premium-input" placeholder="Ex: 75.5">
                    </div>

                    <!-- Endereço -->
                    <div class="md:col-span-4 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Endereço Residencial</label>
                        <input name="address" type="text" required value="{{ old('address') }}"
                            class="premium-input" placeholder="Rua, número, complemento">
                    </div>

                    <!-- Cidade -->
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Cidade</label>
                        <input name="city" type="text" required value="{{ old('city') }}"
                            class="premium-input" placeholder="Cidade">
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-1 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Estado (UF)</label>
                        <input name="state" type="text" maxlength="2" required value="{{ old('state') }}"
                            class="premium-input uppercase" placeholder="UF">
                    </div>

                    <!-- Nível Atividade -->
                    <div class="md:col-span-3 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Nível de Atividade</label>
                        <select name="activity_level" required class="premium-input appearance-none">
                            <option value="">Selecione...</option>
                            <option value="sedentary" {{ old('activity_level') == 'sedentary' ? 'selected' : '' }}>Sedentário (Pouco exercício)</option>
                            <option value="lightly_active" {{ old('activity_level') == 'lightly_active' ? 'selected' : '' }}>Leve (1-3 dias)</option>
                            <option value="moderately_active" {{ old('activity_level') == 'moderately_active' ? 'selected' : '' }}>Moderado (3-5 dias)</option>
                            <option value="very_active" {{ old('activity_level') == 'very_active' ? 'selected' : '' }}>Intenso (6-7 dias)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Saúde -->
            <div class="premium-card p-10 space-y-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20 shadow-lg">
                        <i data-lucide="activity" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">Saúde & Objetivos</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    @foreach(['disease' => 'Possui alguma doença?', 'injury' => 'Possui alguma lesão?', 'medication' => 'Usa medicação?', 'allergy' => 'Possui alergia?'] as $key => $label)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase tracking-widest text-zinc-600 ml-2">{{ $label }}</label>
                            <div class="flex gap-2 p-1 bg-zinc-950/50 rounded-xl border border-zinc-900 shadow-inner">
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_{{ $key }}" value="1" class="hidden peer" {{ old('has_'.$key) == '1' ? 'checked' : '' }} onclick="toggleDetails('{{ $key }}_details_container', true)">
                                    <span class="px-5 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest text-zinc-700 peer-checked:bg-emerald-500 peer-checked:text-zinc-950 transition-all block">Sim</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_{{ $key }}" value="0" class="hidden peer" {{ old('has_'.$key, '0') == '0' ? 'checked' : '' }} onclick="toggleDetails('{{ $key }}_details_container', false)">
                                    <span class="px-5 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest text-zinc-700 peer-checked:bg-zinc-800 peer-checked:text-white transition-all block">Não</span>
                                </label>
                            </div>
                        </div>
                        <div id="{{ $key }}_details_container" class="{{ old('has_'.$key) == '1' ? '' : 'hidden' }}">
                            <textarea name="{{ $key }}_details" class="premium-input min-h-[100px] resize-none" placeholder="Especifique detalhes importantes para sua segurança...">{{ old($key.'_details') }}</textarea>
                        </div>
                    </div>
                    @endforeach

                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Objetivo Principal</label>
                        <select name="goal" required class="premium-input appearance-none">
                            <option value="">Selecione...</option>
                            <option value="lose" {{ old('goal') == 'lose' ? 'selected' : '' }}>Emagrecimento</option>
                            <option value="gain" {{ old('goal') == 'gain' ? 'selected' : '' }}>Hipertrofia (Ganho de Massa)</option>
                            <option value="maintain" {{ old('goal') == 'maintain' ? 'selected' : '' }}>Saúde e Bem-Estar</option>
                            <option value="performance" {{ old('goal') == 'performance' ? 'selected' : '' }}>Performance Atlética</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Emergência -->
            <div class="premium-card p-10 space-y-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 border border-rose-500/20 shadow-lg">
                        <i data-lucide="phone-call" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">Contato de Emergência</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Nome do Contato</label>
                        <input name="emergency_contact_name" type="text" required value="{{ old('emergency_contact_name') }}"
                            class="premium-input" placeholder="Nome completo">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Telefone do Contato</label>
                        <input name="emergency_contact_phone" type="text" required value="{{ old('emergency_contact_phone') }}"
                            class="premium-input" placeholder="(11) 99999-9999" oninput="maskPhone(this)">
                    </div>
                </div>
            </div>

            @if($token !== 'logged-in' || !$patient->password_hash)
            <!-- Segurança -->
            <div class="premium-card p-10 space-y-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 shadow-lg">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight">Segurança do Acesso</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Sua Senha</label>
                        <input name="password" type="password" required class="premium-input" placeholder="••••••••">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest ml-2">Confirme a Senha</label>
                        <input name="password_confirmation" type="password" required class="premium-input" placeholder="••••••••">
                    </div>
                </div>
            </div>
            @endif

            <!-- Finalização -->
            <div class="premium-card p-10 space-y-10">
                <div class="space-y-6">
                    <div class="flex items-start gap-5 group cursor-pointer">
                        <input type="checkbox" name="truth_confirmation" id="truth" required class="w-6 h-6 rounded-xl bg-zinc-950 border-zinc-800 text-emerald-500 focus:ring-emerald-500/20 mt-1 cursor-pointer transition-all">
                        <label for="truth" class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.1em] leading-relaxed cursor-pointer group-hover:text-zinc-300 transition-colors">
                            Confirmo que todas as informações acima são verdadeiras e estou ciente da importância desses dados para o meu acompanhamento personalizado.
                        </label>
                    </div>
                    <div class="flex items-start gap-5 group cursor-pointer">
                        <input type="checkbox" name="terms" id="terms" required class="w-6 h-6 rounded-xl bg-zinc-950 border-zinc-800 text-emerald-500 focus:ring-emerald-500/20 mt-1 cursor-pointer transition-all">
                        <label for="terms" class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.1em] leading-relaxed cursor-pointer group-hover:text-zinc-300 transition-colors">
                            Aceito integralmente os <button type="button" onclick="window.openLegalProtocol('terms')" class="text-emerald-500 hover:text-emerald-400 font-black outline-none underline underline-offset-2">Termos de Uso</button> e a <button type="button" onclick="window.openLegalProtocol('privacy')" class="text-emerald-500 hover:text-emerald-400 font-black outline-none underline underline-offset-2">Política de Privacidade</button> da NexShape Pro.
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full py-8 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-[2.5rem] transition-all shadow-3xl shadow-emerald-500/20 uppercase tracking-[0.4em] text-xs transform hover:-translate-y-1 active:translate-y-0">
                    FINALIZAR E ACESSAR PAINEL
                </button>
            </div>
        </form>

        <div class="text-center pb-10">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-[9px] font-black text-zinc-700 uppercase tracking-[0.4em] hover:text-emerald-500 transition-all bg-transparent border-none cursor-pointer outline-none">
                    <i data-lucide="chevron-left" class="w-3 h-3 inline mr-1"></i> Voltar para Login
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function toggleDetails(id, show) {
        const container = document.getElementById(id);
        if (show) {
            container.classList.remove('hidden');
            container.querySelector('textarea').setAttribute('required', 'required');
        } else {
            container.classList.add('hidden');
            container.querySelector('textarea').removeAttribute('required');
        }
    }
</script>

@include('partials.toast')
@include('partials.legal-modal')
@include('partials.js-masks')
</body>
</html>
