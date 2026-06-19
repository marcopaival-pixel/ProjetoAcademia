<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Proposta Comercial - NexShape Pro</title>
    <style>
        @page {
            margin: 40px 50px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 13px;
            line-height: 1.4;
        }
        .container {
            width: 100%;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #10b981;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #111827;
            font-size: 26px;
            margin: 0;
            text-transform: uppercase;
        }
        .header span.highlight {
            color: #10b981;
        }
        .header .meta {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }
        .flex-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .half-width {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .box {
            background-color: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            border-left: 3px solid #10b981;
            margin-bottom: 10px;
        }
        .box h4 {
            margin: 0 0 8px 0;
            color: #111827;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        .details-section h3 {
            color: #111827;
            font-size: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: bold;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .total-row td {
            font-size: 15px;
            font-weight: bold;
            color: #111827;
            background-color: #f3f4f6;
        }
        .savings-box {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .checklist {
            column-count: 2;
            margin-bottom: 20px;
        }
        .checklist div {
            margin-bottom: 5px;
        }
        .exclusive-offer {
            text-align: center;
            background-color: #fffbeb;
            border: 2px dashed #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .exclusive-offer h3 {
            color: #d97706;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .exclusive-offer p {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
        }
        .qr-section {
            text-align: center;
            margin-top: 30px;
        }
        .qr-code {
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .signature {
            margin-top: 40px;
            border-top: 1px solid #333;
            width: 300px;
            text-align: center;
            padding-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- CABEÇALHO -->
        <div class="header">
            <h1>NEX<span class="highlight">SHAPE</span> PRO</h1>
            <p style="font-size: 16px; font-weight: bold; margin-top: 5px;">Proposta Comercial de Licenciamento</p>
            <div class="meta">
                @if(isset($proposal))
                Proposta Nº {{ str_pad($proposal->id, 6, '0', STR_PAD_LEFT) }} | 
                Emitida em: {{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y') }} | 
                @else
                Simulação Emitida em: {{ now()->format('d/m/Y') }} | 
                @endif
                Válida até: {{ $validityDate }}
            </div>
        </div>

        <!-- DADOS (REPRESENTANTE E CLÍNICA) -->
        <div class="flex-container">
            <div class="half-width" style="margin-right: 2%;">
                <div class="box">
                    <h4>Dados do Representante</h4>
                    <strong>Nome:</strong> {{ $representative->name }}<br>
                    <strong>E-mail:</strong> {{ $representative->email }}<br>
                    <strong>Telefone/WhatsApp:</strong> {{ $representative->phone ?? 'Não informado' }}<br>
                    <strong>Código de Indicação:</strong> <span style="color: #10b981; font-weight: bold;">{{ $representative->representativeProfile->code ?? '' }}</span>
                </div>
            </div>
            <div class="half-width">
                <div class="box">
                    <h4>Dados da Clínica</h4>
                    @if(isset($proposal))
                    <strong>Nome:</strong> {{ $proposal->clinic_name }}<br>
                    <strong>Responsável:</strong> {{ $proposal->clinic_contact }}<br>
                    <strong>CNPJ:</strong> {{ $proposal->clinic_cnpj }}<br>
                    <strong>Localização:</strong> {{ $proposal->clinic_city }} / {{ $proposal->clinic_state }}<br>
                    <strong>Telefone:</strong> {{ $proposal->clinic_phone }}
                    @else
                    <em>Clínica a ser definida (Simulação)</em><br>
                    <br><br><br><br>
                    @endif
                </div>
            </div>
        </div>

        <!-- RESUMO DO PLANO -->
        <div class="details-section">
            <h3>Resumo do Plano</h3>
            <table>
                <thead>
                    <tr>
                        <th>Descrição do Plano</th>
                        <th style="text-align: right;">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Plano {{ $plan->name }}</strong><br>
                            <small>Licenciamento oficial NexShape Pro</small>
                        </td>
                        <td class="amount">R$ {{ number_format($basePrice, 2, ',', '.') }}</td>
                    </tr>
                    @if($discountAmount > 0)
                    <tr>
                        <td>
                            Desconto Representante <span style="font-weight: bold;">{{ $representative->representativeProfile->code ?? '' }}</span> ({{ $discountRate }}%)
                        </td>
                        <td class="amount" style="color: #ef4444;">- R$ {{ number_format($discountAmount, 2, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Valor Final (Mensal)</td>
                        <td class="amount">R$ {{ number_format($finalPrice, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ECONOMIA GERADA -->
        @if($discountAmount > 0)
        <div class="savings-box">
            Você economiza R$ {{ number_format($discountAmount * 12, 2, ',', '.') }} por ano utilizando esta oferta exclusiva.
        </div>
        @endif

        <!-- FUNCIONALIDADES -->
        <div class="details-section">
            <h3>Funcionalidades Inclusas</h3>
            <div class="checklist">
                <div>✅ Agendamento Online</div>
                <div>✅ Prontuário Eletrônico</div>
                <div>✅ Controle Financeiro</div>
                <div>✅ Gestão de Pacientes</div>
                <div>✅ Integração WhatsApp</div>
                <div>✅ Inteligência Artificial</div>
                <div>✅ Relatórios Gerenciais</div>
                <div>✅ Multiusuários</div>
                <div>✅ Backup Automático</div>
                <div>✅ Suporte Técnico</div>
            </div>
        </div>

        <!-- COMPARATIVO -->
        <div class="details-section">
            <h3>Comparativo Competitivo</h3>
            <table>
                <thead>
                    <tr>
                        <th>Funcionalidade</th>
                        <th>NexShape Pro</th>
                        <th>Concorrentes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>IA Integrada</td><td style="color: #10b981; font-weight: bold;">Sim</td><td>Não</td></tr>
                    <tr><td>WhatsApp Integrado</td><td style="color: #10b981; font-weight: bold;">Sim</td><td>Parcial</td></tr>
                    <tr><td>Prontuário Completo</td><td style="color: #10b981; font-weight: bold;">Sim</td><td>Sim</td></tr>
                    <tr><td>Relatórios Avançados</td><td style="color: #10b981; font-weight: bold;">Sim</td><td>Limitado</td></tr>
                    <tr><td>Suporte Dedicado</td><td style="color: #10b981; font-weight: bold;">Sim</td><td>Variável</td></tr>
                </tbody>
            </table>
        </div>

        <!-- OFERTA EXCLUSIVA -->
        <div class="exclusive-offer">
            <h3>OFERTA EXCLUSIVA DO REPRESENTANTE AUTORIZADO</h3>
            <p>Código: {{ $representative->representativeProfile->code ?? '' }}</p>
            <p>Válida até: {{ $validityDate }}</p>
        </div>

        <!-- QR CODE -->
        @if(isset($qrCode))
        <div class="qr-section">
            <p style="font-weight: bold; margin-bottom: 5px;">Escaneie o QR Code abaixo para realizar o seu cadastro com desconto:</p>
            <img src="{{ $qrCode }}" class="qr-code" alt="QR Code de Cadastro">
            <p style="font-size: 11px; color: #6b7280;">Os dados do representante e desconto serão preenchidos automaticamente.</p>
        </div>
        @endif

        <div style="margin-top: 50px;">
            <div class="signature">
                Representante Autorizado<br>
                <span style="font-weight: normal; font-size: 12px;">{{ $representative->name }}<br>{{ $representative->phone ?? '' }} | {{ $representative->email }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Proposta gerada automaticamente pelo Portal do Representante NexShape Pro. Sem validade fiscal.</p>
            <p>&copy; {{ date('Y') }} NexShape Pro. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
