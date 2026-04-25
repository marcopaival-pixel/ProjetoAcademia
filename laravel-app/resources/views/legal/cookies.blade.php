@extends('layouts.app')

@section('title', 'Política de Cookies')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="glass-card p-5">
                <h1 class="h2 mb-4 text-gradient">Política de Cookies</h1>
                <p class="text-muted small mb-4">Última atualização: {{ date('d/m/Y') }}</p>

                <div class="legal-content text-light" style="line-height: 1.8;">
                    <h3>1. O que são Cookies</h3>
                    <p>Cookies são arquivos de texto pequenos que são armazenados no seu navegador quando você visita o nosso site. Eles nos ajudam a fazer o site funcionar melhor e a entender como você o utiliza.</p>
                    
                    <h3>2. Como Usamos Cookies</h3>
                    <p>Utilizamos cookies estritamente necessários para manter sua sessão ativa e cookies de funcionalidade para lembrar suas preferências (como o modo escuro).</p>
                    
                    <h3>3. Cookies de Terceiros</h3>
                    <p>Podemos utilizar serviços como Google Analytics para coletar dados anônimos de navegação e Mercado Pago para processar pagamentos de forma segura.</p>
                    
                    <h3>4. Como Gerenciar Cookies</h3>
                    <p>Você pode desativar os cookies nas configurações do seu navegador, mas isso pode afetar a funcionalidade de certas partes do nosso sistema.</p>
                </div>

                <div class="mt-5 pt-4 border-top border-secondary">
                    <a href="{{ url()->previous() }}" class="btn btn-primary px-4">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
