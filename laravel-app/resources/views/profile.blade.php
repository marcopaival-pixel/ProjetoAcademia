@extends('layouts.app', ['navCurrent' => 'profile'])

@section('title', 'Meu Perfil — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Profile Header Section -->
    @if($isPurePatient)
        <!-- HEADER INTEGRADO PARA PACIENTE -->
        <div class="flex flex-col md:flex-row items-center gap-10 bg-zinc-900 border border-zinc-800 p-12 rounded-[4rem] shadow-3xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl"></div>
            
            <div class="relative">
                <div class="w-40 h-40 rounded-[3rem] overflow-hidden border-4 border-zinc-950 shadow-2xl">
                    <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=10b981&background=09090b&bold=true' }}" class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-zinc-950 p-3 rounded-2xl shadow-xl border-4 border-zinc-900">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="flex-1 text-center md:text-left space-y-4">
                <div class="space-y-1">
                    <h1 class="text-5xl font-black text-white tracking-tighter uppercase italic leading-none">{{ auth()->user()->name }}</h1>
                    <p class="text-zinc-500 font-bold uppercase tracking-[0.3em] text-xs">Identidade Clínica Sincronizada</p>
                </div>
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <span class="px-5 py-2 bg-zinc-950 border border-zinc-800 rounded-2xl text-[10px] font-black text-zinc-500 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="mail" class="w-3 h-3"></i> {{ auth()->user()->email }}
                    </span>
                    <span class="px-5 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-[10px] font-black text-emerald-500 uppercase tracking-widest">Paciente Ativo</span>
                </div>
            </div>

            <div class="bg-zinc-950 border border-zinc-800 p-10 rounded-[3rem] text-center shadow-inner group">
                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] mb-3 group-hover:text-emerald-500 transition-colors">Estimativa de Bio-Idade</p>
                <p class="text-6xl font-black text-white tabular-nums tracking-tighter italic leading-none">
                    {{ $bioAge }} <span class="text-sm text-zinc-700 uppercase not-italic">Anos</span>
                </p>
            </div>
        </div>
    @else
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-zinc-900">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-inner">Configurações de Identidade</span>
                    <span class="text-zinc-700">•</span>
                    <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter">Balanço Biométrico & Segurança</span>
                </div>
                <h1 class="text-5xl font-black tracking-tight text-white leading-tight uppercase">Meu <span class="text-emerald-500">Perfil</span></h1>
                <p class="text-zinc-500 font-medium max-w-xl">Gerencie seus dados físicos, metas e configurações de saúde em um ecossistema sincronizado.</p>
            </div>

            <div class="flex items-center gap-6 bg-zinc-900/50 border border-zinc-800 p-6 rounded-[2.5rem] shadow-2xl backdrop-blur-md">
                <div class="text-center px-4">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-1">Meta Calórica</p>
                    <div class="flex items-baseline justify-center gap-1">
                        <span class="text-3xl font-black text-emerald-500 tabular-nums">{{ $u->daily_calorie_target ?? '—' }}</span>
                        @if($u->daily_calorie_target) <span class="text-[10px] text-zinc-600 font-bold uppercase">kcal</span> @endif
                    </div>
                </div>
                <div class="w-[1px] h-10 bg-zinc-800"></div>
                <div class="text-center px-4">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-1">Bio-Idade</p>
                    <div class="flex items-baseline justify-center gap-1">
                        <span class="text-3xl font-black text-white tabular-nums">{{ $bioAge ?? '—' }}</span>
                        @if($bioAge) <span class="text-[10px] text-zinc-600 font-bold uppercase">anos</span> @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alert System -->
    @if (!empty($notice))
        <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-[2rem] text-emerald-400 text-xs font-black animate-fade-in flex items-center gap-4 shadow-xl">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-zinc-950 flex items-center justify-center shadow-lg">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            {{ $notice }}
        </div>
    @endif

    @if (!empty($error))
        <div class="p-6 bg-rose-500/10 border border-rose-500/20 rounded-[2rem] text-rose-400 text-xs font-black animate-fade-in flex items-center gap-4 shadow-xl">
            <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-lg">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            {{ $error }}
        </div>
    @endif

    <!-- Calorie Preview Box (Only for Athletes) -->
    @if ($calPreview !== null && !$isPurePatient)
        <div class="bg-zinc-900 border border-emerald-500/30 rounded-[3rem] p-10 shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-1000"></div>
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i data-lucide="calculator" class="w-6 h-6"></i>
                </div>
                <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Prévia da Estimativa Inteligente</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-zinc-950 p-6 rounded-2xl border border-zinc-800 shadow-inner">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-2">TMB (Metabolismo Basal)</p>
                    <p class="text-2xl font-black text-white tabular-nums">{{ (int) round($calPreview['bmr']) }} <span class="text-xs text-zinc-700 uppercase">kcal/dia</span></p>
                </div>
                <div class="bg-zinc-950 p-6 rounded-2xl border border-zinc-800 shadow-inner">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-2">Gasto Total (TDEE)</p>
                    <p class="text-2xl font-black text-white tabular-nums">{{ (int) round($calPreview['tdee']) }} <span class="text-xs text-zinc-700 uppercase">kcal/dia</span></p>
                </div>
                <div class="bg-emerald-500/5 p-6 rounded-2xl border border-emerald-500/20 shadow-inner">
                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest mb-2">Meta Sugerida Elite</p>
                    <p class="text-2xl font-black text-emerald-500 tabular-nums">{{ $calPreview['target'] }} <span class="text-xs text-emerald-700 uppercase">kcal/dia</span></p>
                </div>
            </div>
            <p class="mt-6 text-[10px] text-zinc-600 font-bold uppercase tracking-widest italic">
                * Valores processados para peso de {{ number_format($latestWeight, 1, ',', '.') }} kg em {{ \Carbon\Carbon::parse($calPreview['weighed_at'])->translatedFormat('d/m/Y') }}.
            </p>
        </div>
    @endif

    @if($isPurePatient)
        <!-- SEÇÃO UNIFICADA: REGISTRO CLÍNICO (PACIENTE) -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-[4rem] p-1 shadow-2xl relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Coluna 1: Dados Identitários -->
                <div class="p-12 space-y-10 border-r border-zinc-800/50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                            <i data-lucide="fingerprint" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-tighter italic">Dados Identitários</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-1.5 px-6 py-4 bg-zinc-950/50 rounded-[1.5rem] border border-zinc-900">
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Nome Completo registrado</p>
                            <p class="text-base font-black text-white uppercase tracking-widest">{{ $u->name }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5 px-6 py-4 bg-zinc-950/50 rounded-[1.5rem] border border-zinc-900">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Nascimento</p>
                                <p class="text-base font-black text-white uppercase tracking-widest">{{ \Carbon\Carbon::parse($u->birth_date)->translatedFormat('d/m/Y') }}</p>
                            </div>
                            <div class="space-y-1.5 px-6 py-4 bg-zinc-950/50 rounded-[1.5rem] border border-zinc-900">
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Gênero Bio</p>
                                <p class="text-base font-black text-white uppercase tracking-widest">{{ $u->sex === 'M' ? 'Masculino' : 'Feminino' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna 2: Biometria e Selo Audit -->
                <div class="p-12 space-y-10 bg-zinc-950/20">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-blue-500">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white uppercase tracking-tighter italic">Métricas Físicas</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-8 bg-zinc-950 border border-zinc-900 rounded-[3rem] shadow-inner">
                            <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-1">Peso Atual</p>
                            <p class="text-4xl font-black text-emerald-500 tabular-nums italic">{{ number_format($latestWeight, 1, ',', '.') }}<span class="text-sm ml-1 not-italic text-zinc-700">kg</span></p>
                        </div>
                        <div class="text-center p-8 bg-zinc-950 border border-zinc-900 rounded-[3rem] shadow-inner">
                            <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest mb-1">Altura</p>
                            <p class="text-4xl font-black text-white tabular-nums italic">{{ $u->height_cm }}<span class="text-sm ml-1 not-italic text-zinc-700">cm</span></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 p-6 bg-blue-500/5 border border-blue-500/10 rounded-[2rem]">
                        <div class="w-14 h-14 bg-blue-500/10 text-blue-500 rounded-2xl flex items-center justify-center shadow-inner shrink-0">
                            <i data-lucide="shield-check" class="w-7 h-7"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-black text-white uppercase tracking-tighter italic">Dados Biométricos Auditados</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest leading-relaxed">
                                Estas informações são controladas exclusivamente pelo seu profissional de saúde e refletem seu prontuário oficial.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEÇÃO UNIFICADA: GESTÃO E DIREITOS (PACIENTE) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Segurança -->
            <div class="bg-zinc-900 border border-zinc-800 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Segurança & Acesso</h3>
                </div>
                
                <form method="post" action="{{ route('profile') }}" novalidate autocomplete="off" class="space-y-6">
                    @csrf
                    <input type="hidden" name="profile_action" value="password">
                    <div class="space-y-2">
                        <label for="current_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Senha Atual</label>
                        <input id="current_password" name="current_password" type="password" required class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm outline-none focus:border-emerald-500 transition-all shadow-inner">
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="new_password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nova Senha</label>
                            <input id="new_password" name="new_password" type="password" required minlength="8" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm outline-none focus:border-emerald-500 transition-all shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label for="new_password_confirm" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Confirmar</label>
                            <input id="new_password_confirm" name="new_password_confirm" type="password" required minlength="8" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm outline-none focus:border-emerald-500 transition-all shadow-inner">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-5 bg-zinc-950 border border-zinc-800 text-zinc-500 hover:text-white hover:bg-zinc-800 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all shadow-inner">ATUALIZAR ACESSO</button>
                </form>
            </div>

            <!-- Direitos Digitais (LGPD) -->
            <div class="bg-zinc-900 border border-zinc-800 p-12 rounded-[4rem] shadow-2xl relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-blue-500">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Direitos Digitais</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('patient.access-logs') }}" class="flex items-center justify-between p-6 bg-zinc-950 border border-zinc-800 rounded-3xl group hover:border-blue-500/20 transition-all">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-white uppercase tracking-widest group-hover:text-blue-500 transition-colors">Histórico de Acessos</p>
                                <p class="text-[9px] text-zinc-600 font-bold uppercase">Auditoria LGPD e visualizações</p>
                            </div>
                            <i data-lucide="list-tree" class="w-5 h-5 text-zinc-700 group-hover:text-blue-500 transition-colors"></i>
                        </a>

                        <a href="{{ route('privacy.download') }}" class="flex items-center justify-between p-6 bg-zinc-950 border border-zinc-800 rounded-3xl group hover:border-emerald-500/20 transition-all">
                            <div class="space-y-1">
                                <p class="text-xs font-black text-white uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Portabilidade de Dados</p>
                                <p class="text-[9px] text-zinc-600 font-bold uppercase">Exportar registros em JSON</p>
                            </div>
                            <i data-lucide="download" class="w-5 h-5 text-zinc-700 group-hover:text-emerald-500 transition-colors"></i>
                        </a>

                        <div x-data="{ openPurgeModal: false }" class="relative">
                            <button @click="openPurgeModal = true" class="w-full flex items-center justify-between p-6 bg-rose-500/5 border border-rose-500/10 rounded-3xl group hover:border-rose-500/30 transition-all text-left">
                                <div class="space-y-1">
                                    <p class="text-xs font-black text-rose-500 uppercase tracking-widest">Esquecimento Total</p>
                                    <p class="text-[9px] text-zinc-600 font-bold uppercase">Purga irreversível de dados</p>
                                </div>
                                <i data-lucide="trash-2" class="w-5 h-5 text-rose-500/50 group-hover:text-rose-500 transition-colors"></i>
                            </button>

                            <!-- PURGE MODAL REUTILIZADO -->
                            <div x-show="openPurgeModal" class="fixed inset-0 z-[1000] flex items-center justify-center p-6 bg-zinc-950/95 backdrop-blur-xl" x-cloak>
                                <div class="bg-zinc-900 border border-rose-500/30 w-full max-w-lg rounded-[3.5rem] p-12 shadow-3xl text-center space-y-8" @click.away="openPurgeModal = false">
                                    <div class="w-20 h-20 bg-rose-500 text-white rounded-3xl flex items-center justify-center mx-auto shadow-2xl rotate-12">
                                        <i data-lucide="alert-octagon" class="w-10 h-10"></i>
                                    </div>
                                    <div class="space-y-4">
                                        <h3 class="text-3xl font-black text-white tracking-tighter uppercase italic">Confirmar Exclusão</h3>
                                        <div class="bg-zinc-950/50 border border-zinc-800 rounded-2xl p-6 text-left space-y-2">
                                            <p class="text-[9px] text-zinc-600 font-black flex items-center gap-2 uppercase tracking-widest"><i data-lucide="check" class="w-3 h-3 text-rose-500"></i> Laudos e Prontuários</p>
                                            <p class="text-[9px] text-zinc-600 font-black flex items-center gap-2 uppercase tracking-widest"><i data-lucide="check" class="w-3 h-3 text-rose-500"></i> Registros de Biometria</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('privacy.request-deletion') }}" method="POST" class="grid grid-cols-1 gap-4 pt-4">
                                        @csrf
                                        <button type="submit" class="w-full py-6 bg-rose-500 text-white font-black rounded-3xl hover:bg-rose-600 transition-all text-xs uppercase tracking-[0.2em]">CONFIRMAR PURGA</button>
                                        <button type="button" @click="openPurgeModal = false" class="w-full py-5 text-zinc-500 font-black rounded-3xl hover:text-white transition-all text-[10px] uppercase tracking-widest">CANCELAR</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-zinc-800/50 text-center">
                    <p class="text-[8px] text-zinc-700 font-black uppercase tracking-[0.4em]">NexShape Compliance LGPD</p>
                </div>
            </div>
        </div>
    @else
        <!-- ESTRUTURA PARA ATLETA/ADMIN -->
        <form method="post" action="{{ route('profile') }}" novalidate class="space-y-10">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                
                <!-- CARD: DADOS PESSOAIS -->
                <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Dados Pessoais</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="name" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nome Completo</label>
                            <input id="name" name="name" type="text" required maxlength="120" value="{{ old('name', $u->name) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner uppercase tracking-widest">
                            <p class="text-[9px] text-zinc-700 font-bold uppercase tracking-widest ml-2">ID Sincronizado: {{ $u->email }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="birth_date" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nascimento</label>
                                <input id="birth_date" name="birth_date" type="date" required value="{{ old('birth_date', $u->birth_date) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner uppercase">
                            </div>
                            <div class="space-y-2">
                                <label for="sex" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Bio-Gênero</label>
                                <select id="sex" name="sex" required class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xs font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none cursor-pointer uppercase tracking-widest">
                                    <option value="" @selected(old('sex', $u->sex) === '')>Selecione...</option>
                                    <option value="M" @selected(old('sex', $u->sex) === 'M')>Masculino</option>
                                    <option value="F" @selected(old('sex', $u->sex) === 'F')>Feminino</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="height_cm" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Altura (cm)</label>
                            <div class="relative">
                                <input id="height_cm" name="height_cm" type="number" min="50" max="260" placeholder="ex.: 175" value="{{ old('height_cm', $u->height_cm) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner tabular-nums">
                                <span class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-700 font-black text-[10px] tracking-widest">CM</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: COMPOSIÇÃO E ATIVIDADE -->
                <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Composição & Atividade</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="current_weight_kg" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Peso Atual (kg)</label>
                                <input id="current_weight_kg" name="current_weight_kg" type="number" step="0.1" min="20" max="500" placeholder="0.0" value="{{ old('current_weight_kg', $latestWeight) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-emerald-500 text-xl font-black outline-none focus:border-emerald-500 transition-all shadow-inner tabular-nums">
                            </div>
                            <div class="space-y-2">
                                <label for="target_weight_kg" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Peso Objetivo (kg)</label>
                                <input id="target_weight_kg" name="target_weight_kg" type="number" step="0.1" min="20" max="500" placeholder="0.0" value="{{ old('target_weight_kg', $u->target_weight_kg) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xl font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner tabular-nums">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="activity_level" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nível de Atividade</label>
                            <select id="activity_level" name="activity_level" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xs font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none cursor-pointer uppercase tracking-widest">
                                <option value="sedentary" @selected(old('activity_level', $u->activity_level) === 'sedentary')>🚶 Sedentário (Trabalho de Escritório)</option>
                                <option value="light" @selected(old('activity_level', $u->activity_level) === 'light')>🏃 Leve (1-2x semana)</option>
                                <option value="moderate" @selected(old('activity_level', $u->activity_level) === 'moderate')>🚴 Moderado (3-5x semana)</option>
                                <option value="active" @selected(old('activity_level', $u->activity_level) === 'active')>🏋️ Ativo (6-7x semana)</option>
                                <option value="very_active" @selected(old('activity_level', $u->activity_level) === 'very_active')>🏅 Atleta (Treino Intenso 2x dia)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CARD: METAS DE SAÚDE -->
                <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                            <i data-lucide="target" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Metas Estratégicas</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="goal" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Objetivo Primário</label>
                            <select id="goal" name="goal" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xs font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none cursor-pointer uppercase tracking-widest">
                                <option value="lose" @selected(old('goal', $u->goal) === 'lose')>🔥 Perder Peso / Emagrecer</option>
                                <option value="maintain" @selected(old('goal', $u->goal) === 'maintain')>⚖️ Saúde e Bem-Estar</option>
                                <option value="gain" @selected(old('goal', $u->goal) === 'gain')>💪 Ganhar Massa Muscular</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="daily_calorie_target" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Calorias (kcal)</label>
                                <input id="daily_calorie_target" name="daily_calorie_target" type="number" min="500" max="20000" placeholder="ex.: 2200" value="{{ old('daily_calorie_target', $u->daily_calorie_target) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xl font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner tabular-nums">
                            </div>
                            <div class="space-y-2">
                                <label for="water_target_ml" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Água (ml)</label>
                                <input id="water_target_ml" name="water_target_ml" type="number" min="500" max="10000" step="100" placeholder="2500" value="{{ old('water_target_ml', $u->water_target_ml) }}" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xl font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner tabular-nums">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="climate" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Clima Predominante</label>
                            <select id="climate" name="climate" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-xs font-black outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none cursor-pointer uppercase tracking-widest">
                                <option value="cold" @selected(old('climate', $u->climate) === 'cold')>❄️ Clima Frio</option>
                                <option value="moderate" @selected(old('climate', $u->climate ?? 'moderate') === 'moderate')>☁️ Clima Agradável</option>
                                <option value="hot" @selected(old('climate', $u->climate) === 'hot')>☀️ Clima Quente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CARD: AUTOMAÇÃO INTELIGENTE -->
                <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-10">
                            <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Automação Elite</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <label class="group flex items-center gap-5 p-5 bg-zinc-950 border border-zinc-800 rounded-3xl cursor-pointer hover:border-emerald-500/30 transition-all shadow-inner">
                                <input type="checkbox" name="auto_calorie" value="1" class="w-6 h-6 bg-zinc-900 border-zinc-800 text-emerald-500 rounded-lg focus:ring-emerald-500/50 accent-emerald-500" @checked(old('auto_calorie'))>
                                <div class="space-y-1">
                                    <p class="text-xs font-black text-white uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Cálculo Calórico Automático</p>
                                    <p class="text-[9px] text-zinc-600 font-bold uppercase">Sincroniza com TMB e nível de atividade.</p>
                                </div>
                            </label>
                            
                            <label class="group flex items-center gap-5 p-5 bg-zinc-950 border border-zinc-800 rounded-3xl cursor-pointer hover:border-emerald-500/30 transition-all shadow-inner">
                                <input type="checkbox" name="auto_water" value="1" class="w-6 h-6 bg-zinc-900 border-zinc-800 text-emerald-500 rounded-lg focus:ring-emerald-500/50 accent-emerald-500" @checked(old('auto_water', $u->is_water_target_auto))>
                                <div class="space-y-1">
                                    <p class="text-xs font-black text-white uppercase tracking-widest group-hover:text-emerald-500 transition-colors">Cálculo Hídrico Automático</p>
                                    <p class="text-[9px] text-zinc-600 font-bold uppercase">Ajusta com base no clima e peso biológico.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-10">
                        <button type="submit" class="w-full py-6 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-[11px] uppercase tracking-[0.3em] rounded-3xl transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98]">
                            SALVAR ALTERAÇÕES
                        </button>
                    </div>
                </div>

            </div>
        </form>

        <!-- Achievements -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] p-10 shadow-2xl relative overflow-hidden mt-10">
            <div class="flex items-center gap-4 mb-10">
                <div class="w-12 h-12 bg-amber-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Galeria de Conquistas</h3>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 text-center">
                @foreach(\App\Services\AchievementService::getList(auth()->id()) as $badge)
                    <div class="group relative p-4 bg-zinc-900/40 border {{ $badge->unlocked ? 'border-zinc-800' : 'border-zinc-800/30' }} rounded-[2.5rem] transition-all duration-500 {{ $badge->unlocked ? 'hover:border-white/20 hover:bg-zinc-900 shadow-2xl' : 'opacity-30' }}">
                        @if($badge->unlocked)
                            <div class="absolute -inset-px bg-gradient-to-br from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-[2.5rem]"></div>
                        @endif
                        
                        <div class="relative z-10 flex flex-col items-center">
                            <!-- Icon Container -->
                            <div class="w-14 h-14 mb-4 rounded-2xl flex items-center justify-center text-xl transition-all duration-500 {{ $badge->unlocked ? $badge->bg . ' ' . $badge->color . ' shadow-lg transform group-hover:-rotate-12 group-hover:scale-110' : 'bg-zinc-800/50 text-zinc-700' }}">
                                <i class="{{ $badge->icon }} notranslate"></i>
                            </div>
                            
                            <!-- Status & Title -->
                            <div class="space-y-1">
                                <p class="text-[7px] font-black uppercase tracking-[0.3em] {{ $badge->unlocked ? 'text-emerald-500' : 'text-zinc-700' }}">
                                    {{ $badge->unlocked ? 'Desbloqueado' : 'Bloqueado' }}
                                </p>
                                <p class="text-[9px] font-black {{ $badge->unlocked ? 'text-white' : 'text-zinc-600' }} uppercase tracking-tighter italic leading-tight">
                                    {{ $badge->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Segurança & LGPD para Atleta -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-10">
            <!-- Segurança -->
            <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-emerald-500">
                        <i data-lucide="shield-lock" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Segurança & Acesso</h3>
                </div>
                <form method="post" action="{{ route('profile') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="profile_action" value="password">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Senha Atual</label>
                        <input name="current_password" type="password" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm outline-none focus:border-emerald-500 transition-all shadow-inner">
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nova Senha</label>
                            <input name="new_password" type="password" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm outline-none focus:border-emerald-500 transition-all shadow-inner">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Confirmar</label>
                            <input name="new_password_confirm" type="password" class="w-full bg-zinc-950 border border-zinc-800 p-5 rounded-2xl text-white text-sm outline-none focus:border-emerald-500 transition-all shadow-inner">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-5 bg-zinc-950 border border-zinc-800 text-zinc-500 hover:text-white hover:bg-zinc-800 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all shadow-inner">ROTACIONAR CREDENCIAIS</button>
                </form>
            </div>

            <!-- LGPD -->
            <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center text-blue-500">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tighter italic">Privacidade (LGPD)</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('privacy.download') }}" class="p-6 bg-zinc-950 border border-zinc-800 rounded-3xl text-center group hover:border-emerald-500/20 transition-all">
                        <i data-lucide="download" class="w-6 h-6 mx-auto mb-4 text-zinc-700 group-hover:text-emerald-500 transition-colors"></i>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Portabilidade</p>
                    </a>
                    <button class="p-6 bg-zinc-950 border border-zinc-800 rounded-3xl text-center group hover:border-rose-500/20 transition-all">
                        <i data-lucide="trash-2" class="w-6 h-6 mx-auto mb-4 text-zinc-700 group-hover:text-rose-500 transition-colors"></i>
                        <p class="text-[10px] font-black text-white uppercase tracking-widest">Esquecimento</p>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush

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

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
        opacity: 0.5;
    }
    input[type="date"]::-webkit-calendar-picker-indicator:hover {
        opacity: 1;
    }
</style>
@endsection
