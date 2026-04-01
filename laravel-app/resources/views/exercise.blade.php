@extends('layouts.app', ['navCurrent' => 'exercise'])

@section('title', 'Exercícios')

@section('content')
        <h1>Exercícios</h1>
        <p class="lead">Atividades do dia (gasto calórico opcional).</p>

        @if (!empty($notice))
            <div class="alert alert-success">{{ $notice }}</div>
        @endif
        @if (!empty($error))
            <div class="alert alert-error">{{ $error }}</div>
        @endif

        <form method="get" class="form-group" style="margin-bottom: 1.25rem;" action="{{ route('exercise') }}">
            <label for="date">Dia</label>
            <input id="date" name="date" type="date" value="{{ $date }}" onchange="this.form.submit()">
        </form>

        <div class="grid grid-2">
            <div class="card">
                <h2>{{ $editRow ? 'Editar exercício' : 'Adicionar' }}</h2>
                @if ($editRow)
                    <p class="muted" style="margin:0 0 1rem; font-size:0.875rem;">
                        <a href="{{ route('exercise', ['date' => $date]) }}">Cancelar edição</a>
                    </p>
                @endif
                <form method="post" action="{{ route('exercise') }}" novalidate>
                    @csrf
                    <input type="hidden" name="entry_date" value="{{ $date }}">
                    @if ($editRow)
                        <input type="hidden" name="exercise_edit_id" value="{{ $editRow->id }}">
                    @endif
                    <div class="form-group">
                        <label for="activity_type">Atividade</label>
                        <input id="activity_type" name="activity_type" type="text" required maxlength="120" placeholder="Ex.: Caminhada, musculação" value="{{ old('activity_type', $editRow->activity_type ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label for="duration_min">Duração (min)</label>
                        <input id="duration_min" name="duration_min" type="number" min="0" max="1440" required value="{{ old('duration_min', $editRow ? $editRow->duration_min : '30') }}">
                    </div>
                    <div class="form-group">
                        <label for="calories_burned">Gasto estimado (kcal), opcional</label>
                        <input id="calories_burned" name="calories_burned" type="number" min="0" max="5000" placeholder="vazio se não souber" value="{{ old('calories_burned', $editRow && $editRow->calories_burned !== null ? $editRow->calories_burned : '') }}">
                    </div>
                    <div class="form-group">
                        <label for="notes">Observações</label>
                        <textarea id="notes" name="notes" maxlength="500" placeholder="Opcional">{{ old('notes', $editRow->notes ?? '') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ $editRow ? 'Atualizar' : 'Salvar' }}</button>
                </form>
                @if (!$editRow)
                    <div class="copy-day-form">
                        <h3 class="muted" style="margin:0 0 0.5rem; font-size:0.9375rem; font-weight:600;">Copiar de outro dia</h3>
                        <form method="post" action="{{ route('exercise') }}" class="form-inline">
                            @csrf
                            <input type="hidden" name="action" value="copy_exercises">
                            <input type="hidden" name="target_date" value="{{ $date }}">
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="exercise_source_date">Origem</label>
                                <input id="exercise_source_date" name="source_date" type="date" required>
                            </div>
                            <button type="submit" class="btn btn-ghost btn-sm">Copiar para {{ \Carbon\Carbon::parse($date)->format('d/m') }}</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="card">
                <h2>Resumo do dia</h2>
                <p style="margin:0 0 0.5rem;"><strong>{{ $sumMin }} min</strong> de atividade</p>
                <p class="muted" style="margin:0 0 1rem;">Soma de kcal informadas: <strong>{{ $sumBurn }}</strong></p>
                @if ($rows->isEmpty())
                    <p class="empty-state">Nenhum exercício neste dia.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Atividade</th>
                                    <th>Min</th>
                                    <th>kcal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $r)
                                    <tr>
                                        <td>{{ $r->activity_type }}</td>
                                        <td>{{ $r->duration_min }}</td>
                                        <td>{{ $r->calories_burned !== null ? $r->calories_burned : '—' }}</td>
                                        <td class="td-fit">
                                            <div class="row-actions">
                                                <a class="btn btn-ghost btn-sm" href="{{ route('exercise', ['date' => $date, 'edit' => $r->id]) }}">Editar</a>
                                                <form method="post" action="{{ route('exercise') }}" class="form-row-delete" onsubmit="return confirm('Remover este exercício?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="delete_exercise">
                                                    <input type="hidden" name="entry_date" value="{{ $date }}">
                                                    <input type="hidden" name="exercise_id" value="{{ $r->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
