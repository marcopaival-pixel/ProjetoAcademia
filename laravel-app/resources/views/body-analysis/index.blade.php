@extends('layouts.app', ['navCurrent' => 'body-analysis'])

@section('title', 'Cyber-Fit Body Intelligence')

@section('content')
<div class="cyber-fit-container animate-fade-up">
    <header class="cyber-fit-header">
        <h1>Cyber-Fit <span class="accent-text">Intelligence</span></h1>
        <p class="lead">Análise anatômica 3D e rastreio de evolução corporal assistido por IA.</p>
    </header>

    <div class="cyber-layout">
        <!-- Esquerda: Visualizador Anatómico -->
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
                    <div class="upload-overlay" id="uploadOverlay">
                        <input type="file" id="bodyPhotoInput" accept="image/*" hidden>
                        <button class="btn btn-primary btn-lg" onclick="document.getElementById('bodyPhotoInput').click()">
                            <i class="fas fa-camera me-2"></i>Enviar Foto para Análise
                        </button>
                        <p class="mt-2 text-muted small">Suas fotos são processadas localmente por IA.</p>
                    </div>
                </div>

                <!-- SVG original como fallback ou guia -->
                <div class="avatar-svg-container d-none" id="bodyView">
                    <!-- SVG stuff here if needed -->
                </div>
            </div>

            <div class="viewer-legend">
                <span class="legend-item"><span class="dot target"></span> Pontos Detectados</span>
                <span class="legend-item"><span class="dot focus"></span> Vetores de Força</span>
            </div>
        </div>

        <!-- Direita: Métricas e Controles -->
        <aside class="cyber-metrics">
            <div class="card glass metrics-card">
                <h3>Últimas Análises</h3>
                <div class="history-list mt-3">
                    @foreach($history as $item)
                        <div class="history-item glass p-2 mb-2 rounded d-flex align-items-center gap-2">
                            <img src="{{ Storage::url($item->photo_path) }}" class="rounded" width="40" height="40" style="object-fit:cover">
                            <div class="flex-grow-1">
                                <div class="small fw-bold">{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="text-info" style="font-size:0.7rem">{{ $item->view_type }}</div>
                            </div>
                            <button class="btn btn-sm btn-ghost" onclick="loadAnalysis({{ $item->id }})"><i class="fas fa-eye"></i></button>
                        </div>
                    @endforeach
                    @if($history->isEmpty())
                        <p class="muted text-center small py-3">Nenhum histórico.</p>
                    @endif
                </div>
                <button class="btn btn-outline-info w-full mt-3" onclick="compareMode()">Comparar Fotos</button>
            </div>

            <div class="card glass ai-insights" id="aiInsightsCard">
                <h3>IA Body Analysis</h3>
                <p class="muted" id="aiSummaryText" style="font-size: 0.9rem;">
                    Faça upload de uma foto para que a IA identifique sua postura, simetria e pontos de desenvolvimento.
                </p>
                <div class="actions-inline d-none" id="analysisActions">
                    <button class="btn btn-sm btn-ghost" onclick="showSuggestions()">Ver Dieta/Treino Sugerido</button>
                </div>
            </div>
        </aside>
    </div>
</div>

<style>
    .cyber-fit-container { padding-bottom: 2rem; }
    .cyber-fit-header { margin-bottom: 2rem; text-align: center; }
    .accent-text { background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; }
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
</style>

<!-- MediaPipe Pose -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose/pose.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>

<script>
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
        
        // Cálculo de Simetria (Ex: Ombros - 11 e 12)
        const leftShoulder = lm[11];
        const rightShoulder = lm[12];
        const symmetry = Math.abs(leftShoulder.y - rightShoulder.y);
        const symmetryScore = Math.max(0, 100 - (symmetry * 500)); // Simulado
        
        document.getElementById('symmetryValue').textContent = symmetryScore.toFixed(1) + '%';
        document.getElementById('postureValue').textContent = symmetryScore > 90 ? 'Excelente' : 'Ajustar';

        const metrics = {
            asymmetry_shoulders: (symmetry * 100).toFixed(2),
            posture_score: symmetryScore.toFixed(0)
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
            document.getElementById('aiSummaryText').textContent = data.summary;
            document.getElementById('analysisActions').classList.remove('d-none');
        } else {
            if (data.code === 'credits_exceeded') {
                window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
            } else {
                alert(data.error || 'Erro ao processar análise.');
            }
            overlay.classList.remove('hidden'); // Show upload button again
        }
    }

    function loadAnalysis(id) {
        // Lógica para recarregar uma análise do histórico no canvas
        window.location.reload(); // Simplificado para este demo
    }
</script>
@endsection
