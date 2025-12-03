<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="bottom-nav">
  <a href="cliente_home.php" class="<?php echo $current_page == 'cliente_home.php' ? 'active' : ''; ?>">
    <img src="../assets/images/rotas_icone.png" alt="Rotas" class="icon-img" style="width:24px;height:24px;margin-bottom:4px;">
    <span>Rotas</span>
  </a>

  <a href="perfil_cliente.php" class="<?php echo $current_page == 'perfil_cliente.php' ? 'active' : ''; ?>">
    <img src="../assets/images/icone_adm.png" alt="Perfil" class="icon-img" style="width:24px;height:24px;margin-bottom:4px;">
    <span>Perfil</span>
  </a>

  <a href="logout.php">
    <img src="../assets/images/logout_icone.png" alt="Sair" class="icon-img" style="width:24px;height:24px;margin-bottom:4px;">
    <span>Sair</span>
  </a>
</div>
