<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/chart_weight.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);

$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $error = 'Sessão inválida.';
    } else {
        $day = (string) ($_POST['weighed_at'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            $error = 'Data inválida.';
        } else {
            $w = (float) str_replace(',', '.', (string) ($_POST['weight_kg'] ?? '0'));
            if ($w < 20 || $w > 400) {
                $error = 'Peso fora do intervalo esperado (20–400 kg).';
            } else {
                $find = $pdo->prepare('SELECT id FROM weight_entries WHERE user_id = ? AND weighed_at = ?');
                $find->execute([$uid, $day]);
                $existing = $find->fetch();
                if ($existing) {
                    $upd = $pdo->prepare('UPDATE weight_entries SET weight_kg = ? WHERE id = ? AND user_id = ?');
                    $upd->execute([$w, $existing['id'], $uid]);
                    $notice = 'Peso do dia atualizado.';
                } else {
                    $ins = $pdo->prepare('INSERT INTO weight_entries (user_id, weighed_at, weight_kg) VALUES (?, ?, ?)');
                    $ins->execute([$uid, $day, $w]);
                    $notice = 'Peso registrado.';
                }
            }
        }
    }
}

$list = $pdo->prepare(
    'SELECT weighed_at, weight_kg FROM weight_entries WHERE user_id = ? ORDER BY weighed_at DESC LIMIT 60'
);
$list->execute([$uid]);
$rows = $list->fetchAll();

$chartSeries = array_reverse($rows);
$weightChartHtml = weight_chart_svg($chartSeries);

$today = (new DateTimeImmutable('today'))->format('Y-m-d');

$title = 'Peso';
$loggedIn = true;
$navCurrent = 'weight';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Peso</h1>
        <p class="lead">Um registro por dia; nova entrada no mesmo dia substitui o anterior.</p>

        <?php if ($notice !== '') : ?>
            <div class="alert alert-success"><?= h($notice) ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($weightChartHtml !== '') : ?>
            <div class="card weight-chart" style="margin-bottom: 1.25rem;">
                <h2 style="margin-top:0;">Evolução (últimos registros)</h2>
                <p class="muted" style="margin:0 0 0.75rem; font-size:0.875rem;">Ordem cronológica; eixo horizontal são as datas da primeira à última amostra abaixo.</p>
                <?= $weightChartHtml ?>
            </div>
        <?php elseif (count($rows) === 1) : ?>
            <div class="card" style="margin-bottom: 1.25rem;">
                <p class="muted" style="margin:0;">Adicione pelo menos <strong>dois</strong> registros de peso para ver o gráfico.</p>
            </div>
        <?php endif; ?>

        <div class="grid grid-2">
            <div class="card">
                <h2>Registrar</h2>
                <form method="post" novalidate>
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <div class="form-group">
                        <label for="weighed_at">Data</label>
                        <input id="weighed_at" name="weighed_at" type="date" required value="<?= h($today) ?>">
                    </div>
                    <div class="form-group">
                        <label for="weight_kg">Peso (kg)</label>
                        <input id="weight_kg" name="weight_kg" type="number" min="20" max="400" step="0.1" required placeholder="ex.: 72,5">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
            <div class="card">
                <h2>Histórico recente</h2>
                <?php if (count($rows) === 0) : ?>
                    <p class="empty-state">Nenhum registro ainda.</p>
                <?php else : ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>kg</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) : ?>
                                    <tr>
                                        <td><?= h((new DateTimeImmutable($r['weighed_at']))->format('d/m/Y')) ?></td>
                                        <td><?= h(number_format((float) $r['weight_kg'], 1, ',', '.')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
