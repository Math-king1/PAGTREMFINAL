<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="bottom-nav">
  <a href="usuario_home.php" class="<?php echo $current_page == 'usuario_home.php' ? 'active' : ''; ?>">
    <i class="ri-map-pin-line"></i>
    <span>Rotas</span>
  </a>

  <a href="perfil_usuario.php" class="<?php echo $current_page == 'perfil_usuario.php' ? 'active' : ''; ?>">
    <i class="ri-user-smile-line"></i>
    <span>Perfil</span>
  </a>

  <a href="logout.php">
    <i class="ri-logout-box-r-line"></i>
    <span>Sair</span>
  </a>
</div>