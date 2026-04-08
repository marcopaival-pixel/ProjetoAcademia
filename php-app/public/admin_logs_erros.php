<?php
declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_login($config);

// Em um sistema real, aqui você verificaria se o usuário é Administrador
// Ex: if ($_SESSION['role'] !== 'admin') { header('Location: dashboard.php'); exit; }

$pdo = db($config);

// Ação: Marcar como resolvido ou excluir
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? '')) {
        die('Token inválido');
    }

    $logId = (int)($_POST['log_id'] ?? 0);

    if ($_POST['action'] === 'resolve' && $logId > 0) {
        $stmt = $pdo->prepare("UPDATE logs_erros SET status = 'resolvido' WHERE id = ?");
        $stmt->execute([$logId]);
    } elseif ($_POST['action'] === 'delete' && $logId > 0) {
        $stmt = $pdo->prepare("DELETE FROM logs_erros WHERE id = ?");
        $stmt->execute([$logId]);
    }
    
    // Recarregar com os mesmos filtros
    $query = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . url('admin_logs_erros.php' . $query, $config));
    exit;
}

// Filtros
$filter_type = $_GET['tipo'] ?? '';
$filter_user = $_GET['usuario'] ?? '';
$filter_date = $_GET['data'] ?? '';
$details_id = (int)($_GET['view'] ?? 0);

$where = ["1=1"];
$params = [];

if ($filter_type) {
    if ($filter_type === 'PHP') {
        $where[] = "tipo_erro LIKE 'PHP%'";
    } else {
        $where[] = "tipo_erro = ?";
        $params[] = $filter_type;
    }
}
if ($filter_user) {
    if (is_numeric($filter_user)) {
        $where[] = "usuario_id = ?";
        $params[] = (int)$filter_user;
    } else {
        $where[] = "(u.name LIKE ? OR u.email LIKE ?)";
        $params[] = "%$filter_user%";
        $params[] = "%$filter_user%";
    }
}
if ($filter_date) {
    $where[] = "DATE(data_hora) = ?";
    $params[] = $filter_date;
}

$sql = "
    SELECT l.*, u.name as user_name, u.email as user_email 
    FROM logs_erros l 
    LEFT JOIN users u ON l.usuario_id = u.id 
    WHERE " . implode(' AND ', $where) . "
    ORDER BY l.status DESC, l.data_hora DESC 
    LIMIT 200
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Detalhes do erro selecionado
$selectedLog = null;
if ($details_id > 0) {
    foreach ($logs as $l) {
        if ((int)$l['id'] === $details_id) {
            $selectedLog = $l;
            break;
        }
    }
    // Se não encontrou no set atual, busca no banco
    if (!$selectedLog) {
        $dStmt = $pdo->prepare("SELECT l.*, u.name as user_name, u.email as user_email FROM logs_erros l LEFT JOIN users u ON l.usuario_id = u.id WHERE l.id = ?");
        $dStmt->execute([$details_id]);
        $selectedLog = $dStmt->fetch();
    }
}

// Tipos de erro exclusivos para o filtro
$typesStmt = $pdo->query("SELECT DISTINCT tipo_erro FROM logs_erros ORDER BY tipo_erro");
$errorTypes = $typesStmt->fetchAll(PDO::FETCH_COLUMN);

$title = 'Logs de Erros do Sistema';
$loggedIn = true;
$navCurrent = 'admin_logs';
require dirname(__DIR__) . '/includes/layout_header.php';
?>

<style>
    .admin-container { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    @media (min-width: 992px) {
        .admin-container { grid-template-columns: 350px 1fr; }
    }
    
    .filter-card { padding: 1rem; margin-bottom: 1rem; }
    .log-list { max-height: 70vh; overflow-y: auto; border-radius: 8px; border: 1px solid var(--border-color, #ddd); }
    .log-item { 
        padding: 0.75rem 1rem; 
        border-bottom: 1px solid var(--border-color, #ddd); 
        cursor: pointer; 
        transition: background 0.2s;
        display: block;
        text-decoration: none;
        color: inherit;
    }
    .log-item:hover { background: rgba(0,0,0,0.03); }
    .log-item.active { background: rgba(0,122,255,0.1); border-left: 4px solid #007aff; }
    .log-item.resolvido { opacity: 0.6; }
    
    .status-badge { 
        font-size: 0.7rem; 
        padding: 2px 6px; 
        border-radius: 4px; 
        text-transform: uppercase; 
        font-weight: bold; 
    }
    .status-pendente { background: #ff3b30; color: white; }
    .status-resolvido { background: #34c759; color: white; }
    
    .log-details-card { padding: 1.5rem; }
    .code-block { 
        background: #1c1c1e; 
        color: #f2f2f7; 
        padding: 1rem; 
        border-radius: 6px; 
        overflow-x: auto; 
        font-family: 'Courier New', Courier, monospace;
        margin-top: 0.5rem;
    }
    .meta-info { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0; }
    .meta-info div { font-size: 0.9rem; }
    .meta-info strong { display: block; color: var(--text-muted, #666); font-size: 0.8rem; }
</style>

<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Logs de Erros</h1>
    <a href="<?= h(url('dashboard.php', $config)) ?>" class="btn btn-ghost">Voltar ao Painel</a>
</div>

<div class="admin-container">
    <!-- Lateral: Filtros e Lista -->
    <aside>
        <div class="card filter-card">
            <form method="get" action="">
                <div class="form-group">
                    <label for="tipo">Tipo de Erro</label>
                    <select name="tipo" id="tipo" class="form-control" onchange="this.form.submit()">
                        <option value="">Todos os tipos</option>
                        <option value="PHP" <?= $filter_type === 'PHP' ? 'selected' : '' ?>>Erros PHP (Geral)</option>
                        <?php foreach ($errorTypes as $type): ?>
                            <option value="<?= h($type) ?>" <?= $filter_type === $type ? 'selected' : '' ?>><?= h($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuário (ID ou Nome)</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ex: 1 ou João" value="<?= h($filter_user) ?>">
                </div>
                <div class="form-group">
                    <label for="data">Data</label>
                    <input type="date" name="data" id="data" class="form-control" value="<?= h($filter_date) ?>" onchange="this.form.submit()">
                </div>
                <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Filtrar</button>
                    <a href="<?= h(url('admin_logs_erros.php', $config)) ?>" class="btn btn-ghost" title="Limpar Filtros">✖</a>
                </div>
            </form>
        </div>

        <div class="log-list bg-white">
            <?php if (empty($logs)): ?>
                <div style="padding: 2rem; text-align: center; color: #999;">Nenhum erro encontrado.</div>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <?php 
                        $isActive = (int)$log['id'] === $details_id;
                        $isResolvido = $log['status'] === 'resolvido';
                    ?>
                    <a href="?view=<?= $log['id'] ?><?= $filter_type ? '&tipo='.urlencode($filter_type) : '' ?><?= $filter_user ? '&usuario='.urlencode($filter_user) : '' ?><?= $filter_date ? '&data='.urlencode($filter_date) : '' ?>" 
                       class="log-item <?= $isActive ? 'active' : '' ?> <?= $isResolvido ? 'resolvido' : '' ?>">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span class="status-badge status-<?= $log['status'] ?>"><?= h($log['status']) ?></span>
                            <span class="muted" style="font-size: 0.75rem;"><?= h((new DateTimeImmutable($log['data_hora']))->format('d/m/H:i')) ?></span>
                        </div>
                        <strong style="display: block; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= h($log['tipo_erro']) ?>
                        </strong>
                        <div class="muted" style="font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= h($log['mensagem']) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Central: Detalhes -->
    <main>
        <?php if ($selectedLog): ?>
            <div class="card log-details-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <div>
                        <span class="status-badge status-<?= $selectedLog['status'] ?>" style="font-size: 0.85rem; padding: 4px 10px;"><?= h($selectedLog['status']) ?></span>
                        <h2 style="margin: 0.5rem 0 0 0; color: #d32f2f;"><?= h($selectedLog['tipo_erro']) ?></h2>
                        <p class="muted"><?= h((new DateTimeImmutable($selectedLog['data_hora']))->format('d/m/Y \à\s H:i:s')) ?></p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <?php if ($selectedLog['status'] !== 'resolvido'): ?>
                        <form method="post" onsubmit="return confirm('Marcar este erro como resolvido?')">
                            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="log_id" value="<?= $selectedLog['id'] ?>">
                            <button type="submit" name="action" value="resolve" class="btn btn-success">Resolver</button>
                        </form>
                        <?php endif; ?>
                        <form method="post" onsubmit="return confirm('Excluir este log permanentemente?')">
                            <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="log_id" value="<?= $selectedLog['id'] ?>">
                            <button type="submit" name="action" value="delete" class="btn btn-ghost" style="color: #d32f2f;">Excluir</button>
                        </form>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mensagem</label>
                    <div class="alert alert-danger" style="background: rgba(211, 47, 47, 0.05); color: #c62828; border: 1px solid rgba(211, 47, 47, 0.1);">
                        <?= h($selectedLog['mensagem']) ?>
                    </div>
                </div>

                <div class="meta-info">
                    <div>
                        <strong>Arquivo</strong>
                        <?= h($selectedLog['arquivo'] ?: 'N/A') ?>
                    </div>
                    <div>
                        <strong>Linha</strong>
                        <?= h((string)($selectedLog['linha'] ?: 'N/A')) ?>
                    </div>
                    <div>
                        <strong>Usuário</strong>
                        <?php if ($selectedLog['usuario_id']): ?>
                            <span title="<?= h($selectedLog['user_email']) ?>"><?= h($selectedLog['user_name']) ?> (ID: <?= $selectedLog['usuario_id'] ?>)</span>
                        <?php else: ?>
                            <span class="muted">Visitante (Anon)</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong>Endereço IP</strong>
                        <?= h($selectedLog['ip'] ?: 'N/A') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>URL da Ocorrência</label>
                    <div class="muted" style="background: #f8f9fa; padding: 0.5rem; border-radius: 4px; border: 1px solid #eee; font-size: 0.85rem; word-break: break-all;">
                        <?= h($selectedLog['url'] ?: 'N/A') ?>
                    </div>
                </div>

                <!-- Simulação de Stack Trace se tivéssemos formatado no log -->
                <!-- <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Dados Técnicos</label>
                    <pre class="code-block"><?= h(print_r($selectedLog, true)) ?></pre>
                </div> -->
            </div>
        <?php else: ?>
            <div class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 400px; text-align: center; color: #999;">
                <div style="font-size: 4rem; opacity: 0.2; margin-bottom: 1rem;">🔍</div>
                <h3>Selecione um erro na lista para ver os detalhes</h3>
                <p>Use os filtros laterais para refinar sua busca.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php
require dirname(__DIR__) . '/includes/layout_footer.php';
