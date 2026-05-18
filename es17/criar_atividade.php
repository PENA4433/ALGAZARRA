<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || !isset($_SESSION['nivel'])) {
    include './erro.h';
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Algazarra</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color: #F5F5F5; margin: 10vh 0 10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff;">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse justify-content-center" id="navbarColor01">
			<ul class="navbar-nav mx-auto align-items-center justify-content-center">
				<a class="navbar-brand" href="index.php">ALGAZARRA</a>
				<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
				<li class="nav-item"><a class="nav-link active" href="atividades.php">Atividades</a></li>
				<li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Gestão</a>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="gerir_utilizadores.php">Gerir utilizadores</a></li>
						<li><a class="dropdown-item" href="gerir_criancas.php">Gerir crianças</a></li>
						<li><a class="dropdown-item" href="gerir_atividades.php">Gerir atividades</a></li>
						<li><a class="dropdown-item" href="criar_atividade.php">Criar atividade</a></li>
					</ul>
				</li>
			</ul>

			<ul class="navbar-nav ms-auto">
				<li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
				<li class="nav-item"><a class="nav-link" href="dados.php">Dados pessoais</a></li>
			</ul>
		</div>
	</div>
</nav>
	<div class="container" style="padding: 2vh;">

<h1 class="text-center">Criar atividade</h1>

<form action="load_criar_atividade.php" method="POST">

    <div class="row">
        <div class="form-group col-md-6" style="padding-top: 20px;">
            <label>Título (* campo obrigatório)</label>
            <input type="text" class="form-control" name="titulo" placeholder="Ex.: Paintball" required>
        </div>

        <div class="form-group col-md-6" style="padding-top: 20px;">
            <label>Descrição *</label>
            <textarea class="form-control" name="descricao" required></textarea>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6" style="padding-top: 20px;">
            <label>Data de início *</label>
            <input type="date" class="form-control" name="data_inicio" required>
        </div>

        <div class="form-group col-md-6" style="padding-top: 20px;">
            <label>Data de fim *</label>
            <input type="date" class="form-control" name="data_fim" required>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6" style="padding-top: 20px;">
            <label>Lotação máxima *</label>
            <input type="number" class="form-control" name="lotacao" min="0" placeholder="Ex.: 20" required>
        </div>

        <div class="form-group col-md-6" style="padding-top: 20px;">
            <label>Imagem *</label>
            <input type="text" class="form-control" name="img" placeholder="Ex.: img/path.png" required>
        </div>
    </div>

    <div class="text-end">
        <input type="submit" class="btn btn-danger" style="font-weight:bold; margin-top:5vh;" value="Criar">
    </div>

</form>

<!-- BOTÃO VOLTAR -->
<div class="text-start mt-3">
    <a href="index.php" class="btn btn-secondary">
        ⬅ Voltar
    </a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>
