@extends('layouts.app')

@section('title', 'Criar Conta — NEX SHAPE PRO')

@section('content')
<div class="min-h-screen overflow-y-auto overflow-x-hidden px-6 py-6 relative animate-fade-in-up" 
     x-data="{ 
        step: 0, 
        tipo_acesso: '{{ old('tipo_acesso', '') }}',
        professions: @js(\App\Models\Profession::all())
     }"
     x-init="if(tipo_acesso) step = 1; lucide.createIcons();">
    
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-emerald-500/5 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- Floating Back Button -->
    <div class="fixed top-8 left-8 z-[500]">
        <!-- Link to Home (Step 0) -->
        <a href="{{ route('home') }}" 
           x-show="step === 0"
           class="w-12 h-12 bg-zinc-900/80 backdrop-blur-2xl border border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/50 transition-all duration-500 group shadow-2xl hover:scale-110 active:scale-95"
           title="Voltar para Home">
            <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
        </a>

        <!-- Button to Step 0 (Step 1+) -->
        <button @click="step = 0; $nextTick(() => lucide.createIcons())" 
           x-show="step > 0"
           style="display: none;"
           class="w-12 h-12 bg-zinc-900/80 backdrop-blur-2xl border border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/50 transition-all duration-500 group shadow-2xl hover:scale-110 active:scale-95"
           title="Voltar para Seleção de Protocolo">
            <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
        </button>
    </div>

    <div class="max-w-3xl w-full mx-auto space-y-8 relative z-10 transition-all duration-700">
        
        <!-- STEP 0: SELEÇÃO DE TIPO -->
        <div x-show="step === 0" 
             x-transition:enter="transition ease-out duration-500 transform" 
             x-transition:enter-start="opacity-0 translate-y-8" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             class="space-y-12">
            
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-[1.5rem] bg-zinc-900 border border-zinc-800 mb-4 shadow-3xl backdrop-blur-xl transform -rotate-6">
                    <i data-lucide="shield-check" class="w-8 h-8 text-emerald-500"></i>
                </div>
                <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Selecione seu <span class="text-emerald-500">Protocolo</span></h2>
                <p class="text-zinc-600 font-black uppercase tracking-[0.4em] text-[8px]">Defina seu papel no ecossistema de alta performance</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card Aluno -->
                <button @click="tipo_acesso = 'aluno'; step = 1; $nextTick(() => lucide.createIcons())" 
                        class="group relative bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 p-6 rounded-[2.5rem] text-left hover:border-emerald-500/50 transition-all hover:scale-[1.02] shadow-3xl overflow-hidden flex flex-col min-h-[380px]">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-colors"></div>
                    
                    <div class="w-14 h-14 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-8 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all duration-500 group-hover:rotate-6 shadow-lg">
                        <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                    </div>

                    <div class="space-y-2 mb-6">
                        <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.3em]">Protocolo Performance</span>
                        <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Sou <span class="text-emerald-500">Aluno</span></h3>
                    </div>

                    <p class="text-xs text-zinc-500 leading-relaxed font-medium group-hover:text-zinc-300 transition-colors italic mb-8">
                        Treine com precisão, acompanhe sua evolução e receba protocolos de elite.
                    </p>

                    <ul class="space-y-4 mt-auto border-t border-zinc-800/50 pt-6">
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Treinos & Dietas Guiados
                        </li>
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Dashboard Bio-Evolução
                        </li>
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Canal com Especialista
                        </li>
                    </ul>
                </button>

                <!-- Card Profissional -->
                <button @click="tipo_acesso = 'professional'; step = 1; $nextTick(() => lucide.createIcons())" 
                        class="group relative bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 p-6 rounded-[2.5rem] text-left hover:border-emerald-500/50 transition-all hover:scale-[1.02] shadow-3xl overflow-hidden flex flex-col min-h-[380px]">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-colors"></div>
                    
                    <div class="w-14 h-14 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-8 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all duration-500 group-hover:-rotate-6 shadow-lg">
                        <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>

                    <div class="space-y-2 mb-6">
                        <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.3em]">Elite Pro Specialist</span>
                        <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Sou <span class="text-emerald-500">Especialista</span></h3>
                    </div>

                    <p class="text-xs text-zinc-500 leading-relaxed font-medium group-hover:text-zinc-300 transition-colors italic mb-8">
                        Gerencie atletas e prescreva protocolos avançados com autoridade técnica.
                    </p>

                    <ul class="space-y-4 mt-auto border-t border-zinc-800/50 pt-6">
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Construtor de Protocolos
                        </li>
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Análise de Bio-Data
                        </li>
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Gestão de Atletas & CRM
                        </li>
                    </ul>
                </button>

                <!-- Card Clínica -->
                <button @click="tipo_acesso = 'manager'; step = 1; $nextTick(() => lucide.createIcons())" 
                        class="group relative bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 p-6 rounded-[2.5rem] text-left hover:border-emerald-500/50 transition-all hover:scale-[1.02] shadow-3xl overflow-hidden flex flex-col min-h-[380px]">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-colors"></div>
                    
                    <div class="w-14 h-14 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-8 group-hover:bg-emerald-500 group-hover:text-zinc-950 transition-all duration-500 group-hover:scale-110 shadow-lg">
                        <i data-lucide="building" class="w-6 h-6"></i>
                    </div>

                    <div class="space-y-2 mb-6">
                        <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.3em]">Institutional Control</span>
                        <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Sou <span class="text-emerald-500">Clínica</span></h3>
                    </div>

                    <p class="text-xs text-zinc-500 leading-relaxed font-medium group-hover:text-zinc-300 transition-colors italic mb-8">
                        Centralize o comando da sua unidade e maximize a performance operacional.
                    </p>

                    <ul class="space-y-4 mt-auto border-t border-zinc-800/50 pt-6">
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Gestão Multi-Unidades
                        </li>
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            BI & Relatórios Centrais
                        </li>
                        <li class="flex items-center gap-3 text-[10px] text-zinc-600 font-bold uppercase tracking-wider group-hover:text-zinc-400">
                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Controle de Especialistas
                        </li>
                    </ul>
                </button>
            </div>

            <!-- Representante Section -->
            <div class="mt-8 p-8 rounded-[3rem] bg-zinc-900/30 border border-zinc-800/50 backdrop-blur-sm relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 blur-[80px] rounded-full pointer-events-none group-hover:bg-emerald-500/10 transition-colors duration-700"></div>
                
                <div class="relative flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-zinc-950 border border-zinc-800 flex items-center justify-center shrink-0 shadow-2xl group-hover:border-emerald-500/30 transition-all duration-500 group-hover:-rotate-3">
                        <i data-lucide="handshake" class="w-8 h-8 text-emerald-500"></i>
                    </div>

                    <div class="flex-1 text-center md:text-left space-y-3">
                        <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Seja um <span class="text-emerald-500">Representante</span></h3>
                        <p class="text-xs text-zinc-500 font-medium leading-relaxed max-w-xl">
                            Ganhe comissões indicando nosso sistema para profissionais e clínicas. Buscamos parceiros para expandir nossa solução.
                        </p>
                        
                        <div class="flex flex-wrap justify-center md:justify-start gap-x-8 gap-y-4 pt-2">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">Comissão por venda</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">Receita recorrente</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] text-zinc-400 font-black uppercase tracking-widest">Sem limite de ganhos</span>
                            </div>
                        </div>
                    </div>

                    <button @click="tipo_acesso = 'representative'; step = 1; $nextTick(() => lucide.createIcons())" 
                            class="px-6 py-4 bg-emerald-500/10 hover:bg-emerald-500 border border-emerald-500/20 hover:border-emerald-500 text-emerald-500 hover:text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all duration-500 active:scale-95 shrink-0 shadow-xl hover:shadow-emerald-500/20">
                        Quero ser representante
                    </button>
                </div>
            </div>

            <div class="text-center mt-12">
                <p class="text-[10px] text-zinc-700 font-black uppercase tracking-[0.3em]">
                    Já possui credenciais de acesso?
                    <a href="{{ route('login') }}" class="text-emerald-500 hover:text-emerald-400 ml-2 transition-colors border-b border-emerald-500/20 pb-1">Autenticar agora &rarr;</a>
                </p>
            </div>
        </div>

        <!-- STEP 1: FORMULÁRIO COMPLETO -->
        <div x-show="step === 1" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-500 transform" 
             x-transition:enter-start="opacity-0 translate-y-8" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             id="register-step">
            
            <div class="text-center mb-6 space-y-2">
                <button @click="step = 0; $nextTick(() => lucide.createIcons())" class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-700 hover:text-emerald-500 transition-colors mb-4 mx-auto flex items-center gap-2 group">
                    <i data-lucide="arrow-left" class="w-2.5 h-2.5 group-hover:-translate-x-1 transition-transform"></i> Voltar
                </button>
                <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Identidade <span class="text-emerald-500">Digital</span></h2>
                <div class="flex justify-center pt-1">
                    <div class="inline-flex items-center gap-3 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.2em]" x-text="tipo_acesso === 'aluno' ? 'Aluno Performance' : (tipo_acesso === 'professional' ? 'Especialista Elite' : (tipo_acesso === 'manager' ? 'Admin Clínica' : 'Representante'))"></span>
                    </div>
                </div>
            </div>

            <div id="register-errors" class="hidden p-6 bg-rose-500/10 border border-rose-500/20 rounded-3xl text-rose-500 text-xs font-black mb-8 animate-shake"></div>

            <div class="bg-zinc-900/80 backdrop-blur-2xl border border-zinc-800 p-8 md:p-10 rounded-[2.5rem] shadow-3xl relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none"></div>

                <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-6" novalidate>
                    @csrf
                    <input type="hidden" name="tipo_acesso" x-model="tipo_acesso">

                    <!-- Shared Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="space-y-3 group">
                            <label for="name" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors" x-text="tipo_acesso === 'manager' ? 'Razão Social' : 'Nome Completo'"></label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                :placeholder="tipo_acesso === 'manager' ? 'NOME EMPRESARIAL' : 'IDENTIFICAÇÃO COMPLETA'">
                        </div>

                        <div class="space-y-3 group">
                            <label for="email" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">E-mail Corporativo</label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                placeholder="EXEMPLO@PLATAFORMA.COM">
                        </div>

                        <div class="space-y-3 group" x-show="tipo_acesso !== 'manager'">
                            <label for="cpf" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Documento CPF</label>
                            <input id="cpf" name="cpf" type="text" :required="tipo_acesso !== 'manager'" maxlength="14" value="{{ old('cpf') }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                placeholder="000.000.000-00"
                                oninput="maskCpf(this)">
                        </div>

                        <div class="space-y-3 group" x-show="tipo_acesso === 'manager'" x-cloak>
                            <label for="cnpj" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Documento CNPJ</label>
                            <input id="cnpj" name="cnpj" type="text" :required="tipo_acesso === 'manager'" maxlength="18" value="{{ old('cnpj') }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                placeholder="00.000.000/0000-00"
                                oninput="maskCnpj(this)">
                        </div>

                        <div class="space-y-3 group" x-show="tipo_acesso === 'manager'" x-cloak>
                            <label for="referral_code" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Código do Representante (Opcional)</label>
                            <input id="referral_code" name="referral_code" type="text" value="{{ old('referral_code', session('referral_code')) }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                placeholder="CÓDIGO (EX: MARCO001)">
                        </div>

                        <div class="space-y-3 group">
                            <label for="phone" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Telefone Sinc</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                placeholder="(00) 00000-0000"
                                oninput="maskPhone(this)">
                        </div>

                        <div class="space-y-3 group" x-show="tipo_acesso !== 'manager'">
                            <label for="birth_date" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Data de Nascimento</label>
                            <input id="birth_date" name="birth_date" type="date" :required="tipo_acesso !== 'manager'" value="{{ old('birth_date') }}"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all [color-scheme:dark] shadow-inner">
                        </div>

                        <div class="space-y-3 group" x-show="tipo_acesso !== 'manager'">
                            <label for="sex" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Sexo Biológico</label>
                            <select id="sex" name="sex" :required="tipo_acesso !== 'manager'"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all cursor-pointer shadow-inner">
                                <option value="" class="bg-zinc-950">SELECIONE...</option>
                                <option value="M" class="bg-zinc-950" @selected(old('sex') === 'M')>MASCULINO</option>
                                <option value="F" class="bg-zinc-950" @selected(old('sex') === 'F')>FEMININO</option>
                            </select>
                        </div>
                    </div>

                    <!-- Professional Specific Fields -->
                    <div x-show="tipo_acesso === 'professional'" 
                         x-collapse
                         class="space-y-8 pt-10 border-t border-zinc-800 mt-4 bg-emerald-500/[0.02] -mx-16 px-16 pb-10">
                        <div class="space-y-3 group">
                            <label class="text-[10px] text-emerald-500 font-black uppercase tracking-[0.3em] ml-2 transition-colors">Especialidade Principal *</label>
                            <select name="profession_id" :required="tipo_acesso === 'professional'"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all shadow-inner">
                                <option value="" class="bg-zinc-950">SELECIONE SUA PROFISSÃO...</option>
                                <template x-for="p in professions" :key="p.id">
                                    <option :value="p.id" x-text="p.name.toUpperCase()" class="bg-zinc-950" :selected="p.id == {{ old('profession_id', 0) }}"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3 group">
                                <label class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Registro Profissional</label>
                                <input name="registration_number" type="text" value="{{ old('registration_number') }}"
                                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                    placeholder="EX: CREF/CRM 0000">
                            </div>
                            <div class="space-y-3 group">
                                <label class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Sub-Especialidade</label>
                                <input name="specialty" type="text" value="{{ old('specialty') }}"
                                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                    placeholder="EX: MUSCULAÇÃO / NUTRI ESP">
                            </div>
                        </div>
                    </div>

                    <!-- Passwords -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                        <div class="space-y-3 group">
                            <label for="password" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Chave de Acesso</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required minlength="8"
                                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 pr-12 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                    placeholder="••••••••">
                                <button type="button" onclick="toggleRegisterPass('password', 'eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 hover:text-emerald-500 transition-colors">
                                    <i data-lucide="eye" id="eye1" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3 group">
                            <label for="password_confirmation" class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em] ml-2 group-focus-within:text-emerald-500 transition-colors">Confirmar Chave</label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-5 py-3 pr-12 text-white text-sm font-bold outline-none focus:border-emerald-500/50 transition-all placeholder:text-zinc-900 shadow-inner"
                                    placeholder="••••••••">
                                <button type="button" onclick="toggleRegisterPass('password_confirmation', 'eye2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 hover:text-emerald-500 transition-colors">
                                    <i data-lucide="eye" id="eye2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <label class="flex items-start gap-5 cursor-pointer group pt-4">
                        <input type="checkbox" id="terms" name="terms" value="1" required class="peer sr-only">
                        <div class="w-7 h-7 mt-0.5 rounded-xl bg-zinc-950 border border-zinc-800 peer-checked:bg-emerald-500 peer-checked:border-emerald-500 transition-all flex items-center justify-center shadow-inner group-hover:border-emerald-500/30">
                            <i data-lucide="check" class="w-4 h-4 text-zinc-950 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-[11px] text-zinc-600 leading-relaxed font-medium group-hover:text-zinc-400 transition-colors">
                            Declaro estar ciente e de acordo com os <button type="button" onclick="window.openLegalProtocol('terms')" class="text-emerald-500 hover:text-emerald-400 font-black transition-all underline underline-offset-4 uppercase tracking-widest outline-none">Termos de Uso</button> e 
                            <button type="button" onclick="window.openLegalProtocol('privacy')" class="text-emerald-500 hover:text-emerald-400 font-black transition-all underline underline-offset-4 uppercase tracking-widest outline-none">Protocolo de Privacidade (LGPD)</button>.
                        </span>
                    </label>

                    <button type="submit" id="register-submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 disabled:opacity-30 text-zinc-950 font-black rounded-2xl transition-all active:scale-[0.98] shadow-2xl shadow-emerald-500/20 uppercase tracking-[0.3em] text-[10px] mt-4 flex items-center justify-center gap-3 group">
                        ATIVAR PROTOCOLO NEX SHAPE
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Duplicate Modal --}}
<div id="register-duplicate-modal" class="fixed inset-0 z-[500] hidden flex items-center justify-center p-6" role="dialog" aria-modal="true" aria-hidden="true">
    <div data-reg-dup-backdrop class="absolute inset-0 bg-zinc-950/90 backdrop-blur-xl"></div>
    <div class="relative w-full max-w-md rounded-[3rem] border border-amber-500/20 bg-zinc-900 shadow-3xl p-10 text-center space-y-8 animate-fade-in-up">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[1.5rem] bg-amber-500/10 text-amber-500 shadow-lg shadow-amber-500/5">
            <i data-lucide="shield-alert" class="w-10 h-10"></i>
        </div>
        <div class="space-y-3">
            <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic">Identidade Existente</h3>
            <p id="register-duplicate-message" class="text-sm text-zinc-500 leading-relaxed font-medium"></p>
        </div>
        <button type="button" id="register-duplicate-close" class="w-full py-5 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-600 hover:text-white text-[10px] font-black uppercase tracking-widest transition-all">
            ENTENDI
        </button>
    </div>
</div>

{{-- Confirmation Modal --}}
<div id="register-confirm-modal" class="fixed inset-0 z-[500] hidden flex items-center justify-center p-6" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="absolute inset-0 bg-zinc-950/90 backdrop-blur-xl"></div>
    <div class="relative w-full max-w-2xl rounded-[3rem] border border-emerald-500/20 bg-zinc-900 shadow-3xl p-8 md:p-12 space-y-8 animate-fade-in-up overflow-y-auto max-h-[90vh]">
        <div class="text-center space-y-2">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.2rem] bg-emerald-500/10 text-emerald-500 shadow-lg shadow-emerald-500/5 mb-4">
                <i data-lucide="file-check-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic">Conferir <span class="text-emerald-500">Protocolo</span></h3>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">Valide suas credenciais antes da ativação final</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-zinc-950/50 border border-zinc-800/50 rounded-[2rem] p-6 md:p-8">
            <div class="space-y-1">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Nome / Razão Social</span>
                <p id="confirm-name" class="text-sm text-white font-bold truncate"></p>
            </div>
            <div class="space-y-1">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">E-mail Corporativo</span>
                <p id="confirm-email" class="text-sm text-white font-bold truncate"></p>
            </div>
            <div class="space-y-1">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Documento (CPF/CNPJ)</span>
                <p id="confirm-cpf" class="text-sm text-white font-bold"></p>
            </div>
            <div class="space-y-1">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Telefone</span>
                <p id="confirm-phone" class="text-sm text-white font-bold"></p>
            </div>
            <div class="space-y-1" id="confirm-birth-wrapper">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Data de Nascimento</span>
                <p id="confirm-birth" class="text-sm text-white font-bold"></p>
            </div>
            <div class="space-y-1">
                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Perfil de Acesso</span>
                <p id="confirm-profile" class="text-sm text-emerald-500 font-black italic uppercase"></p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 pt-4">
            <button type="button" id="confirm-back" class="flex-1 py-4 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-500 hover:text-white text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:bg-zinc-800 active:scale-95">
                Voltar e Corrigir
            </button>
            <button type="button" id="confirm-final" class="flex-1 py-4 rounded-2xl bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:bg-emerald-400 shadow-xl shadow-emerald-500/20 active:scale-95">
                Confirmar e Finalizar Cadastro
            </button>
        </div>
    </div>
</div>


<script>
    function toggleRegisterPass(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        icon.setAttribute('data-lucide', type === 'text' ? 'eye-off' : 'eye');
        lucide.createIcons();
    }

    const REGISTER_REDIRECT_FALLBACK = @json(route('registration.pending'));

    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        const closeBtn = document.getElementById('register-duplicate-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                document.getElementById('register-duplicate-modal').classList.add('hidden');
                document.body.style.overflow = '';
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('register-form');
        const submitBtn = document.getElementById('register-submit');
        const errorDiv = document.getElementById('register-errors');
        const dupModal = document.getElementById('register-duplicate-modal');
        const dupMsg = document.getElementById('register-duplicate-message');
        
        const confirmModal = document.getElementById('register-confirm-modal');
        const confirmBack = document.getElementById('confirm-back');
        const confirmFinal = document.getElementById('confirm-final');

        if (!form) return;

        // Function to show confirmation modal
        const showConfirmation = () => {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const cpfInput = document.getElementById('cpf');
            const cnpjInput = document.getElementById('cnpj');
            const cpf = (cpfInput && cpfInput.offsetParent !== null ? cpfInput.value : (cnpjInput ? cnpjInput.value : 'N/A')) || 'N/A';
            const phone = document.getElementById('phone').value || 'Não informado';
            const birthDate = document.getElementById('birth_date')?.value || 'N/A';
            
            // Get profile text from the badge in the form
            const profileText = document.querySelector('[x-text*="tipo_acesso"]').innerText;

            document.getElementById('confirm-name').textContent = name;
            document.getElementById('confirm-email').textContent = email;
            document.getElementById('confirm-cpf').textContent = cpf;
            document.getElementById('confirm-phone').textContent = phone;
            document.getElementById('confirm-birth').textContent = birthDate;
            document.getElementById('confirm-profile').textContent = profileText;

            // Hide birth date if manager
            const tipo_acesso = form.querySelector('[name="tipo_acesso"]').value;
            if (tipo_acesso === 'manager') {
                document.getElementById('confirm-birth-wrapper').style.display = 'none';
            } else {
                document.getElementById('confirm-birth-wrapper').style.display = 'block';
            }

            confirmModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        };

        const closeConfirmation = () => {
            confirmModal.classList.add('hidden');
            document.body.style.overflow = '';
        };

        confirmBack.addEventListener('click', closeConfirmation);

        confirmFinal.addEventListener('click', () => {
            closeConfirmation();
            executeRegistration();
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Custom terms validation
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                alert('Você precisa aceitar os termos para continuar.');
                return;
            }

            showConfirmation();
        });

        const executeRegistration = async () => {
            // Reset UI
            errorDiv.classList.add('hidden');
            errorDiv.innerHTML = '';
            submitBtn.disabled = true;
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> PROCESSANDO PROTOCOLO...';
            lucide.createIcons();

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    console.log('DEBUG: Despachando evento de sucesso', { email: data.email, redirect: data.redirect });
                    // Success! Dispatch event for the success modal
                    window.dispatchEvent(new CustomEvent('registration-success', { 
                        detail: { 
                            email: data.email,
                            message: data.message,
                            redirect: data.redirect
                        } 
                    }));

                    // Keep button disabled
                    submitBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> PROTOCOLO ATIVADO';
                    lucide.createIcons();
                } else if (response.status === 422) {
                    // Validation Errors
                    if (data.duplicate_registration) {
                        let msg = '';
                        if (data.duplicate_registration === 'both') {
                            msg = 'O E-mail e o CPF informados já possuem um registro ativo em nossa base de dados.';
                        } else if (data.duplicate_registration === 'email') {
                            msg = 'O E-mail informado já possui um registro ativo em nossa base de dados.';
                        } else {
                            msg = 'O CPF informado já possui um registro ativo em nossa base de dados.';
                        }
                        dupMsg.textContent = msg;
                        dupModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    } else {
                        // Show errors in the errorDiv
                        errorDiv.classList.remove('hidden');
                        let errorHtml = '<ul class="space-y-1">';
                        Object.values(data.errors).forEach(errs => {
                            errs.forEach(err => {
                                errorHtml += `<li>${err}</li>`;
                            });
                        });
                        errorHtml += '</ul>';
                        errorDiv.innerHTML = errorHtml;
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    lucide.createIcons();
                } else {
                    throw new Error(data.message || 'Erro inesperado no servidor.');
                }
            } catch (error) {
                console.error('Register Error:', error);
                errorDiv.classList.remove('hidden');
                errorDiv.textContent = error.message || 'Falha na conexão com o servidor de protocolos.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                lucide.createIcons();
            }
        };
    });
</script>

<style>
    body { 
        background-color: #080a0f;
        background-image:
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-shake { animation: shake 0.4s ease-in-out 2; }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
</style>
@endsection
