<?php
/**
 * PAGTREM - CRUD Trens (Admin)
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$pdo = getConnection();
$errors = [];
$editTrem = null;

// =============================================
// AÇÃO: Excluir trem
// =============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM trens WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Trem excluído com sucesso!');
    } catch (PDOException $e) {
        error_log("Erro ao excluir trem: " . $e->getMessage());
        setFlash('error', 'Erro ao excluir trem.');
    }
    redirect('trains.php');
}

// =============================================
// AÇÃO: Editar trem (carrega dados)
// =============================================
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM trens WHERE id = ?");
    $stmt->execute([$id]);
    $editTrem = $stmt->fetch();
}

// =============================================
// AÇÃO: Processar formulário (Create/Update)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    
    $nome = trim($_POST['nome'] ?? '');
    $status = $_POST['status'] ?? 'ativo';
    $capacidade = isset($_POST['capacidade']) ? (int) $_POST['capacidade'] : 0;
    $notas = trim($_POST['notas'] ?? '');
    
    // Validações
    if (strlen($nome) < 2) {
        $errors[] = 'Nome do trem deve ter pelo menos 2 caracteres.';
    }
    if ($capacidade < 0) {
        $errors[] = 'Capacidade não pode ser negativa.';
    }
    
    if (empty($errors)) {
        try {
            if ($action === 'create') {
                $stmt = $pdo->prepare("
                    INSERT INTO trens (nome, status, capacidade, notas) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$nome, $status, $capacidade, $notas]);
                setFlash('success', 'Trem cadastrado com sucesso!');
            } else {
                $stmt = $pdo->prepare("
                    UPDATE trens 
                    SET nome = ?, status = ?, capacidade = ?, notas = ?
                    WHERE id = ?
                ");
                $stmt->execute([$nome, $status, $capacidade, $notas, $id]);
                setFlash('success', 'Trem atualizado com sucesso!');
            }
            redirect('trains.php');
        } catch (PDOException $e) {
            error_log("Erro ao salvar trem: " . $e->getMessage());
            $errors[] = 'Erro ao salvar trem.';
        }
    }
}

// =============================================
// Lista de trens
// =============================================
$trens = $pdo->query("SELECT * FROM trens ORDER BY data_criacao DESC")->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Trens</title>
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
                    <?= $editTrem ? '✏️ Editar Trem' : '➕ Novo Trem' ?>
                </h2>
                <?php if ($editTrem): ?>
                    <a href="trains.php" class="btn btn-secondary btn-sm">Cancelar</a>
                <?php endif; ?>
            </div>
            
            <form method="POST" data-validate>
                <input type="hidden" name="action" value="<?= $editTrem ? 'update' : 'create' ?>">
                <?php if ($editTrem): ?>
                    <input type="hidden" name="id" value="<?= $editTrem['id'] ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="nome">Nome do Trem</label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            class="form-control" 
                            placeholder="Ex: Trem Expresso 001"
                            value="<?= e($editTrem['nome'] ?? $_POST['nome'] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="capacidade">Capacidade</label>
                        <input 
                            type="number" 
                            id="capacidade" 
                            name="capacidade" 
                            class="form-control" 
                            placeholder="Número de passageiros"
                            value="<?= e($editTrem['capacidade'] ?? $_POST['capacidade'] ?? '0') ?>"
                            min="0"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control" style="max-width: 200px;">
                        <option value="ativo" <?= ($editTrem['status'] ?? 'ativo') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                        <option value="inativo" <?= ($editTrem['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                        <option value="manutencao" <?= ($editTrem['status'] ?? '') === 'manutencao' ? 'selected' : '' ?>>Manutenção</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="notas">Notas/Observações</label>
                    <textarea 
                        id="notas" 
                        name="notas" 
                        class="form-control" 
                        rows="3"
                        placeholder="Observações sobre o trem..."
                    ><?= e($editTrem['notas'] ?? $_POST['notas'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $editTrem ? 'Atualizar' : 'Cadastrar' ?>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Lista de Trens -->
        <div class="card">
            <h2 class="card-title">🚆 Trens Cadastrados</h2>
            
            <?php if (empty($trens)): ?>
                <div class="empty-state">
                    <div class="icon">🚆</div>
                    <p>Nenhum trem cadastrado.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Capacidade</th>
                                <th>Notas</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trens as $trem): ?>
                                <tr>
                                    <td><?= $trem['id'] ?></td>
                                    <td><strong><?= e($trem['nome']) ?></strong></td>
                                    <td>
                                        <?php
                                        $badgeClass = match($trem['status']) {
                                            'ativo' => 'badge-success',
                                            'inativo' => 'badge-danger',
                                            'manutencao' => 'badge-warning',
                                            default => 'badge-info'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= ucfirst($trem['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= $trem['capacidade'] ?> passageiros</td>
                                    <td><?= e(substr($trem['notas'] ?? '', 0, 50)) ?><?= strlen($trem['notas'] ?? '') > 50 ? '...' : '' ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($trem['data_criacao'])) ?></td>
                                    <td class="actions">
                                        <div class="btn-group">
                                            <a href="?edit=<?= $trem['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                            <a 
                                                href="?delete=<?= $trem['id'] ?>" 
                                                class="btn btn-danger btn-sm"
                                                data-confirm="Tem certeza que deseja excluir este trem?"
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

