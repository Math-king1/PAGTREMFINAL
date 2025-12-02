<?php
/**
 * PAGTREM - CRUD Notificações (Admin)
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$pdo = getConnection();
$errors = [];
$editNotificacao = null;

// =============================================
// AÇÃO: Excluir notificação
// =============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM notificacoes WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Notificação excluída com sucesso!');
    } catch (PDOException $e) {
        error_log("Erro ao excluir notificação: " . $e->getMessage());
        setFlash('error', 'Erro ao excluir notificação.');
    }
    redirect('notifications.php');
}

// =============================================
// AÇÃO: Editar notificação (carrega dados)
// =============================================
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM notificacoes WHERE id = ?");
    $stmt->execute([$id]);
    $editNotificacao = $stmt->fetch();
}

// =============================================
// AÇÃO: Processar formulário (Create/Update)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    
    $titulo = trim($_POST['titulo'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    $data_notificacao = $_POST['data_notificacao'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'ativa';
    
    // Validações
    if (strlen($titulo) < 3) {
        $errors[] = 'Título deve ter pelo menos 3 caracteres.';
    }
    if (strlen($mensagem) < 5) {
        $errors[] = 'Mensagem deve ter pelo menos 5 caracteres.';
    }
    if (empty($data_notificacao)) {
        $errors[] = 'Data é obrigatória.';
    }
    
    if (empty($errors)) {
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("
                    INSERT INTO notificacoes (titulo, mensagem, data_notificacao, status) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$titulo, $mensagem, $data_notificacao, $status]);
                setFlash('success', 'Notificação criada com sucesso!');
            } else {
                $stmt = $pdo->prepare("
                    UPDATE notificacoes 
                    SET titulo = ?, mensagem = ?, data_notificacao = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->execute([$titulo, $mensagem, $data_notificacao, $status, $id]);
                setFlash('success', 'Notificação atualizada com sucesso!');
            }
            redirect('notifications.php');
        } catch (PDOException $e) {
            error_log("Erro ao salvar notificação: " . $e->getMessage());
            $errors[] = 'Erro ao salvar notificação.';
        }
    }
}

// =============================================
// Lista de notificações
// =============================================
$notificacoes = $pdo->query("SELECT * FROM notificacoes ORDER BY data_notificacao DESC, data_criacao DESC")->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Notificações</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>PAGTREM</h1>
            <nav class="header-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php">Usuários</a>
                <a href="trains.php">Trens</a>
                <a href="notifications.php">Notificações</a>
                <a href="../logout.php">Sair</a>
            </nav>
        </div>
    </header>
    
    <div class="container">
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?= implode('<br>', array_map('e', $errors)) ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulário de Cadastro/Edição -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <?= $editNotificacao ? '✏️ Editar Notificação' : '➕ Nova Notificação' ?>
                </h2>
                <?php if ($editNotificacao): ?>
                    <a href="notifications.php" class="btn btn-secondary btn-sm">Cancelar</a>
                <?php endif; ?>
            </div>
            
            <form method="POST" data-validate>
                <input type="hidden" name="action" value="<?= $editNotificacao ? 'update' : 'create' ?>">
                <?php if ($editNotificacao): ?>
                    <input type="hidden" name="id" value="<?= $editNotificacao['id'] ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="titulo">Título</label>
                        <input 
                            type="text" 
                            id="titulo" 
                            name="titulo" 
                            class="form-control" 
                            placeholder="Título da notificação"
                            value="<?= e($editNotificacao['titulo'] ?? $_POST['titulo'] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required" for="data_notificacao">Data</label>
                        <input 
                            type="date" 
                            id="data_notificacao" 
                            name="data_notificacao" 
                            class="form-control" 
                            value="<?= e($editNotificacao['data_notificacao'] ?? $_POST['data_notificacao'] ?? date('Y-m-d')) ?>"
                            required
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="mensagem">Mensagem</label>
                    <textarea 
                        id="mensagem" 
                        name="mensagem" 
                        class="form-control" 
                        rows="4"
                        placeholder="Conteúdo da notificação..."
                        required
                    ><?= e($editNotificacao['mensagem'] ?? $_POST['mensagem'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control" style="max-width: 200px;">
                        <option value="ativa" <?= ($editNotificacao['status'] ?? 'ativa') === 'ativa' ? 'selected' : '' ?>>Ativa</option>
                        <option value="inativa" <?= ($editNotificacao['status'] ?? '') === 'inativa' ? 'selected' : '' ?>>Inativa</option>
                    </select>
                </div>
                
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $editNotificacao ? 'Atualizar' : 'Cadastrar' ?>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Lista de Notificações -->
        <div class="card">
            <h2 class="card-title">🔔 Notificações Cadastradas</h2>
            
            <?php if (empty($notificacoes)): ?>
                <div class="empty-state">
                    <div class="icon">🔔</div>
                    <p>Nenhuma notificação cadastrada.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Mensagem</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notificacoes as $notif): ?>
                                <tr>
                                    <td><?= $notif['id'] ?></td>
                                    <td><strong><?= e($notif['titulo']) ?></strong></td>
                                    <td><?= e(substr($notif['mensagem'], 0, 60)) ?><?= strlen($notif['mensagem']) > 60 ? '...' : '' ?></td>
                                    <td><?= date('d/m/Y', strtotime($notif['data_notificacao'])) ?></td>
                                    <td>
                                        <span class="badge <?= $notif['status'] === 'ativa' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= ucfirst($notif['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($notif['data_criacao'])) ?></td>
                                    <td class="actions">
                                        <div class="btn-group">
                                            <a href="?edit=<?= $notif['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                            <a 
                                                href="?delete=<?= $notif['id'] ?>" 
                                                class="btn btn-danger btn-sm"
                                                data-confirm="Tem certeza que deseja excluir esta notificação?"
                                            >Excluir</a>
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
    
    <script src="../../assets/js/main.js"></script>
</body>
</html>

