<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.php';
    exit;
}

$id = $_GET['id'];

// 🔒 Segurança: prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM atividade WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$atividade = mysqli_fetch_assoc($result);

if (!$atividade) {
    $_SESSION['err'] = "Atividade não encontrada.";
    header("Location: gerir_atividades.php");
    exit;
}
?>

<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar atividade</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color:#F5F5F5; margin: 10vh 0;">

<!-- NAVBAR (igual ao resto do site) -->
<nav class="fixed-top navbar navbar-expand-lg" style="background-color:#00d0ff;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="atividades.php">Atividades</a></li>
                <li class="nav-item"><a class="nav-link" href="contactos.php">Contactos</a></li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
            </ul>
        </div>
    </div>
</nav>
	<div class="container" style="padding:2vh;">

<div class="card" style="background-color:#C5C6D0; border:none; padding:3vh;">
<div class="card-body">

<h1 class="text-center mb-4">Editar atividade</h1>

<form method="POST" action="atualizar_atividade.php">

    <input type="hidden" name="id" value="<?= $atividade['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" class="form-control"
               name="titulo"
               value="<?= htmlspecialchars($atividade['titulo']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Data de início</label>
        <input type="date" class="form-control"
               name="data_inicio"
               value="<?= $atividade['data_inicio'] ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Data de fim</label>
        <input type="date" class="form-control"
               name="data_fim"
               value="<?= $atividade['data_fim'] ?>">
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            Guardar alterações
        </button>
        <a href="gerir_atividades.php" class="btn btn-secondary">
            Cancelar
        </a>
    </div>

</form>

</div>
</div>

</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>