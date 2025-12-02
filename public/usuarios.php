<?php
session_start(); require_once __DIR__.'/db.php'; if(!isset($_SESSION['user'])){ header('Location:index.php'); exit; }
// somente admin (opcional)
// if($_SESSION['user']['tipo'] != 2){ die('Acesso Negado'); }


$errors=[];
// criar novo usuário
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='create'){
$nome = trim($_POST['nome']); $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL); $senha = $_POST['senha']; $tipo = (int)$_POST['tipo'];
if(!$nome||!$email||!$senha) $errors[]='Preencha todos os campos';
if(empty($errors)){
$hash = password_hash($senha,PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO usuario (nome_completo,email,senha,tipo_usuario) VALUES (?,?,?,?)');
$stmt->execute([$nome,$email,$hash,$tipo]);
header('Location: users.php'); exit;
}
}


// delete
if(isset($_GET['delete'])){
$id = (int)$_GET['delete'];
$pdo->prepare('DELETE FROM usuario WHERE id_usuario = ?')->execute([$id]);
header('Location: users.php'); exit;
}


$users = $pdo->query('SELECT id_usuario,nome_completo,email,tipo_usuario,data_criacao FROM usuario')->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Usuários - PAGTREM</title><link rel="stylesheet" href="style/style.css"></head><body>
<div class="header"><div class="container"><div style="display:flex;justify-content:space-between;align-items:center"><h1>PAGTREM</h1><a style="color:#fff" href="dashboard.php">Voltar</a></div></div></div>
<div class="container">
<div class="card">
<h2>Cadastrar Usuário</h2>
<?php if($errors) echo '<div style="color:red">'.implode('<br>',$errors).'</div>'; ?>
<form method="post">
<input type="hidden" name="action" value="create">
<label>Nome</label><input name="nome">
<label>Email</label><input name="email" type="email">
<label>Senha</label><input name="senha" type="password">
<label>Tipo (1 user / 2 admin)</label><input name="tipo" type="number" value="1">