<?php
/**
 * PAGTREM - Dashboard Usuário
 */

require_once __DIR__ . '/../../config/database.php';

initSession();

// Verifica se está logado
if (!isLoggedIn()) {
    redirect('../index.php');
}

try {
    $mysqli = getConnection();

    // Busca notificações ativas (hoje ou futuras)
    $stmt = $mysqli->prepare("SELECT titulo, mensagem, data_notificacao FROM notificacoes WHERE status = 'ativa' AND data_notificacao >= CURDATE() ORDER BY data_notificacao ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    error_log("Erro ao carregar notificações: " . $e->getMessage());
    $notifications = [];
}

// Obtém mensagem flash
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Dashboard Usuário</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="dashboard-header">
        <h2>🚆 PAGTREM - Painel do Usuário</h2>
        <p>Bem-vindo, <?= e($_SESSION['user']['nome'] ?? 'Usuário') ?>!</p>
        <div style="margin-top: 1rem;">
            <a href="../logout.php" class="btn btn-secondary">Sair</a>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="notification-card">
        <h4>📢 Notificações Ativas</h4>
        <?php if (empty($notifications)): ?>
            <p>Nenhuma notificação ativa no momento.</p>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item" style="border-bottom: 1px solid #dee2e6; padding: 1rem 0;">
                    <h5><?= e($notification['titulo']) ?></h5>
                    <p><?= e($notification['mensagem']) ?></p>
                    <small>Data: <?= date('d/m/Y', strtotime($notification['data_notificacao'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
