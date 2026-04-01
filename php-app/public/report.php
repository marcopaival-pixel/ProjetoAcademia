<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);

$end = new DateTimeImmutable('today');
$start = $end->modify('-6 days');

$d0 = $start->format('Y-m-d');
$d1 = $end->format('Y-m-d');

$days = [];
for ($d = $start; $d <= $end; $d = $d->modify('+1 day')) {
    $key = $d->format('Y-m-d');
    $days[$key] = [
        'label' => $d->format('d/m D'),
        'kcal_in' => 0,
        'ex_min' => 0,
        'ex_kcal' => 0,
        'weight' => null,
    ];
}

$f = $pdo->prepare(
    'SELECT entry_date, COALESCE(SUM(calories), 0) AS c FROM food_entries WHERE user_id = ? AND entry_date BETWEEN ? AND ? GROUP BY entry_date'
);
$f->execute([$uid, $d0, $d1]);
foreach ($f->fetchAll() as $r) {
    $k = $r['entry_date'];
    if (isset($days[$k])) {
        $days[$k]['kcal_in'] = (int) $r['c'];
    }
}

$e = $pdo->prepare(
    'SELECT entry_date, COALESCE(SUM(duration_min), 0) AS dm, COALESCE(SUM(calories_burned), 0) AS bk
     FROM exercise_entries WHERE user_id = ? AND entry_date BETWEEN ? AND ? GROUP BY entry_date'
);
$e->execute([$uid, $d0, $d1]);
foreach ($e->fetchAll() as $r) {
    $k = $r['entry_date'];
    if (isset($days[$k])) {
        $days[$k]['ex_min'] = (int) $r['dm'];
        $days[$k]['ex_kcal'] = (int) $r['bk'];
    }
}

$w = $pdo->prepare(
    'SELECT weighed_at, weight_kg FROM weight_entries WHERE user_id = ? AND weighed_at BETWEEN ? AND ? ORDER BY weighed_at ASC'
);
$w->execute([$uid, $d0, $d1]);
foreach ($w->fetchAll() as $r) {
    $k = $r['weighed_at'];
    if (isset($days[$k])) {
        $days[$k]['weight'] = (float) $r['weight_kg'];
    }
}

$totalKcal = 0;
$daysWithFood = 0;
$totalExMin = 0;
$totalExKcal = 0;
foreach ($days as $info) {
    if ($info['kcal_in'] > 0) {
        $totalKcal += $info['kcal_in'];
        $daysWithFood++;
    }
    $totalExMin += $info['ex_min'];
    $totalExKcal += $info['ex_kcal'];
}

$avgKcal = $daysWithFood > 0 ? (int) round($totalKcal / $daysWithFood) : 0;

$wFirst = $pdo->prepare(
    'SELECT weight_kg, weighed_at FROM weight_entries WHERE user_id = ? AND weighed_at <= ? ORDER BY weighed_at DESC LIMIT 1'
);
$wFirst->execute([$uid, $d1]);
$lastBefore = $wFirst->fetch();

$wPeriod = $pdo->prepare(
    'SELECT weight_kg, weighed_at FROM weight_entries WHERE user_id = ? AND weighed_at BETWEEN ? AND ? ORDER BY weighed_at ASC'
);
$wPeriod->execute([$uid, $d0, $d1]);
$weightsInPeriod = $wPeriod->fetchAll();

$deltaWeight = null;
if (count($weightsInPeriod) >= 2) {
    $first = (float) $weightsInPeriod[0]['weight_kg'];
    $last = (float) $weightsInPeriod[count($weightsInPeriod) - 1]['weight_kg'];
    $deltaWeight = round($last - $first, 2);
} elseif (count($weightsInPeriod) === 1 && $lastBefore && $lastBefore['weighed_at'] < $d0) {
    $deltaWeight = round((float) $weightsInPeriod[0]['weight_kg'] - (float) $lastBefore['weight_kg'], 2);
}

$title = 'Relatório semanal';
$loggedIn = true;
$navCurrent = 'report';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Relatório — últimos 7 dias</h1>
        <p class="lead">De <?= h($start->format('d/m/Y')) ?> a <?= h($end->format('d/m/Y')) ?>.</p>

        <div class="stats" aria-label="Resumo da semana" style="margin-bottom: 1.25rem;">
            <div class="stat">
                <p class="stat-label">Média kcal/dia (dias com registro)</p>
                <p class="stat-value"><?= $daysWithFood > 0 ? h((string) $avgKcal) : '—' ?></p>
                <p class="muted" style="margin:0.25rem 0 0; font-size:0.8rem;"><?= h((string) $daysWithFood) ?> dia(s) com alimentação</p>
            </div>
            <div class="stat">
                <p class="stat-label">Total exercício</p>
                <p class="stat-value tabular-nums"><?= h((string) $totalExMin) ?> min</p>
                <p class="muted" style="margin:0.25rem 0 0; font-size:0.8rem;">Σ kcal informadas: <?= h((string) $totalExKcal) ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Variação de peso (período)</p>
                <p class="stat-value tabular-nums"><?= $deltaWeight !== null ? h(sprintf('%+.2f', $deltaWeight)) . ' kg' : '—' ?></p>
                <p class="muted" style="margin:0.25rem 0 0; font-size:0.8rem;">Primeira vs última pesagem na semana (ou vs peso anterior)</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="margin-top:0;">Por dia</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>kcal</th>
                            <th>Ex. (min)</th>
                            <th>Ex. kcal</th>
                            <th>Peso (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($days as $info) : ?>
                            <tr>
                                <td><?= h($info['label']) ?></td>
                                <td class="tabular-nums"><?= $info['kcal_in'] > 0 ? h((string) $info['kcal_in']) : '—' ?></td>
                                <td class="tabular-nums"><?= $info['ex_min'] > 0 ? h((string) $info['ex_min']) : '—' ?></td>
                                <td class="tabular-nums"><?= $info['ex_kcal'] > 0 ? h((string) $info['ex_kcal']) : '—' ?></td>
                                <td class="tabular-nums"><?= $info['weight'] !== null ? h(number_format($info['weight'], 1, ',', '.')) : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="actions-inline">
            <a class="btn btn-primary" href="<?= h(url('export.php', $config)) ?>">Exportar CSV</a>
            <a class="btn btn-ghost" href="<?= h(url('dashboard.php', $config)) ?>">Voltar ao hoje</a>
        </div>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
