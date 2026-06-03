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

<div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 200px); padding: 2vh;">

<?php
if (isset($_SESSION['err'])) {
	echo '
	<div class="alert alert-danger" style="position: absolute; top: 100px; width: 90%; max-width: 400px;">
		<button type="button" class="btn-close" data-bs-dismiss="alert"
			onclick="window.location.href = \'login.php\';"></button>
		<strong>' . $_SESSION['err'] . '</strong>
	</div>';
	unset($_SESSION['err']);
}
?>

	<div class="card login-card" style="width: 100%; max-width: 450px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
		<div class="card-body text-center" style="padding: 3rem;">

			<h1 style="color: #00d0ff; margin-bottom: 2rem; font-weight: 700;">Login</h1>

			<form action="load_login.php" method="post">

				<div class="form-group mt-3">
					<label class="form-label">Nome de utilizador</label>
					<input type="text" class="form-control" name="user" required>
				</div>

				<div class="form-group mt-3">
					<label class="form-label">Palavra-passe</label>
					<input type="password" class="form-control" name="pwd" required>
				</div>

				<input type="submit" class="btn btn-main mt-4 w-100" value="Entrar">

				<hr class="my-4">

				<p style="color: #666; margin-bottom: 1rem;">Ainda não tem conta?</p>

				<a class="btn btn-register w-100" href="registo.php">
					CRIAR CONTA
				</a>

			</form>

		</div>
	</div>

</div>

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>