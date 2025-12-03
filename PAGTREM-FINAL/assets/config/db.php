<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'vaidetrem2';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  die('Erro ao conectar ao MySQL: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>