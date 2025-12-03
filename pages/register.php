<?php
/**
 * PAGTREM - Página de Registro
 */

require_once __DIR__ . '/../config/database.php';

initSession();

// Se já estiver logado, redireciona
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}

$errors = [];

// Processa o registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nome_completo = trim($_POST['nome_completo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Validações
    if (empty($username) || strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username deve ter pelo menos 3 caracteres e conter apenas letras, números e underscore.';
    }
    if (empty($nome_completo) || strlen($nome_completo) > 120) {
        $errors[] = 'Nome completo é obrigatório e deve ter no máximo 120 caracteres.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail válido é obrigatório.';
    }
    if (empty($senha) || strlen($senha) < 6) {
        $errors[] = 'Senha deve ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirmar_senha) {
        $errors[] = 'As senhas não coincidem.';
    }

    if (empty($errors)) {
        try {
            $mysqli = getConnection();

            // Verifica se username já existe
            $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors[] = 'Username já está em uso.';
            }

            // Verifica se email já existe
            $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors[] = 'E-mail já está cadastrado.';
            }

            if (empty($errors)) {
                // Hash da senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                // Insere novo usuário
                $stmt = $mysqli->prepare("INSERT INTO usuarios (username, nome_completo, email, telefone, senha) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $nome_completo, $email, $telefone, $senha_hash);
                $stmt->execute();

                // Login automático após registro
                $user_id = $mysqli->insert_id;
                $stmt = $mysqli->prepare("SELECT id, username, nome_completo, email, role FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'nome' => $user['nome_completo'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                session_regenerate_id(true);

                // Redireciona para dashboard do usuário
                redirect('user/dashboard.php');
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Erro no registro: " . $e->getMessage());
            $errors[] = 'Erro ao processar registro. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Registro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <h1>🚆 PAGTREM</h1>
                <p>Cadastre-se no Sistema de Gerenciamento de Trens</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?= implode('<br>', array_map('e', $errors)) ?>
                </div>
            <?php endif; ?>

            <form method="POST" data-validate>
                <div class="form-group">
                    <label class="form-label required" for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Digite seu username"
                        value="<?= e($_POST['username'] ?? '') ?>"
                        required
                        pattern="[a-zA-Z0-9_]+"
                        minlength="3"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label required" for="nome_completo">Nome Completo</label>
                    <input
                        type="text"
                        id="nome_completo"
                        name="nome_completo"
                        class="form-control"
                        placeholder="Digite seu nome completo"
                        value="<?= e($_POST['nome_completo'] ?? '') ?>"
                        required
                        maxlength="120"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label required" for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Digite seu e-mail"
                        value="<?= e($_POST['email'] ?? '') ?>"
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
                        placeholder="Digite seu telefone"
                        value="<?= e($_POST['telefone'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label required" for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control"
                        placeholder="Digite sua senha"
                        required
                        minlength="6"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label required" for="confirmar_senha">Confirmar Senha</label>
                    <input
                        type="password"
                        id="confirmar_senha"
                        name="confirmar_senha"
                        class="form-control"
                        placeholder="Confirme sua senha"
                        required
                        minlength="6"
                    >
                </div>

                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Cadastrar
                    </button>
                </div>

                <div class="text-center mt-2">
                    <p class="text-muted">
                        Já tem conta? <a href="index.php">Faça login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>
