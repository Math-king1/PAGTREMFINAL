<?php
session_start();

// Remove todos os dados da sessão
session_unset();
session_destroy();

// Evita que a página seja carregada a partir do cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redireciona para a página de login do admin
header("Location: login_admin.php");
exit;
