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
    // Usando department para cidade e job_title para UF (conforme lógica original)
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
  <title>Editar Perfil - Usuário</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <div class="layout-wrapper">
    <?php include '_partials/sidebar_user.php'; ?>

    <div class="main-content">
      <div class="top-header">
        <h1><i class="ri-user-edit-line"></i> Editar Perfil</h1>
      </div>

      <div class="container" style="padding-bottom: 100px;">

        <div class="card">
          <?php if ($feedback): ?>
            <div class="badge success" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 12px;">
              <?php echo $feedback; ?>
            </div>
          <?php endif; ?>

          <?php if ($error): ?>
            <div class="badge red" style="margin-bottom: 16px; width: 100%; justify-content: center; padding: 12px;">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form method="post">
            <label>Nome Completo</label>
            <input class="input" name="name" value="<?php echo htmlspecialchars($me['name']); ?>"
              placeholder="Nome completo">

            <label>Telefone</label>
            <input class="input" name="phone" value="<?php echo htmlspecialchars($me['phone']); ?>"
              placeholder="Telefone">

            <label>CEP</label>
            <input class="input" id="cep" name="cep" value="" placeholder="CEP (opcional)">

            <div style="display: flex; gap: 12px;">
              <div style="flex: 2;">
                <label>Cidade</label>
                <input class="input" id="city" name="city" value="<?php echo htmlspecialchars($me['department']); ?>"
                  placeholder="Cidade">
              </div>
              <div style="flex: 1;">
                <label>UF</label>
                <input class="input" id="state" name="state" value="<?php echo htmlspecialchars($me['job_title']); ?>"
                  placeholder="UF">
              </div>
            </div>

            <button class="btn" type="submit" style="width: 100%; margin-top: 24px;">Salvar Alterações</button>

            <a href="perfil_usuario.php" class="btn secondary"
              style="width: 100%; margin-top: 12px; justify-content: center; text-decoration: none;">Cancelar</a>
          </form>
        </div>

      </div>
    </div>
  </div>

</body>

</html>