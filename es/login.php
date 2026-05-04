<?php
session_start();
include './basedados.h';

if (isset($_SESSION['id_user']) || isset($_SESSION['nivel']))
	include './erro.h';
?>

<html lang="pt">
<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background-color: #f2f6ff;
		}

		.login-card {
			background-color: white;
			border: none;
			border-radius: 18px;
			box-shadow: 0 10px 25px rgba(0,0,0,0.08);
			max-width: 450px;
			margin: auto;
		}

		.card-body {
			padding: 2rem;
		}

		/* ⚫ BOTÕES SEM VERMELHO */
		.btn-main, .btn-register {
			background-color: #e9ecef;
			color: #333;
			font-weight: 600;
			border: none;
		}

		.btn-main:hover, .btn-register:hover {
			background-color: #ced4da;
			color: #000;
		}
	</style>
</head>

<body style="margin: 10vh 0 10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff;">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav me-auto">
				<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link" href="atividades.php">Atividades</a></li>
				<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>
			</ul>

			<ul class="navbar-nav ms-auto">
				<li class="nav-item"><a class="nav-link active" href="login.php">Login</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container" style="padding:2vh;">

<?php
if (isset($_SESSION['err'])) {
	echo '
	<div class="alert alert-danger mx-auto">
		<button type="button" class="btn-close" data-bs-dismiss="alert"
			onclick="window.location.href = \'login.php\';"></button>
		<strong>' . $_SESSION['err'] . '</strong>
	</div>';
	unset($_SESSION['err']);
}
?>

	<div class="card login-card">
		<div class="card-body text-center">

			<h1>Login</h1>

			<form action="load_login.php" method="post">

				<div class="form-group mt-3">
					<label>Nome de utilizador</label>
					<input type="text" class="form-control" name="user" required>
				</div>

				<div class="form-group mt-3">
					<label>Palavra-passe</label>
					<input type="password" class="form-control" name="pwd" required>
				</div>

				<input type="submit" class="btn btn-main mt-4 w-100" value="Entrar">

				<br><br>

				<h5>Ainda não tem conta?</h5>

				<a class="btn btn-register mt-2 w-100" href="registo.php">
					CRIAR CONTA
				</a>

			</form>

		</div>
	</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php include './rodape.php'; ?>