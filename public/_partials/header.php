<?php if (session_status() === PHP_SESSION_NONE)
  session_start(); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="../assets/css/styles.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<div class="header container">
  <div class="logo">
    <div class="brand-icon"><img src="../assets/images/trem_icone.png" alt="Trem" class="icon-img"
        style="width:28px;height:28px;"></div>
    <div>
      <div style="font-size:18px">PagTrem</div>
      <div class="link-muted">Sistema de Gerenciamento</div>
    </div>
  </div>
  <div style="margin-left:auto" class="navbar">
    <a href="dashboard.php">Dashboard</a>
    <a href="cameras.php">Câmeras</a>
    <a href="rotas.php">Rotas</a>
    <a href="avisos.php">Avisos</a>


    <a href="perfil.php">Perfil</a>
    <a href="logout.php" class="badge">Sair</a>
  </div>
</div>