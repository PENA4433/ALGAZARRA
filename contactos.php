<?php
session_start();
include './basedados.h';
?>

<html lang="pt">

<head>
	<meta charset="UTF-8">
	<title>Algazarra</title>
	<link rel="stylesheet" href="bootstrap.min.css">

	<!-- 🔥 FONTE MODERNA -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Inter', sans-serif;
			background-color: #F5F5F5;
			margin: 10vh 0;
		}
	</style>
</head>

<body>

	<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;">
		<div class="container-fluid">
			<a class="navbar-brand" href="index.php">ALGAZARRA</a>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarColor01">
				<ul class="navbar-nav me-auto">
					<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="atividades.php">Atividades</a></li>
					<li class="nav-item"><a class="nav-link active" href="contactos.php">Contactos</a></li>

					<?php
					if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1) {
						echo '
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Gestão</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>
							<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
							<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>
							<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>
						</ul>
					</li>';
					}

					if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
						echo '
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Gestão</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
						</ul>
					</li>';
					}
					?>
				</ul>

				<ul class="navbar-nav ms-auto">
					<?php
					if (isset($_SESSION['id_user']))
						echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';

					echo '<li class="nav-item">';
					if (isset($_SESSION['id_user']))
						echo '<a class="nav-link" href="dados.php">Dados pessoais</a>';
					else
						echo '<a class="nav-link" href="login.php">Login/Registo</a>';
					echo '</li>';
					?>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container" style="padding: 3vh;">

		<div class="text-center">

			<div class="row mt-4">

				<!-- HORÁRIO -->
				<div class="col-lg-4">
					<h4 class="fw-bold">Horário de funcionamento</h4>

					<div class="card border-0 shadow-sm mt-3">
						<table class="table table-sm mb-0">
							<tbody>
								<?php
								$sql = 'SELECT * FROM Horario ORDER BY id';
								$dias = mysqli_query($conn, $sql);

								if (!$dias)
									die('Falha tecnica.');

								$horarios = [];

								while ($horario = mysqli_fetch_assoc($dias)) {
									$dia = strtolower($horario['dia_semana']);

									if (
										!$horario['hora_inicio'] ||
										!$horario['hora_fim'] ||
										($horario['hora_inicio'] == '00:00:00' && $horario['hora_fim'] == '00:00:00')
									) {
										$horarios[$dia] = 'Fechado';
									} else {
										$horarios[$dia] = date('H:i', strtotime($horario['hora_inicio']))
											. ' - '
											. date('H:i', strtotime($horario['hora_fim']));
									}
								}

								echo '<tr>
									<th>segunda a sexta</th>
									<td>' . $horarios['segunda-feira'] . '</td>
								</tr>';

								echo '<tr>
									<th>sábado</th>
									<td>' . $horarios['sábado'] . '</td>
								</tr>';

								echo '<tr>
									<th>domingo</th>
									<td>' . $horarios['domingo'] . '</td>
								</tr>';
								?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- MAPA -->
				<div class="col-lg-8">
					<h4 class="fw-bold">Localização</h4>

					<div style="position: relative; overflow: hidden; padding-top: 50%; background-color: #F2F2F2;">
						<iframe
							src="https://www.google.com/maps?q=Escola%20Superior%20de%20Tecnologia%20Castelo%20Branco&output=embed"
							style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;"
							allowfullscreen>
						</iframe>
					</div>
				</div>

			</div>

		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php include './rodape.php'; ?>