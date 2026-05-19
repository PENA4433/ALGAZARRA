<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || !isset($_SESSION['nivel'])) {
    include './erro.php';
    exit;
}

// validação segura do GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Aluno inválido");
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Algazarra</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color: #F5F5F5; margin:10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color: #00d0ff;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
    </div>
</nav>
	<div class="container" style="padding:2vh;">

<?php
if (isset($_SESSION['err'])) {
    echo '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"
            onclick="window.location.href=\'login.php\'"></button>
            <strong>' . $_SESSION['err'] . '</strong>
          </div>';
    unset($_SESSION['err']);
}
?>

<h1 class="text-center">Atividades</h1>

<div class="row justify-content-center">

<?php

$sql = "SELECT DISTINCT a.*
        FROM atividade a
        INNER JOIN inscricao i ON a.id = i.atividade
        WHERE i.aluno = $id
        ORDER BY a.data_inicio";

$atividades = mysqli_query($conn, $sql);

if (!$atividades) {
    die("Erro SQL: " . mysqli_error($conn));
}

if (mysqli_num_rows($atividades) == 0) {
    echo "<h3 style='margin-top:3vh; text-align:center;'>Não há atividades.</h3>";
} else {

    while ($atividade = mysqli_fetch_assoc($atividades)) {

        echo '
        <div class="card m-3" style="width:40vh;">
            <img src="'.$atividade['imagem'].'" class="card-img-top mt-3 rounded"
                 style="width:36vh;height:30vh;" alt="'.$atividade['titulo'].'">

            <div class="card-body">
                <h5 class="card-title">'.htmlspecialchars($atividade['titulo']).'</h5>

                <p>
                    De '.date('d/m/Y', strtotime($atividade['data_inicio'])).'
                    a '.date('d/m/Y', strtotime($atividade['data_fim'])).'
                </p>

                <a href="pag_atividades.php?id='.$atividade['id'].'">
                    Saber mais
                </a>
            </div>
        </div>';
    }
}
?>

</div>

<!-- BOTÃO VOLTAR -->
<div class="mt-3 text-center">
    <a href="javascript:history.back()" class="btn btn-secondary">
        Voltar
    </a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>