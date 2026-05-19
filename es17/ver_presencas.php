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

<h1 class="text-center mb-3">Histórico de Presenças</h1>

<?php

/* 🔎 nome aluno */
$stmt = mysqli_prepare($conn, "SELECT nome FROM aluno WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_aluno);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) == 0) {
    $_SESSION['erro'] = 'Aluno não encontrado.';
    header('Location: erro.php');
    exit;
}

$aluno = mysqli_fetch_assoc($res)['nome'];
mysqli_stmt_close($stmt);

echo "<h4 class='text-center mb-4'>Aluno: " . htmlspecialchars($aluno) . "</h4>";

/* 🔎 se veio atividade */
if ($id_atividade > 0) {

    $stmt = mysqli_prepare($conn, "SELECT titulo FROM atividade WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_atividade);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res && mysqli_num_rows($res) > 0) {
        $atividade = mysqli_fetch_assoc($res)['titulo'];
        echo "<h5 class='text-center mb-4'>Atividade: " . htmlspecialchars($atividade) . "</h5>";
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "SELECT * FROM inscricao WHERE aluno = ? AND atividade = ? ORDER BY dia");
    mysqli_stmt_bind_param($stmt, "ii", $id_aluno, $id_atividade);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

} else {

    /* se não vier atividade → todas */
    $stmt = mysqli_prepare($conn, "SELECT * FROM inscricao WHERE aluno = ? ORDER BY atividade, dia");
    mysqli_stmt_bind_param($stmt, "i", $id_aluno);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
}

if (!$res) {
    $_SESSION['erro'] = 'Erro na base de dados.';
    header('Location: erro.php');
    exit;
}

if (mysqli_num_rows($res) == 0) {
    echo "<h5 class='text-center'>Sem presenças registadas para esta atividade.</h5>";
} else { 
?>

<table class="table">
    <tr>
        <th style="background:#00d0ff;">Data</th>
        <th style="background:#00d0ff;">Atividade</th>
        <th style="background:#00d0ff;">Estado</th>
    </tr>

<?php 
while ($row = mysqli_fetch_assoc($res)) { 
    // Buscar nome da atividade
    $stmt_at = mysqli_prepare($conn, "SELECT titulo FROM atividade WHERE id = ?");
    mysqli_stmt_bind_param($stmt_at, "i", $row['atividade']);
    mysqli_stmt_execute($stmt_at);
    $res_at = mysqli_stmt_get_result($stmt_at);
    $atividade_nome = ($res_at && mysqli_num_rows($res_at) > 0) ? mysqli_fetch_assoc($res_at)['titulo'] : 'N/A';
    mysqli_stmt_close($stmt_at);
?>

<tr>
    <td><?= date('d/m/Y', strtotime($row['dia'])) ?></td>
    <td><?= htmlspecialchars($atividade_nome) ?></td>
    <td>
        <span class="badge <?= $row['esta_presente'] ? 'bg-success' : 'bg-danger' ?>">
            <?= $row['esta_presente'] ? "Presente" : "Ausente" ?>
        </span>
    </td>
</tr>

<?php } 
mysqli_stmt_close($stmt);
?>

</table>
<?php } ?>

</div>
</div>

<div class="mt-4 text-center">
    <a href="gerir_criancas.php" class="btn btn-secondary">
        ⬅ Voltar
    </a>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include './rodape.php'; ?>
