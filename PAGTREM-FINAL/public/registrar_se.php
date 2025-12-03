<?php
require_once('../assets/config/db.php');
session_start();

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($name && $email && $password) {

    $check = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $err = 'Este e-mail já está cadastrado. Faça login ou use outro.';
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $stmt = $mysqli->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, 'user')");
      $stmt->bind_param('sss', $name, $email, $hash);

      if ($stmt->execute()) {
        $msg = 'Cadastro realizado com sucesso! Faça login.';
      } else {
        $err = 'Erro ao cadastrar: ' . $mysqli->error;
      }

      $stmt->close();
    }

    $check->close();

  } else {
    $err = 'Preencha todos os campos.';
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrar - Vai de Trem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  <script src="../assets/js/app.js" defer></script>
</head>

<body>

  <div class="card-register">

    <div class="brand-icon"><img src="../assets/images/registrar_conta.png" alt="Registrar" class="icon-img"
        style="width:52px;height:52px;"></div>
    <h2>Crie sua conta</h2>
    <div class="sub">Pronto para viajar com a gente?</div>

    <?php if ($msg): ?>
      <div class="success-box"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if ($err): ?>
      <div class="error-box"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>

    <form method="post">

      <label>Nome completo</label>
      <input class="input" name="name" placeholder="Seu nome" required>

      <label style="margin-top:12px;">E-mail</label>
      <input class="input" type="email" name="email" placeholder="email@exemplo.com" required>

      <label style="margin-top:12px;">Senha</label>
      <input class="input" type="password" name="password" placeholder="Digite uma senha" required>

      <label style="margin-top:12px;">Endereço</label>
      <div class="row-cep">
        <input class="input small" id="cep" placeholder="CEP">
        <input class="input small" id="city" placeholder="Cidade">
        <input class="input small" id="uf" placeholder="UF">
      </div>

      <button type="button" class="btn-outline" onclick="buscarCEP('cep',{city:'city',state:'uf'})">
        Buscar CEP
      </button>

      <button type="submit" class="btn-main">Registrar</button>

      <div class="back-link">
        Já tem conta? <a href="login.php">Entrar</a>
      </div>
    </form>

  </div>

</body>

</html>