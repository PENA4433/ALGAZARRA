<?php
session_start();
include './basedados.h';

$id_aluno = isset($_GET['aluno']) ? intval($_GET['aluno']) : 0;
$id_atividade = isset($_GET['atividade']) ? intval($_GET['atividade']) : 0;
$dia = isset($_GET['dia']) ? $_GET['dia'] : '';

if ($id_aluno == 0 || $id_atividade == 0 || empty($dia)) {
    $_SESSION['erro'] = 'Parâmetros inválidos.';
    header('Location: erro.php');
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE inscricao SET esta_presente = 1 WHERE aluno = ? AND atividade = ? AND dia = ?");
mysqli_stmt_bind_param($stmt, "iis", $id_aluno, $id_atividade, $dia);
$retVal = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$retVal) {
    $_SESSION['erro'] = 'Erro ao marcar presença.';
    header('Location: erro.php');
    exit;
}

if (mysqli_affected_rows($conn) > 0) {
    $_SESSION['info'] = 'Marcou a presença no dia ' . date('d/m/Y', strtotime($dia)) . '.';
} else {
    $_SESSION['info'] = 'Presença já estava marcada ou registo não encontrado.';
}

header("Location: ./marcar_presencas.php?aluno=$id_aluno&atividade=$id_atividade");
exit;
?>


