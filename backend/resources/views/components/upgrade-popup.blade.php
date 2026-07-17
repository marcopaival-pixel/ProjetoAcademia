@props(['popup'])

<div x-data="{ open: true }" 
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/90 backdrop-blur-xl">
    
    <div class="bg-zinc-950 border border-white/10 w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-[0_0_100px_rgba(59,130,246,0.1)] relative animate-fade-in-up">
        
        <!-- Background Decor -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-600/10 blur-[120px] rounded-full"></div>

        <div class="p-12 relative z-10 flex flex-col items-center text-center">
            @if($popup->image_url)
                <img src="{{ $popup->image_url }}" alt="Premium" class="w-32 h-32 mb-8 object-contain">
            @else
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-[2rem] flex items-center justify-center text-white text-4xl shadow-2xl mb-8">
                    <i class="fas fa-crown"></i>
                </div>
            @endif

            <h2 class="text-3xl font-black text-white tracking-tight mb-4">{{ $popup->title }}</h2>
            <p class="text-zinc-400 text-sm leading-relaxed mb-8 max-w-md">
                {{ $popup->message }}
            </p>

            @if($popup->benefits)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full mb-10 text-left">
                @foreach($popup->benefits as $benefit)
                <div class="flex items-center gap-3 p-4 bg-white/5 border border-white/5 rounded-2xl">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 text-[10px]">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="text-xs text-zinc-300 font-bold uppercase tracking-widest">{{ $benefit }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="flex flex-col md:flex-row gap-4 w-full">
                <button @click="open = false" class="flex-1 px-8 py-5 bg-white/5 hover:bg-white/10 text-zinc-400 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl transition-all">
                    Continuar Versão Free
                </button>
                <a href="{{ route('plano') }}" class="flex-1 px-8 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-blue-500/20 text-center">
                    {{ $popup->button_text }}
                </a>
            </div>
        </div>

        <button @click="open = false" class="absolute top-8 right-8 text-zinc-600 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
</div>
