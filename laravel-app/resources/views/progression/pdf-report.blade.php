<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NexShape - {{ $plan->name }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1a1a2e; margin: 0; padding: 0; font-size: 11pt; line-height: 1.4; }
        .header { margin-bottom: 30px; border-bottom: 3px solid #0d6efd; padding-bottom: 15px; }
        .logo-text { font-size: 24pt; font-weight: bold; color: #0d6efd; letter-spacing: -1px; }
        .plan-info { float: right; text-align: right; }
        .clearfix { clear: both; }
        
        .athlete-card { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 5px solid #0d6efd; }
        .athlete-name { font-size: 14pt; font-weight: bold; margin: 0; }
        .athlete-meta { font-size: 9pt; color: #666; margin-top: 5px; }

        .exercise-card { margin-bottom: 25px; page-break-inside: avoid; }
        .exercise-title { background: #1a1a2e; color: white; padding: 8px 12px; font-weight: bold; border-radius: 5px 5px 0 0; font-size: 12pt; }
        .exercise-subtitle { font-size: 9pt; color: #ced4da; font-weight: normal; margin-left: 8px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f3f5; color: #495057; font-size: 8pt; text-transform: uppercase; padding: 8px; border: 1px solid #dee2e6; letter-spacing: 1px; }
        td { padding: 10px 8px; border: 1px solid #dee2e6; font-size: 10pt; text-align: center; }
        .col-target { background: #fff; font-weight: bold; }
        .col-actual { background: #fff; color: #dee2e6; } /* Espaço para preencher a caneta */
        
        .sidebar-notes { font-size: 9pt; color: #495057; font-style: italic; margin-top: 5px; padding-left: 5px; }
        
        .qr-section { margin-top: 40px; text-align: center; border-top: 1px dashed #dee2e6; padding-top: 20px; }
        .qr-box { display: inline-block; text-align: center; }
        .qr-text { font-size: 8pt; color: #888; margin-top: 5px; }

        .footer { position: fixed; bottom: -1cm; left: 0; right: 0; height: 1cm; text-align: center; font-size: 8pt; color: #adb5bd; }
    </style>
</head>
<body>
    <div class="header">
        <div class="plan-info">
            <div style="font-size: 16pt; font-weight: bold;">{{ $plan->plan_label ?: 'Treino' }}</div>
            <div style="font-size: 10pt; color: #666;">Exportado em {{ date('d/m/Y') }}</div>
        </div>
        <div class="logo-text">NexShape<span style="color: #1a1a2e;">Arena</span></div>
        <div class="clearfix"></div>
    </div>

    <div class="athlete-card">
        <div class="athlete-name">{{ $user->name }}</div>
        <div class="athlete-meta">
            <strong>Objetivo:</strong> {{ $plan->goal ?: 'Alta Performance' }} | 
            <strong>Plano:</strong> {{ $plan->name }}
        </div>
    </div>

    @foreach($plan->exercises as $ex)
        <div class="exercise-card">
            <div class="exercise-title">
                {{ $ex->catalogExercise->name }}
                <span class="exercise-subtitle">| {{ $ex->catalogExercise->muscle_group }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="10%">Série</th>
                        <th width="20%">Meta Peso</th>
                        <th width="20%">Meta Reps</th>
                        <th width="15%">Descanso</th>
                        <th style="background: #e9ecef; color: #1a1a2e;">Peso Realizado</th>
                        <th style="background: #e9ecef; color: #1a1a2e;">Reps Realizadas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ex->sets as $set)
                        <tr>
                            <td>{{ $set->set_number }}</td>
                            <td class="col-target">{{ $set->weight_target ?? '—' }} <small>kg</small></td>
                            <td class="col-target">{{ $set->reps_target ?? '—' }}</td>
                            <td>{{ $set->rest_seconds }}s</td>
                            <td class="col-actual">________</td>
                            <td class="col-actual">________</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($ex->notes)
                <div class="sidebar-notes">Obs: {{ $ex->notes }}</div>
            @endif
        </div>
    @endforeach

    <div class="qr-section">
        <div class="qr-box">
            @php($appUrl = route('progression.session-log', $plan))
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($appUrl) }}" alt="QR Code Treino">
            <div class="qr-text">Escaneie para registrar digitalmente</div>
        </div>
    </div>

    <div class="footer">
        NexShape Arena - Inteligência em Performance Física | {{ $user->email }}
    </div>
</body>
</html>
