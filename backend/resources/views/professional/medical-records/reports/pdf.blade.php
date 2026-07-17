<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laudo Médico - {{ $patient->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; }
        .logo { max-height: 80px; margin-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; color: #1e3a8a; margin: 0; }
        .subtitle { font-size: 14px; color: #666; }
        .patient-info { background: #f3f4f6; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .patient-info table { width: 100%; }
        .patient-info td { padding: 5px 0; font-size: 14px; }
        .label { font-weight: bold; color: #4b5563; }
        .content { margin-bottom: 40px; }
        .content h3 { border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; color: #1e3a8a; }
        .footer { text-align: center; margin-top: 60px; font-size: 12px; color: #9ca3af; }
        .signature { margin-top: 60px; text-align: center; }
        .signature-line { width: 300px; border-top: 1px solid #000; margin: 0 auto; padding-top: 5px; }
        .qr-code { float: right; margin-top: -100px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">LAUDO TÉCNICO</h1>
        <p class="subtitle">Emissão em: {{ $report->date->format('d/m/Y') }}</p>
    </div>

    <div class="patient-info">
        <table>
            <tr>
                <td width="50%"><span class="label">Paciente:</span> {{ $patient->name }}</td>
                <td width="50%"><span class="label">CPF:</span> {{ $patient->cpf }}</td>
            </tr>
            <tr>
                <td><span class="label">Data de Nasc.:</span> {{ $patient->profile->birth_date ? $patient->profile->birth_date->format('d/m/Y') : 'N/A' }}</td>
                <td><span class="label">Profissional:</span> {{ auth()->user()->name }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        <h3>{{ $report->title }}</h3>
        <p>{{ $report->description }}</p>
        
        @if($report->conclusion)
            <h3>Conclusão / Parecer</h3>
            <p>{{ $report->conclusion }}</p>
        @endif
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p><strong>{{ auth()->user()->name }}</strong></p>
        <p>Registro Profissional: {{ auth()->user()->professionalProfile->registro_profissional ?? '---' }}</p>
    </div>

    <div class="footer">
        <p>Este documento foi gerado eletronicamente através da plataforma NexShape.</p>
        <p>&copy; {{ date('Y') }} - {{ config('app.name') }}</p>
    </div>
</body>
</html>
