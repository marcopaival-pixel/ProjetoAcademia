@extends('layouts.app', ['navCurrent' => 'plano'])

@section('title', 'Pagamento')

@section('content')
        <h1>Pagamento Mercado Pago</h1>

        @if ($status === 'approved')
            <div class="alert alert-success">
                Pagamento <strong>aprovado</strong>. Seu Premium costuma ser ativado em instantes — atualize a página <a href="{{ route('plano') }}">Meu Plano</a>.
            </div>
        @elseif ($status === 'pending')
            <div class="alert alert-success">
                Pagamento <strong>pendente</strong> (ex.: boleto ou revisão). Quando o Mercado Pago confirmar, o Premium será ativado automaticamente. Acompanhe em <a href="{{ route('plano') }}">Meu Plano</a>.
            </div>
        @elseif ($status === 'failure')
            <div class="alert alert-error">
                O pagamento não foi concluído. Você pode tentar de novo em <a href="{{ route('plano') }}">Meu Plano</a>.
            </div>
        @else
            <p class="lead">Retorno do checkout.</p>
            @if ($paymentId !== '')
                <p class="muted">Referência: {{ $paymentId }}</p>
            @endif
        @endif

        <p style="margin-top:1rem;">
            <a class="btn btn-primary" href="{{ route('plano') }}">Meu Plano</a>
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Hoje</a>
        </p>
@endsection
