@extends('layouts.app', ['navCurrent' => 'weight'])

@section('title', 'Peso')

@section('content')
        <h1>Peso</h1>
        <p class="lead">Um registro por dia; nova entrada no mesmo dia substitui o anterior.</p>

        @if (!empty($notice))
            <div class="alert alert-success">{{ $notice }}</div>
        @endif
        @if (!empty($error))
            <div class="alert alert-error">{{ $error }}</div>
        @endif

        @if ($weightChartHtml !== '')
            <div class="card weight-chart" style="margin-bottom: 1.25rem;">
                <h2 style="margin-top:0;">Evolução (últimos registros)</h2>
                <p class="muted" style="margin:0 0 0.75rem; font-size:0.875rem;">Ordem cronológica; eixo horizontal são as datas da primeira à última amostra abaixo.</p>
                {!! $weightChartHtml !!}
            </div>
        @elseif ($rows->count() === 1)
            <div class="card" style="margin-bottom: 1.25rem;">
                <p class="muted" style="margin:0;">Adicione pelo menos <strong>dois</strong> registros de peso para ver o gráfico.</p>
            </div>
        @endif

        <div class="grid grid-2">
            <div class="card">
                <h2>Registrar</h2>
                <form method="post" action="{{ route('weight') }}" novalidate>
                    @csrf
                    <div class="form-group">
                        <label for="weighed_at">Data</label>
                        <input id="weighed_at" name="weighed_at" type="date" required value="{{ old('weighed_at', $today) }}">
                    </div>
                    <div class="form-group">
                        <label for="weight_kg">Peso (kg)</label>
                        <input id="weight_kg" name="weight_kg" type="number" min="20" max="400" step="0.1" required placeholder="ex.: 72,5" value="{{ old('weight_kg') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
            <div class="card">
                <h2>Histórico recente</h2>
                @if ($rows->isEmpty())
                    <p class="empty-state">Nenhum registro ainda.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>kg</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $r)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($r->weighed_at)->translatedFormat('d/m/Y') }}</td>
                                        <td>{{ number_format((float)$r->weight_kg, 1, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
