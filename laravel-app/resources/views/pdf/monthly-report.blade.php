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
    <table style="width: 100%; margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px;">
        <tr>
            <td style="width: 65%; vertical-align: top;">
                <img src="{{ public_path('images/logo_Rodape.png') }}" alt="NexShape" style="width: 140px; margin-bottom: 15px;">
                <h1 style="color: #1a1a1a; margin: 0; font-size: 24px; font-weight: 900;">RELATÓRIO DE PERFORMANCE</h1>
                <p style="color: #666; font-size: 10px; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px;">
                    Inteligência em Saúde & Performance Elite
                </p>
                
                <div style="margin-top: 20px;">
                    <table style="width: 100%; font-size: 10px;">
                        <tr>
                            <td style="color: #888; width: 120px;">PACIENTE:</td>
                            <td style="font-weight: bold; color: #333;">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td style="color: #888;">PROFISSIONAL:</td>
                            <td style="font-weight: bold; color: #333;">{{ $user->activeProfessional->name ?? 'NexShape Academy' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #888;">ID DO DOCUMENTO:</td>
                            <td style="font-family: monospace; color: #555;">{{ $reportRecord->document_id }}</td>
                        </tr>
                        <tr>
                            <td style="color: #888;">VERSÃO:</td>
                            <td style="color: #333;"><strong>v{{ $reportRecord->version }}</strong> (Incremental)</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 35%; text-align: right; vertical-align: top;">
                <div style="display: inline-block; text-align: center; border: 1px solid #eee; padding: 10px; border-radius: 10px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($validationUrl) }}" alt="QR Code" style="width: 100px; height: 100px;">
                    <p style="font-size: 7px; color: #999; margin-top: 5px; text-transform: uppercase;">Aponte para validar<br>autenticidade</p>
                </div>
                <p style="font-size: 8px; color: #aaa; margin-top: 10px;">
                    Gerado em: {{ $reportRecord->generated_at->format('d/m/Y H:i:s') }}
                </p>
            </td>
        </tr>
    </table>

    <div style="background: #fafafa; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #f0f0f0;">
        <p style="margin: 0; font-size: 11px; color: #444;">
            <strong>Contexto da Análise:</strong> Referente ao mês de <strong>{{ $monthLabel }}</strong>. 
            Período de coleta: <strong>{{ $rangeLabel }}</strong>.
        </p>
    </div>

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

    <p class="foot" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px;">
        Gerado em {{ $reportRecord->generated_at->format('d/m/Y H:i') }}. Versão: <strong>v{{ $reportRecord->version }}</strong>. 
        Hash de Autenticidade: <span style="font-family: monospace; font-size: 8px;">{{ $reportRecord->hash }}</span><br>
        Este documento é digitalmente rastreável. A validação pode ser feita via QR Code ou no portal oficial da NexShape.
        Não substitui acompanhamento profissional de saúde ou nutrição.
    </p>
</body>
</html>
