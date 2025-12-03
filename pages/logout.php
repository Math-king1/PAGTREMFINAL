<?php
/**
 * PAGTREM - Logout
 */

require_once __DIR__ . '/../config/database.php';

initSession();

// Limpa a sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Redireciona para login
redirect('index.php');
?>
