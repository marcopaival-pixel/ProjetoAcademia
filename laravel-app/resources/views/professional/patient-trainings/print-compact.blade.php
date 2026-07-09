<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Treino compacto - {{ $plan->name }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 4mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            width: 72mm;
            margin: 0 auto;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
        }

        .toolbar {
            position: sticky;
            top: 0;
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 12px;
            background: #111827;
        }

        .toolbar button,
        .toolbar a {
            border: 0;
            border-radius: 8px;
            padding: 9px 12px;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .toolbar a {
            background: #374151;
        }

        .receipt {
            min-height: 100vh;
            padding: 8px 0;
            background: #fff;
        }

        .center {
            text-align: center;
        }

        .brand {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .muted {
            color: #4b5563;
        }

        .line {
            margin: 8px 0;
            border-top: 1px dashed #111827;
        }

        .section-title {
            margin: 10px 0 5px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .exercise {
            margin-bottom: 9px;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .exercise-name {
            font-size: 12px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th,
        td {
            padding: 2px 1px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        th {
            font-size: 9px;
            text-transform: uppercase;
        }

        .notes {
            margin-top: 4px;
            font-size: 10px;
        }

        .signature {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid #111827;
            text-align: center;
            font-size: 10px;
        }

        @media print {
            body {
                width: 72mm;
                margin: 0;
                background: #fff;
            }

            .toolbar {
                display: none;
            }

            .receipt {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Imprimir 80mm</button>
        <a href="{{ route('professional.patients.trainings.index', $patient->id) }}">Voltar</a>
    </div>

    <main class="receipt">
        <header class="center">
            <div class="brand">NexShape</div>
            <div class="muted">Ficha compacta de treino</div>
            <div>{{ now()->format('d/m/Y H:i') }}</div>
        </header>

        <div class="line"></div>

        <section>
            <div><strong>Aluno:</strong> {{ $patient->name }}</div>
            <div><strong>Treino:</strong> {{ $plan->name }}</div>
            @if($plan->goal)
                <div><strong>Objetivo:</strong> {{ $plan->goal }}</div>
            @endif
            @if($plan->frequency)
                <div><strong>Frequencia:</strong> {{ $plan->frequency }}x/semana</div>
            @endif
            @if($plan->estimated_duration)
                <div><strong>Duracao:</strong> {{ $plan->estimated_duration }} min</div>
            @endif
        </section>

        @if($plan->description)
            <div class="line"></div>
            <section>
                <div class="section-title">Observacoes</div>
                <div>{{ $plan->description }}</div>
            </section>
        @endif

        <div class="line"></div>

        <section>
            <div class="section-title">Exercicios</div>

            @forelse($plan->exercises as $exercise)
                @php($catalog = $exercise->catalogExercise)
                <article class="exercise">
                    <div class="exercise-name">
                        {{ $loop->iteration }}. {{ $exercise->custom_name ?: ($catalog?->name ?? 'Exercicio') }}
                    </div>
                    @if($catalog?->muscle_group)
                        <div class="muted">{{ $catalog->muscle_group }}</div>
                    @endif

                    <table>
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Kg</th>
                                <th>Reps</th>
                                <th>Desc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exercise->sets as $set)
                                <tr>
                                    <td>{{ $set->set_number }}</td>
                                    <td>{{ $set->weight_target !== null ? $set->weight_target : '-' }}</td>
                                    <td>{{ $set->reps_target !== null ? $set->reps_target : '-' }}</td>
                                    <td>{{ $set->rest_seconds ? $set->rest_seconds.'s' : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Sem series cadastradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($exercise->notes)
                        <div class="notes"><strong>Obs:</strong> {{ $exercise->notes }}</div>
                    @endif
                </article>
            @empty
                <div>Nenhum exercicio cadastrado.</div>
            @endforelse
        </section>

        <div class="line"></div>

        <footer>
            <div class="signature">Assinatura do profissional</div>
            <div class="center muted" style="margin-top: 10px;">Gerado pelo NexShape</div>
        </footer>
    </main>
</body>
</html>
