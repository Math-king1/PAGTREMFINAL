<?php
/**
 * PAGTREM - CRUD Usuários (Admin)
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$user = $_SESSION['user'];
$pdo = getConnection();
$errors = [];
$editUser = null;

// =============================================
// AÇÃO: Excluir usuário
// =============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    
    // Não permite excluir a si mesmo
    if ($id === $user['id']) {
        setFlash('error', 'Você não pode excluir sua própria conta.');
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', 'Usuário excluído com sucesso!');
        } catch (PDOException $e) {
            error_log("Erro ao excluir usuário: " . $e->getMessage());
            setFlash('error', 'Erro ao excluir usuário.');
        }
    }
    redirect('users.php');
}

// =============================================
// AÇÃO: Editar usuário (carrega dados)
// =============================================
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch();
}

// =============================================
// AÇÃO: Processar formulário (Create/Update)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    
    $username = trim($_POST['username'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $status = $_POST['status'] ?? 'ativo';
    $senha = $_POST['senha'] ?? '';
    
    // Validações
    if (strlen($username) < 3) {
        $errors[] = 'Nome de usuário deve ter pelo menos 3 caracteres.';
    }
    if (strlen($nome) < 3) {
        $errors[] = 'Nome completo deve ter pelo menos 3 caracteres.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido.';
    }
    if ($action === 'create' && strlen($senha) < 4) {
        $errors[] = 'Senha deve ter pelo menos 4 caracteres.';
    }
    
    // Verifica duplicidade de username
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ? AND id != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Este nome de usuário já está em uso.';
    }
    
    // Verifica duplicidade de email
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $errors[] = 'Este e-mail já está cadastrado.';
    }
    
    if (empty($errors)) {
        try {
            if ($action === 'create') {
                // Criar novo usuário
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (username, nome_completo, email, telefone, senha, role, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$username, $nome, $email, $telefone, $senhaHash, $role, $status]);
                setFlash('success', 'Usuário criado com sucesso!');
            } else {
                // Atualizar usuário
                if (!empty($senha)) {
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("
                        UPDATE usuarios 
                        SET username = ?, nome_completo = ?, email = ?, telefone = ?, senha = ?, role = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $nome, $email, $telefone, $senhaHash, $role, $status, $id]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE usuarios 
                        SET username = ?, nome_completo = ?, email = ?, telefone = ?, role = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $nome, $email, $telefone, $role, $status, $id]);
                }
                setFlash('success', 'Usuário atualizado com sucesso!');
            }
            redirect('users.php');
        } catch (PDOException $e) {
            error_log("Erro ao salvar usuário: " . $e->getMessage());
            $errors[] = 'Erro ao salvar usuário.';
        }
    }
}

// =============================================
// Lista de usuários
// =============================================
$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY data_criacao DESC")->fetchAll();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Usuários</title>
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
                    <?= $editUser ? '✏️ Editar Usuário' : '➕ Novo Usuário' ?>
                </h2>
                <?php if ($editUser): ?>
                    <a href="users.php" class="btn btn-secondary btn-sm">Cancelar</a>
                <?php endif; ?>
            </div>
            
            <form method="POST" data-validate>
                <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">
                <?php if ($editUser): ?>
                    <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="username">Nome de Usuário</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            value="<?= e($editUser['username'] ?? $_POST['username'] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required" for="nome">Nome Completo</label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            class="form-control" 
                            value="<?= e($editUser['nome_completo'] ?? $_POST['nome'] ?? '') ?>"
                            required
                        >
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required" for="email">E-mail</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            value="<?= e($editUser['email'] ?? $_POST['email'] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="telefone">Telefone</label>
                        <input 
                            type="text" 
                            id="telefone" 
                            name="telefone" 
                            class="form-control" 
                            value="<?= e($editUser['telefone'] ?? $_POST['telefone'] ?? '') ?>"
                        >
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label <?= $editUser ? '' : 'required' ?>" for="senha">
                            Senha <?= $editUser ? '(deixe em branco para manter)' : '' ?>
                        </label>
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            class="form-control" 
                            <?= $editUser ? '' : 'required' ?>
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="role">Função</label>
                        <select id="role" name="role" class="form-control">
                            <option value="user" <?= ($editUser['role'] ?? '') === 'user' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin" <?= ($editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control" style="max-width: 200px;">
                        <option value="ativo" <?= ($editUser['status'] ?? 'ativo') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                        <option value="inativo" <?= ($editUser['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>
                
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $editUser ? 'Atualizar' : 'Cadastrar' ?>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Lista de Usuários -->
        <div class="card">
            <h2 class="card-title">👥 Usuários Cadastrados</h2>
            
            <?php if (empty($usuarios)): ?>
                <div class="empty-state">
                    <div class="icon">👥</div>
                    <p>Nenhum usuário cadastrado.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Função</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><strong><?= e($u['username']) ?></strong></td>
                                    <td><?= e($u['nome_completo']) ?></td>
                                    <td><?= e($u['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $u['role'] === 'admin' ? 'badge-info' : 'badge-success' ?>">
                                            <?= $u['role'] === 'admin' ? 'Admin' : 'Usuário' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $u['status'] === 'ativo' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= ucfirst($u['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($u['data_criacao'])) ?></td>
                                    <td class="actions">
                                        <div class="btn-group">
                                            <a href="?edit=<?= $u['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                                            <?php if ($u['id'] !== $user['id']): ?>
                                                <a 
                                                    href="?delete=<?= $u['id'] ?>" 
                                                    class="btn btn-danger btn-sm"
                                                    data-confirm="Tem certeza que deseja excluir este usuário?"
                                                >Excluir</a>
                                            <?php endif; ?>
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

