@extends('layouts.app', ['navCurrent' => 'body-analysis'])

@php
    $isPaciente = session('active_role') === 'paciente' || (!session('active_role') && auth()->user()->hasRole('paciente') && !auth()->user()->hasRole('aluno'));
@endphp

@section('title', $isPaciente ? 'Evolução Corporal' : 'Cyber-Fit Body Intelligence')

@section('content')
<div class="cyber-fit-container animate-fade-up">
    <header class="cyber-fit-header">
        @if($isPaciente)
            <h1>Evolução <span class="accent-text">Corporal</span></h1>
            <p class="lead">Acompanhe as análises, fotos e relatórios liberados pelo seu profissional.</p>
        @else
            <h1>Cyber-Fit <span class="accent-text">Intelligence</span></h1>
            <p class="lead">Analise postural por foto e rastreio de evolucao corporal assistido por IA.</p>
        @endif
    </header>

    <div class="cyber-layout">
        <!-- Esquerda: Visualizador Anatomico -->
        <div class="card glass cyber-viewer">
            <div class="viewer-tabs">
                <button class="tab-btn active" data-view="front" onclick="setView('front')">Vista Frontal</button>
                <button class="tab-btn" data-view="back" onclick="setView('back')">Vista Posterior</button>
                <button class="tab-btn" data-view="side" onclick="setView('side')">Vista Lateral</button>
            </div>
            
            <div class="anatomical-display">
                <div class="hud-overlay" id="hudOverlay">
                    <div class="hud-item hud-top-left">
                        <span class="hud-label">Simetria Bilateral</span>
                        <span class="hud-value" id="symmetryValue">--%</span>
                    </div>
                    <div class="hud-item hud-top-right">
                        <span class="hud-label">Postura</span>
                        <span class="hud-value" id="postureValue">Analise...</span>
                    </div>
                </div>

                <div class="analysis-canvas-container" id="canvasContainer">
                    <canvas id="analysisCanvas" class="body-canvas"></canvas>
                    @if(!$isPaciente)
                    <div class="upload-overlay" id="uploadOverlay">
                        <input type="file" id="bodyPhotoInput" accept="image/*" hidden>
                        <button class="btn btn-primary btn-lg" onclick="document.getElementById('bodyPhotoInput').click()">
                            <i class="fas fa-camera me-2"></i>Enviar Foto para Analise
                        </button>
                        <p class="mt-2 text-muted small">Os pontos corporais sao processados localmente no navegador.</p>
                    </div>
                    @else
                    <div class="upload-overlay" id="uploadOverlay">
                        <p class="text-white text-center font-bold px-6">
                            Selecione uma análise no histórico lateral para visualizar a evolução das suas fotos clínicas enviadas pelo seu profissional.
                        </p>
                    </div>
                    @endif
                </div>

                <!-- SVG original como fallback ou guia -->
                <div class="avatar-svg-container d-none" id="bodyView">
                    <!-- SVG stuff here if needed -->
                </div>
            </div>

            <div class="viewer-legend">
                <span class="legend-item"><span class="dot target"></span> Pontos Detectados</span>
                <span class="legend-item"><span class="dot focus"></span> Vetores de Forca</span>
            </div>
        </div>

        <!-- Direita: Metricas e Controles -->
        <aside class="cyber-metrics">
            <div class="card glass metrics-card">
                <h3>Ultimas Analises</h3>
                <div class="history-list mt-3">
                    @foreach($history as $index => $item)
                        @php
                            $historyTitle = $index === ($history->count() - 1) ? 'Avaliação Inicial' : ($history->count() - 1 - $index) . 'ª Reavaliação';
                        @endphp
                        <div class="history-item glass p-2 mb-2 rounded d-flex align-items-center gap-2" data-analysis-id="{{ $item->id }}" data-shared="{{ json_encode($item->shared_options ?? new \stdClass) }}">
                            <img src="{{ route('body-analysis.photo', $item->id) }}" class="rounded" width="40" height="40" style="object-fit:cover">
                            <div class="flex-grow-1">
                                <div class="small fw-bold">{{ $historyTitle }} ({{ $item->created_at->format('d/m/Y') }})</div>
                                <div class="text-info" style="font-size:0.7rem">{{ $item->view_type }}</div>
                            </div>
                            <button class="btn btn-sm btn-ghost" onclick="loadAnalysis({{ $item->id }}, this.closest('.history-item'))"><i class="fas fa-eye"></i></button>
                        </div>
                    @endforeach
                    @if($history->isEmpty())
                        <p class="muted text-center small py-3">Nenhum historico.</p>
                    @endif
                </div>
                <button class="btn btn-outline-info w-full mt-3" id="btnCompare" onclick="compareMode()">Comparar Fotos</button>
            </div>

            <div class="card glass ai-insights" id="aiInsightsCard">
                @if($isPaciente)
                    <h3>Detalhes da Avaliação</h3>
                    <p class="muted" id="aiSummaryText" style="font-size: 0.9rem;">
                        Selecione uma análise para visualizar.
                    </p>
                @else
                    <h3>IA Body Analysis</h3>
                    <p class="muted" id="aiSummaryText" style="font-size: 0.9rem;">
                        Faca upload de uma foto para que a IA identifique sua postura, simetria e pontos de desenvolvimento.
                    </p>
                @endif
                <div id="analysisDetails" class="mt-3 d-none">
                    <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2" id="attentionPointsTitle">Pontos de atencao</div>
                    <ul id="attentionPointsList" class="space-y-1 text-xs text-zinc-300"></ul>
                    @if(!$isPaciente)
                    <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest mt-4 mb-2">Limites da leitura</div>
                    <ul id="limitationsList" class="space-y-1 text-xs text-zinc-400"></ul>
                    @endif
                </div>
                <div class="actions-inline d-none" id="analysisActions">
                    @if(!$isPaciente)
                    <button class="btn btn-sm btn-outline-info mt-3 w-full" onclick="showSuggestions()"><i class="fas fa-magic me-2"></i>Ver treino e recuperacao</button>
                    <button class="btn btn-sm btn-outline-secondary mt-2 w-full" onclick="openShareModal()"><i class="fas fa-share-alt me-2"></i>Opções de Compartilhamento</button>
                    @else
                    <button class="btn btn-sm btn-outline-info mt-3 w-full d-none" id="btnViewRecommendations" onclick="showSuggestions()"><i class="fas fa-star me-2"></i>Ver Recomendações do Profissional</button>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Modal de Sugestoes Tecnologico -->
<div id="aiSuggestionsModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-zinc-950/80 backdrop-blur-sm px-4" style="display: none;">
    <div class="bg-zinc-900 border border-white/10 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-blue-500"></div>
        
        <div class="p-6 md:p-8 flex items-center justify-between border-b border-white/5">
            <div>
                <h3 class="text-2xl font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-brain text-emerald-500"></i> Protocolo Inteligente
                </h3>
                <p class="text-xs text-zinc-400 mt-1 uppercase tracking-widest">Baseado na sua ultima analise anatomica</p>
            </div>
            <button onclick="document.getElementById('aiSuggestionsModal').style.display = 'none'" class="text-zinc-500 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 bg-zinc-950/30">
            <!-- Treino Sugerido -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400">
                        <i class="fas fa-dumbbell text-lg"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white uppercase tracking-widest">Treino Recomendado</h4>
                </div>
                <div class="p-5 rounded-xl bg-zinc-900/50 border border-white/5">
                    <p id="modalWorkoutText" class="text-sm text-zinc-300 leading-relaxed mb-4"></p>
                    <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2">Exercicios Foco:</div>
    .cyber-layout { display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; }
    
    @media (max-width: 900px) { .cyber-layout { grid-template-columns: 1fr; } }
    
    .cyber-viewer { height: 620px; display: flex; flex-direction: column; padding: 0 !important; overflow: hidden; position: relative; }
    .viewer-tabs { display: flex; padding: 1rem; gap: 0.5rem; background: rgba(0,0,0,0.2); }
    .tab-btn { background: transparent; border: 1px solid var(--border); color: var(--muted); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
    .tab-btn.active { background: var(--accent); color: white; border-color: var(--accent); }
    
    .anatomical-display { flex: 1; position: relative; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at center, rgba(61, 156, 245, 0.1) 0%, transparent 70%); overflow: hidden; }
    .hud-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; padding: 1.5rem; z-index: 10; }
    .hud-item { position: absolute; background: rgba(0,0,0,0.6); border-left: 3px solid var(--accent); padding: 0.5rem 1rem; backdrop-filter: blur(8px); border-radius: 0 4px 4px 0; }
    .hud-top-left { top: 1.5rem; left: 1.5rem; }
    .hud-top-right { top: 1.5rem; right: 1.5rem; }
    .hud-label { display: block; font-size: 0.7rem; text-transform: uppercase; color: var(--muted); letter-spacing: 0.05em; }
    .hud-value { font-weight: 700; font-family: 'Outfit', sans-serif; font-size: 1.2rem; }

    .analysis-canvas-container { width: 100%; height: 100%; position: relative; display: flex; align-items: center; justify-content: center; }
    .body-canvas { max-width: 100%; max-height: 100%; object-fit: contain; }
    .upload-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); transition: opacity 0.3s; z-index: 5; }
    .upload-overlay.hidden { opacity: 0; pointer-events: none; }

    .viewer-legend { padding: 1rem; display: flex; justify-content: center; gap: 1.5rem; font-size: 0.8rem; background: rgba(0,0,0,0.1); border-top: 1px solid var(--border); }
    .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; }
    .dot.target { background: #34c759; box-shadow: 0 0 5px #34c759; }
    .dot.focus { background: #3d9cf5; box-shadow: 0 0 5px #3d9cf5; }

    .history-item { border: 1px solid var(--border); transition: transform 0.2s; cursor: pointer; }
    .history-item:hover { transform: scale(1.02); background: rgba(255,255,255,0.05) !important; }
                    <ul id="modalExercisesList" class="space-y-2"></ul>
                </div>
            </div>
            <!-- Recuperacao Sugerida -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                        <i class="fas fa-leaf text-lg"></i>
                    </div>
                    <h4 class="text-lg font-bold text-white uppercase tracking-widest">Recuperação</h4>
                </div>
                <div class="p-5 rounded-xl bg-zinc-900/50 border border-white/5">
                    <p id="modalDietText" class="text-sm text-zinc-300 leading-relaxed"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Compartilhamento -->
<div id="shareModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-zinc-950/80 backdrop-blur-sm px-4" style="display: none;">
    <div class="bg-zinc-900 border border-white/10 w-full max-w-md rounded-2xl shadow-2xl p-6">
        <h3 class="text-xl font-bold text-white mb-4">Opções de Compartilhamento</h3>
        <form id="shareOptionsForm" onsubmit="saveShareOptions(event)">
            <input type="hidden" id="shareAnalysisId">
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-3 text-white cursor-pointer"><input type="checkbox" id="shareFotos"> Compartilhar Fotos</label>
                <label class="flex items-center gap-3 text-white cursor-pointer"><input type="checkbox" id="shareIndicadores"> Compartilhar Indicadores</label>
                <label class="flex items-center gap-3 text-white cursor-pointer"><input type="checkbox" id="shareObservacoes"> Compartilhar Observações</label>
                <label class="flex items-center gap-3 text-white cursor-pointer"><input type="checkbox" id="shareRecomendacoes"> Compartilhar Recomendações</label>
            </div>
            <div class="flex gap-2">
                <button type="button" class="btn btn-ghost flex-1" onclick="document.getElementById('shareModal').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary flex-1">Salvar</button>
            </div>
        </form>
    </div>
</div>

<style>
    .cyber-layout { display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; }
    @media (max-width: 900px) { .cyber-layout { grid-template-columns: 1fr; } }
    .cyber-viewer { height: 620px; display: flex; flex-direction: column; padding: 0 !important; overflow: hidden; position: relative; }
    .viewer-tabs { display: flex; padding: 1rem; gap: 0.5rem; background: rgba(0,0,0,0.2); }
    .tab-btn { background: transparent; border: 1px solid var(--border); color: var(--muted); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
    .tab-btn.active { background: var(--accent); color: white; border-color: var(--accent); }
    .anatomical-display { flex: 1; position: relative; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at center, rgba(61, 156, 245, 0.1) 0%, transparent 70%); overflow: hidden; }
    .hud-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; padding: 1.5rem; z-index: 10; }
    .hud-item { position: absolute; background: rgba(0,0,0,0.6); border-left: 3px solid var(--accent); padding: 0.5rem 1rem; backdrop-filter: blur(8px); border-radius: 0 4px 4px 0; pointer-events: auto; }
    .hud-top-left { top: 1.5rem; left: 1.5rem; }
    .hud-top-right { top: 1.5rem; right: 1.5rem; }
    .hud-label { display: block; font-size: 0.7rem; text-transform: uppercase; color: var(--muted); letter-spacing: 0.05em; }
    .hud-value { font-weight: 700; font-family: 'Outfit', sans-serif; font-size: 1.2rem; }
    .analysis-canvas-container { width: 100%; height: 100%; position: relative; display: flex; align-items: center; justify-content: center; }
    .body-canvas { max-width: 100%; max-height: 100%; object-fit: contain; }
    .upload-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); transition: opacity 0.3s; z-index: 5; }
    .upload-overlay.hidden { opacity: 0; pointer-events: none; }
    .viewer-legend { padding: 1rem; display: flex; justify-content: center; gap: 1.5rem; font-size: 0.8rem; background: rgba(0,0,0,0.1); border-top: 1px solid var(--border); }
    .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; }
    .dot.target { background: #34c759; box-shadow: 0 0 5px #34c759; }
    .dot.focus { background: #3d9cf5; box-shadow: 0 0 5px #3d9cf5; }
    .history-item { border: 1px solid var(--border); transition: transform 0.2s; cursor: pointer; }
    .history-item:hover { transform: scale(1.02); background: rgba(255,255,255,0.05) !important; }
</style>

<!-- MediaPipe Pose -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose/pose.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>

<script>
    const isPacienteMode = {{ $isPaciente ? 'true' : 'false' }};
    let currentView = 'front';
    const canvas = document.getElementById('analysisCanvas');
    const ctx = canvas.getContext('2d');
    const uploadInput = document.getElementById('bodyPhotoInput');
    const overlay = document.getElementById('uploadOverlay');

    function setView(view) {
        currentView = view;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });
    }

    uploadInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    processImage(img, e.target.files[0]);
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    async function processImage(img, file) {
        overlay.classList.add('hidden');
        
        // Ajustar canvas
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);

        // Inicializar MediaPipe
        const pose = new Pose({locateFile: (file) => {
            return `https://cdn.jsdelivr.net/npm/@mediapipe/pose/${file}`;
        }});

        pose.setOptions({
            modelComplexity: 1,
            smoothLandmarks: true,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5
        });

        pose.onResults((results) => {
            drawResults(results, img);
            analyzeMetrics(results, file);
        });

        await pose.send({image: img});
    }

    function drawResults(results, img) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);
        
        if (results.poseLandmarks) {
            drawConnectors(ctx, results.poseLandmarks, POSE_CONNECTIONS, {color: '#00FF00', lineWidth: 4});
            drawLandmarks(ctx, results.poseLandmarks, {color: '#FF0000', lineWidth: 2});
        }
    }

    async function analyzeMetrics(results, file) {
        if (!results.poseLandmarks) return;

        const lm = results.poseLandmarks;

        // Calculo visual estimativo com base nos pontos do MediaPipe.
        const leftShoulder = lm[11];
        const rightShoulder = lm[12];
        const leftHip = lm[23];
        const rightHip = lm[24];
        const leftEar = lm[7];
        const rightEar = lm[8];

        const visiblePoints = [leftShoulder, rightShoulder, leftHip, rightHip].filter(point => (point.visibility ?? 0) >= 0.5);
        const landmarkConfidence = visiblePoints.length
            ? visiblePoints.reduce((total, point) => total + (point.visibility ?? 0), 0) / visiblePoints.length
            : 0;

        if (visiblePoints.length < 4 || landmarkConfidence < 0.5) {
            overlay.classList.remove('hidden');
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Nao foi possivel detectar ombros e quadril com seguranca. Refaca a foto com corpo inteiro visivel e boa luz.', type: 'error' } }));
            return;
        }

        const shoulderTilt = Math.abs(leftShoulder.y - rightShoulder.y);
        const hipTilt = Math.abs(leftHip.y - rightHip.y);
        const shoulderWidth = Math.max(Math.abs(leftShoulder.x - rightShoulder.x), 0.01);
        const hipWidth = Math.max(Math.abs(leftHip.x - rightHip.x), 0.01);
        const looksSide = shoulderWidth < 0.12 || hipWidth < 0.10;

        if ((currentView === 'side' && !looksSide) || (currentView !== 'side' && looksSide)) {
            overlay.classList.remove('hidden');
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'A vista selecionada nao parece combinar com a pose detectada. Confira Frontal, Posterior ou Lateral e envie novamente.', type: 'error' } }));
            return;
        }

        const asymmetryShoulders = (shoulderTilt / shoulderWidth) * 100;
        const asymmetryHips = (hipTilt / hipWidth) * 100;
        const posturePenalty = (asymmetryShoulders * 4) + (asymmetryHips * 3);
        const postureScore = Math.max(0, Math.min(100, 100 - posturePenalty));

        const earMidX = ((leftEar?.x ?? leftShoulder.x) + (rightEar?.x ?? rightShoulder.x)) / 2;
        const shoulderMidX = (leftShoulder.x + rightShoulder.x) / 2;
        const headForwardScore = currentView === 'side'
            ? Math.abs(earMidX - shoulderMidX) * 100
            : 0;

        document.getElementById('symmetryValue').textContent = postureScore.toFixed(1) + '%';
        document.getElementById('postureValue').textContent = postureScore > 90 ? 'Excelente' : (postureScore >= 70 ? 'Observar' : 'Ajustar');

        const metrics = {
            asymmetry_shoulders: asymmetryShoulders.toFixed(2),
            asymmetry_hips: asymmetryHips.toFixed(2),
            posture_score: postureScore.toFixed(0),
            head_forward_score: headForwardScore.toFixed(2),
            landmark_confidence: landmarkConfidence.toFixed(2)
        };

        // Enviar para o servidor
        const formData = new FormData();
        formData.append('image', file);
        formData.append('view_type', currentView);
        formData.append('landmarks', JSON.stringify(lm));
        formData.append('metrics', JSON.stringify(metrics));
        formData.append('_token', '{{ csrf_token() }}');

        const response = await fetch('{{ route("body-analysis.store") }}', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            renderAnalysisData(data);
        } else {
            if (data.code === 'credits_exceeded') {
                window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
            } else {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.error || 'Erro ao processar analise.', type: 'error' } }));
            }
            overlay.classList.remove('hidden'); // Show upload button again
        }
    }

    let isCompareMode = false;
    let selectedForCompare = [];

    async function loadAnalysis(id, element) {
        if (!isCompareMode) {
            const response = await fetch(`{{ url('/body-analysis') }}/${id}`);
            const data = await response.json();
            if (!data.success) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Nao foi possivel abrir esta analise.', type: 'error' } }));
                return;
            }

            const sharedOptions = element ? JSON.parse(element.getAttribute('data-shared') || '{}') : {};
            window.currentSharedOptions = sharedOptions;
            document.getElementById('shareAnalysisId').value = id;

            // Preenche checkbox do modal (apenas profissional)
            if(!isPacienteMode) {
                document.getElementById('shareFotos').checked = sharedOptions.fotos || false;
                document.getElementById('shareIndicadores').checked = sharedOptions.indicadores || false;
                document.getElementById('shareObservacoes').checked = sharedOptions.observacoes || false;
                document.getElementById('shareRecomendacoes').checked = sharedOptions.recomendacoes || false;
            }

            // Aplicar restricoes de visualizacao para o paciente
            if (isPacienteMode) {
                // Fotos
                if (sharedOptions.fotos) {
                    loadAnalysisImage(data.analysis.photo_url);
                } else {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    overlay.innerHTML = '<p class="text-white text-center font-bold px-6">Sua foto não foi compartilhada nesta avaliação.</p>';
                    overlay.classList.remove('hidden');
                }

                // Indicadores
                if (sharedOptions.indicadores) {
                    document.getElementById('hudOverlay').style.display = 'block';
                } else {
                    document.getElementById('hudOverlay').style.display = 'none';
                }

                // Observacoes
                if (sharedOptions.observacoes) {
                    document.getElementById('attentionPointsTitle').style.display = 'block';
                    document.getElementById('attentionPointsList').style.display = 'block';
                } else {
                    document.getElementById('attentionPointsTitle').style.display = 'none';
                    document.getElementById('attentionPointsList').style.display = 'none';
                }

                // Recomendacoes
                if (sharedOptions.recomendacoes) {
                    document.getElementById('btnViewRecommendations').classList.remove('d-none');
                } else {
                    document.getElementById('btnViewRecommendations').classList.add('d-none');
                }
            } else {
                loadAnalysisImage(data.analysis.photo_url);
            }

            renderAnalysisData(data.analysis);
            document.querySelectorAll('.history-item').forEach(el => el.style.border = '');
            if (element) element.style.border = '2px solid #3d9cf5';
            return;
        }

        if (selectedForCompare.includes(id)) {
            // Deselecionar
            selectedForCompare = selectedForCompare.filter(i => i !== id);
            if(element) element.style.border = '';
            return;
        }

        if (selectedForCompare.length < 2) {
            selectedForCompare.push(id);
            if(element) element.style.border = '2px solid #3d9cf5'; // cyber accent color

            if (selectedForCompare.length === 1) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Primeira foto selecionada. Agora selecione a segunda foto do historico.', type: 'success' } }));
            } else if (selectedForCompare.length === 2) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gerando comparativo...', type: 'success' } }));
                setTimeout(() => {
                    window.location.href = `{{ route('body-analysis.compare') }}?id1=${selectedForCompare[0]}&id2=${selectedForCompare[1]}`;
                }, 500);
            }
        }
    }

    function renderAnalysisData(data) {
        document.getElementById('aiSummaryText').textContent = data.summary || 'Analise concluida.';
        document.getElementById('analysisActions').classList.remove('d-none');
        window.currentAiData = data;

        renderList('attentionPointsList', data.attention_points || []);
        renderList('limitationsList', data.limitations || []);

        const hasDetails = (data.attention_points && data.attention_points.length) || (data.limitations && data.limitations.length);
        document.getElementById('analysisDetails').classList.toggle('d-none', !hasDetails);

        if (data.metrics) {
            const score = Number(data.metrics.posture_score || 0);
            document.getElementById('symmetryValue').textContent = `${score.toFixed(1)}%`;
            document.getElementById('postureValue').textContent = score > 90 ? 'Excelente' : (score >= 70 ? 'Observar' : 'Ajustar');
        }
    }

    function renderList(elementId, items) {
        const list = document.getElementById(elementId);
        list.innerHTML = '';
        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'flex gap-2';
            li.innerHTML = `<span class="text-emerald-400">&bull;</span><span>${item}</span>`;
            list.appendChild(li);
        });
    }

    function loadAnalysisImage(url) {
        const img = new Image();
        img.onload = function() {
            overlay.classList.add('hidden');
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);
        };
        img.src = url;
    }

    function compareMode() {
        isCompareMode = !isCompareMode;
        const btn = document.getElementById('btnCompare');
        
        if (isCompareMode) {
            selectedForCompare = [];
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Modo Comparacao Ativo: Selecione a primeira foto no historico.', type: 'success' } }));
            if(btn) {
                btn.textContent = "Cancelar Comparacao";
                btn.classList.remove('btn-outline-info');
                btn.classList.add('btn-danger');
            }
        } else {
            selectedForCompare = [];
            if(btn) {
                btn.textContent = "Comparar Fotos";
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-info');
            }
            // Limpa bordas
            document.querySelectorAll('.history-item').forEach(el => {
                el.style.border = '';
            });
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Modo Comparacao Cancelado.', type: 'success' } }));
        }
    }

    function showSuggestions() {
        if (!window.currentAiData) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Nenhuma analise recente encontrada. Faca o upload primeiro.', type: 'error' } }));
            return;
        }
        
        // Preencher Modal
        document.getElementById('modalWorkoutText').textContent = window.currentAiData.workout || 'Nenhuma sugestao de treino disponivel.';
        document.getElementById('modalDietText').textContent = window.currentAiData.diet || 'Nenhuma recomendacao de recuperacao disponivel.';
        
        const exercisesList = document.getElementById('modalExercisesList');
        exercisesList.innerHTML = '';
        
        if (window.currentAiData.exercises && window.currentAiData.exercises.length > 0) {
            window.currentAiData.exercises.forEach(ex => {
                const li = document.createElement('li');
                li.className = 'text-xs text-zinc-400 flex items-center gap-2';
                li.innerHTML = `<i class="fas fa-check text-blue-500"></i> ${ex}`;
                exercisesList.appendChild(li);
            });
        } else {
            exercisesList.innerHTML = '<li class="text-xs text-zinc-500 italic">Treino base livre focado no plano atual.</li>';
        }

        // Exibir Modal
        const modal = document.getElementById('aiSuggestionsModal');
        if(modal) {
            modal.style.display = 'flex';
        }
    }

    function openShareModal() {
        const id = document.getElementById('shareAnalysisId').value;
        if (!id) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Selecione uma análise no histórico primeiro.', type: 'error' } }));
            return;
        }
        document.getElementById('shareModal').style.display = 'flex';
    }

    async function saveShareOptions(event) {
        event.preventDefault();
        const id = document.getElementById('shareAnalysisId').value;
        if (!id) return;

        const options = {
            fotos: document.getElementById('shareFotos').checked,
            indicadores: document.getElementById('shareIndicadores').checked,
            observacoes: document.getElementById('shareObservacoes').checked,
            recomendacoes: document.getElementById('shareRecomendacoes').checked,
        };

        try {
            const response = await fetch(`{{ url('/body-analysis') }}/${id}/share`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ shared_options: options })
            });

            const data = await response.json();
            if (data.success) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Opções de compartilhamento salvas com sucesso.', type: 'success' } }));
                document.getElementById('shareModal').style.display = 'none';
                
                // Atualiza o atributo data-shared no elemento da lista
                const historyItem = document.querySelector(`.history-item[data-analysis-id="${id}"]`);
                if (historyItem) {
                    historyItem.setAttribute('data-shared', JSON.stringify(options));
                }
            } else {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Erro ao salvar.', type: 'error' } }));
            }
        } catch (error) {
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Erro na requisição.', type: 'error' } }));
        }
    }
</script>
@endsection
