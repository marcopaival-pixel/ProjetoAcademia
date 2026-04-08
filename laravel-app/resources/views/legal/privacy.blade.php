@extends('layouts.app')

@section('title', 'Política de Privacidade')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="glass-card p-5">
                <h1 class="h2 mb-4 text-gradient">Política de Privacidade</h1>
                <p class="text-muted small mb-4">Última atualização: {{ date('d/m/Y') }}</p>

                <div class="legal-content text-light" style="line-height: 1.8;">
                    <h3>1. Informações que Coletamos</h3>
                    <p>Coletamos informações que você nos fornece diretamente ao criar uma conta, como nome, e-mail, idade, peso e altura. Também coletamos dados de uso automaticamente para melhorar sua experiência.</p>
                    
                    <h3>2. Como Usamos Seus Dados</h3>
                    <p>Seus dados são usados para personalizar seus planos de treino e dieta, realizar cálculos metabólicos e fornecer suporte técnico. Não vendemos suas informações para terceiros.</p>
                    
                    <h3>3. Segurança</h3>
                    <p>Implementamos medidas técnicas e organizacionais para proteger seus dados pessoais contra acesso não autorizado, perda ou alteração. Suas senhas são armazenadas em hash criptográfico.</p>
                    
                    <h3>4. Seus Direitos (LGPD)</h3>
                    <p>Conforme a Lei Geral de Proteção de Dados, você tem o direito de:</p>
                    <ul>
                        <li>Acessar seus dados pessoais.</li>
                        <li>Corrigir dados incompletos ou inexatos.</li>
                        <li>Solicitar a exclusão de seus dados (Direito ao Esquecimento).</li>
                        <li>Exportar seus dados para portabilidade.</li>
                        <li>Revogar seu consentimento a qualquer momento.</li>
                    </ul>
                    
                    <h3>5. Contato</h3>
                    <p>Para dúvidas sobre privacidade, entre em contato com nosso DPO pelo e-mail: dpo@projetoacademia.com.br</p>
                </div>

                <div class="mt-5 pt-4 border-top border-secondary">
                    <a href="{{ url()->previous() }}" class="btn btn-primary px-4">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
