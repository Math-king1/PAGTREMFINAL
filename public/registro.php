<?php
session_start();
require_once __DIR__.'/db.php';
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
$nome = trim($_POST['nome']);
$email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'];
$senha2 = $_POST['senha2'];


if(strlen($nome) < 3) $errors[]='Nome muito curto';
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Email inválido';
if($senha !== $senha2) $errors[]='Senhas não conferem';
if(strlen($senha) < 4) $errors[]='Senha deve ter ao menos 4 caracteres';


// verifica duplicidade
$stmt = $pdo->prepare('SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if($stmt->fetch()) $errors[]='Email já cadastrado';


if(empty($errors)){
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO usuario (nome_completo,email,senha,tipo_usuario) VALUES (?,?,?,1)');
$stmt->execute([$nome,$email,$hash]);
header('Location: index.php');
exit;
}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PAGTREM - Cadastro</title>
<link rel="stylesheet" href="style/style.css">
</head>
<body>
<div class="header"><div class="container"><h1>PAGTREM</h1></div></div>
<div class="container">
<div class="card" style="max-width:600px;margin:0 auto">
<h2>Cadastro</h2>
<?php if($errors): ?><div style="color:red"><?=implode('<br>',$errors)?></div><?php endif; ?>
<form method="post">
<label>Nome completo</label>
<input name="nome" required>
<label>Email</label>
<input type="email" name="email" required>
<label>Senha</label>
<input type="password" name="senha" required>
<label>Confirmar senha</label>
<input type="password" name="senha2" required>
<div style="margin-top:12px"><button class="btn">Cadastrar</button></div>
</form>
</div>
</div>
</body>
</html>