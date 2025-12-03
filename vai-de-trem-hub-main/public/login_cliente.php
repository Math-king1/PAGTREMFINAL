<?php
require_once('../assets/config/db.php');
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  if ($email && $pass) {
    $stmt = $mysqli->prepare("SELECT id, name, email, password, role, avatar FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
      if (password_verify($pass, $row['password']) && $row['role'] === 'user') {
        $_SESSION['user'] = $row;
        header('Location: cliente_home.php');
        exit;
      } else {
        $error = 'E-mail ou senha incorretos, ou conta não é de cliente.';
      }
    } else
      $error = 'Usuário não encontrado.';
  } else
    $error = 'Preencha todos os campos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login do Cliente - Vai de Trem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <div class="card-login">

    <div class="brand-icon"><img src="../assets/images/icone_adm.png" alt="Cliente" class="icon-img"
        style="width:52px;height:52px;"></div>

    <h2>Login do Cliente</h2>
    <div class="sub">Acesse sua área de rotas</div>

    <?php if ($error): ?>
      <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Email</label>
      <input class="input" type="email" name="email" placeholder="cliente@vaidetrem.com" required>

      <label style="margin-top:12px;">Senha</label>
      <input class="input" type="password" name="password" placeholder="Senha" required>
      <div style="text-align:right;margin-top:6px;font-size:13px;">
        <a href="esqueci_senha.php">Esqueci minha senha</a>
      </div>

      <button class="btn-login" type="submit">Entrar</button>
    </form>

    <div class="back-links">
      Não tem conta? <a href="registrar_se.php">Registrar-se</a><br>
      <a href="login_admin.php" style="font-size:13px;">
        Entrar como administrador
      </a>
    </div>

  </div>

</body>

</html>