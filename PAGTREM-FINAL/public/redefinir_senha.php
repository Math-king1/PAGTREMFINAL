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
    <title>Redefinir Senha - Vai de Trem</title>
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

    <div class="card-login">
        <div class="brand-icon"><img src="../assets/images/trem_icone.png" alt="Trem" class="icon-img"
                style="width:52px;height:52px;"></div>
        <h2>Nova Senha</h2>
        <div class="sub">Defina sua nova senha para <strong><?php echo htmlspecialchars($email); ?></strong></div>

        <?php if ($error): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-box"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Nova Senha</label>
            <input class="input" type="password" name="password" placeholder="Nova senha" required>

            <label style="margin-top:12px;">Confirmar Senha</label>
            <input class="input" type="password" name="confirm_password" placeholder="Confirme a senha" required>

            <button class="btn-login" type="submit" style="margin-top:20px;">Redefinir Senha</button>
        </form>

        <a href="login.php" class="back-link">
            <img src="../assets/images/logout_icone.png" alt="Voltar" class="icon-img"
                style="width:16px;height:16px;transform: rotate(180deg);"> Cancelar
        </a>
    </div>

</body>

</html>