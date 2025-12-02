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

// Se for admin, redireciona para dashboard admin
if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$user = $_SESSION['user'];

try {
    $pdo = getConnection();
    
    // Busca trens ativos
    $trens = $pdo->query("SELECT * FROM trens WHERE status = 'ativo' ORDER BY nome")->fetchAll();
    
    // Busca notificações ativas
    $notificacoes = $pdo->query("
        SELECT * FROM notificacoes 
        WHERE status = 'ativa' 
        ORDER BY data_notificacao DESC 
        LIMIT 5
    ")->fetchAll();
    
} catch (PDOException $e) {
    error_log("Erro no dashboard: " . $e->getMessage());
    $trens = [];
    $notificacoes = [];
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAGTREM - Meu Painel</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>PAGTREM</h1>
            <nav class="header-nav">
                <div class="user-info">
                    <span><?= e($user['nome']) ?></span>
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
            <h2>👋 Olá, <?= e($user['nome']) ?>!</h2>
            <p class="text-muted">Bem-vindo ao sistema PAGTREM.</p>
        </div>
        
        <!-- Notificações -->
        <div class="card">
            <h3 class="card-title">🔔 Notificações Recentes</h3>
            
            <?php if (empty($notificacoes)): ?>
                <div class="empty-state">
                    <div class="icon">🔔</div>
                    <p>Nenhuma notificação no momento.</p>
                </div>
            <?php else: ?>
                <div style="margin-top: 1rem;">
                    <?php foreach ($notificacoes as $notif): ?>
                        <div class="card" style="background: #f8fafc; margin-bottom: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem; flex-wrap: wrap;">
                                <div>
                                    <strong><?= e($notif['titulo']) ?></strong>
                                    <p class="text-muted" style="margin-top: 0.25rem; font-size: 0.9rem;">
                                        <?= e($notif['mensagem']) ?>
                                    </p>
                                </div>
                                <span class="badge badge-info">
                                    <?= date('d/m/Y', strtotime($notif['data_notificacao'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Trens Disponíveis -->
        <div class="card">
            <h3 class="card-title">🚆 Trens Ativos</h3>
            
            <?php if (empty($trens)): ?>
                <div class="empty-state">
                    <div class="icon">🚆</div>
                    <p>Nenhum trem ativo no momento.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Capacidade</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($trens as $trem): ?>
                                <tr>
                                    <td><strong><?= e($trem['nome']) ?></strong></td>
                                    <td>
                                        <span class="badge badge-success">Ativo</span>
                                    </td>
                                    <td><?= $trem['capacidade'] ?> passageiros</td>
                                    <td><?= e($trem['notas'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="footer">
        <p>PAGTREM &copy; <?= date('Y') ?> - Sistema de Gerenciamento de Trens</p>
    </footer>
    
    <script src="../../assets/js/main.js"></script>
</body>
</html>

