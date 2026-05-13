@props(['status', 'isPremium' => false])

@php
    $colors = [
        'green' => [
            'bg' => 'from-emerald-500/20 to-emerald-600/5',
            'border' => 'border-emerald-500/30',
            'light' => 'bg-emerald-500 shadow-[0_0_20px_rgba(16,185,129,0.6)]',
            'text' => 'text-emerald-400',
            'glow' => 'bg-emerald-500/10',
        ],
        'orange' => [
            'bg' => 'from-amber-500/20 to-amber-600/5',
            'border' => 'border-amber-500/30',
            'light' => 'bg-amber-500 shadow-[0_0_20px_rgba(245,158,11,0.6)]',
            'text' => 'text-amber-400',
            'glow' => 'bg-amber-500/10',
        ],
        'red' => [
            'bg' => 'from-rose-500/20 to-rose-600/5',
            'border' => 'border-rose-500/30',
            'light' => 'bg-rose-500 shadow-[0_0_20px_rgba(244,63,94,0.6)]',
            'text' => 'text-rose-400',
            'glow' => 'bg-rose-500/10',
        ],
    ];

    $c = $colors[$status['color']] ?? $colors['orange'];
@endphp

<div class="relative group overflow-hidden bg-zinc-900 border {{ $c['border'] }} p-8 rounded-[3rem] shadow-2xl transition-all hover:scale-[1.01]">
    <div class="absolute inset-0 bg-gradient-to-br {{ $c['bg'] }} pointer-events-none"></div>
    <div class="absolute -top-24 -right-24 w-64 h-64 {{ $c['glow'] }} blur-[100px] rounded-full group-hover:scale-150 transition-transform duration-1000"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
        <!-- Semáforo Visual -->
        <div class="flex flex-col gap-4 bg-zinc-950/50 p-4 rounded-3xl border border-white/5 shadow-inner">
            <div class="w-8 h-8 rounded-full {{ $status['color'] === 'red' ? $c['light'] : 'bg-zinc-800' }} transition-all duration-500"></div>
            <div class="w-8 h-8 rounded-full {{ $status['color'] === 'orange' ? $c['light'] : 'bg-zinc-800' }} transition-all duration-500"></div>
            <div class="w-8 h-8 rounded-full {{ $status['color'] === 'green' ? $c['light'] : 'bg-zinc-800' }} transition-all duration-500"></div>
        </div>

        <!-- Info Principal -->
        <div class="flex-grow space-y-4 text-center md:text-left">
            <div class="flex items-center justify-center md:justify-start gap-3">
                <span class="px-3 py-1 rounded-full {{ $c['bg'] }} {{ $c['text'] }} text-[10px] font-black uppercase tracking-widest border {{ $c['border'] }}">
                    Evolução: {{ $status['status_label'] }}
                </span>
                @if($isPremium)
                    <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-widest border border-amber-500/20">
                        <i data-lucide="crown" class="w-3 h-3 inline mr-1"></i> Premium
                    </span>
                @endif
            </div>
            
            <h2 class="text-3xl font-black text-white italic tracking-tighter leading-none uppercase">Status da Jornada</h2>
            <p class="text-zinc-400 text-sm font-medium leading-relaxed max-w-xl">
                {{ $status['message'] }}
            </p>

            <div class="flex items-center justify-center md:justify-start gap-6 pt-2">
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Score Geral</p>
                    <p class="text-4xl font-black {{ $c['text'] }} tracking-tighter tabular-nums">{{ $status['score'] }}<span class="text-sm">/100</span></p>
                </div>
                <div class="h-10 w-[1px] bg-zinc-800 hidden md:block"></div>
                <div class="hidden md:block">
                    <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Análise de IA</p>
                    <p class="text-white text-xs font-bold mt-1">
                        @if($status['score'] >= 80) Consistência Impecável @elseif($status['score'] >= 50) Oscilação Detectada @else Requer Intervenção @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Call to Action ou Resumo Rápido -->
        <div class="shrink-0">
            <div class="relative w-24 h-24 flex items-center justify-center">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-800" />
                    <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="transparent" 
                            stroke-dasharray="282.7" 
                            stroke-dashoffset="{{ 282.7 - (282.7 * ($status['score'] / 100)) }}" 
                            stroke-linecap="round"
                            class="{{ $c['text'] }} transition-all duration-1000" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-8 h-8 {{ $c['text'] }}"></i>
                </div>
            </div>
        </div>
    </div>

    @if($isPremium)
        <!-- Detalhamento Premium -->
        <div class="mt-8 pt-8 border-t border-white/5 grid grid-cols-1 md:grid-cols-5 gap-4">
            @foreach($status['pillars'] as $pillar)
                <div class="p-4 bg-zinc-950/50 rounded-2xl border border-white/5 hover:border-{{ $pillar['score'] >= 80 ? 'emerald' : ($pillar['score'] >= 50 ? 'amber' : 'rose') }}-500/30 transition-all group/pillar">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">{{ $pillar['label'] }}</span>
                        <span class="text-xs font-black {{ $pillar['score'] >= 80 ? 'text-emerald-400' : ($pillar['score'] >= 50 ? 'text-amber-400' : 'text-rose-400') }}">{{ round($pillar['score']) }}%</span>
                    </div>
                    <div class="h-1 w-full bg-zinc-900 rounded-full overflow-hidden">
                        <div class="h-full {{ $pillar['score'] >= 80 ? 'bg-emerald-500' : ($pillar['score'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }} transition-all duration-1000" style="width: {{ $pillar['score'] }}%"></div>
                    </div>
                    <p class="text-[8px] text-zinc-600 mt-2 leading-tight group-hover/pillar:text-zinc-400 transition-colors">{{ $pillar['detail'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl flex items-start gap-4">
            <div class="w-8 h-8 bg-emerald-500 text-zinc-950 rounded-lg flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">Recomendação NexBot Ultra</p>
                <p class="text-white text-xs font-medium leading-relaxed italic">"{{ $status['recommendation'] }}"</p>
            </div>
        </div>
    @else
        <!-- Upsell para Free -->
        <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-between">
            <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest">Desbloqueie análise detalhada por pilar com o Plano Premium</p>
            <a href="{{ route('plano') }}" class="text-[9px] text-amber-500 font-black uppercase tracking-widest hover:underline flex items-center gap-1">
                Ver Planos <i data-lucide="chevron-right" class="w-3 h-3"></i>
            </a>
        </div>
    @endif
</div>
