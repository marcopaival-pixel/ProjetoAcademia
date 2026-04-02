<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/macro_helpers.php';
require_once dirname(__DIR__) . '/includes/nutrition.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);
$isPremium = is_current_user_premium($pdo);

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
    $notice = 'Registro adicionado.';
} elseif ($flash === 'removed') {
    $notice = 'Item removido.';
} elseif ($flash === 'updated') {
    $notice = 'Registro atualizado.';
} elseif ($flash === 'copied') {
    $n = (int) ($_GET['n'] ?? 0);
    $notice = $n > 0 ? 'Copiado(s) ' . $n . ' item(ns) de outro dia.' : 'Registros copiados.';
}

$editId = (int) ($_GET['edit'] ?? 0);
$editRow = null;
if ($editId > 0) {
    $es = $pdo->prepare(
        'SELECT id, meal_type, food_name, calories, protein_g, carbs_g, fat_g FROM food_entries
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
    } elseif (($_POST['action'] ?? '') === 'copy_day') {
        $targetDate = (string) ($_POST['target_date'] ?? '');
        $sourceDate = (string) ($_POST['source_date'] ?? '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $sourceDate)) {
            $error = 'Datas inválidas.';
        } elseif ($sourceDate === $targetDate) {
            $error = 'O dia de origem deve ser diferente do dia do diário.';
        } else {
            $src = $pdo->prepare(
                'SELECT meal_type, food_name, calories, protein_g, carbs_g, fat_g FROM food_entries WHERE user_id = ? AND entry_date = ?'
            );
            $src->execute([$uid, $sourceDate]);
            $items = $src->fetchAll();
            if (count($items) === 0) {
                $error = 'Não há alimentos no dia de origem.';
            } else {
                $ins = $pdo->prepare(
                    'INSERT INTO food_entries (user_id, entry_date, meal_type, food_name, calories, protein_g, carbs_g, fat_g) VALUES (?,?,?,?,?,?,?,?)'
                );
                foreach ($items as $it) {
                    $ins->execute([
                        $uid,
                        $targetDate,
                        $it['meal_type'],
                        $it['food_name'],
                        $it['calories'],
                        $it['protein_g'],
                        $it['carbs_g'],
                        $it['fat_g'],
                    ]);
                }
                header(
                    'Location: ' . url(
                        'diary.php?date=' . rawurlencode($targetDate) . '&flash=copied&n=' . count($items),
                        $config
                    ),
                    true,
                    303
                );
                exit;
            }
        }
    } elseif (($_POST['action'] ?? '') === 'delete_food') {
        $delDate = (string) ($_POST['entry_date'] ?? '');
        $fid = (int) ($_POST['food_id'] ?? 0);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $delDate)) {
            $error = 'Data inválida.';
        } elseif ($fid <= 0) {
            $error = 'Item inválido.';
        } else {
            $del = $pdo->prepare(
                'DELETE FROM food_entries WHERE id = ? AND user_id = ? AND entry_date = ?'
            );
            $del->execute([$fid, $uid, $delDate]);
            if ($del->rowCount() === 0) {
                $error = 'Não foi possível excluir o item.';
            } else {
                header(
                    'Location: ' . url('diary.php?date=' . rawurlencode($delDate) . '&flash=removed', $config),
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
            $meal = (string) ($_POST['meal_type'] ?? 'other');
            $allowed = ['breakfast', 'lunch', 'dinner', 'snack', 'other'];
            if (!in_array($meal, $allowed, true)) {
                $meal = 'other';
            }
            $name = trim((string) ($_POST['food_name'] ?? ''));
            $calories = (int) ($_POST['calories'] ?? 0);
            $p = (float) ($_POST['protein_g'] ?? 0);
            $c = (float) ($_POST['carbs_g'] ?? 0);
            $f = (float) ($_POST['fat_g'] ?? 0);
            $foodEditId = (int) ($_POST['food_edit_id'] ?? 0);
            if ($name === '') {
                $error = 'Informe o nome do alimento.';
            } elseif ($calories < 0 || $calories > 20000) {
                $error = 'Calorias fora do intervalo esperado.';
            } elseif ($foodEditId > 0) {
                $own = $pdo->prepare(
                    'SELECT id FROM food_entries WHERE id = ? AND user_id = ? AND entry_date = ? LIMIT 1'
                );
                $own->execute([$foodEditId, $uid, $date]);
                if (!$own->fetch()) {
                    $error = 'Item não encontrado.';
                } else {
                    $upd = $pdo->prepare(
                        'UPDATE food_entries SET meal_type = ?, food_name = ?, calories = ?, protein_g = ?, carbs_g = ?, fat_g = ?
                         WHERE id = ? AND user_id = ? AND entry_date = ?'
                    );
                    $upd->execute([$meal, $name, $calories, $p, $c, $f, $foodEditId, $uid, $date]);
                    header(
                        'Location: ' . url('diary.php?date=' . rawurlencode($date) . '&flash=updated', $config),
                        true,
                        303
                    );
                    exit;
                }
            } else {
                $ins = $pdo->prepare(
                    'INSERT INTO food_entries (user_id, entry_date, meal_type, food_name, calories, protein_g, carbs_g, fat_g)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $ins->execute([$uid, $date, $meal, $name, $calories, $p, $c, $f]);
                header(
                    'Location: ' . url('diary.php?date=' . rawurlencode($date) . '&flash=added', $config),
                    true,
                    303
                );
                exit;
            }
        }
    }
}

$list = $pdo->prepare(
    'SELECT id, meal_type, food_name, calories, protein_g, carbs_g, fat_g, created_at
     FROM food_entries WHERE user_id = ? AND entry_date = ? ORDER BY created_at ASC, id ASC'
);
$list->execute([$uid, $date]);
$rows = $list->fetchAll();

$sumStmt = $pdo->prepare('SELECT COALESCE(SUM(calories),0), COALESCE(SUM(protein_g),0), COALESCE(SUM(carbs_g),0), COALESCE(SUM(fat_g),0) FROM food_entries WHERE user_id = ? AND entry_date = ?');
$sumStmt->execute([$uid, $date]);
$sums = $sumStmt->fetch(PDO::FETCH_NUM);
$sumCal = (int) $sums[0];
$sumP = (float) $sums[1];
$sumC = (float) $sums[2];
$sumF = (float) $sums[3];

$profStmt = $pdo->prepare(
    'SELECT daily_calorie_target, protein_target_g, carbs_target_g, fat_target_g FROM user_profiles WHERE user_id = ?'
);
$profStmt->execute([$uid]);
$macroProf = $profStmt->fetch() ?: [];
$macroTargets = nutrition_macro_targets_for_display($isPremium, $macroProf);
$hasMacroTargets = $isPremium
    ? (($macroTargets['p'] ?? 0) > 0 || ($macroTargets['c'] ?? 0) > 0 || ($macroTargets['f'] ?? 0) > 0)
    : (isset($macroProf['daily_calorie_target']) && $macroProf['daily_calorie_target'] !== null && (int) $macroProf['daily_calorie_target'] > 0);

$mealLabels = [
    'breakfast' => 'Café da manhã',
    'lunch' => 'Almoço',
    'dinner' => 'Jantar',
    'snack' => 'Lanche',
    'other' => 'Outro',
];

$formMeal = (string) ($editRow['meal_type'] ?? 'other');
if (!in_array($formMeal, ['breakfast', 'lunch', 'dinner', 'snack', 'other'], true)) {
    $formMeal = 'other';
}

$title = 'Alimentação';
$loggedIn = true;
$navCurrent = 'diary';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Diário alimentar</h1>
        <p class="lead">Registre refeições e veja o total do dia.</p>

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
                <h2><?= $editRow ? 'Editar registro' : 'Adicionar' ?></h2>
                <?php if ($editRow) : ?>
                    <p class="muted" style="margin:0 0 1rem; font-size:0.875rem;">
                        <a href="<?= h(url('diary.php?date=' . rawurlencode($date), $config)) ?>">Cancelar edição</a>
                    </p>
                <?php endif; ?>
                <form method="post" novalidate>
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="entry_date" value="<?= h($date) ?>">
                    <?php if ($editRow) : ?>
                        <input type="hidden" name="food_edit_id" value="<?= h((string) $editRow['id']) ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="meal_type">Refeição</label>
                        <select id="meal_type" name="meal_type">
                            <?php foreach ($mealLabels as $k => $lab) : ?>
                                <option value="<?= h($k) ?>"<?= (string) $formMeal === $k ? ' selected' : '' ?>><?= h($lab) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="border: 1px solid #e0e0e0; padding: 1rem; border-radius: 0.25rem; background: #fafafa; margin-bottom: 1rem;">
                        <h3 style="margin: 0 0 0.75rem; font-size: 0.9375rem; font-weight: 600;">🔍 Buscar Alimento</h3>
                        <div style="display: grid; gap: 0.5rem;">
                            <div>
                                <label for="food_barcode" style="font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Código de Barras (EAN)</label>
                                <div style="display: grid; grid-template-columns: 1fr auto; gap: 0.5rem;">
                                    <input id="food_barcode" type="text" placeholder="Escanear ou digitar código" style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 0.25rem;">
                                    <button type="button" id="food_barcode_btn" class="btn btn-ghost" style="padding: 0.5rem 1rem;">Escanear</button>
                                </div>
                            </div>
                            <div id="food-lookup-notification"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="food_name">Alimento</label>
                        <input id="food_name" name="food_name" type="text" required maxlength="200" placeholder="Ex.: Arroz integral (ou busca automática acima)" value="<?= h($editRow['food_name'] ?? '') ?>">
                        <p style="font-size: 0.8125rem; color: #666; margin: 0.25rem 0 0; margin-top: 0.5rem;">💡 Digite para autocomplete (mín. 2 caracteres)</p>
                    </div>
                    <div class="form-group">
                        <label for="calories">Calorias (kcal)</label>
                        <input id="calories" name="calories" type="number" min="0" max="20000" required value="<?= h((string) ($editRow['calories'] ?? '0')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="protein_g">Proteína (g)</label>
                        <input id="protein_g" name="protein_g" type="number" min="0" step="0.1" value="<?= h((string) ($editRow['protein_g'] ?? '0')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="carbs_g">Carboidrato (g)</label>
                        <input id="carbs_g" name="carbs_g" type="number" min="0" step="0.1" value="<?= h((string) ($editRow['carbs_g'] ?? '0')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="fat_g">Gordura (g)</label>
                        <input id="fat_g" name="fat_g" type="number" min="0" step="0.1" value="<?= h((string) ($editRow['fat_g'] ?? '0')) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editRow ? 'Atualizar' : 'Salvar' ?></button>
                </form>
                <?php if (!$editRow) : ?>
                    <div class="copy-day-form">
                        <h3 class="muted" style="margin:0 0 0.5rem; font-size:0.9375rem; font-weight:600;">Copiar de outro dia</h3>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="action" value="copy_day">
                            <input type="hidden" name="target_date" value="<?= h($date) ?>">
                            <div class="form-group" style="margin-bottom:0;">
                                <label for="source_date">Origem</label>
                                <input id="source_date" name="source_date" type="date" required>
                            </div>
                            <button type="submit" class="btn btn-ghost btn-sm">Copiar para <?= h((new DateTimeImmutable($date))->format('d/m')) ?></button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card">
                <h2>Totais do dia</h2>
                <p style="margin:0 0 0.5rem;"><strong><?= h((string) $sumCal) ?> kcal</strong></p>
                <p class="muted" style="margin:0;">P <?= h(number_format($sumP, 1, ',', '.')) ?> g ·
                    C <?= h(number_format($sumC, 1, ',', '.')) ?> g ·
                    F <?= h(number_format($sumF, 1, ',', '.')) ?> g</p>
                <?php if ($hasMacroTargets) : ?>
                    <div class="macro-grid" style="margin-top:1rem; gap:0.75rem;">
                        <?php
                        $mb = [
                            ['P', 'Proteína', $sumP, $macroTargets['p'], '#3d9cf5'],
                            ['C', 'Carbo', $sumC, $macroTargets['c'], '#34c759'],
                            ['G', 'Gordura', $sumF, $macroTargets['f'], '#ff9f0a'],
                        ];
                        foreach ($mb as $row) :
                            [$ab, $lb, $cur, $tgt, $col] = $row;
                            $pc = macro_bar_percent($cur, $tgt);
                            ?>
                            <?php if ($pc !== null) : ?>
                                <div class="macro-item">
                                    <div class="macro-item-head">
                                        <span class="macro-abbr" style="color:<?= h($col) ?>"><?= h($ab) ?></span>
                                        <span class="macro-label"><?= h($lb) ?></span>
                                    </div>
                                    <div class="macro-bar" style="--macro-fill: <?= h($col) ?>"><span style="width: <?= h((string) $pc) ?>%;"></span></div>
                                    <p class="macro-stat muted" style="margin:0.25rem 0 0;"><?= h(number_format($cur, 1, ',', '.')) ?> / <?= h(number_format((float) $tgt, 1, ',', '.')) ?> g</p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (count($rows) === 0) : ?>
                    <p class="empty-state">Nada registrado neste dia.</p>
                <?php else : ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Refeição</th>
                                    <th>Item</th>
                                    <th>kcal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r) : ?>
                                    <tr>
                                        <td><?= h($mealLabels[$r['meal_type']] ?? $r['meal_type']) ?></td>
                                        <td><?= h($r['food_name']) ?></td>
                                        <td><?= h((string) $r['calories']) ?></td>
                                        <td class="td-fit">
                                            <div class="row-actions">
                                                <a class="btn btn-ghost btn-sm" href="<?= h(url('diary.php?date=' . rawurlencode($date) . '&edit=' . (int) $r['id'], $config)) ?>">Editar</a>
                                                <form method="post" class="form-row-delete" onsubmit="return confirm('Remover este item?');">
                                                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="delete_food">
                                                    <input type="hidden" name="entry_date" value="<?= h($date) ?>">
                                                    <input type="hidden" name="food_id" value="<?= h((string) $r['id']) ?>">
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
<script src="<?= h(url('js/food-lookup.js', $config)) ?>"></script>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
