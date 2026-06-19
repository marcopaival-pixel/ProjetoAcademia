<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Atestado Médico - {{ $patient->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.8; padding: 40px; }
        .header { text-align: center; margin-bottom: 60px; }
        .title { font-size: 28px; font-weight: bold; text-decoration: underline; margin-bottom: 10px; }
        .content { font-size: 16px; text-align: justify; margin-bottom: 60px; }
        .date-location { text-align: right; margin-bottom: 60px; }
        .signature { text-align: center; }
        .signature-line { width: 300px; border-top: 1px solid #000; margin: 0 auto; padding-top: 5px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">ATESTADO</h1>
    </div>

    <div class="content">
        <p>Atesto, para os devidos fins, a pedido do interessado, que o(a) Sr(a). <strong>{{ $patient->name }}</strong>, 
        inscrito(a) no CPF sob o nº <strong>{{ $patient->cpf }}</strong>, foi atendido(a) nesta data por este profissional.</p>
        
        <p>O referido {{ mb_strtolower($patientLabel) }} apresenta <strong>{{ $certificate->reason }}</strong>, necessitando de um período de 
        afastamento de suas atividades habituais por um prazo de <strong>{{ $certificate->period ?: '---' }}</strong>, 
        contados a partir de <strong>{{ $certificate->start_date->format('d/m/Y') }}</strong>.</p>
        
        @if($certificate->observations)
            <p><strong>Observações:</strong> {{ $certificate->observations }}</p>
        @endif
    </div>

    <div class="date-location">
        <p>Cidade e Estado, {{ $certificate->date->format('d \d\e F \d\e Y') }}</p>
    </div>

    <div class="signature">
        <div class="signature-line"></div>
        <p><strong>{{ auth()->user()->name }}</strong></p>
        <p>Registro Profissional: {{ auth()->user()->professionalProfile->registro_profissional ?? '---' }}</p>
    </div>

    <div class="footer">
        <p>Documento emitido via Sistema NexShape - Autenticidade verificável eletronicamente.</p>
    </div>
</body>
</html>



