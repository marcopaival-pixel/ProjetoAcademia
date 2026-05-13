<!-- Modal de Publicação (Global) -->
<div x-data="{ open: false, sticker_id: null, sticker_url: null }" 
     @open-post-modal.window="open = true" 
     x-show="open" 
     x-cloak 
     class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-sm">
    
    <div @click.away="open = false" 
         class="bg-zinc-900 border border-zinc-800 w-full max-w-xl rounded-[3rem] p-8 shadow-3xl animate-fade-in-up relative overflow-hidden">
        
        <!-- Decorative Glow -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full pointer-events-none"></div>

        <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
            @csrf
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500">
                        <i data-lucide="plus-circle" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">Criar <span class="text-emerald-500">Post</span></h2>
                </div>
                <button type="button" @click="open = false" class="w-10 h-10 bg-zinc-800 text-zinc-500 hover:text-white rounded-xl transition-all flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Status Activity -->
                <div class="flex flex-wrap gap-2">
                    @foreach(['💪 Treino concluído', '🥗 Dieta em dia', '🏃 Corrida realizada', '🏆 Meta batida'] as $status)
                        <label class="cursor-pointer group/status">
                            <input type="radio" name="activity_status" value="{{ $status }}" class="hidden peer">
                            <span class="px-4 py-2 rounded-full bg-zinc-950 border border-zinc-800 text-[9px] font-black text-zinc-600 peer-checked:bg-emerald-500 peer-checked:text-zinc-950 peer-checked:border-emerald-500 transition-all uppercase tracking-widest block group-hover/status:border-emerald-500/30">
                                {{ $status }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <textarea name="content" 
                          rows="4" 
                          placeholder="O que você está treinando hoje?" 
                          class="w-full bg-zinc-950 border border-zinc-800 rounded-[2rem] p-6 text-white placeholder-zinc-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none font-medium"></textarea>
                
                <div x-show="sticker_url" class="relative w-32 h-32 mx-auto bg-zinc-950 rounded-2xl border border-zinc-800 p-2 animate-bounce-slow">
                    <img :src="sticker_url" class="w-full h-full object-contain">
                    <button type="button" @click="sticker_id = null; sticker_url = null" class="absolute -top-2 -right-2 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                    <input type="hidden" name="sticker_id" :value="sticker_id">
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex-1 flex items-center justify-center gap-3 px-6 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl text-zinc-500 hover:text-white hover:border-emerald-500/30 transition-all cursor-pointer group/input">
                        <i data-lucide="image" class="w-5 h-5 group-hover/input:text-emerald-500 transition-colors"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Fotos</span>
                        <input type="file" name="images[]" multiple class="hidden">
                    </label>

                    <button type="button" @click="$dispatch('open-sticker-selector')" class="flex-1 flex items-center justify-center gap-3 px-6 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl text-zinc-500 hover:text-white hover:border-emerald-500/30 transition-all group/input">
                        <i data-lucide="smile" class="w-5 h-5 group-hover/input:text-emerald-500 transition-colors"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Figurinhas</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <select name="visibility" class="bg-zinc-950 border border-zinc-800 rounded-2xl px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest outline-none focus:border-emerald-500/30 transition-all">
                        <option value="public">🌍 Público</option>
                        <option value="clinic">🏢 Minha Academia</option>
                        <option value="private">🔒 Privado</option>
                    </select>
                    
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl px-6 py-4 text-[10px] uppercase tracking-widest shadow-xl shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Publicar Agora
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Seletor de Figurinhas (Global) -->
<div x-data="{ open: false }" 
     @open-sticker-selector.window="open = true" 
     x-show="open" 
     x-cloak 
     class="fixed inset-0 z-[160] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    
    <div @click.away="open = false" 
         class="bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3rem] p-8 shadow-3xl relative overflow-hidden">
        
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Escolha sua <span class="text-emerald-500">Figurinha</span></h3>
            <button @click="open = false" class="w-8 h-8 bg-zinc-800 text-zinc-500 hover:text-white rounded-lg flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>

        <div class="grid grid-cols-4 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
            @php $stickers = \App\Models\CommunitySticker::where('is_active', true)->get(); @endphp
            @foreach($stickers as $sticker)
                <button type="button" 
                        @click="$dispatch('open-post-modal'); $root.sticker_id = {{ $sticker->id }}; $root.sticker_url = '{{ $sticker->url }}'; open = false" 
                        class="aspect-square bg-zinc-950 border border-zinc-800 rounded-2xl p-3 hover:border-emerald-500 transition-all group hover:scale-105 active:scale-95">
                    <img src="{{ $sticker->url }}" class="w-full h-full object-contain group-hover:rotate-12 transition-transform">
                </button>
            @endforeach
            @if($stickers->isEmpty())
                <div class="col-span-4 text-center py-10 text-zinc-600 font-bold italic">Nenhuma figurinha disponível.</div>
            @endif
        </div>
    </div>
</div>
