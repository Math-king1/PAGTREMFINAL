<?php
require_once('../assets/config/db.php');
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $login_type = $_POST['login_type'] ?? 'cliente';

  if ($email && $pass) {
    $stmt = $mysqli->prepare("SELECT id, name, email, password, role, avatar FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {

      if (password_verify($pass, $row['password'])) {

        if (($login_type === 'cliente' && $row['role'] !== 'user')) {
          $error = 'Essa conta é de administrador. Use a tela de login de usuário.';
        } elseif (($login_type === 'usuario' && $row['role'] !== 'admin')) {
          $error = 'Essa conta é de cliente. Use o login de cliente.';
        } else {

          $_SESSION['user'] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'role' => $row['role'],
            'avatar' => $row['avatar']
          ];

          if ($row['role'] === 'admin') {
            header('Location: dashboard.php');
          } else {
            header('Location: cliente_home.php');
          }
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
  <title>Login - Vai de Trem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  </style>
</head>

<body>

  <div class="card-login">

    <div class="brand-icon"><img src="../assets/images/trem_icone.png" alt="Trem" class="icon-img"
        style="width:52px;height:52px;"></div>

    <h2>Vai de Trem</h2>
    <div class="sub">Acesso ao sistema</div>

    <?php if ($error): ?>
      <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="tab-switch">
      <button type="button" id="tab-cliente" class="tab-btn active" onclick="showTab('cliente')">Cliente</button>
      <a href="login_admin.php" target="_blank" id="tab-usuario" class="tab-btn">Usuário/Admin</a>
    </div>

    <!-- Form CLIENTE -->
    <form id="form-cliente" method="post">
      <input type="hidden" name="login_type" value="cliente">

      <label>Email</label>
      <input class="input" type="email" name="email" placeholder="cliente@vaidetrem.com" required>

      <label style="margin-top:12px">Senha</label>
      <input class="input" type="password" name="password" placeholder="Sua senha" required>
    <div style="text-align:right;margin-top:6px;font-size:13px;">
      <a href="esqueci_senha.php">Esqueci minha senha</a>
    </div>

      <button class="btn-login" type="submit">Entrar</button>

      <p class="auth-note">
        Não tem conta? <a href="registrar_se.php">Registrar-se</a>
      </p>
    </form>

  </div>

  <script>
    function showTab(type) {
      document.getElementById('form-cliente').style.display = 'block';
      document.getElementById('tab-cliente').classList.add('active');
      document.getElementById('tab-usuario').classList.remove('active');
    }
  </script>

</body>

</html>