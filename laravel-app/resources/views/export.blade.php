@extends('layouts.app', ['navCurrent' => 'export'])

@section('title', 'Exportar dados')

@section('content')
        <h1>Exportar dados</h1>
        @if (!$isPremium)
            <div class="premium-paywall premium-paywall--page">
                <div class="premium-paywall__icon" aria-hidden="true">👑</div>
                <p class="premium-paywall__title">Recurso Premium</p>
                <p class="premium-paywall__text">Exporte alimentação, exercícios e peso em CSV para enviar à nutricionista ou analisar no Excel. Assine para liberar os downloads.</p>
                <a class="btn btn-primary" href="{{ route('plano') }}">Ver planos e assinar</a>
            </div>
            <p style="margin-top:1rem;"><a class="btn btn-ghost" href="{{ route('report') }}">Relatório semanal (visualização no app)</a></p>
        @else
        <p class="lead">Baixe seus registros em CSV (UTF-8, separador <strong>;</strong>, compatível com Excel em PT-BR).</p>
        <div class="card" style="max-width: 36rem; margin-bottom: 1.25rem;">
            <h2 style="margin-top:0;">Filtrar por período (opcional)</h2>
            <form method="get" class="export-filter-form" action="{{ route('export') }}">
                <div class="form-group">
                    <label for="f_from">De</label>
                    <input id="f_from" name="f_from" type="date" value="{{ $formFrom }}">
                </div>
                <div class="form-group">
                    <label for="f_to">Até</label>
                    <input id="f_to" name="f_to" type="date" value="{{ $formTo }}">
                </div>
                <button type="submit" class="btn btn-ghost">Aplicar filtro nos links abaixo</button>
            </form>
            <p class="muted" style="margin:0.75rem 0 0; font-size:0.875rem;">Deixe em branco para incluir <strong>todos</strong> os registros. Depois use os botões de download com o período aplicado.</p>
        </div>

        @php
            $fromQ = $formFrom !== '' ? ['from' => $formFrom] : [];
            $toQ = $formTo !== '' ? ['to' => $formTo] : [];
            $q = array_merge($fromQ, $toQ);
        @endphp
        <div class="card" style="max-width: 36rem;">
            <h2 style="margin-top:0;">Download</h2>
            <ul class="export-list muted" style="margin:0; padding-left:1.15rem; line-height:2;">
                <li><a class="btn btn-primary" style="display:inline-block; margin:0.25rem 0;" href="{{ route('export', array_merge(['kind' => 'food'], $q)) }}">Alimentação (CSV)</a></li>
                <li><a class="btn btn-primary" style="display:inline-block; margin:0.25rem 0;" href="{{ route('export', array_merge(['kind' => 'exercise'], $q)) }}">Exercícios (CSV)</a></li>
                <li><a class="btn btn-primary" style="display:inline-block; margin:0.25rem 0;" href="{{ route('export', array_merge(['kind' => 'weight'], $q)) }}">Peso (CSV)</a></li>
            </ul>
            <p class="muted" style="margin:1rem 0 0; font-size:0.875rem;">Os arquivos contêm apenas os dados da sua conta.</p>
        </div>
        <p style="margin-top:1rem;"><a class="btn btn-ghost" href="{{ route('report') }}">Ver relatório semanal</a></p>
        @endif
@endsection
