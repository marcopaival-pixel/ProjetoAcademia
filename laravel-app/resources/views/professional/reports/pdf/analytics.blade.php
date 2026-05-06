<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
        .header h1 { color: #10b981; margin: 0; text-transform: uppercase; }
        .header p { font-size: 12px; color: #666; margin-top: 5px; }
        .summary { margin-bottom: 30px; }
        .summary-card { display: inline-block; width: 30%; border: 1px solid #eee; padding: 15px; border-radius: 10px; margin-right: 2%; text-align: center; }
        .summary-card span { display: block; font-size: 10px; color: #999; text-transform: uppercase; margin-bottom: 5px; }
        .summary-card b { font-size: 18px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f9fafb; padding: 12px; text-align: left; font-size: 10px; text-transform: uppercase; color: #666; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 11px; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NexShape <span style="color:#111">Pro</span></h1>
        <p>{{ $title }} • Gerado em {{ now()->format('d/m/Y H:i') }}</p>
        <p>Profissional: {{ $user->name }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <span>Alunos Ativos</span>
            <b>{{ $data['active_students'] }} / {{ $data['total_students'] }}</b>
        </div>
        <div class="summary-card">
            <span>Média Aderência Nutri</span>
            <b>{{ $data['avg_adherence_food'] }}%</b>
        </div>
        <div class="summary-card">
            <span>Média Aderência Treino</span>
            <b>{{ $data['avg_adherence_training'] }}%</b>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Treinos</th>
                <th>Dias Nutri</th>
                <th>Aderência Treino</th>
                <th>Aderência Nutri</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['students_data'] as $s)
            <tr>
                <td>
                    <b>{{ $s['name'] }}</b><br>
                    <small style="color:#999">{{ $s['email'] }}</small>
                </td>
                <td>{{ $s['workouts'] }}</td>
                <td>{{ $s['food_days'] }}</td>
                <td>{{ $s['adherence_training'] }}%</td>
                <td>{{ $s['adherence_food'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        NexShape Academy — Plataforma de Gestão de Performance de Alta Performance<br>
        Relatório confidencial gerado por {{ $user->name }}
    </div>
</body>
</html>
