<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.php';
    exit;
}

$id = $_GET['id'];

if (!$id) {
    $_SESSION['err'] = "ID inválido.";
    header("Location: gerir_atividades.php");
    exit;
}

/* 🔒 apagar primeiro dependências (boa prática) */

// apagar inscrições da atividade
$stmt = mysqli_prepare($conn, "DELETE FROM inscricao WHERE atividade = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

// apagar atividade
$stmt = mysqli_prepare($conn, "DELETE FROM atividade WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$_SESSION['info'] = "Atividade apagada com sucesso.";
header("Location: gerir_atividades.php");
exit;
?>