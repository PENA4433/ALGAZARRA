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

	<!-- 🔥 FONTE GLOBAL -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background-color: #f2f6ff;
		}

		* {
			font-family: 'Poppins', sans-serif;
		}

		.login-card {
			background-color: white;
			border: none;
			border-radius: 18px;
			box-shadow: 0 10px 25px rgba(0,0,0,0.08);
		}

		/* 🔴 BOTÃO ENTRAR AGORA VERMELHO */
		.btn-main {
			background-color: #dc3545;
			color: white;
			font-weight: 600;
			border: none;
		}

		.btn-main:hover {
			background-color: #bb2d3b;
			color: white;
		}

		/* 🔴 REGISTO também vermelho consistente */
		.btn-register {
			background-color: #dc3545;
			color: white;
			font-weight: 600;
			border: none;
		}

		.btn-register:hover {
			background-color: #bb2d3b;
			color: white;
		}

		input, label, h1, h4, p, a, button {
			font-family: 'Poppins', sans-serif;
		}
	</style>
</head>

<body style="margin: 10vh 0 10vh 0;">

<!-- NAVBAR -->
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
				<li class="nav-item"><a class="nav-link active" href="login.php">Login/Registo</a></li>
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
		<div class="card-body">

			<div class="row d-flex align-items-center">

				<!-- REGISTO -->
				<div class="col-md-6 text-center" style="padding:3vh;">
					<h1>Bem-vindo à Algazarra!</h1>
					<h4>Ainda não criou uma conta?</h4>

					<a href="registo.php" class="btn btn-register mt-3">
						Registe-se
					</a>
				</div>

				<!-- LOGIN -->
				<div class="col-md-6 text-center" style="padding:3vh;">

					<h1>Login</h1>

					<form action="load_login.php" method="post">

						<div class="form-group mt-3" style="width: 50vh; margin:auto;">
							<label>Nome de utilizador</label>
							<input type="text" class="form-control" name="user" required>
						</div>

						<div class="form-group mt-3" style="width: 50vh; margin:auto;">
							<label>Palavra-passe</label>
							<input type="password" class="form-control" name="pwd" required>
						</div>

						<input type="submit" class="btn btn-main mt-4" value="Entrar">

					</form>

				</div>

			</div>

		</div>
	</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php include './rodape.php'; ?>