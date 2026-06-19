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
            <div class="group relative bg-zinc-900 border border-zinc-800/50 rounded-[2.5rem] p-6 hover:border-emerald-500/30 transition-all duration-500 shadow-xl overflow-hidden" x-data="{ showComments: false }">
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
                            @php
                                $hasLiked = $post->reactions->where('user_id', auth()->id())->where('emoji', '❤️')->count() > 0;
                            @endphp
                            <button @click="reactPost({{ $post->id }}, '❤️')" class="flex items-center gap-2 transition-colors group/btn {{ $hasLiked ? 'text-red-500' : 'text-zinc-500 hover:text-red-500' }}" id="like-btn-{{ $post->id }}">
                                <div class="w-10 h-10 rounded-full bg-zinc-950 flex items-center justify-center transition-colors {{ $hasLiked ? 'bg-red-500/10' : 'group-hover/btn:bg-red-500/10' }}">
                                    <i class="fa-heart {{ $hasLiked ? 'fas text-red-500' : 'far' }}" id="like-icon-{{ $post->id }}"></i>
                                </div>
                                <span class="text-xs font-bold" id="like-count-{{ $post->id }}">{{ $post->reactions->where('emoji', '❤️')->count() }}</span>
                            </button>
                            
                            <button @click="showComments = !showComments" class="flex items-center gap-2 text-zinc-500 hover:text-emerald-500 transition-colors group/btn">
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

                        <!-- Comments Section (Alpine x-show) -->
                        <div x-show="showComments" x-collapse x-cloak class="pt-4 mt-4 border-t border-zinc-800/50">
                            <!-- Comment form -->
                            <form action="{{ route('community.comment', $post->id) }}" method="POST" class="flex gap-3 mb-4">
                                @csrf
                                <img src="{{ auth()->user()->profile_photo_url }}" class="w-10 h-10 rounded-xl border border-zinc-800">
                                <div class="flex-1 flex gap-2">
                                    <input type="text" name="content" placeholder="Escreva um comentário..." class="flex-1 bg-zinc-950 border border-zinc-800 rounded-xl px-4 text-xs text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-all" required maxlength="500">
                                    <button type="submit" class="px-6 bg-emerald-500 text-zinc-950 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-500/10 active:scale-95 flex items-center justify-center">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>

                            <!-- Comment list -->
                            @if($post->comments->count() > 0)
                                <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                                    @foreach($post->comments->sortByDesc('created_at') as $comment)
                                        <div class="flex gap-3 bg-zinc-950/40 p-4 rounded-2xl border border-zinc-800/30">
                                            <img src="{{ $comment->user->profile_photo_url }}" class="w-8 h-8 rounded-xl border border-zinc-800 flex-shrink-0">
                                            <div class="flex-1">
                                                <div class="flex items-baseline justify-between mb-1">
                                                    <span class="text-xs font-bold text-white">{{ $comment->user->name }}</span>
                                                    <span class="text-[9px] text-zinc-500 uppercase tracking-widest">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-xs text-zinc-300 leading-relaxed">{{ $comment->content }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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

@endsection

@push('scripts')
<script>
    function reactPost(postId, emoji) {
        // Toggle visual imediato para melhor UX
        const btn = document.getElementById('like-btn-' + postId);
        const icon = document.getElementById('like-icon-' + postId);
        const countSpan = document.getElementById('like-count-' + postId);
        
        let isLiked = icon.classList.contains('fas');
        let currentCount = parseInt(countSpan.textContent) || 0;

        if (isLiked) {
            icon.classList.remove('fas', 'text-red-500');
            icon.classList.add('far');
            btn.classList.remove('text-red-500');
            btn.classList.add('text-zinc-500');
            btn.querySelector('.rounded-full').classList.remove('bg-red-500/10');
            countSpan.textContent = Math.max(0, currentCount - 1);
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas', 'text-red-500');
            btn.classList.remove('text-zinc-500');
            btn.classList.add('text-red-500');
            btn.querySelector('.rounded-full').classList.add('bg-red-500/10');
            countSpan.textContent = currentCount + 1;
        }

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
            // Se houver algum erro do servidor (ex: status errado), revertemos
            if (data.status !== 'added' && data.status !== 'removed') {
                window.location.reload();
            }
        })
        .catch(() => {
            // Em caso de falha de rede, recarregar a página para o estado original
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
