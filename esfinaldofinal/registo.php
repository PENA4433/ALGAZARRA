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

<div class="container" style="padding-top: 120px; padding-bottom: 120px;">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<?php
			if (isset($_SESSION['err']))
			{
				echo
				'<div class="alert alert-dismissible alert-danger">
					<button type="button" class="btn-close" data-bs-dismiss="alert" onclick="window.location.href = \'registo.php\';"></button>
					<strong>'; echo $_SESSION['err']; unset($_SESSION['err']); echo '</strong>
				</div>';
			}
			?>
			<div class="card" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: none;">
				<div class="card-body" style="padding: 3rem;">
					<h1 class="text-center" style="color: #00d0ff; margin-bottom: 2rem; font-weight: 700;">Registo</h1>
					<form action="load_registo.php" method="post">
						<div class="row">
							<div class="form-group col-md-6 mb-3">
								<label class="form-label">Nome completo *</label>
								<input type="text" class="form-control" name="nome" placeholder="Ex.: Cristiano Ronaldo" required>
							</div>
							<div class="form-group col-md-6 mb-3">
								<label class="form-label">Data de nascimento *</label>
								<input type="date" class="form-control" name="data_nascimento" max="'. date('Y-m-d') .'" required>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6 mb-3">
								<label class="form-label">Número de telemóvel *</label>
								<input type="text" class="form-control" name="telemovel" placeholder="Ex.: 967788999" required>
							</div>
							<div class="form-group col-md-6 mb-3">
								<label class="form-label">Endereço de email *</label>
								<input type="email" class="form-control" name="email" placeholder="Ex.: jojo@gmail.com" required>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6 mb-3">
								<label class="form-label">Nome de utilizador *</label>
								<input type="text" class="form-control" name="user" placeholder="Ex.: João" required>
							</div>
							<div class="form-group col-md-6 mb-3">
								<label class="form-label">Palavra-passe *</label>
								<input type="password" class="form-control" name="pwd" placeholder="Palavra-passe" autocomplete="off" required>
							</div>
						</div>
						<div class="d-grid gap-2 mt-4">
							<input type="submit" class="btn btn-main" style="font-weight: 600; padding: 0.75rem;" value="Registar">
						</div>
					</form>
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