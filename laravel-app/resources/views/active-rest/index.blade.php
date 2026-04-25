@extends('layouts.app')

@section('title', 'Descanso Ativo — NexShape')

@section('content')
@if(auth()->user()->hasPremiumAccess())
    <div class="py-10 space-y-12 animate-fade-in max-w-[1400px] mx-auto px-6">
        <!-- Header Section -->
        <div class="mb-12 space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                            Protocolos de Biohacking
                        </span>
                        @if($isPremium)
                        <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-widest">
                            PRO Ativado
                        </span>
                        @endif
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-none">
                        Descanso <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Ativo</span>
                    </h1>
                    <p class="text-zinc-400 text-lg font-medium max-w-2xl leading-relaxed">
                        Otimize a sua recuperação com protocolos guiados de mobilidade, alongamento e libertação miofascial.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="?favorites={{ request('favorites') == '1' ? '0' : '1' }}" 
                       class="px-6 py-3 rounded-2xl border transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-widest {{ request('favorites') == '1' ? 'bg-amber-500 border-amber-500 text-zinc-950' : 'bg-zinc-900/40 border-white/5 text-zinc-400 hover:text-white' }}">
                        <i class="fas fa-star"></i>
                        Meus Favoritos
                    </a>
                </div>
            </div>

            <!-- Filter Bar -->
            <form action="{{ route('active-rest.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4 rounded-3xl bg-zinc-900/40 border border-white/5">
                <div class="space-y-1.5">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Categoria</label>
                    <select name="category" onchange="this.form.submit()" class="w-full bg-zinc-950 border-white/5 rounded-xl text-zinc-300 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <option value="Todos" {{ request('category') == 'Todos' ? 'selected' : '' }}>Todas Categorias</option>
                        <option value="Mobilidade" {{ request('category') == 'Mobilidade' ? 'selected' : '' }}>Mobilidade</option>
                        <option value="Alongamento" {{ request('category') == 'Alongamento' ? 'selected' : '' }}>Alongamento</option>
                        <option value="Prevenção de lesão" {{ request('category') == 'Prevenção de lesão' ? 'selected' : '' }}>Prevenção de lesão</option>
                        <option value="Relaxamento" {{ request('category') == 'Relaxamento' ? 'selected' : '' }}>Relaxamento</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Nível</label>
                    <select name="level" onchange="this.form.submit()" class="w-full bg-zinc-950 border-white/5 rounded-xl text-zinc-300 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <option value="Todos" {{ request('level') == 'Todos' ? 'selected' : '' }}>Todos os Níveis</option>
                        <option value="Iniciante" {{ request('level') == 'Iniciante' ? 'selected' : '' }}>Iniciante</option>
                        <option value="Intermediário" {{ request('level') == 'Intermediário' ? 'selected' : '' }}>Intermediário</option>
                        <option value="Avançado" {{ request('level') == 'Avançado' ? 'selected' : '' }}>Avançado</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Duração Máx.</label>
                    <select name="duration" onchange="this.form.submit()" class="w-full bg-zinc-950 border-white/5 rounded-xl text-zinc-300 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <option value="" {{ !request('duration') ? 'selected' : '' }}>Qualquer duração</option>
                        <option value="5" {{ request('duration') == '5' ? 'selected' : '' }}>Até 5 minutos</option>
                        <option value="10" {{ request('duration') == '10' ? 'selected' : '' }}>Até 10 minutos</option>
                        <option value="15" {{ request('duration') == '15' ? 'selected' : '' }}>Até 15 minutos</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('active-rest.index') }}" class="w-full py-2.5 rounded-xl bg-zinc-800 text-zinc-400 text-[10px] font-black uppercase tracking-widest text-center hover:bg-zinc-700 transition-all">
                        Limpar Filtros
                    </a>
                </div>
            </form>
        </div>

        <!-- Routines Grid -->
        @if($routines->isEmpty())
        <div class="p-20 text-center space-y-4 rounded-3xl bg-zinc-900/20 border border-dashed border-white/10">
            <div class="w-20 h-20 rounded-full bg-zinc-800/50 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-zinc-600 text-3xl"></i>
            </div>
            <h2 class="text-white font-black text-xl">Nenhuma rotina encontrada</h2>
            <p class="text-zinc-500 text-sm">Tente ajustar os filtros para encontrar outros protocolos.</p>
            <a href="{{ route('active-rest.index') }}" class="inline-block px-8 py-3 rounded-2xl bg-emerald-500 text-zinc-950 font-black text-xs uppercase tracking-widest">Ver tudo</a>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($routines as $routine)
                <div class="group relative bg-zinc-900/40 rounded-[2.5rem] border border-white/5 overflow-hidden hover:border-emerald-500/20 hover:shadow-2xl hover:shadow-emerald-500/5 transition-all duration-500 flex flex-col h-full animate-fade-in"
                     style="animation-delay: {{ $loop->index * 0.1 }}s">
                    
                    <!-- Category Badge -->
                    <div class="absolute top-6 left-6 z-20 flex flex-col gap-2">
                        <span class="px-3 py-1 rounded-lg bg-zinc-950/80 backdrop-blur-md border border-white/10 text-white text-[9px] font-black uppercase tracking-widest">
                            {{ $routine->category }}
                        </span>
                        <span class="px-3 py-1 rounded-lg bg-emerald-500/20 backdrop-blur-md border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-widest">
                            {{ $routine->recommended_level }}
                        </span>
                    </div>

                    <!-- Favorite Button -->
                    <button onclick="toggleFavorite(event, {{ $routine->id }})" 
                            class="absolute top-6 right-6 z-30 w-10 h-10 rounded-xl bg-zinc-950/80 backdrop-blur-md border border-white/10 flex items-center justify-center transition-all hover:scale-110 active:scale-95"
                            id="fav-btn-{{ $routine->id }}">
                        <i class="fas fa-star {{ in_array($routine->id, $userFavorites) ? 'text-amber-400' : 'text-zinc-600' }}"></i>
                    </button>

                    <!-- Card Thumbnail -->
                    <div class="relative aspect-[4/3] overflow-hidden bg-zinc-950">
                        <img src="{{ $routine->thumbnail ?? '/images/tutorials/hip_mobility.png' }}" 
                             alt="{{ $routine->title }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 grayscale-[30%] group-hover:grayscale-0">
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent"></div>
                        
                        @if($routine->is_premium && !$isPremium)
                        <div class="absolute inset-0 flex items-center justify-center bg-zinc-950/40 backdrop-blur-[2px] z-10">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-full bg-amber-500 flex items-center justify-center text-zinc-950 shadow-2xl">
                                    <i class="fas fa-lock text-lg"></i>
                                </div>
                                <span class="text-amber-500 font-black text-[10px] uppercase tracking-widest">Plano Pro</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-8 flex flex-col flex-grow">
                        <div class="flex items-center gap-4 text-zinc-500 text-[10px] font-black uppercase tracking-widest mb-3">
                            <span class="flex items-center gap-1.5"><i class="fas fa-clock text-emerald-400/60"></i> {{ $routine->duration }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-800"></span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-bolt text-emerald-400/60"></i> {{ $routine->intensity }}</span>
                        </div>
                        
                        <h3 class="text-xl font-black text-white leading-tight mb-4 group-hover:text-emerald-400 transition-colors">
                            {{ $routine->title }}
                        </h3>
                        
                        <p class="text-zinc-500 text-sm font-medium leading-relaxed mb-8 line-clamp-2">
                            {{ $routine->benefit }}
                        </p>

                        <div class="mt-auto pt-6 border-t border-white/5 flex items-center justify-between">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full border-2 border-zinc-950 bg-zinc-800 flex items-center justify-center text-[10px] text-zinc-500">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <a href="{{ route('active-rest.show', $routine->id) }}" 
                               class="px-6 py-2.5 rounded-xl {{ $routine->is_premium && !$isPremium ? 'bg-zinc-800 text-zinc-500 cursor-not-allowed' : 'bg-white text-zinc-950 hover:bg-emerald-500 hover:text-white' }} font-black text-[10px] uppercase tracking-widest transition-all">
                                {{ $routine->is_premium && !$isPremium ? 'Bloqueado' : 'Praticar' }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Recovery Insights Footer -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-10">
            @php
                $tips = [
                    ['icon' => '💧', 'title' => 'Hidratação Extra', 'msg' => 'Beba 500ml a mais para ajudar na síntese proteica.'],
                    ['icon' => '🚶', 'title' => 'Caminhada Leve', 'msg' => '15 min ao ar livre ajudam na oxigenação e circulação.'],
                    ['icon' => '💤', 'title' => 'Sono Profundo', 'msg' => 'Tente dormir 30 min mais cedo. O músculo cresce em repouso.']
                ];
            @endphp

            @foreach($tips as $tip)
                <div
                    class="flex gap-6 p-6 bg-emerald-500/5 border border-emerald-500/10 rounded-[2rem] items-start group hover:bg-emerald-500/10 transition-all">
                    <span class="text-4xl group-hover:scale-110 transition-transform">{{ $tip['icon'] }}</span>
                    <div class="space-y-1">
                        <h5 class="text-white font-black text-sm tracking-tight">{{ $tip['title'] }}</h5>
                        <p class="text-zinc-500 text-xs leading-relaxed font-medium">{{ $tip['msg'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

<script>
    function toggleFavorite(event, id) {
        event.preventDefault();
        event.stopPropagation();
        
        const btn = document.getElementById(`fav-btn-${id}`);
        const icon = btn.querySelector('i');
        
        fetch(`{{ url('/active-rest/toggle-favorite') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.isFavorite) {
                icon.classList.remove('text-zinc-600');
                icon.classList.add('text-amber-400');
            } else {
                icon.classList.remove('text-amber-400');
                icon.classList.add('text-zinc-600');
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.8s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    body {
        background-color: #0b0e14;
    }
</style>
@else
{{-- LAYOUT PAYWALL RECOVERY --}}
<div class="max-w-4xl mx-auto py-20 px-4 text-center">
    <div class="mb-10 inline-flex items-center justify-center w-24 h-24 rounded-[2.5rem] bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 shadow-2xl shadow-emerald-500/20">
        <i class="fas fa-heartbeat text-4xl"></i>
    </div>
    
    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter mb-6 leading-tight">
        Otimiza a tua <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-500">Recuperação</span>
    </h1>
    
    <p class="text-xl text-zinc-400 mb-12 max-w-2xl mx-auto font-medium leading-relaxed">
        Não treines apenas no duro, treina de forma inteligente. Acesso exclusivo a protocolos de regeneração biológica.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-16 text-left">
        <div class="p-8 rounded-[2.5rem] bg-zinc-900/50 border border-white/5 hover:border-emerald-500/30 transition-all flex gap-6">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <i class="fas fa-magic"></i>
            </div>
            <div>
                <h3 class="text-white font-bold mb-2 uppercase tracking-wide text-xs">Biohacking Sessions</h3>
                <p class="text-sm text-zinc-500 leading-relaxed">Tutoriais guiados de mobilidade e libertação miofascial para prevenir lesões e desbloquear performance.</p>
            </div>
        </div>
        
        <div class="p-8 rounded-[2.5rem] bg-zinc-900/50 border border-white/5 hover:border-blue-500/30 transition-all flex gap-6">
            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <h3 class="text-white font-bold mb-2 uppercase tracking-wide text-xs">Recuperação Express</h3>
                <p class="text-sm text-zinc-500 leading-relaxed">Protocolos rápidos de 8-15 minutos para dias de descanso, focados em oxigenação e circulação.</p>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-b from-emerald-500 to-teal-600 p-[1px] rounded-2xl inline-block shadow-2xl shadow-emerald-600/20">
        <a href="{{ route('plano') }}" class="flex items-center gap-3 px-10 py-5 bg-[#0b0e14] rounded-[15px] text-white hover:bg-transparent transition-all group">
            <i class="fas fa-crown text-amber-500"></i>
            <span class="font-black uppercase tracking-[0.2em] text-sm">Desbloquear NexRecovery</span>
            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    
    <p class="mt-8 text-zinc-600 text-[10px] uppercase font-bold tracking-widest italic">Ferramentas de biohacking para atletas pro</p>
</div>
@endif
@endsection