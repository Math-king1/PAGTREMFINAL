<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

$user = $_SESSION['user'] ?? null;

// Impede admin de acessar essa página
if ($user['role'] !== 'user') {
  header('Location: dashboard.php');
  exit;
}

$feedback = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $cep = trim($_POST['cep'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $state = trim($_POST['state'] ?? '');

  if ($name) {
    $stmt = $mysqli->prepare("UPDATE users SET name=?, phone=?, department=?, job_title=? WHERE id=?");
    $stmt->bind_param('ssssi', $name, $phone, $city, $state, $user['id']);
    if ($stmt->execute()) {
      $_SESSION['user']['name'] = $name;
      $feedback = "Perfil atualizado!";
    } else {
      $error = "Erro ao salvar alterações.";
    }
  } else {
    $error = "O nome é obrigatório.";
  }
}

$res = $mysqli->query("SELECT * FROM users WHERE id=" . $user['id']);
$me = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <title>Editar Perfil - Cliente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  <style>
  </style>

</head>

<body>

  <div class="container" style="max-width:900px; margin: 20px auto;">

    <div class="topbar">
      <h2>Editar Perfil</h2>
      <p class="link-muted">Atualize suas informações abaixo</p>
    </div>

    <div class="form-card">

      <?php if ($feedback): ?>
        <div class="badge success"><?php echo $feedback; ?></div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="badge error"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="post">
        <input class="input" name="name" value="<?php echo htmlspecialchars($me['name']); ?>"
          placeholder="Nome completo">

        <input class="input" name="phone" value="<?php echo htmlspecialchars($me['phone']); ?>" placeholder="Telefone">

        <input class="input" id="cep" name="cep" value="" placeholder="CEP (opcional)">

        <input class="input" id="city" name="city" value="<?php echo htmlspecialchars($me['department']); ?>"
          placeholder="Cidade">

        <input class="input" id="state" name="state" value="<?php echo htmlspecialchars($me['job_title']); ?>"
          placeholder="UF">

        <button class="btn-save" type="submit">Salvar Alterações</button>
      </form>
    </div>

  </div>

  <!-- MENU INFERIOR MOBILE -->
  <div class="mobile-menu">
    <a href="cliente_home.php">
      <img src="../assets/images/rotas_icone.png" alt="Rotas" class="icon-img"
        style="width:24px;height:24px;margin-bottom:4px;">
      <span>Rotas</span>
    </a>
    <a href="perfil_cliente.php" class="active">
      <img src="../assets/images/icone_adm.png" alt="Perfil" class="icon-img"
        style="width:24px;height:24px;margin-bottom:4px;">
      <span>Perfil</span>
    </a>
    <a href="logout.php">
      <img src="../assets/images/logout_icone.png" alt="Sair" class="icon-img"
        style="width:24px;height:24px;margin-bottom:4px;">
      <span>Sair</span>
    </a>
  </div>

</body>

</html>