@extends('layouts.app')

@section('title', 'A sua jornada fitness começa aqui')

@section('content')
<div class="landing-page">
    <!-- Hero Section -->
    <section class="hero animate-fade-up">
        <h1 class="hero-title">Transforme sua saúde com inteligência</h1>
        <p class="hero-subtitle">Acompanhe sua alimentação, treinos e evolução em um só lugar. Tudo o que você precisa para alcançar seus objetivos, powered by AI.</p>
        <div class="actions-inline justify-center">
            <a href="{{ route('onboarding.welcome') }}" class="btn btn-xl btn-primary">Começar Gratuitamente</a>
            <a href="#features" class="btn btn-xl btn-ghost">Ver recursos</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="animate-fade-up delay-1">
        <h2 class="section-title">Tudo o que você precisa</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🥗</div>
                <h3>Diário Alimentar</h3>
                <p>Registre suas refeições com facilidade e tenha o controle total de calorias e macronutrientes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💪</div>
                <h3>Treinos Personalizados</h3>
                <p>Acompanhe sua rotina de exercícios, séries e repetições para maximizar seus resultados.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <h3>Chat com IA</h3>
                <p>Nossa inteligência artificial analisa seus dados e fornece dicas personalizadas para sua dieta.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Relatórios Avançados</h3>
                <p>Visualize sua evolução com gráficos detalhados e relatórios PDF mensais automáticos.</p>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing animate-fade-up delay-2">
        <h2 class="section-title">Escolha seu plano</h2>
        <div class="pricing-grid">
            <div class="price-card">
                <h3 class="price-title">Grátis</h3>
                <div class="price-value">R$ 0<span class="price-period">/mês</span></div>
                <p class="muted">Para quem está começando agora.</p>
                <ul class="price-features">
                    <li>Diário alimentar básico</li>
                    <li>Registro de exercícios</li>
                    <li>1 relatório por mês</li>
                    <li>Acesso à comunidade</li>
                </ul>
                <a href="{{ route('onboarding.welcome') }}" class="btn btn-ghost w-full">Começar Gratuitamente</a>
            </div>
            
            <div class="price-card featured">
                <div class="badge">Mais popular</div>
                <h3 class="price-title">Premium</h3>
                <div class="price-value">R$ 29,90<span class="price-period">/mês</span></div>
                <p class="muted">A experiência completa para resultados máximos.</p>
                <ul class="price-features">
                    <li>Diário alimentar ilimitado</li>
                    <li>Chat ilimitado com IA</li>
                    <li>Relatórios PDF detalhados</li>
                    <li>Suporte prioritário</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary w-full">Seja Premium</a>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="hero animate-fade-up delay-3">
        <h2 class="section-title">Pronto para mudar de vida?</h2>
        <p class="hero-subtitle">Junte-se a centenas de usuários que já estão transformando sua rotina com o NexShape.</p>
        <div class="actions-inline justify-center">
            <a href="{{ route('onboarding.welcome') }}" class="btn btn-xl btn-primary">Começar Gratuitamente</a>
        </div>
    </section>
</div>
@endsection
