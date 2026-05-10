@extends('layouts.app')

@section('title', 'Comunidade NexShape')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 py-8" x-data="{ }">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Coluna Principal (Feed) -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Header da Comunidade -->
    <div class="relative overflow-hidden rounded-[3rem] bg-zinc-900 border border-zinc-800 p-8 shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center md:text-left">
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Comunidade <span class="text-emerald-500">NexShape</span></h1>
                <p class="text-zinc-500 font-medium">Compartilhe sua evolução, motive outros atletas e conquiste medalhas.</p>
            </div>
            <button @click="$dispatch('open-post-modal')" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all shadow-xl shadow-emerald-500/10 flex items-center gap-3 text-xs uppercase tracking-widest active:scale-95">
                <i class="fas fa-plus"></i>
                Nova Publicação
            </button>
        </div>
    </div>

    <!-- Feed de Publicações -->
    <div class="space-y-6">
        @forelse($posts as $post)
            <div class="group relative bg-zinc-900 border border-zinc-800/50 rounded-[2.5rem] p-6 hover:border-emerald-500/30 transition-all duration-500 shadow-xl overflow-hidden">
                <!-- Status Badge -->
                @if($post->activity_status)
                    <div class="absolute top-6 right-6 px-4 py-1.5 bg-zinc-950/80 border border-zinc-800 rounded-full text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                        {{ $post->activity_status }}
                    </div>
                @endif

                <div class="flex gap-4">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <img src="{{ $post->user->profile_photo_url }}" alt="{{ $post->user->name }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-zinc-800 group-hover:border-emerald-500/50 transition-colors">
                    </div>

                    <!-- Post Content -->
                    <div class="flex-1 space-y-4">
                        <div class="space-y-0.5">
                            <h3 class="font-bold text-white text-lg">{{ $post->user->name }}</h3>
                            <p class="text-xs text-zinc-500 font-medium tracking-wide">{{ $post->created_at->diffForHumans() }} • <span class="uppercase tracking-tighter">{{ $post->visibility }}</span></p>
                        </div>

                        @if($post->content)
                            <div class="text-zinc-300 leading-relaxed font-medium">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        @endif

                        <!-- Media Grid -->
                        @if($post->media->count() > 0)
                            <div class="grid gap-3 {{ $post->media->count() > 1 ? 'grid-cols-2' : 'grid-cols-1' }}">
                                @foreach($post->media as $media)
                                    <div class="relative aspect-square overflow-hidden rounded-[1.5rem] bg-zinc-950 border border-zinc-800">
                                        @if($media->type === 'sticker')
                                            <img src="{{ $media->url }}" alt="Sticker" class="w-full h-full object-contain p-4 animate-float">
                                        @else
                                            <img src="{{ $media->url }}" alt="Post image" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Post Actions -->
                        <div class="pt-4 flex items-center gap-6 border-t border-zinc-800/50">
                            <button @click="reactPost({{ $post->id }}, '❤️')" class="flex items-center gap-2 text-zinc-500 hover:text-red-500 transition-colors group/btn">
                                <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center group-hover/btn:bg-red-500/10 transition-colors">
                                    <i class="far fa-heart"></i>
                                </div>
                                <span class="text-xs font-bold">{{ $post->reactions->where('emoji', '❤️')->count() }}</span>
                            </button>
                            
                            <button class="flex items-center gap-2 text-zinc-500 hover:text-emerald-500 transition-colors group/btn">
                                <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center group-hover/btn:bg-emerald-500/10 transition-colors">
                                    <i class="far fa-comment"></i>
                                </div>
                                <span class="text-xs font-bold">{{ $post->comments->count() }}</span>
                            </button>

                            <button class="flex items-center gap-2 text-zinc-500 hover:text-blue-500 transition-colors group/btn ml-auto">
                                <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center group-hover/btn:bg-blue-500/10 transition-colors">
                                    <i class="fas fa-share-alt"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-zinc-900 border border-zinc-800 rounded-[3rem] border-dashed">
                <div class="w-20 h-20 bg-zinc-950 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-users text-zinc-700 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Silêncio no Feed...</h3>
                <p class="text-zinc-500 mb-8">Seja o primeiro a motivar a galera hoje!</p>
                <button @click="$dispatch('open-post-modal')" class="px-8 py-3 bg-zinc-800 hover:bg-emerald-500 hover:text-zinc-950 text-white font-black rounded-xl transition-all text-[10px] uppercase tracking-widest italic">
                    Começar Publicação
                </button>
            </div>
        @endforelse

        <div class="pt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Coluna Lateral (Rankings) -->
    <div class="lg:col-span-4 space-y-8">
        <!-- Top Motivadores -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-2xl">
            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter mb-6 flex items-center gap-3">
                <i class="fas fa-crown text-amber-500"></i>
                Top <span class="text-emerald-500">Motivadores</span>
            </h3>
            <div class="space-y-4">
                @foreach($rankings['top_motivadores'] as $rank)
                    <div class="flex items-center gap-4 p-3 bg-zinc-950 border border-zinc-800 rounded-2xl">
                        <div class="w-8 h-8 rounded-full bg-zinc-900 flex items-center justify-center font-black text-xs text-zinc-500 border border-zinc-800">
                            {{ $loop->iteration }}
                        </div>
                        <img src="{{ $rank->profile_photo_url }}" class="w-10 h-10 rounded-xl border border-zinc-800">
                        <div class="flex-1">
                            <p class="text-white font-bold text-xs">{{ $rank->name }}</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $rank->social_score }} Reações</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Mestre das Figurinhas -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-2xl">
            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter mb-6 flex items-center gap-3">
                <i class="fas fa-sticky-note text-blue-500"></i>
                Mestre <span class="text-emerald-500">Stickers</span>
            </h3>
            <div class="space-y-4">
                @foreach($rankings['mestre_figurinhas'] as $rank)
                    <div class="flex items-center gap-4 p-3 bg-zinc-950 border border-zinc-800 rounded-2xl">
                        <img src="{{ $rank->profile_photo_url }}" class="w-10 h-10 rounded-xl border border-zinc-800">
                        <div class="flex-1">
                            <p class="text-white font-bold text-xs">{{ $rank->name }}</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $rank->social_score }} Figurinhas</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Influenciador -->
        <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-2xl">
            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter mb-6 flex items-center gap-3">
                <i class="fas fa-fire text-orange-500"></i>
                Top <span class="text-emerald-500">Influencer</span>
            </h3>
            <div class="space-y-4">
                @foreach($rankings['influenciadores'] as $rank)
                    <div class="flex items-center gap-4 p-3 bg-zinc-950 border border-zinc-800 rounded-2xl">
                        <img src="{{ $rank->profile_photo_url }}" class="w-10 h-10 rounded-xl border border-zinc-800">
                        <div class="flex-1">
                            <p class="text-white font-bold text-xs">{{ $rank->name }}</p>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $rank->social_score }} Comentários</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal de Publicação (Alpine.js) -->
<div x-data="{ open: false, sticker_id: null, sticker_url: null }" @open-post-modal.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-sm">
    <div @click.away="open = false" class="bg-zinc-900 border border-zinc-800 w-full max-w-xl rounded-[3rem] p-8 shadow-3xl animate-fade-in-up">
        <form action="{{ route('community.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">Criar <span class="text-emerald-500">Post</span></h2>
                <button type="button" @click="open = false" class="text-zinc-500 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Status Activity -->
                <div class="flex flex-wrap gap-2">
                    @foreach(['💪 Treino concluído', '🥗 Dieta em dia', '🏃 Corrida realizada', '🏆 Meta batida'] as $status)
                        <label class="cursor-pointer">
                            <input type="radio" name="activity_status" value="{{ $status }}" class="hidden peer">
                            <span class="px-4 py-2 rounded-full bg-zinc-950 border border-zinc-800 text-[10px] font-black text-zinc-500 peer-checked:bg-emerald-500 peer-checked:text-zinc-950 peer-checked:border-emerald-500 transition-all uppercase tracking-widest block">
                                {{ $status }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <textarea name="content" rows="4" placeholder="O que você está treinando hoje?" class="w-full bg-zinc-950 border border-zinc-800 rounded-[2rem] p-6 text-white placeholder-zinc-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all outline-none font-medium"></textarea>
                
                <div x-show="sticker_url" class="relative w-32 h-32 mx-auto bg-zinc-950 rounded-2xl border border-zinc-800 p-2">
                    <img :src="sticker_url" class="w-full h-full object-contain">
                    <button type="button" @click="sticker_id = null; sticker_url = null" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-[10px]">
                        <i class="fas fa-times"></i>
                    </button>
                    <input type="hidden" name="sticker_id" :value="sticker_id">
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex-1 flex items-center justify-center gap-3 px-6 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl text-zinc-500 hover:text-white hover:border-zinc-700 transition-all cursor-pointer">
                        <i class="fas fa-image"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Fotos</span>
                        <input type="file" name="images[]" multiple class="hidden">
                    </label>

                    <button type="button" @click="$dispatch('open-sticker-selector')" class="flex-1 flex items-center justify-center gap-3 px-6 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl text-zinc-500 hover:text-white hover:border-zinc-700 transition-all">
                        <i class="fas fa-sticky-note"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Figurinhas</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <select name="visibility" class="bg-zinc-950 border border-zinc-800 rounded-2xl px-6 py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest outline-none">
                        <option value="public">🌍 Público</option>
                        <option value="clinic">🏢 Minha Academia</option>
                        <option value="private">🔒 Privado</option>
                    </select>
                    
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl px-6 py-4 text-[10px] uppercase tracking-widest shadow-xl shadow-emerald-500/10 transition-all active:scale-95">
                        Publicar Agora
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Seletor de Figurinhas -->
<div x-data="{ open: false }" @open-sticker-selector.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm">
    <div @click.away="open = false" class="bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3rem] p-8 shadow-3xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter">Escolha sua <span class="text-emerald-500">Figurinha</span></h3>
            <button @click="open = false" class="text-zinc-500 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <div class="grid grid-cols-4 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
            @foreach($stickers as $sticker)
                <button type="button" @click="$dispatch('open-post-modal'); $root.sticker_id = {{ $sticker->id }}; $root.sticker_url = '{{ $sticker->url }}'; open = false" class="aspect-square bg-zinc-950 border border-zinc-800 rounded-xl p-2 hover:border-emerald-500 transition-all group">
                    <img src="{{ $sticker->url }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform">
                </button>
            @endforeach
            @if($stickers->isEmpty())
                <div class="col-span-4 text-center py-10 text-zinc-600 font-bold italic">Nenhuma figurinha disponível.</div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function reactPost(postId, emoji) {
        fetch(`/community/react/post/${postId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ emoji: emoji })
        })
        .then(response => response.json())
        .then(data => {
            // Atualizar UI via reload ou via Alpine se estiver usando state reativo completo
            window.location.reload();
        });
    }
</script>
@endpush

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
</style>
@endpush
