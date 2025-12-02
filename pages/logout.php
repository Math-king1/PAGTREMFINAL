<?php
/**
 * PAGTREM - Logout
 */

require_once __DIR__ . '/../config/database.php';

initSession();

// Limpa todos os dados da sessão
$_SESSION = [];

// Destrói o cookie da sessão
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destrói a sessão
session_destroy();

// Redireciona para login
header('Location: index.php');
exit;
?>

