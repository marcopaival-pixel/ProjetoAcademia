@extends('layouts.app')

@section('title', 'NexHydra — Controle de Hidratação Intelinte')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header: NexHydra Strategy -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Active Bio-Balance</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Real-Time Osmolarity Tracking</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Nex<span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-400">Hydra</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Inteligência aplicada à sua hidratação. Mantenha o equilíbrio celular e maximize sua performance cognitiva e física.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <button onclick="toggleSettingsModal()" class="group px-6 py-3 bg-zinc-900/60 backdrop-blur-xl text-zinc-400 font-bold rounded-xl border border-white/5 hover:border-blue-500/30 hover:text-white transition-all flex items-center gap-3">
                <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Configurações
            </button>
        </div>
    </div>

    <!-- Main Hydra Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Tracker Card (Left/Center) -->
        <div class="lg:col-span-12 xl:col-span-5 space-y-10">
            <div class="group relative bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-blue-500/30">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent pointer-events-none"></div>
                
                <!-- Fluid Animation Chamber -->
                <div class="relative h-[400px] bg-zinc-950/50 overflow-hidden flex flex-col items-center justify-center">
                    <!-- Dynamic Wave Background -->
                    <div id="hydration-wave" class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-blue-600 to-blue-400 transition-all duration-1000 ease-in-out opacity-20" style="height: 0%">
                        <div class="absolute top-0 left-0 w-[200%] h-20 bg-blue-400/20 -translate-y-1/2 animate-[wave_6s_linear_infinite] rounded-[40%]"></div>
                        <div class="absolute top-0 left-0 w-[200%] h-20 bg-blue-300/10 -translate-y-1/2 animate-[wave_8s_linear_infinite_reverse] rounded-[45%]"></div>
                    </div>

                    <!-- HUD Stats -->
                    <div class="relative z-10 text-center space-y-2">
                        <div id="current-percentage" class="text-8xl font-black text-white tracking-tighter tabular-nums drop-shadow-2xl">0%</div>
                        <div id="current-label" class="text-blue-400 font-black uppercase tracking-[0.3em] text-[10px]">0ml / 0ml</div>
                    </div>

                    <!-- Water Bottle Silhouette Overlay (Optional High-End Detail) -->
                    <div class="absolute inset-x-0 bottom-0 top-1/4 pointer-events-none opacity-5 border-x-[40px] border-zinc-900 rounded-t-full"></div>
                </div>

                <!-- Registration Controls -->
                <div class="p-10 space-y-10 relative z-10">
                    <div class="grid grid-cols-4 gap-4">
                        @foreach([['ml' => 200, 'label' => 'Copo'], ['ml' => 300, 'label' => 'Grande'], ['ml' => 500, 'label' => 'Garrafa'], ['ml' => 1000, 'label' => 'Max']] as $btn)
                        <button onclick="addWater({{ $btn['ml'] }})" class="group flex flex-col items-center justify-center p-4 bg-zinc-950/50 border border-white/5 rounded-3xl hover:bg-blue-600 hover:scale-105 transition-all active:scale-95 shadow-lg">
                            <span class="text-white font-black text-lg">+{{ $btn['ml'] }}</span>
                            <span class="text-[8px] text-zinc-500 font-black uppercase tracking-widest group-hover:text-white">{{ $btn['label'] }}</span>
                        </button>
                        @endforeach
                    </div>

                    <div class="relative">
                        <input type="number" id="custom-amount" class="w-full bg-zinc-950/50 border border-white/5 rounded-[2.5rem] p-6 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all placeholder:text-zinc-700 shadow-inner" placeholder="Volume customizado (ml)...">
                        <button onclick="addWater(document.getElementById('custom-amount').value)" class="absolute right-3 top-3 bottom-3 px-6 bg-white text-zinc-900 font-black rounded-3xl hover:bg-blue-400 hover:text-white transition-all active:scale-95 shadow-lg uppercase text-[10px] tracking-widest">
                            Log
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- History & Analytics (Right) -->
        <div class="lg:col-span-12 xl:col-span-7 space-y-10">
            <!-- Bento History Card -->
            <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[3.5rem] p-10 shadow-2xl space-y-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-black text-2xl tracking-tight">Timeline Diária</h3>
                        <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mt-1">Registros Automáticos & Manuais</p>
                    </div>
                    <span class="px-4 py-2 bg-white/5 rounded-2xl text-blue-400 text-[10px] font-black uppercase tracking-widest border border-white/10">Hoje, {{ now()->format('d M') }}</span>
                </div>

                <div id="entries-list" class="space-y-4 max-h-[300px] overflow-y-auto pr-4 custom-scrollbar">
                    <!-- Dynamic Items -->
                    <div class="flex items-center justify-center py-20 text-zinc-600 font-black uppercase text-[10px] tracking-[0.2em] italic">
                        Iniciando sistemas de monitoramento...
                    </div>
                </div>
            </div>

            <!-- Analytics Chart -->
            <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[3.5rem] p-10 shadow-2xl h-[400px]">
                <div class="flex items-center justify-between mb-8 px-4">
                    <h3 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em]">Performance Hydration Analytics (7D)</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                        <span class="text-zinc-500 text-[10px] font-black uppercase">Consumo Global</span>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="hydrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal: Cyber Style -->
<div id="settingsModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-6 bg-black/80 backdrop-blur-md">
    <div class="bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[3rem] p-10 shadow-3xl animate-dashboard-entry">
        <div class="space-y-10">
            <div class="flex items-center justify-between border-b border-white/5 pb-6">
                <h2 class="text-3xl font-black text-white tracking-tight">NexHydra <span class="text-blue-500">Config</span></h2>
                <button onclick="toggleSettingsModal()" class="text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"></path></svg>
                </button>
            </div>

            <form id="settings-form" class="space-y-8">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Modo de Cálculo</label>
                    <select id="target-mode" onchange="toggleManualTarget()" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-blue-500/30 transition-all appearance-none cursor-pointer">
                        <option value="auto">🔥 Inteligente (Baseado em Bio-Métricas)</option>
                        <option value="manual">⚙️ Manual (Meta Fixa)</option>
                    </select>
                </div>

                <div id="manual-target-group" class="space-y-4 hidden">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Meta Personalizada (ml)</label>
                    <input type="number" id="manual-target" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" step="100" min="500">
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2">Ambiente Térmico</label>
                    <select id="climate-setting" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white font-bold outline-none focus:ring-2 focus:ring-blue-500/30 transition-all appearance-none cursor-pointer">
                        <option value="cold">❄️ Frio (Baixo Gasto Hídrico)</option>
                        <option value="moderate">🍃 Moderado</option>
                        <option value="hot">☀️ Quente (Indução de Sede)</option>
                    </select>
                </div>

                <div class="pt-6 grid grid-cols-2 gap-4">
                    <button type="button" onclick="toggleSettingsModal()" class="py-4 bg-zinc-800 text-zinc-400 font-black rounded-2xl hover:bg-zinc-700 transition-all uppercase text-[10px] tracking-widest">Descartar</button>
                    <button type="button" onclick="saveSettings()" class="py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-400 transition-all shadow-xl uppercase text-[10px] tracking-widest">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes wave {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59,130,246,0.2); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let currentData = null;

    document.addEventListener('DOMContentLoaded', function() {
        refreshStatus();
        loadChart();
    });

    function refreshStatus() {
        fetch('{{ url("/api/hydration/status") }}')
            .then(r => r.json())
            .then(data => {
                currentData = data;
                updateUI(data);
            });
    }

    function updateUI(data) {
        const perc = Math.min(data.percentage, 100);
        const waveBox = document.getElementById('hydration-wave');
        waveBox.style.height = perc + '%';
        waveBox.style.opacity = (0.2 + (perc / 200)).toString(); // Dynamic opacity based on level

        document.getElementById('current-percentage').textContent = data.percentage + '%';
        document.getElementById('current-label').textContent = `${data.consumed}ml / ${data.target}ml`;
        
        // Settings form sync
        document.getElementById('target-mode').value = data.is_auto ? 'auto' : 'manual';
        document.getElementById('manual-target').value = data.target;
        toggleManualTarget();
        
        // Entries list assembly
        const list = document.getElementById('entries-list');
        if (data.entries.length === 0) {
            list.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 text-zinc-600 space-y-4">
                    <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-black uppercase text-[10px] tracking-[0.2em] italic">Zero hídrico hoje. Inicie o suporte.</p>
                </div>
            `;
        } else {
            list.innerHTML = data.entries.map(e => `
                <div class="flex items-center justify-between p-6 bg-zinc-950/40 border border-white/5 rounded-3xl group transition-all hover:bg-zinc-800">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-400 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-16 0m16 0v10l-8 4-8-4V7m16 0l-8 4-8-4"></path></svg>
                        </div>
                        <div>
                            <div class="text-white font-black text-base">+${e.amount_ml}ml</div>
                            <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest">${new Date(e.drank_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} • ${e.source || 'Manual'}</div>
                        </div>
                    </div>
                    <button onclick="deleteEntry(${e.id})" class="p-3 text-zinc-700 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `).join('');
        }
    }

    function addWater(amount) {
        if (!amount || amount <= 0) return;
        fetch('{{ url("/api/hydration/add") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ amount_ml: amount })
        })
        .then(async r => {
            const data = await r.json();
            if (r.ok && data.success) {
                refreshStatus();
                loadChart();
                document.getElementById('custom-amount').value = '';
            } else {
                alert('Erro de Sincronização: ' + (data.message || 'Falha no servidor.'));
            }
        })
        .catch(err => {
            console.error('Core Trace Error:', err);
            alert('Falha crítica na rede. Verifique sua conexão com o NexShape Arena.');
        });
    }

    function deleteEntry(id) {
        if (!confirm('Purgar registro de bio-balanço?')) return;
        fetch('{{ url("/api/hydration/entry") }}/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(() => {
            refreshStatus();
            loadChart();
        });
    }

    function toggleSettingsModal() {
        const modal = document.getElementById('settingsModal');
        modal.classList.toggle('hidden');
    }

    function toggleManualTarget() {
        const mode = document.getElementById('target-mode').value;
        document.getElementById('manual-target-group').style.display = (mode === 'manual' ? 'block' : 'none');
    }

    function saveSettings() {
        const mode = document.getElementById('target-mode').value;
        const target = document.getElementById('manual-target').value;
        const climate = document.getElementById('climate-setting').value;

        fetch('{{ url("/api/hydration/settings") }}', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                is_water_target_auto: (mode === 'auto'),
                water_target_ml: target,
                climate: climate
            })
        })
        .then(async r => {
            const data = await r.json().catch(() => ({ success: false, message: 'Estrutura JSON inválida no servidor.' }));
            if (r.ok && data.success) {
                toggleSettingsModal();
                refreshStatus();
            } else {
                alert('Erro de Configuração: ' + (data.message || 'Falha na resposta do servidor.'));
            }
        })
        .catch(err => {
            console.error('Core Sync Error:', err);
            alert('Falha total na sincronização hídrica. Verifique se o servidor XAMPP está ativo.');
        });
    }

    function loadChart() {
        fetch('{{ url("/api/hydration/reports") }}?days=7')
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('hydrationChart').getContext('2d');
                if (window.myHydrationChart) window.myHydrationChart.destroy();
                
                const labels = data.map(d => new Date(d.entry_date).toLocaleDateString('pt-BR', {weekday: 'short'}));
                const values = data.map(d => d.total);

                const blueGradient = ctx.createLinearGradient(0, 0, 0, 300);
                blueGradient.addColorStop(0, '#3b82f6');
                blueGradient.addColorStop(1, '#3b82f600');

                window.myHydrationChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Consumo Global',
                            data: values,
                            backgroundColor: blueGradient,
                            borderColor: '#3b82f6',
                            borderWidth: 2,
                            borderRadius: 12,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#18181b',
                                titleFont: { size: 12, weight: '900' },
                                bodyFont: { size: 10, weight: 'bold' },
                                padding: 12,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: 'rgba(255,255,255,0.03)', borderDash: [5, 5] },
                                ticks: { color: '#52525b', font: { size: 9, weight: 'bold' } }
                            },
                            x: { 
                                grid: { display: false },
                                ticks: { color: '#52525b', font: { size: 9, weight: 'bold' } }
                            }
                        }
                    }
                });
            });
    }
</script>
@endsection
