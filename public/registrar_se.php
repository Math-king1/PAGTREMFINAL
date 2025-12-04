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
  <title>Registrar - PagTrem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  <script src="../assets/js/app.js" defer></script>
</head>

<body>

  <div class="auth-card">

    <div class="brand-icon">
      <i class="ri-user-add-line" style="font-size: 40px;"></i>
    </div>
    <h2>Crie sua conta</h2>
    <p class="text-muted" style="margin-bottom: 24px;">Pronto para viajar com a gente?</p>

    <?php if ($msg): ?>
      <div class="badge success" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 8px;">
        <?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if ($err): ?>
      <div class="badge red" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 8px;">
        <?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>

    <form method="post">

      <label>Nome completo</label>
      <input class="input" name="name" placeholder="Seu nome" required>

      <label>E-mail</label>
      <input class="input" type="email" name="email" placeholder="email@exemplo.com" required>

      <label>Senha</label>
      <input class="input" type="password" name="password" placeholder="Digite uma senha" required>

      <label>Endereço</label>
      <div class="row-cep" style="display: flex; gap: 12px; margin-bottom: 12px;">
        <input class="input" id="cep" placeholder="CEP" style="flex: 1;">
        <input class="input" id="city" placeholder="Cidade" style="flex: 2;">
        <input class="input" id="uf" placeholder="UF" style="width: 60px;">
      </div>

      <button type="button" class="btn secondary" onclick="buscarCEP('cep',{city:'city',state:'uf'})"
        style="width: 100%; margin-bottom: 24px;">
        Buscar CEP
      </button>

      <button type="submit" class="btn" style="width: 100%;">Registrar</button>

      <div style="margin-top: 24px; text-align: center; font-size: 0.875rem;">
        Já tem conta? <a href="login.php">Entrar</a>
      </div>
    </form>

  </div>

</body>

</html>