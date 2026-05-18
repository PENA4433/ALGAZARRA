<?php
session_start();
include './basedados.h';

if (!isset($_SESSION['id_user']) || $_SESSION['nivel'] != 1) {
    include './erro.php';
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$titulo = isset($_POST['titulo']) ? mysqli_real_escape_string($conn, $_POST['titulo']) : '';
$data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : '';
$data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : '';

if ($id == 0 || empty($titulo) || empty($data_inicio) || empty($data_fim)) {
    $_SESSION['err'] = 'Dados inválidos.';
    header('Location: editar_atividade.php?id=' . $id);
    exit;
}

// Prepared statement update
$stmt = mysqli_prepare($conn, "UPDATE atividade SET titulo = ?, data_inicio = ?, data_fim = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "sssi", $titulo, $data_inicio, $data_fim, $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['info'] = 'Atividade atualizada com sucesso!';
} else {
    $_SESSION['err'] = 'Erro ao atualizar: ' . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

header('Location: gerir_atividades.php');
exit;
?>

