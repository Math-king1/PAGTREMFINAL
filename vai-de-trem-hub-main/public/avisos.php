<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $body = trim($_POST['body'] ?? '');
  $tag = $_POST['tag'] ?? 'Sistema';

  if ($title && $body) {
    $stmt = $mysqli->prepare("INSERT INTO notices(title, body, tag) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $title, $body, $tag);
    if ($stmt->execute()) {
      $success_msg = "Aviso criado com sucesso!";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Avisos - Vai de Trem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <div class="top-header">
    <h1><img src="../assets/images/notificacao_icone.png" alt="Avisos" class="icon-img" style="width:22px;height:22px;">
      Avisos</h1>
  </div>

  <?php if ($success_msg): ?>
    <div class="success-box" style="margin: 20px;">
      <?php echo $success_msg; ?>
    </div>
  <?php endif; ?>

  <div class="post-box">
    <h2>Criar Aviso</h2>
    <form method="post">

      <input class="input" name="title" placeholder="Título do aviso" required>

      <select class="select" name="tag">
        <option>Manutenção</option>
        <option>Novidades</option>
        <option selected>Sistema</option>
      </select>

      <textarea class="textarea" name="body" rows="3" placeholder="Escreva o aviso..." required></textarea>

      <button class="btn" style="margin-top:8px;width:100%;">Publicar</button>
    </form>
  </div>

  <?php include '_partials/bottom_nav.php'; ?>

</body>

</html>