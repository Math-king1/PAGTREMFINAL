<?php
/**
 * PAGTREM - Página de Cadastro
 */

require_once __DIR__ . '/../config/database.php';

initSession();

// Se já estiver logado, redireciona
if (isLoggedIn()) {
    redirect('user/dashboard.php');
}

$errors = [];
$success = false;

// Processa o cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';
    
    // Validações
    if (strlen($username) < 3) {
        $errors[] = 'O nome de usuário deve ter pelo menos 3 caracteres.';
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'O nome de usuário pode conter apenas letras, números e underscore.';
    }
    if (strlen($nome) < 3) {
        $errors[] = 'O nome completo deve ter pelo menos 3 caracteres.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'E-mail inválido.';
    }
    if (strlen($senha) < 4) {
        $errors[] = 'A senha deve ter pelo menos 4 caracteres.';
    }
    if ($senha !== $senha2) {
        $errors[] = 'As senhas não conferem.';
    }
    
    if (empty($errors)) {
        try {
            $pdo = getConnection();
            
            // Verifica se username já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Este nome de usuário já está em uso.';
            }
            
            // Verifica se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Este e-mail já está cadastrado.';
            }
            
            if (empty($errors)) {
                // Insere o novo usuário
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO usuarios (username, nome_completo, email, telefone, senha, role, status) 
                    VALUES (?, ?, ?, ?, ?, 'user', 'ativo')
                ");
                $stmt->execute([$username, $nome, $email, $telefone, $senhaHash]);
                
                $success = true;
            }
        } catch (PDOException $e) {
            error_log("Erro no cadastro: " . $e->getMessage());
            $errors[] = 'Erro ao processar cadastro. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Cadastro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <h1>🚆 PAGTREM</h1>
                <p>Criar nova conta</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Cadastro realizado com sucesso! <a href="index.php">Faça login</a>
                </div>
            <?php else: ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?= implode('<br>', array_map('e', $errors)) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" data-validate>
                    <div class="form-group">
                        <label class="form-label required" for="username">Nome de Usuário</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            placeholder="Ex: joao123"
                            value="<?= e($_POST['username'] ?? '') ?>"
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
                            placeholder="Seu nome completo"
                            value="<?= e($_POST['nome'] ?? '') ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required" for="email">E-mail</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="seu@email.com"
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
                            placeholder="(11) 99999-9999"
                            value="<?= e($_POST['telefone'] ?? '') ?>"
                        >
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="senha">Senha</label>
                            <input 
                                type="password" 
                                id="senha" 
                                name="senha" 
                                class="form-control" 
                                placeholder="Mínimo 4 caracteres"
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required" for="senha2">Confirmar Senha</label>
                            <input 
                                type="password" 
                                id="senha2" 
                                name="senha2" 
                                class="form-control" 
                                placeholder="Repita a senha"
                                required
                            >
                        </div>
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
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>

