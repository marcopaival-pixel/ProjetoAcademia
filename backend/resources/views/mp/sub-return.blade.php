@extends('layouts.app', ['navCurrent' => 'plano'])

@section('title', 'Assinatura')

@section('content')
        <h1>Assinatura Mercado Pago</h1>

        @if ($status === 'authorized' || $status === 'approved')
            <div class="alert alert-success">
                Assinatura <strong>autorizada</strong>. Sua primeira cobrança pode levar alguns instantes; o Premium é ativado quando o pagamento for confirmado. Consulte <a href="{{ route('plano') }}">Meu Plano</a>.
            </div>
        @elseif ($status === 'pending' || $status === '')
            <div class="alert alert-success">
                Processo em andamento no Mercado Pago. Quando a assinatura for autorizada e o pagamento aprovado, o Premium será liberado automaticamente.
            </div>
        @else
            <div class="alert alert-error">
                Não foi possível concluir a assinatura. Tente novamente em <a href="{{ route('plano') }}">Meu Plano</a>.
            </div>
        @endif

        @if ($preId !== '')
            <p class="muted" style="font-size:0.875rem;">Referência assinatura: <code>{{ $preId }}</code></p>
        @endif

        <p style="margin-top:1rem;">
            <a class="btn btn-primary" href="{{ route('plano') }}">Meu Plano</a>
            <a class="btn btn-ghost" href="{{ route('dashboard') }}">Hoje</a>
        </p>
@endsection
