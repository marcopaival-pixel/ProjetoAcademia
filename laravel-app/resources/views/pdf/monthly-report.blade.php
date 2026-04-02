<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .meta { margin: 0 0 16px; font-size: 10px; color: #555; }
        .stats { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .stats td { border: 1px solid #ccc; padding: 6px 8px; vertical-align: top; }
        .stats .lab { font-weight: bold; width: 42%; background: #f5f5f5; }
        table.days { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.days th, table.days td { border: 1px solid #ddd; padding: 4px 5px; text-align: left; }
        table.days th { background: #eee; }
        .num { text-align: right; }
        .foot { margin-top: 16px; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <h1>ProjetoAcademia — relatório mensal</h1>
    <p class="meta">
        <strong>{{ $user->name }}</strong> · {{ $user->email }}<br>
        Mês: <strong>{{ $monthLabel }}</strong> · Período no PDF: {{ $rangeLabel }}
    </p>

    <table class="stats" aria-label="Resumo">
        <tr>
            <td class="lab">Média kcal / dia (com registo alimentar)</td>
            <td>{{ $days_with_food > 0 ? $avg_kcal . ' kcal (' . $days_with_food . ' dia(s))' : '—' }}</td>
        </tr>
        <tr>
            <td class="lab">Exercício no período</td>
            <td>{{ $total_ex_min }} min · {{ $total_ex_kcal }} kcal indicadas</td>
        </tr>
        <tr>
            <td class="lab">Peso (variação no período, quando aplicável)</td>
            <td>
                @if ($delta_weight !== null)
                    {{ sprintf('%+.2f', $delta_weight) }} kg
                    @if ($first_weight !== null && $last_weight !== null)
                        ({{ number_format($first_weight, 2, ',', '.') }} → {{ number_format($last_weight, 2, ',', '.') }} kg)
                    @endif
                @else
                    —
                @endif
            </td>
        </tr>
    </table>

    <p style="margin:0 0 6px; font-weight:bold;">Detalhe por dia</p>
    <table class="days">
        <thead>
            <tr>
                <th>Dia</th>
                <th class="num">kcal</th>
                <th class="num">Ex. min</th>
                <th class="num">Ex. kcal</th>
                <th class="num">Peso kg</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($days as $r)
                <tr>
                    <td>{{ $r['label'] }}</td>
                    <td class="num">{{ $r['kcal_in'] > 0 ? $r['kcal_in'] : '—' }}</td>
                    <td class="num">{{ $r['ex_min'] > 0 ? $r['ex_min'] : '—' }}</td>
                    <td class="num">{{ $r['ex_kcal'] > 0 ? $r['ex_kcal'] : '—' }}</td>
                    <td class="num">{{ $r['weight'] !== null ? number_format($r['weight'], 2, ',', '.') : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="foot">
        Gerado em {{ now()->translatedFormat('d/m/Y H:i') }}. Valores baseados nos registos da sua conta.
        Não substitui acompanhamento profissional de saúde ou nutrição.
    </p>
</body>
</html>
