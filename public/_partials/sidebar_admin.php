<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <i class="ri-train-line" style="font-size: 32px; color: var(--brand);"></i>
        <span>PagTrem</span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="ri-dashboard-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="avisos.php" class="<?php echo $current_page == 'avisos.php' ? 'active' : ''; ?>">
            <i class="ri-notification-3-line"></i>
            <span>Avisos</span>
        </a>
        <a href="rotas.php" class="<?php echo $current_page == 'rotas.php' ? 'active' : ''; ?>">
            <i class="ri-map-pin-line"></i>
            <span>Rotas</span>
        </a>
        <a href="funcionarios.php" class="<?php echo $current_page == 'funcionarios.php' ? 'active' : ''; ?>">
            <i class="ri-group-line"></i>
            <span>Funcionários</span>
        </a>
        <a href="meu_perfil.php" class="<?php echo $current_page == 'meu_perfil.php' ? 'active' : ''; ?>">
            <i class="ri-user-settings-line"></i>
            <span>Perfil</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout_admin.php" class="logout-link">
            <i class="ri-logout-box-r-line"></i>
            <span>Sair</span>
        </a>
    </div>
</div>