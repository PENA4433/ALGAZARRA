<?php
	session_start();
	include './basedados.h';

	if (isset($_SESSION['id_user']) || isset($_SESSION['nivel']))
		include './erro.php';
?>
<html lang="pt">
<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color: #F5F5F5; margin: 10vh 0 10vh 0;">
	<nav class="fixed-top navbar navbar-expand-lg" style="background-color:  #00d0ff; border: none;" data-bs-theme="light">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarColor01">
			<ul class="navbar-nav me-auto">
				<li class="nav-item">
					<a class="nav-link" href="index.php">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="atividades.php">Atividades</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="contactos.php">Contactos</a>
				</li>
			</ul>
			<ul class="navbar-nav ms-auto">
			<li class="nav-item">
				<a class="nav-link active" href="login.php">Login/Registo</a>
			</li>
			</ul>
		</div>
	</div>
	</nav>
	
	<div class="container" style="padding: 2vh;">
	<?php
	if (isset($_SESSION['err']))
	{
		echo
		'<div class="alert alert-dismissible alert-danger mx-auto">
			<button type="button" class="btn-close" data-bs-dismiss="alert" onclick="window.location.href = \'registo.php\';"></button>
			<strong>'; echo $_SESSION['err']; unset($_SESSION['err']); echo '</strong></a>
		</div>';
	}
	?>
		<div class="card" style="background-color: #C5C6D0; border: none; padding: 3vh;">
			<div class="card-body">
			<h1 class="text-center">Registo</h1>
				<form action="load_registo.php" method="post">
				<div class="row">
					<div class="form-group col-md-6" style="padding-top: 20px;">
						<label>Nome completo (* Indica um campo obrigatório)</label>
						<input type="text" class="form-control" name="nome" placeholder="Ex.: Cristiano Ronaldo" required>
					</div>
					<div class="form-group col-md-6" style="padding-top: 20px;">
						<label>Data de nascimento *</label>
						<input type="date" class="form-control" name="data_nascimento" max="'. date('Y-m-d') .'" required>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6" style="padding-top: 20px;">
						<label>Número de telemóvel *</label>
						<input type="text" class="form-control" name="telemovel" placeholder="Ex.: 967788999" required>
					</div>
					<div class="form-group col-md-6" style="padding-top: 20px;">
						<label>Endereço de email *</label>
						<input type="email" class="form-control" name="email" placeholder="Ex.: jojo@gmail.com" required>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-md-6" style="padding-top: 20px;">
						<label>Nome de utilizador *</label>
						<input type="text" class="form-control" name="user" placeholder="Ex.: João" required>
					</div>
					<div class="form-group col-md-6" style="padding-top: 20px;">
						<label>Palavra-passe *</label>
						<input type="password" class="form-control" name="pwd" placeholder="Palavra-passe" autocomplete="off" required>
					</div>
				</div>
				<div style="text-align: right;">
					<input type="submit" class="btn" style="background-color: #FD7D21; color: white; font-weight: bold; margin-top: 5vh;" value="Registar">
				</div>
			</form>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
	</script>
</body>
<?php
	include './rodape.php';
?>