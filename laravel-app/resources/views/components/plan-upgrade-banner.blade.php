@if(auth()->check() && !auth()->user()->isPremiumActive())
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gradient-to-r from-zinc-900 to-zinc-950 border border-amber-500/10 p-4 rounded-2xl shadow-2xl mb-8 group transition-all hover:border-amber-500/30']) }}>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(245,158,11,0.1),transparent)]"></div>
    
    <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20 shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-crown text-sm"></i>
            </div>
            <div>
                <h5 class="text-xs font-black text-white uppercase tracking-widest">Você está no <span class="text-amber-500">Plano Free</span></h5>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">Sua produtividade está limitada. Desbloqueie 100% do potencial da plataforma.</p>
            </div>
        </div>
        
        <a href="{{ route('plano') }}" class="px-6 py-2.5 bg-amber-500 text-zinc-950 font-black rounded-xl hover:bg-amber-400 transition-all text-[9px] uppercase tracking-widest shadow-lg shadow-amber-500/20 active:scale-95 flex items-center gap-2">
            Fazer Upgrade Pro
            <i class="fas fa-chevron-right text-[7px]"></i>
        </a>
    </div>
</div>
@endif
