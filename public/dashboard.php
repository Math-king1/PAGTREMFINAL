<?php
session_start();
require_once __DIR__.'/db.php';
if(!isset($_SESSION['user'])){ header('Location: index.php'); exit; }
$user = $_SESSION['user'];
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="/style/style.css">
<meta charset="utf-8">
<title>PAGTREM - Dashboard</title>
<link rel="stylesheet" href="style/style.css">
</head>
<body>
<div class="header"><div class="container">
<div style="display:flex;justify-content:space-between;align-items:center">
<div>
<strong>PAGTREM</strong>
</div>
<div>
<span class="small">Usuário: <?=$user['nome']?></span>
<a href="logout.php" style="color:#fff;margin-left:12px">Sair</a>
</div>
</div>
</div></div>
<div class="container">
<div class="card">
<h2>Dashboard</h2>
<div style="display:flex;gap:10px">
<a class="btn" href="users.php">CRUD Usuários</a>
<a class="btn" href="trains.php">CRUD Trens</a>
<a class="btn" href="sensors.php">CRUD Sensores</a>
</div>
</div>
</div>
</body>
</html>