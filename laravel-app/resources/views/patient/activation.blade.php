@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#3d9cf5');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complementação de Dados — NexShape</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --accent: {{ $accentColor }};
            --primary-gradient: linear-gradient(135deg, {{ $accentColor }} 0%, {{ $accentColor }}cc 100%);
        }
        body { background-color: #0b0e14; font-family: 'Inter', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
    </style>
</head>
<body class="min-h-screen bg-[#0b0e14] text-white">

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 relative animate-fade-in overflow-x-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-600/10 rounded-full blur-[150px] pointer-events-none"></div>

    <div class="max-w-4xl w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-2xl">
                <i class="fas fa-file-medical text-3xl text-blue-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Primeiro Acesso</h2>
            <p class="mt-2 text-xs text-zinc-500 font-bold uppercase tracking-widest">Complete seus dados obrigatórios para liberar o acesso ao painel.</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 p-4 rounded-2xl">
                <ul class="list-disc list-inside text-rose-400 text-[11px] font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $token === 'logged-in' ? route('patient.profile.store') : route('patient.activate.process', $token) }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Seção 1: Dados Pessoais -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <i class="fas fa-user text-blue-500 text-xs"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg">Dados Pessoais</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-2 col-span-1 md:col-span-2 lg:col-span-1">
                        <label for="name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome Completo</label>
                        <input id="name" name="name" type="text" required value="{{ old('name', $patient->name) }}"
                            {{ $patient->name ? 'readonly' : '' }}
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700 {{ $patient->name ? 'opacity-60 cursor-not-allowed' : '' }}" 
                            placeholder="Seu nome completo">
                    </div>

                    <div class="space-y-2">
                        <label for="cpf" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">CPF (Identificador)</label>
                        <input id="cpf" name="cpf" type="text" required value="{{ old('cpf', $patient->cpf) }}"
                            {{ $patient->cpf ? 'readonly' : '' }}
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700 {{ $patient->cpf ? 'opacity-60 cursor-not-allowed' : '' }}" 
                            placeholder="000.000.000-00" oninput="maskCPF(this)">
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Telefone / WhatsApp</label>
                        <input id="phone" name="phone" type="text" required value="{{ old('phone', $patient->phone) }}"
                            {{ $patient->phone ? 'readonly' : '' }}
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700 {{ $patient->phone ? 'opacity-60 cursor-not-allowed' : '' }}" 
                            placeholder="(00) 00000-0000">
                    </div>

                    <div class="space-y-2">
                        <label for="sex" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Sexo</label>
                        <div class="relative">
                            <select id="sex" name="sex" required 
                                {{ ($patient->profile->sex ?? '') ? 'disabled' : '' }}
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none {{ ($patient->profile->sex ?? '') ? 'opacity-60 cursor-not-allowed pointer-events-none' : '' }}">
                                <option value="">Selecione...</option>
                                <option value="M" {{ old('sex', $patient->profile->sex ?? '') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('sex', $patient->profile->sex ?? '') == 'F' ? 'selected' : '' }}>Feminino</option>
                            </select>
                            @if($patient->profile->sex ?? '')
                                <input type="hidden" name="sex" value="{{ $patient->profile->sex }}">
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="birth_date" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Data de Nascimento</label>
                        <input id="birth_date" name="birth_date" type="date" required value="{{ old('birth_date', $patient->profile->birth_date ?? '') }}"
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all [color-scheme:dark]">
                    </div>

                    <div class="space-y-2">
                        <label for="height_cm" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Altura (cm)</label>
                        <input id="height_cm" name="height_cm" type="number" required value="{{ old('height_cm', $patient->profile->height_cm ?? '') }}"
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all" placeholder="Ex: 175">
                    </div>

                    <div class="space-y-2">
                        <label for="weight_kg" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Peso Atual (kg)</label>
                        <input id="weight_kg" name="weight_kg" type="number" step="0.1" required value="{{ old('weight_kg') }}"
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all" placeholder="Ex: 75.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div class="space-y-2 md:col-span-2">
                        <label for="address" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Endereço Residencial</label>
                        <input id="address" name="address" type="text" required value="{{ old('address') }}"
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all" placeholder="Rua, número, complemento">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="city" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Cidade</label>
                            <input id="city" name="city" type="text" required value="{{ old('city') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all" placeholder="Cidade">
                        </div>
                        <div class="space-y-2">
                            <label for="state" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Estado (UF)</label>
                            <input id="state" name="state" type="text" maxlength="2" required value="{{ old('state') }}"
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all uppercase" placeholder="UF">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção 2: Dados de Saúde -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl space-y-6">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <i class="fas fa-heartbeat text-emerald-500 text-xs"></i>
                    </div>
                    <h3 class="text-white font-bold text-lg">Informações de Saúde</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Doença -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-zinc-300 font-medium">Possui alguma doença?</label>
                            <div class="flex bg-zinc-950/50 p-1 rounded-xl border border-white/5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_disease" value="1" class="hidden peer" {{ old('has_disease') == '1' ? 'checked' : '' }} onclick="toggleDetails('disease_details_container', true)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-blue-600 peer-checked:text-white transition-all block">Sim</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_disease" value="0" class="hidden peer" {{ old('has_disease', '0') == '0' ? 'checked' : '' }} onclick="toggleDetails('disease_details_container', false)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-zinc-800 peer-checked:text-white transition-all block">Não</span>
                                </label>
                            </div>
                        </div>
                        <div id="disease_details_container" class="{{ old('has_disease') == '1' ? '' : 'hidden' }}">
                            <textarea name="disease_details" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Quais?">{{ old('disease_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Lesão -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-zinc-300 font-medium">Possui alguma lesão?</label>
                            <div class="flex bg-zinc-950/50 p-1 rounded-xl border border-white/5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_injury" value="1" class="hidden peer" {{ old('has_injury') == '1' ? 'checked' : '' }} onclick="toggleDetails('injury_details_container', true)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-blue-600 peer-checked:text-white transition-all block">Sim</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_injury" value="0" class="hidden peer" {{ old('has_injury', '0') == '0' ? 'checked' : '' }} onclick="toggleDetails('injury_details_container', false)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-zinc-800 peer-checked:text-white transition-all block">Não</span>
                                </label>
                            </div>
                        </div>
                        <div id="injury_details_container" class="{{ old('has_injury') == '1' ? '' : 'hidden' }}">
                            <textarea name="injury_details" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Onde? Qual o tipo?">{{ old('injury_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Medicação -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-zinc-300 font-medium">Usa alguma medicação?</label>
                            <div class="flex bg-zinc-950/50 p-1 rounded-xl border border-white/5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="uses_medication" value="1" class="hidden peer" {{ old('uses_medication') == '1' ? 'checked' : '' }} onclick="toggleDetails('medication_details_container', true)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-blue-600 peer-checked:text-white transition-all block">Sim</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="uses_medication" value="0" class="hidden peer" {{ old('uses_medication', '0') == '0' ? 'checked' : '' }} onclick="toggleDetails('medication_details_container', false)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-zinc-800 peer-checked:text-white transition-all block">Não</span>
                                </label>
                            </div>
                        </div>
                        <div id="medication_details_container" class="{{ old('uses_medication') == '1' ? '' : 'hidden' }}">
                            <textarea name="medication_details" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="Quais medicamentos e horários?">{{ old('medication_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Alergia -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-zinc-300 font-medium">Possui alguma alergia?</label>
                            <div class="flex bg-zinc-950/50 p-1 rounded-xl border border-white/5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_allergy" value="1" class="hidden peer" {{ old('has_allergy') == '1' ? 'checked' : '' }} onclick="toggleDetails('allergy_details_container', true)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-blue-600 peer-checked:text-white transition-all block">Sim</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="has_allergy" value="0" class="hidden peer" {{ old('has_allergy', '0') == '0' ? 'checked' : '' }} onclick="toggleDetails('allergy_details_container', false)">
                                    <span class="px-4 py-2 rounded-lg text-xs font-bold text-zinc-500 peer-checked:bg-zinc-800 peer-checked:text-white transition-all block">Não</span>
                                </label>
                            </div>
                        </div>
                        <div id="allergy_details_container" class="{{ old('has_allergy') == '1' ? '' : 'hidden' }}">
                            <textarea name="allergy_details" class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="A que possui alergia?">{{ old('allergy_details') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div class="space-y-2">
                        <label for="activity_level" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nível de Atividade Física</label>
                        <select id="activity_level" name="activity_level" required class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all">
                            <option value="">Selecione...</option>
                            <option value="sedentary" {{ old('activity_level') == 'sedentary' ? 'selected' : '' }}>Sedentário (Pouco ou nenhum exercício)</option>
                            <option value="lightly_active" {{ old('activity_level') == 'lightly_active' ? 'selected' : '' }}>Levemente Ativo (Exercício 1-3 dias/semana)</option>
                            <option value="moderately_active" {{ old('activity_level') == 'moderately_active' ? 'selected' : '' }}>Moderadamente Ativo (Exercício 3-5 dias/semana)</option>
                            <option value="very_active" {{ old('activity_level') == 'very_active' ? 'selected' : '' }}>Muito Ativo (Exercício pesado 6-7 dias/semana)</option>
                            <option value="extra_active" {{ old('activity_level') == 'extra_active' ? 'selected' : '' }}>Extra Ativo (Trabalho físico intenso ou atleta)</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="goal" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Objetivo Principal</label>
                        <div class="relative">
                            <select id="goal" name="goal" required 
                                {{ ($patient->profile->goal ?? '') ? 'disabled' : '' }}
                                class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all appearance-none {{ ($patient->profile->goal ?? '') ? 'opacity-60 cursor-not-allowed pointer-events-none' : '' }}">
                                <option value="">Selecione...</option>
                                <option value="lose" {{ old('goal', $patient->profile->goal ?? '') == 'lose' ? 'selected' : '' }}>Emagrecimento</option>
                                <option value="gain" {{ old('goal', $patient->profile->goal ?? '') == 'gain' ? 'selected' : '' }}>Hipertrofia (Ganho de Massa)</option>
                                <option value="maintain" {{ old('goal', $patient->profile->goal ?? '') == 'maintain' ? 'selected' : '' }}>Saúde e Bem-estar</option>
                                <option value="performance" {{ old('goal', $patient->profile->goal ?? '') == 'performance' ? 'selected' : '' }}>Performance Atlética</option>
                                <option value="rehab" {{ old('goal', $patient->profile->goal ?? '') == 'rehab' ? 'selected' : '' }}>Reabilitação / Fisioterapia</option>
                            </select>
                            @if($patient->profile->goal ?? '')
                                <input type="hidden" name="goal" value="{{ $patient->profile->goal }}">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção 3: Contato de Emergência e Acesso -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center">
                                <i class="fas fa-phone-alt text-rose-500 text-xs"></i>
                            </div>
                            <h3 class="text-white font-bold text-lg">Contato de Emergência</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="emergency_contact_name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nome do Contato</label>
                                <input id="emergency_contact_name" name="emergency_contact_name" type="text" required value="{{ old('emergency_contact_name') }}"
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all" placeholder="Nome completo">
                            </div>
                            <div class="space-y-2">
                                <label for="emergency_contact_phone" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Telefone do Contato</label>
                                <input id="emergency_contact_phone" name="emergency_contact_phone" type="text" required value="{{ old('emergency_contact_phone') }}"
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                    </div>

                    @if($token !== 'logged-in' || !$patient->password_hash)
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/20 flex items-center justify-center">
                                <i class="fas fa-lock text-blue-500 text-xs"></i>
                            </div>
                            <h3 class="text-white font-bold text-lg">Segurança do Acesso</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Defina sua Senha</label>
                                <input id="password" name="password" type="password" required
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="••••••••">
                            </div>
                            <div class="space-y-2">
                                <label for="password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirme a Senha</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Seção Final: Termos e Botão -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-8 rounded-[2.5rem] shadow-2xl">
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="relative flex items-center mt-1">
                            <input type="checkbox" name="truth_confirmation" id="truth_confirmation" required
                                class="peer h-5 w-5 bg-zinc-950/50 border border-white/5 rounded-lg checked:bg-blue-600 transition-all outline-none">
                            <i class="fas fa-check absolute left-1 text-[10px] font-black text-white scale-0 peer-checked:scale-100 transition-transform pointer-events-none"></i>
                        </div>
                        <label for="truth_confirmation" class="text-[11px] text-zinc-400 font-medium leading-relaxed">
                            Confirmo que todas as informações acima são verdadeiras e estou ciente da importância desses dados para o meu acompanhamento profissional.
                        </label>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="relative flex items-center mt-1">
                            <input type="checkbox" name="terms" id="terms" required
                                class="peer h-5 w-5 bg-zinc-950/50 border border-white/5 rounded-lg checked:bg-blue-600 transition-all outline-none">
                            <i class="fas fa-check absolute left-1 text-[10px] font-black text-white scale-0 peer-checked:scale-100 transition-transform pointer-events-none"></i>
                        </div>
                        <label for="terms" class="text-[11px] text-zinc-400 font-medium leading-relaxed">
                            Aceito os <a href="{{ route('legal.terms') }}" target="_blank" class="text-blue-500 hover:underline">Termos de Uso</a> e a <a href="{{ route('legal.privacy') }}" target="_blank" class="text-blue-500 hover:underline">Política de Privacidade</a> do sistema NexShape.
                        </label>
                    </div>

                    <button type="submit" class="w-full mt-6 py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-xs">
                        Concluir Cadastro e Ir para Login
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest hover:text-blue-500 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Voltar para o Login
            </a>
        </div>
    </div>
</div>

<script>
    function maskCPF(i) {
        let v = i.value.replace(/\D/g, '');
        if (v.length > 11) v = v.substring(0, 11);
        
        let m = '';
        if (v.length > 0) {
            m = v.substring(0, 3);
            if (v.length > 3) m += '.' + v.substring(3, 6);
            if (v.length > 6) m += '.' + v.substring(6, 9);
            if (v.length > 9) m += '-' + v.substring(9, 11);
        }
        i.value = m;
    }

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
</body>
</html>
