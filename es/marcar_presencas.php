<?php
session_start();
include './basedados.h';

/* 🔐 Apenas professores (nível 2) */
if (!isset($_SESSION['id_user']) || ($_SESSION['nivel'] != 1 && $_SESSION['nivel'] != 2)) {
    $_SESSION['erro'] = 'Login necessário.';
    header('Location: erro.php');
    exit;
}

$id_aluno = isset($_GET['aluno']) ? intval($_GET['aluno']) : (isset($_GET['crianca']) ? intval($_GET['crianca']) : 0);
$id_atividade = isset($_GET['atividade']) ? intval($_GET['atividade']) : 0;

if ($id_aluno == 0 || $id_atividade == 0) {
    $_SESSION['erro'] = 'Parâmetros inválidos: aluno/crianca ou atividade em falta.';
    header('Location: erro.php');
    exit;
}

setlocale(LC_TIME, 'pt_PT.UTF-8');

/* Nome do aluno */
$stmt = mysqli_prepare($conn, "SELECT nome FROM aluno WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_aluno);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) == 0) {
    $_SESSION['erro'] = 'Aluno não encontrado.';
    header('Location: erro.php');
    exit;
}

$nome_aluno = mysqli_fetch_assoc($res)['nome'];
mysqli_stmt_close($stmt);

/* Nome da atividade */
$stmt = mysqli_prepare($conn, "SELECT titulo FROM atividade WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_atividade);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) == 0) {
    $_SESSION['erro'] = 'Atividade não encontrada.';
    header('Location: erro.php');
    exit;
}

$titulo_atividade = mysqli_fetch_assoc($res)['titulo'];
mysqli_stmt_close($stmt);

/* Inscrições (dias) */
$stmt = mysqli_prepare($conn, "SELECT * FROM inscricao WHERE aluno = ? AND atividade = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_aluno, $id_atividade);
mysqli_stmt_execute($stmt);
$dias = mysqli_stmt_get_result($stmt);

if (!$dias) {
    $_SESSION['erro'] = 'Erro ao carregar inscrições: ' . mysqli_error($conn);
    header('Location: erro.php');
    exit;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Marcar Presenças</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body style="background: #F5F5F5; margin:10vh 0;">

<nav class="navbar fixed-top" style="background:#00d0ff;">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">ALGAZARRA</a>
    </div>
</nav>

<div class="container" style="padding:2vh;">

<h2 class="text-center">Marcar presenças</h2>

<h4 class="text-center mb-4">
    Aluno: <?= htmlspecialchars($nome_aluno) ?><br>
    Atividade: <?= htmlspecialchars($titulo_atividade) ?>
</h4>

<table class="table">
    <tr>
        <th>Dia</th>
        <th>Dia da semana</th>
        <th>Presença</th>
    </tr>

<?php if (mysqli_num_rows($dias) == 0) { ?>

    <tr>
        <td colspan="3" class="text-center text-muted py-4">
            Nenhuma inscrição encontrada para este aluno e atividade.
        </td>
    </tr>

<?php } else {

    while ($dia = mysqli_fetch_assoc($dias)) {
        $data = $dia['dia'];
        $semana = strftime('%A', strtotime($data));
?>

    <tr>
        <td><?= date('d/m/Y', strtotime($data)) ?></td>
        <td><?= $semana ?></td>
        <td><?= $dia['esta_presente'] ? 'Sim' : 'Não' ?></td>
    </tr>

<?php
    }
}
?>

</table>

</div>

</body>
</html>

<?php include './rodape.php'; ?>