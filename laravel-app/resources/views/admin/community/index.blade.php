@extends('layouts.admin')

@section('title', 'Moderação da Comunidade')

@section('content')
<div class="animate-fade-in space-y-8">
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Moderação <span class="text-emerald-500">Community</span></h1>
            <p class="text-zinc-500 font-medium">Controle de publicações, denúncias e biblioteca de figurinhas.</p>
        </div>
        <div class="flex gap-4">
            <div class="px-6 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl text-center">
                <span class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest">Pendentes</span>
                <span class="text-2xl font-black text-white">{{ $pendingPosts->count() }}</span>
            </div>
            <div class="px-6 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl text-center">
                <span class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest">Denúncias</span>
                <span class="text-2xl font-black text-rose-500">{{ $reports->count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna de Publicações Pendentes -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter italic">Aguardando Aprovação</h3>
                    <i class="fas fa-clock text-zinc-700"></i>
                </div>
                <div class="divide-y divide-zinc-800/50">
                    @forelse($pendingPosts as $post)
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $post->user->profile_photo_url }}" class="w-10 h-10 rounded-xl border border-zinc-800">
                                <div>
                                    <p class="text-white font-bold text-sm">{{ $post->user->name }}</p>
                                    <p class="text-[10px] text-zinc-600 font-bold uppercase">{{ $post->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-zinc-400 text-sm italic font-medium leading-relaxed">
                                "{{ $post->content }}"
                            </div>
                            @if($post->media->count() > 0)
                                <div class="flex gap-2">
                                    @foreach($post->media as $media)
                                        <div class="w-20 h-20 bg-zinc-950 border border-zinc-800 rounded-xl overflow-hidden">
                                            <img src="{{ $media->url }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex gap-3 pt-2">
                                <form action="{{ route('admin.community.post.status', $post) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" name="status" value="approved" class="w-full py-2 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-zinc-950 border border-emerald-500/20 font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">Aprovar</button>
                                </form>
                                <form action="{{ route('admin.community.post.status', $post) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" name="status" value="rejected" class="w-full py-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white border border-rose-500/20 font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">Rejeitar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center text-zinc-700 font-black uppercase tracking-widest text-xs italic">Nenhuma publicação pendente.</div>
                    @endforelse
                </div>
            </div>

            <!-- Denúncias -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-zinc-800 flex items-center justify-between bg-rose-500/5">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter italic">Denúncias Ativas</h3>
                    <i class="fas fa-exclamation-triangle text-rose-500"></i>
                </div>
                <div class="divide-y divide-zinc-800/50">
                    @forelse($reports as $report)
                        <div class="p-6 space-y-4 bg-zinc-950/20">
                            <div class="flex justify-between items-start">
                                <div class="space-y-1">
                                    <p class="text-rose-400 text-xs font-black uppercase tracking-widest">Motivo: {{ $report->reason }}</p>
                                    <p class="text-zinc-500 text-[10px] font-medium">Por: {{ $report->user->name }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.community.report.resolve', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit" name="action" value="delete_post" class="px-4 py-2 bg-rose-600 text-white font-black rounded-xl text-[9px] uppercase tracking-widest hover:bg-rose-500 transition-all">Remover Post</button>
                                    </form>
                                    <form action="{{ route('admin.community.report.resolve', $report) }}" method="POST">
                                        @csrf
                                        <button type="submit" name="action" value="dismiss" class="px-4 py-2 bg-zinc-800 text-zinc-400 font-black rounded-xl text-[9px] uppercase tracking-widest hover:bg-zinc-700 hover:text-white transition-all">Ignorar</button>
                                    </form>
                                </div>
                            </div>
                            <div class="p-4 bg-zinc-950 border border-zinc-800 rounded-2xl">
                                <p class="text-zinc-500 text-[10px] font-black uppercase tracking-tighter mb-2">Conteúdo Denunciado:</p>
                                <p class="text-zinc-300 text-xs italic">"{{ $report->post->content }}"</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center text-zinc-700 font-black uppercase tracking-widest text-xs italic">Nenhuma denúncia no momento.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Biblioteca de Figurinhas -->
        <div class="space-y-6">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-2xl">
                <h3 class="text-xl font-black text-white uppercase tracking-tighter italic mb-6">Nova <span class="text-emerald-500">Figurinha</span></h3>
                <form action="{{ route('admin.community.sticker.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Nome</label>
                        <input type="text" name="name" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-xs font-bold focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Categoria</label>
                        <select name="category" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white text-xs font-bold focus:border-emerald-500 outline-none transition-all">
                            <option value="Training">💪 Treino</option>
                            <option value="Diet">🥗 Dieta</option>
                            <option value="Motivation">🔥 Motivação</option>
                            <option value="General">✨ Geral</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Arquivo PNG/WEBP</label>
                        <input type="file" name="file" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-zinc-500 text-xs font-bold">
                    </div>
                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/10 transition-all active:scale-95">Adicionar Figurinha</button>
                </form>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-8 shadow-2xl">
                <h3 class="text-xl font-black text-white uppercase tracking-tighter italic mb-6">Biblioteca</h3>
                <div class="grid grid-cols-3 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($stickers as $sticker)
                        <div class="aspect-square bg-zinc-950 border border-zinc-800 rounded-xl p-2 relative group">
                            <img src="{{ $sticker->url }}" class="w-full h-full object-contain">
                            <div class="absolute inset-0 bg-zinc-950/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2 text-center">
                                <span class="text-[8px] text-white font-black uppercase tracking-widest">{{ $sticker->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
