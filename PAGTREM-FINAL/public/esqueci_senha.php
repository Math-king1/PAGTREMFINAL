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
    <title>Esqueci minha senha - Vai de Trem</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

    <div class="card-login">
        <div class="brand-icon"><img src="../assets/images/trem_icone.png" alt="Trem" class="icon-img"
                style="width:52px;height:52px;"></div>
        <h2>Recuperar Senha</h2>
        <div class="sub">Digite seu e-mail para continuar</div>

        <?php if ($error): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>E-mail</label>
            <input class="input" type="email" name="email" placeholder="seu@email.com" required>

            <button class="btn-login" type="submit" style="margin-top:20px;">Continuar</button>
        </form>

        <a href="login.php" class="back-link">
            <img src="../assets/images/logout_icone.png" alt="Voltar" class="icon-img"
                style="width:16px;height:16px;transform: rotate(180deg);"> Voltar ao login
        </a>
    </div>

</body>

</html>