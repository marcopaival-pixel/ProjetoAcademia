@extends('layouts.app')

@section('title', 'Galeria de Evolução Fotorrápida — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-inner shadow-emerald-500/5">Visual Tracking</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter">Histórico de Transformação Física</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight uppercase">Galeria de <span class="text-emerald-500">Fotos</span></h1>
            <p class="text-zinc-500 font-medium">Acompanhe sua evolução visual e métricas corporais ao longo da sua jornada NexShape.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-8 py-4 bg-emerald-500 text-zinc-950 font-black text-xs rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 flex items-center gap-3 uppercase tracking-widest">
                <i data-lucide="camera" class="w-4 h-4"></i>
                Lançar Registro
            </button>
        </div>
    </div>

    <!-- Alert Handling -->
    @if(session('success'))
        <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-[2rem] text-emerald-400 text-xs font-black animate-fade-in flex items-center gap-4 shadow-xl">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-zinc-950 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </div>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-6 bg-rose-500/10 border border-rose-500/20 rounded-[2rem] text-rose-400 text-xs font-black animate-fade-in flex items-center gap-4 shadow-xl">
            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            </div>
            {{ session('error') }}
        </div>
    @endif

    <!-- Photo Gallery Grid -->
    @forelse($photos as $month => $monthPhotos)
        <div class="space-y-6">
            <div class="flex items-center gap-4 pl-4">
                <div class="w-1.5 h-6 bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                <h3 class="text-sm font-black text-white uppercase tracking-[0.3em] italic">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</h3>
                <div class="h-[1px] flex-1 bg-zinc-900"></div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                @foreach($monthPhotos as $photo)
                    <div class="relative group bg-zinc-950 border border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-2xl transition-all hover:border-emerald-500/30">
                        <div class="aspect-[3/4] relative overflow-hidden">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto de Evolução" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent flex flex-col justify-end p-8 opacity-90 group-hover:opacity-100 transition-opacity">
                                <span class="text-white font-black text-lg tracking-tighter tabular-nums drop-shadow-2xl">{{ \Carbon\Carbon::parse($photo->registered_date)->format('d/m/Y') }}</span>
                                @if($photo->weight_kg)
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        <span class="text-emerald-400 text-[10px] font-black uppercase tracking-widest tabular-nums">{{ $photo->weight_kg }} kg</span>
                                    </div>
                                @endif
                                <div class="mt-4 flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md bg-zinc-900/80 backdrop-blur-md border border-white/5 text-[8px] text-zinc-500 font-black uppercase tracking-widest italic">{{ $photo->type ?? 'Geral' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="absolute top-6 right-6 flex flex-col gap-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                            <form action="{{ route('evolution.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Purgar registo visual?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-12 h-12 rounded-2xl bg-zinc-950/90 border border-zinc-800 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all backdrop-blur-xl shadow-2xl">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                            <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank" class="w-12 h-12 rounded-2xl bg-zinc-950/90 border border-zinc-800 text-emerald-500 flex items-center justify-center hover:bg-emerald-500 hover:text-zinc-950 hover:border-emerald-500 transition-all backdrop-blur-xl shadow-2xl">
                                <i data-lucide="maximize-2" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <!-- Empty State -->
        <div class="p-20 text-center bg-zinc-900 border border-zinc-800 rounded-[3.5rem] max-w-2xl mx-auto mt-20 shadow-2xl relative overflow-hidden group">
            <div class="absolute -inset-20 bg-emerald-500/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
            <div class="relative z-10">
                <div class="w-24 h-24 bg-zinc-950 border border-zinc-800 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <i data-lucide="image" class="w-10 h-10 text-zinc-800 group-hover:text-emerald-500 transition-colors"></i>
                </div>
                <h4 class="text-2xl font-black text-white mb-4 uppercase tracking-tighter">Primeiro Registro</h4>
                <p class="text-zinc-500 text-sm mb-10 max-w-xs mx-auto font-medium">Nenhuma foto registrada na galeria. Tire uma foto hoje e comece a acompanhar cada pequeno avanço visual!</p>
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-10 py-5 bg-emerald-500 text-zinc-950 font-black rounded-2xl hover:bg-emerald-400 active:scale-95 transition-all text-xs tracking-widest uppercase shadow-xl shadow-emerald-500/10">
                    Tirar Foto Agora
                </button>
            </div>
        </div>
    @endforelse

</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-zinc-950/95 backdrop-blur-xl hidden animate-fade-in">
    <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] max-w-md w-full overflow-hidden relative shadow-3xl animate-fade-in-up">
        <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="absolute top-8 right-8 text-zinc-600 hover:text-rose-500 transition-all bg-zinc-950 border border-zinc-800 w-12 h-12 rounded-2xl flex items-center justify-center shadow-xl">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        
        <div class="px-10 py-8 border-b border-zinc-800 bg-zinc-900/50">
            <h2 class="text-2xl font-black text-white uppercase tracking-tighter italic flex items-center gap-4">
                <i data-lucide="camera" class="w-7 h-7 text-emerald-500"></i>
                Registrar Foto
            </h2>
            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Sincronização Visual de Performance</p>
        </div>
        
        <form action="{{ route('evolution.store') }}" method="POST" enctype="multipart/form-data" class="px-10 py-10 space-y-8">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[9px] uppercase font-black text-zinc-500 tracking-widest ml-2">Data do Registro</label>
                        <input type="date" name="registered_date" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white text-xs font-black focus:outline-none focus:border-emerald-500/50 transition-colors shadow-inner uppercase" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[9px] uppercase font-black text-zinc-500 tracking-widest ml-2">Ângulo/Tipo</label>
                        <select name="type" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white text-xs font-black focus:outline-none focus:border-emerald-500/50 transition-colors appearance-none shadow-inner uppercase tracking-widest cursor-pointer">
                            <option value="front">FRENTE</option>
                            <option value="side">LADO (ESQ/DIR)</option>
                            <option value="back">COSTAS</option>
                            <option value="custom">OUTRO</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[9px] uppercase font-black text-zinc-500 tracking-widest ml-2">Peso Corporal (Opcional)</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="weight_kg" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 pr-14 text-white text-sm font-black focus:outline-none focus:border-emerald-500/50 transition-colors shadow-inner tabular-nums" placeholder="EX: 82.5">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-700 font-black text-[10px] tracking-widest">KG</span>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[9px] uppercase font-black text-zinc-500 tracking-widest ml-2">Arquivo de Mídia</label>
                    <div class="relative group/file">
                        <input type="file" name="photo" accept="image/*" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-4 text-zinc-600 text-[10px] font-black focus:outline-none focus:border-emerald-500/50 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:bg-zinc-800 file:text-emerald-500 file:uppercase file:tracking-widest cursor-pointer" required>
                    </div>
                </div>
            </div>
            
            <div class="pt-6">
                <button type="submit" class="w-full py-6 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black uppercase text-[10px] tracking-[0.3em] rounded-3xl transition-all shadow-2xl shadow-emerald-500/20 active:scale-95">
                    REGISTRAR AGORA
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush

<style>
    body { 
        background-color: #080a0f;
        background-image:
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }
    
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Custom Scrollbar for Modal if needed */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.1); border-radius: 20px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.2); }
</style>
@endsection
