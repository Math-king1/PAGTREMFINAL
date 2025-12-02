<?php
/**
 * PAGTREM - Página de Login
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

// Processa o login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    // Validação básica
    if (empty($login)) {
        $errors[] = 'Informe o usuário ou e-mail.';
    }
    if (empty($senha)) {
        $errors[] = 'Informe a senha.';
    }
    
    if (empty($errors)) {
        try {
            $pdo = getConnection();
            
            // Busca por username ou email
            $stmt = $pdo->prepare("
                SELECT id, username, nome_completo, email, senha, role, status 
                FROM usuarios 
                WHERE (username = ? OR email = ?) 
                LIMIT 1
            ");
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($senha, $user['senha'])) {
                // Verifica se está ativo
                if ($user['status'] !== 'ativo') {
                    $errors[] = 'Sua conta está inativa. Contate o administrador.';
                } else {
                    // Login bem-sucedido - salva na sessão
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'nome' => $user['nome_completo'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    // Redireciona conforme o tipo de usuário
                    if ($user['role'] === 'admin') {
                        redirect('admin/dashboard.php');
                    } else {
                        redirect('user/dashboard.php');
                    }
                }
            } else {
                $errors[] = 'Usuário ou senha inválidos.';
            }
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            $errors[] = 'Erro ao processar login. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <h1>🚆 PAGTREM</h1>
                <p>Sistema de Gerenciamento de Trens</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?= implode('<br>', array_map('e', $errors)) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" data-validate>
                <div class="form-group">
                    <label class="form-label required" for="login">Usuário ou E-mail</label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        class="form-control" 
                        placeholder="Digite seu usuário ou e-mail"
                        value="<?= e($_POST['login'] ?? '') ?>"
                        required
                        autofocus
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
                    >
                </div>
                
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Entrar
                    </button>
                </div>
                
                <div class="text-center mt-2">
                    <p class="text-muted">
                        Não tem conta? <a href="register.php">Cadastre-se</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>

