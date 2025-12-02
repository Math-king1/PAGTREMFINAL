<?php
session_start();
require_once __DIR__ . '/db.php';


// se já logado
if (isset($_SESSION['user'])) {
header('Location: dashboard.php');
exit;
}


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'];


$stmt = $pdo->prepare('SELECT * FROM usuario WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$u = $stmt->fetch();
if ($u && password_verify($senha, $u['senha'])) {
// guarda dados essenciais
$_SESSION['user'] = [
'id' => $u['id_usuario'],
'nome' => $u['nome_completo'],
'email' => $u['email'],
'tipo' => $u['tipo_usuario']
];
header('Location: dashboard.php');
exit;
}
$errors[] = 'Usuário ou senha inválidos.';
}
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="../style/style.css">
<meta charset="utf-8">
<title>PAGTREM - Login</title>
<link rel="stylesheet" href="../style/style.css">
</head>
<body>
<div class="header">
<div class="container">
<div style="display:flex;justify-content:space-between;align-items:center">
<h1>PAGTREM</h1>
<div class="small">Sistema de administração de trens</div>
</div>
</div>
</div>
<div class="container">
<div class="card" style="max-width:480px;margin:0 auto">
<h2>Login</h2>
<?php if($errors): ?>
<div style="color:red;margin-bottom:10px"><?=implode('<br>',$errors)?></div>
<?php endif; ?>
<form method="post">
<label>Email</label>
<input type="email" name="email" required class="input">
<label>Senha</label>
<input type="password" name="senha" required class="input">
<div style="margin-top:12px">
<button class="btn" name="login">Entrar</button>
<a href="register.php" style="margin-left:8px">Cadastrar</a>
</div>
</form>
</div>
</div>
</body>
</html>