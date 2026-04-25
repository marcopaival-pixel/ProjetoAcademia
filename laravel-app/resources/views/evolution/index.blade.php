@extends('layouts.app')

@section('title', 'Galeria de Evolução Fotorrápida — NexShape')

@section('content')
<div class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4 relative z-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tighter uppercase">Galeria de <span class="text-indigo-500">Fotos</span></h1>
            <p class="text-zinc-500 text-sm font-medium">Acompanhe sua evolução visual ao longo do tempo.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-6 py-3 bg-indigo-600 text-white font-black text-xs rounded-xl hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                <i class="fas fa-camera"></i>
                Lançar Registro
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    @forelse($photos as $month => $monthPhotos)
        <div class="space-y-4">
            <h3 class="text-xs font-black text-zinc-500 uppercase tracking-widest pl-2 border-l-2 border-indigo-500">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($monthPhotos as $photo)
                    <div class="relative bg-zinc-900 border border-white/5 rounded-[2rem] overflow-hidden group shadow-2xl">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto" class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent flex flex-col justify-end p-5 opacity-80 group-hover:opacity-100 transition-opacity">
                            <span class="text-white font-black text-sm drop-shadow-md">{{ \Carbon\Carbon::parse($photo->registered_date)->format('d/m/Y') }}</span>
                            @if($photo->weight_kg)
                                <span class="text-indigo-400 text-[10px] font-black uppercase tracking-widest">{{ $photo->weight_kg }} kg</span>
                            @endif
                        </div>
                        <form action="{{ route('evolution.destroy', $photo->id) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity" data-confirm-delete>
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-xl bg-rose-500/80 text-white flex items-center justify-center hover:bg-rose-500 hover:scale-110 transition-all backdrop-blur-md">
                                <i class="fas fa-trash text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="p-12 text-center bg-zinc-900/40 border border-white/5 rounded-[3rem] max-w-2xl mx-auto mt-10 shadow-2xl">
            <i class="fas fa-image text-5xl text-zinc-800 mb-6 drop-shadow-lg block"></i>
            <h4 class="text-xl font-black text-white mb-2">Primeiro Registro</h4>
            <p class="text-zinc-500 text-sm mb-6">Nenhuma foto registrada na galeria. Tire uma foto hoje e comece a acompanhar cada pequeno avanço!</p>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-8 py-4 bg-white text-zinc-950 font-black rounded-2xl hover:scale-105 active:scale-95 transition-all text-xs tracking-widest uppercase">
                Tirar Foto Agora
            </button>
        </div>
    @endforelse

</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 hidden backdrop-blur-xl animate-fade-in">
    <div class="bg-zinc-900 border border-white/10 rounded-[2.5rem] max-w-md w-full overflow-hidden relative shadow-2xl animate-dashboard-entry">
        <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="absolute top-6 right-6 text-zinc-500 hover:text-white transition-colors bg-zinc-800 w-8 h-8 rounded-full flex items-center justify-center">
            <i class="fas fa-times text-xs"></i>
        </button>
        
        <div class="px-8 py-6 border-b border-white/5 bg-zinc-900/50">
            <h2 class="text-xl font-black text-white uppercase tracking-tighter flex items-center gap-3">
                <i class="fas fa-camera text-indigo-500"></i>
                Registrar Foto
            </h2>
        </div>
        
        <form action="{{ route('evolution.store') }}" method="POST" enctype="multipart/form-data" class="px-8 py-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[9px] uppercase font-black text-zinc-500 mb-1.5 tracking-widest pl-1">Data</label>
                        <input type="date" name="registered_date" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white text-sm focus:outline-none focus:border-indigo-500/50 transition-colors" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="block text-[9px] uppercase font-black text-zinc-500 mb-1.5 tracking-widest pl-1">Ângulo</label>
                        <select name="type" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 text-white text-sm focus:outline-none focus:border-indigo-500/50 transition-colors appearance-none">
                            <option value="front">Frente</option>
                            <option value="side">Lado</option>
                            <option value="back">Costas</option>
                            <option value="custom">Outro</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[9px] uppercase font-black text-zinc-500 mb-1.5 tracking-widest pl-1">Peso no Cadastro (Opcional)</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="weight_kg" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-4 pr-12 text-white text-sm focus:outline-none focus:border-indigo-500/50 transition-colors" placeholder="Ex: 80.5">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 font-black text-xs">KG</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[9px] uppercase font-black text-zinc-500 mb-1.5 tracking-widest pl-1">Arquivo da Imagem</label>
                    <input type="file" name="photo" accept="image/*" class="w-full bg-zinc-950 border border-white/10 rounded-2xl p-3 text-zinc-400 text-sm focus:outline-none focus:border-indigo-500/50 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-zinc-800 file:text-white" required>
                </div>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase text-[10px] tracking-widest rounded-2xl transition-all shadow-xl shadow-indigo-600/20 active:scale-95">
                    Adicionar à Galeria
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    body { background-color: #0b0e14; }
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-dashboard-entry { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
