<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laudo Clínico - {{ $patient->name }}</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            color: #09090b; 
            line-height: 1.5; 
            font-size: 10px; 
            margin: 0; 
            padding: 0; 
            background: #ffffff;
        }
        
        .page-wrapper { padding: 1.5cm; }

        /* Header Premium */
        .header { border-bottom: 4px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 900; color: #3b82f6; text-transform: uppercase; letter-spacing: -1.5px; }
        .logo span { color: #09090b; }
        .report-title { text-align: right; font-size: 14px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }

        .pro-box { margin-bottom: 35px; }
        .pro-name { font-size: 14px; font-weight: 900; color: #1e3a8a; margin: 0; }
        .pro-meta { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; }

        /* Patient Card */
        .patient-card { 
            background: #f8fafc; 
            border: 1px solid #e2e8f0; 
            border-radius: 15px; 
            padding: 20px; 
            margin-bottom: 35px; 
        }
        .section-header { margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; }
        .section-title { font-size: 9px; font-weight: 900; color: #3b82f6; text-transform: uppercase; letter-spacing: 1px; }
        
        .data-grid { width: 100%; }
        .data-grid td { padding: 6px 0; border: 0; }
        .label { color: #64748b; font-weight: 800; font-size: 8px; text-transform: uppercase; width: 110px; }
        .value { color: #0f172a; font-weight: 900; font-size: 11px; }

        /* QR Section */
        .qr-section { float: right; text-align: center; width: 120px; margin-top: -120px; }
        .qr-box { background: white; border: 1px solid #e2e8f0; padding: 10px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
        .qr-image { width: 85px; height: 85px; }
        .qr-text { font-size: 7px; color: #94a3b8; margin-top: 8px; text-transform: uppercase; font-weight: 800; }

        /* History Table */
        table.history { width: 100%; border-collapse: separate; border-spacing: 0 5px; }
        table.history th { text-align: left; padding: 12px; color: #64748b; font-size: 8px; text-transform: uppercase; font-weight: 800; border-bottom: 2px solid #f1f5f9; }
        table.history td { padding: 12px; background: #fafafa; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        table.history tr td:first-child { border-left: 1px solid #e2e8f0; border-top-left-radius: 10px; border-bottom-left-radius: 10px; font-weight: 800; color: #0f172a; }
        table.history tr td:last-child { border-right: 1px solid #e2e8f0; border-top-right-radius: 10px; border-bottom-right-radius: 10px; font-weight: 900; color: #3b82f6; font-size: 12px; text-align: right; }

        /* Footer */
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            right: 0; 
            padding: 1cm 1.5cm; 
            background: #fafafa; 
            border-top: 1px solid #f1f5f9; 
            text-align: center; 
            font-size: 8px; 
            color: #94a3b8; 
        }
        .cert-id { font-family: monospace; color: #cbd5e1; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td style="border: 0;"><div class="logo">Nex<span>Shape</span> Clinical</div></td>
                    <td class="report-title" style="border: 0;">Laudo Clínico de Evolução</td>
                </tr>
            </table>
        </div>

        <div class="pro-box">
            <p class="pro-name">{{ $professional->name }}</p>
            <p class="pro-meta">Responsável Técnico • CRM/CREF: {{ $professional->profile->professional_id ?? 'Não Informado' }}</p>
        </div>

        <div class="qr-section">
            <div class="qr-box">
                <img src="{{ $qrCode }}" class="qr-image" alt="QR Code Verification">
            </div>
            <div class="qr-text">Validar Autenticidade</div>
        </div>

        <div class="patient-card">
            <div class="section-header"><div class="section-title">Informações do Prontuário</div></div>
            <table class="data-grid">
                <tr>
                    <td class="label">Nome Completo</td>
                    <td class="value">{{ $patient->name }}</td>
                    <td class="label">Sexo Biológico</td>
                    <td class="value">{{ $clinicalData['sex'] }}</td>
                </tr>
                <tr>
                    <td class="label">Objetivo Clínico</td>
                    <td class="value">{{ $clinicalData['goal'] }}</td>
                    <td class="label">Estatura Atual</td>
                    <td class="value">{{ $clinicalData['height'] }}</td>
                </tr>
                <tr>
                    <td class="label">Última Aferição</td>
                    <td class="value" style="color: #3b82f6;">{{ $clinicalData['last_weight'] }}</td>
                    <td class="label">Registro Interno</td>
                    <td class="value">#{{ str_pad($patient->id, 8, '0', STR_PAD_LEFT) }}</td>
                </tr>
            </table>
        </div>

        <div class="section-header"><div class="section-title">Evolução Ponderal Recente</div></div>
        @if($history->isNotEmpty())
            <table class="history">
                <thead>
                    <tr>
                        <th>Data da Coleta</th>
                        <th>Natureza do Registro</th>
                        <th style="text-align: right;">Peso Corporal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $entry)
                        <tr>
                            <td>{{ $entry->weighed_at->format('d/m/Y') }}</td>
                            <td style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 700;">Acompanhamento de Rotina</td>
                            <td style="text-align: right;">{{ number_format($entry->weight_kg, 1, ',', '.') }} kg</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 30px; text-align: center; color: #94a3b8; border: 2px dashed #f1f5f9; border-radius: 15px; font-weight: 700;">
                Sem registros de evolução ponderal para o período selecionado.
            </div>
        @endif

        <div class="footer">
            Este laudo é um documento digital emitido via plataforma NexShape em {{ $emissionDate }}.<br>
            <span class="cert-id">CERTIDÃO: {{ $reportRecord->hash }}</span><br>
            A integridade das informações contidas neste laudo pode ser verificada eletronicamente.
        </div>
    </div>
</body>
</html>
