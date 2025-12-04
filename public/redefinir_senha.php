<?php
require_once('../assets/config/db.php');
session_start();

$error = '';
$success = '';
$email = $_GET['email'] ?? '';

if (!$email) {
    header('Location: esqueci_senha.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($pass && $confirm) {
        if ($pass === $confirm) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $hash, $email);

            if ($stmt->execute()) {
                $success = 'Senha redefinida com sucesso! Redirecionando...';
                echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
            } else {
                $error = 'Erro ao atualizar a senha.';
            }
        } else {
            $error = 'As senhas não coincidem.';
        }
    } else {
        $error = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir Senha - PagTrem</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

    <div class="auth-card">
        <div class="brand-icon">
            <i class="ri-key-2-line" style="font-size: 40px;"></i>
        </div>
        <h2>Nova Senha</h2>
        <p class="text-muted" style="margin-bottom: 24px;">Defina sua nova senha para
            <strong><?php echo htmlspecialchars($email); ?></strong></p>

        <?php if ($error): ?>
            <div class="badge red" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 8px;">
                <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="badge success" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 8px;">
                <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Nova Senha</label>
            <input class="input" type="password" name="password" placeholder="Nova senha" required>

            <label>Confirmar Senha</label>
            <input class="input" type="password" name="confirm_password" placeholder="Confirme a senha" required>

            <button class="btn" type="submit" style="width: 100%; margin-top: 24px;">Redefinir Senha</button>
        </form>

        <div style="margin-top: 24px; text-align: center;">
            <a href="login.php"
                style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.875rem;">
                <i class="ri-close-circle-line"></i> Cancelar
            </a>
        </div>
    </div>

</body>

</html>