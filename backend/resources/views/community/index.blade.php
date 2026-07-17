@extends('layouts.app')

@section('title', 'Comunidade NexShape')

@section('content')
@php
    $communityInitialCounts = $posts->getCollection()->mapWithKeys(fn ($post) => [
        $post->id => [
            'reactions' => $post->reactions->count(),
            'comments' => $post->comments->count(),
        ],
    ]);
@endphp

<div class="max-w-[1400px] mx-auto px-4 py-8" x-data='communityFeed(@json($communityInitialCounts))' x-init="startSync()">
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
            <div id="post-{{ $post->id }}" class="group relative bg-zinc-900 border border-zinc-800/50 rounded-[2.5rem] p-6 hover:border-emerald-500/30 transition-all duration-500 shadow-xl overflow-hidden">
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

                        @if((int) $post->user_id === (int) auth()->id() || auth()->user()?->isAdministrator())
                            <div class="flex justify-end">
                                <button type="button" @click="deletePost = { open: true, action: '{{ route('community.destroy', $post) }}' }" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-[10px] text-zinc-500 hover:text-rose-400 hover:border-rose-500/30 hover:bg-rose-500/10 font-black uppercase tracking-widest transition-all" title="Excluir publicação">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    Excluir
                                </button>
                            </div>
                        @endif

                        @if($post->content)
                            <div class="text-zinc-300 leading-relaxed font-medium">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        @endif

                        <!-- Media Grid -->
                        @if($post->media->count() > 0)
                            <div class="grid gap-3 {{ $post->media->count() > 1 ? 'grid-cols-2' : 'grid-cols-1' }}">
                                @foreach($post->media as $media)
                                    <div class="relative h-64 sm:h-80 overflow-hidden rounded-[1.5rem] bg-zinc-950 border border-zinc-800">
                                        @if($media->type === 'sticker')
                                            <img src="{{ $media->url }}" alt="Sticker" class="w-full h-full object-contain p-4 animate-float">
                                        @else
                                            <button type="button" @click="imageLightbox = '{{ $media->url }}'" class="w-full h-full block group/media">
                                                <img src="{{ $media->url }}" alt="Post image" class="w-full h-full object-contain transition-transform duration-500 group-hover/media:scale-105">
                                            </button>
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
                                <span class="text-xs font-bold" x-text="counts[{{ $post->id }}]?.reactions ?? {{ $post->reactions->where('emoji', '❤️')->count() }}"></span>
                            </button>
                            
                            <button class="flex items-center gap-2 text-zinc-500 hover:text-emerald-500 transition-colors group/btn">
                                <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center group-hover/btn:bg-emerald-500/10 transition-colors">
                                    <i class="far fa-comment"></i>
                                </div>
                                <span class="text-xs font-bold" x-text="counts[{{ $post->id }}]?.comments ?? {{ $post->comments->count() }}"></span>
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

    <div x-show="imageLightbox" x-cloak class="fixed inset-0 z-[220] bg-zinc-950/90 backdrop-blur-sm flex items-center justify-center p-4" x-transition.opacity @keydown.escape.window="imageLightbox = null">
        <button type="button" class="absolute inset-0 cursor-zoom-out" @click="imageLightbox = null" aria-label="Fechar imagem"></button>
        <div class="relative max-w-5xl w-full max-h-[88vh]">
            <button type="button" @click="imageLightbox = null" class="absolute -top-12 right-0 w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-400 hover:text-white flex items-center justify-center transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            <img :src="imageLightbox" alt="Imagem ampliada" class="relative z-10 w-full max-h-[88vh] object-contain rounded-2xl border border-white/10 bg-zinc-950 shadow-2xl">
        </div>
    </div>

    <div x-show="deletePost.open" x-cloak class="fixed inset-0 z-[230] bg-zinc-950/85 backdrop-blur-sm flex items-center justify-center p-4" x-transition.opacity @keydown.escape.window="deletePost.open = false">
        <button type="button" class="absolute inset-0" @click="deletePost.open = false" aria-label="Cancelar exclusão"></button>
        <div class="relative w-full max-w-md bg-zinc-900 border border-rose-500/20 rounded-[2rem] p-6 shadow-2xl">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 flex items-center justify-center shrink-0">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </div>
                <div class="space-y-2">
                    <p class="text-[10px] text-rose-300 font-black uppercase tracking-[0.25em]">Excluir publicação</p>
                    <h3 class="text-xl font-black text-white uppercase tracking-tight">Tem certeza?</h3>
                    <p class="text-sm text-zinc-400 font-medium leading-relaxed">Essa publicação, imagens, comentários e reações serão removidos. Essa ação não pode ser desfeita.</p>
                </div>
            </div>

            <form method="POST" :action="deletePost.action" class="grid grid-cols-2 gap-3 mt-8">
                @csrf
                @method('DELETE')
                <button type="button" @click="deletePost.open = false" class="py-4 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:bg-zinc-800 text-xs font-black uppercase tracking-widest transition-all">
                    Cancelar
                </button>
                <button type="submit" class="py-4 rounded-2xl bg-rose-500 hover:bg-rose-400 text-white text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-rose-500/10">
                    Excluir
                </button>
            </form>
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
            window.dispatchEvent(new CustomEvent('community-count-updated', {
                detail: {
                    postId,
                    reactions: data.reactions_count,
                    comments: data.comments_count
                }
            }));
        });
    }

    function communityFeed(initialCounts = {}) {
        return {
            imageLightbox: null,
            deletePost: { open: false, action: '' },
            counts: initialCounts,
            syncTimer: null,
            startSync() {
                window.addEventListener('community-count-updated', event => {
                    const { postId, reactions, comments } = event.detail;
                    this.counts[postId] = {
                        reactions: reactions ?? this.counts[postId]?.reactions ?? 0,
                        comments: comments ?? this.counts[postId]?.comments ?? 0,
                    };
                });

                this.syncCounts();
                this.syncTimer = setInterval(() => this.syncCounts(), 10000);
            },
            syncCounts() {
                const ids = Object.keys(this.counts);
                if (!ids.length) return;

                fetch(`/community/reaction-counts?posts=${ids.join(',')}`)
                    .then(response => response.json())
                    .then(data => {
                        Object.entries(data.posts || {}).forEach(([id, payload]) => {
                            this.counts[id] = {
                                reactions: payload.reactions_count ?? 0,
                                comments: payload.comments_count ?? 0,
                            };
                        });
                    });
            }
        };
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
