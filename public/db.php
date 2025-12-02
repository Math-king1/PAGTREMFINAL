<?php
$host = "localhost";
$dbname = "PAGTREM";
$user = "admin_trem";
$pass = "trem123";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro BD: " . $e->getMessage();
}
?>
