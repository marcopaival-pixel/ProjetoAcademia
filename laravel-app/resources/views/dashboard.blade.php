@extends('layouts.app')

@section('title', 'Dashboard — NexShape')

@section('content')
    <div class="py-10 space-y-12 animate-fade-in-up mx-auto px-4 md:px-6">
        <!-- App Promotion Banner -->
        <x-marketing.promo-banner />

        <!-- Header Strategy: Glassmorphic Floating Header -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-zinc-900">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span
                        class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">Jornada de Aprendizado Ativa</span>
                    <span class="text-zinc-700">•</span>
                    <span class="text-zinc-500 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
                </div>
                <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                    Força, <span
                        class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-emerald-600">{{ explode(' ', Auth::user()->name)[0] }}</span>!
                </h1>
            </div>

            @if($showChecklist)
            <!-- Onboarding Progress Bar -->
            <div class="hidden lg:flex flex-col items-end gap-2 max-w-xs w-full">
                <div class="flex items-center justify-between w-full text-[10px] font-black uppercase tracking-widest">
                    <span class="text-zinc-500 italic">CONFIGURAÇÃO INICIAL</span>
                    <span class="text-emerald-400">{{ round($setupPercentage) }}%</span>
                </div>
                <div class="h-2 w-full bg-zinc-900 rounded-full border border-white/5 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-600 to-emerald-400 transition-all duration-1000" style="width: {{ $setupPercentage }}%"></div>
                </div>
            </div>
            @elseif(Auth::user()->profile_completion_percentage < 100)
            <div class="hidden lg:flex flex-col items-end gap-2 max-w-xs w-full">
                <div class="flex items-center justify-between w-full text-[10px] font-black uppercase tracking-widest">
                    <span class="text-zinc-500 italic">PERFIL BIOMÉTRICO</span>
                    <span class="text-emerald-400">{{ Auth::user()->profile_completion_percentage }}%</span>
                </div>
                <div class="h-2 w-full bg-zinc-900 rounded-full border border-white/5 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-600 to-teal-400 transition-all duration-1000" style="width: {{ Auth::user()->profile_completion_percentage }}%"></div>
                </div>
            </div>
            @endif

            <div class="flex flex-wrap items-center gap-6">
                <div class="hidden xl:flex items-center gap-4 p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl max-w-sm">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-zinc-950 shrink-0 shadow-lg shadow-emerald-500/20">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none mb-1">Dica de Aprendizado</p>
                        <p class="text-white text-[10px] font-medium leading-tight">Consistência é mais importante que intensidade no início da sua jornada NexShape.</p>
                    </div>
                </div>

                <div class="flex gap-2 p-1.5 bg-zinc-900/50 backdrop-blur-xl rounded-2xl border border-white/5 shadow-2xl">
                    <a href="{{ route('diary') }}"
                        class="group relative px-6 py-3 bg-emerald-600 text-zinc-950 font-black rounded-xl overflow-hidden transition-all hover:pr-10 active:scale-95 shadow-lg shadow-emerald-500/20">
                        <span class="relative z-10 uppercase text-xs tracking-widest">Refeição</span>
                        <i data-lucide="plus" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-0 group-hover:opacity-100 transition-all"></i>
                    </a>
                    <a href="{{ route('exercise') }}"
                        class="px-6 py-3 text-zinc-400 hover:text-white font-bold rounded-xl transition-all uppercase text-xs tracking-widest">Treino</a>
                </div>

                <div class="relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000">
                    </div>
                    <div
                        class="relative flex items-center gap-3 bg-zinc-900 px-5 py-2.5 rounded-2xl border border-white/10">
                        <span class="text-2xl animate-bounce-slow">🔥</span>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-tighter">Sequência Atual</p>
                            <p class="text-white font-black text-lg leading-none">12 Dias</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Readiness Alert Banner -->
        @if(Auth::user()->isOnboardingPending())
        <div class="bg-gradient-to-r from-amber-600 to-orange-700 p-6 rounded-[2.5rem] border border-amber-500/20 shadow-2xl animate-pulse-slow">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-xl rounded-[1.5rem] flex items-center justify-center border border-white/20 shadow-xl">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-300"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white italic tracking-tighter leading-none uppercase">Configuração Incompleta</h3>
                        <p class="text-sm font-medium text-amber-100 mt-2">Sua conta ainda não está totalmente pronta para operar. Conclua os itens abaixo para liberar todas as funcionalidades.</p>
                    </div>
                </div>
                <button x-data @click="$dispatch('open-onboarding')" class="px-8 py-4 bg-white text-zinc-900 font-black rounded-2xl hover:bg-zinc-900 hover:text-white transition-all text-xs shadow-2xl uppercase tracking-widest whitespace-nowrap">
                    CONCLUIR AGORA
                </button>
            </div>
        </div>
        @endif

        <!-- Evolution Status Traffic Light -->
        <x-evolution-status-traffic-light :status="$evolutionStatus" :isPremium="$isPremium" />

        <!-- Performance & Readiness Hub (ACWR) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 relative group bg-zinc-900 border {{ $performanceStatus['risk_level'] === 'danger' ? 'border-red-500/30' : ($performanceStatus['risk_level'] === 'high' ? 'border-amber-500/30' : 'border-emerald-500/20') }} p-8 rounded-[3rem] overflow-hidden shadow-2xl transition-all">
                <div class="absolute inset-0 bg-gradient-to-br {{ $performanceStatus['risk_level'] === 'danger' ? 'from-red-500/5' : ($performanceStatus['risk_level'] === 'high' ? 'from-amber-500/5' : 'from-emerald-500/5') }} to-transparent"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <!-- Gauge ACWR -->
                    <div class="relative w-48 h-48 flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-800" />
                            <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" 
                                    stroke-dasharray="282.7" 
                                    stroke-dashoffset="{{ 282.7 - (282.7 * min($performanceStatus['acwr'] / 2.0, 1)) }}" 
                                    stroke-linecap="round"
                                    class="{{ $performanceStatus['risk_level'] === 'danger' ? 'text-red-500' : ($performanceStatus['risk_level'] === 'high' ? 'text-amber-500' : 'text-emerald-500') }} transition-all duration-1000 shadow-[0_0_15px_rgba(0,0,0,0.5)]" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-4xl font-black text-white tracking-tighter">{{ $performanceStatus['acwr'] }}</span>
                            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Nível de Carga</span>
                        </div>
                    </div>

                    <div class="flex-grow space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full {{ $performanceStatus['risk_level'] === 'danger' ? 'bg-red-500/20 text-red-400 border-red-500/20' : ($performanceStatus['risk_level'] === 'high' ? 'bg-amber-500/20 text-amber-400 border-amber-500/20' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20') }} text-[10px] font-black uppercase tracking-widest border">
                                Status: {{ strtoupper($performanceStatus['risk_level']) }}
                            </span>
                            <span class="text-zinc-500">•</span>
                            <span class="text-zinc-400 text-xs font-bold uppercase tracking-tighter">Análise Bio-Rítmica Ativa</span>
                        </div>
                        <h2 class="text-3xl font-black text-white italic tracking-tighter leading-none uppercase">Prontidão para Treino</h2>
                        <p class="text-zinc-400 text-sm font-medium leading-relaxed max-w-xl">
                            {{ $performanceStatus['recommendation'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="relative group bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl flex flex-col justify-center">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-emerald-400">
                                <i data-lucide="zap" class="w-4 h-4"></i>
                            </div>
                            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Nível de Recuperação</span>
                        </div>
                        <span class="text-2xl font-black text-white tracking-tighter">{{ $performanceStatus['recovery_score'] }}%</span>
                    </div>
                    <div class="h-2 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                        <div class="h-full bg-gradient-to-r from-red-500 via-amber-500 to-emerald-500 transition-all duration-1000" style="width: {{ $performanceStatus['recovery_score'] }}%"></div>
                    </div>
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-tighter italic">Baseado em HRV, Sono e Histórico de Carga Recente.</p>
                </div>
            </div>
        </div>

        <!-- AI Credit Management Hub -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 bg-zinc-900 border {{ $isPremium ? 'border-emerald-500/20 shadow-emerald-500/5' : 'border-amber-500/20 shadow-amber-500/5' }} p-8 rounded-[3rem] relative overflow-hidden shadow-2xl group">
                <div class="absolute -top-24 -right-24 w-64 h-64 {{ $isPremium ? 'bg-emerald-500/5' : 'bg-amber-500/5' }} blur-[100px] rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="flex-shrink-0 flex flex-col items-center gap-4">
                        <div class="relative w-32 h-32 flex items-center justify-center">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="6" fill="transparent" class="text-zinc-800" />
                                <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" 
                                        stroke-dasharray="282.7" 
                                        stroke-dashoffset="{{ 282.7 - (282.7 * min($aiCreditWallet->balance / (max($aiCreditWallet->monthly_allowance, 1)), 1)) }}" 
                                        stroke-linecap="round"
                                        class="{{ $isPremium ? 'text-emerald-500' : 'text-amber-500' }} transition-all duration-1000" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-3xl font-black text-white tabular-nums">{{ $aiCreditWallet->balance }}</span>
                                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Créditos IA</span>
                            </div>
                        </div>
                        <div class="px-4 py-1 rounded-full {{ $isPremium ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 'bg-zinc-800 text-zinc-400 border-white/5' }} border text-[10px] font-black uppercase tracking-widest">
                            Plano: {{ Auth::user()->activePlan?->plan?->name ?? (Auth::user()->hasPremiumAccess() ? 'Premium' : 'Free') }}
                        </div>
                    </div>

                    <div class="flex-grow grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Incluso Mês</p>
                            <p class="text-xl font-black text-white">{{ $aiCreditWallet->monthly_allowance }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Créditos Avulsos</p>
                            <p class="text-xl font-black text-emerald-400">{{ $aiCreditWallet->extra_credits }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Consumo no Ciclo</p>
                            <p class="text-xl font-black text-white">{{ Auth::user()->getAiCreditsUsedThisMonth() }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-1">Próxima Renovação</p>
                            <p class="text-sm font-black text-zinc-400 uppercase tracking-tighter">{{ $aiCreditWallet->renewal_date?->format('d/m/Y') ?: '--' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl flex flex-col justify-center items-center text-center group hover:border-emerald-500/40 transition-all">
                <i data-lucide="shopping-cart" class="w-8 h-8 text-emerald-500 mb-4 group-hover:scale-110 transition-transform"></i>
                <h4 class="text-xs font-black text-white uppercase tracking-widest mb-2">Precisa de mais?</h4>
                <p class="text-[10px] text-zinc-500 font-medium mb-6 uppercase tracking-tighter">Adquira pacotes extras de IA instantaneamente.</p>
                <a href="{{ route('ai-credits.index') }}" class="w-full py-3 bg-emerald-500 text-zinc-950 font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/10">
                    Comprar Créditos
                </a>
            </div>
        </div>

        @if($systemAccessLinks->isNotEmpty())
        <!-- My Systems - Direct Access Links -->
        <div class="space-y-8">
            <div class="flex items-center gap-3">
                <div class="h-[1px] flex-grow bg-zinc-900"></div>
                <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] italic">Meus Sistemas</h2>
                <div class="h-[1px] flex-grow bg-zinc-900"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($systemAccessLinks as $link)
                <div class="group relative bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] overflow-hidden shadow-2xl transition-all hover:border-blue-500/30">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-500/5 blur-[50px] rounded-full group-hover:scale-150 transition-transform duration-1000"></div>
                    
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 border border-blue-500/20 group-hover:bg-blue-500 group-hover:text-zinc-950 transition-all">
                            <i data-lucide="external-link" class="w-8 h-8"></i>
                        </div>
                        
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter mb-2">{{ $link->system_name }}</h3>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-6">Acesso Direto ao Sistema</p>
                        
                        <div class="w-full space-y-3">
                            <a href="{{ $link->system_url }}" target="_blank" class="flex items-center justify-center w-full py-4 bg-blue-600 text-white font-black rounded-2xl text-[10px] uppercase tracking-widest hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/10 no-underline">
                                Acessar Sistema
                            </a>
                            <button onclick="copyToClipboard('{{ $link->system_url }}')" class="flex items-center justify-center w-full py-4 bg-zinc-950 border border-zinc-800 text-zinc-400 font-black rounded-2xl text-[10px] uppercase tracking-widest hover:text-white transition-all">
                                Copiar Link
                            </button>
                        </div>
                        
                        @if($link->qr_code_path)
                        <div class="mt-6 pt-6 border-t border-zinc-800 w-full flex flex-col items-center">
                            <img src="{{ asset('storage/' . $link->qr_code_path) }}" alt="QR Code" class="w-20 h-20 opacity-50 group-hover:opacity-100 transition-opacity mb-2">
                            <span class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">Acesso pelo Celular</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Link copiado para a área de transferência!');
                });
            }
        </script>
        @endif

        <!-- Layout Bento Moderno -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            <!-- Bloco de Calorias Master (Full Width & Centralizado) -->
            <div class="lg:col-span-12 space-y-12">
                <div
                    class="group relative bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] overflow-hidden shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] transition-all hover:border-emerald-500/20">
                    <!-- Glossy Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                    <div
                        class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/10 blur-[150px] rounded-full group-hover:scale-125 transition-transform duration-1000">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center relative z-10">
                        <!-- Progress Hub -->
                        <div class="flex flex-col items-center justify-center w-full">
                            <div class="relative w-full max-w-[450px] aspect-square flex items-center justify-center">
                                <!-- Inner Glow -->
                                <div class="absolute inset-10 rounded-full bg-emerald-500/5 blur-3xl"></div>

                                <svg class="w-full h-full -rotate-90" viewBox="0 0 450 450">
                                    <circle cx="225" cy="225" r="200" stroke="currentColor" stroke-width="4"
                                        fill="transparent" class="text-zinc-800/20" />
                                    <circle cx="225" cy="225" r="200" stroke="currentColor" stroke-width="12"
                                        fill="transparent" class="text-zinc-800/50" />
                                    <circle cx="225" cy="225" r="200" stroke="url(#cyber_gradient)" stroke-width="24"
                                        fill="transparent" stroke-dasharray="1256"
                                        stroke-dashoffset="{{ 1256 - (1256 * min($consumed / ($calorieTarget ?: 2000), 1)) }}"
                                        stroke-linecap="round"
                                        class="transition-all duration-1000 ease-in-out drop-shadow-[0_0_15px_rgba(16,185,129,0.3)]" />
                                    <defs>
                                        <linearGradient id="cyber_gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#10b981" />
                                            <stop offset="50%" stop-color="#34d399" />
                                            <stop offset="100%" stop-color="#059669" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                    <div class="relative">
                                        <span
                                            class="text-8xl font-black text-white tracking-tight tabular-nums">{{ number_format($remaining) }}</span>
                                        <span class="absolute -top-3 -right-8 text-emerald-400 font-bold text-lg leading-none">kcal</span>
                                    </div>
                                    <span
                                        class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] mt-5 bg-zinc-800/50 px-5 py-1.5 rounded-full">Disponíveis</span>
                                </div>
                            </div>
                        </div>

                        <!-- Macro Architecture -->
                        <div class="space-y-12">
                            <div class="grid grid-cols-1 gap-8">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between font-bold">
                                        <span class="text-zinc-400 text-[11px] uppercase tracking-widest">Consumo
                                            Diário</span>
                                        <span
                                            class="text-white text-lg tabular-nums">{{ number_format(($consumed / ($calorieTarget ?: 2000)) * 100, 0) }}%</span>
                                    </div>
                                    <div class="h-4 w-full bg-zinc-950 rounded-full p-1 border border-white/5 shadow-inner">
                                        <div class="h-full bg-gradient-to-r from-emerald-600 via-emerald-400 to-emerald-500 rounded-full transition-all duration-1000 relative"
                                            style="width: {{ min(($consumed / ($calorieTarget ?: 2000)) * 100, 100) }}%">
                                            <div class="absolute top-0 right-0 h-full w-4 bg-white/20 blur-sm rounded-full">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6">
                                @php
                                    $macros = [
                                        ['label' => 'Prot', 'val' => $sumProt, 'color' => 'rose', 'icon' => 'beef'],
                                        ['label' => 'Carb', 'val' => $sumCarb, 'color' => 'emerald', 'icon' => 'wheat'],
                                        ['label' => 'Gord', 'val' => $sumFat, 'color' => 'amber', 'icon' => 'droplet'],
                                    ];
                                @endphp
                                @foreach($macros as $m)
                                    <div class="relative group/macro cursor-help">
                                        <div
                                            class="absolute -inset-2 bg-{{ $m['color'] }}-500/0 group-hover/macro:bg-{{ $m['color'] }}-500/5 rounded-3xl transition-all duration-300">
                                        </div>
                                        <div class="text-center relative">
                                            <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mx-auto mb-3 text-{{ $m['color'] }}-400 group-hover/macro:border-{{ $m['color'] }}-500/50 transition-all shadow-lg">
                                                <i data-lucide="{{ $m['icon'] }}" class="w-5 h-5"></i>
                                            </div>
                                            <p class="text-[10px] text-zinc-500 font-black uppercase mb-1 tracking-tighter">
                                                {{ $m['label'] }}</p>
                                            <p class="text-white font-black text-xl tabular-nums">{{ number_format($m['val'], 0) }}g</p>
                                            <div class="mt-2 h-1 w-8 mx-auto bg-zinc-950 rounded-full overflow-hidden">
                                                @php
                                                    $targetKey = $m['label'] == 'Prot' ? 'p' : ($m['label'] == 'Carb' ? 'c' : 'f');
                                                    $percent = ($hasMacroTargets && ($macroTargets[$targetKey] ?? 0) > 0) 
                                                        ? min(($m['val'] / $macroTargets[$targetKey]) * 100, 100) 
                                                        : 0;
                                                @endphp
                                                <div class="h-full bg-{{ $m['color'] }}-500 shadow-[0_0_8px_rgba(var(--tw-color-{{ $m['color'] }}-500),0.5)]"
                                                    style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Performance Metrics Bar -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <div
                        class="relative group bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] transition-all hover:border-emerald-500/20 shadow-xl">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-xl border border-emerald-500/20 shadow-inner">
                                <i data-lucide="percent" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Gordura Corporal
                                    (Estimada)</p>
                                <h4 class="text-3xl font-black text-white tabular-nums">
                                    {{ $latestAssessment ? $latestAssessment->bf_percent . '%' : 'S/ Dados' }}</h4>
                            </div>
                        </div>
                        @if($latestAssessment)
                            <p class="mt-4 text-[10px] text-zinc-600 font-bold italic uppercase tracking-tighter">* Método da
                                Marinha via Circunferência</p>
                        @endif
                    </div>

                    <div
                        class="relative group bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] transition-all hover:border-emerald-500/20 shadow-xl">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-amber-500/10 text-amber-400 rounded-xl flex items-center justify-center text-xl border border-amber-500/20 shadow-inner">
                                <i data-lucide="weight" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Melhor 1RM
                                    Estimado</p>
                                <h4 class="text-3xl font-black text-white tabular-nums">
                                    {{ $topExercisePR ? round($topExercisePR->one_rm) . 'kg' : '---' }}
                                </h4>
                            </div>
                        </div>
                        @if($topExercisePR)
                            <p class="mt-4 text-[10px] text-zinc-600 font-bold uppercase tracking-tighter">
                                {{ $topExercisePR->exercise_name }} ({{ round($topExercisePR->weight_kg) }}kg x
                                {{ $topExercisePR->reps_done }})</p>
                        @endif
                    </div>

                    <!-- Widget de Predição IA -->
                    <a @if(!$isPremium) data-premium-locked @elseif($nextTraining) href="{{ route('progression.log', $nextTraining->id) }}" @else href="{{ route('progression.plans.index') }}" @endif
                        class="relative group bg-zinc-950 p-8 rounded-[2.5rem] border border-emerald-500/20 shadow-lg shadow-emerald-500/5 cursor-pointer overflow-hidden transition-all hover:border-emerald-500/40 no-underline block">
                        @if(!$isPremium)
                            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-[4px] z-20 flex flex-col items-center justify-center text-center p-6">
                                <i data-lucide="lock" class="w-6 h-6 text-emerald-500 mb-2"></i>
                                <p class="text-[9px] text-white font-black uppercase tracking-widest">Previsão IA Premium</p>
                            </div>
                        @endif
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-emerald-500 text-zinc-950 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <i data-lucide="zap" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Atalhar Resultados</p>
                                <h4 class="text-2xl font-black text-white uppercase tracking-tighter">Sugerir Cargas</h4>
                            </div>
                        </div>
                        
                        @if($isPremium && $neuralPrediction)
                            <div class="mt-6 p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl">
                                <p class="text-[10px] text-emerald-400 font-black uppercase tracking-tighter mb-1">{{ $topExercisePR->exercise_name }}</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-white tabular-nums">{{ $neuralPrediction['suggested_weight'] }} <small class="text-xs">kg</small></span>
                                    <span class="text-[10px] text-zinc-500 font-bold">({{ $neuralPrediction['confidence'] }}% Confiança)</span>
                                </div>
                                <p class="mt-3 text-[10px] text-zinc-400 font-medium leading-relaxed italic">"{{ $neuralPrediction['message'] }}"</p>
                            </div>
                        @else
                            <p class="mt-4 text-[10px] text-emerald-500 font-black uppercase tracking-widest animate-pulse">Otimização Neural Ativa</p>
                        @endif
                    </a>
                </div>

                @if($showChecklist)
                <!-- Checklist de Performance -->
                <div class="lg:col-span-12 space-y-8 animate-fade-in mt-6">
                    <div class="flex items-center gap-3">
                        <div class="h-[1px] flex-grow bg-zinc-900"></div>
                        <h2 class="text-xs font-black text-zinc-500 uppercase tracking-[0.4em] italic">Plano de Evolução</h2>
                        <div class="h-[1px] flex-grow bg-zinc-900"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        @foreach($setupChecklist as $key => $task)
                        <a href="{{ $task['route'] }}" 
                           @if($task['premium'] && !$isPremium) data-premium-locked @endif
                           class="group relative p-6 bg-zinc-900 border {{ $task['done'] ? 'border-emerald-500/20' : 'border-zinc-800' }} rounded-3xl transition-all hover:border-emerald-500/40 no-underline shadow-xl">
                            
                            @if($task['premium'] && !$isPremium)
                                <div class="absolute top-3 right-3 text-amber-500">
                                    <i data-lucide="crown" class="w-3 h-3"></i>
                                </div>
                            @endif

                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors shadow-inner {{ $task['done'] ? 'bg-emerald-500 text-zinc-950' : 'bg-zinc-950 text-zinc-700' }}">
                                    @if($task['done'])
                                        <i data-lucide="check" class="w-5 h-5"></i>
                                    @else
                                        <span class="font-black text-xs">{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                                <div class="flex-grow">
                                    <p class="text-xs font-black uppercase tracking-tight {{ $task['done'] ? 'text-white' : 'text-zinc-500' }} leading-tight">{{ $task['label'] }}</p>
                                    @if($task['premium'])
                                        <p class="text-[8px] text-emerald-500/60 font-black uppercase tracking-tighter mt-1 italic">Recurso PRO</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Dashboard Dynamic Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Pro Training -->
                    <div
                        class="group relative bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] overflow-hidden transition-all hover:border-emerald-500/20 shadow-2xl">
                        <div
                            class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-700">
                        </div>

                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                    <i data-lucide="zap" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Treino Diário</h3>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest">
                                            @if(!$isPremium)
                                                {{ $trainingCount }} de 3 treinos utilizados
                                            @else
                                                Sugerido para você
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($nextTraining)
                            <div class="mb-10 p-6 bg-zinc-950 rounded-3xl border border-zinc-800 shadow-inner">
                                <h4 class="text-xl font-black text-white mb-2 uppercase tracking-tight">{{ $nextTraining->name }}</h4>
                                <p class="text-zinc-500 text-sm italic font-medium">"Prepare-se para superar seus limites hoje."</p>
                            </div>
                        @endif

                        <a href="{{ route('exercise') }}"
                            class="flex items-center justify-between w-full p-2 pr-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-emerald-400 transition-all group/btn shadow-xl shadow-emerald-500/10 no-underline">
                            <div
                                class="h-14 w-14 bg-zinc-950 text-emerald-500 rounded-2xl flex items-center justify-center group-hover/btn:scale-110 transition-transform shadow-lg">
                                <i data-lucide="play" class="w-6 h-6 fill-current"></i>
                            </div>
                            <span class="text-lg uppercase tracking-widest">COMEÇAR AGORA</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 transition-transform group-hover/btn:translate-x-1"></i>
                        </a>
                    </div>

                    <!-- AI Intelligence & Alerts -->
                    <div class="group bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] relative overflow-hidden shadow-2xl transition-all hover:border-emerald-500/20 flex flex-col">
                        <div class="absolute -top-10 -right-10 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                            <i data-lucide="brain" class="w-64 h-64 text-emerald-500"></i>
                        </div>
 
                        <div class="flex items-center justify-between mb-8">
                            <div class="px-3 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">
                                {{ $isPremium ? 'Elite Analysis IA' : 'NexBot Coach' }}
                            </div>
                        </div>

                        <div class="space-y-4 flex-grow overflow-y-auto max-h-[180px] pr-2 custom-scrollbar relative z-10">
                            @forelse($healthAlerts as $alert)
                                <div class="flex gap-4 p-3 rounded-2xl bg-zinc-950/50 border {{ $alert->severity === 'danger' ? 'border-red-500/20' : ($alert->severity === 'warning' ? 'border-amber-500/20' : 'border-blue-500/20') }}">
                                    <div class="w-8 h-8 rounded-lg {{ $alert->severity === 'danger' ? 'bg-red-500/10 text-red-500' : ($alert->severity === 'warning' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500') }} flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="{{ $alert->severity === 'danger' ? 'alert-triangle' : ($alert->severity === 'warning' ? 'zap' : 'info') }}" class="w-4 h-4"></i>
                                    </div>
                                    <p class="text-[10px] text-zinc-400 leading-relaxed">{{ $alert->message }}</p>
                                </div>
                            @empty
                                <div class="relative">
                                    <i data-lucide="quote" class="absolute -left-6 -top-4 w-12 h-12 text-emerald-500/10"></i>
                                    <p class="text-2xl font-black text-white leading-tight tracking-tight pl-4 uppercase italic">
                                        "{{ $aiInsight }}"
                                    </p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 flex items-center justify-between border-t border-zinc-800 pt-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500 text-zinc-950 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                    <i data-lucide="bot" class="w-4 h-4"></i>
                                </div>
                                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">NexBot Ativo</span>
                            </div>
                            <a href="{{ route('assessments.index') }}" class="text-[10px] text-emerald-500 font-black uppercase tracking-widest hover:underline">Ver Histórico</a>
                        </div>
                    </div>
                </div>

                <!-- Comunidade NexShape Feed Widget -->
                <x-community.dashboard-widget :posts="$communityPosts" />

                <!-- Mentor / Profissional Vínculo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="group relative bg-zinc-900 border {{ $linkedProfessional ? 'border-emerald-500/20' : ($pendingRequest ? 'border-amber-500/20' : 'border-zinc-800') }} p-10 rounded-[3.5rem] overflow-hidden transition-all hover:scale-[1.01] shadow-2xl">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg {{ $linkedProfessional ? 'bg-emerald-500 text-zinc-950' : 'bg-zinc-950 text-zinc-700 border border-zinc-800' }}">
                                    @if($linkedProfessional)
                                        <i data-lucide="user-cog" class="w-7 h-7"></i>
                                    @else
                                        <i data-lucide="user-plus" class="w-7 h-7"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white uppercase tracking-tighter">
                                        {{ $linkedProfessional ? $linkedProfessional->name : ($pendingRequest ? 'Aguardando' : 'Mentor Profissional') }}
                                    </h3>
                                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">
                                        {{ $linkedProfessional ? 'Acompanhamento Ativo' : ($pendingRequest ? 'Solicitação em análise' : 'Aumente sua Performance') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($linkedProfessional)
                            <div class="mb-10 p-6 bg-zinc-950 rounded-3xl border border-zinc-800 shadow-inner">
                                <p class="text-zinc-500 text-sm leading-relaxed font-medium">Você está sob supervisão de <span class="text-emerald-400 font-black">{{ $linkedProfessional->name }}</span>. Seus dados são auditados por este mentor.</p>
                            </div>
                            <div class="flex gap-4">
                                <a href="{{ route('assessments.create') }}" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all text-[10px] uppercase tracking-widest text-center no-underline shadow-lg shadow-emerald-500/10">
                                    Enviar Medidas
                                </a>
                            </div>
                        @elseif($pendingRequest)
                            <div class="mb-10 p-6 bg-amber-500/5 rounded-3xl border border-amber-500/10 shadow-inner">
                                <p class="text-amber-400 text-xs font-medium leading-relaxed italic">"Sua solicitação com {{ $pendingRequest->professional->name }} está em análise. Você será notificado em breve."</p>
                            </div>
                        @else
                            <div class="mb-10 p-6 bg-zinc-950 rounded-3xl border border-zinc-800 shadow-inner">
                                <p class="text-zinc-500 text-sm leading-relaxed font-medium">Conecte-se a um especialista para ter acompanhamento profissional direto na plataforma.</p>
                            </div>
                            <a href="{{ route('patient.professionals.search') }}" class="flex items-center justify-between w-full p-2 pr-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-emerald-400 transition-all group/btn shadow-xl shadow-emerald-500/10 no-underline">
                                <div class="h-14 w-14 bg-zinc-950 text-emerald-500 rounded-2xl flex items-center justify-center group-hover/btn:scale-110 transition-transform shadow-lg">
                                    <i data-lucide="search" class="w-6 h-6"></i>
                                </div>
                                <span class="text-lg uppercase tracking-widest">Encontrar Mentor</span>
                                <i data-lucide="arrow-right" class="w-5 h-5 transition-transform group-hover/btn:translate-x-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Horizontal Footer Grid -->
            <div class="lg:col-span-12 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 items-stretch">

                <!-- WATER BLOCK 3.0 -->
                <div class="relative group bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-emerald-500/20">
                    <div class="relative z-20 flex flex-col h-full">
                        <div class="flex items-start justify-between mb-8">
                            <div>
                                <h3 class="text-2xl font-black text-white flex items-center gap-2 uppercase tracking-tighter">Nex<span class="text-emerald-500">Hydra</span></h3>
                                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Intelligent Hydration</p>
                            </div>
                            <div class="text-right">
                                <span class="text-4xl font-black text-white tabular-nums">{{ $waterConsumed }}</span>
                                <span class="text-[10px] text-zinc-600 font-black block uppercase tracking-widest mt-1">ml / {{ $waterTarget }}</span>
                            </div>
                        </div>

                        <!-- Wave Center -->
                        <div class="relative h-64 bg-zinc-950 rounded-[2.5rem] border border-zinc-800 shadow-inner flex items-center justify-center overflow-hidden mb-8">
                            <div class="absolute bottom-0 left-0 w-[300%] h-full transition-all duration-1000 cubic-bezier(0.4, 0, 0.2, 1)"
                                style="transform: translateY({{ 100 - min(($waterConsumed / ($waterTarget ?: 1)) * 100, 100) }}%)">
                                <svg class="absolute bottom-full left-0 w-full h-24 animate-wave-slow fill-emerald-500/20"
                                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                                    <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5,73.84-4.36,147.54,16.88,218.2,35.26,69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113,2,1200,34.47V0Z"></path>
                                </svg>
                                <svg class="absolute bottom-full left-[-100%] w-full h-20 animate-wave-fast fill-emerald-400/30"
                                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                                    <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5,73.84-4.36,147.54,16.88,218.2,35.26,69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113,2,1200,34.47V0Z"></path>
                                </svg>
                                <div class="w-full h-full bg-gradient-to-b from-emerald-500/30 to-emerald-700/50"></div>
                            </div>

                            <div class="relative z-30 text-center">
                                <span class="text-7xl font-black text-white tabular-nums">{{ number_format(($waterConsumed / ($waterTarget ?: 1)) * 100, 0) }}%</span>
                                <div class="flex items-center gap-2 justify-center mt-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <p class="text-[10px] text-emerald-500/60 font-black uppercase tracking-[0.3em]">Carga de Hidratação</p>
                                </div>
                            </div>
                        </div>

                        <!-- Hydro Controls -->
                        <form action="" method="POST" class="grid grid-cols-2 gap-4">
                            @csrf
                            <button type="submit" name="water_add" value="250"
                                class="h-16 bg-zinc-950 border border-zinc-800 rounded-2xl text-white font-black text-xs uppercase tracking-widest hover:bg-emerald-500 hover:text-zinc-950 transition-all active:scale-95 shadow-xl flex items-center justify-center gap-3">
                                <i data-lucide="droplets" class="w-4 h-4"></i>
                                250ml
                            </button>
                            <button type="submit" name="water_add" value="500"
                                class="h-16 bg-zinc-950 border border-zinc-800 rounded-2xl text-white font-black text-xs uppercase tracking-widest hover:bg-emerald-500 hover:text-zinc-950 transition-all active:scale-95 shadow-xl flex items-center justify-center gap-3">
                                <i data-lucide="glass-water" class="w-4 h-4"></i>
                                500ml
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Evolution Spark -->
                <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl relative overflow-hidden group transition-all hover:border-emerald-500/20">
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-emerald-500/5 blur-3xl rounded-full"></div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4 italic">Sua Transformação</p>
                            <div class="flex items-baseline gap-3 mb-6">
                                <h4 class="text-6xl font-black text-white tabular-nums tracking-tighter">{{ $lastWeight ? $lastWeight->weight : '--' }}</h4>
                                <span class="text-2xl font-black text-zinc-600 uppercase">kg</span>
                            </div>
                            <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                                <i data-lucide="trending-down" class="w-4 h-4 text-emerald-400"></i>
                                <span class="text-[10px] text-emerald-400 font-black uppercase tracking-widest">-0.8kg este mês</span>
                            </div>
                        </div>
                        <a @if(!$isPremium) data-premium-locked @else href="{{ route('assessments.index') }}" @endif
                            class="mt-10 w-full py-5 bg-zinc-950 border border-zinc-800 text-white font-black rounded-3xl hover:bg-emerald-500 hover:text-zinc-950 transition-all text-[10px] uppercase tracking-widest text-center no-underline shadow-xl flex items-center justify-center gap-3 relative overflow-hidden group">
                            @if(!$isPremium)
                                <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm z-10 flex items-center justify-center">
                                    <i data-lucide="lock" class="w-4 h-4 text-emerald-500"></i>
                                </div>
                            @endif
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Nova Avaliação
                        </a>
                        @if(!$isPremium)
                        <div class="mt-4 flex items-center justify-between px-2">
                            <span class="text-[9px] font-black text-zinc-600 uppercase tracking-widest italic">Limite Free atingido</span>
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">{{ $assessmentsCount }} de 1</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Achievements & Social -->
                <div class="bg-zinc-900 border border-zinc-800 p-10 rounded-[3.5rem] shadow-2xl flex flex-col justify-between">
                    <div class="space-y-10">
                        <div>
                            <h3 class="text-xl font-black text-white uppercase tracking-tighter mb-8 flex items-center justify-between">
                                Recordes
                                @if($prsCount > 0)
                                    <span class="px-3 py-1 bg-amber-500 text-zinc-950 text-[10px] font-black rounded-full">{{ $prsCount }}</span>
                                @endif
                            </h3>
                            <a @if(!$isPremium) data-premium-locked @else href="{{ route('progression.charts') }}" @endif class="flex items-center gap-5 p-4 rounded-3xl bg-zinc-950 border border-zinc-800 hover:border-amber-500/40 transition-all no-underline shadow-inner group relative">
                                @if(!$isPremium)
                                    <div class="absolute top-2 right-2 px-2 py-0.5 rounded-lg bg-amber-500 text-zinc-950 text-[8px] font-black uppercase tracking-widest z-20">PREMIUM</div>
                                    <div class="absolute inset-0 bg-zinc-950/40 backdrop-blur-[2px] rounded-3xl z-10 flex items-center justify-end pr-4">
                                        <i data-lucide="lock" class="w-4 h-4 text-amber-500"></i>
                                    </div>
                                @endif
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-amber-500 bg-zinc-900 shadow-xl border border-zinc-800 group-hover:scale-110 transition-transform">
                                    <i data-lucide="trophy" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="font-black text-white text-sm uppercase tracking-tight">Elite Performance</p>
                                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">
                                        {{ $prsCount > 0 ? 'Superou ' . $prsCount . ' limites' : 'Batendo metas' }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%),
                radial-gradient(at 50% 100%, rgba(20, 184, 166, 0.05) 0, transparent 40%);
            background-attachment: fixed;
        }

        @keyframes wave-slow {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        @keyframes wave-fast {
            from { transform: translateX(-25%); }
            to { transform: translateX(25%); }
        }

        .animate-wave-slow { animation: wave-slow 10s linear infinite; }
        .animate-wave-fast { animation: wave-fast 7s linear infinite; }

        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-bounce-slow { animation: bounce-slow 4s ease-in-out infinite; }

        .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.1); border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.2); }
    </style>
@endsection