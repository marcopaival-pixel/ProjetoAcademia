<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receita Médica - {{ $patient->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
        .title { font-size: 28px; font-weight: bold; color: #065f46; margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .subtitle { font-size: 12px; color: #666; margin-top: 5px; font-weight: bold; }
        .patient-info { background: #f9fafb; padding: 20px; border-radius: 15px; margin-bottom: 30px; border: 1px solid #e5e7eb; }
        .patient-info table { width: 100%; }
        .patient-info td { padding: 8px 0; font-size: 14px; }
        .label { font-weight: bold; color: #374151; text-transform: uppercase; font-size: 10px; }
        .prescription-box { border: 2px solid #10b981; border-radius: 20px; padding: 30px; margin-bottom: 40px; background: #fff; position: relative; }
        .prescription-box::after { content: 'Rx'; position: absolute; top: 10px; right: 20px; font-size: 60px; color: rgba(16, 185, 129, 0.1); font-weight: bold; font-style: italic; }
        .medicine-name { font-size: 22px; font-weight: 900; color: #111827; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .specs { margin-bottom: 20px; }
        .spec-item { margin-bottom: 15px; }
        .spec-label { font-weight: bold; color: #6b7280; font-size: 11px; text-transform: uppercase; display: block; }
        .spec-value { font-size: 16px; color: #111827; font-weight: 600; }
        .observations { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin-top: 20px; border-radius: 0 10px 10px 0; }
        .observations-label { font-weight: bold; color: #92400e; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .observations-text { font-size: 13px; color: #451a03; font-style: italic; }
        .footer { text-align: center; margin-top: 80px; font-size: 10px; color: #9ca3af; border-top: 1px solid #eee; pt: 20px; }
        .signature { text-align: center; margin-top: 100px; }
        .signature-line { width: 250px; border-top: 1.5px solid #111827; margin: 0 auto; padding-top: 10px; }
        .signature-name { font-size: 14px; font-weight: 800; color: #111827; margin: 0; }
        .signature-reg { font-size: 11px; color: #6b7280; margin: 5px 0 0 0; font-weight: 600; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Receituário Digital</h1>
        <p class="subtitle">DATA DE EMISSÃO: {{ $prescription->date->format('d/m/Y') }}</p>
    </div>

    <div class="patient-info">
        <table>
            <tr>
                <td width="60%"><span class="label">{{ $patientLabel }}:</span><br><strong>{{ $patient->name }}</strong></td>
                <td width="40%"><span class="label">CPF/ID:</span><br><strong>{{ $patient->cpf ?: '---' }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="prescription-box">
        <div class="medicine-name">{{ $prescription->medicine }}</div>
        
        <div class="specs">
            <div class="spec-item">
                <span class="spec-label">Dosagem / Posologia</span>
                <span class="spec-value">{{ $prescription->dosage ?: 'Conforme orientação verbal' }}</span>
            </div>
            <div class="spec-item">
                <span class="spec-label">Frequência de Uso</span>
                <span class="spec-value">{{ $prescription->frequency ?: 'Dose única' }}</span>
            </div>
            <div class="spec-item">
                <span class="spec-label">Período / Duração</span>
                <span class="spec-value">{{ $prescription->duration ?: 'Uso contínuo' }}</span>
            </div>
        </div>

        @if($prescription->observations)
            <div class="observations">
                <span class="observations-label">Orientações Especiais</span>
                <div class="observations-text">"{{ $prescription->observations }}"</div>
            </div>
        @endif
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p class="signature-name">{{ $prescription->professional->name }}</p>
        <p class="signature-reg">{{ $prescription->professional->professionalProfile->registration_number ?? 'REGISTRO NÃO INFORMADO' }}</p>
    </div>

    <div class="footer">
        <p>Este documento é uma via digital de orientação ao {{ mb_strtolower($patientLabel) }}, gerada via plataforma <strong>{{ config('app.name') }}</strong>.</p>
        <p>A autenticidade deste documento pode ser validada pelo profissional responsável.</p>
        <p>&copy; {{ date('Y') }} - NEX SHAPE PRO PERFORMANCE</p>
    </div>
</body>
</html>



