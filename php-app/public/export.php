<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/csv_export.php';
require_once dirname(__DIR__) . '/includes/export_range.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);
$isPremium = is_current_user_premium($pdo);
$kind = (string) ($_GET['kind'] ?? '');

if ($kind !== '') {
    if (!$isPremium) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Exportação em CSV é um recurso Premium. Abra Meu Plano para assinar.';
        exit;
    }
    $parsed = export_parse_range($_GET['from'] ?? null, $_GET['to'] ?? null);
    if (!$parsed['ok']) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo $parsed['message'];
        exit;
    }
    $range = ['from' => $parsed['from'], 'to' => $parsed['to']];
    $pf = $parsed['from'];
    $pt = $parsed['to'];

    $rangeSuffix = '';
    if ($pf !== null || $pt !== null) {
        $rangeSuffix = '_' . ($pf ?? 'inicio') . '_a_' . ($pt ?? 'fim');
    }
    $stamp = (new DateTimeImmutable('now'))->format('Y-m-d_His');

    if ($kind === 'food') {
        $extra = export_sql_date_range_clause('entry_date', $range);
        $sql = 'SELECT id, entry_date, meal_type, food_name, calories, protein_g, carbs_g, fat_g, created_at
             FROM food_entries WHERE user_id = ?' . $extra['clause'] . ' ORDER BY entry_date ASC, id ASC';
        $st = $pdo->prepare($sql);
        $st->execute(array_merge([$uid], $extra['params']));
        $rows = [];
        foreach ($st->fetchAll() as $r) {
            $rows[] = [
                $r['id'],
                $r['entry_date'],
                $r['meal_type'],
                $r['food_name'],
                $r['calories'],
                $r['protein_g'],
                $r['carbs_g'],
                $r['fat_g'],
                $r['created_at'],
            ];
        }
        send_csv_download(
            'projetoacademia_alimentacao' . $rangeSuffix . '_' . $stamp . '.csv',
            ['id', 'data', 'refeicao', 'alimento', 'kcal', 'proteina_g', 'carbo_g', 'gordura_g', 'criado_em'],
            $rows
        );
        exit;
    }

    if ($kind === 'exercise') {
        $extra = export_sql_date_range_clause('entry_date', $range);
        $sql = 'SELECT id, entry_date, activity_type, duration_min, calories_burned, notes, created_at
             FROM exercise_entries WHERE user_id = ?' . $extra['clause'] . ' ORDER BY entry_date ASC, id ASC';
        $st = $pdo->prepare($sql);
        $st->execute(array_merge([$uid], $extra['params']));
        $rows = [];
        foreach ($st->fetchAll() as $r) {
            $rows[] = [
                $r['id'],
                $r['entry_date'],
                $r['activity_type'],
                $r['duration_min'],
                $r['calories_burned'] ?? '',
                $r['notes'] ?? '',
                $r['created_at'],
            ];
        }
        send_csv_download(
            'projetoacademia_exercicios' . $rangeSuffix . '_' . $stamp . '.csv',
            ['id', 'data', 'atividade', 'minutos', 'kcal_gasto', 'observacoes', 'criado_em'],
            $rows
        );
        exit;
    }

    if ($kind === 'weight') {
        $extra = export_sql_date_range_clause('weighed_at', $range);
        $sql = 'SELECT id, weighed_at, weight_kg, created_at FROM weight_entries WHERE user_id = ?'
            . $extra['clause'] . ' ORDER BY weighed_at ASC, id ASC';
        $st = $pdo->prepare($sql);
        $st->execute(array_merge([$uid], $extra['params']));
        $rows = [];
        foreach ($st->fetchAll() as $r) {
            $rows[] = [$r['id'], $r['weighed_at'], $r['weight_kg'], $r['created_at']];
        }
        send_csv_download(
            'projetoacademia_peso' . $rangeSuffix . '_' . $stamp . '.csv',
            ['id', 'data', 'peso_kg', 'criado_em'],
            $rows
        );
        exit;
    }

    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Tipo de exportação inválido.';
    exit;
}

$formFrom = (string) ($_GET['f_from'] ?? '');
$formTo = (string) ($_GET['f_to'] ?? '');

$title = 'Exportar dados';
$loggedIn = true;
$navCurrent = 'export';
require dirname(__DIR__) . '/includes/layout_header.php';

$base = url('export.php', $config);
$queryBase = ($formFrom !== '' ? '&from=' . rawurlencode($formFrom) : '') . ($formTo !== '' ? '&to=' . rawurlencode($formTo) : '');
?>
        <h1>Exportar dados</h1>
        <?php if (!$isPremium) : ?>
            <div class="premium-paywall premium-paywall--page">
                <div class="premium-paywall__icon" aria-hidden="true">👑</div>
                <p class="premium-paywall__title">Recurso Premium</p>
                <p class="premium-paywall__text">Exporte alimentação, exercícios e peso em CSV para enviar à nutricionista ou analisar no Excel. Assine para liberar os downloads.</p>
                <a class="btn btn-primary" href="<?= h(url('plano.php', $config)) ?>">Ver planos e assinar</a>
            </div>
            <p style="margin-top:1rem;"><a class="btn btn-ghost" href="<?= h(url('report.php', $config)) ?>">Relatório semanal (visualização no app)</a></p>
        <?php else : ?>
        <p class="lead">Baixe seus registros em CSV (UTF-8, separador <strong>;</strong>, compatível com Excel em PT-BR).</p>

        <div class="card" style="max-width: 36rem; margin-bottom: 1.25rem;">
            <h2 style="margin-top:0;">Filtrar por período (opcional)</h2>
            <form method="get" class="export-filter-form">
                <div class="form-group">
                    <label for="f_from">De</label>
                    <input id="f_from" name="f_from" type="date" value="<?= h($formFrom) ?>">
                </div>
                <div class="form-group">
                    <label for="f_to">Até</label>
                    <input id="f_to" name="f_to" type="date" value="<?= h($formTo) ?>">
                </div>
                <button type="submit" class="btn btn-ghost">Aplicar filtro nos links abaixo</button>
            </form>
            <p class="muted" style="margin:0.75rem 0 0; font-size:0.875rem;">Deixe em branco para incluir <strong>todos</strong> os registros. Depois use os botões de download com o período aplicado.</p>
        </div>

        <div class="card" style="max-width: 36rem;">
            <h2 style="margin-top:0;">Download</h2>
            <ul class="export-list muted" style="margin:0; padding-left:1.15rem; line-height:2;">
                <li><a class="btn btn-primary" style="display:inline-block; margin:0.25rem 0;" href="<?= h($base . '?kind=food' . $queryBase) ?>">Alimentação (CSV)</a></li>
                <li><a class="btn btn-primary" style="display:inline-block; margin:0.25rem 0;" href="<?= h($base . '?kind=exercise' . $queryBase) ?>">Exercícios (CSV)</a></li>
                <li><a class="btn btn-primary" style="display:inline-block; margin:0.25rem 0;" href="<?= h($base . '?kind=weight' . $queryBase) ?>">Peso (CSV)</a></li>
            </ul>
            <p class="muted" style="margin:1rem 0 0; font-size:0.875rem;">Os arquivos contêm apenas os dados da sua conta.</p>
        </div>
        <p style="margin-top:1rem;"><a class="btn btn-ghost" href="<?= h(url('report.php', $config)) ?>">Ver relatório semanal</a></p>
        <?php endif; ?>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
