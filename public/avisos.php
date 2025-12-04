<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $id = $_POST['id'] ?? '';
  $title = trim($_POST['title'] ?? '');
  $body = trim($_POST['body'] ?? '');
  $tag = $_POST['tag'] ?? 'Sistema';

  if ($action === 'create') {
    if ($title && $body) {
      $stmt = $mysqli->prepare("INSERT INTO notices(title, body, tag) VALUES (?, ?, ?)");
      $stmt->bind_param('sss', $title, $body, $tag);
      if ($stmt->execute()) {
        $success_msg = "Aviso criado com sucesso!";
      }
    }
  } elseif ($action === 'update' && $id) {
    if ($title && $body) {
      $stmt = $mysqli->prepare("UPDATE notices SET title=?, body=?, tag=? WHERE id=?");
      $stmt->bind_param('sssi', $title, $body, $tag, $id);
      if ($stmt->execute()) {
        $success_msg = "Aviso atualizado com sucesso!";
      }
    }
  } elseif ($action === 'delete' && $id) {
    $stmt = $mysqli->prepare("DELETE FROM notices WHERE id=?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
      $success_msg = "Aviso excluído com sucesso!";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Avisos - PagTrem</title>

  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <div class="layout-wrapper">
    <?php include '_partials/sidebar_admin.php'; ?>

    <div class="main-content">
      <div class="top-header">
        <h1><i class="ri-notification-3-line"></i> Avisos</h1>
      </div>

      <div class="container">

        <?php if ($success_msg): ?>
          <div class="badge success" style="margin-bottom: 24px; width: 100%; justify-content: center; padding: 12px;">
            <?php echo $success_msg; ?>
          </div>
        <?php endif; ?>

        <div class="notice-grid">
          <?php
          $res = $mysqli->query("SELECT * FROM notices ORDER BY id DESC");
          if ($res->num_rows > 0) {
            while ($n = $res->fetch_assoc()) {
              $badge = match ($n['tag']) {
                'Manutenção' => '<span class="badge red">Manutenção</span>',
                'Novidades' => '<span class="badge blue">Novidades</span>',
                default => '<span class="badge">Sistema</span>',
              };

              // Prepare data for JS
              $jsonData = htmlspecialchars(json_encode($n), ENT_QUOTES, 'UTF-8');

              echo "
              <div class='notice-card' onclick='editNotice($jsonData)' style='cursor: pointer;'>
                <div class='notice-top'>
                  <div class='notice-title'>" . htmlspecialchars($n['title']) . "</div>
                  $badge
                </div>
                <div class='notice-body'>" . nl2br(htmlspecialchars($n['body'])) . "</div>
                <div class='notice-footer'>
                  <div class='notice-date'>
                    <i class='ri-calendar-line'></i>
                    " . date('d/m/Y', strtotime($n['created_at'])) . "
                    <span style='margin: 0 4px;'>•</span>
                    <i class='ri-time-line'></i>
                    " . date('H:i', strtotime($n['created_at'])) . "
                  </div>
                  <i class='ri-pencil-line' style='color: var(--muted);'></i>
                </div>
              </div>";
            }
          } else {
            echo "<p class='text-muted' style='grid-column: 1/-1;'>Nenhum aviso registrado.</p>";
          }
          ?>
        </div>

      </div>
    </div>
  </div>

  <!-- FAB -->
  <div class="fab" onclick="openCreateModal()">
    <i class="ri-add-line" style="font-size: 32px;"></i>
  </div>

  <!-- MODAL -->
  <div class="modal-bg" id="noticeModal">
    <div class="modal" onclick="event.stopPropagation()">
      <h2 id="modalTitle" style="margin-bottom: 24px;">Novo Aviso</h2>
      <form method="post">
        <input type="hidden" name="action" id="formAction" value="create">
        <input type="hidden" name="id" id="noticeId">

        <label>Título</label>
        <input class="input" name="title" id="noticeTitle" placeholder="Título do aviso" required>

        <label>Categoria</label>
        <select class="select" name="tag" id="noticeTag">
          <option value="Sistema">Sistema</option>
          <option value="Manutenção">Manutenção</option>
          <option value="Novidades">Novidades</option>
        </select>

        <label>Mensagem</label>
        <textarea class="textarea" name="body" id="noticeBody" rows="4" placeholder="Escreva o aviso..."
          required></textarea>

        <div style="display:flex; gap:12px; margin-top:24px;">
          <button type="button" class="btn secondary" style="flex:1;" onclick="closeNoticeModal()">Cancelar</button>
          <button type="submit" class="btn" style="flex:1;">Salvar</button>
        </div>

        <div id="deleteBtnContainer" style="margin-top:16px; text-align:center; display:none;">
          <button type="submit" name="action" value="delete" class="btn secondary"
            style="color:var(--danger); border-color:var(--danger-bg); width:100%;"
            onclick="return confirm('Tem certeza que deseja excluir este aviso?')">Excluir Aviso</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const noticeModal = document.getElementById("noticeModal");
    const modalTitle = document.getElementById("modalTitle");
    const formAction = document.getElementById("formAction");
    const noticeId = document.getElementById("noticeId");
    const noticeTitle = document.getElementById("noticeTitle");
    const noticeTag = document.getElementById("noticeTag");
    const noticeBody = document.getElementById("noticeBody");
    const deleteBtnContainer = document.getElementById("deleteBtnContainer");

    function openCreateModal() {
      modalTitle.textContent = "Novo Aviso";
      formAction.value = "create";
      noticeId.value = "";
      noticeTitle.value = "";
      noticeTag.value = "Sistema";
      noticeBody.value = "";
      deleteBtnContainer.style.display = "none";
      noticeModal.style.display = "flex";
    }

    function editNotice(data) {
      modalTitle.textContent = "Editar Aviso";
      formAction.value = "update";
      noticeId.value = data.id;
      noticeTitle.value = data.title;
      noticeTag.value = data.tag;
      noticeBody.value = data.body;
      deleteBtnContainer.style.display = "block";
      noticeModal.style.display = "flex";
    }

    function closeNoticeModal() {
      noticeModal.style.display = "none";
    }

    window.addEventListener("click", function (e) {
      if (e.target === noticeModal) {
        closeNoticeModal();
      }
    });
  </script>

</body>

</html>