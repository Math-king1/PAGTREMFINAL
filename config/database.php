<?php
/**
 * PAGTREM - Configuração do Banco de Dados
 * Conexão MySQLi com MySQL
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'PAGTREM');
define('DB_USER', 'root');          // Altere conforme seu ambiente
define('DB_PASS', '');              // Altere conforme seu ambiente
define('DB_CHARSET', 'utf8mb4');

/**
 * Cria e retorna a conexão MySQLi
 * @return mysqli
 */
function getConnection(): mysqli {
    static $mysqli = null;
    
    if ($mysqli === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $mysqli->set_charset(DB_CHARSET);
        } catch (mysqli_sql_exception $e) {
            // Log do erro (em produção, não exibir detalhes)
            error_log("Erro de conexão: " . $e->getMessage());
            die("Erro ao conectar ao banco de dados. Verifique as configurações.");
        }
    }
    
    return $mysqli;
}

/**
 * Inicia sessão segura
 */
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verifica se usuário está logado
 * @return bool
 */
function isLoggedIn(): bool {
    initSession();
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Verifica se usuário é admin
 * @return bool
 */
function isAdmin(): bool {
    initSession();
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Redireciona para página
 * @param string $url
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Sanitiza saída HTML
 * @param string|null $string
 * @return string
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Exibe mensagem flash
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlash(string $type, string $message): void {
    initSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Obtém e limpa mensagem flash
 * @return array|null
 */
function getFlash(): ?array {
    initSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>

