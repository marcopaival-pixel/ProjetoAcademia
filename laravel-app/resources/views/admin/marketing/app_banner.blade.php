@extends('layouts.admin')

@section('title', 'Banner do Aplicativo — Marketing')

@section('content')
<div class="animate-fade-in space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-emerald-600/10 border border-emerald-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-emerald-400 text-[9px] font-black uppercase tracking-widest">Marketing Hub</span>
                </div>
                <span class="text-zinc-600 text-[10px] font-bold tracking-tight">• Lançamento App Mobile</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                Banner do <span class="text-emerald-500">Aplicativo</span>
            </h1>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="localStorage.removeItem('hide_app_banner'); window.location.reload();" class="px-6 py-3 bg-zinc-900 border border-white/5 text-zinc-500 hover:text-emerald-400 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-all flex items-center gap-3">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Resetar Visualização
            </button>
            <a href="{{ route('admin.marketing.app-banner.leads') }}" class="px-6 py-3 bg-zinc-900 border border-white/5 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-zinc-800 transition-all flex items-center gap-3">
                <i data-lucide="users" class="w-4 h-4"></i>
                Ver Leads ({{ $metrics['leads'] }})
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-3xl shadow-xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Visualizações</p>
            <div class="text-3xl font-black text-white italic tracking-tighter uppercase">{{ number_format($metrics['views']) }}</div>
        </div>
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-3xl shadow-xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Cliques no CTA</p>
            <div class="text-3xl font-black text-emerald-500 italic tracking-tighter uppercase">{{ number_format($metrics['clicks']) }}</div>
        </div>
        <div class="bg-zinc-900/50 border border-white/5 p-6 rounded-3xl shadow-xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Taxa de Conversão</p>
            <div class="text-3xl font-black text-blue-500 italic tracking-tighter uppercase">
                {{ $metrics['views'] > 0 ? round(($metrics['leads'] / $metrics['views']) * 100, 1) : 0 }}%
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold flex items-center gap-3 animate-fade-in">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Form Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">
            <form action="{{ route('admin.marketing.app-banner.update') }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 border border-white/5 p-8 rounded-[2.5rem] shadow-2xl space-y-8">
                @csrf
                
                <div class="flex items-center justify-between p-6 bg-zinc-950/50 rounded-2xl border border-white/5">
                    <div>
                        <h4 class="text-white font-black uppercase tracking-widest text-xs">Ativar Banner</h4>
                        <p class="text-[10px] text-zinc-500 font-medium">Exibir o banner promocional no dashboard dos usuários.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="enabled" class="sr-only peer" {{ $settings['enabled'] ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-zinc-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Título do Banner</label>
                        <input type="text" name="title" value="{{ $settings['title'] }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500/50 outline-none transition-all" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Data Lançamento (Contador)</label>
                        <input type="date" name="launch_date" value="{{ $settings['launch_date'] }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Descrição / Texto de Apoio</label>
                    <textarea name="description" rows="3" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500/50 outline-none transition-all" required>{{ $settings['description'] }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Link Google Play</label>
                        <input type="text" name="google_play_link" value="{{ $settings['google_play_link'] }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Link App Store (iOS)</label>
                        <input type="text" name="apple_store_link" value="{{ $settings['apple_store_link'] }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Exibir nos Painéis</label>
                    <div class="bg-zinc-950 border border-white/5 rounded-2xl p-6 max-h-64 overflow-y-auto space-y-3 custom-scrollbar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($roles as $role)
                            <label class="flex items-center gap-3 p-3 bg-white/[0.02] border border-white/5 hover:border-emerald-500/30 rounded-2xl cursor-pointer transition-all group">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    class="w-5 h-5 rounded-lg border-white/10 bg-zinc-900 text-emerald-600 focus:ring-offset-zinc-950"
                                    {{ in_array($role->id, $settings['roles'] ?? []) ? 'checked' : '' }}>
                                <div class="flex flex-col">
                                    <span class="text-xs text-zinc-300 font-black uppercase tracking-wider group-hover:text-emerald-400 transition-colors">
                                        @if($role->name === 'admin') Painel Administrativo
                                        @elseif($role->name === 'professional') Painel do Profissional
                                        @elseif($role->name === 'aluno') Painel do Aluno
                                        @elseif($role->name === 'paciente') Painel do Paciente
                                        @elseif($role->name === 'instructor') Painel do Instrutor
                                        @elseif($role->name === 'manager') Painel do Gestor
                                        @elseif($role->name === 'receptionist') Painel da Recepção
                                        @elseif($role->name === 'finance') Painel Financeiro
                                        @else Painel {{ $role->label ?? $role->name }}
                                        @endif
                                    </span>
                                    <span class="text-[8px] text-zinc-600 font-bold uppercase tracking-widest">{{ $role->name }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-[0.2em] px-2 italic">
                        * Se nenhum painel for selecionado, o banner não será exibido para ninguém.
                    </p>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest px-2">Imagem de Fundo / Mockup (Upload)</label>
                    <div class="flex items-center gap-6 p-6 bg-zinc-950/50 border border-dashed border-white/10 rounded-2xl">
                        @if($settings['image_url'])
                            <img src="{{ $settings['image_url'] }}" class="w-20 h-20 rounded-lg object-cover border border-white/10">
                        @else
                            <div class="w-20 h-20 bg-zinc-900 rounded-lg flex items-center justify-center border border-white/5 text-zinc-700">
                                <i data-lucide="image" class="w-8 h-8"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="image" class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-emerald-600 file:text-zinc-950 hover:file:bg-emerald-500 transition-all">
                            <p class="text-[9px] text-zinc-600 mt-2 uppercase font-bold tracking-widest italic">PNG, JPG ou WEBP (Máx. 2MB)</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-zinc-900 flex justify-end">
                    <button type="submit" class="px-10 py-4 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black rounded-2xl transition-all shadow-2xl shadow-emerald-500/20 text-xs uppercase tracking-[0.2em]">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="bg-zinc-900 border border-white/5 p-8 rounded-[2.5rem] shadow-2xl h-full">
                <h3 class="text-white font-black uppercase tracking-widest text-xs mb-6 flex items-center gap-3 italic">
                    <i data-lucide="eye" class="w-4 h-4 text-emerald-500"></i>
                    Preview do Banner
                </h3>
                
                <div class="relative rounded-3xl overflow-hidden border border-white/5 aspect-[4/5] bg-zinc-950 group">
                    <!-- Banner Mockup -->
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-zinc-950 pointer-events-none"></div>
                    @if($settings['image_url'])
                        <img src="{{ $settings['image_url'] }}" class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:scale-110 transition-transform duration-[3s]">
                    @endif

                    <div class="relative h-full p-8 flex flex-col justify-end">
                        <div class="space-y-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-full text-emerald-400 text-[8px] font-black uppercase tracking-[0.2em]">
                                <i data-lucide="smartphone" class="w-3 h-3"></i>
                                Mobile App
                            </div>
                            <h4 class="text-2xl font-black text-white italic tracking-tighter uppercase leading-none">{{ $settings['title'] }}</h4>
                            <p class="text-xs text-zinc-400 font-medium leading-relaxed">{{ $settings['description'] }}</p>
                            
                            <div class="pt-4">
                                <button class="w-full py-4 bg-emerald-600 text-zinc-950 font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl shadow-emerald-500/20">
                                    Quero ser avisado
                                </button>
                            </div>

                            <div class="flex items-center gap-4 justify-center pt-2 grayscale opacity-40">
                                <i data-lucide="play-circle" class="w-5 h-5 text-white"></i>
                                <i data-lucide="apple" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="mt-6 text-[9px] text-zinc-600 font-bold uppercase tracking-[0.2em] text-center italic">Este preview é uma simulação da aparência no dashboard mobile.</p>
            </div>
        </div>
    </div>
</div>
@endsection
