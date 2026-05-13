<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laudo Técnico - {{ $user->name }}</title>
    <style>
        @page { 
            margin: 0; 
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            color: #09090b; 
            line-height: 1.5; 
            font-size: 11px; 
            margin: 0;
            background: #ffffff;
        }
        
        .page-wrapper {
            padding: 1.5cm;
        }

        /* Header Premium */
        .header { 
            border-bottom: 4px solid #10b981; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
            position: relative;
        }
        .header table { width: 100%; }
        .logo { 
            font-size: 28px; 
            font-weight: 900; 
            color: #10b981; 
            text-transform: uppercase; 
            letter-spacing: -1.5px; 
        }
        .logo span { color: #09090b; }
        .report-type { 
            text-align: right; 
            font-size: 10px; 
            font-weight: 800; 
            color: #71717a; 
            text-transform: uppercase; 
            letter-spacing: 2px;
        }
        .report-title { 
            text-align: right; 
            font-size: 18px; 
            font-weight: 900; 
            color: #09090b; 
            text-transform: uppercase;
            margin-top: 5px;
        }

        /* Patient Info Card */
        .patient-card {
            background: #fafafa;
            border: 1px solid #e4e4e7;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .patient-card table { width: 100%; }
        .patient-card td { border: 0; padding: 4px 0; }
        .label { font-size: 9px; font-weight: 700; color: #71717a; text-transform: uppercase; }
        .value { font-size: 11px; font-weight: 800; color: #09090b; }

        /* Sections */
        .section { margin-bottom: 35px; }
        .section-header { 
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 1px solid #e4e4e7;
            padding-bottom: 8px;
        }
        .section-title { 
            display: table-cell;
            font-size: 12px;
            font-weight: 900; 
            text-transform: uppercase; 
            color: #09090b; 
            letter-spacing: 1px;
        }
        
        /* Grid Metrics */
        .metrics-grid {
            width: 100%;
        }
        .metric-item {
            padding: 12px 0;
            border-bottom: 1px solid #f4f4f5;
        }
        .metric-item:last-child { border: 0; }
        .metric-info { display: inline-block; width: 45%; }
        .metric-data { display: inline-block; width: 20%; text-align: right; }
        .metric-bar-container { display: inline-block; width: 30%; margin-left: 5%; }
        
        .metric-name { font-weight: 700; font-size: 10px; color: #3f3f46; }
        .metric-val { font-weight: 900; font-size: 13px; color: #09090b; }
        .metric-unit { font-size: 9px; color: #a1a1aa; font-weight: 400; margin-left: 2px; }
        
        .progress-bg { height: 6px; background: #f4f4f5; border-radius: 10px; position: relative; top: 4px; }
        .progress-fill { height: 100%; background: #10b981; border-radius: 10px; }
        .progress-fill.warning { background: #f59e0b; }
        .progress-fill.danger { background: #ef4444; }

        /* Segmental Analysis */
        .segmental-table { width: 100%; border-spacing: 10px; border-collapse: separate; margin-left: -10px; }
        .segment-box { 
            background: #ffffff; 
            border: 1px solid #e4e4e7; 
            border-radius: 12px; 
            padding: 15px; 
            text-align: center;
        }
        .segment-title { font-size: 8px; font-weight: 700; color: #a1a1aa; text-transform: uppercase; margin-bottom: 5px; }
        .segment-value { font-size: 16px; font-weight: 900; color: #09090b; }

        /* Insights Box */
        .insights-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            padding: 20px;
        }
        .insight-item { margin-bottom: 15px; }
        .insight-item:last-child { margin-bottom: 0; }
        .insight-tag { 
            display: inline-block; 
            font-size: 8px; 
            font-weight: 900; 
            padding: 2px 8px; 
            border-radius: 4px; 
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .tag-success { background: #dcfce7; color: #166534; }
        .tag-warning { background: #fef3c7; color: #92400e; }
        .tag-danger { background: #fee2e2; color: #991b1b; }
        .insight-msg { font-size: 10px; color: #166534; line-height: 1.4; }

        /* Footer & Validation */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5cm;
            background: #fafafa;
            border-top: 1px solid #e4e4e7;
        }
        .footer-table { width: 100%; }
        .footer-info { font-size: 8px; color: #a1a1aa; line-height: 1.6; }
        .qr-code { width: 80px; height: 80px; }
        .validation-text { font-size: 9px; font-weight: 800; color: #09090b; margin-top: 10px; }
        .validation-id { color: #10b981; }

    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="header">
            <table>
                <tr>
                    <td style="border:0;">
                        <span class="logo">Nex<span>Shape</span></span><br>
                        <span style="font-size:9px; color:#71717a; font-weight:600; letter-spacing:1px;">CLINICAL BIOMETRY SYSTEMS</span>
                    </td>
                    <td style="border:0; vertical-align: bottom;">
                        <div class="report-type">Documento Técnico Oficial</div>
                        <div class="report-title">Composição Corporal</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="patient-card">
            <table>
                <tr>
                    <td width="50%">
                        <div class="label">Identificação do Paciente</div>
                        <div class="value" style="font-size:14px;">{{ $user->name }}</div>
                    </td>
                    <td width="25%">
                        <div class="label">Idade</div>
                        <div class="value">{{ $profile->birth_date ? $profile->birth_date->age : '--' }} anos</div>
                    </td>
                    <td width="25%">
                        <div class="label">Sexo</div>
                        <div class="value">{{ $profile->sex == 'male' ? 'Masculino' : 'Feminino' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="label">Código de Registro</div>
                        <div class="value">#{{ str_pad($user->id, 8, '0', STR_PAD_LEFT) }}</div>
                    </td>
                    <td>
                        <div class="label">Estatura</div>
                        <div class="value">{{ $profile->height_cm }} cm</div>
                    </td>
                    <td>
                        <div class="label">Data da Avaliação</div>
                        <div class="value">{{ $assessment->assessment_date->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-header"><div class="section-title">Análise Biétrica Central</div></div>
            <div class="metrics-grid">
                <!-- Peso -->
                <div class="metric-item">
                    <div class="metric-info">
                        <div class="metric-name">Peso Corporal Total</div>
                        <div style="font-size:8px; color:#a1a1aa;">Massa total incluindo líquidos e sólidos</div>
                    </div>
                    <div class="metric-data">
                        <span class="metric-val">{{ number_format($assessment->weight_kg, 1, ',', '.') }}</span>
                        <span class="metric-unit">kg</span>
                    </div>
                    <div class="metric-bar-container">
                        <div class="progress-bg"><div class="progress-fill" style="width: 75%;"></div></div>
                    </div>
                </div>

                <!-- MME -->
                <div class="metric-item">
                    <div class="metric-info">
                        <div class="metric-name">Massa Muscular Esquelética (MME)</div>
                        <div style="font-size:8px; color:#a1a1aa;">Massa muscular voluntária controlada pelo sistema nervoso</div>
                    </div>
                    <div class="metric-data">
                        @php $mme = $assessment->muscle_percent ? ($assessment->weight_kg * $assessment->muscle_percent) / 100 : 0; @endphp
                        <span class="metric-val">{{ $mme ? number_format($mme, 1, ',', '.') : '--' }}</span>
                        <span class="metric-unit">kg</span>
                    </div>
                    <div class="metric-bar-container">
                        <div class="progress-bg"><div class="progress-fill" style="width: 65%;"></div></div>
                    </div>
                </div>

                <!-- Gordura -->
                <div class="metric-item">
                    <div class="metric-info">
                        <div class="metric-name">Massa de Gordura Corporal</div>
                        <div style="font-size:8px; color:#a1a1aa;">Total de tecido adiposo acumulado</div>
                    </div>
                    <div class="metric-data">
                        <span class="metric-val">{{ $assessment->body_fat_mass_kg ? number_format($assessment->body_fat_mass_kg, 1, ',', '.') : '--' }}</span>
                        <span class="metric-unit">kg</span>
                    </div>
                    <div class="metric-bar-container">
                        <div class="progress-bg"><div class="progress-fill {{ ($assessment->body_fat_percent ?? 0) > 25 ? 'danger' : 'warning' }}" style="width: {{ min(($assessment->body_fat_percent ?? 20) * 2, 100) }}%;"></div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header"><div class="section-title">Distribuição Segmental de Massa Magra</div></div>
            <table class="segmental-table">
                <tr>
                    <td width="33%">
                        <div class="segment-box">
                            <div class="segment-title">Braço Esquerdo</div>
                            <div class="segment-value">{{ $assessment->segmental_lean_arm_l ?? '--' }}<span style="font-size:9px; color:#a1a1aa;">kg</span></div>
                        </div>
                    </td>
                    <td width="34%">
                        <div class="segment-box" style="background: #fafafa; border-color: #10b981;">
                            <div class="segment-title" style="color:#10b981;">Tronco Central</div>
                            <div class="segment-value">{{ $assessment->segmental_lean_trunk ?? '--' }}<span style="font-size:9px; color:#a1a1aa;">kg</span></div>
                        </div>
                    </td>
                    <td width="33%">
                        <div class="segment-box">
                            <div class="segment-title">Braço Direito</div>
                            <div class="segment-value">{{ $assessment->segmental_lean_arm_r ?? '--' }}<span style="font-size:9px; color:#a1a1aa;">kg</span></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="segment-box">
                            <div class="segment-title">Perna Esquerda</div>
                            <div class="segment-value">{{ $assessment->segmental_lean_leg_l ?? '--' }}<span style="font-size:9px; color:#a1a1aa;">kg</span></div>
                        </div>
                    </td>
                    <td></td>
                    <td>
                        <div class="segment-box">
                            <div class="segment-title">Perna Direita</div>
                            <div class="segment-value">{{ $assessment->segmental_lean_leg_r ?? '--' }}<span style="font-size:9px; color:#a1a1aa;">kg</span></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        @if(count($bioInsights) > 0)
        <div class="section">
            <div class="section-header"><div class="section-title">Análise Clínica NexBot (IA)</div></div>
            <div class="insights-box">
                @foreach($bioInsights as $insight)
                <div class="insight-item">
                    <div class="insight-tag tag-{{ $insight['level'] }}">{{ $insight['title'] }}</div>
                    <div class="insight-msg">{{ $insight['message'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td width="70%" class="footer-info">
                        <strong>Autenticidade Garantida</strong><br>
                        Este laudo técnico foi gerado eletronicamente pelo ecossistema NexShape e possui certificação digital de integridade. 
                        A reprodução parcial deste documento é proibida. Os dados aqui contidos são de uso clínico e devem ser interpretados por um profissional qualificado.<br><br>
                        
                        <div class="validation-text">
                            VALIDAÇÃO DO DOCUMENTO: <span class="validation-id">{{ $reportRecord->document_id }}</span><br>
                            VERSÃO: {{ $reportRecord->version }} • EMISSÃO: {{ $emissionDate }}
                        </div>
                    </td>
                    <td width="30%" style="text-align: right;">
                        <img src="{{ $qrCode }}" class="qr-code">
                        <div style="font-size:7px; color:#a1a1aa; margin-top:5px; font-weight:700;">ESCANEIE PARA VALIDAR</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
