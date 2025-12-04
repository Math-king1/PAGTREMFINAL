<?php
require_once('../assets/config/db.php');
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  if ($email && $pass) {
    $stmt = $mysqli->prepare("SELECT id, name, email, password, role, avatar FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {

      if (password_verify($pass, $row['password'])) {

        if ($row['role'] !== 'user') {
          $error = 'Esta área é exclusiva para usuários. Administradores devem usar o login administrativo.';
        } else {
          $_SESSION['user'] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => $row['role'],
            'avatar' => $row['avatar']
          ];
          header('Location: usuario_home.php');
          exit;
        }

      } else {
        $error = 'Senha incorreta.';
      }

    } else {
      $error = 'Usuário não encontrado.';
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
  <title>Login do Usuário - PagTrem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>

  <div class="auth-card">

    <div class="brand-icon">
      <i class="ri-user-smile-line" style="font-size: 40px;"></i>
    </div>

    <h2>Login do Usuário</h2>
    <p class="text-muted" style="margin-bottom: 24px;">Acesse sua área de usuário</p>

    <?php if ($error): ?>
      <div class="badge red" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 8px;">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label>Email</label>
      <input class="input" type="email" name="email" placeholder="usuario@pagtrem.com" required>

      <label>Senha</label>
      <input class="input" type="password" name="password" placeholder="Sua senha" required>
      <div style="text-align:right; margin-top:8px; font-size:0.875rem;">
        <a href="esqueci_senha.php" style="color: var(--text-light);">Esqueci minha senha</a>
      </div>

      <button class="btn" type="submit" style="width: 100%; margin-top: 24px;">Entrar</button>

      <p class="auth-note" style="margin-top: 24px; font-size: 0.875rem;">
        Não tem conta? <a href="registrar_se.php">Registrar-se</a>
      </p>
    </form>

  </div>

</body>

</html>