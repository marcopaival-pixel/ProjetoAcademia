<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laudo do Paciente - {{ $patient->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #3b82f6; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1e3a8a; text-transform: uppercase; }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; font-size: 18px; color: #3b82f6; border-bottom: 1px solid #e5e7eb; margin-bottom: 10px; padding-bottom: 5px; }
        .data-grid { display: block; width: 100%; }
        .data-item { margin-bottom: 10px; }
        .label { font-weight: bold; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
        table th { background-color: #f9fafb; color: #374151; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .qr-code { float: right; margin-top: -100px; text-align: center; }
        .qr-code img { border: 1px solid #ddd; padding: 5px; background: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laudo Clínico</h1>
        <p>{{ $professional->name }} - Profissional Responsável</p>
    </div>

    <div class="qr-code">
        <img src="{{ $qrCodeUrl }}" width="120" height="120" alt="QR Code de Verificação">
        <p style="font-size: 10px;">Validar Autenticidade</p>
    </div>

    <div class="section">
        <div class="section-title">Dados do Paciente</div>
        <div class="data-item"><span class="label">Nome:</span> {{ $patient->name }}</div>
        <div class="data-item"><span class="label">E-mail:</span> {{ $patient->email }}</div>
        <div class="data-item"><span class="label">CPF:</span> {{ $patient->cpf }}</div>
    </div>

    <div class="section">
        <div class="section-title">Dados Clínicos</div>
        <div class="data-item"><span class="label">Objetivo:</span> {{ $clinicalData['goal'] }}</div>
        <div class="data-item"><span class="label">Sexo:</span> {{ $clinicalData['sex'] }}</div>
        <div class="data-item"><span class="label">Altura:</span> {{ $clinicalData['height'] }}</div>
        <div class="data-item"><span class="label">Último Peso Registrado:</span> {{ $clinicalData['last_weight'] }}</div>
    </div>

    <div class="section">
        <div class="section-title">Histórico Recente de Evolução</div>
        @if($history->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Peso (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $entry)
                        <tr>
                            <td>{{ $entry->weighed_at->format('d/m/Y') }}</td>
                            <td>{{ $entry->weight_kg }} kg</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nenhum histórico registrado.</p>
        @endif
    </div>

    <div class="footer">
        Documento emitido em: {{ $emissionDate }}<br>
        Sistema NexShape - Gestão Inteligente de Saúde
    </div>
</body>
</html>
