<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/macro_helpers.php';
require_once dirname(__DIR__) . '/includes/nutrition.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);
$isPremium = is_current_user_premium($pdo);
$today = (new DateTimeImmutable('today'))->format('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['water_add'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        die('Token inválido');
    }
    $amount_ml = (int) $_POST['water_add'];
    if ($amount_ml > 0) {
        $insStmt = $pdo->prepare('INSERT INTO water_entries (user_id, entry_date, amount_ml) VALUES (?, ?, ?)');
        $insStmt->execute([$uid, $today, $amount_ml]);
    }
    header('Location: ' . url('dashboard.php', $config));
    exit;
}

$targetStmt = $pdo->prepare(
    'SELECT daily_calorie_target, protein_target_g, carbs_target_g, fat_target_g, water_target_ml FROM user_profiles WHERE user_id = ?'
);
$targetStmt->execute([$uid]);
$prof = $targetStmt->fetch() ?: [];
$calorieTarget = isset($prof['daily_calorie_target']) && $prof['daily_calorie_target'] !== null
    ? (int) $prof['daily_calorie_target'] : null;
$waterTarget = isset($prof['water_target_ml']) && $prof['water_target_ml'] !== null
    ? (int) $prof['water_target_ml'] : 2000;
$macroTargets = nutrition_macro_targets_for_display($isPremium, $prof);
$hasMacroTargets = $isPremium
    ? (($macroTargets['p'] ?? 0) > 0 || ($macroTargets['c'] ?? 0) > 0 || ($macroTargets['f'] ?? 0) > 0)
    : ($calorieTarget !== null);

$foodStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(calories), 0), COALESCE(SUM(protein_g), 0), COALESCE(SUM(carbs_g), 0), ' .
    'COALESCE(SUM(fat_g), 0) FROM food_entries WHERE user_id = ? AND entry_date = ?'
);
$foodStmt->execute([$uid, $today]);
$foodSums = $foodStmt->fetch(PDO::FETCH_NUM);
$consumed = (int) $foodSums[0];
$sumProt = (float) $foodSums[1];
$sumCarb = (float) $foodSums[2];
$sumFat = (float) $foodSums[3];

$burnStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(calories_burned), 0) FROM exercise_entries WHERE user_id = ? AND entry_date = ? AND calories_burned IS NOT NULL'
);
$burnStmt->execute([$uid, $today]);
$burned = (int) $burnStmt->fetchColumn();

$remaining = $calorieTarget !== null ? $calorieTarget - $consumed + $burned : null;

$weightStmt = $pdo->prepare(
    'SELECT weight_kg, weighed_at FROM weight_entries WHERE user_id = ? ORDER BY weighed_at DESC LIMIT 1'
);
$weightStmt->execute([$uid]);
$lastWeight = $weightStmt->fetch();

$waterStmt = $pdo->prepare('SELECT COALESCE(SUM(amount_ml), 0) FROM water_entries WHERE user_id = ? AND entry_date = ?');
$waterStmt->execute([$uid, $today]);
$waterConsumed = (int) $waterStmt->fetchColumn();

$title = 'Hoje';
$loggedIn = true;
$navCurrent = 'dashboard';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Hoje</h1>
        <p class="lead"><?= h((new DateTimeImmutable($today))->format('d/m/Y')) ?> — resumo rápido do dia.</p>

        <section class="stats" aria-label="Resumo calórico">
            <div class="stat">
                <p class="stat-label">Meta (kcal)</p>
                <p class="stat-value"><?= $calorieTarget !== null ? h((string) $calorieTarget) : '—' ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Consumidas</p>
                <p class="stat-value"><?= h((string) $consumed) ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Gastas (est.)</p>
                <p class="stat-value"><?= h((string) $burned) ?></p>
            </div>
            <div class="stat">
                <p class="stat-label">Saldo (meta − consumo + gasto)</p>
                <p class="stat-value"><?= $remaining !== null ? h((string) $remaining) : '—' ?></p>
            </div>
        </section>

        <?php if ($calorieTarget === null) : ?>
            <div class="alert alert-success">
                Defina sua <strong>meta calórica diária</strong> em <a href="<?= h(url('profile.php', $config)) ?>">Perfil</a> para ver o saldo.
            </div>
        <?php endif; ?>

        <?php if ($hasMacroTargets) : ?>
            <section class="card macro-section" aria-label="Macronutrientes hoje">
                <h2 style="margin-top:0;">Macros hoje</h2>
                <div class="macro-grid">
                    <?php
                    $rows = [
                        ['P', 'Proteína', $sumProt, $macroTargets['p'], '#3d9cf5'],
                        ['C', 'Carboidrato', $sumCarb, $macroTargets['c'], '#34c759'],
                        ['G', 'Gordura', $sumFat, $macroTargets['f'], '#ff9f0a'],
                    ];
                    foreach ($rows as $row) :
                        [$abbr, $label, $cur, $tgt, $color] = $row;
                        $pct = macro_bar_percent($cur, $tgt);
                        ?>
                        <div class="macro-item">
                            <div class="macro-item-head">
                                <span class="macro-abbr" style="color:<?= h($color) ?>"><?= h($abbr) ?></span>
                                <span class="macro-label"><?= h($label) ?></span>
                            </div>
                            <?php if ($pct !== null) : ?>
                                <div class="macro-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= h((string) $pct) ?>" style="--macro-fill: <?= h($color) ?>">
                                    <span style="width: <?= h((string) $pct) ?>%;"></span>
                                </div>
                                <p class="macro-stat muted"><?= h(number_format($cur, 1, ',', '.')) ?> / <?= h(number_format((float) $tgt, 1, ',', '.')) ?> g</p>
                            <?php else : ?>
                                <p class="macro-stat muted"><?= h(number_format($cur, 1, ',', '.')) ?> g · <a href="<?= h(url('profile.php', $config)) ?>">definir meta</a></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <div class="card">
            <h2>Último peso registrado</h2>
            <?php if ($lastWeight) : ?>
                <p style="margin:0;"><strong><?= h(number_format((float) $lastWeight['weight_kg'], 1, ',', '.')) ?> kg</strong>
                    <span class="muted"> em <?= h((new DateTimeImmutable($lastWeight['weighed_at']))->format('d/m/Y')) ?></span></p>
            <?php else : ?>
                <p class="empty-state">Nenhum peso ainda. Registre em <a href="<?= h(url('weight.php', $config)) ?>">Peso</a>.</p>
            <?php endif; ?>
        </div>

        <section class="card water-section" aria-label="Controle de Água">
            <h2 style="margin-top:0;">💦 Água hoje</h2>
            
            <?php $waterPct = min(100, $waterTarget > 0 ? ($waterConsumed / $waterTarget) * 100 : 0); ?>
            
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                <strong><?= h(number_format($waterConsumed / 1000, 2, ',', '.')) ?>L</strong> / <?= h(number_format($waterTarget / 1000, 2, ',', '.')) ?>L
            </div>
            
            <div class="water-bar-container" style="background: rgba(0,0,0,0.05); height: 16px; border-radius: 8px; overflow: hidden; margin-bottom: 1rem; border: 1px solid rgba(0,0,0,0.1);">
                <div class="water-bar-fill" style="width: <?= h((string) $waterPct) ?>%; background: #007aff; height: 100%; transition: width 0.3s ease;"></div>
            </div>

            <form method="post" action="<?= h(url('dashboard.php', $config)) ?>" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <button type="submit" name="water_add" value="250" class="btn btn-ghost" style="flex: 1; padding: 0.5rem; text-align: center;">💧 +250ml</button>
                <button type="submit" name="water_add" value="500" class="btn btn-ghost" style="flex: 1; padding: 0.5rem; text-align: center;">🍼 +500ml</button>
            </form>
        </section>

        <div class="actions-inline" style="margin-top: 1.25rem;">
            <a class="btn btn-primary" href="<?= h(url('diary.php', $config)) ?>">Registrar refeição</a>
            <a class="btn btn-ghost" href="<?= h(url('exercise.php', $config)) ?>">Registrar exercício</a>
        </div>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
