<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user'], $_SESSION['nivel'])) {
    $_SESSION['erro'] = 'Sessão expirada. Faça login novamente.';
    header('Location: erro.php');
    exit;
}

/* 🔎 validar dados */
if (!isset($_GET['aluno'])) {
    $_SESSION['err'] = "Dados inválidos.";
    header("Location: gerir_criancas.php");
    exit;
}

$id_aluno = intval($_GET['aluno']);
$id_atividade = isset($_GET['atividade']) ? intval($_GET['atividade']) : 0;
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Presenças</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background-color:#F5F5F5; margin:10vh 0;">

<nav class="fixed-top navbar navbar-expand-lg" style="background-color:#00d0ff;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
    </div>
</nav>

<div class="container" style="padding:2vh;">

<div class="card" style="background:#C5C6D0; border:none; padding:3vh;">
<div class="card-body">

<h1 class="text-center mb-3">Presenças</h1>

<?php

/* 🔎 nome aluno */
$sql = "SELECT nome FROM aluno WHERE id = $id_aluno";
$res = mysqli_query($conn, $sql);
$aluno = mysqli_fetch_assoc($res)['nome'];

echo "<h4 class='text-center mb-4'>Aluno: $aluno</h4>";

/* 🔎 se veio atividade */
if ($id_atividade > 0) {

    $sql = "SELECT titulo FROM atividade WHERE id = $id_atividade";
    $res = mysqli_query($conn, $sql);
    $atividade = mysqli_fetch_assoc($res)['titulo'];

    echo "<h5 class='text-center mb-4'>Atividade: $atividade</h5>";

    $sql = "SELECT * FROM inscricao
            WHERE aluno = $id_aluno
            AND atividade = $id_atividade
            ORDER BY dia";

} else {

    /* se não vier atividade → todas */
    $sql = "SELECT * FROM inscricao
            WHERE aluno = $id_aluno
            ORDER BY atividade, dia";
}

$res = mysqli_query($conn, $sql);

if (!$res) {
    $_SESSION['erro'] = 'Erro na base de dados.';
    header('Location: erro.php');
    exit;
}
if (mysqli_num_rows($res) == 0) {
    echo "<h5 class='text-center'>Sem presenças registadas.</h5>";
} else { ?>
?>

<table class="table">
    <tr>
        <th style="background:#00d0ff;">Data</th>
        <th style="background:#00d0ff;">Presença</th>
        <th style="background:#00d0ff;">Ação</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($res)) { ?>

<tr>
    <td><?= date('d/m/Y', strtotime($row['dia'])) ?></td>
    <td><?= $row['esta_presente'] ? "Sim" : "Não" ?></td>

    <td>
        <span class="text-muted">Visualização apenas</span>
    </td>
    </tr>

<?php } ?>

</table>
<?php } ?>

</div>
</div>

</div>

</body>
</html>

<?php include './rodape.php'; ?>