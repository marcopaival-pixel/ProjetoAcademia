@extends('layouts.app')

@section('title', 'Buscar Profissional — NexShape')

@section('content')
<div class="py-12 space-y-12 animate-fade-in max-w-[1200px] mx-auto px-6">
    <div class="text-center space-y-4">
        <h1 class="text-5xl font-black text-white italic tracking-tighter">Encontre seu <span class="text-indigo-400 underline decoration-indigo-500/30">Mentor</span></h1>
        <p class="text-zinc-500 font-medium text-lg">Busque por especialistas prontos para elevar o seu nível.</p>
    </div>

    <!-- Filters Section -->
    <div class="bg-zinc-900/40 backdrop-blur-2xl border border-white/5 p-8 rounded-[3rem] shadow-2xl">
        <form action="{{ route('patient.professionals.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Nome/Email -->
            <div class="space-y-2 lg:col-span-1">
                <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">Nome ou Código</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Ex: João Silva..." 
                    class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold placeholder:text-zinc-700 focus:outline-none focus:border-indigo-500/50 transition-all">
            </div>

            <!-- Especialidade -->
            <div class="space-y-2">
                <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">Especialidade</label>
                <select name="specialty" class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:border-indigo-500/50 transition-all appearance-none cursor-pointer">
                    <option value="">Todas</option>
                    @foreach($specialties as $specialty)
                        <option value="{{ $specialty->nome }}" {{ request('specialty') == $specialty->nome ? 'selected' : '' }}>{{ $specialty->nome }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo de Atendimento -->
            <div class="space-y-2">
                <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">Atendimento</label>
                <select name="service_type" class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:border-indigo-500/50 transition-all appearance-none cursor-pointer">
                    <option value="">Qualquer</option>
                    <option value="online" {{ request('service_type') == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="presencial" {{ request('service_type') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                </select>
            </div>

            <!-- Cidade -->
            <div class="space-y-2">
                <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">Cidade</label>
                <select name="city" class="w-full bg-black/40 border border-white/5 rounded-2xl py-4 px-6 text-white font-bold focus:outline-none focus:border-indigo-500/50 transition-all appearance-none cursor-pointer">
                    <option value="">Todas as Cidades</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botão Filtrar -->
            <div class="flex items-end">
                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-indigo-600/20 uppercase tracking-widest text-xs">
                    Filtrar Resultados
                </button>
            </div>
        </form>
    </div>

    <!-- Results Grid -->
    <div class="space-y-8">
        <div class="flex items-center justify-between px-4">
            <h2 class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em]">Profissionais Encontrados ({{ $professionals->total() }})</h2>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="available" id="available" {{ request('available') ? 'checked' : '' }} 
                    onchange="this.form.submit()" form="search-form"
                    class="rounded border-zinc-800 bg-zinc-900 text-indigo-600 focus:ring-indigo-500">
                <label for="available" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Apenas com disponibilidade</label>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($professionals as $pro)
                <div class="bg-zinc-900/40 backdrop-blur-xl border border-white/5 rounded-[3rem] p-8 flex flex-col space-y-6 transition-all hover:bg-zinc-900/60 hover:border-white/10 group shadow-lg">
                    <!-- Header: Foto e Nome -->
                    <div class="flex items-start justify-between">
                        <div class="flex gap-4">
                            @if($pro->avatar)
                                <img src="{{ asset('storage/' . $pro->avatar) }}" class="w-20 h-20 rounded-3xl object-cover shadow-2xl border-2 border-white/5">
                            @else
                                <div class="w-20 h-20 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center text-white font-black text-3xl shadow-2xl">
                                    {{ strtoupper(mb_substr($pro->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-xl font-black text-white group-hover:text-indigo-400 transition-colors leading-tight">{{ $pro->name }}</h3>
                                <p class="text-indigo-500/80 text-[10px] font-black uppercase tracking-widest mt-1">
                                    {{ $pro->professionalProfile?->profession?->name ?? 'Especialista' }}
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-zinc-500 text-[10px] font-bold uppercase">Disponível</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details: Especialidade, Cidade, Atendimento -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-black/20 p-4 rounded-2xl border border-white/5">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-tighter mb-1">Especialidade</p>
                            <p class="text-zinc-300 text-xs font-bold truncate">{{ $pro->professionalProfile?->specialty ?: 'Geral' }}</p>
                        </div>
                        <div class="bg-black/20 p-4 rounded-2xl border border-white/5">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-tighter mb-1">Localização</p>
                            <p class="text-zinc-300 text-xs font-bold truncate">{{ $pro->profile?->city ?: 'Remoto' }}</p>
                        </div>
                        <div class="bg-black/20 p-4 rounded-2xl border border-white/5 col-span-2">
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-tighter mb-1">Tipo de Atendimento</p>
                            <div class="flex gap-2">
                                @php $types = $pro->professionalProfile?->service_types ?? ['online']; @endphp
                                @foreach($types as $type)
                                    <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-indigo-500/20">
                                        {{ ucfirst($type) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                        <a href="{{ route('patient.professionals.show', $pro->id) }}" class="flex items-center justify-center py-4 bg-white/5 hover:bg-white/10 text-white font-black rounded-2xl border border-white/10 transition-all text-[9px] uppercase tracking-widest">
                            Ver Perfil
                        </a>
                        <a href="{{ route('patient.professionals.show', $pro->id) }}#schedule" class="flex items-center justify-center py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 text-[9px] uppercase tracking-widest">
                            Agendar
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 text-center bg-zinc-900/20 border border-white/5 border-dashed rounded-[4rem]">
                    <div class="w-20 h-20 bg-zinc-800/50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-zinc-700 text-3xl"></i>
                    </div>
                    <p class="text-zinc-500 font-bold uppercase tracking-widest text-xs">Nenhum profissional encontrado com os filtros selecionados.</p>
                    <a href="{{ route('patient.professionals.search') }}" class="inline-block mt-6 text-indigo-400 font-black uppercase text-[10px] tracking-[0.2em] hover:text-indigo-300 transition-colors">Limpar Filtros</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $professionals->links() }}
        </div>
    </div>
</div>
@endsection
