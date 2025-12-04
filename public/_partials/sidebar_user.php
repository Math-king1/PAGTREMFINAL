<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <i class="ri-train-line" style="font-size: 32px; color: var(--brand);"></i>
        <span>PagTrem</span>
    </div>
    <nav class="sidebar-nav">
        <a href="usuario_home.php" class="<?php echo $current_page == 'usuario_home.php' ? 'active' : ''; ?>">
            <i class="ri-map-pin-line"></i>
            <span>Rotas</span>
        </a>
        <a href="perfil_usuario.php" class="<?php echo $current_page == 'perfil_usuario.php' ? 'active' : ''; ?>">
            <i class="ri-user-smile-line"></i>
            <span>Perfil</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-link">
            <i class="ri-logout-box-r-line"></i>
            <span>Sair</span>
        </a>
    </div>
</div>