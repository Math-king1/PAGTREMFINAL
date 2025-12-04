<?php
require_once('../assets/config/db.php');
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Redireciona para redefinir a senha passando o email (inseguro, mas solicitado assim)
            header('Location: redefinir_senha.php?email=' . urlencode($email));
            exit;
        } else {
            $error = 'E-mail não encontrado.';
        }
    } else {
        $error = 'Por favor, digite seu e-mail.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Esqueci minha senha - PagTrem</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

    <div class="auth-card">
        <div class="brand-icon">
            <i class="ri-lock-password-line" style="font-size: 40px;"></i>
        </div>
        <h2>Recuperar Senha</h2>
        <p class="text-muted" style="margin-bottom: 24px;">Digite seu e-mail para continuar</p>

        <?php if ($error): ?>
            <div class="badge red" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 8px;">
                <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>E-mail</label>
            <input class="input" type="email" name="email" placeholder="seu@email.com" required>

            <button class="btn" type="submit" style="width: 100%; margin-top: 24px;">Continuar</button>
        </form>

        <div style="margin-top: 24px; text-align: center;">
            <a href="login.php"
                style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 0.875rem;">
                <i class="ri-arrow-left-line"></i> Voltar ao login
            </a>
        </div>
    </div>

</body>

</html>