<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <title>Área do Usuário - PagTrem</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    .tabs {
      display: flex;
      gap: 16px;
      margin-bottom: 24px;
      border-bottom: 1px solid var(--border);
    }

    .tab-btn {
      background: none;
      border: none;
      padding: 12px 16px;
      font-size: 16px;
      font-weight: 600;
      color: var(--text-light);
      cursor: pointer;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
    }

    .tab-btn:hover {
      color: var(--brand);
    }

    .tab-btn.active {
      color: var(--brand);
      border-bottom-color: var(--brand);
    }

    .tab-content {
      display: none;
      animation: fadeIn 0.3s ease;
    }

    .tab-content.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>

  <div class="layout-wrapper">
    <?php include '_partials/sidebar_user.php'; ?>

    <div class="main-content">
      <div class="top-header">
        <h1><i class="ri-user-smile-line"></i> Olá, <?php echo htmlspecialchars($user['name']); ?>!</h1>
      </div>

      <div class="container" style="padding-bottom: 100px;">

        <div class="card" style="margin-bottom: 24px; text-align: center; padding: 32px 24px;">
          <i class="ri-train-line"
            style="font-size: 48px; color: var(--brand); margin-bottom: 16px; display: block;"></i>
          <h2 style="margin-bottom: 8px;">Bem-vindo ao PagTrem</h2>
          <p class="text-muted">Acompanhe suas rotas e viagens em tempo real.</p>
        </div>

        <!-- TABS -->
        <div class="tabs">
          <button class="tab-btn active" onclick="openTab(event, 'rotas')">Rotas Disponíveis</button>
          <button class="tab-btn" onclick="openTab(event, 'notificacoes')">Notificações</button>
        </div>

        <!-- TAB ROTAS -->
        <div id="rotas" class="tab-content active">
          <div class="route-list">
            <?php
            $res = $mysqli->query("SELECT * FROM routes ORDER BY id DESC");
            if ($res->num_rows > 0) {
              while ($r = $res->fetch_assoc()) {
                $badgeClass = ($r['status'] === 'manutencao') ? 'red' : 'blue';
                $badgeText = ($r['status'] === 'manutencao') ? 'Manutenção' : 'Ativa';

                echo "
                <div class='route-card'>
                    <div class='route-title'>
                    <span>" . htmlspecialchars($r['name']) . "</span>
                    <span class='badge $badgeClass'>$badgeText</span>
                    </div>
                    <div class='details'>
                    <div style='display:flex; align-items:center; gap:8px;'>
                        <i class='ri-calendar-line' style='color:var(--brand);'></i>
                        <span>Opera diariamente</span>
                    </div>
                    </div>";

                if (!empty($r['extra_info'])) {
                  echo "
                    <div class='live-info'>
                    <i class='ri-notification-3-line' style='font-size:18px; color:var(--text-light);'></i>
                    <span>" . htmlspecialchars($r['extra_info']) . "</span>
                    </div>";
                }

                echo "</div>";
              }
            } else {
              echo "<p class='text-muted' style='grid-column: 1/-1; text-align: center;'>Nenhuma rota disponível no momento.</p>";
            }
            ?>
          </div>
        </div>

        <!-- TAB NOTIFICAÇÕES -->
        <div id="notificacoes" class="tab-content">
          <?php
          $noticesRes = $mysqli->query("SELECT * FROM notices ORDER BY created_at DESC");
          if ($noticesRes->num_rows > 0) {
            while ($n = $noticesRes->fetch_assoc()) {
              $tagClass = 'blue';
              if ($n['tag'] === 'Manutenção')
                $tagClass = 'red';
              if ($n['tag'] === 'Novidades')
                $tagClass = 'green';

              $date = date('d/m/Y H:i', strtotime($n['created_at']));

              echo "
                    <div class='card' style='margin-bottom: 16px; padding: 16px;'>
                        <div style='display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;'>
                            <h3 style='font-size:18px; margin:0;'>" . htmlspecialchars($n['title']) . "</h3>
                            <span class='badge $tagClass'>" . htmlspecialchars($n['tag']) . "</span>
                        </div>
                        <p style='color:var(--text); margin-bottom:8px; line-height: 1.5;'>" . nl2br(htmlspecialchars($n['body'])) . "</p>
                        <small class='text-muted'><i class='ri-time-line'></i> $date</small>
                    </div>";
            }
          } else {
            echo "<p class='text-muted' style='text-align: center;'>Nenhuma notificação encontrada.</p>";
          }
          ?>
        </div>

      </div>
    </div>
  </div>

  <script>
    function openTab(evt, tabName) {
      // Hide all tab contents
      const contents = document.getElementsByClassName('tab-content');
      for (let i = 0; i < contents.length; i++) {
        contents[i].classList.remove('active');
      }

      // Remove active class from all buttons
      const buttons = document.getElementsByClassName('tab-btn');
      for (let i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('active');
      }

      // Show current tab
      document.getElementById(tabName).classList.add('active');

      // Add active class to the clicked button
      evt.currentTarget.classList.add('active');
    }
  </script>

</body>

</html>