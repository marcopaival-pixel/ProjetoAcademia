@props(['resource' => 'patients'])

@php
    $surplus = auth()->user()->getSurplusCount($resource);
@endphp

@if($surplus > 0)
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gradient-to-r from-red-900/20 to-zinc-950 border border-red-500/20 p-4 rounded-2xl shadow-2xl mb-8 group transition-all hover:border-red-500/40']) }}>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(239,68,68,0.1),transparent)]"></div>
    
    <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20 shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-exclamation-triangle text-sm"></i>
            </div>
            <div>
                <h5 class="text-xs font-black text-white uppercase tracking-widest">Você excedeu o limite do plano atual</h5>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">
                    Há <span class="text-red-500">{{ $surplus }}</span> {{ $resource === 'patients' ? 'pacientes' : ($resource === 'workouts' ? 'treinos' : $resource) }} bloqueados. Faça upgrade para reativá-los.
                </p>
            </div>
        </div>
        
        <a href="{{ route('plano') }}" class="px-6 py-2.5 bg-red-500 text-white font-black rounded-xl hover:bg-red-400 transition-all text-[9px] uppercase tracking-widest shadow-lg shadow-red-500/20 active:scale-95 flex items-center gap-2">
            Regularizar Plano
            <i class="fas fa-arrow-up text-[7px]"></i>
        </a>
    </div>
</div>
@endif
