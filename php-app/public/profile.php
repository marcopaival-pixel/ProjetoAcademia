<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/nutrition.php';
require_login($config);

$uid = (int) current_user_id();
$pdo = db($config);
$isPremium = is_current_user_premium($pdo);

$notice = '';
$error = '';

$st = $pdo->prepare(
    'SELECT u.name, u.email, p.birth_date, p.sex, p.height_cm, p.activity_level, p.goal, p.daily_calorie_target,
            p.protein_target_g, p.carbs_target_g, p.fat_target_g, p.water_target_ml
     FROM users u
     LEFT JOIN user_profiles p ON p.user_id = u.id
     WHERE u.id = ? LIMIT 1'
);
$st->execute([$uid]);
$u = $st->fetch();
if (!$u) {
    session_destroy();
    header('Location: ' . url('login.php', $config));
    exit;
}

$wst = $pdo->prepare(
    'SELECT weight_kg, weighed_at FROM weight_entries WHERE user_id = ? ORDER BY weighed_at DESC LIMIT 1'
);
$wst->execute([$uid]);
$latestWeightRow = $wst->fetch();

$calPreview = null;
if ($latestWeightRow && !empty($u['birth_date']) && $u['height_cm'] !== null) {
    $est = nutrition_estimate_target(
        (string) $u['birth_date'],
        (int) $u['height_cm'],
        (string) ($u['sex'] ?? ''),
        (string) ($u['activity_level'] ?? 'moderate'),
        (string) ($u['goal'] ?? 'maintain'),
        (float) $latestWeightRow['weight_kg']
    );
    if ($est['ok']) {
        $calPreview = $est + ['weighed_at' => $latestWeightRow['weighed_at']];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['_csrf'] ?? null)) {
        $error = 'Sessão inválida.';
    } elseif (($_POST['profile_action'] ?? '') === 'password') {
        $cur = (string) ($_POST['current_password'] ?? '');
        $n1 = (string) ($_POST['new_password'] ?? '');
        $n2 = (string) ($_POST['new_password_confirm'] ?? '');
        if ($cur === '' || $n1 === '' || $n2 === '') {
            $error = 'Preencha senha atual e a nova senha duas vezes.';
        } elseif ($n1 !== $n2) {
            $error = 'A nova senha e a confirmação não coincidem.';
        } elseif (strlen($n1) < 8) {
            $error = 'A nova senha deve ter pelo menos 8 caracteres.';
        } else {
            $ph = $pdo->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
            $ph->execute([$uid]);
            $hashRow = $ph->fetch();
            if (!$hashRow || !password_verify($cur, $hashRow['password_hash'])) {
                $error = 'Senha atual incorreta.';
            } else {
                $newHash = password_hash($n1, PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $uid]);
                $notice = 'Senha alterada com sucesso.';
            }
        }
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $birth = (string) ($_POST['birth_date'] ?? '');
        $birthSql = $birth === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth) ? null : $birth;
        $sex = (string) ($_POST['sex'] ?? '');
        if (!in_array($sex, ['', 'M', 'F', 'O'], true)) {
            $sex = '';
        }
        $height = ($_POST['height_cm'] ?? '');
        $heightSql = $height === '' ? null : (int) $height;
        if ($heightSql !== null && ($heightSql < 50 || $heightSql > 260)) {
            $error = 'Altura inválida.';
        }
        $activity = (string) ($_POST['activity_level'] ?? 'moderate');
        $actAllowed = ['sedentary', 'light', 'moderate', 'active', 'very_active'];
        if (!in_array($activity, $actAllowed, true)) {
            $activity = 'moderate';
        }
        $goal = (string) ($_POST['goal'] ?? 'maintain');
        if (!in_array($goal, ['lose', 'gain', 'maintain'], true)) {
            $goal = 'maintain';
        }
        $autoCalorie = !empty($_POST['auto_calorie']);
        $target = ($_POST['daily_calorie_target'] ?? '');
        $targetSql = $target === '' ? null : (int) $target;
        $est = null;

        if ($error === '' && $autoCalorie) {
            $wPost = $pdo->prepare(
                'SELECT weight_kg, weighed_at FROM weight_entries WHERE user_id = ? ORDER BY weighed_at DESC LIMIT 1'
            );
            $wPost->execute([$uid]);
            $lw = $wPost->fetch();
            $lwKg = $lw ? (float) $lw['weight_kg'] : null;
            $est = nutrition_estimate_target($birthSql, $heightSql, $sex, $activity, $goal, $lwKg);
            if (!$est['ok']) {
                $error = $est['message'];
            } else {
                $targetSql = $est['target'];
            }
        }

        if ($error === '' && $targetSql !== null && ($targetSql < 500 || $targetSql > 20000)) {
            $error = 'Meta calórica fora do intervalo (500–20000).';
        }

        $pt = ($_POST['protein_target_g'] ?? '');
        $ct = ($_POST['carbs_target_g'] ?? '');
        $ft = ($_POST['fat_target_g'] ?? '');
        $wt = ($_POST['water_target_ml'] ?? '');
        if ($isPremium) {
            $proteinT = $pt === '' ? null : (float) str_replace(',', '.', (string) $pt);
            $carbsT = $ct === '' ? null : (float) str_replace(',', '.', (string) $ct);
            $fatT = $ft === '' ? null : (float) str_replace(',', '.', (string) $ft);
        } else {
            $proteinT = null;
            $carbsT = null;
            $fatT = null;
        }
        $waterT = $wt === '' ? null : (int) $wt;
        foreach (['Proteína' => $proteinT, 'Carboidrato' => $carbsT, 'Gordura' => $fatT] as $lab => $v) {
            if ($v !== null && ($v < 0 || $v > 600)) {
                $error = "Meta de {$lab} inválida (0–600 g).";
                break;
            }
        }
        if ($error === '' && $waterT !== null && ($waterT < 500 || $waterT > 10000)) {
            $error = 'Meta de água inválida (500–10000 ml).';
        }

        if ($error === '') {
            if ($name === '') {
                $error = 'Nome obrigatório.';
            }
        }

        if ($error === '') {
            $pdo->beginTransaction();
            try {
                $pdo->prepare('UPDATE users SET name = ? WHERE id = ?')->execute([$name, $uid]);
                $chk = $pdo->prepare('SELECT 1 FROM user_profiles WHERE user_id = ?');
                $chk->execute([$uid]);
                if (!$chk->fetch()) {
                    $pdo->prepare('INSERT INTO user_profiles (user_id) VALUES (?)')->execute([$uid]);
                }
                $pdo->prepare(
                    'UPDATE user_profiles SET birth_date = ?, sex = ?, height_cm = ?, activity_level = ?, goal = ?, daily_calorie_target = ?,
                     protein_target_g = ?, carbs_target_g = ?, fat_target_g = ?, water_target_ml = ?
                     WHERE user_id = ?'
                )->execute([$birthSql, $sex, $heightSql, $activity, $goal, $targetSql, $proteinT, $carbsT, $fatT, $waterT, $uid]);
                $pdo->commit();
                $notice = 'Perfil atualizado.';
                if ($autoCalorie && is_array($est) && ($est['ok'] ?? false)) {
                    $bmrR = (int) round($est['bmr']);
                    $tdeeR = (int) round($est['tdee']);
                    $notice .= " Meta estimada: {$est['target']} kcal (TMB ≈ {$bmrR}, gasto estimado ≈ {$tdeeR} kcal/dia).";
                }
                $st->execute([$uid]);
                $u = $st->fetch();
                $wst->execute([$uid]);
                $latestWeightRow = $wst->fetch();
                $calPreview = null;
                if ($latestWeightRow && !empty($u['birth_date']) && $u['height_cm'] !== null) {
                    $estP = nutrition_estimate_target(
                        (string) $u['birth_date'],
                        (int) $u['height_cm'],
                        (string) ($u['sex'] ?? ''),
                        (string) ($u['activity_level'] ?? 'moderate'),
                        (string) ($u['goal'] ?? 'maintain'),
                        (float) $latestWeightRow['weight_kg']
                    );
                    if ($estP['ok']) {
                        $calPreview = $estP + ['weighed_at' => $latestWeightRow['weighed_at']];
                    }
                }
            } catch (Throwable $e) {
                $pdo->rollBack();
                $error = 'Não foi possível salvar.';
            }
        }
    }
}

$title = 'Perfil';
$loggedIn = true;
$navCurrent = 'profile';
require dirname(__DIR__) . '/includes/layout_header.php';
?>
        <h1>Perfil</h1>
        <p class="lead">Altura, idade e peso recente permitem estimar TMB e gasto (TDEE); você pode fixar a meta manualmente ou calcular ao salvar.</p>

        <?php if ($notice !== '') : ?>
            <div class="alert alert-success"><?= h($notice) ?></div>
        <?php endif; ?>
        <?php if ($error !== '') : ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($calPreview !== null) : ?>
            <div class="card" style="max-width: 32rem; margin-bottom: 1rem;">
                <h2 style="margin-top:0;">Prévia da estimativa</h2>
                <p class="muted" style="margin:0 0 0.75rem; font-size:0.9rem;">
                    Com base no peso de <strong><?= h(number_format($calPreview['weight_kg'], 1, ',', '.')) ?> kg</strong>
                    (<?= h((new DateTimeImmutable($calPreview['weighed_at']))->format('d/m/Y')) ?>),
                    idade <?= h((string) $calPreview['age']) ?> anos e dados abaixo:
                </p>
                <ul class="muted" style="margin:0; padding-left:1.25rem; font-size:0.9rem;">
                    <li>TMB (Mifflin–St Jeor): ≈ <?= h((string) (int) round($calPreview['bmr'])) ?> kcal/dia</li>
                    <li>Gasto estimado (TDEE): ≈ <?= h((string) (int) round($calPreview['tdee'])) ?> kcal/dia</li>
                    <li>Meta sugerida para o objetivo atual: <strong><?= h((string) $calPreview['target']) ?> kcal/dia</strong></li>
                </ul>
                <p class="muted" style="margin:0.75rem 0 0; font-size:0.8rem;">Referência geral; não substitui orientação de nutricionista ou médico.</p>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 32rem;">
            <form method="post" novalidate>
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input id="name" name="name" type="text" required maxlength="120" value="<?= h((string) $u['name']) ?>">
                </div>
                <p class="muted" style="margin: 0 0 1rem; font-size: 0.875rem;">Email: <strong><?= h((string) $u['email']) ?></strong> (alteração em breve)</p>
                <div class="form-group">
                    <label for="birth_date">Nascimento</label>
                    <input id="birth_date" name="birth_date" type="date" value="<?= h($u['birth_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="sex">Sexo (opcional)</label>
                    <select id="sex" name="sex">
                        <option value="" <?= (($u['sex'] ?? '') === '') ? ' selected' : '' ?>>—</option>
                        <option value="M" <?= (($u['sex'] ?? '') === 'M') ? ' selected' : '' ?>>Masculino</option>
                        <option value="F" <?= (($u['sex'] ?? '') === 'F') ? ' selected' : '' ?>>Feminino</option>
                        <option value="O" <?= (($u['sex'] ?? '') === 'O') ? ' selected' : '' ?>>Outro / prefiro não informar</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="height_cm">Altura (cm)</label>
                    <input id="height_cm" name="height_cm" type="number" min="50" max="260" placeholder="ex.: 170" value="<?= $u['height_cm'] !== null ? h((string) $u['height_cm']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="activity_level">Nível de atividade</label>
                    <select id="activity_level" name="activity_level">
                        <?php
                        $acts = [
                            'sedentary' => 'Sedentário',
                            'light' => 'Leve',
                            'moderate' => 'Moderado',
                            'active' => 'Ativo',
                            'very_active' => 'Muito ativo',
                        ];
                        $cur = (string) ($u['activity_level'] ?? 'moderate');
                        foreach ($acts as $val => $lab) :
                            ?>
                            <option value="<?= h($val) ?>"<?= $cur === $val ? ' selected' : '' ?>><?= h($lab) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="goal">Objetivo</label>
                    <select id="goal" name="goal">
                        <?php
                        $goals = ['lose' => 'Perder peso', 'gain' => 'Ganhar peso', 'maintain' => 'Manter peso'];
                        $gcur = (string) ($u['goal'] ?? 'maintain');
                        foreach ($goals as $val => $lab) :
                            ?>
                            <option value="<?= h($val) ?>"<?= $gcur === $val ? ' selected' : '' ?>><?= h($lab) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="daily_calorie_target">Meta calórica diária (kcal)</label>
                    <input id="daily_calorie_target" name="daily_calorie_target" type="number" min="500" max="20000" placeholder="ex.: 2000" value="<?= $u['daily_calorie_target'] !== null ? h((string) $u['daily_calorie_target']) : '' ?>">
                </div>
                <?php
                $freeMacroPrev = null;
                if (!$isPremium && $u['daily_calorie_target'] !== null && (int) $u['daily_calorie_target'] > 0) {
                    $freeMacroPrev = nutrition_default_macro_targets_from_kcal((int) $u['daily_calorie_target']);
                }
                ?>
                <?php if ($isPremium) : ?>
                <p class="muted" style="margin: -0.5rem 0 0.75rem; font-size:0.875rem;">Metas de macros (opcional) — usadas no painel <strong>Hoje</strong> e nos totais do diário.</p>
                <div class="form-group">
                    <label for="protein_target_g">Meta proteína (g/dia)</label>
                    <input id="protein_target_g" name="protein_target_g" type="number" min="0" max="600" step="0.1" placeholder="ex.: 120" value="<?= ($u['protein_target_g'] ?? null) !== null ? h((string) (float) $u['protein_target_g']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="carbs_target_g">Meta carboidrato (g/dia)</label>
                    <input id="carbs_target_g" name="carbs_target_g" type="number" min="0" max="600" step="0.1" placeholder="ex.: 200" value="<?= ($u['carbs_target_g'] ?? null) !== null ? h((string) (float) $u['carbs_target_g']) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="fat_target_g">Meta gordura (g/dia)</label>
                    <input id="fat_target_g" name="fat_target_g" type="number" min="0" max="600" step="0.1" placeholder="ex.: 60" value="<?= ($u['fat_target_g'] ?? null) !== null ? h((string) (float) $u['fat_target_g']) : '' ?>">
                </div>
                <?php else : ?>
                <div class="premium-gate" style="margin-top: 1rem; min-height: 13.5rem;">
                    <div class="premium-gate__inner">
                        <p class="muted" style="margin:0 0 0.75rem; font-size:0.875rem;">Metas de macros no plano grátis seguem uma repartição padrão (25% proteína / 45% carboidrato / 30% gordura das suas kcal). <strong>Assine Premium</strong> para definir gramas manualmente (ideal para musculação e acompanhamento fino).</p>
                        <?php if ($freeMacroPrev !== null) : ?>
                            <div class="form-group" style="margin-bottom:0.5rem;"><span class="muted">Proteína (calculada)</span><br><strong class="tabular-nums"><?= h((string) $freeMacroPrev['p']) ?> g/dia</strong></div>
                            <div class="form-group" style="margin-bottom:0.5rem;"><span class="muted">Carboidrato (calculado)</span><br><strong class="tabular-nums"><?= h((string) $freeMacroPrev['c']) ?> g/dia</strong></div>
                            <div class="form-group" style="margin-bottom:0;"><span class="muted">Gordura (calculada)</span><br><strong class="tabular-nums"><?= h((string) $freeMacroPrev['f']) ?> g/dia</strong></div>
                        <?php else : ?>
                            <p class="muted" style="margin:0;">Defina uma meta calórica acima para ver as metas de macro automáticas.</p>
                        <?php endif; ?>
                    </div>
                    <div class="premium-gate__overlay">
                        <span class="premium-gate__crown" aria-hidden="true">👑</span>
                        <p class="premium-gate__head">Recurso Premium</p>
                        <p class="premium-gate__sub">Edite proteína, carbo e gorda como quiser.</p>
                        <a class="btn btn-primary" href="<?= h(url('plano.php', $config)) ?>">Assinar</a>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="water_target_ml">Meta de Água (ml/dia)</label>
                    <input id="water_target_ml" name="water_target_ml" type="number" min="500" max="10000" step="100" placeholder="ex.: 2000" value="<?= ($u['water_target_ml'] ?? null) !== null ? h((string) (int) $u['water_target_ml']) : '' ?>">
                </div>
                <div class="form-group">
                    <label style="display:flex; align-items:flex-start; gap:0.5rem; cursor:pointer; max-width:100%;">
                        <input type="checkbox" name="auto_calorie" value="1" style="width:auto; margin-top:0.2rem;">
                        <span>Ao salvar, <strong>calcular meta automaticamente</strong> (TMB + TDEE + objetivo), usando o peso mais recente.</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>

        <div class="card" style="max-width: 32rem; margin-top: 1.25rem;">
            <h2 style="margin-top:0;">Alterar senha</h2>
            <form method="post" novalidate autocomplete="off">
                <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="profile_action" value="password">
                <div class="form-group">
                    <label for="current_password">Senha atual</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nova senha (mín. 8 caracteres)</label>
                    <input id="new_password" name="new_password" type="password" autocomplete="new-password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Confirmar nova senha</label>
                    <input id="new_password_confirm" name="new_password_confirm" type="password" autocomplete="new-password" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary">Atualizar senha</button>
            </form>
        </div>
<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
