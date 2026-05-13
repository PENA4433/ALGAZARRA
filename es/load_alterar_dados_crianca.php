<?php
session_start();
include './basedados.h';

if (!isset($_POST['id'], $_POST['nome'], $_POST['data_nascimento'])) {
    $_SESSION['err'] = 'Dados inválidos.';
    header('Location: alterar_dados_crianca.php');
    exit;
}

$id_crianca = intval($_POST['id']);
$nome = trim($_POST['nome']);
$data_nascimento = $_POST['data_nascimento'];

if ($nome === '' || $data_nascimento === '') {
    $_SESSION['err'] = 'Não preencheu campos obrigatórios.';
    header("Location: alterar_dados_crianca.php?id=$id_crianca");
    exit;
}

$stmt = $conn->prepare("
    UPDATE aluno
    SET nome = ?, data_nascimento = ?
    WHERE id = ?
");

$stmt->bind_param("ssi", $nome, $data_nascimento, $id_crianca);

if (!$stmt->execute()) {
    die('Erro SQL: ' . $conn->error);
}

$_SESSION['info'] = "Os dados do aluno $nome foram alterados com sucesso!";

header('Location: gerir_criancas.php');
exit;
?>