<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

// PROCESSAR FORMULÁRIO DE AVISOS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $tag = $_POST['tag'] ?? 'Sistema';

    if ($action === 'update' && $id) {
        $stmt = $mysqli->prepare("UPDATE notices SET title=?, body=?, tag=? WHERE id=?");
        $stmt->bind_param('sssi', $title, $body, $tag, $id);
        $stmt->execute();
    } elseif ($action === 'delete' && $id) {
        $stmt = $mysqli->prepare("DELETE FROM notices WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    header('Location: dashboard.php');
    exit;
}

// DELETE VIA GET (Opcional, mas mantendo padrão)
if (isset($_GET['delete_notice'])) {
    $id = (int)$_GET['delete_notice'];
    $mysqli->query("DELETE FROM notices WHERE id=$id");
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Vai de Trem</title>

  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <link href="../assets/css/styles.css" rel="stylesheet">

</head>

<body>

  <!-- HEADER -->
  <div class="top-header">
    <h1><img src="../assets/images/inicio_png.png" alt="Dashboard" class="icon-img" style="width:22px;height:22px;">
      Dashboard</h1>
  </div>

  <?php
  $routesAtivas = $mysqli->query("SELECT COUNT(*) AS total FROM routes WHERE status='ativa'")->fetch_assoc()['total'];
  $notices_count = $mysqli->query("SELECT COUNT(*) AS total FROM notices")->fetch_assoc()['total'];
  $employees = $mysqli->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'];
  ?>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="stat-card">
      <img src="../assets/images/rotas_icone.png" alt="Rotas" class="icon-img"
        style="width:28px;height:28px;margin:0 auto;">
      <div class="stat-value"><?php echo $routesAtivas; ?></div>
      <div class="stat-label">Rotas Ativas</div>
    </div>

    <div class="stat-card">
      <img src="../assets/images/icones_funcionarios.png" alt="Funcionários" class="icon-img"
        style="width:28px;height:28px;margin:0 auto;">
      <div class="stat-value"><?php echo $employees; ?></div>
      <div class="stat-label">Funcionários</div>
    </div>

    <div class="stat-card">
      <img src="../assets/images/notificacao_icone.png" alt="Avisos" class="icon-img"
        style="width:28px;height:28px;margin:0 auto;">
      <div class="stat-value"><?php echo $notices_count; ?></div>
      <div class="stat-label">Avisos</div>
    </div>
  </div>

  <!-- AVISOS RECENTES -->
  <div class="recent-section">
    <h2>Avisos Recentes</h2>
    <?php
    $res = $mysqli->query("SELECT * FROM notices ORDER BY id DESC LIMIT 5");
    if ($res->num_rows > 0) {
      while ($n = $res->fetch_assoc()) {
        $badge = match ($n['tag']) {
          'Manutenção' => '<span class="badge red">Manutenção</span>',
          'Novidades' => '<span class="badge blue">Novidades</span>',
          default => '<span class="badge">Sistema</span>',
        };

        // Prepara dados para o JS
        $jsonData = htmlspecialchars(json_encode($n), ENT_QUOTES, 'UTF-8');

        echo "
        <div class='notice-card' style='position:relative;'>
          <div class='notice-top'>
            <div class='notice-title'>" . htmlspecialchars($n['title']) . "</div>
            $badge
          </div>
          <div class='notice-body'>" . nl2br(htmlspecialchars($n['body'])) . "</div>
          <div class='notice-date'>" . date('d/m/Y H:i', strtotime($n['created_at'])) . "</div>
          
          <div style='position:absolute; top:10px; right:10px; cursor:pointer;' onclick='editNotice($jsonData)'>
            <i class='ri-pencil-line' style='color:var(--muted); font-size:18px;'></i>
          </div>
        </div>";
      }
    } else {
      echo "<p style='padding:0 20px;'>Nenhum aviso recente.</p>";
    }
    ?>
  </div>

  <!-- ATIVIDADES RECENTES -->
  <div class="recent-section">
    <h2>Atividades Recentes</h2>

    <div class="recent-item">
      <img src="../assets/images/trem_icone.png" alt="Trem" class="icon-img" style="width:24px;height:24px;">
      09:10 — Trem #4321 partiu para Curitiba
    </div>

    <div class="recent-item">
      <img src="../assets/images/registrar_conta.png" alt="Novo usuário" class="icon-img"
        style="width:24px;height:24px;">
      09:00 — Novo funcionário cadastrado em Operações
    </div>

    <div class="recent-item">
      <img src="../assets/images/notificacao_icone.png" alt="Câmera" class="icon-img" style="width:24px;height:24px;">
      08:52 — Câmera #7 voltou ao status Online
    </div>

    <div class="recent-item">
      <img src="../assets/images/rotas_icone.png" alt="Manutenção" class="icon-img" style="width:24px;height:24px;">
      08:47 — Manutenção agendada em rota SP → Campinas
    </div>
  </div>

  <!-- MODAL AVISOS -->
  <div class="modal-bg" id="noticeModal">
    <div class="modal" onclick="event.stopPropagation()">
      <h2 id="modalTitle">Editar Aviso</h2>
      <form method="post">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="noticeId">

        <label>Título</label>
        <input class="input" name="title" id="noticeTitle" required>

        <label>Tag</label>
        <select class="select" name="tag" id="noticeTag">
          <option value="Sistema">Sistema</option>
          <option value="Manutenção">Manutenção</option>
          <option value="Novidades">Novidades</option>
        </select>

        <label>Mensagem</label>
        <textarea class="textarea" name="body" id="noticeBody" rows="4" required></textarea>

        <div style="display:flex; gap:10px; margin-top:15px;">
          <button type="button" class="btn secondary" onclick="closeNoticeModal()">Cancelar</button>
          <button type="submit" class="btn">Salvar</button>
        </div>

        <div style="margin-top:10px; text-align:center;">
          <a href="#" id="deleteNoticeLink" class="btn" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5; display:block;">Excluir Aviso</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const noticeModal = document.getElementById("noticeModal");
    const noticeId = document.getElementById("noticeId");
    const noticeTitle = document.getElementById("noticeTitle");
    const noticeTag = document.getElementById("noticeTag");
    const noticeBody = document.getElementById("noticeBody");
    const deleteNoticeLink = document.getElementById("deleteNoticeLink");

    function editNotice(data) {
      noticeId.value = data.id;
      noticeTitle.value = data.title;
      noticeTag.value = data.tag;
      noticeBody.value = data.body;

      deleteNoticeLink.href = "?delete_notice=" + data.id;
      deleteNoticeLink.onclick = function(e) {
        if (!confirm('Tem certeza que deseja excluir este aviso?')) {
          e.preventDefault();
        }
      };

      noticeModal.style.display = "flex";
    }

    function closeNoticeModal() {
      noticeModal.style.display = "none";
    }

    window.addEventListener("click", function(e) {
      if (e.target === noticeModal) {
        closeNoticeModal();
      }
    });
  </script>

  <!-- NAV -->
  <?php include '_partials/bottom_nav.php'; ?>


</body>

</html>