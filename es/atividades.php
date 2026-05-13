<?php
session_start();
include './basedados.h';
?>

<html lang="pt">
<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">

	<!-- 🔥 FONTE GLOBAL -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Inter', sans-serif;
			background-color: #F5F5F5;
			margin: 10vh 0;
		}

		.navbar {
			background-color: #00d0ff !important;
		}

		.card {
			border: none;
			border-radius: 12px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.08);
			transition: 0.2s;
		}

		.card:hover {
			transform: translateY(-3px);
		}

		/* 🔥 BOTÃO AGORA VERMELHO */
		.btn-custom {
			background-color: #dc3545;
			color: white;
			font-weight: 600;
			border: none;
		}

		.btn-custom:hover {
			background-color: #bb2d3b;
			color: white;
		}
	</style>
</head>

<body>

<nav class="fixed-top navbar navbar-expand-lg">
	<div class="container-fluid">
		<a class="navbar-brand" href="index.php">ALGAZARRA</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarColor01">

			<ul class="navbar-nav me-auto">
				<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link active" href="atividades.php">Atividades</a></li>
				<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>
			</ul>

			<ul class="navbar-nav ms-auto">
				<?php
				if (isset($_SESSION['id_user']))
					echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';

				echo '<li class="nav-item">';
				if (isset($_SESSION['id_user']))
					echo '<a class="nav-link" href="dados.php">Dados pessoais</a>';
				else
					echo '<a class="nav-link" href="login.php">Login</a>';
				echo '</li>';
				?>
			</ul>

		</div>
	</div>
</nav>

<div class="container" style="padding:3vh;">

	<div class="text-center">
		<h1>Atividades</h1>
	</div>

	<div class="row justify-content-center mt-4">

	<?php
	$sql = 'SELECT * FROM Atividade ORDER BY data_inicio';
	$atividades = mysqli_query($conn, $sql);

	if (mysqli_num_rows($atividades) > 0) {

		while ($atividade = mysqli_fetch_assoc($atividades)) {

			echo '
			<div class="col-md-4 d-flex justify-content-center">
				<div class="card m-3" style="width: 20rem;">

					<img src="' . $atividade['imagem'] . '" class="card-img-top" style="height:200px; object-fit:cover;">

					<div class="card-body text-center">

						<h5 class="card-title">' . $atividade['titulo'] . '</h5>

						<p>
							' . date('d/m/Y', strtotime($atividade['data_inicio'])) . ' - ' . date('d/m/Y', strtotime($atividade['data_fim'])) . '
						</p>

						<a href="pag_atividades.php?id=' . $atividade['id'] . '" class="btn btn-custom w-100">
							Saber mais
						</a>

					</div>

				</div>
			</div>';
		}

	} else {
		echo '<h3 class="text-center mt-4">Não estão a decorrer atividades.</h3>';
	}
	?>

	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

<?php include './rodape.php'; ?>
</html>