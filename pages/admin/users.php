<?php
/**
 * PAGTREM - Gerenciamento de Usuários (Admin)
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

$mysqli = getConnection();
$errors = [];
$success = '';

// Processa ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            // Criar usuário
            $username = trim($_POST['username'] ?? '');
            $nome_completo = trim($_POST['nome_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $role = $_POST['role'] ?? 'user';
            $status = $_POST['status'] ?? 'ativo';
            $senha = $_POST['senha'] ?? '';

            // Validações
            if (empty($username) || empty($nome_completo) || empty($email) || empty($senha)) {
                $errors[] = 'Todos os campos são obrigatórios.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'E-mail inválido.';
            }
            if (strlen($senha) < 6) {
                $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
            }

            if (empty($errors)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt = $mysqli->prepare("INSERT INTO usuarios (username, nome_completo, email, telefone, senha, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $username, $nome_completo, $email, $telefone, $senha_hash, $role, $status);
                $stmt->execute();

                $success = 'Usuário criado com sucesso!';
            }
        } elseif ($action === 'update') {
            // Atualizar usuário
            $id = (int)($_POST['id'] ?? 0);
            $nome_completo = trim($_POST['nome_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $role = $_POST['role'] ?? 'user';
            $status = $_POST['status'] ?? 'ativo';

            if ($id > 0 && !empty($nome_completo) && !empty($email)) {
                $stmt = $mysqli->prepare("UPDATE usuarios SET nome_completo = ?, email = ?, telefone = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $nome_completo, $email, $telefone, $role, $status, $id);
                $stmt->execute();

                $success = 'Usuário atualizado com sucesso!';
            } else {
                $errors[] = 'Dados inválidos.';
            }
        } elseif ($action === 'delete') {
            // Excluir usuário
            $id = (int)($_POST['id'] ?? 0);
            if ($id > 0 && $id !== $_SESSION['user']['id']) { // Não permite excluir a si mesmo
                $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $success = 'Usuário excluído com sucesso!';
            } else {
                $errors[] = 'Não é possível excluir este usuário.';
            }
        }
    } catch (mysqli_sql_exception $e) {
        $errors[] = 'Erro ao processar operação: ' . $e->getMessage();
    }
}

// Busca usuários
$users = [];
try {
    $result = $mysqli->query("SELECT id, username, nome_completo, email, telefone, role, status, data_criacao FROM usuarios ORDER BY data_criacao DESC");
    $users = $result->fetch_all(MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    $errors[] = 'Erro ao carregar usuários.';
}

// Obtém mensagem flash
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Gerenciar Usuários</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-header">
        <h2>👥 Gerenciar Usuários</h2>
        <div style="margin-top: 1rem;">
            <button onclick="openModal('createUserModal')" class="btn btn-success">Novo Usuário</button>
            <a href="dashboard.php" class="btn btn-secondary">Voltar ao Dashboard</a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?= implode('<br>', array_map('e', $errors)) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Nome Completo</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Data Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= e($user['username']) ?></td>
                    <td><?= e($user['nome_completo']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['telefone']) ?></td>
                    <td><?= e($user['role']) ?></td>
                    <td><?= e($user['status']) ?></td>
                    <td><?= date('d/m/Y', strtotime($user['data_criacao'])) ?></td>
                    <td>
                        <button onclick="editUser(<?= $user['id'] ?>, '<?= e($user['nome_completo']) ?>', '<?= e($user['email']) ?>', '<?= e($user['telefone']) ?>', '<?= e($user['role']) ?>', '<?= e($user['status']) ?>')" class="btn btn-primary btn-sm">Editar</button>
                        <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Criar Usuário -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createUserModal')">&times;</span>
            <h3>Novo Usuário</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label class="form-label required">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label required">Nome Completo</label>
                    <input type="text" name="nome_completo" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label required">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label required">Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Criar</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Usuário -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editUserModal')">&times;</span>
            <h3>Editar Usuário</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="editUserId">
                <div class="form-group">
                    <label class="form-label required">Nome Completo</label>
                    <input type="text" name="nome_completo" id="editNomeCompleto" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label required">E-mail</label>
                    <input type="email" name="email" id="editEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" id="editTelefone" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" id="editRole" class="form-control">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editStatus" class="form-control">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Atualizar</button>
            </form>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        function editUser(id, nome, email, telefone, role, status) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editNomeCompleto').value = nome;
            document.getElementById('editEmail').value = email;
            document.getElementById('editTelefone').value = telefone;
            document.getElementById('editRole').value = role;
            document.getElementById('editStatus').value = status;
            openModal('editUserModal');
        }
    </script>
</body>
</html>
