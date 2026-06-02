<?php
session_start();
include './basedados.h';
?>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff; border: none;" data-bs-theme="light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarColor01">
            <ul class="navbar-nav mx-auto align-items-center justify-content-center">
                <a class="navbar-brand" href="index.php">ALGAZARRA</a>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="atividades.php">Atividades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactos.php">Contactos</a>
                </li>
                <?php
                // Administrador
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 1) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">';
                    echo '<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>';
                    echo '<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                // Pai
                if (isset($_SESSION['id_user']) && $_SESSION['nivel'] == 2) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPai" role="button" data-bs-toggle="dropdown" aria-expanded="false">Gestão</a>';
                    echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdownPai">';
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
                ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) {
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>';
                }
                echo '<li class="nav-item">';
                if (isset($_SESSION['id_user']) && isset($_SESSION['nivel'])) {
                    echo '<a class="nav-link" href="dados.php">Dados pessoais</a>';
                } else {
                    echo '<a class="nav-link" href="login.php">Login</a>';
                }
                ?>
                </li>
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

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>