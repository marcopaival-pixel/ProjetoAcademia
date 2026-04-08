@extends('layouts.app')

@section('title', 'Treinando: ' . $plan->name)

@section('content')
<div class="container py-3">
    <form action="{{ route('progression.log.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ date('Y-m-d') }}">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 text-gradient mb-0">{{ $plan->name }}</h2>
                <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> {{ date('d/m/Y') }}</small>
            </div>
            <button type="submit" class="btn btn-primary shadow-lg">Finalizar Treino</button>
        </div>

        @foreach($plan->exercises as $index => $exercise)
            <div class="card glass-card border-0 mb-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-info mb-1">{{ $exercise->catalogExercise->name }}</h5>
                        <span class="badge bg-dark-subtle text-muted">{{ $exercise->catalogExercise->muscle_group }}</span>
                    </div>
                    @if($exercise->last_log)
                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                            <small class="text-success" style="font-size: 0.75rem">
                                <i class="fas fa-history me-1"></i> Último: {{ $exercise->last_log->weight_kg }}kg x {{ $exercise->last_log->reps_done }}
                            </small>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.7rem;">
                                <i class="fas fa-bolt me-1"></i> {{ $exercise->suggestion['message'] }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <input type="hidden" name="logs[{{ $index }}][training_plan_exercise_id]" value="{{ $exercise->id }}">
                    <input type="hidden" name="logs[{{ $index }}][exercise_id]" value="{{ $exercise->exercise_id }}">
                    
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm text-light mb-0">
                            <thead>
                                <tr class="text-muted small text-center">
                                    <th width="10%">SET</th>
                                    <th width="20%">META</th>
                                    <th width="20%">KG</th>
                                    <th width="20%">REPS</th>
                                    <th width="10%"><i class="fas fa-skull text-danger" title="Falha"></i></th>
                                    <th width="20%">TIMER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exercise->sets as $set)
                                    <tr class="align-middle text-center set-row-active" data-rest="{{ $set->rest_seconds }}">
                                        <td><span class="fw-bold">{{ $set->set_number }}</span></td>
                                        <td class="small text-secondary">
                                            @php
                                                $suggestedWeight = $exercise->suggestion['suggested_weight'] ?? ($exercise->last_log->weight_kg ?? ($set->weight_target ?? 0));
                                            @endphp
                                            <span class="{{ ($exercise->suggestion['indicator'] ?? '') == 'increase' ? 'text-success' : 'text-info' }}">{{ $suggestedWeight }}</span>kg x {{ $set->reps_target ?? '-' }}
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" name="logs[{{ $index }}][sets][{{ $set->set_number-1 }}][weight]" 
                                                class="form-control form-control-sm glass-input text-center weight-input" 
                                                value="{{ $suggestedWeight }}"
                                                placeholder="{{ $exercise->last_log->weight_kg ?? '0' }}">
                                        </td>
                                        <td>
                                            <input type="number" name="logs[{{ $index }}][sets][{{ $set->set_number-1 }}][reps]" 
                                                class="form-control form-control-sm glass-input text-center reps-input" 
                                                placeholder="{{ $exercise->last_log->reps_done ?? '0' }}">
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input" type="checkbox" name="logs[{{ $index }}][sets][{{ $set->set_number-1 }}][failure]" value="1">
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-timer" onclick="startTimer(this, {{ $set->rest_seconds }})">
                                                <i class="fas fa-stopwatch"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-grid mb-5">
            <button type="submit" class="btn btn-primary btn-lg shadow">Finalizar Treino</button>
        </div>
    </form>
</div>

</style>

<script>
function startTimer(btn, seconds) {
    const originalContent = btn.innerHTML;
    let timeLeft = seconds;
    
    btn.disabled = true;
    btn.classList.remove('btn-outline-warning');
    btn.classList.add('btn-warning');
    
    const interval = setInterval(() => {
        const mins = Math.floor(timeLeft / 60);
        const secs = timeLeft % 60;
        btn.innerHTML = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
        
        if (timeLeft <= 0) {
            clearInterval(interval);
            btn.innerHTML = originalContent;
            btn.disabled = false;
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-outline-warning');
            
            // Som de alerta opcional ou vibração
            if (navigator.vibrate) navigator.vibrate(200);
            
            // Notificação visual
            btn.classList.add('animate__animated', 'animate__bounce');
            setTimeout(() => btn.classList.remove('animate__animated', 'animate__bounce'), 1000);
        }
        timeLeft--;
    }, 1000);
}
</script>
@endsection
