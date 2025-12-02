<?php
/**
 * PAGTREM - Script de Seed
 * Cria o usuário admin se não existir
 * Executar uma vez após criar o banco de dados
 * 
 * Acesse: http://localhost/PAGTREMFINAL/PAGTREMFINAL/config/seed.php
 */

require_once __DIR__ . '/database.php';

echo "<h2>PAGTREM - Seed do Banco de Dados</h2>";

try {
    $pdo = getConnection();
    
    // Verifica se o admin já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute(['admin']);
    
    if (!$stmt->fetch()) {
        // Cria o usuário admin
        // Senha: lucas123
        $senhaHash = password_hash('lucas123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (username, nome_completo, email, senha, role, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'admin',
            'Administrador do Sistema',
            'admin@pagtrem.com',
            $senhaHash,
            'admin',
            'ativo'
        ]);
        
        echo "<p style='color:green'>✅ Usuário admin criado com sucesso!</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Senha:</strong> lucas123</p>";
    } else {
        echo "<p style='color:blue'>ℹ️ Usuário admin já existe.</p>";
    }
    
    echo "<p><a href='../pages/index.php'>Ir para Login</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

