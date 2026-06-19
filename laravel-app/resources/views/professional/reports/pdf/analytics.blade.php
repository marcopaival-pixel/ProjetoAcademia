<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
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

        /* Header Premium */
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
        
        /* User Box */
        .user-box { background: #fafafa; border: 1px solid #e4e4e7; border-radius: 15px; padding: 20px; margin-top: 25px; margin-bottom: 30px; }
        .user-table { width: 100%; border: 0; }
        .user-table td { padding: 4px 0; border: 0; }
        .label { color: #71717a; font-weight: 700; font-size: 8px; text-transform: uppercase; width: 90px; }
        .value { color: #09090b; font-weight: 800; font-size: 10px; }

        /* Grid de Resumo */
        .summary-grid { width: 100%; margin: 0 0 30px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .card { background: #ffffff; border: 1px solid #e4e4e7; border-radius: 15px; padding: 18px; }
        .card-title { font-size: 8px; font-weight: 800; color: #71717a; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #f4f4f5; padding-bottom: 6px; }
        .card-value { font-size: 18px; font-weight: 900; color: #09090b; }
        .card-unit { font-size: 9px; color: #a1a1aa; font-weight: 600; margin-left: 3px; }

        /* Tabela de Estudantes */
        .section-title { font-size: 11px; font-weight: 900; color: #09090b; text-transform: uppercase; margin: 35px 0 15px; border-left: 4px solid #10b981; padding-left: 12px; letter-spacing: 1px; }
        table.students { width: 100%; border-collapse: separate; border-spacing: 0 5px; }
        table.students th { text-align: left; padding: 12px; color: #71717a; font-size: 8px; text-transform: uppercase; font-weight: 800; border-bottom: 2px solid #f4f4f5; }
        table.students td { padding: 12px; background: #fafafa; border-top: 1px solid #e4e4e7; border-bottom: 1px solid #e4e4e7; }
        table.students tr td:first-child { border-left: 1px solid #e4e4e7; border-top-left-radius: 10px; border-bottom-left-radius: 10px; font-weight: 800; color: #09090b; }
        table.students tr td:last-child { border-right: 1px solid #e4e4e7; border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        .num { text-align: center; font-weight: 800; }
        
        .progress-bar { width: 100%; background: #e4e4e7; height: 6px; border-radius: 4px; margin-top: 4px; }
        .progress-fill { height: 100%; background: #10b981; border-radius: 4px; }

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
                <div class="report-badge">Analytics - Gestão de {{ $patientsLabel }}</div>
                <h1 class="report-title">{{ $title }}</h1>
            </div>

            <div class="user-box">
                <table class="user-table">
                    <tr>
                        <td class="label">Profissional</td>
                        <td class="value">{{ $user->name }}</td>
                        <td class="label">Data de Geração</td>
                        <td class="value">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <table class="summary-grid">
            <tr>
                <td style="width: 33%;">
                    <div class="card">
                        <div class="card-title">{{ $patientsLabel }} Ativos</div>
                        <div class="card-value">{{ $data['active_students'] }} <span class="card-unit">/ {{ $data['total_students'] }} totais</span></div>
                    </div>
                </td>
                <td style="width: 34%;">
                    <div class="card" style="border-color: #10b981;">
                        <div class="card-title" style="color: #10b981;">Aderência Nutricional</div>
                        <div class="card-value">{{ $data['avg_adherence_food'] }}<span class="card-unit">% global</span></div>
                    </div>
                </td>
                <td style="width: 33%;">
                    <div class="card" style="border-color: #3b82f6;">
                        <div class="card-title" style="color: #3b82f6;">Aderência Treinos</div>
                        <div class="card-value">{{ $data['avg_adherence_training'] }}<span class="card-unit">% global</span></div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Engajamento Detalhado por {{ $patientLabel }}</div>
        
        <table class="students">
            <thead>
                <tr>
                    <th>Identificação do {{ $patientLabel }}</th>
                    <th class="num">Sessões Treino</th>
                    <th class="num">Dias Dieta</th>
                    <th class="num">Ad. Treino</th>
                    <th class="num">Ad. Nutricional</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['students_data'] as $s)
                    <tr>
                        <td>
                            <div>{{ $s['name'] }}</div>
                            <div style="font-size: 7px; color: #a1a1aa; font-weight: 600; margin-top: 2px;">{{ $s['email'] }}</div>
                        </td>
                        <td class="num">{{ $s['workouts'] }}</td>
                        <td class="num">{{ $s['food_days'] }}</td>
                        <td>
                            <div class="num">{{ $s['adherence_training'] }}%</div>
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ $s['adherence_training'] }}%; background-color: #3b82f6;"></div></div>
                        </td>
                        <td>
                            <div class="num">{{ $s['adherence_food'] }}%</div>
                            <div class="progress-bar"><div class="progress-fill" style="width: {{ $s['adherence_food'] }}%;"></div></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            Relatório confidencial gerado pelo painel do Profissional para fins de acompanhamento.<br>
            NexShape Academy © {{ date('Y') }} - Todos os direitos reservados.
        </div>
    </div>
</body>
</html>



