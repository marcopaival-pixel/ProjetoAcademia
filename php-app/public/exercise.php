<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);

$dateRaw = (string) ($_GET['date'] ?? '');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateRaw)) {
    $date = (new DateTimeImmutable('today'))->format('Y-m-d');
} else {
    $date = $dateRaw;
}

$notice = '';
$error = '';

$flash = (string) ($_GET['flash'] ?? '');
if ($flash === 'added') {
    $notice = 'Exercício registrado.';
} elseif ($flash === 'removed') {
    $notice = 'Exercício removido.';
} elseif ($flash === 'updated') {
    $notice = 'Exercício atualizado.';
} elseif ($flash === 'copied') {
    $n = (int) ($_GET['n'] ?? 0);
    $notice = $n > 0 ? 'Copiado(s) ' . $n . ' exercício(s) de outro dia.' : 'Registros copiados.';
}

$editId = (int) ($_GET['edit'] ?? 0);
$editRow = null;
if ($editId > 0) {
    $es = $pdo->prepare(
        'SELECT id, activity_type, duration_min, calories_burned, notes FROM exercise_entries
         WHERE id = ? AND user_id = ? AND entry_date = ? LIMIT 1'
    );
    $es->execute([$editId, $uid, $date]);
    $editRow = $es->fetch() ?: null;
    if (!$editRow) {
        $editId = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $error = 'Sessão inválida.';
    } elseif (($_POST['action'] ?? '') === 'copy_exercises') {
        $targetDate = (string) ($_POST['target_date'] ?? '');
        $sourceDate = (string) ($_POST['source_date'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $sourceDate)) {
            $error = 'Datas inválidas.';
        } elseif ($sourceDate === $targetDate) {
            $error = 'O dia de origem deve ser diferente do dia selecionado.';
        } else {
            $src = $pdo->prepare(
                'SELECT activity_type, duration_min, calories_burned, notes FROM exercise_entries WHERE user_id = ? AND entry_date = ?'
            );
            $src->execute([$uid, $sourceDate]);
            $items = $src->fetchAll();
            if (count($items) === 0) {
                $error = 'Não há exercícios no dia de origem.';
            } else {
                $ins = $pdo->prepare(
                    'INSERT INTO exercise_entries (user_id, entry_date, activity_type, duration_min, calories_burned, notes)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                foreach ($items as $it) {
                    $ins->execute([
                        $uid,
                        $targetDate,
                        $it['activity_type'],
                        $it['duration_min'],
                        $it['calories_burned'],
                        $it['notes'],
                    ]);
                }
                header(
                    'Location: ' . url(
                        'exercise.php?date=' . rawurlencode($targetDate) . '&flash=copied&n=' . count($items),
                        $config
                    ),
                    true,
                    303
                );
                exit;
            }
        }
    } elseif (($_POST['action'] ?? '') === 'delete_exercise') {
        $delDate = (string) ($_POST['entry_date'] ?? '');
        $eid = (int) ($_POST['exercise_id'] ?? 0);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $delDate)) {
            $error = 'Data inválida.';
        } elseif ($eid <= 0) {
            $error = 'Registro inválido.';
        } else {
            $del = $pdo->prepare(
                'DELETE FROM exercise_entries WHERE id = ? AND user_id = ? AND entry_date = ?'
            );
            $del->execute([$eid, $uid, $delDate]);
            if ($del->rowCount() === 0) {
                $error = 'Não foi possível excluir.';
            } else {
                header(
                    'Location: ' . url('exercise.php?date=' . rawurlencode($delDate) . '&flash=removed', $config),
                    true,
                    303
                );
                exit;
            }
        }
    } else {
        $date = (string) ($_POST['entry_date'] ?? $date);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $error = 'Data inválida.';
        } else {
            $type = trim((string) ($_POST['activity_type'] ?? ''));
            $dur = (int) ($_POST['duration_min'] ?? 0);
            $cb = ($_POST['calories_burned'] ?? '');
            $cbVal = $cb === '' ? null : (int) $cb;
            $notes = trim((string) ($_POST['notes'] ?? ''));
            $exEditId = (int) ($_POST['exercise_edit_id'] ?? 0);
            if ($type === '') {
                $error = 'Informe o tipo de atividade.';
            } elseif ($dur < 0 || $dur > 1440) {
                $error = 'Duração inválida (minutos).';
            } elseif ($exEditId > 0) {
                $own = $pdo->prepare(
                    'SELECT id FROM exercise_entries WHERE id = ? AND user_id = ? AND entry_date = ? LIMIT 1'
                );
                $own->execute([$exEditId, $uid, $date]);
                if (!$own->fetch()) {
                    $error = 'Registro não encontrado.';
                } else {
                    $upd = $pdo->prepare(
                        'UPDATE exercise_entries SET activity_type = ?, duration_min = ?, calories_burned = ?, notes = ?
                         WHERE id = ? AND user_id = ? AND entry_date = ?'
                    );
                    $upd->execute([$type, $dur, $cbVal, $notes === '' ? null : substr($notes, 0, 500), $exEditId, $uid, $date]);
                    header(
                        'Location: ' . url('exercise.php?date=' . rawurlencode($date) . '&flash=updated', $config),
                        true,
                        303
                    );
                    exit;
                }
            } else {
                $ins = $pdo->prepare(
                    'INSERT INTO exercise_entries (user_id, entry_date, activity_type, duration_min, calories_burned, notes)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $ins->execute([$uid, $date, $type, $dur, $cbVal, $notes === '' ? null : substr($notes, 0, 500)]);
                header(
                    'Location: ' . url('exercise.php?date=' . rawurlencode($date) . '&flash=added', $config),
                    true,
                    303
                );
                exit;
            }
        }
    }
}

$list = $pdo->prepare(
    'SELECT id, activity_type, duration_min, calories_burned, notes, created_at
     FROM exercise_entries WHERE user_id = ? AND entry_date = ? ORDER BY created_at ASC, id ASC'
);
$list->execute([$uid, $date]);
$rows = $list->fetchAll();

$sumStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(duration_min), 0), COALESCE(SUM(calories_burned), 0) FROM exercise_entries WHERE user_id = ? AND entry_date = ?'
);
$sumStmt->execute([$uid, $date]);
$sumRow = $sumStmt->fetch(PDO::FETCH_NUM);
$sumMin = (int) $sumRow[0];
$sumBurn = (int) $sumRow[1];

$title = 'Exercícios';
$loggedIn = true;
$navCurrent = 'exercise';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Exercícios</h1>
        <p class="lead">Atividades do dia (gasto calórico opcional).</p>

        <?php if ($notice !== '') : ?>
            <div class="alert alert-success"><?= h($notice) ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <form method="get" class="form-group" style="margin-bottom: 1.25rem;">
            <label for="date">Dia</label>
            <input id="date" name="date" type="date" value="<?= h($date) ?>" onchange="this.form.submit()">
        </form>

        <div class="grid grid-2">
            <div class="card">
                <h2><?= $editRow ? 'Editar exercício' : 'Adicionar' ?></h2>
                <?php if ($editRow) : ?>
                    <p class="muted" style="margin:0 0 1rem; font-size:0.875rem;">
                        <a href="<?= h(url('exercise.php?date=' . rawurlencode($date), $config)) ?>">Cancelar edição</a>
                    </p>
                <?php endif; ?>
                <form method="post" novalidate>
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="entry_date" value="<?= h($date) ?>">
                    <?php if ($editRow) : ?>
                        <input type="hidden" name="exercise_edit_id" value="<?= h((string) $editRow['id']) ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="activity_type">Atividade</label>
                        <input id="activity_type" name="activity_type" type="text" required maxlength="120" placeholder="Ex.: Caminhada, musculação" value="<?= h($editRow['activity_type'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="duration_min">Duração (min)</label>
                        <input id="duration_min" name="duration_min" type="number" min="0" max="1440" required value="<?= h((string) ($editRow['duration_min'] ?? '30')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="calories_burned">Gasto estimado (kcal), opcional</label>
                        <input id="calories_burned" name="calories_burned" type="number" min="0" max="5000" placeholder="vazio se não souber" value="<?= $editRow && $editRow['calories_burned'] !== null ? h((string) $editRow['calories_burned']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="notes">Observações</label>
                        <textarea id="notes" name="notes" maxlength="500" placeholder="Opcional"><?= h($editRow['notes'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editRow ? 'Atualizar' : 'Salvar' ?></button>
                </form>
                <?php if (!$editRow) : ?>
                    <div class="copy-day-form">
                        <h3 class="muted" style="margin:0 0 0.5rem; font-size:0.9375rem; font-weight:600;">Copiar de outro dia</h3>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="action" value="copy_exercises">
                            <input type="hidden" name="target_date" value="<?= h($date) ?>">
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="exercise_source_date">Origem</label>
                                <input id="exercise_source_date" name="source_date" type="date" required>
                            </div>
                            <button type="submit" class="btn btn-ghost btn-sm">Copiar para <?= h((new DateTimeImmutable($date))->format('d/m')) ?></button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card">
                <h2>Resumo do dia</h2>
                <p style="margin:0 0 0.5rem;"><strong><?= h((string) $sumMin) ?> min</strong> de atividade</p>
                <p class="muted" style="margin:0 0 1rem;">Soma de kcal informadas: <strong><?= h((string) $sumBurn) ?></strong></p>
                <?php if (count($rows) === 0) : ?>
                    <p class="empty-state">Nenhum exercício neste dia.</p>
                <?php else : ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Atividade</th>
                                    <th>Min</th>
                                    <th>kcal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) : ?>
                                    <tr>
                                        <td><?= h($r['activity_type']) ?></td>
                                        <td><?= h((string) $r['duration_min']) ?></td>
                                        <td><?= $r['calories_burned'] !== null ? h((string) $r['calories_burned']) : '—' ?></td>
                                        <td class="td-fit">
                                            <div class="row-actions">
                                                <a class="btn btn-ghost btn-sm" href="<?= h(url('exercise.php?date=' . rawurlencode($date) . '&edit=' . (int) $r['id'], $config)) ?>">Editar</a>
                                                <form method="post" class="form-row-delete" onsubmit="return confirm('Remover este exercício?');">
                                                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="delete_exercise">
                                                    <input type="hidden" name="entry_date" value="<?= h($date) ?>">
                                                    <input type="hidden" name="exercise_id" value="<?= h((string) $r['id']) ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
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
