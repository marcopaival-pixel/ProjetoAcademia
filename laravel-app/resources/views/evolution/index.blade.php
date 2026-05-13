@extends('layouts.app')

@section('title', 'Hub de Evolução — NexShape')

@section('style')
<style>
    :root {
        --brand-primary: #3b82f6;
        --brand-accent: #10b981;
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
    }

    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6 relative z-10 pb-32">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-inner shadow-emerald-500/5">Performance Hub</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter">Sua Jornada Visual</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight uppercase">Hub de <span class="text-emerald-500">Evolução</span></h1>
            <p class="text-zinc-500 font-medium">Acompanhe sua transformação física de forma soberana e inteligente.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <button {{ !$isPremium ? 'data-premium-locked' : 'onclick=generateShareCard()' }} class="w-14 h-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-xl" title="Gerar Card de Evolução">
                <i data-lucide="share-2" class="w-6 h-6"></i>
            </button>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-8 py-5 bg-emerald-500 text-zinc-950 font-black text-xs rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 flex items-center gap-3 uppercase tracking-widest">
                <i data-lucide="camera" class="w-4 h-4"></i>
                Lançar Registro
            </button>
        </div>
    </div>

    <!-- Health Score & Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Health Score -->
        <div class="glass-card rounded-[3rem] p-8 flex items-center justify-between relative group overflow-hidden">
            <div class="absolute -right-4 -top-4 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-all duration-700"></div>
            <div class="space-y-1 relative z-10">
                <span class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em]">Health Score Global</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-5xl font-black text-white italic tracking-tighter">{{ $healthScore ?? 0 }}</span>
                    <span class="text-sm font-bold text-zinc-500">/100</span>
                </div>
            </div>
            <div class="w-24 h-24 relative flex items-center justify-center z-10">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" 
                        class="text-emerald-500" 
                        stroke-dasharray="251.2" 
                        stroke-dashoffset="{{ 251.2 * (1 - ($healthScore ?? 0) / 100) }}" 
                        stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i data-lucide="zap" class="w-6 h-6 text-emerald-500/50 animate-pulse"></i>
                </div>
            </div>
        </div>

        <!-- Latest Assessment Summary -->
        <div class="lg:col-span-2 glass-card rounded-[3rem] p-8 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="space-y-1">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Peso Atual</span>
                <div class="text-2xl font-black text-white italic tracking-tighter">
                    {{ number_format($latestAssessment->weight_kg ?? 0, 1) }}<small class="text-[10px] not-italic ml-1 text-zinc-600">kg</small>
                </div>
            </div>
            <div class="space-y-1">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Gordura (BF)</span>
                <div class="text-2xl font-black text-emerald-400 italic tracking-tighter">
                    {{ $latestAssessment->bf_percent ?? '--' }}<small class="text-[10px] not-italic ml-1 text-zinc-600">%</small>
                </div>
            </div>
            <div class="space-y-1">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Massa Magra</span>
                <div class="text-2xl font-black text-blue-400 italic tracking-tighter">
                    {{ $latestAssessment->lean_mass ?? '--' }}<small class="text-[10px] not-italic ml-1 text-zinc-600">kg</small>
                </div>
            </div>
            <div class="space-y-1">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Última Avaliação</span>
                <div class="text-xs font-black text-white uppercase tracking-tighter">
                    {{ $latestAssessment ? $latestAssessment->assessment_date->format('d/m/Y') : 'Pendente' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Gallery -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-12">
        <!-- Main Content (Gallery) -->
        <div class="xl:col-span-3 space-y-12">
            <!-- Visual Transformation (Premium Pair) -->
            @if(count($evolutionPhotos) > 0)
            <div class="space-y-6 relative">
                @if(!$isPremium)
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center p-8 text-center bg-zinc-950/60 backdrop-blur-md rounded-[2.5rem] border border-amber-500/20">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/20 text-amber-500 flex items-center justify-center border border-amber-500/30 mb-4 shadow-lg">
                        <i data-lucide="lock" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-white font-black uppercase tracking-widest text-sm">Comparativo Premium</h4>
                    <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-2 max-w-[250px]">Libere o comparativo automático de evolução visual sendo membro Premium.</p>
                    <button data-premium-locked class="mt-4 px-6 py-2 bg-amber-500 text-zinc-950 font-black text-[10px] rounded-xl uppercase tracking-widest">Upgrade Agora</button>
                </div>
                @endif

                <div class="flex items-center justify-between px-2 {{ !$isPremium ? 'blur-sm' : '' }}">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-zinc-500">Destaque de Transformação</h2>
                    <span class="text-[9px] font-bold text-blue-400 bg-blue-500/10 px-3 py-1 rounded-full uppercase tracking-widest">Comparativo Automático</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 {{ !$isPremium ? 'blur-sm select-none pointer-events-none' : '' }}">
                    @foreach($evolutionPhotos as $type => $pair)
                    <div class="glass-card rounded-[2.5rem] overflow-hidden p-4 space-y-4 hover:border-emerald-500/20 transition-all">
                        <div class="flex items-center justify-between px-2">
                            <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Vista {{ ucfirst($type == 'front' ? 'Frontal' : ($type == 'side' ? 'Lateral' : 'Posterior')) }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 relative">
                            <div class="relative rounded-2xl overflow-hidden aspect-[3/4] bg-zinc-900">
                                <img src="{{ asset('storage/' . $pair['first']->photo_path) }}" class="w-full h-full object-cover grayscale opacity-60" alt="Antes">
                            </div>
                            <div class="relative rounded-2xl overflow-hidden aspect-[3/4] bg-zinc-900 border border-emerald-500/30">
                                <img src="{{ asset('storage/' . $pair['last']->photo_path) }}" class="w-full h-full object-cover" alt="Depois">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Full Gallery Grid -->
            @forelse($photos as $month => $monthPhotos)
                <div class="space-y-6">
                    <div class="flex items-center gap-4 pl-4">
                        <div class="w-1.5 h-6 bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                        <h3 class="text-sm font-black text-white uppercase tracking-[0.3em] italic">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</h3>
                        <div class="h-[1px] flex-1 bg-zinc-900"></div>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @foreach($monthPhotos as $photo)
                            <div class="relative group bg-zinc-950 border border-zinc-800 rounded-[2rem] overflow-hidden shadow-2xl transition-all hover:border-emerald-500/30">
                                <div class="aspect-[3/4] relative overflow-hidden">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto de Evolução" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                    <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent flex flex-col justify-end p-6 opacity-90 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white font-black text-sm tracking-tighter tabular-nums">{{ \Carbon\Carbon::parse($photo->registered_date)->format('d/m/Y') }}</span>
                                        @if($photo->weight_kg)
                                            <span class="text-emerald-400 text-[10px] font-black uppercase tracking-widest tabular-nums mt-1">{{ $photo->weight_kg }} kg</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <form action="{{ route('evolution.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Purgar registo visual?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-zinc-950/90 border border-zinc-800 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all backdrop-blur-xl">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-20 text-center bg-zinc-900/50 border border-zinc-800 rounded-[3rem] max-w-2xl mx-auto mt-20">
                    <div class="w-20 h-20 bg-zinc-950 border border-zinc-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="image" class="w-8 h-8 text-zinc-800"></i>
                    </div>
                    <h4 class="text-xl font-black text-white mb-4 uppercase tracking-tighter">Sem Registros</h4>
                    <p class="text-zinc-500 text-sm mb-8 font-medium">Você ainda não registrou fotos de evolução. Comece hoje para acompanhar sua transformação!</p>
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="px-8 py-4 bg-emerald-500 text-zinc-950 font-black rounded-2xl hover:bg-emerald-400 transition-all text-xs tracking-widest uppercase shadow-xl shadow-emerald-500/10">
                        Tirar Foto Agora
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Sidebar (Trends and History) -->
        <div class="space-y-12">
            <!-- Trend Chart -->
            @if(count($chartData['dates'] ?? []) > 1)
            <div class="glass-card rounded-[3rem] p-8 space-y-6 relative overflow-hidden">
                @if(!$isPremium)
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center p-4 text-center bg-zinc-950/60 backdrop-blur-sm border border-amber-500/10">
                    <i data-lucide="lock" class="w-6 h-6 text-amber-500 mb-2"></i>
                    <p class="text-[8px] text-zinc-400 font-black uppercase tracking-widest">Gráficos Premium</p>
                </div>
                @endif
                <div class="flex items-center justify-between {{ !$isPremium ? 'blur-[2px]' : '' }}">
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Tendência de Peso</h2>
                    <i data-lucide="trending-up" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div id="evolutionChart" class="w-full h-48 {{ !$isPremium ? 'blur-sm' : '' }}"></div>
            </div>
            @endif

            <!-- Assessment History -->
            <div class="space-y-6 relative overflow-hidden">
                @if(!$isPremium)
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-end p-8 text-center bg-gradient-to-t from-zinc-950 via-zinc-950/80 to-transparent">
                    <button data-premium-locked class="px-6 py-3 bg-zinc-900 border border-zinc-800 text-zinc-400 text-[9px] font-black rounded-2xl uppercase tracking-widest hover:text-white transition-all">Ver Histórico Completo</button>
                </div>
                @endif
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600 px-2">Histórico Técnico</h2>
                @foreach(($assessments ?? []) as $index => $assessment)
                @if($isPremium || $index == 0)
                <div class="glass-card rounded-[2rem] p-5 space-y-3 hover:border-white/10 transition-all {{ !$isPremium && $index > 0 ? 'blur-sm opacity-30 select-none pointer-events-none' : '' }}">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-black text-white uppercase">{{ $assessment->assessment_date->format('d/m/y') }}</span>
                        <span class="text-[9px] font-bold text-emerald-500">{{ number_format($assessment->weight_kg, 1) }} kg</span>
                    </div>
                    <div class="h-[1px] bg-white/5"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-[8px] font-bold text-zinc-500 uppercase">BF%</span>
                        <span class="text-[9px] font-black text-blue-400">{{ $assessment->bf_percent }}%</span>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Share Card Modal (Portal Edition) -->
<div id="shareModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-black/90 backdrop-blur-md">
    <div class="w-full max-w-sm space-y-6">
        <div id="captureCard" class="bg-[#06080c] w-full aspect-[9/16] rounded-[3rem] p-8 border border-white/10 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 via-transparent to-emerald-600/20"></div>
            <div class="relative z-10 space-y-6 text-center pt-4">
                <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-500">NexShape Performance</h2>
                <div class="space-y-1">
                    <h1 class="text-2xl font-black text-white italic tracking-tighter">{{ $user->name }}</h1>
                    <p class="text-[8px] font-bold text-emerald-400 uppercase tracking-widest">Evolução Soberana</p>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="glass-card p-4 rounded-3xl">
                        <span class="text-[8px] font-black uppercase text-zinc-500">Peso</span>
                        <div class="text-xl font-black text-white italic">{{ number_format($latestAssessment->weight_kg ?? 0, 1) }}<small class="text-[9px] ml-0.5">kg</small></div>
                    </div>
                    <div class="glass-card p-4 rounded-3xl border-emerald-500/30">
                        <span class="text-[8px] font-black uppercase text-zinc-500">BF%</span>
                        <div class="text-xl font-black text-emerald-400 italic">{{ $latestAssessment->bf_percent ?? '--' }}%</div>
                    </div>
                </div>
                @if(count($evolutionPhotos) > 0)
                @php($pair = reset($evolutionPhotos))
                <div class="grid grid-cols-2 gap-2 pt-4">
                    <div class="rounded-2xl overflow-hidden aspect-square border border-white/5 relative">
                        <img src="{{ asset('storage/' . $pair['first']->photo_path) }}" class="w-full h-full object-cover grayscale opacity-50">
                        <div class="absolute bottom-1 left-2 text-[6px] font-black uppercase text-zinc-500">Antes</div>
                    </div>
                    <div class="rounded-2xl overflow-hidden aspect-square border border-emerald-500/30 relative">
                        <img src="{{ asset('storage/' . $pair['last']->photo_path) }}" class="w-full h-full object-cover">
                        <div class="absolute bottom-1 right-2 text-[6px] font-black uppercase text-emerald-400">Depois</div>
                    </div>
                </div>
                @endif
                <div class="pt-8">
                    <div class="px-6 py-3 bg-white/5 rounded-full border border-white/10 inline-flex items-center gap-2">
                        <span class="text-[8px] font-black text-zinc-500 uppercase">Health Score</span>
                        <span class="text-xs font-black text-blue-400">{{ $healthScore ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="relative z-10 text-center pb-4 opacity-30 text-[7px] font-black uppercase tracking-[0.4em]">NexShape Performance Elite</div>
        </div>
        <div class="flex gap-4">
            <button onclick="downloadCard()" class="flex-1 py-5 bg-emerald-500 text-zinc-950 font-black rounded-2xl hover:bg-emerald-400 transition-all flex items-center justify-center gap-3 text-xs tracking-widest uppercase">
                <i data-lucide="download" class="w-4 h-4"></i>
                Baixar Card
            </button>
            <button onclick="closeShareModal()" class="w-16 h-16 bg-white/10 text-white rounded-2xl flex items-center justify-center hover:bg-white/20">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-zinc-950/95 backdrop-blur-xl hidden animate-fade-in">
    <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] max-w-md w-full overflow-hidden relative shadow-3xl animate-fade-in-up">
        <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="absolute top-8 right-8 text-zinc-600 hover:text-rose-500 transition-all bg-zinc-950 border border-zinc-800 w-12 h-12 rounded-2xl flex items-center justify-center">
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
                        <input type="date" name="registered_date" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white text-xs font-black focus:border-emerald-500/50 transition-colors shadow-inner uppercase" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[9px] uppercase font-black text-zinc-500 tracking-widest ml-2">Ângulo/Tipo</label>
                        <select name="type" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white text-xs font-black focus:border-emerald-500/50 transition-colors appearance-none shadow-inner uppercase tracking-widest cursor-pointer">
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
                        <input type="number" step="0.1" name="weight_kg" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 pr-14 text-white text-sm font-black focus:border-emerald-500/50 transition-colors shadow-inner tabular-nums" placeholder="EX: 82.5">
                        <span class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-700 font-black text-[10px] tracking-widest">KG</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[9px] uppercase font-black text-zinc-500 tracking-widest ml-2">Arquivo de Mídia</label>
                    <input type="file" name="photo" accept="image/*" class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-4 text-zinc-600 text-[10px] font-black focus:border-emerald-500/50 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:bg-zinc-800 file:text-emerald-500 file:uppercase file:tracking-widest cursor-pointer" required>
                </div>
            </div>
            <div class="pt-6">
                <button type="submit" class="w-full py-6 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black uppercase text-[10px] tracking-[0.3em] rounded-3xl transition-all shadow-2xl active:scale-95">
                    REGISTRAR AGORA
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    function generateShareCard() {
        document.getElementById('shareModal').classList.remove('hidden');
        document.getElementById('shareModal').classList.add('flex');
    }
    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
        document.getElementById('shareModal').classList.remove('flex');
    }
    function downloadCard() {
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GERANDO...';
        btn.disabled = true;
        const card = document.getElementById('captureCard');
        html2canvas(card, {
            useCORS: true,
            scale: 3,
            backgroundColor: '#06080c'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `evolucao-nexshape.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        @if(count($chartData['dates'] ?? []) > 1)
        const options = {
            series: [{ name: 'Peso (kg)', data: {!! json_encode($chartData['weight']) !!} }],
            chart: { height: 200, type: 'area', toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent', foreColor: '#52525b' },
            colors: ['#10b981'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: {!! json_encode($chartData['dates']) !!}, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '9px', fontWeight: 900 } } },
            yaxis: { show: false },
            grid: { show: true, borderColor: 'rgba(255, 255, 255, 0.03)', xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            legend: { show: false },
            tooltip: { theme: 'dark' }
        };
        new ApexCharts(document.querySelector("#evolutionChart"), options).render();
        @endif
    });
</script>
@endpush
@endsection
