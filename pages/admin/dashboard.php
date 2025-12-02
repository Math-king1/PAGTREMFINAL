<?php
/**
 * PAGTREM - Dashboard Admin
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado e é admin
if (!isLoggedIn()) {
    redirect('../index.php');
}
if (!isAdmin()) {
    redirect('../user/dashboard.php');
}

$user = $_SESSION['user'];

try {
    $pdo = getConnection();
    
    // Estatísticas
    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $totalTrens = $pdo->query("SELECT COUNT(*) FROM trens")->fetchColumn();
    $trensAtivos = $pdo->query("SELECT COUNT(*) FROM trens WHERE status = 'ativo'")->fetchColumn();
    $totalNotificacoes = $pdo->query("SELECT COUNT(*) FROM notificacoes WHERE status = 'ativa'")->fetchColumn();
    
} catch (PDOException $e) {
    error_log("Erro no dashboard: " . $e->getMessage());
    $totalUsuarios = $totalTrens = $trensAtivos = $totalNotificacoes = 0;
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Dashboard Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>PAGTREM</h1>
            <nav class="header-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php">Usuários</a>
                <a href="trains.php">Trens</a>
                <a href="notifications.php">Notificações</a>
                <div class="user-info">
                    <span><?= e($user['nome']) ?></span>
                    <span class="user-badge">Admin</span>
                </div>
                <a href="../logout.php">Sair</a>
            </nav>
        </div>
    </header>
    
    <!-- Conteúdo -->
    <div class="container">
        
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>👋 Bem-vindo, <?= e($user['nome']) ?>!</h2>
            <p class="text-muted">Painel de administração do sistema PAGTREM.</p>
        </div>
        
        <!-- Estatísticas -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon blue">👥</div>
                <div class="stat-info">
                    <h3><?= $totalUsuarios ?></h3>
                    <p>Usuários</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">🚆</div>
                <div class="stat-info">
                    <h3><?= $totalTrens ?></h3>
                    <p>Trens</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon yellow">✅</div>
                <div class="stat-info">
                    <h3><?= $trensAtivos ?></h3>
                    <p>Trens Ativos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">🔔</div>
                <div class="stat-info">
                    <h3><?= $totalNotificacoes ?></h3>
                    <p>Notificações</p>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="card mt-3">
            <h3 class="card-title">⚡ Ações Rápidas</h3>
            <div class="quick-actions mt-2">
                <a href="users.php" class="action-btn">
                    <span class="icon">👥</span>
                    <span>Usuários</span>
                </a>
                <a href="trains.php" class="action-btn">
                    <span class="icon">🚆</span>
                    <span>Trens</span>
                </a>
                <a href="notifications.php" class="action-btn">
                    <span class="icon">🔔</span>
                    <span>Notificações</span>
                </a>
                <a href="../logout.php" class="action-btn">
                    <span class="icon">🚪</span>
                    <span>Sair</span>
                </a>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>PAGTREM &copy; <?= date('Y') ?> - Sistema de Gerenciamento de Trens</p>
    </footer>
    
    <script src="../../assets/js/main.js"></script>
</body>
</html>

