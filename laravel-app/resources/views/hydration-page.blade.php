@extends('layouts.app')

@section('title', 'NexHydra — Controle de Hidratação Inteligente')

@section('content')
    <div class="py-10 space-y-12 animate-fade-in-up max-w-[1700px] mx-auto px-6">
        <!-- Header: NexHydra Strategy -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-zinc-900">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span
                        class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">Equilíbrio
                        Biológico Ativo</span>
                    <span class="text-zinc-700">•</span>
                    <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter">Monitoramento da Osmolaridade em Tempo Real</span>
                </div>
                <h1 class="text-5xl font-black tracking-tight text-white leading-tight uppercase">
                    Nex<span class="text-emerald-500">Hydra</span>
                </h1>
                <p class="text-zinc-500 font-medium max-w-xl">Inteligência aplicada à sua hidratação. Mantenha o equilíbrio
                    celular e maximize sua performance cognitiva e física.</p>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <button onclick="toggleSettingsModal()"
                    class="group px-6 py-3 bg-zinc-900 text-zinc-400 font-black rounded-xl border border-zinc-800 hover:border-emerald-500/30 hover:text-white transition-all flex items-center gap-3 shadow-xl uppercase text-xs tracking-widest">
                    <i data-lucide="settings" class="w-4 h-4 transition-transform group-hover:rotate-90"></i>
                    Configurações
                </button>
            </div>
        </div>

        <!-- Main Hydra Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Tracker Card (Left/Center) -->
            <div class="lg:col-span-12 xl:col-span-5 space-y-10">
                <div
                    class="group relative bg-zinc-900 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-2xl transition-all hover:border-emerald-500/30">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none">
                    </div>

                    <!-- Fluid Animation Chamber -->
                    <div
                        class="relative h-[450px] bg-zinc-950 overflow-hidden flex flex-col items-center justify-center shadow-inner">
                        <!-- Dynamic Wave Background -->
                        <div id="hydration-wave"
                            class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-emerald-600 to-emerald-400 transition-all duration-1000 ease-in-out opacity-20 shadow-[0_0_50px_rgba(16,185,129,0.3)]"
                            style="height: 0%">
                            <div
                                class="absolute top-0 left-0 w-[200%] h-32 bg-emerald-400/20 -translate-y-1/2 animate-wave-slow rounded-[40%]">
                            </div>
                            <div
                                class="absolute top-0 left-0 w-[200%] h-32 bg-emerald-300/10 -translate-y-1/2 animate-wave-fast rounded-[45%]">
                            </div>
                        </div>

                        <!-- HUD Stats -->
                        <div class="relative z-10 text-center space-y-4">
                            <div id="current-percentage"
                                class="text-9xl font-black text-white tracking-tighter tabular-nums drop-shadow-[0_10px_20px_rgba(0,0,0,0.5)]">0%
                            </div>
                            <div id="current-label" class="bg-zinc-900/80 backdrop-blur-md border border-white/5 px-6 py-2 rounded-full text-emerald-400 font-black uppercase tracking-[0.3em] text-[10px] shadow-2xl">
                                0ml / 0ml</div>
                        </div>
                    </div>

                    <!-- Registration Controls -->
                    <div class="p-10 space-y-10 relative z-10">
                        <div class="grid grid-cols-4 gap-4">
                            @foreach([['ml' => 200, 'label' => 'Copo'], ['ml' => 300, 'label' => 'Grande'], ['ml' => 500, 'label' => 'Garrafa'], ['ml' => 1000, 'label' => 'Max']] as $btn)
                                <button onclick="addWater({{ $btn['ml'] }})"
                                    class="group flex flex-col items-center justify-center p-6 bg-zinc-950 border border-zinc-800 rounded-3xl hover:bg-emerald-500 hover:scale-105 transition-all active:scale-95 shadow-xl">
                                    <span class="text-white font-black text-xl group-hover:text-zinc-950 tabular-nums">+{{ $btn['ml'] }}</span>
                                    <span
                                        class="text-[9px] text-zinc-600 font-black uppercase tracking-widest group-hover:text-zinc-950 mt-1">{{ $btn['label'] }}</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="relative">
                            <input type="number" id="custom-amount"
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-[2.5rem] p-6 text-white text-sm font-black focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all placeholder:text-zinc-800 shadow-inner tabular-nums uppercase"
                                placeholder="VOLUME CUSTOMIZADO (ML)...">
                            <button onclick="addWater(document.getElementById('custom-amount').value)"
                                class="absolute right-3 top-3 bottom-3 px-8 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-emerald-400 transition-all active:scale-95 shadow-xl uppercase text-[10px] tracking-widest">
                                REGISTRAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History & Analytics (Right) -->
            <div class="lg:col-span-12 xl:col-span-7 space-y-10">
                <!-- Bento History Card -->
                <div
                    class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] p-10 shadow-2xl space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <h3 class="text-white font-black text-2xl tracking-tight uppercase italic">Linha do Tempo Diária</h3>
                            <p class="text-zinc-600 text-[10px] font-black uppercase tracking-widest">Sincronização Hídrica v3.0</p>
                        </div>
                        <span
                            class="px-4 py-2 bg-zinc-950 border border-zinc-800 rounded-2xl text-emerald-500 text-[10px] font-black uppercase tracking-widest shadow-inner">Hoje,
                            {{ now()->translatedFormat('d M') }}</span>
                    </div>

                    <div id="entries-list" class="space-y-4 max-h-[350px] overflow-y-auto pr-4 custom-scrollbar">
                        <!-- Dynamic Items -->
                        <div
                            class="flex items-center justify-center py-20 text-zinc-800 font-black uppercase text-[10px] tracking-[0.2em] italic animate-pulse">
                            Iniciando sensores hídricos...
                        </div>
                    </div>
                </div>

                <!-- Analytics Chart -->
                <div
                    class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] p-10 shadow-2xl h-[450px]">
                    <div class="flex items-center justify-between mb-8 px-4">
                        <div class="space-y-1">
                            <h3 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em]">Performance de Hidratação (7D)</h3>
                            <p class="text-[9px] text-zinc-700 font-bold uppercase">Análise de Balanço Acumulado</p>
                        </div>
                        <div class="flex items-center gap-3 bg-zinc-950 px-4 py-2 rounded-full border border-zinc-800 shadow-inner">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                            <span class="text-zinc-500 text-[9px] font-black uppercase tracking-widest">Consumo Global</span>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="hydrationChart"></canvas>
                    </div>
                </div>

                <!-- Premium Biometric Status Card -->
                <div id="premium-status-container" class="hidden">
                    <div class="bg-zinc-900 border border-emerald-500/20 rounded-[3.5rem] p-10 shadow-2xl relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all duration-1000"></div>
                        
                        <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                            <div class="relative">
                                <svg class="w-32 h-32 transform -rotate-90 drop-shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                                    <circle cx="64" cy="64" r="60" stroke="currentColor" stroke-width="8" fill="transparent" class="text-zinc-950" />
                                    <circle id="status-progress-circle" cx="64" cy="64" r="60" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="377" stroke-dashoffset="377" class="text-emerald-500 transition-all duration-1000 shadow-lg" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span id="status-percentage" class="text-3xl font-black text-white tabular-nums">0%</span>
                                </div>
                            </div>
                            
                            <div class="flex-1 space-y-4 text-center md:text-left">
                                <div class="flex items-center justify-center md:justify-start gap-3">
                                    <span class="px-3 py-1 bg-emerald-500 text-zinc-950 text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-emerald-500/10">Elite Bio-Status</span>
                                    <span id="status-indicator" class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                </div>
                                <h3 id="status-title" class="text-3xl font-black text-white leading-tight uppercase tracking-tighter">PROCESSANDO...</h3>
                                <p id="status-description" class="text-zinc-500 text-sm font-medium leading-relaxed italic">"Monitorando seu balanço osmótico para otimizar a recuperação celular."</p>
                                
                                <div class="grid grid-cols-2 gap-4 pt-2">
                                    <div class="bg-zinc-950 p-5 rounded-2xl border border-zinc-800 shadow-inner">
                                        <span class="text-[8px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Esperado Agora</span>
                                        <span id="status-expected" class="text-xl font-black text-white tabular-nums">0ml</span>
                                    </div>
                                    <div class="bg-zinc-950 p-5 rounded-2xl border border-zinc-800 shadow-inner">
                                        <span class="text-[8px] text-zinc-600 font-black uppercase tracking-widest block mb-1">Diferença</span>
                                        <span id="status-diff" class="text-xl font-black text-white tabular-nums">0ml</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Free Upsell Card -->
                <div id="free-upsell-container" class="hidden">
                    <div class="bg-zinc-950 border border-zinc-800 rounded-[3.5rem] p-12 text-center space-y-8 shadow-2xl">
                        <div class="w-20 h-20 bg-zinc-900 border border-zinc-800 rounded-3xl flex items-center justify-center mx-auto text-zinc-700 shadow-xl">
                            <i data-lucide="lock" class="w-10 h-10"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Bio-Status Premium</h3>
                            <p class="text-zinc-600 text-sm max-w-sm mx-auto font-medium">Saiba se você está adiantado ou atrasado na sua hidratação com base no seu metabolismo hídrico.</p>
                        </div>
                        <button onclick="window.location.href='{{ route('plano') }}'" class="px-10 py-4 bg-emerald-500 text-zinc-950 font-black rounded-2xl hover:bg-emerald-400 transition-all text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/10">ATIVAR NEXELITE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settingsModal"
        class="fixed inset-0 z-[100] hidden flex items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-xl">
        <div
            class="bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3.5rem] p-10 shadow-3xl animate-fade-in-up">
            <div class="space-y-10">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-8">
                    <div class="space-y-1">
                        <h2 class="text-3xl font-black text-white tracking-tighter uppercase italic">Nex<span class="text-emerald-500">Hydra</span> Config</h2>
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Protocolos de ajuste hídrico</p>
                    </div>
                    <button onclick="toggleSettingsModal()" class="w-12 h-12 bg-zinc-950 border border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-600 hover:text-rose-500 transition-all shadow-xl">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <form id="settings-form" class="space-y-8">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 px-3">Algoritmo de Cálculo</label>
                        <select id="target-mode" onchange="toggleManualTarget()"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white font-black outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all appearance-none cursor-pointer uppercase text-xs tracking-widest">
                            <option value="auto">🔥 INTELIGENTE (BIO-MÉTRICAS)</option>
                            <option value="manual">⚙️ MANUAL (META FIXA)</option>
                        </select>
                    </div>

                    <div id="manual-target-group" class="space-y-4 hidden">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 px-3">Meta Personalizada (ml)</label>
                        <input type="number" id="manual-target"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white font-black outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all tabular-nums uppercase text-sm"
                            step="100" min="500">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 px-3">Ambiente Térmico</label>
                        <select id="climate-setting"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-5 text-white font-black outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all appearance-none cursor-pointer uppercase text-xs tracking-widest">
                            <option value="cold">❄️ FRIO (BAIXO GASTO)</option>
                            <option value="moderate">🍃 MODERADO</option>
                            <option value="hot">☀️ QUENTE (INDUCÃO DE SEDE)</option>
                        </select>
                    </div>

                    <div class="pt-6 grid grid-cols-2 gap-4">
                        <button type="button" onclick="toggleSettingsModal()"
                            class="py-5 bg-zinc-950 border border-zinc-800 text-zinc-600 font-black rounded-2xl hover:bg-zinc-800 transition-all uppercase text-[10px] tracking-widest shadow-xl">DESCARTAR</button>
                        <button type="button" onclick="saveSettings()"
                            class="py-5 bg-emerald-500 text-zinc-950 font-black rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 uppercase text-[10px] tracking-widest">SALVAR AJUSTES</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @endpush

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let currentData = null;

        document.addEventListener('DOMContentLoaded', function () {
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
            waveBox.style.opacity = (0.2 + (perc / 200)).toString(); 

            document.getElementById('current-percentage').textContent = data.percentage + '%';
            document.getElementById('current-label').textContent = `${data.consumed}ml / ${data.target}ml`;

            // Premium Status Logic
            if (data.is_premium) {
                document.getElementById('premium-status-container').classList.remove('hidden');
                document.getElementById('free-upsell-container').classList.add('hidden');
                
                const expected = data.expected_now;
                const actual = data.consumed;
                const diff = actual - expected;
                
                document.getElementById('status-expected').textContent = expected + 'ml';
                document.getElementById('status-diff').textContent = (diff > 0 ? '+' : '') + diff + 'ml';
                document.getElementById('status-diff').className = diff >= 0 ? 'text-xl font-black text-emerald-400 tabular-nums' : 'text-xl font-black text-rose-400 tabular-nums';
                
                const statusPerc = Math.min(100, Math.round((actual / expected) * 100)) || 0;
                document.getElementById('status-percentage').textContent = statusPerc + '%';
                
                const circle = document.getElementById('status-progress-circle');
                const offset = 377 - (377 * (statusPerc / 100));
                circle.style.strokeDashoffset = offset;
                
                const indicator = document.getElementById('status-indicator');
                const title = document.getElementById('status-title');
                const desc = document.getElementById('status-description');
                
                if (data.status === 'ahead') {
                    indicator.className = 'w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.5)] animate-pulse';
                    title.textContent = 'Performance Superior';
                    desc.textContent = 'Você está acima da curva média biológica. Seu corpo está em estado de máxima refrigeração.';
                } else if (data.status === 'on_track') {
                    indicator.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)] animate-pulse';
                    title.textContent = 'Equilíbrio Celular';
                    desc.textContent = 'Sincronização perfeita com o horário do dia. Continue mantendo este ritmo para evitar platôs.';
                } else {
                    indicator.className = 'w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)] animate-pulse';
                    title.textContent = 'DÉBITO HÍDRICO!';
                    desc.textContent = 'Você está atrás da meta prevista para este horário. Beba pelo menos 300ml agora.';
                }
            } else {
                document.getElementById('premium-status-container').classList.add('hidden');
                document.getElementById('free-upsell-container').classList.remove('hidden');
            }

            // Settings form sync
            document.getElementById('target-mode').value = data.is_auto ? 'auto' : 'manual';
            document.getElementById('manual-target').value = data.target;
            toggleManualTarget();

            // Entries list assembly
            const list = document.getElementById('entries-list');
            if (data.entries.length === 0) {
                list.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-800 space-y-6">
                                <i data-lucide="droplet-off" class="w-16 h-16 opacity-10"></i>
                                <p class="font-black uppercase text-[10px] tracking-[0.3em] italic">Déficit hídrico detectado. Inicie o suporte.</p>
                            </div>
                        `;
                lucide.createIcons();
            } else {
                list.innerHTML = data.entries.map(e => `
                            <div class="flex items-center justify-between p-6 bg-zinc-950 border border-zinc-800 rounded-3xl group transition-all hover:border-emerald-500/20 shadow-inner">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-xl">
                                        <i data-lucide="droplets" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-black text-lg tabular-nums uppercase">+${e.amount_ml}ml</div>
                                        <div class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-0.5">${new Date(e.drank_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} • ${e.source || 'MANUAL'}</div>
                                    </div>
                                </div>
                                <button onclick="deleteEntry(${e.id})" class="w-10 h-10 flex items-center justify-center bg-zinc-900 border border-zinc-800 rounded-xl text-zinc-700 hover:text-rose-500 transition-all active:scale-95 shadow-xl opacity-0 group-hover:opacity-100">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        `).join('');
                lucide.createIcons();
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
                    alert('Falha crítica na rede. Verifique sua conexão.');
                });
        }

        function deleteEntry(id) {
            if (typeof window.openNxConfirmDelete !== 'function') {
                if (!confirm('Purgar registo de bio-balanço?')) return;
                deleteEntryDoFetch(id);
                return;
            }
            window.openNxConfirmDelete({
                title: 'PURGAR REGISTRO',
                message: 'Deseja realmente purgar este registro de bio-balanço?',
                primaryLabel: 'PURGAR',
                onConfirm: function () { deleteEntryDoFetch(id); }
            });
        }

        function deleteEntryDoFetch(id) {
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
            if(!modal.classList.contains('hidden')) lucide.createIcons();
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
                    const data = await r.json().catch(() => ({ success: false, message: 'Estrutura JSON inválida.' }));
                    if (r.ok && data.success) {
                        toggleSettingsModal();
                        refreshStatus();
                    } else {
                        alert('Erro: ' + (data.message || 'Falha na resposta.'));
                    }
                })
                .catch(err => {
                    console.error('Core Sync Error:', err);
                    alert('Falha total na sincronização hídrica.');
                });
        }

        function loadChart() {
            fetch('{{ url("/api/hydration/reports") }}?days=7')
                .then(r => r.json())
                .then(data => {
                    const ctx = document.getElementById('hydrationChart').getContext('2d');
                    if (window.myHydrationChart) window.myHydrationChart.destroy();

                    const labels = data.map(d => new Date(d.entry_date).toLocaleDateString('pt-BR', { weekday: 'short' }).toUpperCase());
                    const values = data.map(d => d.total);

                    const emeraldGradient = ctx.createLinearGradient(0, 0, 0, 300);
                    emeraldGradient.addColorStop(0, '#10b981');
                    emeraldGradient.addColorStop(1, '#10b98100');

                    window.myHydrationChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Consumo Global',
                                data: values,
                                backgroundColor: emeraldGradient,
                                borderColor: '#10b981',
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
                                    backgroundColor: '#09090b',
                                    titleFont: { size: 12, weight: '900' },
                                    bodyFont: { size: 10, weight: 'bold' },
                                    padding: 12,
                                    displayColors: false,
                                    borderColor: '#10b98133',
                                    borderWidth: 1
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(255,255,255,0.02)', borderDash: [5, 5] },
                                    ticks: { color: '#3f3f46', font: { size: 9, weight: '900' } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#3f3f46', font: { size: 9, weight: '900' } }
                                }
                            }
                        }
                    });
                });
        }
    </script>

    <style>
        body {
            background-color: #080a0f;
            background-image:
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
            background-attachment: fixed;
        }

        @keyframes wave-slow {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        @keyframes wave-fast {
            from { transform: translateX(-25%); }
            to { transform: translateX(25%); }
        }

        .animate-wave-slow { animation: wave-slow 12s linear infinite; }
        .animate-wave-fast { animation: wave-fast 8s linear infinite; }

        .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.1); border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.2); }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection