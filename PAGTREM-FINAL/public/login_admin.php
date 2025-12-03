<?php
require_once('../assets/config/db.php');
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  if ($email && $pass) {
    $stmt = $mysqli->prepare("SELECT id,name,email,password,role,avatar FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
      if (password_verify($pass, $row['password']) && $row['role'] === 'admin') {
        $_SESSION['user'] = $row;
        header('Location: dashboard.php');
        exit;
      } else {
        $error = 'Acesso negado. Esta conta não é de administrador.';
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
  <title>Login Administrativo - Vai de Trem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <div class="card-login">

    <div class="brand-icon"><img src="../assets/images/icone_adm.png" alt="Admin" class="icon-img"
        style="width:52px;height:52px;"></div>

    <h2>Área Administrativa</h2>
    <div class="sub">Acesso restrito</div>

    <?php if ($error): ?>
      <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Email</label>
      <input class="input" type="email" name="email" placeholder="admin@vaidetrem.com" required>

      <label style="margin-top:12px;">Senha</label>
      <input class="input" type="password" name="password" placeholder="Senha" required>
      <div style="text-align:right;margin-top:6px;font-size:13px;">
        <a href="esqueci_senha.php">Esqueci minha senha</a>
      </div>

      <button class="btn-login" type="submit">Entrar</button>
    </form>

    <a href="login.php" class="back-link">
      <img src="../assets/images/logout_icone.png" alt="Voltar" class="icon-img"
        style="width:16px;height:16px;transform: rotate(180deg);"> Voltar ao login de clientes
    </a>

  </div>

</body>

</html>