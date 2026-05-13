<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Performance Mensal - {{ $user->name }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            color: #09090b; 
            line-height: 1.4; 
            font-size: 10px; 
            margin: 0; 
            background: #ffffff;
        }
        
        .page-wrapper { padding: 1.5cm; }

        /* Header Moderno */
        .header { border-bottom: 4px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 26px; font-weight: 900; color: #10b981; text-transform: uppercase; letter-spacing: -1.5px; }
        .logo span { color: #09090b; }
        .tagline { font-size: 8px; color: #71717a; text-transform: uppercase; font-weight: 800; letter-spacing: 2px; margin-top: -5px; }
        
        .report-badge { 
            background: #f0fdf4; 
            color: #166534; 
            padding: 4px 12px; 
            border-radius: 6px; 
            font-size: 8px; 
            font-weight: 900; 
            text-transform: uppercase; 
            display: inline-block; 
            margin-bottom: 8px;
            border: 1px solid #bbf7d0;
        }
        .report-title { font-size: 24px; font-weight: 900; margin: 0; color: #09090b; text-transform: uppercase; }
        
        /* Box de Informações */
        .user-box { background: #fafafa; border: 1px solid #e4e4e7; border-radius: 15px; padding: 20px; margin-top: 25px; }
        .user-table { width: 100%; border: 0; }
        .user-table td { padding: 4px 0; border: 0; }
        .label { color: #71717a; font-weight: 700; font-size: 8px; text-transform: uppercase; width: 90px; }
        .value { color: #09090b; font-weight: 800; font-size: 10px; }

        /* QR Code & Validação */
        .qr-section { float: right; text-align: center; width: 120px; margin-top: -110px; }
        .qr-box { background: white; border: 1px solid #e4e4e7; padding: 10px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
        .qr-image { width: 85px; height: 85px; }
        .qr-text { font-size: 7px; color: #71717a; margin-top: 8px; text-transform: uppercase; font-weight: 800; }

        /* Grid de Resumo */
        .summary-grid { width: 100%; margin: 30px 0; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .card { background: #ffffff; border: 1px solid #e4e4e7; border-radius: 15px; padding: 18px; }
        .card-title { font-size: 8px; font-weight: 800; color: #71717a; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #f4f4f5; padding-bottom: 6px; }
        .card-value { font-size: 18px; font-weight: 900; color: #09090b; }
        .card-unit { font-size: 9px; color: #a1a1aa; font-weight: 600; margin-left: 3px; }
        .card-trend { font-size: 7px; color: #a1a1aa; margin-top: 6px; }

        /* Tabela de Registros */
        .section-title { font-size: 11px; font-weight: 900; color: #09090b; text-transform: uppercase; margin: 35px 0 15px; border-left: 4px solid #10b981; padding-left: 12px; letter-spacing: 1px; }
        
        table.days { width: 100%; border-collapse: separate; border-spacing: 0 5px; }
        table.days th { text-align: left; padding: 12px; color: #71717a; font-size: 8px; text-transform: uppercase; font-weight: 800; border-bottom: 2px solid #f4f4f5; }
        table.days td { padding: 12px; background: #fafafa; border-top: 1px solid #e4e4e7; border-bottom: 1px solid #e4e4e7; }
        table.days tr td:first-child { border-left: 1px solid #e4e4e7; border-top-left-radius: 10px; border-bottom-left-radius: 10px; font-weight: 800; color: #09090b; }
        table.days tr td:last-child { border-right: 1px solid #e4e4e7; border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        .num { text-align: right; }
        .positive { color: #10b981; }
        .negative { color: #ef4444; }

        /* Footer */
        .footer { 
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1cm 1.5cm;
            border-top: 1px solid #f4f4f5; 
            text-align: center; 
            font-size: 8px; 
            color: #a1a1aa;
            background: #fafafa;
        }
        .hash-id { color: #d4d4d8; font-family: monospace; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td style="border: 0;">
                        <div class="logo">Nex<span>Shape</span></div>
                        <div class="tagline">Inteligência em Performance Elite</div>
                    </td>
                </tr>
            </table>
            
            <div style="margin-top: 35px;">
                <div class="report-badge">Performance Mensal Analítica</div>
                <h1 class="report-title">{{ $monthLabel }}</h1>
            </div>

            <div class="user-box">
                <table class="user-table">
                    <tr>
                        <td class="label">Atleta/Paciente</td>
                        <td class="value">{{ $user->name }}</td>
                        <td class="label">Período Fiscal</td>
                        <td class="value">{{ $rangeLabel }}</td>
                    </tr>
                    <tr>
                        <td class="label">Certificação</td>
                        <td class="value">#{{ str_pad($reportRecord->document_id, 10, '0', STR_PAD_LEFT) }}</td>
                        <td class="label">Versão</td>
                        <td class="value">{{ $reportRecord->version }}.0.0 - Premium</td>
                    </tr>
                </table>
            </div>

            <div class="qr-section">
                <div class="qr-box">
                    <img src="{{ $qrCode }}" class="qr-image" alt="QR Validation">
                </div>
                <div class="qr-text">Certificado NexShape</div>
            </div>
        </div>

        <table class="summary-grid">
            <tr>
                <td style="width: 33%;">
                    <div class="card">
                        <div class="card-title">Média Calórica</div>
                        <div class="card-value">{{ $days_with_food > 0 ? round($avg_kcal) : '--' }}<span class="card-unit">kcal/dia</span></div>
                        <div class="card-trend">Baseado em {{ $days_with_food }} dias registrados</div>
                    </div>
                </td>
                <td style="width: 34%;">
                    <div class="card" style="border-color: #10b981;">
                        <div class="card-title" style="color: #10b981;">Atividade Física</div>
                        <div class="card-value">{{ number_format($total_ex_min, 0, ',', '.') }}<span class="card-unit">min totais</span></div>
                        <div class="card-trend">Gasto estimado: {{ number_format($total_ex_kcal, 0, ',', '.') }} kcal</div>
                    </div>
                </td>
                <td style="width: 33%;">
                    <div class="card">
                        <div class="card-title">Variação Ponderal</div>
                        <div class="card-value {{ $delta_weight <= 0 ? 'positive' : 'negative' }}">
                            {{ $delta_weight !== null ? sprintf('%+.1f', $delta_weight) : '--' }}<span class="card-unit">kg</span>
                        </div>
                        <div class="card-trend">{{ $first_weight ?? '--' }}kg → {{ $last_weight ?? '--' }}kg</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Log de Performance e Biometria Diária</div>
        
        <table class="days">
            <thead>
                <tr>
                    <th>Data do Registro</th>
                    <th class="num">Ingestão (kcal)</th>
                    <th class="num">Treino (min)</th>
                    <th class="num">Gasto (kcal)</th>
                    <th class="num">Peso (kg)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($days as $r)
                    <tr>
                        <td>{{ $r['label'] }}</td>
                        <td class="num" style="color: {{ $r['kcal_in'] > 2500 ? '#ef4444' : '#09090b' }}">{{ $r['kcal_in'] > 0 ? number_format($r['kcal_in'], 0, ',', '.') : '—' }}</td>
                        <td class="num">{{ $r['ex_min'] > 0 ? $r['ex_min'] : '—' }}</td>
                        <td class="num">{{ $r['ex_kcal'] > 0 ? number_format($r['ex_kcal'], 0, ',', '.') : '—' }}</td>
                        <td class="num" style="font-weight: 800;">{{ $r['weight'] !== null ? number_format($r['weight'], 1, ',', '.') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Este relatório analítico foi gerado pela Inteligência Artificial NexBot para fins de monitoramento de performance.<br>
            <span class="hash-id">AUTENTICIDADE: {{ $reportRecord->hash }}</span><br>
            NexShape Academy © {{ date('Y') }} - Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
