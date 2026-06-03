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
<body style="background: url('img/index2.png') center/cover no-repeat fixed;">
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
                        echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
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
                    echo '<li><a class="dropdown-item" href="gerir_criancas.php">Gerir Alunos</a></li>';
                    echo '<li><a class="dropdown-item" href="criar_perfil.php">Inscrever Aluno</a></li>';
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

<div class="container" style="padding-top: 120px; padding-bottom: 120px;">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
				<div class="card-body text-center" style="padding: 3rem;">
					<h1 style="color: #00d0ff; margin-bottom: 1.5rem;">Bem-vindo à Algazarra</h1>
					<p style="font-size: 1.1rem; color: #666; margin-bottom: 2rem;">
						Descubra as melhores atividades para as crianças. Diversão, aprendizagem e memórias inesquecíveis!
                        </p>
					<div class="d-flex gap-3 justify-content-center flex-wrap">
						<a href="atividades.php" class="btn btn-main btn-lg">Ver Atividades</a>
						<a href="contactos.php" class="btn btn-custom btn-lg">Contacte-nos</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

	<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js_check.js"></script></body>
</html>