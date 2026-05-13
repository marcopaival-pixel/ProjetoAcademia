@props(['posts', 'showActions' => true])

<div class="lg:col-span-12 group bg-zinc-900/50 backdrop-blur-3xl border border-white/5 p-12 rounded-[4rem] relative overflow-hidden shadow-[0_0_80px_rgba(0,0,0,0.5)] transition-all hover:border-emerald-500/20" x-data="{ loading: false }">
    <!-- Mesh Gradient Background -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 blur-[120px] rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-1000"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500/5 blur-[120px] rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-1000"></div>
    
    <div class="flex flex-col lg:flex-row items-center justify-between mb-16 relative z-10 gap-8">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="absolute -inset-2 bg-gradient-to-tr from-emerald-500 to-blue-500 rounded-3xl blur-md opacity-20 group-hover:opacity-40 transition-opacity"></div>
                <div class="w-16 h-16 bg-zinc-950 border border-white/10 text-emerald-500 rounded-2xl flex items-center justify-center shadow-2xl relative z-10">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl md:text-4xl font-black text-white uppercase tracking-tighter flex items-center gap-3">
                    🗨️ Comunidade <span class="text-emerald-500 italic">NexShape</span>
                </h3>
                <div class="flex items-center gap-3 mt-2">
                    <span class="w-8 h-[1px] bg-emerald-500/50"></span>
                    <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em]">Conexão • Performance • Evolução</p>
                </div>
            </div>
        </div>
        
        @if($showActions)
        <div class="flex flex-wrap items-center gap-4">
            @auth
                <button @click="$dispatch('open-post-modal')" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center gap-3 shadow-xl shadow-emerald-500/20 active:scale-95">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Nova Publicação
                </button>
            @else
                <a href="{{ route('login') }}" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center gap-3 shadow-xl shadow-emerald-500/20 active:scale-95">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    Participar Agora
                </a>
            @endauth
            <a href="{{ route('community.index') }}" class="px-8 py-4 bg-zinc-950/50 hover:bg-zinc-900 text-white border border-white/5 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center gap-3 backdrop-blur-xl group/btn">
                Ir para o Feed
                <i data-lucide="arrow-right" class="w-4 h-4 text-emerald-500 group-hover/btn:translate-x-1 transition-transform"></i>
            </a>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8 relative z-10" x-show="!loading">
        @forelse($posts as $post)
            <div class="bg-zinc-950/40 backdrop-blur-xl border border-white/5 p-8 rounded-[2.5rem] space-y-6 hover:border-emerald-500/30 hover:bg-zinc-950/60 transition-all duration-500 flex flex-col group/post relative overflow-hidden">
                <!-- Inner Glow Decor -->
                <div class="absolute -top-10 -right-10 w-20 h-20 bg-emerald-500/5 blur-2xl rounded-full opacity-0 group-hover/post:opacity-100 transition-opacity"></div>
                
                <!-- Header -->
                <div class="flex items-center gap-4 relative z-10">
                    <div class="relative group/avatar">
                        <div class="absolute -inset-1 bg-gradient-to-tr from-emerald-500 to-blue-500 rounded-2xl blur opacity-0 group-hover/post:opacity-30 transition-opacity"></div>
                        <img src="{{ $post->user->profile_photo_url }}" class="w-14 h-14 rounded-2xl border border-white/10 shadow-2xl object-cover relative z-10 grayscale-[30%] group-hover/post:grayscale-0 transition-all duration-500">
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-[3px] border-zinc-950 flex items-center justify-center z-20">
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-[0_0_10px_white]"></div>
                        </div>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-white font-black text-sm truncate tracking-tight">{{ $post->user->name }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[8px] px-2 py-0.5 bg-white/5 text-zinc-400 group-hover/post:text-emerald-400 group-hover/post:bg-emerald-500/10 rounded-lg font-black uppercase tracking-widest border border-white/5 transition-all">
                                {{ $post->user->community_profile_label }}
                            </span>
                            <span class="text-[8px] text-zinc-600 font-bold uppercase tracking-tighter">{{ $post->created_at->diffForHumans(null, true) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-grow relative z-10">
                    <p class="text-zinc-400 text-[12px] line-clamp-4 font-medium leading-relaxed italic group-hover/post:text-zinc-200 transition-colors duration-500">
                        "{{ $post->content }}"
                    </p>
                </div>

                <!-- Media Preview (if exists) -->
                @if($post->media->count() > 0)
                    <div class="relative aspect-video rounded-3xl overflow-hidden border border-white/5 group-hover/post:border-emerald-500/20 transition-all duration-500 shadow-2xl">
                        @php $firstMedia = $post->media->first(); @endphp
                        @if($firstMedia->type === 'sticker')
                            <div class="w-full h-full bg-zinc-900/30 flex items-center justify-center p-6">
                                <img src="{{ $firstMedia->url }}" class="h-full object-contain drop-shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                            </div>
                        @else
                            <img src="{{ $firstMedia->url }}" class="w-full h-full object-cover grayscale-[20%] group-hover/post:grayscale-0 transition-all duration-700">
                        @endif
                        
                        @if($post->media->count() > 1)
                            <div class="absolute bottom-3 right-3 px-3 py-1.5 bg-black/60 backdrop-blur-xl rounded-xl border border-white/10 text-[9px] font-black text-white uppercase tracking-widest">
                                +{{ $post->media->count() - 1 }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/60 to-transparent opacity-0 group-hover/post:opacity-100 transition-opacity duration-500"></div>
                    </div>
                @endif

                <!-- Footer Stats -->
                <div class="flex items-center justify-between pt-6 border-t border-white/5 relative z-10">
                    <div class="flex items-center gap-5">
                        <div class="flex items-center gap-2 text-zinc-500 group-hover/post:text-red-400 transition-colors">
                            <i data-lucide="heart" class="w-4 h-4 {{ $post->reactions->count() > 0 ? 'fill-red-500/20 text-red-500' : '' }}"></i>
                            <span class="text-[11px] font-black tabular-nums">{{ $post->reactions->count() }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-500 group-hover/post:text-emerald-400 transition-colors">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            <span class="text-[11px] font-black tabular-nums">{{ $post->comments->count() }}</span>
                        </div>
                    </div>
                    
                    @if($showActions)
                    <a href="{{ route('community.index') }}#post-{{ $post->id }}" class="group/more text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] hover:text-emerald-400 transition-colors flex items-center gap-2">
                        EXPLORAR
                        <i data-lucide="chevron-right" class="w-3 h-3 group-hover/more:translate-x-1 transition-transform"></i>
                    </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-zinc-950/50 rounded-3xl border border-dashed border-zinc-800 space-y-4">
                <div class="w-16 h-16 bg-zinc-900 rounded-2xl flex items-center justify-center mx-auto text-zinc-700">
                    <i data-lucide="message-square-off" class="w-8 h-8"></i>
                </div>
                <div class="space-y-1">
                    <p class="text-zinc-500 font-black uppercase tracking-[0.2em] text-[10px]">Silêncio no Feed</p>
                    <p class="text-zinc-600 font-medium italic text-xs">Seja o primeiro a publicar e motivar a galera!</p>
                </div>
                @if($showActions)
                    @auth
                        <button @click="$dispatch('open-post-modal')" class="px-6 py-3 bg-zinc-900 hover:bg-emerald-500 hover:text-zinc-950 text-white font-black rounded-xl transition-all text-[9px] uppercase tracking-widest border border-zinc-800">
                            Começar Agora
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 bg-zinc-900 hover:bg-emerald-500 hover:text-zinc-950 text-white font-black rounded-xl transition-all text-[9px] uppercase tracking-widest border border-zinc-800">
                            Começar Agora
                        </a>
                    @endauth
                @endif
            </div>
        @endforelse
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="col-span-full py-20 flex flex-col items-center justify-center space-y-4">
        <div class="w-12 h-12 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin"></div>
        <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px] animate-pulse">Sincronizando Feed...</p>
    </div>
</div>
