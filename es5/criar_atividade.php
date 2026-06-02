<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || !isset($_SESSION['nivel'])) {
    include './erro.h';
    exit;
}
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

<?php include './rodape.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js_check.js"></script>
</body>
</html>