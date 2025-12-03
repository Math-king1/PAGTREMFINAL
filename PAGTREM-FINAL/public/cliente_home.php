<?php
require_once('../assets/config/auth.php');
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <title>Área do Cliente - Vai de Trem</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="../assets/css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

</head>

<body>

  <div class="container" style="max-width:960px; margin: 20px auto;">

    <div class="topbar">
      <div class="brand-icon"><img src="../assets/images/trem_icone.png" alt="Trem" class="icon-img"
          style="width:28px;height:28px;"></div>
      <h2>Olá, <?php echo htmlspecialchars($user['name']); ?>!</h2>
      <p>Bem-vindo(a) à sua área de rotas.</p>
    </div>

    <h3 class="section-title">Rotas Disponíveis</h3>
    <p class="link-muted">Escolha uma rota para visualizar mais informações.</p>

    <div class="routes-grid">

      <div class="route-card">
        <div class="route-header">
          <h3>São Paulo → Rio de Janeiro</h3>
          <span class="route-tag">Ativa</span>
        </div>
        <div class="route-info">
          <p><img src="../assets/images/local_icone.png" alt="Local" class="icon-img" style="width:16px;height:16px;">
            Estações: Central • Norte • Sul</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Tempo" class="icon-img" style="width:16px;height:16px;">
            6h 30min</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Calendário" class="icon-img"
              style="width:16px;height:16px;"> Opera diariamente</p>
        </div>
      </div>

      <div class="route-card">
        <div class="route-header">
          <h3>Campinas → Santos</h3>
          <span class="route-tag">Ativa</span>
        </div>
        <div class="route-info">
          <p><img src="../assets/images/local_icone.png" alt="Local" class="icon-img" style="width:16px;height:16px;">
            KM 45 • Ponte Rio Grande</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Tempo" class="icon-img" style="width:16px;height:16px;">
            3h 45min</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Calendário" class="icon-img"
              style="width:16px;height:16px;"> Opera diariamente</p>
        </div>
      </div>

      <div class="route-card">
        <div class="route-header">
          <h3>Belo Horizonte → São Paulo</h3>
          <span class="route-tag red">Manutenção</span>
        </div>
        <div class="route-info">
          <p><img src="../assets/images/local_icone.png" alt="Local" class="icon-img" style="width:16px;height:16px;">
            Estação Sul • Estação Central</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Tempo" class="icon-img" style="width:16px;height:16px;">
            8h 15min</p>
          <p><img src="../assets/images/notificacao_icone.png" alt="Alerta" class="icon-img"
              style="width:16px;height:16px;"> Retorno previsto: 15/11</p>
        </div>
      </div>

      <div class="route-card">
        <div class="route-header">
          <h3>Curitiba → Florianópolis</h3>
          <span class="route-tag">Ativa</span>
        </div>
        <div class="route-info">
          <p><img src="../assets/images/local_icone.png" alt="Local" class="icon-img" style="width:16px;height:16px;">
            Estação Norte • Ponte Rio Grande</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Tempo" class="icon-img" style="width:16px;height:16px;">
            5h 20min</p>
          <p><img src="../assets/images/relogio_icone.png" alt="Calendário" class="icon-img"
              style="width:16px;height:16px;"> Opera diariamente</p>
        </div>
      </div>

    </div>
  </div>

  <!-- MENU MOBILE -->
  <?php include '_partials/bottom_nav_cliente.php'; ?>

</body>

</html>