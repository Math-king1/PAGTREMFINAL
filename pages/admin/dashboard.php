<?php
/**
 * PAGTREM - Dashboard Admin
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

try {
    $mysqli = getConnection();

    // Total de usuários
    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM usuarios");
    $stmt->execute();
    $total_users = $stmt->get_result()->fetch_assoc()['total'];

    // Total de notificações ativas
    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM notificacoes WHERE status = 'ativa'");
    $stmt->execute();
    $total_notifications = $stmt->get_result()->fetch_assoc()['total'];

} catch (mysqli_sql_exception $e) {
    error_log("Erro ao carregar dashboard: " . $e->getMessage());
    $total_users = 0;
    $total_notifications = 0;
}

// Obtém mensagem flash
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
    <div class="dashboard-header">
        <h2>🚆 PAGTREM - Painel Administrativo</h2>
        <p>Bem-vindo, <?= e($_SESSION['user']['nome'] ?? 'Admin') ?>!</p>
        <div style="margin-top: 1rem;">
            <a href="users.php" class="btn btn-primary">Gerenciar Usuários</a>
            <a href="notifications.php" class="btn btn-primary">Gerenciar Notificações</a>
            <a href="../logout.php" class="btn btn-secondary">Sair</a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= $total_users ?></h3>
            <p>Total de Usuários</p>
        </div>
        <div class="stat-card">
            <h3><?= $total_notifications ?></h3>
            <p>Notificações Ativas</p>
        </div>
    </div>

    <div class="notification-card">
        <h4>📋 Ações Rápidas</h4>
        <p>Use os botões acima para gerenciar usuários e notificações do sistema.</p>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
